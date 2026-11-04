import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_vol_test_b_' + Date.now());

const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  `--user-data-dir=${profileDir}`,
  '--remote-debugging-port=9335',
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
  const targets = await fetch('http://127.0.0.1:9335/json').then(r => r.json());
  const pageTarget = targets.find(t => t.type === 'page') || targets[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');

  console.log('=== STARTING VOLUNTEER UI TEST FOR DESIGN B ===\n');

  // LOGIN
  console.log('1. Logging into WordPress admin...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);
  await new Promise(r => setTimeout(r, 3000));
  console.log('Logged in successfully.\n');

  // ==========================================
  // TEST 1: HERO HEADLINE IN ELEMENTOR EDITOR
  // ==========================================
  console.log('--- TEST 1: HEADLINE IN ELEMENTOR EDITOR ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=23&action=elementor' });
  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-preview-iframe') && !!window.$e`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 3000));

  console.log('Selecting Welcome Heading in editor and updating to "All are welcome here, whichever language you pray in."...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('wel_h2');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { title: 'All are welcome here, whichever language you pray in.' }
    });
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));

  let liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t1_edit_pass = liveHtml.includes('All are welcome here, whichever language you pray in.');
  console.log('1. Live site shows edited headline:', t1_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring headline to "You are welcome here, whichever language you pray in."...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('wel_h2');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { title: 'You are welcome here, whichever language you pray in.' }
    });
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t1_restore_pass = liveHtml.includes('You are welcome here, whichever language you pray in.');
  console.log('1. Live site restored original headline:', t1_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 2: CARD PHOTO IN ELEMENTOR EDITOR
  // ==========================================
  console.log('\n--- TEST 2: PHOTO IN ELEMENTOR EDITOR ---');
  console.log('Selecting Cantonese service image and swapping to church-sign.jpg...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('img_canto');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: {
        image: {
          url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/church-sign.jpg',
          id: 9
        }
      }
    });
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t2_edit_pass = liveHtml.includes('church-sign.jpg');
  console.log('2. Live site shows new photo (church-sign.jpg):', t2_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring image to thumb-cantonese.png...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('img_canto');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: {
        image: {
          url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/thumb-cantonese.png',
          id: 14
        }
      }
    });
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t2_restore_pass = liveHtml.includes('thumb-cantonese.png');
  console.log('2. Live site restored original photo:', t2_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 3: MENU ITEM IN APPEARANCE > MENUS
  // ==========================================
  console.log('\n--- TEST 3: MENU ITEM IN APPEARANCE > MENUS ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/nav-menus.php' });
  await new Promise(r => setTimeout(r, 3000));

  await evalExpr(ws, `(() => {
    const items = document.querySelectorAll('#menu-to-edit li');
    let newsItem = null;
    items.forEach(li => {
      const title = li.querySelector('.menu-item-title')?.textContent || '';
      if (title.includes('Newsletter')) newsItem = li;
    });
    if (newsItem) {
      const editBtn = newsItem.querySelector('.item-edit');
      if (editBtn) editBtn.click();
      const input = newsItem.querySelector('.edit-menu-item-title');
      if (input) {
        input.value = 'Weekly Bulletin';
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    document.querySelector('#save_menu_header').click();
  })()`);

  console.log('Waiting 4s for menu save...');
  await new Promise(r => setTimeout(r, 4000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t3_edit_pass = liveHtml.includes('Weekly Bulletin');
  console.log('3. Live site shows "Weekly Bulletin":', t3_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring menu item to "Newsletter"...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/nav-menus.php' });
  await new Promise(r => setTimeout(r, 3000));
  await evalExpr(ws, `(() => {
    const items = document.querySelectorAll('#menu-to-edit li');
    let newsItem = null;
    items.forEach(li => {
      const title = li.querySelector('.menu-item-title')?.textContent || '';
      if (title.includes('Weekly Bulletin')) newsItem = li;
    });
    if (newsItem) {
      const editBtn = newsItem.querySelector('.item-edit');
      if (editBtn) editBtn.click();
      const input = newsItem.querySelector('.edit-menu-item-title');
      if (input) {
        input.value = 'Newsletter';
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
    document.querySelector('#save_menu_header').click();
  })()`);
  await new Promise(r => setTimeout(r, 4000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t3_restore_pass = liveHtml.includes('Newsletter') && !liveHtml.includes('Weekly Bulletin');
  console.log('3. Live site restored menu item to "Newsletter":', t3_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 4: PRIMARY GLOBAL COLOUR IN SITE SETTINGS
  // ==========================================
  console.log('\n--- TEST 4: PRIMARY GLOBAL COLOUR IN SITE SETTINGS ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=6&action=elementor' });
  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-panel') && !!window.$e`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 3000));

  console.log('Changing Primary Global Colour to #0E6B5C via Site Settings...');
  await evalExpr(ws, `(() => {
    const page = window.elementor.settings.page.model;
    const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
    sysColors[0].color = '#0E6B5C';
    page.set('system_colors', sysColors);
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  const kitCss = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
  const t4_edit_pass = kitCss.includes('#0E6B5C');
  console.log('4. Live Kit CSS reflects Primary #0E6B5C:', t4_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring Primary Global Colour to #943759...');
  await evalExpr(ws, `(() => {
    const page = window.elementor.settings.page.model;
    const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
    sysColors[0].color = '#943759';
    page.set('system_colors', sysColors);
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  const kitCssRestored = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
  const t4_restore_pass = kitCssRestored.includes('#943759') && !kitCssRestored.includes('#0E6B5C');
  console.log('4. Live Kit CSS restored Primary to #943759:', t4_restore_pass ? 'PASSED' : 'FAILED\n');

  console.log('=== SUMMARY OF VOLUNTEER UI TESTS ===');
  console.log('1. Headline Edit & Restore:         ' + (t1_edit_pass && t1_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('2. Photo Edit & Restore:            ' + (t2_edit_pass && t2_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('3. Menu Item Edit & Restore:        ' + (t3_edit_pass && t3_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('4. Primary Global Colour & Restore: ' + (t4_edit_pass && t4_restore_pass ? 'PASSED' : 'FAILED'));

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error during volunteer test:', err);
  proc.kill();
  process.exit(1);
});
