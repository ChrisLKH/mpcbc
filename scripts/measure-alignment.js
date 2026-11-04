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
  await new Promise(r => setTimeout(r, 3000));

  const evalRes = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const results = [];
      const sections = document.querySelectorAll('.elementor > .e-con, .elementor > .e-con-boxed, .elementor > .e-con-full');
      for (const s of sections) {
        const id = s.id || s.getAttribute('data-id') || 'unnamed';
        const rect = s.getBoundingClientRect();
        const inner = s.querySelector('.e-con-inner') || s;
        const innerRect = inner.getBoundingClientRect();
        const firstChild = inner.firstElementChild;
        const childRect = firstChild ? firstChild.getBoundingClientRect() : null;
        results.push({
          id,
          secLeft: rect.left,
          secWidth: rect.width,
          innerLeft: innerRect.left,
          innerWidth: innerRect.width,
          childLeft: childRect ? childRect.left : null
        });
      }
      return results;
    })()`,
    returnByValue: true
  });

  console.log('Sections left edge measurements at 1440px:');
  console.table(evalRes.result.value);

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error:', err);
  process.exit(1);
});
