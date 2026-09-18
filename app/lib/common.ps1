<#
  Shared helpers for the MPCBC control panel and its scripts.

  Dot-source this, don't run it:
      . "$PSScriptRoot\lib\common.ps1"

  Everything a non-technical editor touches goes through here, so two rules
  hold throughout:

    * Nothing writes to the registry or to a machine-wide setting. PATH is
      read and applied to the current process only.
    * Every failure has a sentence a person can act on, and the technical
      detail goes to a log file rather than the screen.
#>

Set-StrictMode -Version 2.0

# Ports the two dev servers listen on. Tina indexes content and serves
# GraphQL on 4001; Astro serves the site on 4321.
$script:TinaPort  = 4001
$script:AstroPort = 4321

$script:SiteUrl   = "http://localhost:$script:AstroPort"
$script:AdminUrl  = "http://localhost:$script:AstroPort/admin"
$script:RepoUrl   = 'https://github.com/ChrisLKH/mpcbc'
$script:ActionsUrl = "$script:RepoUrl/actions"
$script:GuideUrl  = "$script:RepoUrl/blob/main/GUIDE.md"

# Portable Git/Node land here when the machine has no admin rights. Kept
# outside the repo so "Undo My Changes" can never remove the toolchain.
$script:PortableToolsDir = 'C:\mpcbc-tools'

# --- Where things are ---------------------------------------------------

function Get-RepoRoot {
  <#
    The repo root, derived from this file's location: scripts\lib\common.ps1
    is always two levels below it. Deriving beats guessing - the control
    panel can be launched from a desktop shortcut with any working directory.
  #>
  return (Split-Path -Parent (Split-Path -Parent $PSScriptRoot))
}

function Get-LogDir {
  $dir = Join-Path (Get-RepoRoot) 'logs'
  if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
  return $dir
}

function Get-LogPath   { param([string]$Name) return (Join-Path (Get-LogDir) "$Name.log") }
function Get-PidsPath  { return (Join-Path (Get-LogDir) 'pids.json') }

# --- Logging ------------------------------------------------------------

function Write-Log {
  <#
    Appends one timestamped line to logs\app.log. This is what the control
    panel's "Show Details" pane reads and what "Copy for Chris" copies, so
    it is the only place a diagnostic needs to go.
  #>
  param(
    [Parameter(Mandatory = $true)][string]$Message,
    [ValidateSet('info', 'warn', 'error')][string]$Level = 'info'
  )
  # Emits NOTHING to the pipeline, deliberately. Returning the line here
  # would silently corrupt every function that logs before returning a
  # value - Invoke-Git would hand back @(string, result) instead of result,
  # and every caller reading .Ok would break.
  $line = '{0}  {1,-5}  {2}' -f (Get-Date -Format 'HH:mm:ss'), $Level.ToUpper(), $Message
  try {
    Add-Content -Path (Get-LogPath 'app') -Value $line -Encoding utf8
  } catch {
    # Logging must never be the thing that breaks the app.
  }
}

function Get-RecentLog {
  param([int]$Lines = 50)
  $path = Get-LogPath 'app'
  if (-not (Test-Path $path)) { return '' }
  try {
    return ((Get-Content -Path $path -Tail $Lines -Encoding UTF8) -join "`r`n")
  } catch {
    return ''
  }
}

# --- Ports and processes ------------------------------------------------

function Test-PortOpen {
  <#
    Try BOTH loopback addresses explicitly, and call the port open if
    either answers.

    This is not belt-and-braces, it is the whole trick. Astro and Tina bind
    IPv6 loopback (::1) only. Whether "localhost" reaches that depends on
    the machine's hosts file, and on plenty of Windows installs it resolves
    to 127.0.0.1 first - so a probe by name reports "not running" for a
    server that is serving pages perfectly well.

    Getting this wrong is not a cosmetic bug. The control panel would sit
    on "Starting up..." forever, and start.ps1 would hit its timeout and
    kill a healthy server.
  #>
  param([Parameter(Mandatory = $true)][int]$Port, [int]$TimeoutMs = 400)

  foreach ($address in @([System.Net.IPAddress]::IPv6Loopback, [System.Net.IPAddress]::Loopback)) {
    $client = $null
    try {
      $client = New-Object System.Net.Sockets.TcpClient($address.AddressFamily)
      $async = $client.BeginConnect($address, $Port, $null, $null)
      if ($async.AsyncWaitHandle.WaitOne($TimeoutMs, $false)) {
        $client.EndConnect($async)
        return $true
      }
    } catch {
      # This family isn't available or nothing is listening on it; try the
      # other one before concluding anything.
    } finally {
      if ($client) { $client.Close() }
    }
  }
  return $false
}

function Wait-ForPort {
  param([Parameter(Mandatory = $true)][int]$Port, [int]$Seconds = 180)
  for ($i = 0; $i -lt $Seconds; $i++) {
    if (Test-PortOpen -Port $Port) { return $true }
    Start-Sleep -Seconds 1
  }
  return $false
}

function Get-PortOwner {
  <#
    Which process is listening on a port, so "port already in use" can say
    whether it is our own leftover server or something unrelated.
    Returns $null when nothing is listening or the lookup isn't available.
  #>
  param([Parameter(Mandatory = $true)][int]$Port)
  try {
    $conn = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction Stop |
            Select-Object -First 1
    if (-not $conn) { return $null }
    return (Get-Process -Id $conn.OwningProcess -ErrorAction Stop)
  } catch {
    return $null
  }
}

function Save-ServerPids {
  param([int]$TinaPid, [int]$AstroPid)
  $data = [pscustomobject]@{
    tina    = $TinaPid
    astro   = $AstroPid
    started = (Get-Date).ToString('o')
  }
  $data | ConvertTo-Json | Out-File -FilePath (Get-PidsPath) -Encoding utf8
}

function Get-ServerPids {
  $path = Get-PidsPath
  if (-not (Test-Path $path)) { return $null }
  try {
    return (Get-Content -Path $path -Raw | ConvertFrom-Json)
  } catch {
    return $null
  }
}

function Clear-ServerPids {
  $path = Get-PidsPath
  if (Test-Path $path) { Remove-Item -Path $path -Force -ErrorAction SilentlyContinue }
}

function Stop-ProcessTree {
  <#
    node spawns children that outlive a plain Stop-Process, and an orphaned
    Tina server holding port 4001 is exactly what makes tomorrow's launch
    fail. taskkill /T is the only reliable way to take the whole tree.
  #>
  param([Parameter(Mandatory = $true)][int]$ProcessId)
  if ($ProcessId -le 0) { return }
  try {
    Start-Process -FilePath 'taskkill.exe' -ArgumentList @('/PID', $ProcessId, '/T', '/F') `
      -WindowStyle Hidden -Wait -ErrorAction Stop
  } catch {
    Write-Log "taskkill failed for PID $ProcessId : $($_.Exception.Message)" 'warn'
  }
}

function Get-SiteStatus {
  <#
    One place decides what state the site is in, so the control panel and
    the scripts can never disagree.

    stopped  - neither server answering
    starting - one up, the other not yet
    running  - both answering
    crashed  - we started servers, they are gone, and nothing is listening
  #>
  $tinaUp  = Test-PortOpen -Port $script:TinaPort
  $astroUp = Test-PortOpen -Port $script:AstroPort

  if ($tinaUp -and $astroUp) { $state = 'running' }
  elseif ($tinaUp -or $astroUp) { $state = 'starting' }
  else {
    $state = 'stopped'
    $pids = Get-ServerPids
    if ($pids) {
      # We recorded a launch but nothing is listening and the processes are
      # gone: that is a crash, not a clean stop.
      $alive = $false
      foreach ($id in @($pids.tina, $pids.astro)) {
        if ($id -and (Get-Process -Id $id -ErrorAction SilentlyContinue)) { $alive = $true }
      }
      if (-not $alive) { $state = 'crashed' }
    }
  }

  return [pscustomobject]@{
    State   = $state
    TinaUp  = $tinaUp
    AstroUp = $astroUp
  }
}

# --- Finding git and node ------------------------------------------------

function Update-PathFromRegistry {
  <#
    A freshly installed Git or Node is not visible to the window that ran
    the installer, because PATH was read when the process started. Re-read
    the persisted value and apply it HERE, to this process only.

    This is a READ. Nothing is written back. Never call
    [Environment]::SetEnvironmentVariable('Path', ..., 'Machine'/'User')
    from these scripts - that call flattens REG_EXPAND_SZ entries such as
    %SystemRoot% and is the classic way to permanently corrupt a PATH.
  #>
  $parts = @()
  foreach ($key in @(
    'HKLM:\SYSTEM\CurrentControlSet\Control\Session Manager\Environment',
    'HKCU:\Environment'
  )) {
    try {
      $value = (Get-ItemProperty -Path $key -Name Path -ErrorAction Stop).Path
      if ($value) { $parts += $value }
    } catch {
      # A missing user Environment key is normal on a fresh profile.
    }
  }
  # Keep the portable toolchain visible too, if this machine uses one.
  foreach ($sub in @('git\cmd', 'node')) {
    $dir = Join-Path $script:PortableToolsDir $sub
    if (Test-Path $dir) { $parts += $dir }
  }
  if ($parts.Count -gt 0) {
    $env:Path = ($parts -join ';')
  }
}

function Get-GitExe {
  <#
    Resolve git even when it isn't on PATH yet: PATH may be stale after an
    install, and a no-admin machine keeps git in the portable tools folder.
    Returns $null if git genuinely isn't present.
  #>
  $cmd = Get-Command git -ErrorAction SilentlyContinue
  if ($cmd) { return $cmd.Source }

  Update-PathFromRegistry
  $cmd = Get-Command git -ErrorAction SilentlyContinue
  if ($cmd) { return $cmd.Source }

  foreach ($candidate in @(
    (Join-Path $script:PortableToolsDir 'git\cmd\git.exe'),
    "$env:ProgramFiles\Git\cmd\git.exe",
    "${env:ProgramFiles(x86)}\Git\cmd\git.exe",
    "$env:LOCALAPPDATA\Programs\Git\cmd\git.exe"
  )) {
    if (Test-Path $candidate) { return $candidate }
  }
  return $null
}

function Get-NodeExe {
  $cmd = Get-Command node -ErrorAction SilentlyContinue
  if ($cmd) { return $cmd.Source }

  Update-PathFromRegistry
  $cmd = Get-Command node -ErrorAction SilentlyContinue
  if ($cmd) { return $cmd.Source }

  foreach ($candidate in @(
    (Join-Path $script:PortableToolsDir 'node\node.exe'),
    "$env:ProgramFiles\nodejs\node.exe",
    "$env:LOCALAPPDATA\Programs\nodejs\node.exe"
  )) {
    if (Test-Path $candidate) { return $candidate }
  }
  return $null
}

function Invoke-Git {
  <#
    Run git and capture everything. Returns an object rather than throwing,
    because every caller wants to turn a failure into a plain sentence
    rather than a red stack trace.

    stderr is merged deliberately here: git writes ordinary progress to
    stderr, so treating it as failure output would mislabel a healthy run.
  #>
  param(
    [Parameter(Mandatory = $true)][string[]]$Arguments,
    [string]$WorkingDirectory = (Get-RepoRoot)
  )

  $git = Get-GitExe
  if (-not $git) {
    return [pscustomobject]@{ Ok = $false; ExitCode = -1; Output = ''; Error = 'Git is not installed.' }
  }

  $outFile = [System.IO.Path]::GetTempFileName()
  $errFile = [System.IO.Path]::GetTempFileName()
  try {
    $proc = Start-Process -FilePath $git -ArgumentList $Arguments `
      -WorkingDirectory $WorkingDirectory -NoNewWindow -Wait -PassThru `
      -RedirectStandardOutput $outFile -RedirectStandardError $errFile

    $out = ''
    $err = ''
    # git speaks UTF-8; PowerShell 5.1's Get-Content defaults to ANSI.
    if (Test-Path $outFile) { $out = (Get-Content -Path $outFile -Raw -Encoding UTF8) }
    if (Test-Path $errFile) { $err = (Get-Content -Path $errFile -Raw -Encoding UTF8) }
    if ($null -eq $out) { $out = '' }
    if ($null -eq $err) { $err = '' }

    if ($proc.ExitCode -ne 0) {
      Write-Log ("git {0} -> exit {1}: {2}" -f ($Arguments -join ' '), $proc.ExitCode, $err.Trim()) 'warn'
    }

    return [pscustomobject]@{
      Ok       = ($proc.ExitCode -eq 0)
      ExitCode = $proc.ExitCode
      Output   = $out.Trim()
      Error    = $err.Trim()
    }
  } finally {
    Remove-Item $outFile, $errFile -Force -ErrorAction SilentlyContinue
  }
}

# --- Describing changes in plain language --------------------------------

function Get-ChangeSummary {
  <#
    Turn `git status --porcelain` into something an editor recognises.

    "2 announcements, 1 photo" is reviewable before publishing;
    "M src/content/announcements/christmas-eve.md" is not. Returns Count 0
    when the working tree is clean.

    -PathList bypasses git and describes the paths given. That exists so
    the wording can be checked without a working tree in a particular
    state - this text is the last thing an editor reads before publishing
    to a live site, so it is worth being able to test it directly.
  #>
  param([string[]]$PathList)

  if ($PSBoundParameters.ContainsKey('PathList')) {
    $paths = @($PathList)
  } else {
    $status = Invoke-Git -Arguments @('status', '--porcelain')
    if (-not $status.Ok) {
      return [pscustomobject]@{ Count = 0; Summary = ''; Items = @(); Paths = @() }
    }

    $paths = @()
    foreach ($line in ($status.Output -split "`r?`n")) {
      if ([string]::IsNullOrWhiteSpace($line)) { continue }
      # Porcelain v1: two status characters, a space, then the path. A
      # rename reads "old -> new"; the new name is the one worth reporting.
      $path = $line.Substring(3).Trim().Trim('"')
      if ($path -match ' -> ') { $path = ($path -split ' -> ')[-1].Trim().Trim('"') }
      $paths += $path
    }
  }

  $groups = [ordered]@{}
  function Add-Item {
    param($Bucket, $Name)
    if (-not $groups.Contains($Bucket)) { $groups[$Bucket] = New-Object System.Collections.ArrayList }
    [void]$groups[$Bucket].Add($Name)
  }

  foreach ($path in $paths) {
    $p = $path -replace '\\', '/'
    $leaf = [System.IO.Path]::GetFileNameWithoutExtension($p)
    # Make a filename readable: christmas-eve -> Christmas Eve
    $pretty = (($leaf -replace '[-_]', ' ') -split ' ' | ForEach-Object {
      if ($_.Length -gt 0) { $_.Substring(0, 1).ToUpper() + $_.Substring(1) } else { $_ }
    }) -join ' '

    switch -Regex ($p) {
      '^src/content/settings/homepage\.'   { Add-Item 'the homepage' 'Homepage'; break }
      '^src/content/announcements/'        { Add-Item 'announcement' $pretty; break }
      '^src/content/events/'               { Add-Item 'event' $pretty; break }
      '^src/content/sermons/' {
        # A sermon's filename is its YouTube video ID, so prettifying it
        # produces gibberish like "DQw4w9WgXcQ". The real title is inside
        # the file. A deleted sermon can't be read, hence the fallback.
        $title = ''
        try {
          $full = Join-Path (Get-RepoRoot) ($p -replace '/', '\')
          # -Encoding UTF8 is not optional: PowerShell 5.1's Get-Content
          # defaults to the system ANSI codepage, which turns a Chinese
          # sermon title into mojibake in the publish confirmation - the
          # one screen an editor is meant to read carefully.
          if (Test-Path $full) { $title = (Get-Content -Path $full -Raw -Encoding UTF8 | ConvertFrom-Json).title }
        } catch { }
        if ([string]::IsNullOrWhiteSpace($title)) { $title = '' }
        Add-Item 'sermon' $title
        break
      }
      '^src/content/pages/'                { Add-Item 'page' $pretty; break }
      '^public/images/'                    { Add-Item 'photo' $pretty; break }
      default                              { Add-Item 'other file' $p; break }
    }
  }

  $items = @()
  foreach ($bucket in $groups.Keys) {
    $n = $groups[$bucket].Count
    if ($bucket -eq 'the homepage') {
      $items += 'the homepage'
    } elseif ($n -eq 1) {
      $name = $groups[$bucket][0]
      # Naming the one thing that changed is the whole point, but a blank
      # name would read as "1 sermon ()".
      if ([string]::IsNullOrWhiteSpace($name)) {
        $items += ('1 {0}' -f $bucket)
      } else {
        $items += ('1 {0} ({1})' -f $bucket, $name)
      }
    } else {
      $items += ('{0} {1}s' -f $n, $bucket)
    }
  }

  return [pscustomobject]@{
    Count   = $paths.Count
    Summary = ($items -join ', ')
    Items   = $items
    Paths   = $paths
  }
}

function Test-SecretInChanges {
  <#
    Last line of defence before `git add -A`.

    .env is protected by one line in .gitignore. That is enough right up
    until someone edits .gitignore, and the cost of being wrong is a leaked
    Tina token in a public repository. This check is independent of it.

    Returns the offending paths, or an empty array when clean.
  #>
  param([string[]]$Paths)
  $bad = @()
  foreach ($path in $Paths) {
    $leaf = (Split-Path -Leaf ($path -replace '/', '\'))
    if ($leaf -eq '.env' -or $leaf -like '.env.*' -or
        $leaf -like '*token*' -or $leaf -like '*secret*' -or
        $leaf -like '*credential*') {
      $bad += $path
    }
  }
  # Plain return, NOT `return ,$bad`: the comma stops PowerShell
  # enumerating the array, so a caller writing @(...) around the call ends
  # up with an array containing an array, and .Count is 1 even when nothing
  # matched. Callers wrap with @() instead, which handles both the empty
  # case and the single-item case correctly.
  return $bad
}

function Confirm-GitIdentity {
  <#
    git refuses to commit on a machine with no user.name / user.email:

        *** Please tell me who you are.

    Left unhandled that lands at publish time - the worst possible moment,
    with the editor's work already done and a wall of git advice on screen.
    Ask early, in a dialog, and write it REPO-LOCAL (no --global) so a
    shared church computer doesn't end up committing as whoever set it up
    first.

    Returns $true when an identity is in place.
  #>
  $name  = (Invoke-Git -Arguments @('config', 'user.name')).Output
  $email = (Invoke-Git -Arguments @('config', 'user.email')).Output

  if (-not [string]::IsNullOrWhiteSpace($name) -and -not [string]::IsNullOrWhiteSpace($email)) {
    return $true
  }

  if ([string]::IsNullOrWhiteSpace($name)) {
    $name = Show-InputDialog -Title 'Who is publishing?' -Prompt (
      "Before your first publish, the website needs to know who you are. " +
      "This is shown in the site's history so everyone can see who changed what.`r`n`r`n" +
      'Your name:')
    if ([string]::IsNullOrWhiteSpace($name)) { return $false }
    [void](Invoke-Git -Arguments @('config', 'user.name', $name))
  }

  if ([string]::IsNullOrWhiteSpace($email)) {
    $email = Show-InputDialog -Title 'Who is publishing?' -Prompt (
      "And the email address on your GitHub account:")
    if ([string]::IsNullOrWhiteSpace($email)) { return $false }
    [void](Invoke-Git -Arguments @('config', 'user.email', $email))
  }

  Write-Log "Git identity set for this folder: $name <$email>"
  return $true
}

function Test-EnvIgnored {
  <#
    Confirms .gitignore still covers .env. Catches an accidental edit to
    .gitignore before it matters rather than after the token is public.
  #>
  $result = Invoke-Git -Arguments @('check-ignore', '-q', '.env')
  return ($result.ExitCode -eq 0)
}

# --- Dialogs -------------------------------------------------------------

function Initialize-Ui {
  Add-Type -AssemblyName System.Windows.Forms -ErrorAction SilentlyContinue
  Add-Type -AssemblyName System.Drawing -ErrorAction SilentlyContinue
  try { [System.Windows.Forms.Application]::EnableVisualStyles() } catch { }
}

function Show-Info {
  param([string]$Message, [string]$Title = 'MPCBC Website')
  Initialize-Ui
  [void][System.Windows.Forms.MessageBox]::Show($Message, $Title,
    [System.Windows.Forms.MessageBoxButtons]::OK,
    [System.Windows.Forms.MessageBoxIcon]::Information)
}

function Show-Problem {
  param([string]$Message, [string]$Title = 'MPCBC Website')
  Initialize-Ui
  Write-Log $Message 'error'
  [void][System.Windows.Forms.MessageBox]::Show($Message, $Title,
    [System.Windows.Forms.MessageBoxButtons]::OK,
    [System.Windows.Forms.MessageBoxIcon]::Warning)
}

function Confirm-Action {
  <#
    Yes/No. $DangerousDefault = $false puts the highlighted button on "No",
    which is what we want anywhere an Enter keypress would destroy work.
  #>
  param(
    [string]$Message,
    [string]$Title = 'MPCBC Website',
    [bool]$DefaultYes = $false
  )
  Initialize-Ui
  $default = if ($DefaultYes) {
    [System.Windows.Forms.MessageBoxDefaultButton]::Button1
  } else {
    [System.Windows.Forms.MessageBoxDefaultButton]::Button2
  }
  $answer = [System.Windows.Forms.MessageBox]::Show($Message, $Title,
    [System.Windows.Forms.MessageBoxButtons]::YesNo,
    [System.Windows.Forms.MessageBoxIcon]::Question,
    $default)
  return ($answer -eq [System.Windows.Forms.DialogResult]::Yes)
}

function Show-InputDialog {
  <#
    A plain "type something here" box. Built by hand rather than borrowing
    Microsoft.VisualBasic.Interaction::InputBox so the prompt can wrap, the
    OK button can be disabled while the box is empty, and Enter submits.
  #>
  param(
    [string]$Prompt,
    [string]$Title = 'MPCBC Website',
    [string]$Default = '',
    [bool]$RequireValue = $true
  )
  Initialize-Ui

  $form = New-Object System.Windows.Forms.Form
  $form.Text = $Title
  # Sized for a multi-line prompt: these prompts carry an example on its
  # own line, and a shorter box crops it behind the text field.
  $form.Size = New-Object System.Drawing.Size(470, 268)
  $form.StartPosition = 'CenterScreen'
  $form.FormBorderStyle = 'FixedDialog'
  $form.MaximizeBox = $false
  $form.MinimizeBox = $false
  $form.Font = New-Object System.Drawing.Font('Segoe UI', 10)

  $label = New-Object System.Windows.Forms.Label
  $label.Text = $Prompt
  $label.SetBounds(16, 16, 422, 96)
  $form.Controls.Add($label)

  $box = New-Object System.Windows.Forms.TextBox
  $box.SetBounds(16, 120, 422, 26)
  $box.Text = $Default
  $form.Controls.Add($box)

  $ok = New-Object System.Windows.Forms.Button
  $ok.Text = 'OK'
  $ok.SetBounds(262, 164, 85, 30)
  $ok.DialogResult = [System.Windows.Forms.DialogResult]::OK
  $form.Controls.Add($ok)

  $cancel = New-Object System.Windows.Forms.Button
  $cancel.Text = 'Cancel'
  $cancel.SetBounds(353, 164, 85, 30)
  $cancel.DialogResult = [System.Windows.Forms.DialogResult]::Cancel
  $form.Controls.Add($cancel)

  $form.AcceptButton = $ok
  $form.CancelButton = $cancel

  if ($RequireValue) {
    $ok.Enabled = -not [string]::IsNullOrWhiteSpace($Default)
    $box.Add_TextChanged({ $ok.Enabled = -not [string]::IsNullOrWhiteSpace($box.Text) }.GetNewClosure())
  }

  $box.Select()
  $result = $form.ShowDialog()
  $value = $box.Text
  $form.Dispose()

  if ($result -ne [System.Windows.Forms.DialogResult]::OK) { return $null }
  return $value
}

function Open-Url {
  param([string]$Url)
  try { Start-Process $Url } catch { Write-Log "Could not open $Url : $($_.Exception.Message)" 'warn' }
}

# --- Prerequisites -------------------------------------------------------

function Get-Prerequisites {
  <#
    What this computer has, and what it still needs, as one object.

    The control panel gates every button on this: nothing that cannot
    possibly work is ever clickable. An editor should meet a greyed-out
    button and a checklist, never an error dialog explaining that a thing
    they have never heard of is missing.

    Each check is the cheapest question that is actually conclusive - no
    version shelling out unless the executable is there to shell out to.
  #>
  $root = Get-RepoRoot

  $gitExe  = Get-GitExe
  $nodeExe = Get-NodeExe

  $nodeMajor = 0
  if ($nodeExe) {
    try { $nodeMajor = [int]((((& $nodeExe --version) -replace '^v', '') -split '\.')[0]) } catch { $nodeMajor = 0 }
  }

  # "The files are here" means a real clone, not just a folder: the
  # publish and update buttons are meaningless without git history.
  $repoOk = (Test-Path (Join-Path $root '.git')) -and (Test-Path (Join-Path $root 'package.json'))

  # node_modules can exist and still be useless - a half-finished or
  # interrupted npm install leaves the folder behind. Check for something
  # the site actually starts with.
  $depsOk = (Test-Path (Join-Path $root 'node_modules\astro')) -and
            (Test-Path (Join-Path $root 'node_modules\.bin'))

  $envOk = Test-Path (Join-Path $root '.env')

  $nodeOk = ($nodeExe -and $nodeMajor -ge 20)

  return [pscustomobject]@{
    GitOk     = [bool]$gitExe
    NodeOk    = [bool]$nodeOk
    NodeMajor = $nodeMajor
    RepoOk    = [bool]$repoOk
    DepsOk    = [bool]$depsOk
    EnvOk     = [bool]$envOk
    ToolsOk   = ([bool]$gitExe -and [bool]$nodeOk)
    Ready     = ([bool]$gitExe -and [bool]$nodeOk -and [bool]$repoOk -and [bool]$depsOk -and [bool]$envOk)
  }
}

# --- Remote state --------------------------------------------------------

function Get-UpdateCount {
  <#
    How many commits the remote is ahead of us.

    Uses only what git already knows locally - it does NOT fetch - so it is
    cheap enough to call from the panel's timer. Start-RemoteRefresh is
    what makes that local knowledge current.

    Returns 0 when there is no upstream, no git, or nothing to collect.
  #>
  $result = Invoke-Git -Arguments @('rev-list', '--count', 'HEAD..@{u}')
  if (-not $result.Ok) { return 0 }
  $count = 0
  if ([int]::TryParse($result.Output.Trim(), [ref]$count)) { return $count }
  return 0
}

function Start-RemoteRefresh {
  <#
    Fire-and-forget fetch, so the update count becomes current without
    freezing the window for the length of a network round trip.

    A fetch only updates our record of what is on GitHub. It changes no
    file in the working folder and cannot disturb an editor's work, which
    is why it is safe to do on launch without asking - unlike a pull,
    which waits for the one deliberate click.
  #>
  $git = Get-GitExe
  if (-not $git) { return }
  try {
    Start-Process -FilePath $git -ArgumentList @('fetch', '--quiet') `
      -WorkingDirectory (Get-RepoRoot) -WindowStyle Hidden | Out-Null
  } catch {
    # Offline is normal and not worth reporting; the count just stays stale.
  }
}
