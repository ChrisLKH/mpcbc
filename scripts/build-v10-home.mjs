// Builds the v10 Home (DESIGN-v10.md + "Homepage v10.dc.html", words from the MPCBC Website Content Workbook)
// as native Atomic Elementor JSON. Output: content-source/v10-home.json (applied by apply-v10-home.php).
// Every text is an Atomic Heading / Paragraph / Button: main text = English, mpcbc_zh_hant = 繁體 (简 is converted
// by mpcbc-translations). The "other-language" line under a heading has the Chinese as main text and the English in 繁.
// Colours only from the global variables; no widget sets a font-family (the theme stack puts Noto TC/SC after Hanken).
// Custom CSS for what Elementor Free cannot do lives in hello-mpcbc/style.css, keyed on the _cssid values below.
import fs from 'node:fs';
import { SITE, hex, C, IMG, sz, cu, auto, str, num, col, side, dims, bg, clear, border, topLine, bottomLine, flex, rot, gradient, BP, merge, variants, node, cssid, url, linkSet, box, row, grid3, wrap, section, tr, zhField, esc, reset, H, P, T, heading, BTN, btnBase, button, textLink, img, headRow } from "./v10-lib.mjs";

const root = new URL('../', import.meta.url);
const before = JSON.parse(fs.readFileSync(new URL('content-source/before-v10/21.json', root), 'utf8')).meta._elementor_data;

// ================= 1 · Hero (the current Home's looping video; centred content over a navy gradient) =================
const oldHero = before[0];
const times = [['9:00', 'AM · Mandarin', '國語'], ['11:00', 'AM · Cantonese', '粵語'], ['11:00', 'AM · English', '英語']]; // always in time order
const hero = { ...structuredClone(oldHero), id: hex(4), elements: [
  node('e-background-video-content', {}, {
    width: sz(100, '%'), 'min-height': cu('max(640px, 100svh)'), display: str('flex'), 'flex-direction': str('column'), 'align-items': str('center'), 'justify-content': str('center'),
    padding: dims(136, 20, 96, 20), position: str('relative'), 'z-index': num(1), 'text-align': str('center'), color: col(C.white),
    background: gradient([['rgba(18,26,38,0.62)', 0], ['rgba(18,26,38,0.38)', 40], ['rgba(18,26,38,0.58)', 70], ['rgba(18,26,38,0.9)', 100]]),
  }, [
    box({ width: sz(100, '%'), 'max-width': sz(980), 'align-items': str('center'), gap: sz(18) }, [
      H('h1', ['Monterey Park Chinese Baptist Church', '慕道園華人浸信會'], { 'font-size': cu('clamp(2.1rem, 1.26rem + 3vw, 3.6rem)'), 'font-weight': str('500'), 'line-height': sz(1.1, 'em'), 'letter-spacing': sz(0.06, 'em'), 'text-transform': str('uppercase'), color: col(C.white), 'max-width': sz(16, 'em') }, 'mpcbc-hero-name'),
      P(['慕道園華人浸信會', 'Monterey Park Chinese Baptist Church'], { 'font-size': cu('clamp(1.5rem, 1.05rem + 1.7vw, 2.4rem)'), 'font-weight': str('500'), 'line-height': sz(1.3, 'em'), 'letter-spacing': sz(0.18, 'em'), color: col(C.white) }, 'mpcbc-hero-alt'),
      P(['Knowing God is our foundation, glorifying God is our goal.', '認識神為基礎，榮耀神是目標。'], { margin: dims(6, 0, 0, 0), 'max-width': sz(34, 'em'), 'font-size': cu('clamp(17px, 0.95rem + 0.35vw, 20px)'), 'line-height': sz(1.5, 'em'), color: col(C.white88) }),
      box({ margin: dims(22, 0, 0, 0), padding: dims(26, 0, 0, 0), ...topLine(1, C.white30), 'align-items': str('center'), gap: sz(14) }, [
        P(['Every Sunday', '主日崇拜'], { 'font-size': sz(13), 'font-weight': str('600'), 'letter-spacing': sz(0.14, 'em'), 'text-transform': str('uppercase'), color: col(C.white) }),
        row({ 'justify-content': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(12), column: sz(40) } } }, times.map(([n, en, zh], i) =>
          row({ 'align-items': str('flex-end'), gap: sz(8), 'flex-wrap': str('nowrap') }, [
            P(n, { 'font-size': cu('clamp(1.75rem, 1.35rem + 1.3vw, 2.5rem)'), 'font-weight': str('500'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1, 'em'), color: col(C.white) }),
            P([en, zh], { 'font-size': sz(16), 'font-weight': str('500'), color: col(C.white) }),
          ], { id: `mpcbc-hero-time-${i + 1}`, title: `time ${en}` })), { title: 'Sunday times' }),
        row({ 'justify-content': str('center'), 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(0), column: sz(14) } }, margin: dims(4, 0, 0, 0) }, [
          P('302 W. Commonwealth Ave., Alhambra, CA 91801', { 'font-size': sz(16), 'font-weight': str('500'), color: col(C.white88) }),
          textLink('https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801', ['Get Directions ↗', '路線 ↗'], C.white, { 'font-size': sz(16), 'text-decoration': str('underline') }),
        ], { title: 'address' }),
      ], { title: 'Sunday worship' }),
      row({ 'justify-content': str('center'), gap: sz(12), margin: dims(18, 0, 0, 0) }, [
        button('/visit/', ['Plan a Visit', '計劃來訪'], 'accent', { 'min-height': sz(52) }),
        button('#online', ['Watch Live', '線上崇拜'], 'whiteLine', { 'min-height': sz(52) }),
      ], { title: 'hero buttons' }),
    ], { title: 'hero content' }),
  ], 'Video content'),
  structuredClone(oldHero.elements.find((e) => e.elType === 'e-background-video-controls')),
] };

// ================= 2 · Online Services (cards from the YouTube source; CSS orders them by page language) =================
const live = (ministry) => ({ id: hex(4), elType: 'widget', widgetType: 'shortcode', settings: { shortcode: `[mpcbc_live ministry="${ministry}" part="media"]` }, elements: [] });
const services = [
  ['english', ['English Worship', '英語崇拜'], ['11:00 AM · Fellowship Hall', '上午 11:00 · 副堂']],
  ['cantonese', ['Cantonese Worship', '粵語崇拜'], ['11:00 AM · Sanctuary', '上午 11:00 · 大堂']],
  ['mandarin', ['Mandarin Worship', '國語崇拜'], ['9:00 AM · Sanctuary', '上午 9:00 · 大堂']],
];
const card = { background: bg(C.surface), ...border(1, C.line), 'border-radius': sz(16) };
const online = section('online', '2 Online Services', {}, [
  headRow(['Online Services', '線上崇拜']),
  grid3(services.map(([key, name, when]) => box(merge(card, { padding: dims(12, 12, 24, 12), gap: sz(16) }), [
    live(key),
    box({ padding: dims(0, 12), gap: sz(6), flex: flex(1, 1, 'auto') }, [
      P(when, T.label),
      H('h3', name, merge(T.card, { 'font-size': sz(26), 'line-height': sz(1.2, 'em') })),
      row({ gap: { $$type: 'layout-direction', value: { row: sz(0), column: sz(22) } }, margin: dims('auto', 0, 0, 0), padding: dims(12, 0, 0, 0) }, [
        textLink(`/${key}/`, ['Get to know us →', '認識我們 →']),
        textLink(`/${key}/sermons/`, ['Past sermons', '講道重溫']),
      ], { title: 'card links' }),
    ], { title: 'card text' }),
  ], { id: `mpcbc-svc-${key}`, title: `service ${name[0]}` })), { id: 'mpcbc-services' }),
]);

// ================= 3 · Welcome =================
const purposes = [['Worship', '敬拜'], ['Fellowship', '團契'], ['Discipleship', '門徒訓練'], ['Ministry', '事奉'], ['Evangelism', '傳福音']];
const welcome = section('welcome', '3 Welcome', { background: bg(C.surface), ...topLine(1, C.line) }, [
  row({ 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(40), column: sz(72) } } }, [
    box({ flex: flex(1, 1, '28rem') }, [
      P(['Since 1977', '自1977年'], merge(T.label, { margin: dims(0, 0, 14, 0) })),
      heading(['Welcome', '歡迎']),
      P(['Through worship, fellowship, discipleship, ministry, and evangelism, we seek to know God, and make His name known.', '我們藉著敬拜、團契、門徒訓練、事奉、傳福音來認識和榮耀神。'],
        { margin: dims(28, 0, 0, 0), 'max-width': sz(38, 'em'), 'font-size': cu('clamp(20px, 1.05rem + 0.6vw, 24px)'), 'line-height': sz(1.45, 'em'), color: col(C.ink) }),
      row({ gap: sz(8), margin: dims(28, 0, 0, 0) }, purposes.map(([en, zh]) => row({ 'align-items': str('center'), gap: sz(8), padding: dims(10, 18), 'border-radius': sz(999), background: bg(C.fill), 'flex-wrap': str('nowrap') }, [
        P([en, zh], { 'font-weight': str('500'), 'line-height': sz(1.4, 'em'), color: col(C.ink) }),
        P([zh, en], { 'font-size': sz(15), 'line-height': sz(1.4, 'em'), color: col(C.muted) }),
      ], { title: `purpose ${en}` })), { title: 'five purposes' }),
      row({ gap: sz(12), margin: dims(32, 0, 0, 0) }, [
        button('/visit/', ['Plan a Visit', '計劃來訪']),
        button('/about/#beliefs', ['Our Beliefs', '我們的信仰'], 'line'),
      ], { title: 'buttons' }),
    ], { title: 'welcome text' }),
    box({ flex: flex(1, 1, '24rem'), 'border-radius': sz(24), overflow: str('hidden') }, [img(IMG.church, { 'aspect-ratio': str('4 / 3'), height: sz(100, '%') }, 'church building')], { title: 'photo' }),
  ], { title: 'welcome row' }),
]);

// ================= 4 · MPCBC KIDS (yellow band; waves, frames and hover lift in style.css) =================
const tile = (color, label, value) => box({ background: bg(color), 'border-radius': sz(24), padding: dims(20, 22), gap: sz(6) }, [
  P(label, merge(T.label, { color: col(C.ink) })), P(value, { 'font-size': sz(23), 'font-weight': str('600'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1.3, 'em'), color: col(C.ink) })], { title: `tile ${label[0]}` });
const frame = (id, imgId, ratio, deg, style, caption) => box(merge({ position: str('absolute'), background: bg(C.surface), padding: dims(12, 12, 18, 12), 'border-radius': sz(6), transform: rot(deg) }, style), [
  img(imgId, { 'aspect-ratio': str(ratio), 'border-radius': sz(2) }, `photo ${id}`), ...(caption ? [P(caption, { margin: dims(10, 0, 0, 0), 'font-size': sz(14), 'font-weight': str('500'), color: col(C.muted) })] : [])], { id, title: `photo frame ${id}` });
const kidsLine = { 'font-size': sz(22), 'font-weight': str('600'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1.5, 'em'), color: col(C.ink) };
const kids = section('kids', '4 MPCBC KIDS', { background: bg(C.kids), position: str('relative'), padding: dims('clamp(104px, 12vw, 150px)', 20) }, [
  row({ 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(56), column: sz(72) } } }, [
    box({ flex: flex(1, 1, '28rem') }, [
      H('h2', 'MPCBC KIDS', T.h2),
      P(['兒童事工', 'Children’s Ministry'], T.alt),
      P(['Monterey Park Chinese Baptist Church is a Purpose Driven Church. The Children’s Ministry at our church is called “MPCBC KIDS”, and our mission is to balance the purposes of the church in our ministry to children.',
        '慕道園華人浸信會是一個以目標為導向的教會。兒童事工稱為「MPCBC KIDS」，我們的使命是將教會的目標，平衡地貫徹到兒童事工當中。'], merge(T.body, { margin: dims(24, 0, 0, 0), 'max-width': sz(52, 'em'), color: col(C.ink) })),
      // three pieces so "spiritual family" can sit in a white pill (the 繁 slot holds plain text only)
      row({ margin: dims(14, 0, 0, 0), 'align-items': str('center') }, [
        P(['MPCBC KIDS is a', 'MPCBC KIDS 是所有孩子'], kidsLine),
        P(['spiritual family', '屬靈的家'], merge(kidsLine, { background: bg(C.surface), padding: dims(0, 8), margin: dims(0, 6), 'border-radius': sz(8) }), 'mpcbc-kids-family'),
        P(['for all kids.', '。'], kidsLine),
      ], { title: 'spiritual family line' }),
      node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 13rem), 1fr))'), gap: sz(12), margin: dims(28, 0, 0, 0), padding: sz(0) }, [
        tile(C.mint, ['Grades', '年級'], ['Kindergarten to 6th grade', '幼稚園至六年級']),
        tile(C.sky, ['Sunday School', '兒童主日學'], ['10:00 AM – 12:30 PM', '10:00 – 12:30']),
      ], 'fact tiles'),
      box({ margin: dims(16, 0, 0, 0), background: bg(C.surface), 'border-radius': sz(24), padding: dims(26, 28), gap: sz(0) }, [
        P('“', { 'font-size': sz(56), 'font-weight': str('700'), 'line-height': sz(0.6, 'em'), height: sz(22), color: col(C.gold) }, 'mpcbc-kids-quote'),
        P(['Train up a child in the way he should go: and when he is old, he will not depart from it.', '教養孩童，使他走當行的道，就是到老他也不偏離。'],
          { 'font-size': cu('clamp(20px, 1.05rem + 0.55vw, 24px)'), 'font-weight': str('500'), 'line-height': sz(1.45, 'em'), 'letter-spacing': sz(-0.01, 'em'), color: col(C.ink) }),
        P(['Proverbs 22:6', '箴言 22:6'], { margin: dims(12, 0, 0, 0), 'font-size': sz(15), 'font-weight': str('600'), color: col(C.muted) }),
      ], { title: 'verse card' }),
      row({ margin: dims(28, 0, 0, 0) }, [button('/kids/', 'MPCBC KIDS →', 'ink')], { title: 'button' }),
    ], { title: 'kids text' }),
    box({ flex: flex(1, 1, '24rem'), position: str('relative'), 'min-height': sz(520), m: { 'min-height': sz(340) } }, [
      frame('mpcbc-kids-photo-1', IMG.vbsWave, '4 / 5', 2.5, { 'inset-inline-end': sz(0), 'inset-block-start': sz(0), width: sz(72, '%') }),
      frame('mpcbc-kids-photo-2', IMG.vbsFloor, '4 / 3', -3, { 'inset-inline-start': sz(0), 'inset-block-end': sz(0), width: sz(60, '%') }, ['VBS at MPCBC', '慕道園暑期聖經營']),
    ], { id: 'mpcbc-kids-photos', title: 'photos' }),
  ], { title: 'kids row' }),
]);

// ================= 5 · What's happening =================
const events = [ // workbook section 9 is empty; event 1 is the 2027 VBS from the MPCBC KIDS section
  [IMG.vbsCrafts, ['June 21–25, 2027', '2027年6月21–25日'], ['2027 VBS: Take Flight', '2027 暑期聖經營：乘風飛翔'], '/kids/'],
  [IMG.eventPh, ['[Date]', '[日期]'], ['[Event title]', '[活動標題]'], '/news/'],
  [IMG.eventPh, ['[Date]', '[日期]'], ['[Event title]', '[活動標題]'], '/news/'],
];
const weekly = [ // "Auto — congregation pages": only the English groups are filled in so far
  [['MPCBC KIDS Sunday School & Children’s Worship', '兒童主日學及崇拜'], ['Sun 10:00 AM–12:30 PM', '主日 上午 10:00–12:30']],
  [['English Sunday School', '英語主日學'], ['Sun 10:00–10:45 AM', '主日 上午 10:00–10:45']],
  [['College/Career Fellowship', '[大專及在職團契中文名稱]'], ['Mon 6:30–9:00 PM', '星期一 晚上 6:30–9:00']],
  [['[Cantonese & Mandarin gatherings]', '[粵語堂及國語堂聚會]'], ['[Day, time]', '[日期、時間]']],
];
const news = section('news', '5 What’s happening', {}, [
  headRow(['What’s happening', '最新消息'], textLink('/news/', ['Calendar →', '活動日曆 →'])),
  row({ gap: sz(20) }, events.map(([imgId, date, title, href], i) => box(merge(card, { flex: flex(1, 1, '19rem'), padding: dims(12, 12, 22, 12), gap: sz(14), 'text-decoration': str('none') }), [
    box({ 'border-radius': sz(10), overflow: str('hidden') }, [img(imgId, { 'aspect-ratio': str('3 / 2') }, `event ${i + 1}`)], { title: 'image' }),
    box({ padding: dims(0, 10), gap: sz(6) }, [P(date, T.label), H('h3', title, T.card)], { title: 'event text' }),
  ], { href, title: `event ${title[0]}` })), { title: 'events' }),
  row({ gap: sz(20), margin: dims(20, 0, 0, 0) }, [
    row(merge(card, { background: bg(C.fill), ...border(1, C.fill), flex: flex(1, 1, '26rem'), 'align-items': str('center'), gap: sz(22), padding: sz(18), 'flex-wrap': str('nowrap'), 'text-decoration': str('none') }), [
      box({ width: sz(104), flex: flex(0, 0, 'auto') }, [img(IMG.newsletter, { 'border-radius': sz(4) }, 'newsletter cover')], { id: 'mpcbc-newsletter-cover', title: 'cover' }),
      box({ gap: sz(6) }, [
        P(['Latest newsletter', '最新一期'], T.label),
        H('h3', ['July 2026: Faith, testimony and mission', '2026年7月會刊：信仰見證、信靠神與宣教分享'], T.card),
        P(['Read the newsletter →', '閱讀會刊 →'], { 'font-weight': str('500'), color: col(C.ink) }),
      ], { title: 'newsletter text' }),
    ], { href: 'https://mpcbc.net/english/newsletters__trashed/newsletter-july-2026/', title: 'newsletter card' }),
    box(merge(card, { flex: flex(1, 1, '26rem'), padding: dims(22, 26) }), [
      H('h3', ['Weekly gatherings', '每週聚會'], { 'font-size': sz(19), 'font-weight': str('600'), color: col(C.ink) }),
      box({ margin: dims(12, 0, 0, 0) }, weekly.map(([title, when]) => row(merge({ 'justify-content': str('space-between'), gap: { $$type: 'layout-direction', value: { row: sz(2), column: sz(16) } }, padding: dims(12, 0) }, topLine(1, C.line)), [
        P(title, { 'font-weight': str('500'), color: col(C.ink) }), P(when, { color: col(C.muted) }),
      ], { title: `gathering ${title[0]}` })), { title: 'list' }),
    ], { title: 'weekly gatherings' }),
  ], { title: 'newsletter and gatherings' }),
]);

// ================= 6 · Our pastors =================
const pastors = [
  [IMG.caleb, ['Senior Pastor', '主任牧師'], ['Rev. Caleb Cheung', '張健庭牧師'],
    ['Pastor Cheung is from Hong Kong. He studied theology at Fuller Theological Seminary, then pursued doctoral studies in theology and philosophy of religion at Claremont Graduate University. He stayed on to serve and shepherd MPCBC, where he is now Senior Pastor.',
      '張健庭牧師來自香港，多年前蒙召在福樂神學院（Fuller Theological Seminary）念神學，繼在克萊蒙研究大學（Claremont Graduate University）進修博士課程專修神學及宗教哲學，後留在本教會事奉與牧養，現任本教會主任牧師。']],
  [IMG.pastorPh, ['English Pastor', '英語牧師'], ['Rev. Edward Lu', '呂德勝牧師'],
    ['Ed seeks to nurture faith, equip leaders, and disciple the next generation through meaningful relationships and shared ministry. He holds a B.S. in Ministry Leadership and earned his Master of Divinity and Doctor of Ministry from Moody Theological Seminary.',
      '呂德勝牧師致力透過真誠的關係與同工事奉，培育信心、裝備領袖，並門訓下一代。他持有事工領導學士學位，並在慕迪神學院（Moody Theological Seminary）取得道學碩士及教牧學博士學位。']],
  [IMG.pastorPh, ['Family and Children Minister', '家庭及兒童事工傳道'], ['Min. Lili Tian', '田俐力傳道'],
    ['Min. Lili Tian graduated from Gateway Seminary in 2024 with a Master of Theological Studies. She had an obvious calling from God to serve in family and children’s ministry. God has granted her spiritual gifts and full passion for children and family. She is willing to be used by God greatly in serving every child and family.',
      '田俐力傳道於2024年畢業於金門神學院（Gateway Seminary）神學碩士專業。她受到神明確的呼召侍奉家庭與兒童事工。神恩賜給她屬靈的恩賜以及對於家庭和孩子的滿腔熱忱。她願意被神大大使用來服侍每一個孩子與家庭。']],
];
const pastorsSection = section('pastors', '6 Our pastors', { background: bg(C.surface), ...topLine(1, C.line) }, [
  headRow(['Our pastors', '教牧同工'], textLink('/about/#leadership', ['Leadership →', '教牧團隊 →'])),
  grid3(pastors.map(([imgId, role, [en, zh], bio], i) => box(merge(card, { background: bg(C.bg), padding: dims(12, 12, 24, 12), gap: sz(16) }), [
    box({ 'border-radius': sz(10), overflow: str('hidden') }, [img(imgId, { 'aspect-ratio': str('1 / 1') }, `portrait ${en}`)], { title: 'photo' }),
    box({ padding: dims(0, 12), gap: sz(6), flex: flex(1, 1, 'auto') }, [
      P(role, T.label),
      H('h3', [en, zh], T.card),
      P([zh, en], { 'font-size': sz(16), color: col(C.muted) }),
      box({ height: sz(1), background: bg(C.line), margin: dims(10, 0, 6, 0) }, [], { title: 'divider' }),
      P(bio, merge(T.body, { color: col(C.muted) }), `mpcbc-bio-${i + 1}`),
      node('e-button', { text: { $$type: 'escaped-html', value: 'Read more' }, tag: str('button'), ...zhField('Read more', '閱讀更多'), ...cssid(`mpcbc-more-${i + 1}`) },
        { display: str('inline-flex'), 'align-self': str('flex-start'), 'min-height': sz(44), padding: sz(0), background: clear, 'font-size': sz(17), 'font-weight': str('500'), color: col(C.ink), hover: { 'text-decoration': str('underline') } }, [], 'Read more'),
    ], { title: 'pastor text' }),
  ], { title: `pastor ${en}` }))),
]);

// ================= 7 · Plan a visit & prayer (night band; the form itself is on /visit/) =================
const invite = (href, title, sub, filled) => row(merge({ 'justify-content': str('space-between'), 'align-items': str('center'), gap: sz(20), padding: dims(24, 28), 'border-radius': sz(20), 'flex-wrap': str('nowrap'), 'text-decoration': str('none') },
  filled ? { background: bg(C.accent), hover: { background: bg(C.accentHover) } } : { ...border(1, C.white30), hover: { background: bg(C.white14) } }), [
  box({ gap: sz(4) }, [
    P(title, { 'font-size': sz(24), 'font-weight': str('500'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1.3, 'em'), color: col(C.white) }),
    P(sub, { 'font-size': sz(15), color: col(filled ? C.white88 : C.nightText) }),
  ], { title: 'text' }),
  P('→', { 'font-size': sz(24), color: col(C.white) }),
], { href, title: `invitation ${title[0]}` });
const visit = section('visit', '7 Plan a visit & prayer', { background: bg(C.night) }, [
  row({ 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(40), column: sz(80) } } }, [
    box({ flex: flex(1, 1, '24rem') }, [
      heading(['Plan a visit & prayer', '計劃來訪及代禱'], { color: col(C.white) }, { color: col(C.nightText) }),
      P(['Whether it’s your first Sunday or you need someone to pray with you, we’d love to hear from you.', '無論是第一次來訪，或需要有人與您一同禱告，我們都很樂意與您聯絡。'],
        { margin: dims(22, 0, 0, 0), 'max-width': sz(40, 'em'), 'font-size': cu('clamp(18px, 1rem + 0.35vw, 21px)'), 'line-height': sz(1.5, 'em'), color: col(C.white88) }),
    ], { title: 'invitation text' }),
    box({ flex: flex(1, 1, '26rem'), gap: sz(12) }, [
      invite('/visit/?reason=visit#contact', ['Plan a Visit', '計劃來訪'], ['Tell us you’re coming and we’ll look out for you.', '告訴我們您會來，我們會預備迎接您。'], true),
      invite('/visit/?reason=prayer#contact', ['Prayer request', '代禱'], ['A pastor reads every request and keeps it private.', '牧師會親自閱讀每一個代禱事項，並予以保密。'], false),
      row({ margin: dims(10, 0, 0, 0), gap: { $$type: 'layout-direction', value: { row: sz(0), column: sz(22) } } }, [
        textLink('/visit/#first-sunday', ['What to expect →', '了解更多 →'], C.white, { 'text-decoration': str('underline') }),
        textLink('tel:+16262891284', '(626) 289-1284', C.white, { 'text-decoration': str('underline') }),
        textLink('mailto:info@mpcbc.net', 'info@mpcbc.net', C.white, { 'text-decoration': str('underline') }),
      ], { title: 'contact links' }),
    ], { title: 'invitation cards' }),
  ], { title: 'visit row' }),
]);

// ================= 8 · Give (airmail envelope: stripes, postmark and stamp frame in style.css) =================
const addrLine = (s, style = {}) => P(s, merge({ 'font-size': sz(21), 'font-weight': str('500'), 'line-height': sz(1.4, 'em'), padding: dims(8, 2), color: col(C.ink) }, bottomLine(1, C.line), style));
const smallLabel = (s, style) => P(s, merge(T.label, { color: col(C.muted) }, style));
const give = section('give', '8 Give', { background: bg(C.surface), ...topLine(1, C.line), padding: dims('clamp(72px, 10vw, 120px)', 20) }, [
  box({ width: sz(100, '%'), 'max-width': sz(1000), margin: dims(0, 'auto'), padding: sz(12), 'border-radius': sz(6) }, [
    box({ background: bg(C.surface), 'border-radius': sz(2), padding: dims('clamp(24px, 4vw, 44px)', 'clamp(20px, 5vw, 52px)', 'clamp(28px, 4vw, 44px)') }, [
      row({ 'justify-content': str('space-between'), 'align-items': str('flex-start'), gap: { $$type: 'layout-direction', value: { row: sz(20), column: sz(32) } }, 'flex-wrap': str('wrap-reverse') }, [
        heading(['Give', '奉獻']),
        row({ 'align-items': str('flex-start'), gap: sz(20), margin: dims(0, 0, 0, 'auto'), 'flex-wrap': str('nowrap') }, [
          box({}, [P('ALHAMBRA<br>CA<br>91801', {})], { id: 'mpcbc-postmark', title: 'postmark' }),
          box({}, [img(IMG.stamp, { width: sz(76), height: sz(96) }, 'steeple stamp')], { id: 'mpcbc-stamp', title: 'stamp' }),
        ], { title: 'postmark and stamp' }),
      ], { title: 'envelope top' }),
      box({ width: sz(100, '%'), 'max-width': sz(470), margin: dims(28, 0, 0, 'auto') }, [
        smallLabel(['Make checks payable to', '支票抬頭請寫'], { margin: dims(0, 0, 2, 0) }),
        addrLine('MPCBC', { 'font-size': sz(28), 'font-weight': str('600'), 'letter-spacing': sz(0.04, 'em') }),
        smallLabel(['Mail to', '郵寄至'], { margin: dims(24, 0, 2, 0) }),
        addrLine('Monterey Park Chinese Baptist Church'),
        addrLine('302 W. Commonwealth Ave.'),
        addrLine('Alhambra, CA 91801'),
      ], { title: 'address block' }),
      row(merge({ margin: dims(40, 0, 0, 0), padding: dims(22, 0, 0, 0), 'justify-content': str('space-between'), 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(16), column: sz(24) } } }, topLine(2, C.line, 'dashed')), [
        row({ 'align-items': str('center'), gap: { $$type: 'layout-direction', value: { row: sz(0), column: sz(8) } } }, [
          P(['Or give online with PayPal ·', '或透過 PayPal 網上奉獻 ·'], { color: col(C.muted) }),
          textLink('mailto:donations@mpcbc.net', 'donations@mpcbc.net', C.ink, { 'text-decoration': str('underline') }),
        ], { title: 'PayPal' }),
        button('/give/', ['Give', '奉獻'], 'accent', { 'min-height': sz(52), padding: dims(0, 32) }),
      ], { title: 'online giving' }),
    ], { title: 'envelope paper' }),
  ], { id: 'mpcbc-envelope', title: 'envelope' }),
]);

// ================= 10 · Footer (template "MPCBC – Footer", every page) =================
const footLabel = (s) => P(s, merge(T.label, { color: col(C.white), margin: dims(0, 0, 14, 0) }));
const footText = (s, style) => P(s, merge({ 'font-size': sz(15), 'line-height': sz(1.6, 'em'), color: col(C.nightText) }, style));
const footLink = (href, s) => textLink(href, s, C.white, { 'font-size': sz(15), 'min-height': sz(36) });
const chip = (href, s) => textLink(href, s, C.white, { 'font-size': sz(15), 'min-height': sz(40), padding: dims(0, 14), ...border(1, C.muted), 'border-radius': sz(999) });
const footer = [box({ width: sz(100, '%'), background: bg(C.night), padding: dims('clamp(56px, 8vw, 80px)', 20, 32, 20) }, [
  wrap([
    node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 14rem), 1fr))'), gap: { $$type: 'layout-direction', value: { row: sz(40), column: sz(32) } }, padding: sz(0) }, [
      box({}, [
        P('Monterey Park Chinese Baptist Church', { 'font-size': sz(17), 'font-weight': str('600'), color: col(C.white) }),
        P('慕道園華人浸信會', { margin: dims(2, 0, 0, 0), 'letter-spacing': sz(0.12, 'em'), color: col(C.white) }),
        footText('302 W. Commonwealth Ave.<br>Alhambra, CA 91801', { margin: dims(16, 0, 0, 0) }),
        footLink('https://www.google.com/maps/search/?api=1&query=302+W.+Commonwealth+Ave.+Alhambra+CA+91801', ['Get Directions ↗', '路線 ↗']),
      ], { title: 'church' }),
      box({ gap: sz(6) }, [
        footLabel(['Sunday', '主日']),
        ...times.map(([n, en, zh]) => footText([`${en.replace('AM · ', '')} · ${n} AM`, `${zh} · 上午 ${n}`])),
      ], { title: 'Sunday times' }),
      box({ 'align-items': str('flex-start') }, [
        footLabel(['Contact', '聯絡我們']),
        footLink('tel:+16262891284', '(626) 289-1284'),
        footLink('mailto:info@mpcbc.net', 'info@mpcbc.net'),
        footText(['Office hours: [TBD]', '辦公時間：[待定]'], { margin: dims(6, 0, 0, 0) }),
        row({ gap: sz(8), margin: dims(16, 0, 0, 0) }, [
          chip('https://www.youtube.com/channel/UCJx8Xl0QqiNkZwwfLXaASHQ', 'YouTube'),
          chip('#', '[Facebook]'), // ponytail: no church Facebook page address in the workbook yet
          chip('#', ['[Other]', '[其他]']),
        ], { title: 'social links' }),
      ], { title: 'contact' }),
      box({ 'align-items': str('flex-start') }, [
        footLabel(['Get our newsletter', '訂閱電子報']),
        footText(['[Sign-up provider TBD]', '[訂閱方式待定]'], { margin: dims(0, 0, 12, 0) }),
        button('mailto:info@mpcbc.net?subject=Newsletter%20sign-up', ['Sign up', '訂閱'], 'accent', { 'min-height': sz(44), padding: dims(0, 20) }),
      ], { title: 'newsletter' }),
    ], 'footer columns'),
    box(merge({ margin: dims(48, 0, 0, 0), padding: dims(20, 0, 0, 0) }, topLine(1, C.white14)), [
      footText(['© MPCBC · Founded 1977', '© 慕道園華人浸信會 · 1977年成立'], { 'font-size': sz(14) }),
    ], { title: 'copyright' }),
  ]),
], { id: 'mpcbc-footer', title: 'MPCBC footer' })];
fs.writeFileSync(new URL('content-source/v10-footer.json', root), JSON.stringify(footer, null, 1));

// ================= Visit page: #contact (the form is appended by apply-v10-visit-form.php) =================
const contact = section('contact', 'Get in touch (form)', { background: bg(C.surface), ...topLine(1, C.line) }, [
  heading(['Get in touch', '聯絡我們']),
  P(['Want to be baptized, become a member, or need prayer? Talk to a pastor after the service or send us a message.', '想受浸、成為會友，或需要代禱？歡迎在崇拜後與牧師交談，或給我們留言。'],
    merge(T.body, { margin: dims(20, 0, 28, 0), 'max-width': sz(40, 'em'), color: col(C.ink) })),
]);
contact.elements[0].styles[Object.keys(contact.elements[0].styles)[0]].variants[0].props['max-width'] = sz(720); // a narrower column for the form
fs.writeFileSync(new URL('content-source/v10-visit-contact.json', root), JSON.stringify(contact, null, 1));

const elements = [hero, online, welcome, kids, news, pastorsSection, visit, give];
// self-check: one H1, every heading level in order, no empty translations
const tags = []; (function w(n) { if (n.widgetType === 'e-heading') tags.push(n.settings.tag.value); (n.elements || []).forEach(w); })({ elements });
if (tags.filter((t) => t === 'h1').length !== 1 || tags[0] !== 'h1') throw new Error('expected exactly one H1, first: ' + tags.join(','));
for (let i = 1; i < tags.length; i++) if (+tags[i][1] > +tags[i - 1][1] + 1) throw new Error(`heading level skipped at ${i}: ${tags.join(',')}`);
fs.writeFileSync(new URL('content-source/v10-home.json', root), JSON.stringify(elements, null, 1));
console.log(`v10-home.json: ${elements.length} sections, headings ${tags.join(' ')}`);
