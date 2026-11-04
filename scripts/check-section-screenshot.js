import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { spawn, execSync } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outDir = path.resolve('C:\\Projects\\mpcbc\\screenshots\\progress');
if (!fs.existsSync(outDir)) {
  fs.mkdirSync(outDir, { recursive: true });
}

const sectionTag = process.argv[2] || 'sec4';
const scrollId = process.argv[3] || sectionTag;

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
  const tmpProfile = fs.mkdtempSync(path.join(os.tmpdir(), 'chrome-shot-'));
  const port = 9225;
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
  await new Promise(r => setTimeout(r, 2000));

  const res = await new Promise((resolve, reject) => {
    const req = http.get(`http://127.0.0.1:${port}/json`, (r) => {
      let d = '';
      r.on('data', c => d += c);
      r.on('end', () => resolve(JSON.parse(d)));
    });
    req.on('error', reject);
  });

  const pageTarget = res.find(t => t.type === 'page') || res[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Network.enable');
  await sendCmd(ws, 'Network.clearBrowserCache');
  await sendCmd(ws, 'Network.setCacheDisabled', { cacheDisabled: true });

  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/' });
  await new Promise(r => setTimeout(r, 2000));

  const vps = [
    { name: '1440', width: 1440, height: 900, mobile: false },
    { name: '390', width: 390, height: 844, mobile: true }
  ];

  for (const vp of vps) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: vp.width,
      height: vp.height,
      deviceScaleFactor: 1,
      mobile: vp.mobile
    });
    await new Promise(r => setTimeout(r, 600));

    // Scroll target into view
    await sendCmd(ws, 'Runtime.evaluate', {
      expression: `const el = document.getElementById("${scrollId}"); if (el) el.scrollIntoView();`
    });
    await new Promise(r => setTimeout(r, 600));

    const shot = await sendCmd(ws, 'Page.captureScreenshot', {
      format: 'png',
      captureBeyondViewport: false
    });
    const shotPath = path.join(outDir, `${sectionTag}-${vp.name}.png`);
    fs.writeFileSync(shotPath, Buffer.from(shot.data, 'base64'));
    console.log(`Saved screenshot: ${shotPath}`);
  }

  ws.close();
  try {
    execSync(`taskkill /T /F /PID ${proc.pid}`, { stdio: 'ignore' });
  } catch (_) {}
  try {
    fs.rmSync(tmpProfile, { recursive: true, force: true });
  } catch (_) {}

  console.log(`Check for ${sectionTag} complete.`);
  process.exit(0);
}

capture().catch(err => {
  console.error(err);
  process.exit(1);
});
