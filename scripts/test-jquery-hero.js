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

  console.log('Opening Elementor editor...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });

  for (let i = 0; i < 25; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `(() => {
      const iframe = document.querySelector('#elementor-preview-iframe');
      return !!iframe?.contentDocument?.querySelector('.elementor-element-hero_heading');
    })()`);
    if (ready) {
      console.log('Editor ready at poll', i + 1);
      break;
    }
  }

  // Click hero heading in iframe
  const res = await evalExpr(ws, `(() => {
    const iframe = document.querySelector('#elementor-preview-iframe');
    const hero = iframe.contentDocument.querySelector('.elementor-element-hero_heading');
    hero.click();

    // Check jQuery in window
    const $ = window.jQuery;
    const titleArea = $('textarea[data-setting="title"]');
    
    // Set value and trigger input via jQuery
    titleArea.val('All are welcome here.').trigger('input').trigger('change');
    
    // Check if saver button has changed state or class
    const saveBtn = $('#elementor-panel-saver-button-publish');
    const isSaveDisabled = saveBtn.hasClass('elementor-disabled') || saveBtn.prop('disabled');

    // Run document save via $e or save button
    $e.run('document/save/default');

    return {
      titleVal: titleArea.val(),
      isSaveDisabled: isSaveDisabled,
      hasE: !!window.$e,
    };
  })()`);

  console.log('Save triggered result:', res);
  console.log('Waiting 5s for save...');
  await new Promise(r => setTimeout(r, 5000));

  const live = await fetch('http://mpcbc.local/').then(r => r.text());
  console.log('Live site shows "All are welcome here.":', live.includes('All are welcome here.') ? 'PASSED' : 'FAILED');

  if (live.includes('All are welcome here.')) {
    // Restore
    console.log('Restoring to "You’re welcome here."...');
    await evalExpr(ws, `(() => {
      const $ = window.jQuery;
      $('textarea[data-setting="title"]').val('You’re welcome here.').trigger('input').trigger('change');
      $e.run('document/save/default');
    })()`);
    await new Promise(r => setTimeout(r, 5000));
    const liveRestored = await fetch('http://mpcbc.local/').then(r => r.text());
    console.log('Live site restored:', liveRestored.includes('You’re welcome here.') && !liveRestored.includes('All are welcome here.') ? 'PASSED' : 'FAILED');
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
