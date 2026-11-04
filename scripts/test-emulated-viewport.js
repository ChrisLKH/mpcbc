import http from 'node:http';
import { spawn } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const args = [
  '--headless=new',
  '--no-sandbox',
  '--disable-gpu',
  '--remote-debugging-port=9222',
  '--window-size=1440,900',
  'http://mpcbc.local/'
];

const proc = spawn(chromePath, args);

function sendCmd(ws, method, params = {}) {
  return new Promise((resolve) => {
    const id = Math.floor(Math.random() * 1000000);
    const handler = (evt) => {
      const data = JSON.parse(evt.data);
      if (data.id === id) {
        ws.removeEventListener('message', handler);
        resolve(data.result);
      }
    };
    ws.addEventListener('message', handler);
    ws.send(JSON.stringify({ id, method, params }));
  });
}

async function run() {
  await new Promise(r => setTimeout(r, 3500));

  http.get('http://127.0.0.1:9222/json', async (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', async () => {
      const targets = JSON.parse(data);
      const pageTarget = targets.find(t => t.type === 'page' && t.url.includes('mpcbc.local')) || targets[0];
      const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);

      await new Promise(r => ws.onopen = r);

      const checkAtWidth = async (width, height, isMobile = false) => {
        await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
          width,
          height,
          deviceScaleFactor: 1,
          mobile: isMobile
        });

        // wait 300ms for layout to settle
        await new Promise(r => setTimeout(r, 300));

        const expr = `(() => {
          const checkEl = (sel) => {
            const el = document.querySelector(sel);
            if (!el) return null;
            const inner = el.querySelector(':scope > .e-con-inner');
            const target = inner || el;
            const cs = window.getComputedStyle(target);
            return {
              display: cs.display,
              flexDir: cs.flexDirection,
              width: Math.round(parseFloat(cs.width)) + 'px',
            };
          };

          return {
            window_innerWidth: window.innerWidth,
            times_card: checkEl('.elementor-element-times_card'),
            times_col_header: checkEl('.elementor-element-times_col_header'),
            times_col_cantonese: checkEl('.elementor-element-times_col_cantonese'),
            times_col_links: checkEl('.elementor-element-times_col_links'),
            find_place_cards_grid: checkEl('.elementor-element-find_place_cards_grid'),
            card_english: checkEl('.elementor-element-card_english'),
            card_cantonese: checkEl('.elementor-element-card_cantonese'),
            pastor_root: checkEl('.elementor-element-pastor_root'),
            kids_inner: checkEl('.elementor-element-kids_inner'),
          };
        })()`;

        const res = await sendCmd(ws, 'Runtime.evaluate', { expression: expr, returnByValue: true });
        return res.result.value;
      };

      console.log('--- 1440px DESKTOP ---');
      console.log(JSON.stringify(await checkAtWidth(1440, 900, false), null, 2));

      console.log('--- 768px TABLET ---');
      console.log(JSON.stringify(await checkAtWidth(768, 1024, false), null, 2));

      console.log('--- 390px MOBILE ---');
      console.log(JSON.stringify(await checkAtWidth(390, 844, true), null, 2));

      ws.close();
      proc.kill();
      process.exit(0);
    });
  });
}

run();
