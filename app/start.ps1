<#
.SYNOPSIS
  Starts the MPCBC website on this machine. Windows.

.DESCRIPTION
  Tina's content server has to be up and finished indexing before Astro
  starts, so this launches Tina, waits for it to answer on :4001, then
  launches Astro on :4321.

  Both run HIDDEN, with their output redirected to logs\tina.log and
  logs\astro.log. That is deliberate, and it removes three traps that
  reliably defeat non-technical users:

    * Console QuickEdit. Clicking inside a console window selects text and
      SUSPENDS the process. The site freezes with no error and no clue.
    * "Leave this window open." They close it.
    * Ctrl+C to stop. Nobody discovers that. Now it is a button.

  The cost is that errors are no longer on screen, which is paid for by the
  log files plus the control panel's status dot and "Show Details" pane.

  ASTRO_DEV_BACKGROUND is set on purpose: Astro 7 otherwise runs the dev
  server as a detached child with a hardcoded 30-second window to claim its
  lock file, which a cold Vite cache regularly misses. Running inline
  removes that failure entirely.

.PARAMETER NoWait
  Return as soon as both servers are launched, without waiting for them to
  answer. The control panel uses this - it polls the ports itself.

.EXAMPLE
  powershell -ExecutionPolicy Bypass -File .\app\start.ps1
#>
[CmdletBinding()]
param([switch]$NoWait)

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

$root = Get-RepoRoot
Set-Location $root

Write-Log 'Start requested.'

# --- Already running? ---------------------------------------------------
# Never answer this with "port in use". Either it is our own site, in which
# case there is nothing to do, or it is a stranger, which needs saying in
# words rather than an error code.

$status = Get-SiteStatus
if ($status.State -eq 'running') {
  Write-Log 'Already running - nothing to do.'
  exit 0
}

foreach ($port in @($script:TinaPort, $script:AstroPort)) {
  if (Test-PortOpen -Port $port) {
    $owner = Get-PortOwner -Port $port
    $known = Get-ServerPids
    $isOurs = $known -and (@($known.tina, $known.astro) -contains $(if ($owner) { $owner.Id } else { -1 }))
    if (-not $isOurs -and $owner -and $owner.ProcessName -notmatch 'node|cmd') {
      Show-Problem ("Another program on this computer is already using the address the website needs (port $port).`r`n`r`n" +
                    "It is called '$($owner.ProcessName)'. Close it and try again, or tell the website administrator.")
      exit 1
    }
  }
}

# --- Prerequisites ------------------------------------------------------

if (-not (Test-Path (Join-Path $root 'node_modules'))) {
  Show-Problem ("The website's building blocks are missing.`r`n`r`n" +
                'Click "Install" first - that sets them up. It takes a few minutes the first time.')
  exit 1
}

if (-not (Get-NodeExe)) {
  Show-Problem ("Node.js is not installed on this computer, and the website needs it.`r`n`r`n" +
                'Tell the website administrator - this needs setting up once.')
  exit 1
}

# --- Launch -------------------------------------------------------------
# Both servers go through cmd.exe /c so npx and npm resolve their shims
# normally. The recorded PID is cmd's; Stop-ProcessTree takes the whole
# tree, which matters because node spawns children that outlive a plain
# Stop-Process.

function Start-Hidden {
  param([string]$Command, [string]$LogName)

  $log = Get-LogPath $LogName
  # Truncate per run: a log that only covers this attempt is far easier to
  # read than one going back weeks.
  '' | Out-File -FilePath $log -Encoding utf8

  $proc = Start-Process -FilePath $env:ComSpec `
    -ArgumentList @('/c', $Command) `
    -WorkingDirectory $root `
    -WindowStyle Hidden `
    -RedirectStandardOutput $log `
    -RedirectStandardError (Get-LogPath "$LogName-errors") `
    -PassThru
  return $proc
}

Write-Log 'Starting the content service (Tina).'
$tina = Start-Hidden -Command 'npx tinacms dev' -LogName 'tina'
Save-ServerPids -TinaPid $tina.Id -AstroPid 0

if (-not (Wait-ForPort -Port $script:TinaPort -Seconds 180)) {
  Write-Log 'Tina never came up within 180s.' 'error'
  Stop-ProcessTree -ProcessId $tina.Id
  Clear-ServerPids
  Show-Problem ("The website's content service did not start.`r`n`r`n" +
                'Open "Show Details", click "Copy Log", and send that to the website administrator.')
  exit 1
}
Write-Log 'Content service ready.'

# Inherited by the child process. Set here rather than in package.json so
# the workaround stays visible to whoever reads this file.
$env:ASTRO_DEV_BACKGROUND = '1'

Write-Log 'Starting the website (Astro).'
$astro = Start-Hidden -Command 'npm run dev' -LogName 'astro'
Save-ServerPids -TinaPid $tina.Id -AstroPid $astro.Id

if (-not $NoWait) {
  if (-not (Wait-ForPort -Port $script:AstroPort -Seconds 180)) {
    Write-Log 'Astro never came up within 180s.' 'error'
    Show-Problem ("The website did not finish starting.`r`n`r`n" +
                  'Open "Show Details", click "Copy Log", and send that to the website administrator.')
    exit 1
  }
  Write-Log "Website ready at $script:SiteUrl"
}

exit 0
