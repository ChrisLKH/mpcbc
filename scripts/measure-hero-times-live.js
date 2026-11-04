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
      const hero = document.getElementById('hero_container') || document.querySelector('.elementor-element-hero_container');
      const times = document.getElementById('times_outer_section') || document.querySelector('.elementor-element-times_outer_section');
      const live = document.getElementById('live');

      return {
        hero: hero ? { top: hero.offsetTop, height: hero.offsetHeight, bottom: hero.offsetTop + hero.offsetHeight } : null,
        times: times ? { top: times.offsetTop, height: times.offsetHeight, bottom: times.offsetTop + times.offsetHeight } : null,
        live: live ? { top: live.offsetTop, height: live.offsetHeight } : null
      };
    })()`,
    returnByValue: true
  });

  console.log('Geometry:', JSON.stringify(evalRes.result.value, null, 2));

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error:', err);
  process.exit(1);
});
