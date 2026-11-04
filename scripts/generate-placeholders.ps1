# Saved as UTF-8 WITH BOM: Windows PowerShell 5.1 misreads UTF-8 without one (garbled labels)
Add-Type -AssemblyName System.Drawing

$outputDir = Join-Path $PSScriptRoot "..\placeholders"
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
}

$images = @(
    @{ Name = "hero-video-poster.png"; Width = 1440; Height = 860; Bg = "#1E3A44"; Text = "looping video · shot 1 of 4 · greeters at the front door, handshake" },
    @{ Name = "photo-english.png"; Width = 800; Height = 560; Bg = "#1E3A44"; Text = "photo · English service" },
    @{ Name = "photo-cantonese.png"; Width = 800; Height = 560; Bg = "#24434D"; Text = "photo · Cantonese service, tea after" },
    @{ Name = "photo-mandarin.png"; Width = 800; Height = 560; Bg = "#2C4A45"; Text = "photo · Mandarin service, small group" },
    @{ Name = "photo-kids-door.png"; Width = 800; Height = 560; Bg = "#24434D"; Text = "photo · kids at the check-in door (consent-cleared)" },
    @{ Name = "pastor-portrait.png"; Width = 600; Height = 600; Bg = "#24434D"; Text = "pastor portrait" },
    @{ Name = "photo-kids-craft.png"; Width = 380; Height = 460; Bg = "#1E3A44"; Text = "photo · Sunday school craft" },
    @{ Name = "photo-kids-vbs.png"; Width = 300; Height = 360; Bg = "#2C4A45"; Text = "photo · VBS game" },
    @{ Name = "photo-kids-checkin.png"; Width = 320; Height = 240; Bg = "#2C4A45"; Text = "photo · parent at check-in desk" },
    @{ Name = "photo-community-kids.png"; Width = 800; Height = 500; Bg = "#24434D"; Text = "photo · kids ministry" },
    @{ Name = "photo-community-youth.png"; Width = 800; Height = 500; Bg = "#1E3A44"; Text = "photo · youth and students" },
    @{ Name = "photo-community-fellowship.png"; Width = 800; Height = 500; Bg = "#2C4A45"; Text = "photo · fellowship group" },
    @{ Name = "photo-community-care.png"; Width = 800; Height = 500; Bg = "#24434D"; Text = "photo · care and prayer" },
    @{ Name = "photo-newsletter.png"; Width = 600; Height = 400; Bg = "#1E3A44"; Text = "photo · newsletter feature" },
    @{ Name = "photo-give.png"; Width = 600; Height = 400; Bg = "#24434D"; Text = "photo · giving" },
    @{ Name = "church-logo.png"; Width = 280; Height = 88; Bg = "#0A1F26"; Text = "[Church logo]" },
    @{ Name = "sermon-english.png"; Width = 320; Height = 180; Bg = "#0F2A33"; Text = "Play > English Sermon" },
    @{ Name = "sermon-cantonese.png"; Width = 320; Height = 180; Bg = "#0F2A33"; Text = "Play > Cantonese Sermon" },
    @{ Name = "sermon-mandarin.png"; Width = 320; Height = 180; Bg = "#0F2A33"; Text = "Play > Mandarin Sermon" }
)

foreach ($item in $images) {
    [int]$w = $item.Width
    [int]$h = $item.Height
    $bmp = New-Object System.Drawing.Bitmap($w, $h)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
    $g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit

    $bgColor = [System.Drawing.ColorTranslator]::FromHtml($item.Bg)
    $brush = New-Object System.Drawing.SolidBrush($bgColor)
    $g.FillRectangle($brush, 0, 0, $w, $h)

    # Draw subtle grid/dot texture
    $dotPen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(25, 255, 255, 255), 1)
    for ($x = 0; $x -lt $w; $x += 24) {
        for ($y = 0; $y -lt $h; $y += 24) {
            $g.DrawRectangle($dotPen, $x, $y, 1, 1)
        }
    }

    # Draw border
    $borderPen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(60, 255, 255, 255), 2)
    $g.DrawRectangle($borderPen, 1, 1, ($w - 2), ($h - 2))

    # Text formatting
    $fontSize = [Math]::Max(12, [Math]::Min(18, [int]($w / 30)))
    $font = New-Object System.Drawing.Font("Consolas", [float]$fontSize, [System.Drawing.FontStyle]::Regular)
    $textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(220, 255, 255, 255))
    
    $rect = [System.Drawing.RectangleF]::new(16.0, 16.0, [float]($w - 32), [float]($h - 32))
    $sf = New-Object System.Drawing.StringFormat
    $sf.Alignment = [System.Drawing.StringAlignment]::Center
    $sf.LineAlignment = [System.Drawing.StringAlignment]::Center

    $g.DrawString($item.Text, $font, $textBrush, $rect, $sf)

    $savePath = Join-Path $outputDir $item.Name
    $bmp.Save($savePath, [System.Drawing.Imaging.ImageFormat]::Png)

    $g.Dispose()
    $bmp.Dispose()
    Write-Host "Created placeholder: $($item.Name)"
}
