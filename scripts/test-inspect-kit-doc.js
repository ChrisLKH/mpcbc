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
    document.querySelector('#rememberme').checked = true;
    document.querySelector('#wp-submit').click();
  })()`);

  for (let i = 0; i < 20; i++) {
    await new Promise(r => setTimeout(r, 500));
    const url = await evalExpr(ws, `window.location.href`);
    if (url && url.includes('wp-admin')) {
      console.log('Login confirmed, now at:', url);
      break;
    }
  }

  console.log('Opening Home (post 21) in Elementor editor...');
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/post.php?post=21&action=elementor' });

  for (let i = 0; i < 30; i++) {
    await new Promise(r => setTimeout(r, 1000));
    const ready = await evalExpr(ws, `!!document.querySelector('#elementor-panel') && !!window.$e && !document.querySelector('.elementor-templates-modal')`);
    if (ready) break;
  }
  await new Promise(r => setTimeout(r, 4000));


  const headerHtml = await evalExpr(ws, `(() => {
    const header = document.querySelector('#elementor-panel-header');
    return header ? header.outerHTML : 'no header';
  })()`);
  console.log('Header HTML:', headerHtml);

  const panelInspect = await evalExpr(ws, `(() => {
    const p = window.elementor.panel;
    return {
      keys: Object.keys(p || {}),
      currentView: p?.currentView ? Object.keys(p.currentView) : [],
      setPage: typeof p?.setPage,
      openSettings: typeof p?.openSettings
    };
  })()`);
  console.log('Panel Inspect:', JSON.stringify(panelInspect, null, 2));

  // Try p.setPage('menu') or p.openSettings()
  const trySetPage = await evalExpr(ws, `(() => {
    try {
      if (window.elementor.panel.setPage) {
        window.elementor.panel.setPage('menu');
        return 'called setPage(menu)';
      }
    } catch(e) {
      return e.message;
    }
  })()`);
  console.log('Try setPage:', trySetPage);
  await new Promise(r => setTimeout(r, 1000));
  const afterSetPage = await evalExpr(ws, `(() => {
    return Array.from(document.querySelectorAll('.elementor-panel-menu-item, .elementor-panel-box, a, li, button')).map(el => el.innerText?.trim()).filter(Boolean).slice(0, 20);
  })()`);
  console.log('After setPage elements:', afterSetPage);




  await new Promise(r => setTimeout(r, 2000));

  const afterTo = await evalExpr(ws, `(() => {
    return Array.from(document.querySelectorAll('#elementor-panel *'))
      .map(el => ({ tag: el.tagName, text: el.innerText?.trim()?.slice(0, 40), class: el.className }))
      .filter(x => x.text && x.text.length > 0)
      .slice(0, 25);
  })()`);
  console.log('After to route:', JSON.stringify(afterTo, null, 2));











  // done

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  proc.kill();
  process.exit(1);
});
