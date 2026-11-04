import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_header_test_' + Date.now());

const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  `--user-data-dir=${profileDir}`,
  '--remote-debugging-port=9222',
  '--window-size=1440,900',
  'about:blank'
];

const proc = spawn(chromePath, args);

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
  await new Promise(r => setTimeout(r, 2500));
  const targets = await fetch('http://127.0.0.1:9222/json').then(r => r.json());
  const pageTarget = targets.find(t => t.type === 'page') || targets[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');

  // Test 1: Home header
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/' });
  await new Promise(r => setTimeout(r, 2000));

  const homeHeader = await evalExpr(ws, `(() => {
    const h = document.querySelector('.site-header');
    if (!h) return null;
    const cs = getComputedStyle(h);
    const link = h.querySelector('.site-navigation a');
    const linkCs = link ? getComputedStyle(link) : {};
    return {
      pos: cs.position,
      bg: cs.backgroundColor,
      borderBottom: cs.borderBottom,
      height: cs.height,
      linkColor: linkCs.color,
    };
  })()`);
  console.log('Home Header Computed Style:', homeHeader);

  // Test 2: Prayer (inner page) header
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/prayer/' });
  await new Promise(r => setTimeout(r, 2000));

  const innerHeader = await evalExpr(ws, `(() => {
    const h = document.querySelector('.site-header');
    if (!h) return null;
    const cs = getComputedStyle(h);
    const link = h.querySelector('.site-navigation a');
    const linkCs = link ? getComputedStyle(link) : {};
    const logo = h.querySelector('.site-branding .site-title a');
    const logoCs = logo ? getComputedStyle(logo) : {};
    const zh = h.querySelector('.site-navigation a[href*="/zh"]');
    const zhCs = zh ? getComputedStyle(zh) : {};
    const btn = h.querySelector('.site-navigation li.button a');
    const btnCs = btn ? getComputedStyle(btn) : {};
    return {
      pos: cs.position,
      bg: cs.backgroundColor,
      borderBottom: cs.borderBottom,
      height: cs.height,
      linkColor: linkCs.color,
      logoText: logo?.innerText,
      logoBorder: logoCs.border,
      zhBorder: zhCs.border,
      btnBg: btnCs.backgroundColor,
    };
  })()`);
  console.log('Prayer Header Computed Style:', innerHeader);

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
