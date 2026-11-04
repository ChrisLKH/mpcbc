<#
  Detecting and installing what the website needs: Git and Node.js.

  This lives in its own file because two different callers need it at two
  different moments:

    * Install\install.ps1 - on a blank computer, BEFORE the repository
      exists (it is copied next to that script in the setup ZIP by
      app\make-setup-zip.ps1).
    * app\control-panel.ps1 - afterwards, so the app can repair its own
      prerequisites instead of sending someone back to a file they threw
      away weeks ago.

  Dot-source it; do not run it:
      . "$PSScriptRoot\lib\prereqs.ps1"

  Requires Update-PathFromRegistry, which common.ps1 provides and
  bootstrap\install.ps1 defines for itself.
#>

$script:MinNodeMajor = 20
if (-not (Get-Variable -Name PortableToolsDir -Scope Script -ErrorAction SilentlyContinue)) {
  $script:PortableToolsDir = 'C:\mpcbc-tools'
}

function Get-NodeMajor {
  <# Major version of whatever node we can reach, or 0 if none. #>
  param([string]$NodeExe)
  if (-not $NodeExe) { return 0 }
  try {
    $raw = (& $NodeExe --version) -replace '^v', ''
    return [int](($raw -split '\.')[0])
  } catch {
    return 0
  }
}

function Install-WithWinget {
  param([string]$PackageId, [string]$Label)
  if (-not (Get-Command winget -ErrorAction SilentlyContinue)) { return $false }
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
    Newest release asset matching a pattern. PortableGit's filename carries
    a version that changes every few weeks, so a hardcoded URL would rot.
  #>
  param([string]$RepoPath, [string]$Pattern)
  $release = Invoke-RestMethod -Uri "https://api.github.com/repos/$RepoPath/releases/latest" `
    -Headers @{ 'User-Agent' = 'mpcbc-setup' }
  return ($release.assets | Where-Object { $_.name -match $Pattern } | Select-Object -First 1)
}

function Install-PortableGit {
  <#
    PortableGit is a self-extracting archive, not an installer: no
    administrator rights needed. Unlike MinGit it still ships Git
    Credential Manager, which is what makes the browser sign-in work the
    first time an editor publishes.
  #>
  try {
    $asset = Get-GitHubAsset -RepoPath 'git-for-windows/git' -Pattern 'PortableGit-.*-64-bit\.7z\.exe$'
    if (-not $asset) { return $false }

    $target = Join-Path $script:PortableToolsDir 'git'
    $tmp = Join-Path $env:TEMP $asset.name
    Invoke-WebRequest -Uri $asset.browser_download_url -OutFile $tmp -UseBasicParsing

    New-Item -ItemType Directory -Path $target -Force | Out-Null
    Start-Process -FilePath $tmp -ArgumentList @('-o', $target, '-y') -Wait | Out-Null
    Remove-Item $tmp -Force -ErrorAction SilentlyContinue

    Update-PathFromRegistry
    return (Test-Path (Join-Path $target 'cmd\git.exe'))
  } catch {
    return $false
  }
}

function Install-PortableNode {
  try {
    $index = Invoke-RestMethod -Uri 'https://nodejs.org/dist/index.json' -UseBasicParsing
    $lts = $index | Where-Object { $_.lts } | Select-Object -First 1
    if (-not $lts) { return $false }

    $name = "node-$($lts.version)-win-x64"
    $url  = "https://nodejs.org/dist/$($lts.version)/$name.zip"
    $tmp  = Join-Path $env:TEMP "$name.zip"
    Invoke-WebRequest -Uri $url -OutFile $tmp -UseBasicParsing

    $staging = Join-Path $env:TEMP 'mpcbc-node'
    if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
    Expand-Archive -Path $tmp -DestinationPath $staging -Force
    Remove-Item $tmp -Force -ErrorAction SilentlyContinue

    $target = Join-Path $script:PortableToolsDir 'node'
    if (Test-Path $target) { Remove-Item $target -Recurse -Force }
    New-Item -ItemType Directory -Path $script:PortableToolsDir -Force | Out-Null
    Move-Item -Path (Join-Path $staging $name) -Destination $target

    Update-PathFromRegistry
    return (Test-Path (Join-Path $target 'node.exe'))
  } catch {
    return $false
  }
}

function Install-Prerequisites {
  <#
    Make Git and Node present, by whatever route this computer allows.

    Order matters, and it is about administrator rights:
      1. winget - clean, but each install raises a UAC prompt.
      2. Portable copies into C:\mpcbc-tools - no installer, no UAC, no
         machine-wide change. This is what makes a locked-down church PC
         work rather than turning into a support call.
      3. The caller falls back to opening the download pages by hand.

    Reports progress through the supplied callback so the caller decides
    whether that goes to a console or a window.

    Returns an object saying what is present afterwards.
  #>
  param([scriptblock]$Progress = { param($m) })

  if (-not (Get-Command git -ErrorAction SilentlyContinue)) { Update-PathFromRegistry }

  $gitExe = Get-GitExe
  if (-not $gitExe) {
    & $Progress 'Installing Git...'
    if (-not (Install-WithWinget -PackageId 'Git.Git' -Label 'Git')) {
      & $Progress 'Installing Git without administrator rights (this takes a little longer)...'
      [void](Install-PortableGit)
    }
    Update-PathFromRegistry
    $gitExe = Get-GitExe
  }

  $nodeExe = Get-NodeExe
  if (-not $nodeExe -or (Get-NodeMajor $nodeExe) -lt $script:MinNodeMajor) {
    & $Progress 'Installing Node.js...'
    if (-not (Install-WithWinget -PackageId 'OpenJS.NodeJS.LTS' -Label 'Node.js')) {
      & $Progress 'Installing Node.js without administrator rights (this takes a little longer)...'
      [void](Install-PortableNode)
    }
    Update-PathFromRegistry
    $nodeExe = Get-NodeExe
  }

  return [pscustomobject]@{
    GitExe    = $gitExe
    NodeExe   = $nodeExe
    GitOk     = [bool]$gitExe
    NodeOk    = ($nodeExe -and (Get-NodeMajor $nodeExe) -ge $script:MinNodeMajor)
  }
}
