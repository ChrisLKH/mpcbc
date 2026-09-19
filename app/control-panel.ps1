<#
.SYNOPSIS
  The MPCBC website control panel. Windows.

.DESCRIPTION
  One window, and on any given day one button.

  "Update & Start" runs the whole chain in start-working.ps1:
  install anything missing, collect updates from GitHub, install the
  building blocks, start the site. Each step decides for itself whether
  there is work to do, so it is both a first-time install and a two-second
  routine launch. It becomes "Stop" once the site is up.

  "Install" runs the same chain, and exists so that its
  greyed-out state can answer "does this computer need anything?" without
  anyone reading a word.

  Design rules, all of them load-bearing:

    * Nothing is changed before that click. On launch the panel only
      FETCHES - which updates our record of what is on GitHub and touches
      no file - so it can say "3 updates to collect" without having
      collected them.
    * Publishing is never automatic and never a forced end-of-wizard
      prompt. Publish and Undo are ordinary buttons that light up when
      there is something to publish, so closing the window and coming back
      tomorrow is always a safe answer.
    * No console. The dev servers run hidden (see start.ps1 for why), and
      every question is a dialog.
    * Nothing that cannot work is clickable. Get-Prerequisites decides.
    * The main screen shows plain sentences only. Technical detail lives
      behind "Show Details", with "Copy Log" beside it.

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

$script:BusyProcess = $null
$script:BusyLabel   = ''
$script:DetailsOpen = $false
$script:HasChanges  = $false
$script:LastChangeCheck = [datetime]::MinValue
$script:Prereqs     = $null
$script:LastPrereqCheck = [datetime]::MinValue
$script:SetupVisible = $true
$script:Updates     = 0

$SETUP_OFFSET     = 144
$COLLAPSED_HEIGHT = 470
$DETAILS_EXTRA    = 240

# --- Window --------------------------------------------------------------

$form = New-Object System.Windows.Forms.Form
$form.Text = 'MPCBC Website'
$form.ClientSize = New-Object System.Drawing.Size(452, $COLLAPSED_HEIGHT)
$form.StartPosition = 'CenterScreen'
$form.FormBorderStyle = 'FixedSingle'
$form.MaximizeBox = $false
$form.Font = New-Object System.Drawing.Font('Segoe UI', 10)
$form.BackColor = [System.Drawing.Color]::White

# The church logo, in the title bar, the taskbar and Alt-Tab. Built by
# app\make-icon.ps1; missing is not fatal, it just falls back to the
# default PowerShell icon.
try {
  $iconPath = Join-Path $PSScriptRoot 'mpcbc.ico'
  if (Test-Path $iconPath) { $form.Icon = New-Object System.Drawing.Icon($iconPath) }
} catch {
  Write-Log "Could not load the window icon: $($_.Exception.Message)" 'warn'
}

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
$dot.Text = [char]0x25CF
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

# --- Setup checklist (only while setup is unfinished) --------------------
# Not a to-do list for the editor: every one of these is done by the Install
# button. It is here so that a five-minute first run looks like
# progress rather than a hang, and so a photo of the screen tells the website administrator
# exactly which part failed.

$setupTitle = New-Object System.Windows.Forms.Label
$setupTitle.Text = 'THIS COMPUTER STILL NEEDS'
$setupTitle.Font = New-Object System.Drawing.Font('Segoe UI', 8, [System.Drawing.FontStyle]::Bold)
$setupTitle.ForeColor = [System.Drawing.Color]::FromArgb(148, 55, 89)   # church maroon
$setupTitle.SetBounds(20, 90, 416, 18)
$form.Controls.Add($setupTitle)

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

$sepSetup = New-Separator -Y 222

$setupControls = @($setupTitle, $sepSetup)
foreach ($s in $stepControls) { $setupControls += $s.Mark; $setupControls += $s.Text }

# --- Main controls -------------------------------------------------------
# Base Y positions assume the checklist is hidden; Set-Layout shifts them
# down by $SETUP_OFFSET while it is showing.

# Install is its own button, and its greyed-out state is the message: if it
# is clickable, this computer needs something; if it is grey, it does not.
# That stays true as the project grows, because Get-Prerequisites compares
# the lockfile against the last install rather than merely checking that
# node_modules exists.
#
# The doubled && is not a typo. A Button treats a single & as the marker for
# a keyboard accelerator and eats it, so 'Update & Start' would render as
# "Update _Start". && is how you ask for a literal ampersand.
$btnInstall    = New-Button 'Install'          18 90 416 44 $true
$btnPrimary    = New-Button 'Update && Start'  18 142 416 50 $true
$btnOpenSite   = New-Button 'Preview'          18 200 203 42 $true
$btnOpenEditor = New-Button 'Edit Content'     231 200 203 42 $true

$sepMid = New-Separator -Y 254

$btnHelper  = New-Button 'Code'                 18 266 416 40
$btnPublish = New-Button 'Publish to Live Site' 18 312 416 40
$btnUndo    = New-Button 'Undo All Changes'     18 358 416 40

$sepBottom = New-Separator -Y 410

$btnHelp    = New-Button 'Help'         18 422 130 34
$btnDetails = New-Button 'Show Details' 304 422 130 34

# --- Details pane --------------------------------------------------------

$logBox = New-Object System.Windows.Forms.TextBox
$logBox.Multiline = $true
$logBox.ReadOnly = $true
$logBox.ScrollBars = 'Vertical'
$logBox.Font = New-Object System.Drawing.Font('Consolas', 9)
$logBox.BackColor = [System.Drawing.Color]::FromArgb(248, 248, 248)
$logBox.SetBounds(18, 418, 416, 180)
$logBox.Visible = $false
$form.Controls.Add($logBox)

$btnCopy = New-Button 'Copy Log' 18 608 200 34
$btnCopy.Visible = $false

$copyHint = New-Object System.Windows.Forms.Label
$copyHint.Text = ''
$copyHint.ForeColor = [System.Drawing.Color]::DimGray
$copyHint.SetBounds(226, 616, 216, 22)
$copyHint.Visible = $false
$form.Controls.Add($copyHint)

# One list, so a layout change cannot miss a control.
$mainLayout = @(
  @{ C = $btnInstall; Y = 90 },   @{ C = $btnPrimary;  Y = 142 }, @{ C = $btnOpenSite;   Y = 200 },
  @{ C = $btnOpenEditor; Y = 200 },
  @{ C = $sepMid;     Y = 254 },  @{ C = $btnHelper;   Y = 266 }, @{ C = $btnPublish;    Y = 312 },
  @{ C = $btnUndo;    Y = 358 },  @{ C = $sepBottom;   Y = 410 }, @{ C = $btnHelp;       Y = 422 },
  @{ C = $btnDetails; Y = 422 },  @{ C = $logBox;      Y = 468 }, @{ C = $btnCopy;       Y = 658 },
  @{ C = $copyHint;   Y = 666 }
)

function Set-Layout {
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
    One function owns every button's state. Scattering that across handlers
    is how you get a Publish button that works during a publish.

    -Force re-reads the expensive things (git, node versions) instead of
    waiting for their caches to age.
  #>
  param([switch]$Force)

  $busy = ($null -ne $script:BusyProcess)

  if ($Force -or $null -eq $script:Prereqs -or ((Get-Date) - $script:LastPrereqCheck).TotalSeconds -ge 30) {
    try { $script:Prereqs = Get-Prerequisites } catch { }
    try { $script:Updates = Get-UpdateCount } catch { $script:Updates = 0 }
    $script:LastPrereqCheck = Get-Date
  }
  $pre = $script:Prereqs
  $ready = ($null -ne $pre -and $pre.Ready)

  $wantSetup = (-not $ready)
  if ($wantSetup -ne $script:SetupVisible) {
    $script:SetupVisible = $wantSetup
    Set-Layout -ShowSetup $wantSetup -ShowDetails $script:DetailsOpen
  }

  if ($wantSetup -and $null -ne $pre) {
    foreach ($s in $stepControls) {
      if ([bool]$pre.($s.Key)) {
        $s.Mark.Text = [string][char]0x2713
        $s.Mark.ForeColor = [System.Drawing.Color]::ForestGreen
        $s.Text.ForeColor = [System.Drawing.Color]::DimGray
      } else {
        $s.Mark.Text = [string][char]0x25CB
        $s.Mark.ForeColor = [System.Drawing.Color]::Silver
        $s.Text.ForeColor = [System.Drawing.Color]::Black
      }
    }
  }

  $status = Get-SiteStatus
  $running = ($status.State -eq 'running')

  if ($busy) {
    Set-Status 'Goldenrod' $script:BusyLabel 'Please wait - this can take a few minutes.'
  } elseif (-not $ready) {
    Set-Status 'Goldenrod' 'This computer needs setting up' 'Click Install below. It takes a few minutes.'
  } else {
    switch ($status.State) {
      'running'  { Set-Status 'ForestGreen' 'The website is running' "Ready at $script:SiteUrl" }
      'starting' { Set-Status 'Goldenrod' 'Starting up...' 'This takes up to a minute the first time.' }
      'crashed'  { Set-Status 'Firebrick' 'The website stopped unexpectedly' 'Click "Show Details", then "Copy Log".' }
      default {
        # The update count comes from the launch fetch. Saying it here is
        # the point of that fetch: the editor learns there is something to
        # collect, and the one button collects it.
        if ($script:Updates -gt 0) {
          $word = 'updates'
          if ($script:Updates -eq 1) { $word = 'update' }
          Set-Status 'Silver' 'Ready to start' "$($script:Updates) $word to collect, then the site opens."
        } else {
          Set-Status 'Silver' 'Ready to start' 'Everything is up to date.'
        }
      }
    }
  }

  # Install is clickable only when something actually needs installing, so
  # its state answers "does this computer need anything?" without anybody
  # reading a word. While it is lit, Update & Start is greyed: there is one
  # obvious thing to do, and doing it also collects updates and opens the
  # site, so nothing is lost by making it the only door.
  $btnInstall.Enabled = ((-not $ready) -and (-not $busy))

  if ($running -or $status.State -eq 'starting') {
    $btnPrimary.Text = 'Stop'
  } else {
    $btnPrimary.Text = 'Update && Start'
  }
  $btnPrimary.Enabled = ($ready -and -not $busy -and $status.State -ne 'starting') -or
                        (($running -or $status.State -eq 'running') -and -not $busy)

  $btnOpenSite.Enabled   = ($running -and -not $busy)
  $btnOpenEditor.Enabled = ($running -and -not $busy)
  $btnHelper.Enabled     = ($ready -and -not $busy)
  $btnHelp.Enabled       = $true
  $btnDetails.Enabled    = $true

  # Publish and Undo light up only when there is something to publish.
  # Deliberately never automatic and never forced: closing the window with
  # unpublished work is a safe, ordinary thing to do.
  #
  # Asking git is far too heavy for a 2-second timer, so it is cached and
  # refreshed whenever a job finishes - which is when it can have changed.
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
  param([string]$Script, [string[]]$ScriptArgs = @(), [string]$Label)

  if ($script:BusyProcess) { return }

  $argList = @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $PSScriptRoot $Script)) + $ScriptArgs
  $script:BusyLabel = $Label
  $script:BusyProcess = Start-Process -FilePath 'powershell.exe' -ArgumentList $argList `
    -WorkingDirectory $root -WindowStyle Hidden -PassThru
  Update-Ui
}

# --- Handlers ------------------------------------------------------------

function Test-RepoUsable {
  <#
    start-working.ps1 can install tools, collect updates and build, but it
    cannot turn a plain folder into a clone. A ZIP downloaded from GitHub
    has no history, and silently cloning a second copy elsewhere would
    leave an editor with two folders and no idea which one is live.
  #>
  $pre = $script:Prereqs
  if ($null -ne $pre -and -not $pre.RepoOk) {
    Show-Problem ('This copy of the website is missing its history, so it can never update or publish.' +
                  [Environment]::NewLine + [Environment]::NewLine +
                  'It was probably unpacked from a plain ZIP. Ask the website administrator to reinstall it using "Install MPCBC Website".')
    return $false
  }
  return $true
}

# Install runs exactly the same chain as Update & Start. Installing, then
# collecting updates, then opening the site is what somebody wants in one
# go - being returned to the window to press a second button would just be
# a chore with no decision in it.
$btnInstall.Add_Click({
  if (-not (Test-RepoUsable)) { return }
  Start-Job-Script -Script 'start-working.ps1' -Label 'Installing what is needed...'
})

$btnPrimary.Add_Click({
  $status = Get-SiteStatus
  if ($status.State -eq 'running' -or $status.State -eq 'starting') {
    Start-Job-Script -Script 'stop.ps1' -Label 'Stopping...'
    return
  }
  if (-not (Test-RepoUsable)) { return }
  Start-Job-Script -Script 'start-working.ps1' -Label 'Starting the website...'
})

$btnOpenSite.Add_Click({ Open-Url $script:SiteUrl })
$btnOpenEditor.Add_Click({ Open-Url $script:AdminUrl })
$btnPublish.Add_Click({ Start-Job-Script -Script 'publish.ps1' -Label 'Publishing...' })
$btnUndo.Add_Click({ Start-Job-Script -Script 'undo.ps1' -Label 'Undoing...' })

# Help answers two different questions depending on the day. "How do I pin
# an announcement?" wants the short reference; "I have never done this
# before" wants the full walkthrough. One button, two entries - the second
# is far more common, so it is listed first.
#
# Both are the GitHub-rendered pages, never the local .md files: opening
# those from disk hands someone raw Markdown in whatever claims the
# extension, usually Notepad.
$helpMenu = New-Object System.Windows.Forms.ContextMenuStrip
$helpCommon = $helpMenu.Items.Add('Common questions  -  how do I...?')
$helpCommon.Add_Click({ Open-Url $script:ReadmeUrl })
$helpFull = $helpMenu.Items.Add('Full guide  -  start to finish')
$helpFull.Add_Click({ Open-Url $script:GuideUrl })

$btnHelp.Add_Click({ $helpMenu.Show($btnHelp, 0, $btnHelp.Height) })

$menu = New-Object System.Windows.Forms.ContextMenuStrip
foreach ($entry in @(
  # Folder first, deliberately: it needs nothing installed and covers every
  # way of working this list does not anticipate. The rest escalate from
  # "edit it yourself" to "ask for it in words".
  @{ Text = 'Open the folder  -  see the files'; Tool = 'folder' },
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
  # Nearly every "it does not work" message turns out to be one of these
  # five being false, so the line earns its space.
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
    "Updates:  $($script:Updates) waiting",
    '',
    (Get-RecentLog -Lines 50)
  ) -join "`r`n"
  try {
    Set-Clipboard -Value $report
    Show-Info 'Copied.'
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
        'Something is still running.' + [Environment]::NewLine + [Environment]::NewLine + 'Close anyway?'))) {
      $e.Cancel = $true
      return
    }
  }

  # Closing the window stops the site. No prompt: the servers exist only to
  # serve this window, an editor has no reason to want them left behind,
  # and a leaked Tina server holding port 4001 is exactly what makes
  # tomorrow's launch fail. Asking only offered a way to get that wrong.
  #
  # Nothing is lost by stopping - the published website is untouched, and
  # unpublished edits are files on disk, not something the server holds.
  $status = Get-SiteStatus
  if ($status.State -eq 'running' -or $status.State -eq 'starting') {
    Write-Log 'Window closing - stopping the website.'
    $form.Cursor = [System.Windows.Forms.Cursors]::WaitCursor
    & (Join-Path $PSScriptRoot 'stop.ps1') | Out-Null
  }
  Write-Log '--- Control panel closed ---'
})

# --- Go ------------------------------------------------------------------

Set-Layout -ShowSetup $true -ShowDetails $false
Set-Status 'Silver' 'Checking...' 'One moment.'

# The first Update-Ui runs git and node several times over, which takes a
# few seconds on a cold disk. Doing that BEFORE ShowDialog means the editor
# double-clicks the icon and stares at an empty desktop until it finishes -
# which reads as "nothing happened" and gets the icon double-clicked again.
#
# Shown fires once the window is actually on screen, so it paints
# immediately and fills itself in a moment later.
$form.Add_Shown({
  # Ask GitHub what is there, without taking any of it. This is the only
  # thing that happens before the editor clicks, and it modifies nothing.
  Start-RemoteRefresh
  Update-Ui -Force
  $timer.Start()
})

[void]$form.ShowDialog()
$timer.Stop()
$timer.Dispose()
$form.Dispose()
