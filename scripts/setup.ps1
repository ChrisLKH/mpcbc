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
# PATH may still be stale from an install that happened moments ago, so
# look properly before concluding something is missing.

Write-Step 'Checking Git and Node'

$git = Get-GitExe
if (-not $git) {
  Stop-Setup 'Git is not installed on this computer.' `
             'Run "Install MPCBC Website" again, or ask Chris to set this computer up.'
}

$node = Get-NodeExe
if (-not $node) {
  Stop-Setup 'Node.js is not installed on this computer.' `
             'Run "Install MPCBC Website" again, or ask Chris to set this computer up.'
}

$nodeRaw = (& $node --version) -replace '^v', ''
$nodeMajor = [int]($nodeRaw -split '\.')[0]
if ($nodeMajor -lt 20) {
  Stop-Setup "This computer has Node $nodeRaw, and the website needs 20 or newer." `
             'Ask Chris to update it.'
}
Write-Ok "node v$nodeRaw"

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
  Write-Step "Updating the code in $Path"

  $before = (Invoke-Git -Arguments @('rev-parse', 'HEAD') -WorkingDirectory $Path).Output

  $pull = Invoke-Git -Arguments @('pull', '--ff-only') -WorkingDirectory $Path
  if (-not $pull.Ok) {
    Stop-Setup 'The update could not be applied, usually because of unfinished edits on this computer.' `
               'Click "Publish My Changes" to send your work first, or "Undo My Changes" to throw it away. Then try again.'
  }

  $after = (Invoke-Git -Arguments @('rev-parse', 'HEAD') -WorkingDirectory $Path).Output

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

Write-Step 'Installing the site''s building blocks (1-3 minutes)'
$npmLog = Get-LogPath 'npm-install'
$npm = Start-Process -FilePath $env:ComSpec -ArgumentList @('/c', 'npm install --no-fund --no-audit') `
  -WorkingDirectory $Path -WindowStyle Hidden -Wait -PassThru `
  -RedirectStandardOutput $npmLog -RedirectStandardError (Get-LogPath 'npm-install-errors')

if ($npm.ExitCode -ne 0) {
  Stop-Setup 'The website''s building blocks could not be installed.' `
             'Open "Show Details" in the app and use "Copy for Chris", then send him the message.'
}
Write-Ok 'Building blocks are up to date.'

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
  & (Join-Path $Path 'scripts\control-panel.ps1')
}
exit 0
