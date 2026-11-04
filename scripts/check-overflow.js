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
  const tmpProfile = fs.mkdtempSync(path.join(os.tmpdir(), 'chrome-overflow-'));
  const port = 9227;
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
        const docW = document.documentElement.clientWidth;
        const scrollW = document.documentElement.scrollWidth;
        const main = document.querySelector('main') || document.querySelector('.elementor-21');
        const mainScrollW = main ? main.scrollWidth : null;
        const mainW = main ? main.clientWidth : null;
        
        // Find top-level elements or sections that overflow
        const sections = document.querySelectorAll('.elementor-21 > .elementor-element, #site-header, #site-footer');
        const secReport = [];
        sections.forEach(s => {
          const r = s.getBoundingClientRect();
          secReport.push({
            id: s.id,
            tag: s.tagName,
            cls: s.className.slice(0, 40),
            left: Math.round(r.left),
            right: Math.round(r.right),
            width: Math.round(r.width),
            scrollW: s.scrollWidth,
            clientW: s.clientWidth
          });
        });

        // Find any element with right > 391
        const deepEls = [];
        document.querySelectorAll('*').forEach(el => {
          const r = el.getBoundingClientRect();
          if (r.right > 391) {
            deepEls.push({
              tag: el.tagName,
              id: el.id,
              cls: el.className ? (typeof el.className === 'string' ? el.className.slice(0, 50) : '') : '',
              right: Math.round(r.right),
              width: Math.round(r.width)
            });
          }
        });

        return {
          docW,
          scrollW,
          mainW,
          mainScrollW,
          secReport,
          overflowCount: deepEls.length,
          top10Deep: deepEls.slice(0, 10)
        };
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
