<#
.SYNOPSIS
  Opens the website folder in an editor or an AI helper. Windows.

.DESCRIPTION
  Called by the control panel's "Edit with a Helper" menu.

  A missing tool is never an error. It is a question: "Install it now?" -
  and if the answer is no, or there is no reliable installer, the tool's
  download page opens instead.

  Note the deliberate inconsistency about windows: the two dev servers run
  hidden, because a console window there is a trap nobody benefits from.
  The AI helpers are the opposite - they ARE a conversation in a terminal,
  so they get a real visible window on purpose.

.PARAMETER Tool
  vscode | claude | codex | antigravity
#>
[CmdletBinding()]
param(
  [Parameter(Mandatory = $true)]
  [ValidateSet('vscode', 'claude', 'codex', 'antigravity')]
  [string]$Tool
)

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

$root = Get-RepoRoot

# Command to look for, friendly name, how to install, and where to send
# someone when there is no dependable installer.
$catalog = @{
  vscode = @{
    Command     = 'code'
    Name        = 'VS Code'
    Installer   = 'winget'
    Package     = 'Microsoft.VisualStudioCode'
    DownloadUrl = 'https://code.visualstudio.com/download'
    Interactive = $false
  }
  claude = @{
    Command     = 'claude'
    Name        = 'Claude Code'
    Installer   = 'npm'
    Package     = '@anthropic-ai/claude-code'
    DownloadUrl = 'https://claude.com/product/claude-code'
    Interactive = $true
  }
  codex = @{
    Command     = 'codex'
    Name        = 'Codex'
    Installer   = 'npm'
    Package     = '@openai/codex'
    DownloadUrl = 'https://developers.openai.com/codex/cli/'
    Interactive = $true
  }
  antigravity = @{
    Command     = 'antigravity'
    Name        = 'Antigravity'
    # No dependable Windows package id, so this one only ever offers the
    # download page rather than guessing and failing.
    Installer   = 'none'
    Package     = ''
    DownloadUrl = 'https://antigravity.google/'
    Interactive = $false
  }
}

$spec = $catalog[$Tool]

function Test-ToolPresent {
  param([string]$Command)
  if (Get-Command $Command -ErrorAction SilentlyContinue) { return $true }
  Update-PathFromRegistry
  return [bool](Get-Command $Command -ErrorAction SilentlyContinue)
}

function Start-Tool {
  param($Spec)
  Write-Log "Opening $($Spec.Name)."
  if ($Spec.Interactive) {
    # A visible window, because this is a conversation.
    Start-Process -FilePath 'powershell.exe' -ArgumentList @(
      '-NoExit', '-NoProfile', '-Command',
      "Set-Location '$root'; Write-Host 'MPCBC website - $($Spec.Name). Type what you want changed, in plain English.' -ForegroundColor Magenta; $($Spec.Command)"
    ) | Out-Null
  } else {
    Start-Process -FilePath $Spec.Command -ArgumentList @('.') -WorkingDirectory $root
  }
}

# --- Already installed? --------------------------------------------------

if (Test-ToolPresent -Command $spec.Command) {
  Start-Tool -Spec $spec
  exit 0
}

# --- Offer to install ----------------------------------------------------

if ($spec.Installer -eq 'none') {
  if (Confirm-Action -Title $spec.Name -DefaultYes $true -Message (
      "$($spec.Name) is not installed on this computer.`r`n`r`n" +
      "Would you like to open its download page? You only have to do this once.")) {
    Open-Url $spec.DownloadUrl
  }
  exit 0
}

if (-not (Confirm-Action -Title $spec.Name -DefaultYes $true -Message (
    "$($spec.Name) is not installed on this computer.`r`n`r`n" +
    "Would you like to install it now? It takes a minute or two, and you only have to do it once."))) {
  exit 0
}

Write-Log "Installing $($spec.Name) via $($spec.Installer)."

$installLog = Get-LogPath "install-$Tool"
switch ($spec.Installer) {
  'winget' {
    if (-not (Get-Command winget -ErrorAction SilentlyContinue)) {
      Show-Info "This computer can't install it automatically. The download page will open instead."
      Open-Url $spec.DownloadUrl
      exit 0
    }
    $cmd = "winget install --id $($spec.Package) -e --accept-source-agreements --accept-package-agreements"
  }
  'npm' {
    if (-not (Get-NodeExe)) {
      Show-Problem 'Node.js is missing, so this cannot be installed. Tell Chris.'
      exit 1
    }
    $cmd = "npm install -g $($spec.Package)"
  }
}

$proc = Start-Process -FilePath $env:ComSpec -ArgumentList @('/c', $cmd) `
  -WorkingDirectory $root -WindowStyle Hidden -Wait -PassThru `
  -RedirectStandardOutput $installLog -RedirectStandardError (Get-LogPath "install-$Tool-errors")

# A fresh install is not on this window's PATH yet.
Update-PathFromRegistry

if ($proc.ExitCode -ne 0 -or -not (Test-ToolPresent -Command $spec.Command)) {
  Write-Log "$($spec.Name) install failed (exit $($proc.ExitCode))." 'warn'
  if (Confirm-Action -Title $spec.Name -DefaultYes $true -Message (
      "$($spec.Name) could not be installed automatically.`r`n`r`n" +
      'Would you like to open its download page and install it by hand?')) {
    Open-Url $spec.DownloadUrl
  }
  exit 1
}

Write-Log "$($spec.Name) installed."
Start-Tool -Spec $spec
exit 0
