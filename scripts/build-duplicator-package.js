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
    '--remote-debugging-port=9228',
    '--window-size=1440,900',
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
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
    await sendCmd(ws, 'Runtime.enable');

    console.log('Logging in...');
    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc.local/wp-login.php' });
    await new Promise(r => setTimeout(r, 2000));

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

    console.log('Clicking #dupli-create-new (Add New)...');
    await sendCmd(ws, 'Runtime.evaluate', {
      expression: `document.getElementById('dupli-create-new').click()`
    });
    await new Promise(r => setTimeout(r, 4000));

    console.log('Clicking #button-next (Next ▶)...');
    await sendCmd(ws, 'Runtime.evaluate', {
      expression: `document.getElementById('button-next').click()`
    });

    // Wait for scan to complete (Step 2: Scan)
    console.log('Waiting for scan to complete...');
    let scanDone = false;
    for (let i = 0; i < 40; i++) {
      await new Promise(r => setTimeout(r, 3000));
      try {
        const scanCheck = await sendCmd(ws, 'Runtime.evaluate', {
          expression: `(() => {
            const buildBtn = document.querySelector('#dup-build-button, #dup-build-btn, input[value*="Build"], button[id*="build"]');
            return {
              hasBuild: !!buildBtn,
              buildText: buildBtn ? (buildBtn.innerText || buildBtn.value) : '',
              buildId: buildBtn ? buildBtn.id : ''
            };
          })()`,
          returnByValue: true
        });
        const val = scanCheck?.result?.value;
        if (val?.hasBuild) {
          scanDone = true;
          console.log('Scan complete! Preparing to click Build...');

          // Check if there is a warning checkbox to check
          await sendCmd(ws, 'Runtime.evaluate', {
            expression: `(() => {
              const warningCheckbox = document.querySelector('input[type="checkbox"][name*="warn"], input[type="checkbox"][id*="warn"]');
              if (warningCheckbox && !warningCheckbox.checked) {
                warningCheckbox.click();
              }
            })()`
          });
          await new Promise(r => setTimeout(r, 1000));

          // Click the build button
          const clicked = await sendCmd(ws, 'Runtime.evaluate', {
            expression: `(() => {
              const buildBtn = document.getElementById('dup-build-button') ||
                               document.querySelector('#dup-build-btn, input[value*="Build"]');
              if (buildBtn) {
                buildBtn.click();
                return 'Clicked ' + (buildBtn.id || buildBtn.value);
              }
              return 'Could not click build button';
            })()`,
            returnByValue: true
          });
          console.log('Build click result:', clicked?.result?.value);
          break;
        }
      } catch (e) {
        console.log('Waiting during scan: ', e.message);
      }
    }

    if (!scanDone) {
      console.error('Scan timed out or Build button not found.');
      ws.close();
      return;
    }

    // Wait 5 seconds for page reload / ajax build kickoff
    console.log('Waiting for build to kick off...');
    await new Promise(r => setTimeout(r, 5000));

    // Wait for package build to finish
    console.log('Waiting for build to finish...');
    for (let i = 0; i < 120; i++) {
      await new Promise(r => setTimeout(r, 5000));
      try {
        const buildStatus = await sendCmd(ws, 'Runtime.evaluate', {
          expression: `(() => {
            const text = document.body ? document.body.innerText : '';
            const complete = text.includes('Package Completed') || text.includes('Download Both Files') || (text.includes('Download') && text.includes('Archive'));
            const percent = document.querySelector('.dup-progress-percent, .dup-pro-percent')?.innerText || '';
            const links = Array.from(document.querySelectorAll('a')).filter(a => a.innerText.includes('Archive') || a.innerText.includes('Installer') || a.innerText.includes('Download')).map(a => ({ text: a.innerText.trim(), href: a.href }));
            return { complete, percent, links, snippet: text.substring(0, 100).replace(/\\s+/g, ' ') };
          })()`,
          returnByValue: true
        });
        const val = buildStatus?.result?.value;
        if (val) {
          console.log(`[${(i+1)*5}s] Build progress:`, val.percent || (val.complete ? 'COMPLETE' : val.snippet));
          if (val.complete) {
            console.log('Package build SUCCESSFUL!');
            console.log('Links:', JSON.stringify(val.links, null, 2));
            break;
          }
        } else {
          console.log(`[${(i+1)*5}s] In progress...`);
        }
      } catch (err) {
        console.log(`[${(i+1)*5}s] (evaluating):`, err.message);
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
