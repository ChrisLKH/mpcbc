$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"
$port = 9222
$tempProfile = [System.IO.Path]::GetTempPath() + "chrome_cdp_" + [System.Guid]::NewGuid().ToString()

$args = @(
    "--headless=new",
    "--no-sandbox",
    "--disable-gpu",
    "--remote-debugging-port=$port",
    "--user-data-dir=$tempProfile",
    "--window-size=1440,900",
    "http://mpcbc.local/"
)

$proc = Start-Process -FilePath $chromePath -ArgumentList $args -PassThru
Start-Sleep -Seconds 3

try {
    $res = Invoke-RestMethod -Uri "http://127.0.0.1:$port/json"
    $wsUrl = $res[0].webSocketDebuggerUrl

    $ws = [System.Net.WebSockets.ClientWebSocket]::new()
    $cts = [System.Threading.CancellationTokenSource]::new(10000)
    $ws.ConnectAsync([uri]$wsUrl, $cts.Token).Wait()

    $expr = @"
(() => {
  return {
    times_card: window.getComputedStyle(document.querySelector('.elementor-element-times_card')).flexDirection,
    find_place_grid: window.getComputedStyle(document.querySelector('.elementor-element-find_place_cards_grid')).flexDirection,
    card_english_width: window.getComputedStyle(document.querySelector('.elementor-element-card_english')).width,
    card_english_h3: window.getComputedStyle(document.querySelector('.elementor-element-card_english h3')).fontSize,
    kids_inner: window.getComputedStyle(document.querySelector('.elementor-element-kids_inner')).flexDirection,
    pastor_root: window.getComputedStyle(document.querySelector('.elementor-element-pastor_root')).flexDirection,
    events_messages_inner: window.getComputedStyle(document.querySelector('.elementor-element-events_messages_inner')).flexDirection,
    stay_loop_root: window.getComputedStyle(document.querySelector('.elementor-element-stay_loop_root')).flexDirection
  };
})()
"@

    $payload = @{
        id = 1
        method = "Runtime.evaluate"
        params = @{
            expression = $expr
            returnByValue = $true
        }
    } | ConvertTo-Json -Compress

    $bytes = [System.Text.Encoding]::UTF8.GetBytes($payload)
    $segment = [System.ArraySegment[byte]]::new($bytes)
    $ws.SendAsync($segment, [System.Net.WebSockets.WebSocketMessageType]::Text, $true, $cts.Token).Wait()

    $buffer = [byte[]]::new(8192)
    $recvSegment = [System.ArraySegment[byte]]::new($buffer)
    $result = $ws.ReceiveAsync($recvSegment, $cts.Token).Result
    $responseJson = [System.Text.Encoding]::UTF8.GetString($buffer, 0, $result.Count)

    Write-Host "RAW RESPONSE:"
    Write-Host $responseJson

    $ws.CloseAsync([System.Net.WebSockets.WebSocketCloseStatus]::NormalClosure, "Done", $cts.Token).Wait()
} finally {
    Stop-Process -Id $proc.Id -Force -ErrorAction SilentlyContinue
    Remove-Item -Path $tempProfile -Recurse -Force -ErrorAction SilentlyContinue
}
