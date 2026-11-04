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
  // Wait 4 seconds for Chrome to start and load page
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
          const getStyle = (sel) => {
            const el = document.querySelector(sel);
            if (!el) return 'NOT_FOUND: ' + sel;
            return {
              display: window.getComputedStyle(el).display,
              flexDirection: window.getComputedStyle(el).flexDirection,
              width: window.getComputedStyle(el).width,
              fontSize: window.getComputedStyle(el).fontSize,
            };
          };

          return {
            times_card: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-times_card')).flexDirection,
              width: window.getComputedStyle(document.querySelector('.elementor-element-times_card')).width,
            },
            times_col_header: {
              width: window.getComputedStyle(document.querySelector('.elementor-element-times_col_header')).width,
            },
            times_col_cantonese: {
              width: window.getComputedStyle(document.querySelector('.elementor-element-times_col_cantonese')).width,
            },
            find_place_cards_grid: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-find_place_cards_grid')).flexDirection,
              wrap: window.getComputedStyle(document.querySelector('.elementor-element-find_place_cards_grid')).flexWrap,
              width: window.getComputedStyle(document.querySelector('.elementor-element-find_place_cards_grid')).width,
            },
            card_english: {
              width: window.getComputedStyle(document.querySelector('.elementor-element-card_english')).width,
              h3_fontSize: window.getComputedStyle(document.querySelector('.elementor-element-card_english h3')).fontSize,
            },
            card_cantonese: {
              width: window.getComputedStyle(document.querySelector('.elementor-element-card_cantonese')).width,
            },
            chinese_band_inner: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-chinese_band_inner')).flexDirection,
            },
            kids_inner: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-kids_inner')).flexDirection,
            },
            pastor_root: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-pastor_root')).flexDirection,
            },
            events_messages_inner: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-events_messages_inner')).flexDirection,
            },
            stay_loop_root: {
              dir: window.getComputedStyle(document.querySelector('.elementor-element-stay_loop_root')).flexDirection,
            }
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
        console.log('=== COMPUTED STYLES AT 1440px ===');
        const resVal = response.result?.result?.value;
        if (resVal) {
          console.log(JSON.stringify(resVal, null, 2));
        } else {
          console.log('Error evaluating:', JSON.stringify(response, null, 2));
        }
        ws.close();
        proc.kill();
        process.exit(0);
      };
    });
  }).on('error', err => {
    console.error(err);
    proc.kill();
    process.exit(1);
  });
}

run();
