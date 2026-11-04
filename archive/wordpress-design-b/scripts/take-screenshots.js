import http from 'node:http';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { spawn } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outDir = path.resolve('C:/Projects/mpcbc/wordpress-design-b/screenshots');
if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

const pages = [
  { name: 'home', url: 'http://mpcbc-2.local/' },
  { name: 'coming-soon', url: 'http://mpcbc-2.local/coming-soon/' }
];

const viewports = [
  { name: '1920', width: 1920, height: 1080, mobile: false },
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
  const port = 9349;
  const tempProfile = path.join(os.tmpdir(), 'chrome-ss-' + Date.now());
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--hide-scrollbars',
    `--user-data-dir=${tempProfile}`,
    `--remote-debugging-port=${port}`,
    '--window-size=1440,900',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2500));

  const res = await new Promise((resolve, reject) => {
    http.get(`http://127.0.0.1:${port}/json`, (r) => {
      let d = '';
      r.on('data', c => d += c);
      r.on('end', () => resolve(JSON.parse(d)));
    }).on('error', reject);
  });

  const pageTarget = res.find(t => t.type === 'page') || res[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Network.enable');
  await sendCmd(ws, 'Network.clearBrowserCache');
  await sendCmd(ws, 'Network.setCacheDisabled', { cacheDisabled: true });

  for (const page of pages) {
    console.log(`Navigating to ${page.name}: ${page.url}...`);
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

      // Viewport above-fold screenshot
      const foldRes = await sendCmd(ws, 'Page.captureScreenshot', {
        format: 'png',
        captureBeyondViewport: false
      });
      const foldPath = path.join(outDir, `${page.name}-${vp.name}.png`);
      fs.writeFileSync(foldPath, Buffer.from(foldRes.data, 'base64'));
      console.log(`Saved fold screenshot: ${foldPath}`);

      // Fullpage screenshot
      const fullRes = await sendCmd(ws, 'Page.captureScreenshot', {
        format: 'png',
        captureBeyondViewport: true
      });
      const fullPath = path.join(outDir, `${page.name}-fullpage-${vp.name}.png`);
      fs.writeFileSync(fullPath, Buffer.from(fullRes.data, 'base64'));
      console.log(`Saved fullpage screenshot: ${fullPath}`);
    }
  }

  ws.close();
  proc.kill();
  console.log('All screenshots completed successfully!');
  process.exit(0);
}

capture().catch(err => {
  console.error('Error during capture:', err);
  process.exit(1);
});
