import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { spawn, execSync } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outDir = path.resolve('C:\\Projects\\mpcbc\\screenshots\\after-visual-pass');
if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

const pages = [
  { name: 'home-en', url: 'http://mpcbc.local/' },
  { name: 'home-zh-hant', url: 'http://mpcbc.local/?lang=zh-hant' },
  { name: 'home-zh-hans', url: 'http://mpcbc.local/?lang=zh-hans' },
];

const viewports = [
  { name: '1440', width: 1440, height: 900, mobile: false },
  { name: '768', width: 768, height: 1024, mobile: false },
  { name: '390', width: 390, height: 844, mobile: true }
];

function sendCmd(ws, method, params = {}) {
  return new Promise((resolve, reject) => {
    const id = Math.floor(Math.random() * 1000000);
    const handler = (evt) => {
      const data = JSON.parse(evt.data);
      if (data.id === id) {
        ws.removeEventListener('message', handler);
        if (data.error) reject(data.error);
        else resolve(data.result);
      }
    };
    ws.addEventListener('message', handler);
    ws.send(JSON.stringify({ id, method, params }));
  });
}

async function capture() {
  const tmpProfile = fs.mkdtempSync(path.join(os.tmpdir(), 'chrome-after-shot-'));
  const port = 9226;
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--hide-scrollbars',
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${tmpProfile}`,
    '--window-size=1440,900',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2500));

  let ws;
  try {
    const res = await new Promise((resolve, reject) => {
      const req = http.get(`http://127.0.0.1:${port}/json`, (r) => {
        let d = '';
        r.on('data', c => d += c);
        r.on('end', () => resolve(JSON.parse(d)));
      });
      req.on('error', reject);
    });

    const pageTarget = res.find(t => t.type === 'page') || res[0];
    ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
    await new Promise(r => ws.onopen = r);

    await sendCmd(ws, 'Page.enable');
    await sendCmd(ws, 'Network.enable');
    await sendCmd(ws, 'Network.clearBrowserCache');
    await sendCmd(ws, 'Network.setCacheDisabled', { cacheDisabled: true });

    for (const page of pages) {
      console.log(`\nNavigating to ${page.name}: ${page.url}...`);
      await sendCmd(ws, 'Page.navigate', { url: page.url });
      await new Promise(r => setTimeout(r, 2000));

      for (const vp of viewports) {
        console.log(`Setting viewport ${vp.name} (${vp.width}x${vp.height})...`);
        await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
          width: vp.width,
          height: vp.height,
          deviceScaleFactor: 1,
          mobile: vp.mobile
        });

        await new Promise(r => setTimeout(r, 1000));

        // Smooth scroll to fetch lazy images
        await sendCmd(ws, 'Runtime.evaluate', {
          awaitPromise: true,
          expression: `(async () => {
            for (let y = 0; y < document.body.scrollHeight; y += 600) {
              window.scrollTo(0, y);
              await new Promise(r => setTimeout(r, 120));
            }
            window.scrollTo(0, 0);
            await new Promise(r => setTimeout(r, 400));
          })()`
        });

        // Check horizontal overflow
        const overflowCheck = await sendCmd(ws, 'Runtime.evaluate', {
          returnByValue: true,
          expression: `(() => {
            const docWidth = document.documentElement.clientWidth;
            const scrollWidth = document.documentElement.scrollWidth;
            const overflowingElements = [];
            document.querySelectorAll('*').forEach(el => {
              const rect = el.getBoundingClientRect();
              if (rect.right > docWidth + 1) {
                overflowingElements.push({
                  tag: el.tagName,
                  class: el.className,
                  id: el.id,
                  right: rect.right,
                  docWidth
                });
              }
            });
            return {
              hasHorizontalScroll: scrollWidth > docWidth,
              scrollWidth,
              docWidth,
              overflowCount: overflowingElements.length,
              sample: overflowingElements.slice(0, 3)
            };
          })()`
        });

        console.log(`  [${page.name} @ ${vp.name}] Horizontal overflow: ${JSON.stringify(overflowCheck.result.value)}`);

        // Above-fold screenshot
        const foldRes = await sendCmd(ws, 'Page.captureScreenshot', {
          format: 'png',
          captureBeyondViewport: false
        });
        const foldPath = path.join(outDir, `${page.name}-${vp.name}.png`);
        fs.writeFileSync(foldPath, Buffer.from(foldRes.data, 'base64'));
        console.log(`  Saved fold screenshot: ${foldPath}`);

        // Fullpage screenshot
        const fullRes = await sendCmd(ws, 'Page.captureScreenshot', {
          format: 'png',
          captureBeyondViewport: true
        });
        const fullPath = path.join(outDir, `${page.name}-fullpage-${vp.name}.png`);
        fs.writeFileSync(fullPath, Buffer.from(fullRes.data, 'base64'));
        console.log(`  Saved fullpage screenshot: ${fullPath}`);
      }
    }

    console.log('\nAll after screenshots captured successfully!');
  } finally {
    if (ws) {
      try { ws.close(); } catch (_) {}
    }
    try {
      execSync(`taskkill /T /F /PID ${proc.pid}`);
    } catch (_) {}
    try {
      fs.rmSync(tmpProfile, { recursive: true, force: true });
    } catch (_) {}
  }
}

capture().catch(err => {
  console.error('Error during capture:', err);
  process.exit(1);
});
