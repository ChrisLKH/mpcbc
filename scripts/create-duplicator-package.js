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
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    '--remote-debugging-port=9223',
    '--window-size=1440,900',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2000));

  try {
    const targets = await new Promise((resolve) => {
      http.get('http://127.0.0.1:9223/json', (r) => {
        let d = '';
        r.on('data', c => d += c);
        r.on('end', () => resolve(JSON.parse(d)));
      });
    });

    const pageTarget = targets.find(t => t.type === 'page');
    const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
    await new Promise(r => ws.addEventListener('open', r));

    await sendCmd(ws, 'Page.enable');
    await sendCmd(ws, 'Runtime.enable');

    console.log('Navigating to login...');
    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-login.php' });
    await new Promise(r => setTimeout(r, 2000));

    // Fill login
    await sendCmd(ws, 'Runtime.evaluate', {
      expression: `
        document.getElementById('user_login').value = 'mpcbc';
        document.getElementById('user_pass').value = 'mpcbc';
        document.getElementById('wp-submit').click();
      `
    });
    await new Promise(r => setTimeout(r, 3000));

    console.log('Navigating to Duplicator...');
    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-admin/admin.php?page=duplicator' });
    await new Promise(r => setTimeout(r, 3000));

    let pageInfo = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `({ title: document.title, url: window.location.href, body: document.body.innerText.substring(0, 300) })`,
      returnByValue: true
    });
    console.log('Duplicator page:', pageInfo.result.value);

    // Click Create New / Next
    const clickRes = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const createBtn = document.querySelector('a[href*="action=package_new"]') || 
                          document.querySelector('.dup-button-primary') ||
                          Array.from(document.querySelectorAll('a, button')).find(el => el.innerText.includes('Create New'));
        if (createBtn) {
          createBtn.click();
          return 'Clicked ' + createBtn.innerText;
        }
        return 'Not found';
      })()`,
      returnByValue: true
    });
    console.log('Click result:', clickRes.result.value);

    await new Promise(r => setTimeout(r, 4000));

    // Now on Step 1: click Next / Scan
    const nextRes = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const nextBtn = document.querySelector('#dup-next-btn') || 
                        Array.from(document.querySelectorAll('button, input[type="button"], a')).find(el => el.innerText.includes('Next') || el.value === 'Next');
        if (nextBtn) {
          nextBtn.click();
          return 'Clicked Next: ' + (nextBtn.innerText || nextBtn.value);
        }
        return 'Next button not found';
      })()`,
      returnByValue: true
    });
    console.log('Next result:', nextRes.result.value);

    // Wait for scan to complete (can take 10-20 seconds)
    console.log('Waiting for scan...');
    for (let i = 0; i < 30; i++) {
      await new Promise(r => setTimeout(r, 2000));
      const scanStatus = await sendCmd(ws, 'Runtime.evaluate', {
        expression: `(() => {
          const buildBtn = document.querySelector('#dup-build-btn') || 
                           Array.from(document.querySelectorAll('button, input[type="button"]')).find(el => (el.innerText || el.value || '').includes('Build'));
          return {
            hasBuild: !!buildBtn,
            text: buildBtn ? (buildBtn.innerText || buildBtn.value) : '',
            statusText: document.querySelector('.dup-scan-status, .scan-completed')?.innerText || ''
          };
        })()`,
        returnByValue: true
      });
      if (scanStatus.result.value.hasBuild) {
        console.log('Scan completed! Clicking Build...');
        await sendCmd(ws, 'Runtime.evaluate', {
          expression: `(() => {
            const buildBtn = document.querySelector('#dup-build-btn') || 
                             Array.from(document.querySelectorAll('button, input[type="button"]')).find(el => (el.innerText || el.value || '').includes('Build'));
            if (buildBtn) buildBtn.click();
          })()`
        });
        break;
      }
    }

    // Wait for build to complete
    console.log('Waiting for build to complete...');
    for (let i = 0; i < 60; i++) {
      await new Promise(r => setTimeout(r, 3000));
      const buildStatus = await sendCmd(ws, 'Runtime.evaluate', {
        expression: `(() => {
          const text = document.body.innerText;
          const isComplete = text.includes('Package Completed') || text.includes('Download') || text.includes('Installer');
          const percent = document.querySelector('.dup-progress-percent')?.innerText || '';
          return { isComplete, percent, snippet: text.substring(0, 200) };
        })()`,
        returnByValue: true
      });
      console.log(`[${i * 3}s] Build status:`, buildStatus.result.value.percent || 'in progress...');
      if (buildStatus.result.value.isComplete) {
        console.log('Package Build Finished!');
        break;
      }
    }

    ws.close();
  } catch (err) {
    console.error('Error:', err);
  } finally {
    proc.kill();
  }
}

run();
