import http from 'node:http';
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
    '--remote-debugging-port=9227',
    '--window-size=1440,900',
    'about:blank'
  ]);
  await new Promise(r => setTimeout(r, 2000));

  try {
    const targets = await new Promise((resolve) => {
      http.get('http://127.0.0.1:9227/json', (r) => {
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

    const res = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const h1 = document.querySelector('h1');
        const planA = Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Plan your visit') && a.closest('section, div[data-element_type="container"]'));
        const watchA = Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Watch live'));
        const p = document.querySelector('p');
        return {
          h1: h1 ? { left: h1.getBoundingClientRect().left, text: h1.innerText, cls: h1.className, parentCls: h1.parentElement.className } : null,
          p: p ? { left: p.getBoundingClientRect().left, text: p.innerText } : null,
          planA: planA ? { left: planA.getBoundingClientRect().left, text: planA.innerText, parentCls: planA.parentElement.className, containerCls: planA.closest('.e-con')?.className } : null,
          watchA: watchA ? { left: watchA.getBoundingClientRect().left, text: watchA.innerText } : null,
        };
      })()`,
      returnByValue: true
    });
    console.log('Hero positions:', JSON.stringify(res.result.value, null, 2));

    ws.close();
  } catch (err) {
    console.error('Error:', err);
  } finally {
    proc.kill();
  }
}

run();
