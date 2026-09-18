<#
.SYNOPSIS
  Stops the MPCBC website. Windows.

.DESCRIPTION
  Takes down both hidden servers and clears the PID file.

  This exists because a leaked Tina server holding port 4001 is precisely
  what makes tomorrow's launch fail with a message nobody can act on. Every
  path that ends a session - the Stop button, closing the control panel -
  comes through here.

  Falls back to killing whatever owns the two ports when the PID file is
  missing or stale, so an editor is never stuck with a half-running site
  they have no way to clear.
#>
[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

Write-Log 'Stop requested.'

$stopped = 0

# 1. The processes we launched.
$pids = Get-ServerPids
if ($pids) {
  foreach ($id in @($pids.tina, $pids.astro)) {
    if ($id -and $id -gt 0 -and (Get-Process -Id $id -ErrorAction SilentlyContinue)) {
      Stop-ProcessTree -ProcessId $id
      $stopped++
    }
  }
}
Clear-ServerPids

# 2. Anything still holding the ports. The PID file can be stale - the
#    machine was rebooted, or a previous run was killed - and "stop" has to
#    actually stop things or it is worse than useless.
foreach ($port in @($script:TinaPort, $script:AstroPort)) {
  if (Test-PortOpen -Port $port) {
    $owner = Get-PortOwner -Port $port
    if ($owner -and $owner.ProcessName -match 'node|cmd') {
      Write-Log "Clearing leftover process $($owner.ProcessName) ($($owner.Id)) on port $port."
      Stop-ProcessTree -ProcessId $owner.Id
      $stopped++
    }
  }
}

# Ports take a moment to release after the process dies.
Start-Sleep -Milliseconds 700
Write-Log "Stopped ($stopped process tree(s))."
exit 0
