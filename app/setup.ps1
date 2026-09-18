<#
.SYNOPSIS
  Gets the MPCBC website up to date and ready to run. Windows.

.DESCRIPTION
  Safe to run as many times as you like. It pulls the latest code into the
  copy it finds, installs anything new, and makes sure this machine is in a
  state where the site can actually start.

  Installing Git and Node is NOT this script's job - by the time it runs,
  the repository exists, which means git already worked. That work belongs
  to scripts\bootstrap\Install MPCBC Website.bat, which runs before there
  is a repository to put scripts in.

.PARAMETER Path
  Where the site should live. Defaults to C:\mpcbc. Ignored when the script
  is already sitting inside a clone.

  Not Documents: that folder is routinely redirected into OneDrive by
  Known Folder Move, and OneDrive syncing node_modules (~40,000 files)
  causes sync conflicts, file locks during npm install, and a dev-server
  file watcher fighting the sync client.

.PARAMETER Start
  Launch the site as soon as setup finishes.

.PARAMETER Quiet
  Report through dialogs only, for when the control panel is driving.

.EXAMPLE
  powershell -ExecutionPolicy Bypass -File .\setup.ps1 -Start
#>
[CmdletBinding()]
param(
  [string]$Path = 'C:\mpcbc',
  [string]$Repo = 'https://github.com/ChrisLKH/mpcbc.git',
  [switch]$Start,
  [switch]$Quiet
)

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"
. "$PSScriptRoot\lib\prereqs.ps1"

function Write-Step { param($m) if (-not $Quiet) { Write-Host "`n==> $m" -ForegroundColor Magenta }; Write-Log $m }
function Write-Ok   { param($m) if (-not $Quiet) { Write-Host "    $m" -ForegroundColor DarkGray } }

function Stop-Setup {
  param($Message, $Fix)
  Write-Log "$Message $Fix" 'error'
  if (-not $Quiet) {
    Write-Host "`nStopped: $Message" -ForegroundColor Red
    if ($Fix) { Write-Host "What to do: $Fix" -ForegroundColor Yellow }
  }
  Show-Problem "$Message`r`n`r`n$Fix"
  exit 1
}

# --- 1. Prerequisites ---------------------------------------------------
# This script is the single "set this computer up" path, so a missing tool
# is something to FIX rather than something to complain about. It used to
# stop here and send people back to an installer they had long since
# deleted; now it installs what is missing, by whichever route the machine
# allows (see lib\prereqs.ps1 - winget first, portable copies when there
# are no administrator rights).
#
# PATH may also be stale from an install that finished moments ago, which
# Get-GitExe / Get-NodeExe already handle by re-reading it.

Write-Step 'Checking Git and Node'

$git = Get-GitExe
$node = Get-NodeExe
$nodeMajor = 0
if ($node) {
  try { $nodeMajor = [int]((((& $node --version) -replace '^v', '') -split '\.')[0]) } catch { $nodeMajor = 0 }
}

if (-not $git -or -not $node -or $nodeMajor -lt 20) {
  Write-Step 'Installing the programs the website needs (a few minutes)'
  $installed = Install-Prerequisites -Progress { param($m) Write-Ok $m; Write-Log $m }
  $git = $installed.GitExe
  $node = $installed.NodeExe
  $nodeMajor = 0
  if ($node) {
    try { $nodeMajor = [int]((((& $node --version) -replace '^v', '') -split '\.')[0]) } catch { $nodeMajor = 0 }
  }
}

if (-not $git) {
  Stop-Setup 'Git could not be installed on this computer.' `
             'Please tell Chris - this computer needs Git installed by hand from https://git-scm.com/downloads.'
}
if (-not $node -or $nodeMajor -lt 20) {
  Stop-Setup 'Node.js could not be installed on this computer, or the version is too old.' `
             'Please tell Chris - this computer needs Node.js 20 or newer from https://nodejs.org.'
}
Write-Ok "git and node v$nodeMajor ready"

# --- 2. Find or fetch the code ------------------------------------------
# Running from inside a clone always wins over -Path: updating the copy you
# are standing in is what you meant. This is also what keeps a developer's
# own working copy from being ignored in favour of C:\mpcbc.

$here = Split-Path -Parent $PSScriptRoot
$insideClone = (Test-Path (Join-Path $here '.git')) -and (Test-Path (Join-Path $here 'package.json'))
if ($insideClone) { $Path = $here }

# An existing install from before the move to C:\ - use it rather than
# cloning a second copy the editor would then have to choose between.
if (-not $insideClone -and -not (Test-Path (Join-Path $Path '.git'))) {
  $legacy = Join-Path ([Environment]::GetFolderPath('MyDocuments')) 'mpcbc'
  if (Test-Path (Join-Path $legacy '.git')) {
    Write-Ok "Using the existing copy in $legacy"
    $Path = $legacy
  }
}

if (Test-Path (Join-Path $Path '.git')) {
  $before = (Invoke-Git -Arguments @('rev-parse', 'HEAD') -WorkingDirectory $Path).Output
  $after = $before

  # Unpublished work wins over collecting updates. This runs as part of one
  # "Start Working" click, so stopping the whole chain here - as it used to -
  # would mean an editor with a half-finished announcement could not start
  # the site at all. Skip the pull, say so, and carry on; publish.ps1 does
  # its own pull --rebase --autostash when they are actually ready to send.
  $dirty = (Invoke-Git -Arguments @('status', '--porcelain') -WorkingDirectory $Path).Output

  if ($dirty) {
    Write-Step 'Skipping the update - you have unpublished changes'
    Write-Ok 'Your work is untouched. The update will happen after you publish.'
  } else {
    Write-Step "Updating the code in $Path"
    $pull = Invoke-Git -Arguments @('pull', '--ff-only') -WorkingDirectory $Path
    if (-not $pull.Ok) {
      # Not fatal: a network blip or a diverged branch should not stop
      # someone looking at the site. It is logged for Chris either way.
      Write-Log "Pull failed: $($pull.Error)" 'warn'
      Write-Ok 'Could not collect updates just now - carrying on with the version already here.'
    }
    $after = (Invoke-Git -Arguments @('rev-parse', 'HEAD') -WorkingDirectory $Path).Output
  }

  # --- Self-update guard ---
  # Everything here lives in the repo, so an update can replace the very
  # files that are running. .ps1 is safe - PowerShell parses the whole
  # file into memory before executing - but cmd.exe reads a .bat
  # incrementally from a byte offset, so rewriting one mid-run can resume
  # at the wrong place and behave bizarrely.
  if ($before -and $after -and $before -ne $after) {
    $touched = (Invoke-Git -Arguments @('diff', '--name-only', $before, $after) -WorkingDirectory $Path).Output
    if ($touched -match '(?im)^\s*[^\r\n]*\.bat\s*$' -or $touched -match 'control-panel\.ps1') {
      Write-Log 'Update changed the launcher itself; asking for a restart.'
      Show-Info ("The website app has been updated.`r`n`r`n" +
                 'Please close this window and open "MPCBC Website" again.')
      exit 0
    }
  }
} else {
  Write-Step "Downloading the site into $Path"
  $parent = Split-Path -Parent $Path
  if ($parent -and -not (Test-Path $parent)) {
    New-Item -ItemType Directory -Path $parent -Force | Out-Null
  }
  $clone = Invoke-Git -Arguments @('clone', $Repo, $Path) -WorkingDirectory ([Environment]::GetFolderPath('MyDocuments'))
  if (-not $clone.Ok) {
    Stop-Setup 'The website could not be downloaded.' `
               'Check that this computer is connected to the internet, then try again.'
  }
}
Write-Ok "Now at: $Path"

# --- 3. Dependencies ----------------------------------------------------

# npm install takes two to three minutes, and this script now runs on every
# "Start Working" click - so doing it unconditionally would put that wait in
# front of every session. Stamp the lockfile hash after a successful install
# and skip when nothing has changed since.
#
# The stamp lives in logs\ (gitignored), so it is per-machine and a fresh
# clone correctly starts with no stamp and installs.

$lockPath  = Join-Path $Path 'package-lock.json'
$stampPath = Join-Path (Get-LogDir) 'deps.stamp'

$lockHash = ''
if (Test-Path $lockPath) { $lockHash = (Get-FileHash -Path $lockPath -Algorithm SHA256).Hash }

$stamp = ''
if (Test-Path $stampPath) { $stamp = (Get-Content -Path $stampPath -Raw -Encoding UTF8).Trim() }

# Check the folder really is usable, not just present: an interrupted
# install leaves node_modules behind with nothing working inside it.
$depsPresent = (Test-Path (Join-Path $Path 'node_modules\astro')) -and
               (Test-Path (Join-Path $Path 'node_modules\.bin'))

if ($depsPresent -and $lockHash -and $lockHash -eq $stamp) {
  Write-Step 'Building blocks are already up to date'
} else {
  Write-Step 'Installing the site''s building blocks (1-3 minutes)'
  $npmLog = Get-LogPath 'npm-install'
  $npm = Start-Process -FilePath $env:ComSpec -ArgumentList @('/c', 'npm install --no-fund --no-audit') `
    -WorkingDirectory $Path -WindowStyle Hidden -Wait -PassThru `
    -RedirectStandardOutput $npmLog -RedirectStandardError (Get-LogPath 'npm-install-errors')

  if ($npm.ExitCode -ne 0) {
    Stop-Setup 'The website''s building blocks could not be installed.' `
               'Open "Show Details" in the app and use "Copy for Chris", then send him the message.'
  }
  if ($lockHash) { $lockHash | Out-File -FilePath $stampPath -Encoding utf8 }
  Write-Ok 'Building blocks are up to date.'
}

# --- 4. Environment file ------------------------------------------------
# Local editing runs TinaCMS in local mode, which needs no credentials at
# all. The file exists so nothing has to guess, and so the real values have
# an obvious home if the live editor is ever switched on. An existing file
# is never overwritten - it may hold Chris's real token.

$envPath = Join-Path $Path '.env'
Write-Step 'Checking the settings file'
if (Test-Path $envPath) {
  Write-Ok '.env already exists - left untouched.'
} else {
  @'
# TinaCMS credentials. Editing the website on this computer does NOT need
# real values here - the editor at localhost:4321/admin works with this
# file exactly as it is.
#
# Only fill these in to run the editor against the live site, and get them
# from Chris. Never commit this file or paste it into a chat or an email.

TINA_CLIENT_ID=
TINA_TOKEN=
TINA_BRANCH=main
'@ | Out-File -FilePath $envPath -Encoding utf8
  Write-Ok '.env created with blanks - nothing else needed for editing.'
}

if (-not (Test-EnvIgnored)) {
  Write-Log '.env is NOT covered by .gitignore.' 'error'
  Show-Problem ("Something is wrong with the website's safety settings.`r`n`r`n" +
                'Tell Chris and mention the ".env" file before you publish anything.')
}

# --- 5. Who is publishing? ----------------------------------------------
# Asked now rather than at publish time, where a missing identity aborts
# the commit with git's own "Please tell me who you are" wall of text.

[void](Confirm-GitIdentity)

# --- 6. Desktop shortcut ------------------------------------------------
# One icon. An editor should never have to find a folder.

Write-Step 'Putting a shortcut on the desktop'
try {
  $desktop = [Environment]::GetFolderPath('Desktop')
  $link = Join-Path $desktop 'MPCBC Website.lnk'
  if (-not (Test-Path $link)) {
    $shell = New-Object -ComObject WScript.Shell
    $shortcut = $shell.CreateShortcut($link)
    $shortcut.TargetPath = (Join-Path $Path 'MPCBC Website.bat')
    $shortcut.WorkingDirectory = $Path
    $shortcut.Description = 'Open the MPCBC website editor'
    $shortcut.Save()
    Write-Ok 'Shortcut created.'
  } else {
    Write-Ok 'Shortcut already there.'
  }
} catch {
  # A missing shortcut is a small inconvenience, not a reason to fail setup.
  Write-Log "Could not create the desktop shortcut: $($_.Exception.Message)" 'warn'
}

# --- 7. Done ------------------------------------------------------------

Write-Log "Setup complete at $Path"
if (-not $Quiet) {
  Write-Host "`nReady." -ForegroundColor Green
  Write-Host "The site lives in $Path"
}

if ($Start) {
  & (Join-Path $Path 'app\control-panel.ps1')
}
exit 0
