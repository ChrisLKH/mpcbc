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
    * Buttons enable and disable by state, so a wrong click is usually
      impossible rather than merely discouraged.
    * The main screen only ever shows plain sentences. Technical detail
      lives behind "Show Details", with "Copy for Chris" beside it -
      because "scroll up and find the red line" is not something a
      non-technical user can do.

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

$COLLAPSED_HEIGHT = 470
$EXPANDED_HEIGHT  = 700

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

New-Separator -Y 78

# --- Buttons -------------------------------------------------------------

$btnOpenSite   = New-Button 'Look at the Website' 18 92 203 46 $true
$btnOpenEditor = New-Button 'Edit the Words'      231 92 203 46 $true
$btnStartStop  = New-Button 'Start the Website'   18 146 416 46 $true

New-Separator -Y 208

$btnUpdate  = New-Button 'Get the Latest Version' 18 220 416 40
$btnHelper  = New-Button 'Edit with a Helper'     18 266 416 40
$btnPublish = New-Button 'Publish My Changes'     18 312 416 40
$btnUndo    = New-Button 'Undo My Changes'        18 358 416 40

New-Separator -Y 410

$btnHelp    = New-Button 'Help'         18 422 130 34
$btnDetails = New-Button 'Show Details' 304 422 130 34

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

    -Force re-reads the expensive things (git) immediately instead of
    waiting for the cache to age.
  #>
  param([switch]$Force)

  $busy = ($null -ne $script:BusyProcess)
  $status = Get-SiteStatus

  if ($busy) {
    Set-Status 'Goldenrod' $script:BusyLabel 'Please wait - this can take a few minutes.'
  } else {
    switch ($status.State) {
      'running' {
        Set-Status 'ForestGreen' 'The website is running' "Ready at $script:SiteUrl"
      }
      'starting' {
        Set-Status 'Goldenrod' 'Starting up...' 'This takes up to a minute the first time.'
      }
      'crashed' {
        Set-Status 'Firebrick' 'The website stopped unexpectedly' 'Click "Show Details", then "Copy for Chris".'
      }
      default {
        Set-Status 'Silver' 'The website is not running' 'Click "Start the Website" to begin.'
      }
    }
  }

  $running = ($status.State -eq 'running')

  $btnOpenSite.Enabled   = ($running -and -not $busy)
  $btnOpenEditor.Enabled = ($running -and -not $busy)

  $btnStartStop.Enabled = (-not $busy -and $status.State -ne 'starting')
  if ($running -or $status.State -eq 'starting') {
    $btnStartStop.Text = 'Stop the Website'
  } else {
    $btnStartStop.Text = 'Start the Website'
  }

  # Updating while the servers are running would pull files out from under
  # them, so it is offered only when the site is stopped.
  $btnUpdate.Enabled  = (-not $busy -and $status.State -ne 'running' -and $status.State -ne 'starting')
  $btnHelper.Enabled  = (-not $busy)
  $btnHelp.Enabled    = $true
  $btnDetails.Enabled = $true

  # Publish and Undo are pointless with nothing changed, and saying so by
  # greying them out is quieter than a dialog that says "nothing to do".
  #
  # Answering that means running git, which is far too heavy for a 2-second
  # timer - it would spawn a process 30 times a minute for the life of the
  # window. Cache it, refresh every ~10 seconds, and refresh immediately
  # whenever a job finishes (that is when it can actually have changed).
  if ($busy) {
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

  if ($script:DetailsOpen) { $logBox.Text = Get-RecentLog -Lines 60; $logBox.SelectionStart = $logBox.TextLength; $logBox.ScrollToCaret() }
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

$btnPublish.Add_Click({
  Start-Job-Script -Script 'publish.ps1' -Label 'Publishing...'
})

$btnUndo.Add_Click({
  Start-Job-Script -Script 'undo.ps1' -Label 'Undoing...'
})

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
  $logBox.Visible = $script:DetailsOpen
  $btnCopy.Visible = $script:DetailsOpen
  $copyHint.Visible = $script:DetailsOpen
  if ($script:DetailsOpen) {
    $btnDetails.Text = 'Hide Details'
    $form.ClientSize = New-Object System.Drawing.Size(452, $EXPANDED_HEIGHT)
  } else {
    $btnDetails.Text = 'Show Details'
    $form.ClientSize = New-Object System.Drawing.Size(452, $COLLAPSED_HEIGHT)
  }
  Update-Ui
})

$btnCopy.Add_Click({
  $report = @(
    "MPCBC website problem report",
    "When:     $(Get-Date -Format 'yyyy-MM-dd HH:mm')",
    "Computer: $env:COMPUTERNAME",
    "Folder:   $root",
    "",
    (Get-RecentLog -Lines 50)
  ) -join "`r`n"
  try {
    Set-Clipboard -Value $report
    Show-Info "Copied.`r`n`r`nPaste it into a message to Chris - that is everything he needs."
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
      # A job just ended: this is exactly when Publish/Undo may have
      # become relevant, so don't wait for the cache to age out.
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
        "Something is still running.`r`n`r`nClose anyway?"))) {
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
        "The website is still running on this computer.`r`n`r`n" +
        "Stop it as well? (The published website is not affected.)")) {
      & (Join-Path $PSScriptRoot 'stop.ps1') | Out-Null
    }
  }
  Write-Log '--- Control panel closed ---'
})

# --- Go ------------------------------------------------------------------

Update-Ui -Force
$timer.Start()
[void]$form.ShowDialog()
$timer.Stop()
$timer.Dispose()
$form.Dispose()
