<#
.SYNOPSIS
  Sends your changes to GitHub, which publishes the live site. Windows.

.DESCRIPTION
  Collects whatever landed on GitHub while you were working, packages up
  your edits with a description, and pushes. A push to main triggers the
  deploy workflow, so the live site follows two to four minutes later.

  The pull is --rebase --autostash rather than a plain pull: the nightly
  sermon import commits to main on its own, so a helper who worked for an
  hour will routinely be behind, and an unfinished edit shouldn't block
  catching up.

  Everything an editor sees here is a dialog. Before anything is sent they
  get a plain-language summary of what is about to go live - "2
  announcements, 1 photo" - because "M src/content/announcements/x.md" is
  not something anyone can review.

.PARAMETER Message
  A plain description of what you changed. Optional: when it is missing the
  script asks in a dialog. It is NOT a mandatory parameter, because
  PowerShell answers a missing mandatory parameter with its own bare
  "cmdlet publish.ps1 at command pipeline position 1" prompt, which is
  exactly the sort of thing this whole app exists to avoid.

.EXAMPLE
  powershell -ExecutionPolicy Bypass -File .\scripts\publish.ps1
#>
[CmdletBinding()]
param(
  [Parameter(Position = 0)]
  [string]$Message,
  [switch]$NoBrowser
)

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

$root = Get-RepoRoot
Set-Location $root

Write-Log 'Publish requested.'

if (-not (Get-GitExe)) {
  Show-Problem ("Git is not installed on this computer, so nothing can be published.`r`n`r`n" +
                'Tell the website administrator - this needs setting up once.')
  exit 1
}

# --- 1. Is there anything to send? --------------------------------------

$changes = Get-ChangeSummary
if ($changes.Count -eq 0) {
  Show-Info "Nothing has changed since your last publish, so there is nothing to send."
  exit 0
}

# --- 2. Refuse to publish a password file -------------------------------
# .env is protected by one line in .gitignore, and `git add -A` below would
# happily stage it if that line ever went missing. The cost of being wrong
# is a leaked Tina token in a public repository, so this check does not
# rely on .gitignore being intact.

$secrets = @(Test-SecretInChanges -Paths $changes.Paths)
if ($secrets.Count -gt 0) {
  Write-Log "Refusing to publish; secret-like files present: $($secrets -join ', ')" 'error'
  Show-Problem ("This would publish a password file, so nothing has been sent.`r`n`r`n" +
                "The file is: $($secrets -join ', ')`r`n`r`n" +
                'Tell the website administrator. Do not try again until they say so.')
  exit 1
}

if (-not (Test-EnvIgnored)) {
  Write-Log '.env is no longer covered by .gitignore.' 'error'
  Show-Problem ("Something is wrong with the website's safety settings, so nothing has been sent.`r`n`r`n" +
                'Tell the website administrator and mention the ".env" file.')
  exit 1
}

# --- 3. Who is publishing? ----------------------------------------------

if (-not (Confirm-GitIdentity)) {
  Show-Info 'Nothing was published.'
  exit 0
}

# --- 4. Confirm, in words --------------------------------------------------

$confirmed = Confirm-Action -Title 'Publish these changes?' -DefaultYes $true -Message (
  "You are about to publish:`r`n`r`n" +
  "    $($changes.Summary)`r`n`r`n" +
  "This puts them on the real website, where everyone can see them. " +
  "It takes about 2 to 4 minutes.`r`n`r`n" +
  'Publish now?')

if (-not $confirmed) {
  Write-Log 'Publish cancelled at the confirmation step.'
  exit 0
}

# --- 5. Description -----------------------------------------------------

if ([string]::IsNullOrWhiteSpace($Message)) {
  $Message = Show-InputDialog -Title 'What did you change?' -Prompt (
    "In a few words, what did you change? This is saved in the website's history so " +
    "anyone can see what happened and when.`r`n`r`n" +
    'For example: Added the Christmas Eve service announcement')
}
if ([string]::IsNullOrWhiteSpace($Message)) {
  Write-Log 'Publish cancelled at the description step.'
  exit 0
}

# --- 6. Catch up, then send ---------------------------------------------

Write-Log 'Pulling latest before publishing.'
$pull = Invoke-Git -Arguments @('pull', '--rebase', '--autostash')
if (-not $pull.Ok) {
  Show-Problem ("Your changes and someone else's have collided, and sorting that out needs a person.`r`n`r`n" +
                "Nothing has been published, and your work is safe.`r`n`r`n" +
                'Send the website administrator this message and stop here.')
  exit 1
}

Write-Log 'Staging and committing.'
$add = Invoke-Git -Arguments @('add', '-A')
if (-not $add.Ok) {
  Show-Problem ("The changes could not be packaged up, so nothing was sent.`r`n`r`n" +
                'Open "Show Details" and click "Copy Log".')
  exit 1
}

$commit = Invoke-Git -Arguments @('commit', '-m', $Message)
if (-not $commit.Ok) {
  Show-Problem ("The changes could not be saved, so nothing was sent.`r`n`r`n" +
                'Open "Show Details" and click "Copy Log".')
  exit 1
}

# The first push on a machine opens Git Credential Manager's browser
# sign-in. That is expected and is the only time an editor needs their
# GitHub account.
Write-Log 'Pushing.'
$push = Invoke-Git -Arguments @('push')
if (-not $push.Ok) {
  if ($push.Error -match 'Authentication|denied|403|could not read Username') {
    Show-Problem ("GitHub did not accept your sign-in, so nothing was published.`r`n`r`n" +
                  "Your work is saved and safe - you can try again.`r`n`r`n" +
                  'If it keeps happening, ask the website administrator to check that your GitHub account has been given access.')
  } else {
    Show-Problem ("The changes could not be sent, so the website has not been updated.`r`n`r`n" +
                  "Your work is saved and safe.`r`n`r`n" +
                  'Try again in a moment. If it happens twice, click "Copy Log" and send that to the website administrator.')
  }
  exit 1
}

Write-Log 'Published.'
Show-Info ("Sent.`r`n`r`n" +
           "The website updates itself in about 2 to 4 minutes.`r`n`r`n" +
           'A page will open showing the progress. A green tick means it is live.')

if (-not $NoBrowser) { Open-Url $script:ActionsUrl }
exit 0
