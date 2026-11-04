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
  await new Promise(r => setTimeout(r, 2000));

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

  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/' });
  await new Promise(r => setTimeout(r, 2500));

  const evalRes = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const v = document.querySelector('.elementor-element-live_video_english');
      const w = v ? v.querySelector('.elementor-wrapper') : null;
      return {
        v_borderRadius: v ? window.getComputedStyle(v).borderRadius : null,
        v_overflow: v ? window.getComputedStyle(v).overflow : null,
        w_borderRadius: w ? window.getComputedStyle(w).borderRadius : null,
        w_overflow: w ? window.getComputedStyle(w).overflow : null,
        w_aspectRatio: w ? window.getComputedStyle(w).aspectRatio : null,
        w_width: w ? w.offsetWidth : null,
        w_height: w ? w.offsetHeight : null
      };
    })()`,
    returnByValue: true
  });

  console.log('Video widget computed styles:\n', evalRes.result.value);

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error:', err);
  process.exit(1);
});
