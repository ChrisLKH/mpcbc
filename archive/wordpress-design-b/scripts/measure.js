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

async function evalExpr(ws, expr) {
  const res = await sendCmd(ws, 'Runtime.evaluate', {
    expression: expr,
    returnByValue: true,
    awaitPromise: true
  });
  return res?.result?.value;
}

async function run() {
  const port = 9342;
  const proc = spawn(chromePath, [
    '--headless=new',
    '--no-sandbox',
    '--disable-gpu',
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

  const widths = [1440, 768, 390, 360];
  const allResults = {};

  for (const width of widths) {
    const isMobile = width <= 768;
    await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
      width,
      height: 900,
      deviceScaleFactor: 1,
      mobile: isMobile
    });

    await sendCmd(ws, 'Page.navigate', { url: 'http://mpcbc-2.local/' });
    await new Promise(r => setTimeout(r, 1500));

    const info = await evalExpr(ws, `(() => {
      const header = document.querySelector('.site-header');
      const headerRect = header ? header.getBoundingClientRect() : null;
      const hero = document.querySelector('#top, .hero-section');
      const heroRect = hero ? hero.getBoundingClientRect() : null;
      const burger = document.querySelector('.site-navigation-toggle');
      const burgerIcon = document.querySelector('.site-navigation-toggle-icon');

      const sectionIds = {
        'cbacc1d6': { name: 'Hero', anchor: 'top' },
        '6d01e132': { name: 'Sunday', anchor: 'sunday' },
        '673daa23': { name: 'Announcements', anchor: 'news' },
        '0be3fb7f': { name: 'Welcome', anchor: 'welcome' },
        '9d189964': { name: 'Connect', anchor: 'connect' },
        '28dc47fb': { name: 'Children', anchor: 'children' },
        'f6320806': { name: 'Events', anchor: 'events' },
        'a52ab4d1': { name: 'Leaders', anchor: 'leaders' },
        'f5280123': { name: 'Giving', anchor: 'give' },
        'eb2faec4': { name: 'Footer', anchor: '' }
      };

      const sections = {};
      const contentLeftEdges = {};

      for (const [id, meta] of Object.entries(sectionIds)) {
        const el = document.querySelector('.elementor-element-' + id);
        if (el) {
          const comp = window.getComputedStyle(el);
          const inner = el.querySelector('.e-con-inner');
          const innerComp = inner ? window.getComputedStyle(inner) : null;

          const padTop = parseFloat(comp.paddingTop) + (innerComp ? parseFloat(innerComp.paddingTop) : 0);
          const padBottom = parseFloat(comp.paddingBottom) + (innerComp ? parseFloat(innerComp.paddingBottom) : 0);
          const padLeft = parseFloat(comp.paddingLeft);
          const padRight = parseFloat(comp.paddingRight);

          // Content left edge: inner or child container rect left
          const contentBox = inner || el;
          const boxRect = contentBox.getBoundingClientRect();

          sections[meta.name] = {
            id,
            idAttr: el.id,
            expectedAnchor: meta.anchor,
            padTop: Math.round(padTop),
            padBottom: Math.round(padBottom),
            padLeft: Math.round(padLeft),
            padRight: Math.round(padRight),
            contentBoxLeft: Math.round(boxRect.left + (inner ? 0 : padLeft))
          };

          // Find first text/content
          const firstText = el.querySelector('h1, h2, h3, p, strong, span.elementor-button-text');
          if (firstText) {
            contentLeftEdges[meta.name] = Math.round(firstText.getBoundingClientRect().left);
          }
        }
      }

      // Children card to Events first element gap
      const kidsCard = document.querySelector('.elementor-element-555a0381');
      const eventsHdr = document.querySelector('.elementor-element-f6320806 .elementor-element');
      let childrenToEventsGap = null;
      if (kidsCard && eventsHdr) {
        childrenToEventsGap = Math.round(eventsHdr.getBoundingClientRect().top - kidsCard.getBoundingClientRect().bottom);
      }

      // Cards
      const cardIds = {
        'bcc35c16': 'Announcement card',
        '555a0381': 'Children card',
        'bff0e2cf': 'Giving dark card',
        'c3b6af73': 'Connect form card'
      };
      const cards = {};
      for (const [cid, name] of Object.entries(cardIds)) {
        const el = document.querySelector('.elementor-element-' + cid);
        if (el) {
          const comp = window.getComputedStyle(el);
          cards[name] = {
            id: cid,
            padTop: Math.round(parseFloat(comp.paddingTop)),
            padBottom: Math.round(parseFloat(comp.paddingBottom)),
            padLeft: Math.round(parseFloat(comp.paddingLeft)),
            padRight: Math.round(parseFloat(comp.paddingRight))
          };
        }
      }

      return {
        headerHeight: headerRect ? Math.round(headerRect.height * 10) / 10 : null,
        headerTop: headerRect ? Math.round(headerRect.top) : null,
        heroTop: heroRect ? Math.round(heroRect.top) : null,
        burgerColor: burger ? window.getComputedStyle(burger).color : null,
        burgerIconColor: burgerIcon ? window.getComputedStyle(burgerIcon).color : null,
        sections,
        contentLeftEdges,
        childrenToEventsGap,
        cards,
        scrollWidth: document.documentElement.scrollWidth,
        clientWidth: document.documentElement.clientWidth
      };
    })()`);

    allResults[width] = info;

    console.log(`\n=============================================================`);
    console.log(`=== MEASUREMENTS AT ${width}px ===`);
    console.log(`=============================================================`);
    console.log(`Header height: ${info.headerHeight}px (target: 65px)`);
    console.log(`Header top: ${info.headerTop}px`);
    console.log(`Hero top: ${info.heroTop}px (target: 0px, no white gap)`);
    console.log(`Burger color at top: ${info.burgerColor}`);
    console.log(`Children -> Events content gap: ${info.childrenToEventsGap}px`);
    console.log(`Horizontal: scrollWidth=${info.scrollWidth}, clientWidth=${info.clientWidth}`);
    console.log('\nSections (ID & effective padding top/bottom):');
    for (const [sname, sdata] of Object.entries(info.sections)) {
      console.log(`  ${sname.padEnd(14)} [#${(sdata.idAttr || 'NONE').padEnd(8)}] padTop=${sdata.padTop}px, padBottom=${sdata.padBottom}px (L=${sdata.padLeft}px, R=${sdata.padRight}px)`);
    }
    console.log('\nCards (inner padding):');
    for (const [name, cdata] of Object.entries(info.cards)) {
      console.log(`  ${name.padEnd(20)} top=${cdata.padTop}px, bottom=${cdata.padBottom}px, left=${cdata.padLeft}px, right=${cdata.padRight}px`);
    }
    if (width === 390) {
      console.log('\nMobile 390px content left edges:');
      for (const [sec, left] of Object.entries(info.contentLeftEdges)) {
        console.log(`  ${sec.padEnd(14)}: ${left}px`);
      }
    }
  }

  // Anchor scroll test
  console.log(`\n=============================================================`);
  console.log(`=== ANCHOR SCROLLING TEST (Desktop 1440px) ===`);
  console.log(`=============================================================`);
  await sendCmd(ws, 'Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false
  });

  const anchors = ['sunday', 'news', 'welcome', 'connect', 'children', 'events', 'leaders', 'give'];
  for (const anchor of anchors) {
    const res = await evalExpr(ws, `(() => {
      const el = document.getElementById('${anchor}');
      if (!el) return { found: false };
      el.scrollIntoView({ behavior: 'instant', block: 'start' });
      const rect = el.getBoundingClientRect();
      const header = document.querySelector('.site-header');
      const headerBottom = header ? header.getBoundingClientRect().bottom : 0;
      return {
        found: true,
        top: Math.round(rect.top),
        headerBottom: Math.round(headerBottom),
        visibleBelowHeader: rect.top >= headerBottom - 2
      };
    })()`);
    console.log(`Anchor #${anchor.padEnd(10)} found=${res.found}, element top=${res.top}px, header bottom=${res.headerBottom}px -> visible below header: ${res.visibleBelowHeader}`);
  }

  ws.close();
  proc.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  process.exit(1);
});
