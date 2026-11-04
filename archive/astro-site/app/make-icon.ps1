<#
.SYNOPSIS
  Builds app\mpcbc.ico from the church logo.

.DESCRIPTION
  The control panel's window icon and the desktop shortcut both use it.

  Run this whenever the logo changes:
      powershell -ExecutionPolicy Bypass -File .\app\make-icon.ps1

  Source preference, best first:
    public\images\logo-square.png   the square emblem - what an icon wants
    public\images\logo.png          the wide banner - a poor fallback

  If all four corners are the same flat colour, that colour is treated as
  background: it is made transparent and the image is cropped to what is
  left. Without this the logo's near-white backdrop ships as an opaque
  white tile on the desktop and in the taskbar, and the emblem sits small
  inside its own padding. Pass -KeepBackground to leave it alone.

  Artwork is scaled to fit and centred, never stretched: a squashed cross
  looks broken in a way people notice even at 16 pixels.

  Entry format matters more than it looks. Sizes up to 128 are written as
  classic BMP/DIB entries because System.Drawing - which is what draws the
  window's own icon - CANNOT decode PNG-compressed entries and throws
  "Requested range extends past the end of the array". Only the 256px
  entry is PNG, where the size saving is worth having and only Explorer
  reads it.
#>
[CmdletBinding()]
param(
  [string]$Source,
  [string]$OutputPath,
  [switch]$KeepBackground,
  [int]$Tolerance = 16
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing

$root = Split-Path -Parent $PSScriptRoot
if (-not $OutputPath) { $OutputPath = Join-Path $PSScriptRoot 'mpcbc.ico' }

if (-not $Source) {
  foreach ($candidate in @('public\images\logo-square.png', 'public\images\logo.png')) {
    $full = Join-Path $root $candidate
    if (Test-Path $full) { $Source = $full; break }
  }
}
if (-not $Source -or -not (Test-Path $Source)) {
  throw "No logo found. Put a square PNG at public\images\logo-square.png and run this again."
}

$sizes = @(16, 32, 48, 64, 128, 256)

function Remove-FlatBackground {
  <#
    Make a flat backdrop transparent and crop to what remains.

    Only acts when all four corners agree, which is the signal that the
    backdrop really is a backdrop rather than part of the artwork. Returns
    the original bitmap untouched otherwise, so a logo that genuinely has
    a coloured field is never gutted.
  #>
  param([System.Drawing.Bitmap]$Bitmap, [int]$Tolerance)

  $w = $Bitmap.Width; $h = $Bitmap.Height
  $corners = @(
    $Bitmap.GetPixel(0, 0), $Bitmap.GetPixel(($w - 1), 0),
    $Bitmap.GetPixel(0, ($h - 1)), $Bitmap.GetPixel(($w - 1), ($h - 1))
  )

  $first = $corners[0]
  if ($first.A -lt 250) { return $Bitmap }     # already transparent
  foreach ($c in $corners) {
    if ([Math]::Abs($c.R - $first.R) -gt $Tolerance -or
        [Math]::Abs($c.G - $first.G) -gt $Tolerance -or
        [Math]::Abs($c.B - $first.B) -gt $Tolerance) {
      Write-Host '  Corners disagree - leaving the background alone.'
      return $Bitmap
    }
  }

  Write-Host ("  Background #{0:X2}{1:X2}{2:X2} -> transparent" -f $first.R, $first.G, $first.B)

  $out = New-Object System.Drawing.Bitmap($w, $h, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
  $minX = $w; $minY = $h; $maxX = -1; $maxY = -1

  for ($y = 0; $y -lt $h; $y++) {
    for ($x = 0; $x -lt $w; $x++) {
      $p = $Bitmap.GetPixel($x, $y)
      $isBg = ([Math]::Abs($p.R - $first.R) -le $Tolerance -and
               [Math]::Abs($p.G - $first.G) -le $Tolerance -and
               [Math]::Abs($p.B - $first.B) -le $Tolerance)
      if ($isBg) {
        $out.SetPixel($x, $y, [System.Drawing.Color]::Transparent)
      } else {
        $out.SetPixel($x, $y, $p)
        if ($x -lt $minX) { $minX = $x }
        if ($y -lt $minY) { $minY = $y }
        if ($x -gt $maxX) { $maxX = $x }
        if ($y -gt $maxY) { $maxY = $y }
      }
    }
  }

  if ($maxX -lt 0) { return $out }             # nothing but background

  # A little breathing room, so the artwork is not flush to the edge.
  $pad = [int][Math]::Round([Math]::Max(($maxX - $minX), ($maxY - $minY)) * 0.03)
  $minX = [Math]::Max(0, ($minX - $pad)); $minY = [Math]::Max(0, ($minY - $pad))
  $maxX = [Math]::Min(($w - 1), ($maxX + $pad)); $maxY = [Math]::Min(($h - 1), ($maxY + $pad))

  $rect = New-Object System.Drawing.Rectangle($minX, $minY, ($maxX - $minX + 1), ($maxY - $minY + 1))
  Write-Host ("  Cropped to {0}x{1}" -f $rect.Width, $rect.Height)
  $cropped = $out.Clone($rect, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
  $out.Dispose()
  return $cropped
}

function New-CenterMark {
  <#
    The cross, book and figures on their own, without the ring of text.

    At 16 and 32 pixels the full lockup is mush: "MONTEREY PARK CHINESE
    BAPTIST CHURCH" curved around the edge, plus the Chinese line beneath,
    cannot survive at that size and the whole icon turns into a smudge.
    Using just the central mark for small sizes is ordinary practice, and
    the proportions below were chosen by rendering the alternatives - a
    wider crop drags in fragments of the arc text, which reads as damage.
  #>
  param([System.Drawing.Bitmap]$Source)

  $cw = [int]($Source.Width * 0.50)
  $ch = [int]($Source.Height * 0.62)
  $cx = [int]((($Source.Width - $cw) / 2))
  $cy = [int]((($Source.Height * 0.46) - ($ch / 2)))
  if ($cy -lt 0) { $cy = 0 }
  if (($cy + $ch) -gt $Source.Height) { $ch = $Source.Height - $cy }

  $rect = New-Object System.Drawing.Rectangle($cx, $cy, $cw, $ch)
  return $Source.Clone($rect, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
}

function New-SquareBitmap {
  <# Scale to fit and centre on a transparent square canvas. #>
  param([System.Drawing.Bitmap]$Source, [int]$Size)

  $bmp = New-Object System.Drawing.Bitmap($Size, $Size, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
  $g = [System.Drawing.Graphics]::FromImage($bmp)
  try {
    $g.Clear([System.Drawing.Color]::Transparent)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.PixelOffsetMode   = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.SmoothingMode     = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality

    $scale = [Math]::Min(($Size / $Source.Width), ($Size / $Source.Height))
    $w = [int][Math]::Round($Source.Width * $scale)
    $h = [int][Math]::Round($Source.Height * $scale)
    $x = [int][Math]::Round((($Size - $w) / 2))
    $y = [int][Math]::Round((($Size - $h) / 2))
    $g.DrawImage($Source, $x, $y, $w, $h)
  } finally {
    $g.Dispose()
  }
  return $bmp
}

function ConvertTo-IconDib {
  <#
    A 32bpp BMP/DIB icon image: BITMAPINFOHEADER with a doubled height,
    then bottom-up BGRA rows, then a 1bpp AND mask. The mask is all zeroes
    because transparency already lives in the alpha channel, but it must
    still be present and 4-byte aligned or the entry is rejected.
  #>
  param([System.Drawing.Bitmap]$Bitmap)

  $w = $Bitmap.Width; $h = $Bitmap.Height
  $xorSize  = $w * $h * 4
  $maskRow  = [int][Math]::Floor((($w + 31) / 32)) * 4
  $maskSize = $maskRow * $h

  $ms = New-Object System.IO.MemoryStream
  $bw = New-Object System.IO.BinaryWriter($ms)
  try {
    $bw.Write([UInt32]40)              # biSize
    $bw.Write([Int32]$w)               # biWidth
    $bw.Write([Int32]($h * 2))         # biHeight: XOR + AND stacked
    $bw.Write([UInt16]1)               # biPlanes
    $bw.Write([UInt16]32)              # biBitCount
    $bw.Write([UInt32]0)               # biCompression = BI_RGB
    $bw.Write([UInt32]($xorSize + $maskSize))
    $bw.Write([Int32]0); $bw.Write([Int32]0)
    $bw.Write([UInt32]0); $bw.Write([UInt32]0)

    for ($y = $h - 1; $y -ge 0; $y--) {
      for ($x = 0; $x -lt $w; $x++) {
        $p = $Bitmap.GetPixel($x, $y)
        $bw.Write([Byte]$p.B); $bw.Write([Byte]$p.G); $bw.Write([Byte]$p.R); $bw.Write([Byte]$p.A)
      }
    }
    $bw.Write((New-Object Byte[] $maskSize))

    $bw.Flush()
    # Comma-wrapped, and it matters: a bare `return $bytes` is enumerated
    # into the pipeline one byte at a time, so the caller gets an Object[]
    # of boxed bytes instead of a Byte[]. BinaryWriter then picks a
    # different overload and the icon comes out truncated and unreadable.
    return ,$ms.ToArray()
  } finally {
    $bw.Dispose(); $ms.Dispose()
  }
}

# --- Load and prepare ----------------------------------------------------

$src = New-Object System.Drawing.Bitmap($Source)
$origW = $src.Width; $origH = $src.Height
Write-Host ("Source: {0}  ({1}x{2})" -f (Split-Path -Leaf $Source), $origW, $origH)

if (-not $KeepBackground) {
  $trimmed = Remove-FlatBackground -Bitmap $src -Tolerance $Tolerance
  if (-not [object]::ReferenceEquals($trimmed, $src)) {
    $src.Dispose()
    $src = $trimmed
  }
}

# Judge squareness on what was supplied, not on the crop: trimming the
# padding off a square emblem legitimately leaves a wider shape, and
# warning about that would be noise.
$origRatio = [Math]::Max($origW, $origH) / [Math]::Min($origW, $origH)
if ($origRatio -gt 1.2) {
  Write-Warning ("The source image is not square, so the icon will have transparent space " +
                 "around it. A square emblem at public\images\logo-square.png would look better.")
}

# --- Render every size ---------------------------------------------------

# Sizes at or below this use the central mark rather than the full lockup.
$markThreshold = 48
$mark = New-CenterMark -Source $src
Write-Host ("  Small sizes (<= {0}px) use the central mark: {1}x{2}" -f $markThreshold, $mark.Width, $mark.Height)

$entries = @()
try {
  foreach ($size in $sizes) {
    $artwork = $src
    if ($size -le $markThreshold) { $artwork = $mark }
    $bmp = New-SquareBitmap -Source $artwork -Size $size
    if ($size -ge 256) {
      $ms = New-Object System.IO.MemoryStream
      $bmp.Save($ms, [System.Drawing.Imaging.ImageFormat]::Png)
      $entries += ,@($size, $ms.ToArray(), 'png')
      $ms.Dispose()
    } else {
      $entries += ,@($size, (ConvertTo-IconDib -Bitmap $bmp), 'dib')
    }
    $bmp.Dispose()
  }
} finally {
  $mark.Dispose()
  $src.Dispose()
}

# --- Assemble the ICO ----------------------------------------------------

$stream = New-Object System.IO.MemoryStream
$writer = New-Object System.IO.BinaryWriter($stream)
try {
  $writer.Write([UInt16]0)               # reserved
  $writer.Write([UInt16]1)               # type: 1 = icon
  $writer.Write([UInt16]$entries.Count)

  $offset = 6 + (16 * $entries.Count)
  foreach ($entry in $entries) {
    $size = $entry[0]
    $data = $entry[1]
    # 256 is stored as 0 - the field is a single byte.
    $dim = 0
    if ($size -lt 256) { $dim = $size }
    $writer.Write([Byte]$dim)
    $writer.Write([Byte]$dim)
    $writer.Write([Byte]0)               # palette colours
    $writer.Write([Byte]0)               # reserved
    $writer.Write([UInt16]1)             # colour planes
    $writer.Write([UInt16]32)            # bits per pixel
    $writer.Write([UInt32]$data.Length)
    $writer.Write([UInt32]$offset)
    $offset += $data.Length
  }

  foreach ($entry in $entries) { $writer.Write($entry[1]) }

  $writer.Flush()
  [System.IO.File]::WriteAllBytes($OutputPath, $stream.ToArray())
} finally {
  $writer.Dispose()
  $stream.Dispose()
}

$kb = [math]::Round((Get-Item $OutputPath).Length / 1KB, 1)
$desc = ($entries | ForEach-Object { "$($_[0])($($_[2]))" }) -join ', '
Write-Host ("Wrote {0} ({1} KB): {2}" -f $OutputPath, $kb, $desc) -ForegroundColor Green
