import http from 'node:http';
import { spawn } from 'node:child_process';
import path from 'node:path';
import os from 'node:os';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const profileDir = path.join(os.tmpdir(), 'chrome_wp_vol_full_' + Date.now());

const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  `--user-data-dir=${profileDir}`,
  '--remote-debugging-port=9355',
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
  if (res?.exceptionDetails) {
    console.error('JS EXCEPTION:', res.exceptionDetails.text, res.exceptionDetails.exception?.description);
  }
  return res?.result?.value;
}

async function waitForLiveText(text, shouldInclude = true, maxTries = 8) {
  for (let i = 0; i < maxTries; i++) {
    await new Promise(r => setTimeout(r, 1500));
    const html = await fetch('http://mpcbc-2.local/').then(r => r.text()).catch(() => '');
    const found = html.includes(text);
    if (found === shouldInclude) return true;
  }
  return false;
}

async function run() {
  await new Promise(r => setTimeout(r, 2500));
  const targets = await fetch('http://127.0.0.1:9355/json').then(r => r.json());
  const pageTarget = targets.find(t => t.type === 'page') || targets[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');

  console.log('=== VOLUNTEER UI TEST SUITE FOR DESIGN B ===\n');

  // LOGIN
  console.log('Logging in to WordPress admin...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);
  await new Promise(r => setTimeout(r, 3000));

  // NAVIGATE TO HOME IN ELEMENTOR
  console.log('Navigating to Home in Elementor editor...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=23&action=elementor' });

  for (let i = 0; i < 35; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-widget-heading');
    })()`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 2500));

  // -------------------------------------------------------------
  // TEST 1: HEADLINE EDIT & RESTORE
  // -------------------------------------------------------------
  console.log('\n--- TEST 1: HEADLINE (Welcome Title) ---');
  const t1EditRes = await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const doc = iframe.contentDocument;
    const h2 = Array.from(doc.querySelectorAll('h2')).find(h => h.textContent.includes('You are welcome here'));
    if (!h2) return 'no_h2';
    const widget = h2.closest('.elementor-element');
    const dataId = widget ? widget.getAttribute('data-id') : 'c1a6be9d';
    const c = window.elementor.getContainer(dataId);
    if (!c) return 'no_container';

    // Volunteer clicks headline and edits in panel
    window.$e.run('panel/editor/open', { container: c });
    c.settings.set('title', 'All are welcome here, whichever language you pray in.');
    window.$e.run('document/save/default');
    return 'headline_updated';
  })()`);
  console.log('T1 edit trigger:', t1EditRes);

  const t1_edit_pass = await waitForLiveText('All are welcome here, whichever language you pray in.', true);
  console.log('1. Live site shows new headline:', t1_edit_pass ? 'PASSED' : 'FAILED');

  // Restore headline
  await evalExpr(ws, `(() => {
    const c = window.elementor.getContainer('c1a6be9d');
    if (c) {
      c.settings.set('title', 'You are welcome here, whichever language you pray in.');
      window.$e.run('document/save/default');
    }
  })()`);
  const t1_restore_pass = await waitForLiveText('You are welcome here, whichever language you pray in.', true);
  console.log('1. Live site restored headline:  ', t1_restore_pass ? 'PASSED' : 'FAILED');

  // -------------------------------------------------------------
  // TEST 2: PHOTO EDIT & RESTORE
  // -------------------------------------------------------------
  console.log('\n--- TEST 2: PHOTO (Cantonese Service Card Thumbnail) ---');
  const t2EditRes = await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const doc = iframe.contentDocument;
    const img = Array.from(doc.querySelectorAll('.elementor-widget-image img')).find(i => i.src.includes('thumb-cantonese'));
    if (!img) return 'no_img';
    const widget = img.closest('.elementor-element');
    const dataId = widget ? widget.getAttribute('data-id') : 'db82c894';
    const c = window.elementor.getContainer(dataId);
    if (!c) return 'no_container';

    // Volunteer selects image and changes photo to church-sign.jpg
    window.$e.run('panel/editor/open', { container: c });
    c.settings.set('image', {
      url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/church-sign.jpg',
      id: 9
    });
    window.$e.run('document/save/default');
    return 'photo_swapped';
  })()`);
  console.log('T2 edit trigger:', t2EditRes);

  const t2_edit_pass = await waitForLiveText('church-sign.jpg', true);
  console.log('2. Live site shows new photo:   ', t2_edit_pass ? 'PASSED' : 'FAILED');

  // Restore photo
  await evalExpr(ws, `(() => {
    const c = window.elementor.getContainer('db82c894');
    if (c) {
      c.settings.set('image', {
        url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/thumb-cantonese.png',
        id: 14
      });
      window.$e.run('document/save/default');
    }
  })()`);
  const t2_restore_pass = await waitForLiveText('thumb-cantonese.png', true);
  console.log('2. Live site restored photo:     ', t2_restore_pass ? 'PASSED' : 'FAILED');

  // -------------------------------------------------------------
  // TEST 3: MENU ITEM EDIT & RESTORE
  // -------------------------------------------------------------
  console.log('\n--- TEST 3: MENU ITEM (Appearance > Menus) ---');
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
    document.querySelector('#save_menu_header')?.click();
  })()`);

  const t3_edit_pass = await waitForLiveText('Weekly Bulletin', true);
  console.log('3. Live site shows new menu item: ', t3_edit_pass ? 'PASSED' : 'FAILED');

  // Restore menu item
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
    document.querySelector('#save_menu_header')?.click();
  })()`);

  const t3_restore_pass = await waitForLiveText('Newsletter', true);
  console.log('3. Live site restored menu item:  ', t3_restore_pass ? 'PASSED' : 'FAILED');

  // -------------------------------------------------------------
  // TEST 4: PRIMARY GLOBAL COLOUR EDIT & RESTORE
  // -------------------------------------------------------------
  console.log('\n--- TEST 4: PRIMARY GLOBAL COLOUR ---');
  // Volunteer edits Primary global colour in Site Settings
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=23&action=elementor' });
  for (let i = 0; i < 35; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-widget-heading');
    })()`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 2000));

  // Open Site Settings
  await evalExpr(ws, `window.$e.run('panel/global/open')`);
  await new Promise(r => setTimeout(r, 2000));

  // Change Primary global color to #0E6B5C
  const t4EditRes = await evalExpr(ws, `(() => {
    try {
      const kit = window.elementor?.documents?.get(6) || window.elementor?.documents?.getCurrent();
      if (kit) {
        const sys = JSON.parse(JSON.stringify(kit.container.settings.get('system_colors')));
        sys[0].color = '#0E6B5C';
        kit.container.settings.set('system_colors', sys);
        window.$e.run('document/save/default');
        return 'kit_saved';
      }
      return 'no_kit';
    } catch(e) {
      return e.message;
    }
  })()`);
  console.log('T4 edit trigger:', t4EditRes);

  // Check kit CSS or live site
  let t4_edit_pass = false;
  for (let i = 0; i < 8; i++) {
    await new Promise(r => setTimeout(r, 1500));
    const kitCss = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
    if (kitCss.includes('#0E6B5C')) {
      t4_edit_pass = true;
      break;
    }
  }
  console.log('4. Live Kit reflects Primary #0E6B5C: ', t4_edit_pass ? 'PASSED' : 'FAILED');

  // Restore Primary global color to #943759
  await evalExpr(ws, `(() => {
    try {
      const kit = window.elementor?.documents?.get(6) || window.elementor?.documents?.getCurrent();
      if (kit) {
        const sys = JSON.parse(JSON.stringify(kit.container.settings.get('system_colors')));
        sys[0].color = '#943759';
        kit.container.settings.set('system_colors', sys);
        window.$e.run('document/save/default');
      }
    } catch(e) {}
  })()`);

  let t4_restore_pass = false;
  for (let i = 0; i < 8; i++) {
    await new Promise(r => setTimeout(r, 1500));
    const kitCss = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
    if (kitCss.includes('#943759') && !kitCss.includes('#0E6B5C')) {
      t4_restore_pass = true;
      break;
    }
  }
  console.log('4. Live Kit restored Primary #943759: ', t4_restore_pass ? 'PASSED' : 'FAILED');

  console.log('\n======================================================');
  console.log('=== FINAL RESULTS: VOLUNTEER EDITING ACCEPTANCE ===');
  console.log('1. Headline Edit & Restore:         ' + (t1_edit_pass && t1_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('2. Photo Edit & Restore:            ' + (t2_edit_pass && t2_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('3. Menu Item Edit & Restore:        ' + (t3_edit_pass && t3_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('4. Primary Global Colour & Restore: ' + (t4_edit_pass && t4_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('======================================================\n');

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Fatal error:', err);
  proc.kill();
  process.exit(1);
});
