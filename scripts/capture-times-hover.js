import http from 'node:http';
import fs from 'node:fs';
import { spawn } from 'node:child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

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

async function run() {
  const proc = spawn(chromePath, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--remote-debugging-port=9228',
    '--window-size=1440,1100',
    'about:blank'
  ]);
  await new Promise(r => setTimeout(r, 2000));

  try {
    const targets = await new Promise((resolve) => {
      http.get('http://127.0.0.1:9228/json', (r) => {
        let d = '';
        r.on('data', c => d += c);
        r.on('end', () => resolve(JSON.parse(d)));
      });
    });

    const pageTarget = targets.find(t => t.type === 'page');
    const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
    await new Promise(r => ws.addEventListener('open', r));

    await sendCmd(ws, 'Page.enable');
    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/' });
    await new Promise(r => setTimeout(r, 3000));

    // Find the English container coordinates
    const rectRes = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const el = document.querySelector('.elementor-element-times_col_english');
        if (!el) return null;
        const rect = el.getBoundingClientRect();
        return {
          x: rect.left + rect.width / 2,
          y: rect.top + rect.height / 2,
          rect: { left: rect.left, top: rect.top, width: rect.width, height: rect.height }
        };
      })()`,
      returnByValue: true
    });

    console.log('English container rect:', rectRes.result.value);
    const { x, y } = rectRes.result.value;

    // Dispatch mouse move to hover English container
    await sendCmd(ws, 'Input.dispatchMouseEvent', {
      type: 'mouseMoved',
      x: Math.round(x),
      y: Math.round(y)
    });

    // Wait 500ms for hover transition
    await new Promise(r => setTimeout(r, 600));

    // Verify computed style
    const styleRes = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const el = document.querySelector('.elementor-element-times_col_english');
        const comp = window.getComputedStyle(el);
        return {
          background: comp.backgroundColor,
          borderRadius: comp.borderRadius,
          transition: comp.transition
        };
      })()`,
      returnByValue: true
    });
    console.log('Hover computed style:', styleRes.result.value);

    // Capture screenshot
    const shot = await sendCmd(ws, 'Page.captureScreenshot', {
      format: 'png',
      clip: {
        x: 0,
        y: 600,
        width: 1440,
        height: 500,
        scale: 1
      }
    });

    fs.mkdirSync('C:/Projects/mpcbc/wordpress/screenshots', { recursive: true });
    fs.writeFileSync('C:/Projects/mpcbc/wordpress/screenshots/times-hover-1440.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved times-hover-1440.png');

    ws.close();
  } catch (err) {
    console.error('Error:', err);
  } finally {
    proc.kill();
  }
}

run();
