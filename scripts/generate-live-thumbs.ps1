# Saved as UTF-8 WITH BOM: Windows PowerShell 5.1 misreads UTF-8 without one (garbled labels)
Add-Type -AssemblyName System.Drawing

$outputDir = Join-Path $PSScriptRoot "..\placeholders"

$items = @(
    @{
        Name = "live-thumb-cantonese.png"
        Width = 1280
        Height = 720
        Bg = "#0F2A33"
        Kicker = "● YOUTUBE · LIVE NOW"
        Title = "粵語頻道"
        KickerColor = "#D92D27"
    },
    @{
        Name = "live-thumb-english.png"
        Width = 1280
        Height = 720
        Bg = "#0F2A33"
        Kicker = "YOUTUBE · SCHEDULED"
        Title = "English channel"
        KickerColor = "#888888"
    },
    @{
        Name = "live-thumb-mandarin.png"
        Width = 1280
        Height = 720
        Bg = "#0F2A33"
        Kicker = "YOUTUBE · SCHEDULED"
        Title = "國語頻道"
        KickerColor = "#888888"
    }
)

foreach ($item in $items) {
    [int]$w = $item.Width
    [int]$h = $item.Height
    $bmp = New-Object System.Drawing.Bitmap($w, $h)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
    $g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::ClearTypeGridFit

    $bgColor = [System.Drawing.ColorTranslator]::FromHtml($item.Bg)
    $brush = New-Object System.Drawing.SolidBrush($bgColor)
    $g.FillRectangle($brush, 0, 0, $w, $h)

    # Subtle grid texture
    $dotPen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(25, 255, 255, 255), 1)
    for ($x = 0; $x -lt $w; $x += 24) {
        for ($y = 0; $y -lt $h; $y += 24) {
            $g.DrawRectangle($dotPen, $x, $y, 1, 1)
        }
    }

    # Border
    $borderPen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(60, 255, 255, 255), 2)
    $g.DrawRectangle($borderPen, 1, 1, ($w - 2), ($h - 2))

    # Font
    $fontKicker = New-Object System.Drawing.Font("Consolas", 18, [System.Drawing.FontStyle]::Bold)
    $fontTitle  = New-Object System.Drawing.Font("Microsoft JhengHei", 32, [System.Drawing.FontStyle]::Bold)

    $kickerColor = [System.Drawing.ColorTranslator]::FromHtml($item.KickerColor)
    $kickerBrush = New-Object System.Drawing.SolidBrush($kickerColor)
    $titleBrush  = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(255, 255, 255, 255))

    $sf = New-Object System.Drawing.StringFormat
    $sf.Alignment = [System.Drawing.StringAlignment]::Center
    $sf.LineAlignment = [System.Drawing.StringAlignment]::Center

    $rectKicker = [System.Drawing.RectangleF]::new(20, [float]($h / 2 - 50), [float]($w - 40), 40)
    $rectTitle  = [System.Drawing.RectangleF]::new(20, [float]($h / 2 + 5), [float]($w - 40), 60)

    $g.DrawString($item.Kicker, $fontKicker, $kickerBrush, $rectKicker, $sf)
    $g.DrawString($item.Title, $fontTitle, $titleBrush, $rectTitle, $sf)

    $savePath = Join-Path $outputDir $item.Name
    $bmp.Save($savePath, [System.Drawing.Imaging.ImageFormat]::Png)

    $g.Dispose()
    $bmp.Dispose()
    Write-Host "Re-generated placeholder: $($item.Name)"
}
