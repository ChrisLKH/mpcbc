import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_dup2_' + Date.now());

const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  `--user-data-dir=${profileDir}`,
  '--remote-debugging-port=9365',
  '--window-size=1600,1000',
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
  const targets = await fetch('http://127.0.0.1:9365/json').then(r => r.json());
  const ws = new WebSocket(targets[0].webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);
  await new Promise(r => setTimeout(r, 3000));

  console.log('Navigating to Duplicator Step 1...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/admin.php?page=duplicator&tab=packages&inner_page=new1' });
  await new Promise(r => setTimeout(r, 4000));

  const pageTitle = await evalExpr(ws, `document.title`);
  console.log('Step 1 Page Title:', pageTitle);

  // Click Next
  console.log('Clicking Next on Step 1...');
  const nextRes = await evalExpr(ws, `(() => {
    const btn = document.querySelector('#dup-next-btn') || Array.from(document.querySelectorAll('input, button, a')).find(el => (el.innerText || el.value || '').trim() === 'Next');
    if (btn) {
      btn.click();
      return 'next_clicked';
    }
    return 'no_next_found';
  })()`);
  console.log('Next button result:', nextRes);

  console.log('Waiting for scan to complete...');
  for (let i = 0; i < 30; i++) {
    await new Promise(r => setTimeout(r, 2000));
    const scanReady = await evalExpr(ws, `(() => {
      const buildBtn = document.querySelector('#dup-build-btn') || Array.from(document.querySelectorAll('input, button, a')).find(el => (el.innerText || el.value || '').trim() === 'Build');
      return !!buildBtn;
    })()`);
    if (scanReady) {
      console.log('Scan completed! Build button found.');
      break;
    }
  }

  // Click Build
  console.log('Clicking Build on Step 2...');
  const buildRes = await evalExpr(ws, `(() => {
    const buildBtn = document.querySelector('#dup-build-btn') || Array.from(document.querySelectorAll('input, button, a')).find(el => (el.innerText || el.value || '').trim() === 'Build');
    if (buildBtn) {
      buildBtn.click();
      return 'build_clicked';
    }
    return 'no_build_found';
  })()`);
  console.log('Build button result:', buildRes);

  console.log('Waiting for package to build (up to 90s)...');
  for (let i = 0; i < 45; i++) {
    await new Promise(r => setTimeout(r, 2000));
    const done = await evalExpr(ws, `(() => {
      const text = document.body.innerText;
      return text.includes('Package Completed') || text.includes('Download') || text.includes('Both Files');
    })()`);
    if (done) {
      console.log('Package build completed successfully!');
      break;
    }
  }

  const status = await evalExpr(ws, `document.body.innerText.substring(0, 500)`);
  console.log('Final status snippet:\n', status);

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
