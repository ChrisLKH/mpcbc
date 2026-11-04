import http from 'node:http';
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

async function evalExpr(ws, expr) {
  const res = await sendCmd(ws, 'Runtime.evaluate', {
    expression: expr,
    returnByValue: true,
    awaitPromise: true
  });
  return res?.result?.value;
}

async function run() {
  const port = 9345;
  const tempProfile = path.join(os.tmpdir(), 'chrome-video-' + Date.now());
  const proc = spawn(chromePath, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
    `--user-data-dir=${tempProfile}`,
    `--remote-debugging-port=${port}`,
    '--window-size=1440,900',
    'about:blank'
  ]);
  await new Promise(r => setTimeout(r, 2000));

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
  await sendCmd(ws, 'Emulation.setEmulatedMedia', {
    features: [{ name: 'prefers-reduced-motion', value: '' }]
  });

  const consoleErrors = [];
  ws.addEventListener('message', (evt) => {
    const data = JSON.parse(evt.data);
    if (data.method === 'Runtime.consoleAPICalled' && data.params.type === 'error') {
      consoleErrors.push(data.params.args.map(a => a.value || a.description).join(' '));
    }
  });

  const widths = [1440, 768, 390];

  for (const width of widths) {
    const isMobile = width <= 768;
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width,
      height: 900,
      deviceScaleFactor: 1,
      mobile: isMobile
    });

    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
    await new Promise(r => setTimeout(r, 2500));

    const check = await evalExpr(ws, `(() => {
      const hero = document.querySelector('#top, .hero-section');
      const videoWrap = document.querySelector('.elementor-background-video-container');
      const video = document.querySelector('.elementor-background-video-container video, #top video');
      const btn = document.querySelector('button.mpcbc-video-toggle');

      // What HTML does Elementor output?
      const heroBgHtml = hero ? hero.outerHTML.slice(0, 1000) : null;

      // Video properties
      const videoInfo = video ? {
        src: video.currentSrc || video.src,
        paused: video.paused,
        muted: video.muted,
        loop: video.loop,
        playsInline: video.playsInline,
        currentTime: video.currentTime,
        videoWidth: video.videoWidth,
        videoHeight: video.videoHeight,
      } : null;

      // Button rect & styles
      let btnInfo = null;
      if (btn) {
        const r = btn.getBoundingClientRect();
        const comp = window.getComputedStyle(btn);
        btnInfo = {
          exists: true,
          rect: { top: Math.round(r.top), bottom: Math.round(r.bottom), left: Math.round(r.left), right: Math.round(r.right), width: Math.round(r.width), height: Math.round(r.height) },
          ariaLabel: btn.getAttribute('aria-label'),
          isPaused: btn.classList.contains('is-paused'),
          opacity: comp.opacity,
          borderRadius: comp.borderRadius,
          zIndex: comp.zIndex,
          backgroundColor: comp.backgroundColor,
          color: comp.color
        };
      }

      // Check collision with hero buttons and service-times line
      const actions = hero ? hero.querySelector('.elementor-element-0943648d, [class*="hero_actions"]') : null;
      const heroButtons = hero ? Array.from(hero.querySelectorAll('.elementor-button, .elementor-widget-button, .elementor-widget-text-editor')) : [];
      const buttonRects = heroButtons.map(b => {
        const br = b.getBoundingClientRect();
        return {
          text: (b.innerText || '').slice(0, 30),
          rect: { top: Math.round(br.top), bottom: Math.round(br.bottom), left: Math.round(br.left), right: Math.round(br.right) }
        };
      });

      let touchesTextOrButtons = false;
      let collisionDetails = [];
      if (btn) {
        const bRect = btn.getBoundingClientRect();
        for (const hb of heroButtons) {
          const hr = hb.getBoundingClientRect();
          // check overlap
          const overlap = !(bRect.right < hr.left || bRect.left > hr.right || bRect.bottom < hr.top || bRect.top > hr.bottom);
          if (overlap) {
            touchesTextOrButtons = true;
            collisionDetails.push({ text: hb.innerText, btnRect: bRect, elementRect: hr });
          }
        }
      }

      // Header gap check
      const header = document.querySelector('.site-header');
      const headerRect = header ? header.getBoundingClientRect() : null;
      const heroRect = hero ? hero.getBoundingClientRect() : null;

      // Fallback image in style or element
      const compHero = hero ? window.getComputedStyle(hero) : null;
      const bgImage = compHero ? compHero.backgroundImage : null;

      return {
        videoWrapExists: !!videoWrap,
        videoExists: !!video,
        videoInfo,
        btnExists: !!btn,
        btnInfo,
        touchesTextOrButtons,
        collisionDetails,
        buttonRects,
        headerTop: headerRect ? Math.round(headerRect.top) : null,
        headerHeight: headerRect ? Math.round(headerRect.height) : null,
        heroTop: heroRect ? Math.round(heroRect.top) : null,
        bgImage,
        heroBgHtml: videoWrap ? null : heroBgHtml
      };
    })()`);

    console.log(`\n=============================================================`);
    console.log(`=== CHECK AT ${width}px ===`);
    console.log(`=============================================================`);
    console.log(`Video container exists: ${check.videoWrapExists}`);
    console.log(`Video element exists: ${check.videoExists}`);
    if (check.videoInfo) {
      console.log(`Video source: ${check.videoInfo.src}`);
      console.log(`Video state: muted=${check.videoInfo.muted}, loop=${check.videoInfo.loop}, paused=${check.videoInfo.paused}, currentTime=${check.videoInfo.currentTime}`);
    } else {
      console.log(`Hero HTML sample (first 500 chars): ${check.heroBgHtml ? check.heroBgHtml.slice(0, 500) : 'none'}`);
    }
    console.log(`Fallback bg image: ${check.bgImage}`);
    console.log(`Header top=${check.headerTop}px, height=${check.headerHeight}px, Hero top=${check.heroTop}px (gap=${check.heroTop - check.headerTop}px)`);
    console.log(`Button exists: ${check.btnExists}`);
    if (check.btnInfo) {
      console.log(`Button rect: ${JSON.stringify(check.btnInfo.rect)} (target size: 44x44)`);
      console.log(`Button aria-label: "${check.btnInfo.ariaLabel}"`);
      console.log(`Button touches hero text/buttons: ${check.touchesTextOrButtons}`);
      if (check.touchesTextOrButtons) {
        console.log(`Collisions:`, JSON.stringify(check.collisionDetails));
      }
    }
  }

  // Interactive tests at 1440px
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false
  });
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2000));

  console.log(`\n=============================================================`);
  console.log(`=== INTERACTIVE BUTTON TESTS (1440px) ===`);
  console.log(`=============================================================`);

  // Test click toggle
  const clickTest = await evalExpr(ws, `(async () => {
    const btn = document.querySelector('button.mpcbc-video-toggle');
    const video = document.querySelector('.elementor-background-video-container video');
    if (!btn || !video) return { error: 'btn or video missing' };

    const initialPaused = video.paused;
    const initialLabel = btn.getAttribute('aria-label');

    btn.click();
    await new Promise(r => setTimeout(r, 200));
    const afterClick1Paused = video.paused;
    const afterClick1Label = btn.getAttribute('aria-label');
    const afterClick1Class = btn.classList.contains('is-paused');

    btn.click();
    await new Promise(r => setTimeout(r, 200));
    const afterClick2Paused = video.paused;
    const afterClick2Label = btn.getAttribute('aria-label');
    const afterClick2Class = btn.classList.contains('is-paused');

    return {
      initialPaused,
      initialLabel,
      afterClick1Paused,
      afterClick1Label,
      afterClick1Class,
      afterClick2Paused,
      afterClick2Label,
      afterClick2Class
    };
  })()`);
  console.log(`Click test results:`, JSON.stringify(clickTest, null, 2));

  // Test keyboard focus & activation (Tab, Space, Enter)
  const keyTest = await evalExpr(ws, `(async () => {
    const btn = document.querySelector('button.mpcbc-video-toggle');
    const video = document.querySelector('.elementor-background-video-container video');
    if (!btn || !video) return { error: 'btn or video missing' };

    btn.focus();
    const isFocused = document.activeElement === btn;

    // Simulate keydown Space
    btn.dispatchEvent(new KeyboardEvent('keydown', { key: ' ', code: 'Space', bubbles: true }));
    btn.click();
    await new Promise(r => setTimeout(r, 200));
    const afterSpacePaused = video.paused;
    const afterSpaceLabel = btn.getAttribute('aria-label');

    // Simulate keydown Enter
    btn.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', code: 'Enter', bubbles: true }));
    btn.click();
    await new Promise(r => setTimeout(r, 200));
    const afterEnterPaused = video.paused;
    const afterEnterLabel = btn.getAttribute('aria-label');

    return {
      isFocused,
      afterSpacePaused,
      afterSpaceLabel,
      afterEnterPaused,
      afterEnterLabel
    };
  })()`);
  console.log(`Keyboard test results:`, JSON.stringify(keyTest, null, 2));

  // Test hover style (does hover turn it pink or change size?)
  const hoverTest = await evalExpr(ws, `(() => {
    const btn = document.querySelector('button.mpcbc-video-toggle');
    if (!btn) return { error: 'btn missing' };

    // Find all matching CSS rules for hover
    let hoverRules = [];
    for (const sheet of document.styleSheets) {
      try {
        for (const rule of sheet.cssRules) {
          if (rule.selectorText && rule.selectorText.includes('mpcbc-video-toggle') && rule.selectorText.includes(':hover')) {
            hoverRules.push({
              selector: rule.selectorText,
              bg: rule.style.backgroundColor || rule.style.background,
              color: rule.style.color,
              width: rule.style.width,
              height: rule.style.height
            });
          }
        }
      } catch (e) {}
    }

    return {
      tag: btn.tagName,
      className: btn.className,
      hoverRules
    };
  })()`);
  console.log(`Hover inspection:`, JSON.stringify(hoverTest, null, 2));

  // Reduced motion test
  console.log(`\n=============================================================`);
  console.log(`=== REDUCED MOTION TEST ===`);
  console.log(`=============================================================`);
  await sendCmd(ws, 'Emulation.setEmulatedMedia', {
    features: [{ name: 'prefers-reduced-motion', value: 'reduce' }]
  });
  await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
  await new Promise(r => setTimeout(r, 2500));

  const rmTest = await evalExpr(ws, `(async () => {
    const btn = document.querySelector('button.mpcbc-video-toggle');
    const video = document.querySelector('.elementor-background-video-container video');
    if (!btn || !video) return { error: 'btn or video missing' };

    const pausedAtStart = video.paused;
    const labelAtStart = btn.getAttribute('aria-label');
    const classAtStart = btn.classList.contains('is-paused');

    // Press play
    btn.click();
    await new Promise(r => setTimeout(r, 200));
    const pausedAfterClick = video.paused;
    const labelAfterClick = btn.getAttribute('aria-label');
    const classAfterClick = btn.classList.contains('is-paused');

    return {
      pausedAtStart,
      labelAtStart,
      classAtStart,
      pausedAfterClick,
      labelAfterClick,
      classAfterClick
    };
  })()`);
  console.log(`Reduced motion test results:`, JSON.stringify(rmTest, null, 2));

  console.log(`\nConsole errors recorded during run: ${consoleErrors.length}`);
  if (consoleErrors.length > 0) {
    console.log(consoleErrors);
  }

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  process.exit(1);
});
