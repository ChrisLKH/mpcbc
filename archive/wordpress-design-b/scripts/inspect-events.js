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

  const rowDetails = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const row = document.querySelector('#events .elementor-element-e0514744');
      if (!row) return null;
      return {
        rowH: Math.round(row.getBoundingClientRect().height),
        flexDir: window.getComputedStyle(row).flexDirection,
        gap: window.getComputedStyle(row).gap,
        children: Array.from(row.children).map(c => ({
          tag: c.tagName,
          cls: c.className,
          h: Math.round(c.getBoundingClientRect().height),
          w: Math.round(c.getBoundingClientRect().width),
          mt: window.getComputedStyle(c).marginTop,
          mb: window.getComputedStyle(c).marginBottom,
        }))
      };
    })()`,
    returnByValue: true
  });

  console.log(JSON.stringify(rowDetails.result.value, null, 2));

  ws.close();
  proc.kill();
}

inspect().catch(err => { console.error(err); process.exit(1); });
