<#
.SYNOPSIS
  Builds MPCBC-Website-Setup.zip - the one file a new editor is sent.

.DESCRIPTION
  Run this whenever the bootstrap or the prerequisite installer changes,
  then attach the result to a GitHub release (or send it directly).

  The ZIP exists because browsers block a bare .bat download as dangerous,
  so the very first step of onboarding would fail silently. A .zip is
  allowed through.

  It is assembled rather than kept checked in as a binary for two reasons:
  the contents must always match the scripts in this repository, and
  prereqs.ps1 lives in app\lib for the control panel's benefit while the
  installer needs it sitting next to install.ps1.

.EXAMPLE
  powershell -ExecutionPolicy Bypass -File .\scripts\make-setup-zip.ps1
#>
[CmdletBinding()]
param(
  [string]$OutputPath
)

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
if (-not $OutputPath) { $OutputPath = Join-Path $root 'MPCBC-Website-Setup.zip' }

# published name in the ZIP  ->  source file in the repo
$contents = [ordered]@{
  'Install MPCBC Website.bat' = 'Install\Install MPCBC Website.bat'
  'Read Me First.txt'         = 'Install\Read Me First.txt'
  'install.ps1'               = 'Install\install.ps1'
  'prereqs.ps1'               = 'app\lib\prereqs.ps1'
}

$staging = Join-Path ([System.IO.Path]::GetTempPath()) ('mpcbc-setup-' + [Guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $staging -Force | Out-Null

try {
  foreach ($name in $contents.Keys) {
    $source = Join-Path $root $contents[$name]
    if (-not (Test-Path $source)) {
      throw "Missing source file: $($contents[$name])"
    }
    Copy-Item $source (Join-Path $staging $name) -Force
    Write-Host ("  + {0}" -f $name)
  }

  if (Test-Path $OutputPath) { Remove-Item $OutputPath -Force }
  Compress-Archive -Path (Join-Path $staging '*') -DestinationPath $OutputPath -Force

  $size = [math]::Round((Get-Item $OutputPath).Length / 1KB, 1)
  Write-Host ""
  Write-Host "Built $OutputPath ($size KB)" -ForegroundColor Green
  Write-Host "Send this to a new editor, or attach it to a GitHub release."
} finally {
  Remove-Item $staging -Recurse -Force -ErrorAction SilentlyContinue
}
