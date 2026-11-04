import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import os from 'node:os';
import { spawn, execSync } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

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
  const tmpProfile = fs.mkdtempSync(path.join(os.tmpdir(), 'chrome-headings-'));
  const port = 9228;
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${tmpProfile}`,
    '--window-size=390,844',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2000));

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
    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/' });
    await new Promise(r => setTimeout(r, 2000));

    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: 390,
      height: 844,
      deviceScaleFactor: 1,
      mobile: true
    });
    await new Promise(r => setTimeout(r, 1000));

    const check = await sendCmd(ws, 'Runtime.evaluate', {
      returnByValue: true,
      expression: `(() => {
        const sections = ['watch', 'news', 'pastors', 'community', 'kids', 'sermons', 'connect', 'give'];
        const report = [];
        sections.forEach(id => {
          const sec = document.getElementById(id);
          if (!sec) return;
          const h2 = sec.querySelector('h2');
          if (h2) {
            const style = window.getComputedStyle(h2);
            const lh = parseFloat(style.lineHeight) || (parseFloat(style.fontSize) * 1.2);
            const height = h2.offsetHeight;
            const lines = Math.round(height / lh);
            report.push({
              section: id,
              text: h2.innerText.trim(),
              fontSize: style.fontSize,
              lineHeight: style.lineHeight,
              height,
              lines
            });
          }
        });
        return report;
      })()`
    });

    console.log(JSON.stringify(check.result.value, null, 2));

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

run().catch(console.error);
