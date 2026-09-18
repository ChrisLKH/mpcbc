<#
.SYNOPSIS
  The MPCBC website control panel. Windows.

.DESCRIPTION
  One window, one job per button. This is the entire interface an editor
  ever sees, and everything it does goes through the same scripts a
  developer would run by hand - there is one code path, not two.

  Design rules, all of them load-bearing:

    * No console. The dev servers run hidden (see start.ps1 for why), and
      every question is a dialog.
    * Nothing that cannot possibly work is clickable. Get-Prerequisites
      decides; an editor meets a greyed-out button and a checklist saying
      what is still missing, never an error dialog about a thing they have
      never heard of.
    * The setup checklist is only on screen while setup is unfinished.
      Once the computer is ready it disappears, so the everyday window
      stays small.
    * The main screen only ever shows plain sentences. Technical detail
      lives behind "Show Details", with "Copy for Chris" beside it.

  Launched by "MPCBC Website.bat" in the repository root.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

Initialize-Ui
$root = Get-RepoRoot
Set-Location $root

# Hide our own console window.
#
# This has to happen HERE rather than by launching powershell.exe with
# -WindowStyle Hidden. That switch sets STARTUPINFO.wShowWindow to SW_HIDE
# for the whole process, and Windows applies wShowWindow to the first
# top-level window a process shows - which is this form. The result is an
# app that starts, runs, and displays absolutely nothing.
#
# So: launch normally (brief console flash), then hide the console
# ourselves once we own it.
Add-Type -Name Console -Namespace MpcbcNative -MemberDefinition @'
  [DllImport("kernel32.dll")] public static extern System.IntPtr GetConsoleWindow();
  [DllImport("user32.dll")]   public static extern bool ShowWindow(System.IntPtr hWnd, int nCmdShow);
'@ -ErrorAction SilentlyContinue

try {
  $consoleHandle = [MpcbcNative.Console]::GetConsoleWindow()
  if ($consoleHandle -ne [IntPtr]::Zero) {
    [void][MpcbcNative.Console]::ShowWindow($consoleHandle, 0)   # 0 = SW_HIDE
  }
} catch {
  # A visible console is untidy, not fatal. Never let this stop the app.
}

Write-Log '--- Control panel opened ---'

# --- State ---------------------------------------------------------------

$script:BusyProcess = $null      # long job currently running, if any
$script:BusyLabel   = ''
$script:DetailsOpen = $false
$script:HasChanges  = $false
$script:LastChangeCheck = [datetime]::MinValue
$script:Prereqs     = $null
$script:LastPrereqCheck = [datetime]::MinValue
$script:SetupVisible = $true

# Vertical space the setup block occupies. Everything below it shifts by
# this much while it is on screen, and back again once setup is finished.
$SETUP_OFFSET     = 200
$COLLAPSED_HEIGHT = 468
$DETAILS_EXTRA    = 232

# --- Window --------------------------------------------------------------

$form = New-Object System.Windows.Forms.Form
$form.Text = 'MPCBC Website'
$form.ClientSize = New-Object System.Drawing.Size(452, $COLLAPSED_HEIGHT)
$form.StartPosition = 'CenterScreen'
$form.FormBorderStyle = 'FixedSingle'
$form.MaximizeBox = $false
$form.Font = New-Object System.Drawing.Font('Segoe UI', 10)
$form.BackColor = [System.Drawing.Color]::White

function New-Button {
  param([string]$Text, [int]$X, [int]$Y, [int]$W, [int]$H, [bool]$Primary = $false)
  $b = New-Object System.Windows.Forms.Button
  $b.Text = $Text
  $b.SetBounds($X, $Y, $W, $H)
  $b.FlatStyle = 'System'
  $b.UseVisualStyleBackColor = $true
  if ($Primary) { $b.Font = New-Object System.Drawing.Font('Segoe UI', 11, [System.Drawing.FontStyle]::Bold) }
  $form.Controls.Add($b)
  return $b
}

function New-Separator {
  param([int]$Y)
  $p = New-Object System.Windows.Forms.Label
  $p.SetBounds(18, $Y, 416, 1)
  $p.BorderStyle = 'Fixed3D'
  $form.Controls.Add($p)
  return $p
}

# --- Status block --------------------------------------------------------

$dot = New-Object System.Windows.Forms.Label
$dot.Text = [char]0x25CF          # a filled circle
$dot.Font = New-Object System.Drawing.Font('Segoe UI', 20)
$dot.SetBounds(16, 12, 34, 40)
$dot.TextAlign = 'MiddleCenter'
$form.Controls.Add($dot)

$statusText = New-Object System.Windows.Forms.Label
$statusText.Font = New-Object System.Drawing.Font('Segoe UI', 13, [System.Drawing.FontStyle]::Bold)
$statusText.SetBounds(54, 16, 384, 26)
$form.Controls.Add($statusText)

$statusSub = New-Object System.Windows.Forms.Label
$statusSub.ForeColor = [System.Drawing.Color]::DimGray
$statusSub.SetBounds(56, 44, 384, 22)
$form.Controls.Add($statusSub)

$sepTop = New-Separator -Y 78

# --- Setup block (only while setup is unfinished) ------------------------

$setupTitle = New-Object System.Windows.Forms.Label
$setupTitle.Text = 'FIRST, SET THIS COMPUTER UP'
$setupTitle.Font = New-Object System.Drawing.Font('Segoe UI', 8, [System.Drawing.FontStyle]::Bold)
$setupTitle.ForeColor = [System.Drawing.Color]::FromArgb(148, 55, 89)   # church maroon
$setupTitle.SetBounds(20, 90, 416, 18)
$form.Controls.Add($setupTitle)

# Four checks, in the order they have to happen. The wording names what an
# editor would recognise, with the real tool in brackets so Chris can still
# tell which one failed from a photo of the screen.
$stepDefs = @(
  @{ Key = 'ToolsOk'; Text = 'Install the programs it needs (Git and Node.js)' },
  @{ Key = 'RepoOk';  Text = 'Get the website files' },
  @{ Key = 'DepsOk';  Text = 'Install the building blocks' },
  @{ Key = 'EnvOk';   Text = 'Create the settings file' }
)

$stepControls = @()
for ($i = 0; $i -lt $stepDefs.Count; $i++) {
  $y = 112 + ($i * 26)

  $mark = New-Object System.Windows.Forms.Label
  $mark.Font = New-Object System.Drawing.Font('Segoe UI', 11, [System.Drawing.FontStyle]::Bold)
  $mark.SetBounds(22, $y, 24, 22)
  $form.Controls.Add($mark)

  $text = New-Object System.Windows.Forms.Label
  $text.SetBounds(48, ($y + 2), 388, 22)
  $text.Text = $stepDefs[$i].Text
  $form.Controls.Add($text)

  $stepControls += [pscustomobject]@{ Key = $stepDefs[$i].Key; Mark = $mark; Text = $text }
}

$btnSetup = New-Button 'Set Up This Computer' 18 220 416 44 $true
$sepSetup = New-Separator -Y 278

$setupControls = @($setupTitle, $btnSetup, $sepSetup)
foreach ($s in $stepControls) { $setupControls += $s.Mark; $setupControls += $s.Text }

# --- Main controls -------------------------------------------------------
# Base Y positions assume the setup block is hidden; Set-Layout shifts them
# down by $SETUP_OFFSET while it is showing.

$btnOpenSite   = New-Button 'Look at the Website' 18 90 203 46 $true
$btnOpenEditor = New-Button 'Edit the Words'      231 90 203 46 $true
$btnStartStop  = New-Button 'Start the Website'   18 144 416 46 $true

$sepMid = New-Separator -Y 206

$btnUpdate  = New-Button 'Get the Latest Version' 18 218 416 40
$btnHelper  = New-Button 'Edit with a Helper'     18 264 416 40
$btnPublish = New-Button 'Publish My Changes'     18 310 416 40
$btnUndo    = New-Button 'Undo My Changes'        18 356 416 40

$sepBottom = New-Separator -Y 408

$btnHelp    = New-Button 'Help'         18 420 130 34
$btnDetails = New-Button 'Show Details' 304 420 130 34

# --- Details pane --------------------------------------------------------

$logBox = New-Object System.Windows.Forms.TextBox
$logBox.Multiline = $true
$logBox.ReadOnly = $true
$logBox.ScrollBars = 'Vertical'
$logBox.Font = New-Object System.Drawing.Font('Consolas', 9)
$logBox.BackColor = [System.Drawing.Color]::FromArgb(248, 248, 248)
$logBox.SetBounds(18, 466, 416, 180)
$logBox.Visible = $false
$form.Controls.Add($logBox)

$btnCopy = New-Button 'Copy for Chris' 18 656 200 34
$btnCopy.Visible = $false

$copyHint = New-Object System.Windows.Forms.Label
$copyHint.Text = 'Then send that to Chris.'
$copyHint.ForeColor = [System.Drawing.Color]::DimGray
$copyHint.SetBounds(226, 664, 216, 22)
$copyHint.Visible = $false
$form.Controls.Add($copyHint)

# Every control below the setup block, with the Y it sits at when the setup
# block is hidden. One list, so a layout change cannot miss one.
$mainLayout = @(
  @{ C = $btnOpenSite; Y = 90 },  @{ C = $btnOpenEditor; Y = 90 },  @{ C = $btnStartStop; Y = 144 },
  @{ C = $sepMid;      Y = 206 }, @{ C = $btnUpdate;     Y = 218 }, @{ C = $btnHelper;    Y = 264 },
  @{ C = $btnPublish;  Y = 310 }, @{ C = $btnUndo;       Y = 356 }, @{ C = $sepBottom;    Y = 408 },
  @{ C = $btnHelp;     Y = 420 }, @{ C = $btnDetails;    Y = 420 }, @{ C = $logBox;       Y = 466 },
  @{ C = $btnCopy;     Y = 656 }, @{ C = $copyHint;      Y = 664 }
)

function Set-Layout {
  <#
    Position everything below the status block, and size the window to fit
    exactly what is on screen. Driven by two booleans, so the window height
    and the visible controls cannot disagree.
  #>
  param([bool]$ShowSetup, [bool]$ShowDetails)

  $offset = 0
  if ($ShowSetup) { $offset = $SETUP_OFFSET }

  foreach ($c in $setupControls) { $c.Visible = $ShowSetup }
  foreach ($item in $mainLayout) { $item.C.Top = $item.Y + $offset }

  $logBox.Visible   = $ShowDetails
  $btnCopy.Visible  = $ShowDetails
  $copyHint.Visible = $ShowDetails

  $height = $COLLAPSED_HEIGHT + $offset
  if ($ShowDetails) { $height += $DETAILS_EXTRA }
  $form.ClientSize = New-Object System.Drawing.Size(452, $height)
}

# --- Rendering -----------------------------------------------------------

function Set-Status {
  param([string]$Color, [string]$Text, [string]$Sub)
  $dot.ForeColor = [System.Drawing.Color]::FromName($Color)
  $statusText.Text = $Text
  $statusSub.Text = $Sub
}

function Update-Ui {
  <#
    One function owns every button's enabled state. Scattering that logic
    across handlers is how you end up with a "Publish" button that works
    while a publish is already running.

    -Force re-reads the expensive things (git, node versions) immediately
    instead of waiting for their caches to age.
  #>
  param([switch]$Force)

  $busy = ($null -ne $script:BusyProcess)

  # Prerequisites shell out to node, so they are cached. They only really
  # change when setup runs, and that always ends with a forced refresh.
  if ($Force -or $null -eq $script:Prereqs -or ((Get-Date) - $script:LastPrereqCheck).TotalSeconds -ge 30) {
    try { $script:Prereqs = Get-Prerequisites } catch { }
    $script:LastPrereqCheck = Get-Date
  }
  $pre = $script:Prereqs
  $ready = ($null -ne $pre -and $pre.Ready)

  # Setup block: on screen until the computer is ready, then gone.
  $wantSetup = (-not $ready)
  if ($wantSetup -ne $script:SetupVisible) {
    $script:SetupVisible = $wantSetup
    Set-Layout -ShowSetup $wantSetup -ShowDetails $script:DetailsOpen
  }

  if ($wantSetup -and $null -ne $pre) {
    foreach ($s in $stepControls) {
      $done = [bool]$pre.($s.Key)
      if ($done) {
        $s.Mark.Text = [string][char]0x2713                     # check mark
        $s.Mark.ForeColor = [System.Drawing.Color]::ForestGreen
        $s.Text.ForeColor = [System.Drawing.Color]::DimGray
      } else {
        $s.Mark.Text = [string][char]0x25CB                     # hollow circle
        $s.Mark.ForeColor = [System.Drawing.Color]::Silver
        $s.Text.ForeColor = [System.Drawing.Color]::Black
      }
    }
  }

  $status = Get-SiteStatus

  if ($busy) {
    Set-Status 'Goldenrod' $script:BusyLabel 'Please wait - this can take a few minutes.'
  } elseif (-not $ready) {
    Set-Status 'Goldenrod' 'This computer needs setting up' 'It only has to be done once. Start below.'
  } else {
    switch ($status.State) {
      'running'  { Set-Status 'ForestGreen' 'The website is running' "Ready at $script:SiteUrl" }
      'starting' { Set-Status 'Goldenrod' 'Starting up...' 'This takes up to a minute the first time.' }
      'crashed'  { Set-Status 'Firebrick' 'The website stopped unexpectedly' 'Click "Show Details", then "Copy for Chris".' }
      default    { Set-Status 'Silver' 'The website is not running' 'Click "Start the Website" to begin.' }
    }
  }

  $running = ($status.State -eq 'running')

  $btnSetup.Enabled = (-not $busy)

  # Nothing below the setup block can work until the computer is ready, so
  # none of it is clickable until then.
  $btnOpenSite.Enabled   = ($ready -and $running -and -not $busy)
  $btnOpenEditor.Enabled = ($ready -and $running -and -not $busy)
  $btnStartStop.Enabled  = ($ready -and -not $busy -and $status.State -ne 'starting')

  if ($running -or $status.State -eq 'starting') {
    $btnStartStop.Text = 'Stop the Website'
  } else {
    $btnStartStop.Text = 'Start the Website'
  }

  # Updating while the servers are running would pull files out from under
  # them, so it is offered only when the site is stopped.
  $btnUpdate.Enabled  = ($ready -and -not $busy -and $status.State -ne 'running' -and $status.State -ne 'starting')
  $btnHelper.Enabled  = ($ready -and -not $busy)
  $btnHelp.Enabled    = $true
  $btnDetails.Enabled = $true

  # Publish and Undo are pointless with nothing changed, and saying so by
  # greying them out is quieter than a dialog that says "nothing to do".
  #
  # Answering that means running git, which is far too heavy for a 2-second
  # timer - it would spawn a process 30 times a minute for the life of the
  # window. Cache it, refresh every ~10 seconds, and refresh immediately
  # whenever a job finishes (that is when it can actually have changed).
  if ($busy -or -not $ready) {
    $btnPublish.Enabled = $false
    $btnUndo.Enabled = $false
  } else {
    if ($Force -or ((Get-Date) - $script:LastChangeCheck).TotalSeconds -ge 10) {
      try { $script:HasChanges = ((Get-ChangeSummary).Count -gt 0) } catch { $script:HasChanges = $false }
      $script:LastChangeCheck = Get-Date
    }
    $btnPublish.Enabled = $script:HasChanges
    $btnUndo.Enabled = $script:HasChanges
  }

  if ($script:DetailsOpen) {
    $logBox.Text = Get-RecentLog -Lines 60
    $logBox.SelectionStart = $logBox.TextLength
    $logBox.ScrollToCaret()
  }
}

# --- Running the scripts -------------------------------------------------

function Start-Job-Script {
  <#
    Launch one of the scripts as a separate hidden PowerShell process and
    let the timer notice when it finishes. Running them inline would freeze
    the window, which looks exactly like a crash.
  #>
  param([string]$Script, [string[]]$ScriptArgs = @(), [string]$Label)

  if ($script:BusyProcess) { return }

  $argList = @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $PSScriptRoot $Script)) + $ScriptArgs
  $script:BusyLabel = $Label
  $script:BusyProcess = Start-Process -FilePath 'powershell.exe' -ArgumentList $argList `
    -WorkingDirectory $root -WindowStyle Hidden -PassThru
  Update-Ui
}

# --- Handlers ------------------------------------------------------------

$btnSetup.Add_Click({
  $pre = $script:Prereqs
  # setup.ps1 can install tools, dependencies and the settings file, but it
  # cannot turn a plain folder into a clone. A ZIP downloaded from GitHub
  # has no history, and silently cloning a second copy elsewhere would
  # leave an editor with two folders and no idea which one is live.
  if ($null -ne $pre -and -not $pre.RepoOk) {
    Show-Problem ("This copy of the website is missing its history, so it can never update or publish." + [Environment]::NewLine + [Environment]::NewLine +
                  'It was probably unpacked from a plain ZIP. Ask Chris to reinstall it using "Install MPCBC Website".')
    return
  }
  Start-Job-Script -Script 'setup.ps1' -ScriptArgs @('-Quiet') -Label 'Setting up this computer...'
})

$btnOpenSite.Add_Click({ Open-Url $script:SiteUrl })
$btnOpenEditor.Add_Click({ Open-Url $script:AdminUrl })

$btnStartStop.Add_Click({
  $status = Get-SiteStatus
  if ($status.State -eq 'running' -or $status.State -eq 'starting') {
    Start-Job-Script -Script 'stop.ps1' -Label 'Stopping...'
  } else {
    Start-Job-Script -Script 'start.ps1' -ScriptArgs @('-NoWait') -Label 'Starting the website...'
  }
})

$btnUpdate.Add_Click({
  Start-Job-Script -Script 'setup.ps1' -ScriptArgs @('-Quiet') -Label 'Getting the latest version...'
})

$btnPublish.Add_Click({ Start-Job-Script -Script 'publish.ps1' -Label 'Publishing...' })
$btnUndo.Add_Click({ Start-Job-Script -Script 'undo.ps1' -Label 'Undoing...' })

$btnHelp.Add_Click({
  $local = Join-Path $root 'GUIDE.md'
  if (Test-Path $local) { Start-Process $local } else { Open-Url $script:GuideUrl }
})

# The helper menu. A dropdown rather than a numbered list typed into a
# console - the point is that nobody has to type anything.
$menu = New-Object System.Windows.Forms.ContextMenuStrip
foreach ($entry in @(
  @{ Text = 'VS Code  -  edit the files myself'; Tool = 'vscode' },
  @{ Text = 'Claude Code  -  ask in plain English'; Tool = 'claude' },
  @{ Text = 'Codex  -  ask in plain English'; Tool = 'codex' },
  @{ Text = 'Antigravity  -  ask in plain English'; Tool = 'antigravity' }
)) {
  $item = $menu.Items.Add($entry.Text)
  $item.Tag = $entry.Tool
  $item.Add_Click({
    param($sender, $e)
    Start-Job-Script -Script 'open-tools.ps1' -ScriptArgs @('-Tool', $sender.Tag) -Label "Opening $($sender.Tag)..."
  })
}
$btnHelper.Add_Click({ $menu.Show($btnHelper, 0, $btnHelper.Height) })

$btnDetails.Add_Click({
  $script:DetailsOpen = -not $script:DetailsOpen
  if ($script:DetailsOpen) { $btnDetails.Text = 'Hide Details' } else { $btnDetails.Text = 'Show Details' }
  Set-Layout -ShowSetup $script:SetupVisible -ShowDetails $script:DetailsOpen
  Update-Ui
})

$btnCopy.Add_Click({
  # The setup line is worth its space: nearly every "it does not work"
  # message turns out to be one of these five being false.
  $pre = $script:Prereqs
  $preLine = 'unknown'
  if ($null -ne $pre) {
    $preLine = "git=$($pre.GitOk) node=$($pre.NodeOk)(v$($pre.NodeMajor)) files=$($pre.RepoOk) deps=$($pre.DepsOk) env=$($pre.EnvOk)"
  }
  $report = @(
    'MPCBC website problem report',
    "When:     $(Get-Date -Format 'yyyy-MM-dd HH:mm')",
    "Computer: $env:COMPUTERNAME",
    "Folder:   $root",
    "Setup:    $preLine",
    '',
    (Get-RecentLog -Lines 50)
  ) -join "`r`n"
  try {
    Set-Clipboard -Value $report
    Show-Info ("Copied." + [Environment]::NewLine + [Environment]::NewLine +
               'Paste it into a message to Chris - that is everything he needs.')
  } catch {
    Show-Problem 'The details could not be copied. Please take a photo of this window instead.'
  }
})

# --- Timer ---------------------------------------------------------------

$timer = New-Object System.Windows.Forms.Timer
$timer.Interval = 2000
$timer.Add_Tick({
  if ($script:BusyProcess) {
    if ($script:BusyProcess.HasExited) {
      Write-Log "Job '$script:BusyLabel' finished with exit code $($script:BusyProcess.ExitCode)."
      $script:BusyProcess = $null
      $script:BusyLabel = ''
      # A job just ended: this is exactly when the prerequisites and the
      # Publish/Undo state may have changed, so don't wait for the caches.
      Update-Ui -Force
      return
    }
  }
  Update-Ui
})

# --- Closing -------------------------------------------------------------

$form.Add_FormClosing({
  param($sender, $e)

  if ($script:BusyProcess) {
    if (-not (Confirm-Action -DefaultYes $false -Title 'Still working' -Message (
        "Something is still running." + [Environment]::NewLine + [Environment]::NewLine + 'Close anyway?'))) {
      $e.Cancel = $true
      return
    }
  }

  # A leaked Tina server on port 4001 is exactly what makes tomorrow's
  # launch fail, so never let the window close over a running site without
  # asking.
  $status = Get-SiteStatus
  if ($status.State -eq 'running' -or $status.State -eq 'starting') {
    if (Confirm-Action -DefaultYes $true -Title 'Stop the website?' -Message (
        "The website is still running on this computer." + [Environment]::NewLine + [Environment]::NewLine +
        'Stop it as well? (The published website is not affected.)')) {
      & (Join-Path $PSScriptRoot 'stop.ps1') | Out-Null
    }
  }
  Write-Log '--- Control panel closed ---'
})

# --- Go ------------------------------------------------------------------

Set-Layout -ShowSetup $true -ShowDetails $false
Update-Ui -Force
$timer.Start()
[void]$form.ShowDialog()
$timer.Stop()
$timer.Dispose()
$form.Dispose()
