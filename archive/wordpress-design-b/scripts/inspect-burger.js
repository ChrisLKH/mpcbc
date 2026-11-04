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

async function evalExpr(ws, expr) {
  const res = await sendCmd(ws, 'Runtime.evaluate', {
    expression: expr,
    returnByValue: true,
    awaitPromise: true
  });
  return res?.result?.value;
}

async function run() {
  const port = 9344;
  const proc = spawn(chromePath, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    `--remote-debugging-port=${port}`,
    '--window-size=390,844',
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

  const pageTarget = res.find(t => t.type === 'page') || res[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 390,
    height: 844,
    deviceScaleFactor: 1,
    mobile: true
  });

  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2000));

  const burgerInfo = await evalExpr(ws, `(() => {
    const btn = document.querySelector('.site-navigation-toggle');
    const icon = document.querySelector('.site-navigation-toggle-icon');
    const holder = document.querySelector('.site-navigation-toggle-holder');
    const header = document.querySelector('.site-header');
    const inner = document.querySelector('.header-inner');

    return {
      btnHTML: btn ? btn.outerHTML : null,
      btnStyles: btn ? {
        color: getComputedStyle(btn).color,
        border: getComputedStyle(btn).border,
        bg: getComputedStyle(btn).backgroundColor,
        fill: getComputedStyle(btn).fill
      } : null,
      iconStyles: icon ? {
        color: getComputedStyle(icon).color,
        border: getComputedStyle(icon).border,
        bg: getComputedStyle(icon).backgroundColor,
        beforeBg: getComputedStyle(icon, '::before').backgroundColor,
        afterBg: getComputedStyle(icon, '::after').backgroundColor
      } : null,
      headerInnerChildren: inner ? Array.from(inner.children).map(c => ({
        tag: c.tagName,
        cls: c.className,
        rect: c.getBoundingClientRect()
      })) : []
    };
  })()`);

  console.log('Burger info:', JSON.stringify(burgerInfo, null, 2));

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  process.exit(1);
});
