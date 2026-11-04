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

async function run() {
  await new Promise(r => setTimeout(r, 4000));

  http.get('http://127.0.0.1:9222/json', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', async () => {
      const targets = JSON.parse(data);
      const pageTarget = targets.find(t => t.type === 'page' && t.url.includes('mpcbc.local')) || targets[0];
      const wsUrl = pageTarget.webSocketDebuggerUrl;
      const ws = new WebSocket(wsUrl);

      ws.onopen = () => {
        const expr = `(() => {
          const checkEl = (sel) => {
            const el = document.querySelector(sel);
            if (!el) return 'NOT_FOUND';
            const inner = el.querySelector(':scope > .e-con-inner');
            const target = inner || el;
            return {
              hasInner: !!inner,
              flexDir: window.getComputedStyle(target).flexDirection,
              width: window.getComputedStyle(target).width,
            };
          };

          return {
            hero_actions_wrap: checkEl('.elementor-element-hero_actions_wrap'),
            times_card: checkEl('.elementor-element-times_card'),
            find_place_cards_grid: checkEl('.elementor-element-find_place_cards_grid'),
            card_english: checkEl('.elementor-element-card_english'),
            chinese_band_inner: checkEl('.elementor-element-chinese_band_inner'),
            kids_inner: checkEl('.elementor-element-kids_inner'),
            pastor_root: checkEl('.elementor-element-pastor_root'),
            events_messages_inner: checkEl('.elementor-element-events_messages_inner'),
            stay_loop_root: checkEl('.elementor-element-stay_loop_root')
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
        console.log('=== COMPLETE COMPUTED STYLES AT 1440px ===');
        console.log(JSON.stringify(response.result?.result?.value, null, 2));
        ws.close();
        proc.kill();
        process.exit(0);
      };
    });
  });
}

run();
