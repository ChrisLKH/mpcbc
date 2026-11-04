import http from 'node:http';
import { spawn } from 'node:child_process';

async function testWidth(width, height) {
  const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--remote-debugging-port=9222',
    `--window-size=${width},${height}`,
    'http://mpcbc.local/'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 3500));

  return new Promise((resolve, reject) => {
    http.get('http://127.0.0.1:9222/json', (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        const targets = JSON.parse(data);
        const pageTarget = targets.find(t => t.type === 'page' && t.url.includes('mpcbc.local')) || targets[0];
        const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);

        ws.onopen = () => {
          const expr = `(() => {
            const checkEl = (sel) => {
              const el = document.querySelector(sel);
              if (!el) return 'NOT_FOUND';
              const inner = el.querySelector(':scope > .e-con-inner');
              const target = inner || el;
              return {
                flexDir: window.getComputedStyle(target).flexDirection,
                width: Math.round(parseFloat(window.getComputedStyle(target).width)) + 'px',
              };
            };

            return {
              times_card: checkEl('.elementor-element-times_card'),
              times_col_header: checkEl('.elementor-element-times_col_header'),
              times_col_cantonese: checkEl('.elementor-element-times_col_cantonese'),
              find_place_cards_grid: checkEl('.elementor-element-find_place_cards_grid'),
              card_english: checkEl('.elementor-element-card_english'),
              kids_inner: checkEl('.elementor-element-kids_inner'),
              pastor_root: checkEl('.elementor-element-pastor_root'),
              events_messages_inner: checkEl('.elementor-element-events_messages_inner'),
              stay_loop_root: checkEl('.elementor-element-stay_loop_root'),
            };
          })()`;

          ws.send(JSON.stringify({
            id: 1,
            method: 'Runtime.evaluate',
            params: { expression: expr, returnByValue: true }
          }));
        };

        ws.onmessage = (event) => {
          const response = JSON.parse(event.data);
          ws.close();
          proc.kill();
          resolve(response.result?.result?.value);
        };
      });
    }).on('error', err => {
      proc.kill();
      reject(err);
    });
  });
}

async function run() {
  console.log('=== TABLET (768px) ===');
  const tab = await testWidth(768, 1024);
  console.log(JSON.stringify(tab, null, 2));

  // wait 1s
  await new Promise(r => setTimeout(r, 1000));

  console.log('=== MOBILE (390px) ===');
  const mob = await testWidth(390, 844);
  console.log(JSON.stringify(mob, null, 2));
}

run();
