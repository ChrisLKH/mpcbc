import http from 'node:http';
import os from 'node:os';
import path from 'node:path';
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
  const port = 9339;
  const tempProfile = path.join(os.tmpdir(), 'chrome-test-' + Date.now());
  const proc = spawn(chromePath, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    `--user-data-dir=${tempProfile}`,
    `--remote-debugging-port=${port}`,
    'about:blank'
  ]);
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
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', { width: 1920, height: 1080, deviceScaleFactor: 1, mobile: false });
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2500));

  const boxedInfo = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const list = [];
      document.querySelectorAll('#sunday, #news, #welcome, #connect, #children, #events, #leaders, #give, footer .elementor-element-59a8de09').forEach(c => {
        list.push({
          id: c.id || c.className.split(' ').find(x => x.startsWith('elementor-element-')),
          w: Math.round(c.getBoundingClientRect().width),
          left: Math.round(c.getBoundingClientRect().left),
          cls: c.className
        });
      });
      return list;
    })()`,
    returnByValue: true
  });

  console.log(JSON.stringify(boxedInfo.result.value, null, 2));

  ws.close();
  proc.kill();
}

inspect().catch(err => { console.error(err); process.exit(1); });
