import http from 'node:http';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
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

async function runTests() {
  const port = 9355;
  const tempProfile = path.join(os.tmpdir(), 'chrome-verify-' + Date.now());
  const args = [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    `--user-data-dir=${tempProfile}`,
    `--remote-debugging-port=${port}`,
    'about:blank'
  ];

  const proc = spawn(chromePath, args);
  await new Promise(r => setTimeout(r, 2500));

  const res = await new Promise((resolve, reject) => {
    http.get(`http://127.0.0.1:${port}/json`, (r) => {
      let d = '';
      r.on('data', c => d += c);
      r.on('end', () => resolve(JSON.parse(d)));
    }).on('error', reject);
  });

  const pageTarget = res.find(t => t.type === 'page') || res[0];
  const ws = new WebSocket(pageTarget.webSocketDebuggerUrl);
  await new Promise(r => ws.onopen = r);

  await sendCmd(ws, 'Page.enable');
  await sendCmd(ws, 'Runtime.enable');
  await sendCmd(ws, 'Network.enable');
  await sendCmd(ws, 'Network.clearBrowserCache');
  await sendCmd(ws, 'Network.setCacheDisabled', { cacheDisabled: true });

  const results = {};

  // 1. Mobile 390px scrollHeight and 360px overflow
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 390,
    height: 844,
    deviceScaleFactor: 1,
    mobile: true
  });
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2500));

  const h390 = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `document.documentElement.scrollHeight`,
    returnByValue: true
  });
  results.mobileHeight390 = h390.result.value;

  // 360px overflow
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 360,
    height: 800,
    deviceScaleFactor: 1,
    mobile: true
  });
  await new Promise(r => setTimeout(r, 500));
  const overflow360 = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `document.documentElement.scrollWidth <= 360 && document.body.scrollWidth <= 360`,
    returnByValue: true
  });
  results.noOverflow360 = overflow360.result.value;

  // 2. Hero fills screen at 1920x1080, 1440x900, 768x1024, 390x844
  const viewports = [
    { w: 1920, h: 1080, m: false },
    { w: 1440, h: 900, m: false },
    { w: 768, h: 1024, m: false },
    { w: 390, h: 844, m: true }
  ];
  results.heroFillsScreen = {};
  for (const vp of viewports) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: vp.w,
      height: vp.h,
      deviceScaleFactor: 1,
      mobile: vp.m
    });
    await new Promise(r => setTimeout(r, 400));
    const check = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const hero = document.getElementById('top');
        const rect = hero.getBoundingClientRect();
        return {
          heroHeight: rect.height,
          windowHeight: window.innerHeight,
          fitsOrExceeds: rect.height >= window.innerHeight,
          topAtZero: Math.round(rect.top) === 0
        };
      })()`,
      returnByValue: true
    });
    results.heroFillsScreen[`${vp.w}x${vp.h}`] = check.result.value;
  }

  // 3. Give button contrast at top and after scrolling
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false
  });
  await sendCmd(ws, 'Runtime.evaluate', { expression: `window.scrollTo(0, 0)` });
  await new Promise(r => setTimeout(r, 300));
  const giveTop = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const btn = document.querySelector('.site-header a[href*="#give"]');
      const cs = window.getComputedStyle(btn);
      return { color: cs.color, bg: cs.backgroundColor };
    })()`,
    returnByValue: true
  });
  results.giveTop = giveTop.result.value;

  await sendCmd(ws, 'Runtime.evaluate', { expression: `window.scrollTo(0, 500)` });
  await new Promise(r => setTimeout(r, 500));
  const giveScrolled = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const btn = document.querySelector('.site-header a[href*="#give"]');
      const cs = window.getComputedStyle(btn);
      return { color: cs.color, bg: cs.backgroundColor };
    })()`,
    returnByValue: true
  });
  results.giveScrolled = giveScrolled.result.value;

  // 4. Footer contrast & logo
  const footerTest = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const phoneLink = document.querySelector('footer a[href^="tel:"]');
      const emailLink = document.querySelector('footer a[href^="mailto:"]');
      const label = document.querySelector('footer .ff-el-input-title');
      const logo = document.querySelector('footer .elementor-widget-image');
      return {
        phoneColor: phoneLink ? window.getComputedStyle(phoneLink).color : null,
        emailColor: emailLink ? window.getComputedStyle(emailLink).color : null,
        labelColor: label ? window.getComputedStyle(label).color : null,
        logoAlign: logo ? window.getComputedStyle(logo).textAlign : null,
      };
    })()`,
    returnByValue: true
  });
  results.footer = footerTest.result.value;

  // 5. Connect card dropdown
  const connectCardOptions = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const select = document.querySelector('form#fluentform_3 select[name="service"]');
      if (!select) return null;
      return {
        optionCount: select.options.length,
        placeholderText: select.options[0]?.text,
        placeholderValue: select.options[0]?.value,
        options: Array.from(select.options).map(o => ({ text: o.text, value: o.value }))
      };
    })()`,
    returnByValue: true
  });
  results.connectCard = connectCardOptions.result.value;

  // 6. Event titles x-coordinate at 1440 and 768
  results.eventAlignment = {};
  for (const w of [1440, 768]) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: w,
      height: 900,
      deviceScaleFactor: 1,
      mobile: false
    });
    await new Promise(r => setTimeout(r, 400));
    const evRows = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const titles = [];
        document.querySelectorAll('#events .elementor-widget-text-editor').forEach(el => {
          if (el.innerHTML.includes('Friday fellowship') || el.innerHTML.includes('Church picnic') || el.innerHTML.includes('Small groups') || el.innerHTML.includes('Children’s teacher') || el.innerHTML.includes('50th Anniversary')) {
            const rect = el.getBoundingClientRect();
            titles.push({ text: el.textContent.split('\\n')[0].slice(0, 20), x: Math.round(rect.left) });
          }
        });
        return titles;
      })()`,
      returnByValue: true
    });
    results.eventAlignment[w] = evRows.result.value;
  }

  // 7. Announcement label wrapping at 1440 and 768
  results.announcementLabel = {};
  for (const w of [1440, 768]) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: w,
      height: 900,
      deviceScaleFactor: 1,
      mobile: false
    });
    await new Promise(r => setTimeout(r, 400));
    const labelCheck = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const kicker = document.querySelector('.mp-slider .elementor-element-b52e3e4d p') || document.querySelector('.mp-slider [class*="kicker"] p');
        if (!kicker) return null;
        const rect = kicker.getBoundingClientRect();
        const cs = window.getComputedStyle(kicker);
        const lh = parseFloat(cs.lineHeight) || 20;
        return {
          height: rect.height,
          lineHeight: lh,
          singleLine: rect.height <= (lh * 1.5),
          text: kicker.textContent.trim()
        };
      })()`,
      returnByValue: true
    });
    results.announcementLabel[w] = labelCheck.result.value;
  }

  // 8. Alt text of images
  const altCheck = await sendCmd(ws, 'Runtime.evaluate', {
    expression: `(() => {
      const imgs = Array.from(document.querySelectorAll('img')).map(img => {
        const src = img.src.split('/').pop().split('?')[0];
        return { src, alt: img.alt };
      });
      return imgs;
    })()`,
    returnByValue: true
  });
  // 9. Wide screen footer edge-to-edge (1920px & 2560px)
  results.footerWideScreen = {};
  for (const w of [1920, 2560]) {
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width: w,
      height: 1080,
      deviceScaleFactor: 1,
      mobile: false
    });
    await new Promise(r => setTimeout(r, 400));
    const wideCheck = await sendCmd(ws, 'Runtime.evaluate', {
      expression: `(() => {
        const footer = document.querySelector('#colophon') || document.querySelector('footer');
        const footerContent = footer ? footer.querySelector('.elementor-element-59a8de09') : null;
        if (!footer) return null;
        const rect = footer.getBoundingClientRect();
        const fContentRect = footerContent ? footerContent.getBoundingClientRect() : null;
        const clientW = document.documentElement.clientWidth;
        const expectedCenterLeft = Math.round((clientW - 1080) / 2);
        return {
          windowWidth: window.innerWidth,
          clientWidth: clientW,
          footerWidth: Math.round(rect.width),
          footerLeft: Math.round(rect.left),
          footerRight: Math.round(rect.right),
          isEdgeToEdge: Math.round(rect.left) === 0 && Math.round(rect.width) === clientW,
          footerContentLeft: fContentRect ? Math.round(fContentRect.left) : null,
          footerContentWidth: fContentRect ? Math.round(fContentRect.width) : null,
          expectedCenterLeft,
          contentAligned: fContentRect ? Math.round(fContentRect.left) === expectedCenterLeft : true
        };
      })()`,
      returnByValue: true
    });
    results.footerWideScreen[w] = wideCheck.result.value;
  }

  console.log('--- TEST RESULTS ---');
  console.log(JSON.stringify(results, null, 2));

  ws.close();
  proc.kill();
}

runTests().catch(err => {
  console.error(err);
  process.exit(1);
});
