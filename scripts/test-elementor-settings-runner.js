import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_vol_test_' + Date.now());

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

  console.log('Logging in...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);

  await new Promise(r => setTimeout(r, 3000));

  console.log('Navigating to Elementor editor...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });

  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-element-hero_heading');
    })()`);
    if (ready) break;
  }

  const check = await evalExpr(ws, `(() => {
    const timeContainer = window.elementor.getContainer('times_canto_time');
    const cardContainer = window.elementor.getContainer('card_english');
    const kit = window.elementor.getKitView ? window.elementor.getKitView() : null;
    const kitId = window.elementor.config?.initial_document?.settings?.settings?.kit_id || window.elementor.config?.kit_id;

    return {
      hasTimeContainer: !!timeContainer,
      timeVal: timeContainer?.settings?.get('editor'),
      hasCardContainer: !!cardContainer,
      cardBg: cardContainer?.settings?.get('background_color'),
      kitId: kitId,
      hasSiteSettingsCmd: $e.commands.isCurrentRoute ? $e.commands.isCurrentRoute('panel/global') : null,
    };
  })()`);

  console.log('Containers check:', JSON.stringify(check, null, 2));

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
