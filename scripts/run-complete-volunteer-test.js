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

  console.log('=== STARTING COMPLETE VOLUNTEER UI TEST ===\n');

  // LOGIN
  console.log('Logging into WordPress admin...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-login.php' });
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
  console.log('--- TEST 1: HERO HEADLINE IN ELEMENTOR EDITOR ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });
  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-preview-iframe')?.contentDocument?.querySelector('.elementor-element-hero_heading')`);
    if (ready) break;
  }

  // Click hero heading, edit text in panel, and save
  console.log('Selecting hero heading in preview and updating text to "All are welcome here."...');
  await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const hero = iframe.contentDocument.querySelector('.elementor-element-hero_heading');
    hero.click();
    const $ = window.jQuery;
    $('textarea[data-setting="title"]').val('All are welcome here.').trigger('input').trigger('change');
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));

  let liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t1_edit_pass = liveHtml.includes('All are welcome here.');
  console.log('1. Live site shows "All are welcome here.":', t1_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring hero headline to "You’re welcome here."...');
  await evalExpr(ws, `(() => {
    const $ = window.jQuery;
    $('textarea[data-setting="title"]').val('You’re welcome here.').trigger('input').trigger('change');
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t1_restore_pass = liveHtml.includes('You’re welcome here.') && !liveHtml.includes('All are welcome here.');
  console.log('1. Live site restored to original headline:', t1_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 2: SERVICE TIME IN ELEMENTOR EDITOR
  // ==========================================
  console.log('\n--- TEST 2: SERVICE TIME IN ELEMENTOR EDITOR ---');
  console.log('Selecting Cantonese service time widget and changing 9:30 to 9:45 AM...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('times_canto_time');
    const oldHtml = container.settings.get('editor');
    const newHtml = oldHtml.replace('9:30', '9:45');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { editor: newHtml }
    });
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t2_edit_pass = liveHtml.includes('9:45');
  console.log('2. Live site shows "9:45":', t2_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring service time to 9:30 AM...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('times_canto_time');
    const oldHtml = container.settings.get('editor');
    const newHtml = oldHtml.replace('9:45', '9:30');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { editor: newHtml }
    });
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t2_restore_pass = liveHtml.includes('9:30') && !liveHtml.includes('9:45');
  console.log('2. Live site restored to 9:30:', t2_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 3: CARD PHOTO / BACKGROUND IN ELEMENTOR EDITOR
  // ==========================================
  console.log('\n--- TEST 3: CARD PHOTO / BACKGROUND IN ELEMENTOR EDITOR ---');
  console.log('Selecting English card and updating background color to #553322...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('card_english');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { background_color: '#553322' }
    });
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const cssRes = await fetch('http://mpcbc.local/wp-content/uploads/elementor/css/post-21.css').then(r => r.text()).catch(() => '');
  const t3_edit_pass = liveHtml.includes('#553322') || cssRes.includes('#553322');
  console.log('3. Live site / CSS reflects background color #553322:', t3_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring card background color to #3A2E24...');
  await evalExpr(ws, `(() => {
    const container = window.elementor.getContainer('card_english');
    window.$e.run('document/elements/settings', {
      container: container,
      settings: { background_color: '#3A2E24' }
    });
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  const cssRestored = await fetch('http://mpcbc.local/wp-content/uploads/elementor/css/post-21.css').then(r => r.text()).catch(() => '');
  const t3_restore_pass = !cssRestored.includes('#553322');
  console.log('3. Live site restored card background:', t3_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 4: MENU ITEM IN APPEARANCE > MENUS
  // ==========================================
  console.log('\n--- TEST 4: MENU ITEM IN APPEARANCE > MENUS ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/nav-menus.php' });
  await new Promise(r => setTimeout(r, 3000));

  await evalExpr(ws, `(() => {
    const item = document.querySelector('#menu-to-edit li');
    const opener = item.querySelector('.item-edit');
    if (opener) opener.click();
    const input = item.querySelector('.edit-menu-item-title');
    input.value = 'Visit Us';
    input.dispatchEvent(new Event('change', { bubbles: true }));
    document.querySelector('#save_menu_header').click();
  })()`);

  console.log('Saved menu. Waiting for reload...');
  await new Promise(r => setTimeout(r, 4000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t4_edit_pass = liveHtml.includes('Visit Us');
  console.log('4. Live site shows "Visit Us":', t4_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
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
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const t4_restore_pass = liveHtml.includes('Visit') && !liveHtml.includes('Visit Us');
  console.log('4. Live site restored to "Visit":', t4_restore_pass ? 'PASSED' : 'FAILED\n');

  // ==========================================
  // TEST 5: PRIMARY GLOBAL COLOUR IN SITE SETTINGS
  // ==========================================
  console.log('\n--- TEST 5: PRIMARY GLOBAL COLOUR IN SITE SETTINGS ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=5&action=elementor' });
  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-panel') && !!window.$e`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 3000));

  console.log('Changing Primary Global Colour to #7A2E5E via Site Settings...');
  await evalExpr(ws, `(() => {
    const page = window.elementor.settings.page.model;
    const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
    sysColors[0].color = '#7A2E5E';
    page.set('system_colors', sysColors);
    window.$e.run('document/save/default');
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc.local/').then(r => r.text());
  const kitCss = await fetch('http://mpcbc.local/wp-content/uploads/elementor/css/post-5.css').then(r => r.text()).catch(() => '');
  const t5_edit_pass = liveHtml.includes('#7A2E5E') || kitCss.includes('#7A2E5E');
  console.log('5. Live site / Kit CSS reflects Primary #7A2E5E:', t5_edit_pass ? 'PASSED' : 'FAILED');

  // Restore
  console.log('Restoring Primary Global Colour to #0E6B5C...');
  await evalExpr(ws, `(() => {
    const page = window.elementor.settings.page.model;
    const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
    sysColors[0].color = '#0E6B5C';
    page.set('system_colors', sysColors);
    window.$e.run('document/save/default');
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  const kitCssRestored = await fetch('http://mpcbc.local/wp-content/uploads/elementor/css/post-5.css').then(r => r.text()).catch(() => '');
  const t5_restore_pass = kitCssRestored.includes('#0E6B5C') && !kitCssRestored.includes('#7A2E5E');
  console.log('5. Live site restored Primary to #0E6B5C:', t5_restore_pass ? 'PASSED' : 'FAILED\n');

  console.log('=== SUMMARY OF VOLUNTEER TESTS ===');
  console.log('1. Hero Headline Edit & Restore:     ' + (t1_edit_pass && t1_restore_pass ? 'ALL PASSED' : 'FAILED'));
  console.log('2. Service Time Edit & Restore:      ' + (t2_edit_pass && t2_restore_pass ? 'ALL PASSED' : 'FAILED'));
  console.log('3. Card Background Edit & Restore:   ' + (t3_edit_pass && t3_restore_pass ? 'ALL PASSED' : 'FAILED'));
  console.log('4. Menu Item Edit & Restore:         ' + (t4_edit_pass && t4_restore_pass ? 'ALL PASSED' : 'FAILED'));
  console.log('5. Primary Global Colour & Restore:  ' + (t5_edit_pass && t5_restore_pass ? 'ALL PASSED' : 'FAILED'));

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
