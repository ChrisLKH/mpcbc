<#
.SYNOPSIS
  Everything needed to sit down and work on the website, in one go. Windows.

.DESCRIPTION
  This is what the control panel's single "Update & Start" button runs, and
  it is the whole reason an editor only ever meets one button:

    1. setup.ps1  - installs Git and Node if they are missing, collects
                    updates from GitHub, installs the building blocks, and
                    writes the settings file. Each of those steps decides
                    for itself whether there is anything to do, so a
                    routine launch is quick and a first launch is complete.

    2. start.ps1  - starts the two servers and waits for the site to answer.

  Both are run as separate processes rather than dot-sourced, because each
  ends in `exit` and would otherwise take this script down with it.

  Nothing here publishes anything. Sending work to the live site stays a
  separate, deliberate button - see publish.ps1.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

Set-Location (Get-RepoRoot)
Write-Log '=== Update & Start ==='

function Invoke-Step {
  param([string]$Script, [string[]]$ScriptArgs = @())
  $argList = @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', (Join-Path $PSScriptRoot $Script)) + $ScriptArgs
  $proc = Start-Process -FilePath 'powershell.exe' -ArgumentList $argList `
    -WorkingDirectory (Get-RepoRoot) -WindowStyle Hidden -Wait -PassThru
  return $proc.ExitCode
}

# --- 1. Get this computer ready -----------------------------------------

$code = Invoke-Step -Script 'setup.ps1' -ScriptArgs @('-Quiet')
if ($code -ne 0) {
  Write-Log "Setup step failed with exit code $code; not starting the site." 'error'
  # setup.ps1 has already shown the editor a dialog explaining what went
  # wrong, so say nothing further here and avoid a second popup.
  exit $code
}

# --- 2. Start the site ---------------------------------------------------

$code = Invoke-Step -Script 'start.ps1'
if ($code -ne 0) {
  Write-Log "Start step failed with exit code $code." 'error'
  exit $code
}

Write-Log 'Update & Start finished; site is up.'
exit 0
