<#
.SYNOPSIS
  First-run setup for the MPCBC website on a blank Windows computer.

.DESCRIPTION
  This is the one file an editor is sent. It runs before the repository
  exists, so unlike everything in scripts\, it is entirely self-contained -
  it cannot dot-source lib\common.ps1, because there is no lib yet.

  What it does:

    1. Refuses to run from inside a ZIP preview, which otherwise fails in
       a way nobody can diagnose.
    2. Installs Git and Node if they are missing - through winget when the
       computer allows it, and otherwise through portable copies that need
       no administrator rights at all.
    3. Downloads the website into C:\mpcbc.
    4. Hands over to scripts\setup.ps1, which owns everything from there.

  Progress is printed to the window because a silent three-minute install
  looks broken. Every decision and every failure is a dialog.
#>
[CmdletBinding()]
param(
  [string]$Repo = 'https://github.com/ChrisLKH/mpcbc.git',
  [string]$Path = 'C:\mpcbc'
)

$ErrorActionPreference = 'Stop'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

Add-Type -AssemblyName System.Windows.Forms -ErrorAction SilentlyContinue
try { [System.Windows.Forms.Application]::EnableVisualStyles() } catch { }

$PortableDir = 'C:\mpcbc-tools'

function Say     { param($m) Write-Host "`n==> $m" -ForegroundColor Magenta }
function Detail  { param($m) Write-Host "    $m" -ForegroundColor DarkGray }

function Show-Box {
  param($Message, $Title = 'MPCBC Website', $Icon = 'Information')
  [void][System.Windows.Forms.MessageBox]::Show($Message, $Title,
    [System.Windows.Forms.MessageBoxButtons]::OK,
    [System.Windows.Forms.MessageBoxIcon]::$Icon)
}

function Ask-YesNo {
  param($Message, $Title = 'MPCBC Website')
  return ([System.Windows.Forms.MessageBox]::Show($Message, $Title,
    [System.Windows.Forms.MessageBoxButtons]::YesNo,
    [System.Windows.Forms.MessageBoxIcon]::Question) -eq [System.Windows.Forms.DialogResult]::Yes)
}

function Stop-Install {
  param($Message)
  Show-Box $Message 'MPCBC Website - stopped' 'Warning'
  Write-Host "`nStopped: $Message" -ForegroundColor Red
  exit 1
}

function Update-PathFromRegistry {
  <#
    A program installed a moment ago is invisible to this window, because
    PATH was read when the window opened. Re-read the saved value and apply
    it HERE, to this process only.

    This is a read. Nothing is written back - never call
    [Environment]::SetEnvironmentVariable('Path', ..., 'Machine'/'User'),
    which flattens entries like %SystemRoot% and is how a PATH gets
    permanently broken.
  #>
  $parts = @()
  foreach ($key in @(
    'HKLM:\SYSTEM\CurrentControlSet\Control\Session Manager\Environment',
    'HKCU:\Environment'
  )) {
    try {
      $value = (Get-ItemProperty -Path $key -Name Path -ErrorAction Stop).Path
      if ($value) { $parts += $value }
    } catch { }
  }
  foreach ($sub in @('git\cmd', 'node')) {
    $dir = Join-Path $PortableDir $sub
    if (Test-Path $dir) { $parts += $dir }
  }
  if ($parts.Count -gt 0) { $env:Path = ($parts -join ';') }
}

function Test-Tool {
  param($Name)
  if (Get-Command $Name -ErrorAction SilentlyContinue) { return $true }
  Update-PathFromRegistry
  return [bool](Get-Command $Name -ErrorAction SilentlyContinue)
}

# --- 0. Running from inside the ZIP? ------------------------------------
# Windows happily runs a .bat straight out of Explorer's ZIP preview. It
# executes from a read-only temp folder and fails halfway through with
# something unreadable. Catch it before anything else happens.

$hereDir = $PSScriptRoot
if ($hereDir -like "$env:TEMP*" -or $hereDir -match '\\AppData\\Local\\Temp\\' -or $hereDir -match '\.zip\\') {
  Stop-Install ("This is still inside the ZIP file, so it cannot run properly." + [Environment]::NewLine + [Environment]::NewLine +
                "Please close this, then:" + [Environment]::NewLine +
                "   1. Right-click the ZIP file you downloaded" + [Environment]::NewLine +
                "   2. Choose 'Extract All...'" + [Environment]::NewLine +
                "   3. Open the folder that appears" + [Environment]::NewLine +
                "   4. Double-click 'Install MPCBC Website' in there")
}

Write-Host ''
Write-Host '  MPCBC Website - first-time setup' -ForegroundColor Cyan
Write-Host '  This takes about five minutes. You can leave it running.' -ForegroundColor DarkGray

# --- 1. Git and Node ----------------------------------------------------

function Install-WithWinget {
  param($PackageId, $Label)
  if (-not (Get-Command winget -ErrorAction SilentlyContinue)) { return $false }
  Detail "Installing $Label (a permission box may appear - please click Yes)"
  try {
    $proc = Start-Process -FilePath 'winget' -ArgumentList @(
      'install', '--id', $PackageId, '-e',
      '--accept-source-agreements', '--accept-package-agreements'
    ) -Wait -PassThru -WindowStyle Hidden
    return ($proc.ExitCode -eq 0)
  } catch {
    return $false
  }
}

function Get-GitHubAsset {
  <#
    Newest release asset matching a pattern. Used for PortableGit, whose
    filename carries a version that changes every few weeks - hardcoding a
    URL here would rot.
  #>
  param($RepoPath, $Pattern)
  $release = Invoke-RestMethod -Uri "https://api.github.com/repos/$RepoPath/releases/latest" `
    -Headers @{ 'User-Agent' = 'mpcbc-setup' }
  return ($release.assets | Where-Object { $_.name -match $Pattern } | Select-Object -First 1)
}

function Install-PortableGit {
  <#
    PortableGit is a self-extracting archive, not an installer: it needs no
    administrator rights, and unlike MinGit it still includes Git
    Credential Manager - which is what makes the browser sign-in work when
    the editor first publishes.
  #>
  Detail 'Setting up Git without needing administrator rights...'
  $asset = Get-GitHubAsset -RepoPath 'git-for-windows/git' -Pattern 'PortableGit-.*-64-bit\.7z\.exe$'
  if (-not $asset) { return $false }

  $target = Join-Path $PortableDir 'git'
  $tmp = Join-Path $env:TEMP $asset.name
  Detail "Downloading $($asset.name) ..."
  Invoke-WebRequest -Uri $asset.browser_download_url -OutFile $tmp -UseBasicParsing

  New-Item -ItemType Directory -Path $target -Force | Out-Null
  Detail 'Unpacking...'
  $proc = Start-Process -FilePath $tmp -ArgumentList @('-o', $target, '-y') -Wait -PassThru
  Remove-Item $tmp -Force -ErrorAction SilentlyContinue

  Update-PathFromRegistry
  return (Test-Path (Join-Path $target 'cmd\git.exe'))
}

function Install-PortableNode {
  Detail 'Setting up Node.js without needing administrator rights...'
  $index = Invoke-RestMethod -Uri 'https://nodejs.org/dist/index.json' -UseBasicParsing
  $lts = $index | Where-Object { $_.lts } | Select-Object -First 1
  if (-not $lts) { return $false }

  $name = "node-$($lts.version)-win-x64"
  $url  = "https://nodejs.org/dist/$($lts.version)/$name.zip"
  $tmp  = Join-Path $env:TEMP "$name.zip"

  Detail "Downloading Node.js $($lts.version) ..."
  Invoke-WebRequest -Uri $url -OutFile $tmp -UseBasicParsing

  Detail 'Unpacking...'
  $staging = Join-Path $env:TEMP 'mpcbc-node'
  if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
  Expand-Archive -Path $tmp -DestinationPath $staging -Force
  Remove-Item $tmp -Force -ErrorAction SilentlyContinue

  $target = Join-Path $PortableDir 'node'
  if (Test-Path $target) { Remove-Item $target -Recurse -Force }
  New-Item -ItemType Directory -Path $PortableDir -Force | Out-Null
  Move-Item -Path (Join-Path $staging $name) -Destination $target

  Update-PathFromRegistry
  return (Test-Path (Join-Path $target 'node.exe'))
}

Say 'Checking what this computer already has'

if (Test-Tool 'git')  { Detail 'Git is already installed.' }
if (Test-Tool 'node') { Detail 'Node.js is already installed.' }

if (-not (Test-Tool 'git')) {
  Say 'Installing Git'
  if (-not (Install-WithWinget -PackageId 'Git.Git' -Label 'Git')) { $null = Install-PortableGit }
  Update-PathFromRegistry
}

if (-not (Test-Tool 'node')) {
  Say 'Installing Node.js'
  if (-not (Install-WithWinget -PackageId 'OpenJS.NodeJS.LTS' -Label 'Node.js')) { $null = Install-PortableNode }
  Update-PathFromRegistry
}

# Last resort: send them to the download pages and wait, rather than
# leaving them with an error code and no next step.
if (-not (Test-Tool 'git') -or -not (Test-Tool 'node')) {
  $missing = @()
  if (-not (Test-Tool 'git'))  { $missing += 'Git' }
  if (-not (Test-Tool 'node')) { $missing += 'Node.js' }

  if (Ask-YesNo ("This computer still needs: $($missing -join ' and ').`r`n`r`n" +
                 "Shall I open the download page(s) so you can install by hand? " +
                 "Click Yes, install them, then come back to this window and press Enter.")) {
    if (-not (Test-Tool 'git'))  { Start-Process 'https://git-scm.com/downloads' }
    if (-not (Test-Tool 'node')) { Start-Process 'https://nodejs.org' }
    Write-Host ''
    Write-Host '    Press Enter here once the installers have finished...' -ForegroundColor Yellow
    [void][Console]::ReadLine()
    Update-PathFromRegistry
  }
}

if (-not (Test-Tool 'git') -or -not (Test-Tool 'node')) {
  Stop-Install ("This computer needs two programs installed before the website can run, " +
                "and they could not be installed automatically." + [Environment]::NewLine + [Environment]::NewLine +
                "Please send Chris a message saying: 'the setup needs Git and Node installed'.")
}

Detail 'Git and Node.js are ready.'

# --- 2. Where the site lives --------------------------------------------
# C:\mpcbc, not Documents: OneDrive redirects Documents on most machines,
# and syncing node_modules (about 40,000 files) causes install failures and
# a file watcher that fights the sync client.

Say 'Choosing a place for the website'
$chosen = $Path
try {
  $parent = Split-Path -Parent $chosen
  if ($parent -and -not (Test-Path $parent)) { New-Item -ItemType Directory -Path $parent -Force | Out-Null }
  if (-not (Test-Path $chosen)) { New-Item -ItemType Directory -Path $chosen -Force -ErrorAction Stop | Out-Null }
} catch {
  # Some managed machines refuse a new folder at the top of the C: drive.
  # Still on C:, still outside OneDrive - never fall back to Documents.
  $chosen = Join-Path $env:USERPROFILE 'mpcbc'
  New-Item -ItemType Directory -Path $chosen -Force | Out-Null
}
Detail "The website will live in $chosen"

# --- 3. Download it -----------------------------------------------------

if (Test-Path (Join-Path $chosen '.git')) {
  Detail 'It is already here - setup will just bring it up to date.'
} else {
  Say 'Downloading the website (about a minute)'
  # The repository is public, so this needs no sign-in. A GitHub account is
  # only needed later, the first time the editor publishes something.
  & git clone $Repo $chosen
  if ($LASTEXITCODE -ne 0) {
    Stop-Install ("The website could not be downloaded." + [Environment]::NewLine + [Environment]::NewLine +
                  "Check that this computer is connected to the internet and try again. " +
                  "If it still fails, send Chris a message.")
  }
}

# --- 4. Hand over -------------------------------------------------------

Say 'Setting everything up'
$setup = Join-Path $chosen 'scripts\setup.ps1'
if (-not (Test-Path $setup)) {
  Stop-Install ("The download is incomplete - part of the website is missing." + [Environment]::NewLine + [Environment]::NewLine +
                "Please tell Chris.")
}

# Run setup in its own process. Calling it with & would run it in THIS
# process, where its `exit` would terminate this script too - the check
# below would never execute and the final message would never appear.
$proc = Start-Process -FilePath 'powershell.exe' -ArgumentList @(
  '-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', $setup, '-Path', $chosen
) -Wait -PassThru -NoNewWindow
if ($proc.ExitCode -ne 0) { exit $proc.ExitCode }

Show-Box ("All set." + [Environment]::NewLine + [Environment]::NewLine +
          "There is now an icon on your desktop called 'MPCBC Website'." + [Environment]::NewLine +
          "That is the only thing you need from now on - double-click it whenever you want to work on the site." + [Environment]::NewLine + [Environment]::NewLine +
          "It is opening now.")

Start-Process -FilePath (Join-Path $chosen 'MPCBC Website.bat') -WorkingDirectory $chosen
exit 0
