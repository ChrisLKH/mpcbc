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
  console.log('Logged in URL:', await evalExpr(ws, 'window.location.href'));

  // ==========================================
  // TEST: MENU ITEM IN WP ADMIN (Appearance > Menus)
  // ==========================================
  console.log('\n=== TEST 4: MENU ITEM IN APPEARANCE > MENUS ===');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/nav-menus.php' });
  await new Promise(r => setTimeout(r, 3000));

  // Find first menu item title input
  const firstItemTitleInput = await evalExpr(ws, `(() => {
    const item = document.querySelector('#menu-to-edit li');
    if (!item) return null;
    // Click item to expand if not expanded
    const opener = item.querySelector('.item-edit');
    if (opener) opener.click();
    const input = item.querySelector('.edit-menu-item-title');
    return input ? input.value : null;
  })()`);
  console.log('First menu item title before edit:', firstItemTitleInput);

  // Change to "Visit Us" and save
  await evalExpr(ws, `(() => {
    const item = document.querySelector('#menu-to-edit li');
    const input = item.querySelector('.edit-menu-item-title');
    input.value = 'Visit Us';
    input.dispatchEvent(new Event('change', { bubbles: true }));
    document.querySelector('#save_menu_header').click();
  })()`);

  console.log('Saved menu. Waiting for reload...');
  await new Promise(r => setTimeout(r, 4000));

  // Check live site
  const liveMenuHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const hasVisitUs = liveMenuHtml.includes('Visit Us');
  console.log('Live site shows "Visit Us":', hasVisitUs ? 'PASSED' : 'FAILED');

  // Restore to "Visit"
  console.log('Restoring menu item to "Visit"...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/nav-menus.php' });
  await new Promise(r => setTimeout(r, 3000));

  await evalExpr(ws, `(() => {
    const item = document.querySelector('#menu-to-edit li');
    const opener = item.querySelector('.item-edit');
    if (opener) opener.click();
    const input = item.querySelector('.edit-menu-item-title');
    input.value = 'Visit';
    input.dispatchEvent(new Event('change', { bubbles: true }));
    document.querySelector('#save_menu_header').click();
  })()`);

  await new Promise(r => setTimeout(r, 4000));
  const restoredMenuHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  console.log('Live site restored to "Visit":', restoredMenuHtml.includes('Visit') && !restoredMenuHtml.includes('Visit Us') ? 'PASSED' : 'FAILED');

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
