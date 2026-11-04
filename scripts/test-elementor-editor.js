import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_vol_test');

const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  `--user-data-dir=${profileDir}`,
  '--remote-debugging-port=9222',
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
  await new Promise(r => setTimeout(r, 3000));
  const targets = await fetch('http://127.0.0.1:9222/json').then(r => r.json());
  const pageTarget = targets.find(t => t.type === 'page') || targets[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');

  console.log('Navigating to Elementor editor for page 21 (Home)...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });

  // Wait for Elementor to finish loading (typically 5-10s)
  console.log('Waiting for Elementor editor to load...');
  let loaded = false;
  for (let i = 0; i < 20; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const status = await evalExpr(ws, `(() => {
      return {
        hasElementor: !!window.elementor,
        hasE: !!window.$e,
        isLoaded: window.elementor?.loaded === true || !!document.querySelector('#elementor-preview-iframe'),
      };
    })()`);
    if (status?.isLoaded) {
      console.log('Elementor loaded!', status);
      loaded = true;
      break;
    }
  }

  if (!loaded) {
    console.error('Elementor failed to load within timeout');
  } else {
    // Inspect available commands in $e
    const details = await evalExpr(ws, `(() => {
      return {
        title: document.title,
        publishBtnText: document.querySelector('#elementor-panel-saver-button-publish')?.innerText?.trim(),
        hasContainer: !!window.elementor?.getContainer,
      };
    })()`);
    console.log('Editor details:', details);
  }

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
