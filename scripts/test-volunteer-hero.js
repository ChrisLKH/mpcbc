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

  console.log('Logging into WordPress...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);

  await new Promise(r => setTimeout(r, 3000));

  console.log('Navigating to Elementor editor for page 21 (Home)...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });

  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-element-hero_heading');
    })()`);
    if (ready) break;
  }

  console.log('\n=== TEST 1: HERO HEADLINE IN ELEMENTOR EDITOR ===');
  
  // 1. Click hero heading in preview iframe
  await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const hero = iframe.contentDocument.querySelector('.elementor-element-hero_heading');
    hero.click();
  })()`);

  await new Promise(r => setTimeout(r, 1000));

  // 2. Type "All are welcome here." into textarea[data-setting="title"]
  const typeRes = await evalExpr(ws, `(() => {
    const textarea = document.querySelector('textarea[data-setting="title"]');
    if (!textarea) return 'no_textarea';
    textarea.value = 'All are welcome here.';
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
    
    // Save
    const saveBtn = document.querySelector('#elementor-panel-saver-button-publish');
    if (saveBtn) saveBtn.click();
    return 'typed_and_clicked_save';
  })()`);
  console.log('Edit result:', typeRes);

  console.log('Waiting for save to complete...');
  await new Promise(r => setTimeout(r, 5000));

  // 3. Verify on live site
  const live1 = await fetch('http://mpcbc.local/').then(r => r.text());
  const pass1 = live1.includes('All are welcome here.');
  console.log('Live site shows "All are welcome here.":', pass1 ? 'PASSED' : 'FAILED');

  // 4. Restore original headline "You’re welcome here."
  console.log('Restoring hero headline in editor...');
  await evalExpr(ws, `(() => {
    const textarea = document.querySelector('textarea[data-setting="title"]');
    if (!textarea) return 'no_textarea';
    textarea.value = 'You’re welcome here.';
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
    
    const saveBtn = document.querySelector('#elementor-panel-saver-button-publish');
    if (saveBtn) saveBtn.click();
    return 'restored_and_clicked_save';
  })()`);

  await new Promise(r => setTimeout(r, 5000));
  const live2 = await fetch('http://mpcbc.local/').then(r => r.text());
  const pass2 = live2.includes('You’re welcome here.') && !live2.includes('All are welcome here.');
  console.log('Live site restored to "You’re welcome here.":', pass2 ? 'PASSED' : 'FAILED');

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
