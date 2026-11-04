$sections = @(
    'home-hero.json',
    'home-times-card.json',
    'home-find-your-place.json',
    'home-chinese-band.json',
    'home-kids.json',
    'home-pastor-prayer.json',
    'home-events-messages.json',
    'home-stay-in-loop.json'
)
$allContent = @()
$elemDir = Join-Path $PSScriptRoot "..\elementor"

foreach ($file in $sections) {
    $filePath = Join-Path $elemDir $file
    $json = Get-Content $filePath -Raw | ConvertFrom-Json
    $allContent += $json.content
}

$fullObj = [ordered]@{
    version = "0.4"
    title   = "MPCBC - Home"
    type    = "page"
    content = $allContent
}

$target = Join-Path $elemDir "home-full.json"
$fullObj | ConvertTo-Json -Depth 30 | Set-Content $target -Encoding utf8
Write-Host "Successfully merged all 8 sections into $target"
