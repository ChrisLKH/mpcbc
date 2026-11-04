$chrome = "C:\Program Files\Google\Chrome\Application\chrome.exe"
$outDir = Join-Path $PSScriptRoot "..\screenshots"
if (!(Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir -Force | Out-Null
}

$tempProfile = [System.IO.Path]::GetTempPath() + "chrome_screens_" + [System.Guid]::NewGuid().ToString()

$pages = @(
    @{ Name = "home"; Url = "http://mpcbc.local/" },
    @{ Name = "welcome"; Url = "http://mpcbc.local/welcome/" },
    @{ Name = "prayer"; Url = "http://mpcbc.local/prayer/" },
    @{ Name = "coming-soon"; Url = "http://mpcbc.local/coming-soon/" }
)

$viewports = @(
    @{ Name = "1440"; Width = 1440; Height = 900 },
    @{ Name = "768";  Width = 768;  Height = 1024 },
    @{ Name = "390";  Width = 390;  Height = 844 }
)

foreach ($page in $pages) {
    foreach ($vp in $viewports) {
        $outFile = Join-Path $outDir "$($page.Name)-$($vp.Name).png"
        Write-Host "Capturing $($page.Name) at $($vp.Name)px to $outFile..."
        
        $args = @(
            "--headless=new",
            "--no-sandbox",
            "--disable-gpu",
            "--hide-scrollbars",
            "--user-data-dir=$tempProfile",
            "--window-size=$($vp.Width),$($vp.Height)",
            "--screenshot=$outFile",
            $page.Url
        )
        
        $proc = Start-Process -FilePath $chrome -ArgumentList $args -PassThru -Wait
        if (Test-Path $outFile) {
            Write-Host "Success: $outFile ($((Get-Item $outFile).Length) bytes)"
        } else {
            Write-Warning "Failed: $outFile"
        }
    }
}

Remove-Item -Path $tempProfile -Recurse -Force -ErrorAction SilentlyContinue
Write-Host "Screenshots complete!"
