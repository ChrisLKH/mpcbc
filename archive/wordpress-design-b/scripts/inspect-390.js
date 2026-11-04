import http from 'node:http';
import { spawn } from 'node:child_process';

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

async function inspect() {
  const port = 9336;
  const proc = spawn(chromePath, ['--headless=new', '--no-sandbox', `--remote-debugging-port=${port}`, 'about:blank']);
  await new Promise(r => setTimeout(r, 2000));

  const res = await new Promise((resolve, reject) => {
    http.get(`http://127.0.0.1:${port}/json`, (r) => {
      let d = '';
      r.on('data', c => d += c);
      r.on('end', () => resolve(JSON.parse(d)));
    }).on('error', reject);
  });

  const ws = new WebSocket((res.find(t => t.type === 'page') || res[0]).webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Runtime.enable');
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', { width: 390, height: 844, deviceScaleFactor: 1, mobile: true });
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2000));

  const check = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const craft = document.querySelector('.elementor-element-725d348e');
      const nursery = document.querySelector('.elementor-element-dd886840');
      const r1 = craft.getBoundingClientRect();
      const r2 = nursery.getBoundingClientRect();
      const parent = craft.parentElement;
      return {
        parentTag: parent.tagName,
        parentCls: parent.className,
        parentFlexDir: window.getComputedStyle(parent).flexDirection,
        craft: { x: Math.round(r1.left), y: Math.round(r1.top), w: Math.round(r1.width), h: Math.round(r1.height) },
        nursery: { x: Math.round(r2.left), y: Math.round(r2.top), w: Math.round(r2.width), h: Math.round(r2.height) },
        sideBySide: Math.round(r1.top) === Math.round(r2.top),
        scrollHeight: document.documentElement.scrollHeight
      };
    })()`,
    returnByValue: true
  });

  console.log(JSON.stringify(check.result.value, null, 2));

  ws.close();
  proc.kill();
}

inspect().catch(err => { console.error(err); process.exit(1); });
