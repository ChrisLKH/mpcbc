Add-Type -AssemblyName System.Drawing

$outputDir = "C:\Projects\mpcbc\wordpress-design-b\placeholders"
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
}

$images = @(
    @{ Name = "pastor-portrait.png"; Width = 500; Height = 500; Bg = "#F8F3F4"; Fg = "#6B5560"; Text = "pastor portrait`nsquare crop" },
    @{ Name = "pastor-cantonese.png"; Width = 480; Height = 600; Bg = "#221419"; Fg = "#FFFFFF"; Text = "Photo of the Cantonese`ncongregation pastor" },
    @{ Name = "pastor-english.png"; Width = 480; Height = 600; Bg = "#221419"; Fg = "#FFFFFF"; Text = "Photo of the English`ncongregation pastor" },
    @{ Name = "pastor-mandarin.png"; Width = 480; Height = 600; Bg = "#221419"; Fg = "#FFFFFF"; Text = "Photo of the Mandarin`ncongregation pastor" },
    @{ Name = "thumb-cantonese.png"; Width = 640; Height = 360; Bg = "#1B1014"; Fg = "#FFFFFF"; Text = "YOUTUBE · SCHEDULED`n`n粵語頻道" },
    @{ Name = "thumb-english.png"; Width = 640; Height = 360; Bg = "#1B1014"; Fg = "#FFFFFF"; Text = "● YOUTUBE · LIVE NOW`n`nEnglish channel" },
    @{ Name = "thumb-mandarin.png"; Width = 640; Height = 360; Bg = "#1B1014"; Fg = "#FFFFFF"; Text = "YOUTUBE · LAST RECORDING`n`n國語頻道" },
    @{ Name = "kids-room.png"; Width = 640; Height = 360; Bg = "#FFFFFF"; Fg = "#6B5560"; Text = "Sunday school room, wide shot`nchildren and teacher" },
    @{ Name = "kids-craft.png"; Width = 400; Height = 400; Bg = "#FFFFFF"; Fg = "#6B5560"; Text = "craft table" },
    @{ Name = "kids-nursery.png"; Width = 400; Height = 400; Bg = "#FFFFFF"; Fg = "#6B5560"; Text = "nursery corner" }
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

    # Border
    $borderPen = New-Object System.Drawing.Pen([System.Drawing.ColorTranslator]::FromHtml("#E3D3D8"), 2)
    $g.DrawRectangle($borderPen, 1, 1, ($w - 2), ($h - 2))

    # Text formatting
    $fontSize = [Math]::Max(12, [Math]::Min(18, [int]($w / 28)))
    $font = New-Object System.Drawing.Font("Consolas", [float]$fontSize, [System.Drawing.FontStyle]::Regular)
    $fgColor = [System.Drawing.ColorTranslator]::FromHtml($item.Fg)
    $textBrush = New-Object System.Drawing.SolidBrush($fgColor)

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
