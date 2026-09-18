<#
.SYNOPSIS
  Throws away local changes and goes back to the published website. Windows.

.DESCRIPTION
  The way back when an edit goes wrong. Without this an editor who breaks
  something has no move except phoning for help, which is what makes people
  afraid to touch the site in the first place.

  Two steps, deliberately scoped differently:

    git restore ... -- .            reverts every TRACKED file, so a
                                    mangled component or content file goes
                                    back to how it was published. This can
                                    never delete an untracked file.

    git clean -fd src/content       removes NEW files - a half-made
                 public/images      announcement, an uploaded photo.

  The clean is restricted to the two content folders on purpose. A
  repo-wide `git clean` would also delete untracked files that have nothing
  to do with editing, including the control panel itself on a machine where
  it hasn't been committed yet. Scoping it means this button cannot eat the
  app that offers it.

  `git clean` without -x does not touch ignored files, so .env and
  node_modules are safe either way.
#>
[CmdletBinding()]
param([switch]$Force)

$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\lib\common.ps1"

$root = Get-RepoRoot
Set-Location $root

Write-Log 'Undo requested.'

if (-not (Get-GitExe)) {
  Show-Problem 'Git is not installed on this computer, so there is nothing to undo with. Tell the website administrator.'
  exit 1
}

$changes = Get-ChangeSummary
if ($changes.Count -eq 0) {
  Show-Info "You have no unpublished changes, so there is nothing to undo.`r`n`r`nThe website on your computer already matches the published one."
  exit 0
}

# New files outside the two content folders are deliberately NOT deleted
# (see the note at the top). Say so rather than leaving them behind
# silently - an AI helper that created a new component is exactly the case
# where an editor would otherwise think Undo had put everything back.
$strays = @()
$untracked = Invoke-Git -Arguments @('ls-files', '--others', '--exclude-standard')
if ($untracked.Ok -and $untracked.Output) {
  foreach ($line in ($untracked.Output -split "`r?`n")) {
    $f = $line.Trim().Trim('"')
    if ([string]::IsNullOrWhiteSpace($f)) { continue }
    if ($f -notmatch '^(src/content/|public/images/)') { $strays += $f }
  }
}

if (-not $Force) {
  $extra = ''
  if ($strays.Count -gt 0) {
    $shown = ($strays | Select-Object -First 4) -join "`r`n    "
    $more = ''
    if ($strays.Count -gt 4) { $more = "`r`n    ...and $($strays.Count - 4) more" }
    $extra = "`r`nThese new files are NOT part of the words and pictures, so they will be left exactly as they are:`r`n`r`n    " +
             $shown + $more + "`r`n`r`nIf you did not expect those, mention them to the website administrator.`r`n"
  }

  # Default button is No. Enter must not destroy someone's afternoon.
  $confirmed = Confirm-Action -Title 'Undo your changes?' -DefaultYes $false -Message (
    "This will throw away your changes to:`r`n`r`n" +
    "    $($changes.Summary)`r`n`r`n" +
    "It cannot be undone.`r`n`r`n" +
    "Anything you have already published stays safe and is not affected.`r`n" +
    $extra + "`r`n" +
    'Throw these changes away?')

  if (-not $confirmed) {
    Write-Log 'Undo cancelled.'
    exit 0
  }
}

Write-Log "Undoing: $($changes.Summary)"

$restore = Invoke-Git -Arguments @('restore', '--source=HEAD', '--staged', '--worktree', '--', '.')
if (-not $restore.Ok) {
  Show-Problem ("The changes could not be undone.`r`n`r`n" +
                'Open "Show Details" and click "Copy Log".')
  exit 1
}

# Only ever the content folders - see the note at the top of this file.
foreach ($area in @('src/content', 'public/images')) {
  if (Test-Path (Join-Path $root ($area -replace '/', '\'))) {
    [void](Invoke-Git -Arguments @('clean', '-fd', '--', $area))
  }
}

Write-Log 'Undo complete.'
Show-Info ("Done. The website on your computer is back to the published version.`r`n`r`n" +
           'If it is running, the page will refresh by itself in a moment.')
exit 0
