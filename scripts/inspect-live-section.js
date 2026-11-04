import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { spawn } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const outDir = path.resolve('c:/Projects/mpcbc/wordpress/screenshots');

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

async function run() {
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--remote-debugging-port=9222',
    '--window-size=1440,900',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2500));

  const res = await new Promise((resolve) => {
    http.get('http://127.0.0.1:9222/json', (r) => {
      let d = '';
      r.on('data', c => d += c);
      r.on('end', () => resolve(JSON.parse(d)));
    });
  });

  const pageTarget = res.find(t => t.type === 'page') || res[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Runtime.enable');
  await sendCmd(ws, 'Network.enable');
  await sendCmd(ws, 'Network.clearBrowserCache');
  await sendCmd(ws, 'Network.setCacheDisabled', { cacheDisabled: true });

  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/?t=' + Date.now() });
  await new Promise(r => setTimeout(r, 3000));

  // Take screenshot at 1440, 768, 390 focused on #live
  for (const vp of [
    { name: '1440', width: 1440, height: 900, mobile: false },
    { name: '768', width: 768, height: 1024, mobile: false },
    { name: '390', width: 390, height: 844, mobile: true }
  ]) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: vp.width,
      height: vp.height,
      deviceScaleFactor: 1,
      mobile: vp.mobile
    });
    await new Promise(r => setTimeout(r, 1000));

    // Scroll #live into view
    await sendCmd(ws, 'Runtime.evaluate', {
      expression: `document.getElementById('live')?.scrollIntoView({ behavior: 'instant', block: 'start' });`
    });
    await new Promise(r => setTimeout(r, 600));

    const shot = await sendCmd(ws, 'Page.captureScreenshot', { format: 'png' });
    const shotPath = path.join(outDir, `live-section-${vp.name}.png`);
    fs.writeFileSync(shotPath, Buffer.from(shot.data, 'base64'));
    console.log(`Saved screenshot: ${shotPath}`);
  }

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error:', err);
  process.exit(1);
});
