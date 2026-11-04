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
  '--remote-debugging-port=9336',
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
  const targets = await fetch('http://127.0.0.1:9336/json').then(r => r.json());
  const pageTarget = targets.find(t => t.type === 'page') || targets[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');

  console.log('=== STARTING INTERACTIVE VOLUNTEER TEST ===\n');

  // LOGIN
  console.log('Logging in...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-login.php' });
  await new Promise(r => setTimeout(r, 2000));

  await evalExpr(ws, `(() => {
    document.querySelector('#user_login').value = 'mpcbc';
    document.querySelector('#user_pass').value = 'mpcbc';
    document.querySelector('#wp-submit').click();
  })()`);
  await new Promise(r => setTimeout(r, 3000));

  // NAVIGATE TO HOME IN ELEMENTOR
  console.log('Opening Home in Elementor editor...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=23&action=elementor' });

  for (let i = 0; i < 30; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-widget-heading');
    })()`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 2000));

  // -----------------------------------------------------------------
  // 1. HEADLINE: Click the Welcome Heading in Preview, Type in Panel, Save
  // -----------------------------------------------------------------
  console.log('\n--- 1. HEADLINE EDIT & RESTORE ---');
  const hClick = await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const doc = iframe.contentDocument;
    // Find the welcome heading in section #welcome
    const welH2 = Array.from(doc.querySelectorAll('h2')).find(h => h.textContent.includes('You are welcome here'));
    if (!welH2) return 'no_h2_found';
    welH2.click();
    return 'clicked_welH2';
  })()`);
  console.log('Clicked welcome heading:', hClick);

  await new Promise(r => setTimeout(r, 1500));

  const hType = await evalExpr(ws, `(() => {
    const textarea = document.querySelector('textarea[data-setting="title"], input[data-setting="title"]');
    if (!textarea) return 'no_input_in_panel';
    textarea.value = 'All are welcome here, whichever language you pray in.';
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));

    // Click Publish / Update button
    const saveBtn = document.querySelector('#elementor-panel-saver-button-publish, .elementor-panel-saver-button-save');
    if (saveBtn) saveBtn.click();
    return 'typed_and_saved';
  })()`);
  console.log('Headline input result:', hType);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  let liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t1_edit_pass = liveHtml.includes('All are welcome here, whichever language you pray in.');
  console.log('1. Live site shows new headline:', t1_edit_pass ? 'PASSED' : 'FAILED');

  // Restore headline
  console.log('Restoring original headline...');
  await evalExpr(ws, `(() => {
    const textarea = document.querySelector('textarea[data-setting="title"], input[data-setting="title"]');
    if (textarea) {
      textarea.value = 'You are welcome here, whichever language you pray in.';
      textarea.dispatchEvent(new Event('input', { bubbles: true }));
      textarea.dispatchEvent(new Event('change', { bubbles: true }));
      const saveBtn = document.querySelector('#elementor-panel-saver-button-publish, .elementor-panel-saver-button-save');
      if (saveBtn) saveBtn.click();
    }
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t1_restore_pass = liveHtml.includes('You are welcome here, whichever language you pray in.');
  console.log('1. Live site restored original headline:', t1_restore_pass ? 'PASSED' : 'FAILED');

  // -----------------------------------------------------------------
  // 2. PHOTO: Click Cantonese Thumbnail in Preview, Change via Panel, Save
  // -----------------------------------------------------------------
  console.log('\n--- 2. PHOTO EDIT & RESTORE ---');
  const imgClick = await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const doc = iframe.contentDocument;
    // Find the first service card image
    const img = Array.from(doc.querySelectorAll('.elementor-widget-image img')).find(i => i.src.includes('thumb-cantonese'));
    if (!img) return 'no_img_found';
    img.click();
    return 'clicked_canto_img';
  })()`);
  console.log('Clicked Cantonese image:', imgClick);

  await new Promise(r => setTimeout(r, 1500));

  const imgChange = await evalExpr(ws, `(() => {
    const sel = window.elementor?.selection?.getElements()?.[0];
    if (!sel) return 'no_selection';
    sel.settings.set('image', {
      url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/church-sign.jpg',
      id: 9
    });
    window.$e.run('document/save/default');
    return 'image_swapped_and_saved';
  })()`);
  console.log('Image swap result:', imgChange);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t2_edit_pass = liveHtml.includes('church-sign.jpg');
  console.log('2. Live site shows new photo (church-sign.jpg):', t2_edit_pass ? 'PASSED' : 'FAILED');

  // Restore image
  console.log('Restoring original photo...');
  await evalExpr(ws, `(() => {
    const sel = window.elementor?.selection?.getElements()?.[0];
    if (sel) {
      sel.settings.set('image', {
        url: 'http://mpcbc-2.local/wp-content/uploads/2026/10/thumb-cantonese.png',
        id: 14
      });
      window.$e.run('document/save/default');
    }
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  liveHtml = await fetch('http://mpcbc-2.local/').then(r => r.text());
  const t2_restore_pass = liveHtml.includes('thumb-cantonese.png');
  console.log('2. Live site restored original photo:', t2_restore_pass ? 'PASSED' : 'FAILED');

  // -----------------------------------------------------------------
  // 3. MENU ITEM: Edit in Appearance > Menus
  // -----------------------------------------------------------------
  console.log('\n--- 3. MENU ITEM EDIT & RESTORE ---');
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

  // Restore menu item
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
  console.log('3. Live site restored menu item to "Newsletter":', t3_restore_pass ? 'PASSED' : 'FAILED');

  // -----------------------------------------------------------------
  // 4. PRIMARY GLOBAL COLOUR: Edit via Site Settings UI
  // -----------------------------------------------------------------
  console.log('\n--- 4. PRIMARY GLOBAL COLOUR EDIT & RESTORE ---');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/wp-admin/post.php?post=23&action=elementor' });
  for (let i = 0; i < 30; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-panel') && !!window.$e`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 2000));

  console.log('Opening Site Settings -> Global Colors via Elementor UI...');
  const openKitRes = await evalExpr(ws, `(() => {
    // Open hamburger menu
    const menuBtn = document.querySelector('#elementor-panel-header-menu-button');
    if (menuBtn) menuBtn.click();
    return 'menu_clicked';
  })()`);
  console.log('Opened panel menu:', openKitRes);
  await new Promise(r => setTimeout(r, 1000));

  // Click Site Settings item
  await evalExpr(ws, `(() => {
    const siteSettingsItem = Array.from(document.querySelectorAll('.elementor-panel-menu-item')).find(el => el.innerText.includes('Site Settings'));
    if (siteSettingsItem) siteSettingsItem.click();
  })()`);
  await new Promise(r => setTimeout(r, 2000));

  // Change Primary global color
  console.log('Updating Primary global color to #0E6B5C...');
  await evalExpr(ws, `(() => {
    const page = window.elementor?.settings?.page?.model;
    if (page) {
      const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
      sysColors[0].color = '#0E6B5C';
      page.set('system_colors', sysColors);
      window.$e.run('document/save/default');
    }
  })()`);

  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));
  let kitCss = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
  const t4_edit_pass = kitCss.includes('#0E6B5C');
  console.log('4. Live Kit CSS reflects Primary #0E6B5C:', t4_edit_pass ? 'PASSED' : 'FAILED');

  // Restore Primary global color
  console.log('Restoring Primary global color to #943759...');
  await evalExpr(ws, `(() => {
    const page = window.elementor?.settings?.page?.model;
    if (page) {
      const sysColors = JSON.parse(JSON.stringify(page.get('system_colors')));
      sysColors[0].color = '#943759';
      page.set('system_colors', sysColors);
      window.$e.run('document/save/default');
    }
  })()`);
  await new Promise(r => setTimeout(r, 5000));
  kitCss = await fetch('http://mpcbc-2.local/wp-content/uploads/elementor/css/post-6.css').then(r => r.text()).catch(() => '');
  const t4_restore_pass = kitCss.includes('#943759');
  console.log('4. Live Kit CSS restored Primary to #943759:', t4_restore_pass ? 'PASSED' : 'FAILED');

  console.log('\n========================================');
  console.log('=== SUMMARY OF VOLUNTEER UI TESTS ===');
  console.log('1. Headline Edit & Restore:         ' + (t1_edit_pass && t1_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('2. Photo Edit & Restore:            ' + (t2_edit_pass && t2_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('3. Menu Item Edit & Restore:        ' + (t3_edit_pass && t3_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('4. Primary Global Colour & Restore: ' + (t4_edit_pass && t4_restore_pass ? 'PASSED' : 'FAILED'));
  console.log('========================================\n');

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error('Error during test:', err);
  proc.kill();
  process.exit(1);
});
