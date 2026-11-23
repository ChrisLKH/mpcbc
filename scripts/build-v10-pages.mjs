// Interior pages in the v10 style (approved mockups taste-mockup/about-v10.html and english-v10.html), as native Atomic
// Elementor JSON. Output: content-source/v10-pages.json, applied by apply-v10-pages.php.
// Built only from the homepage's pieces (v10-lib.mjs); pages vary by hero type and section mix, not by new styles.
// Words: the MPCBC Website Content Workbook (sections 2 and 5); the statement of faith is the current site's 8 articles.
import fs from 'node:fs';
import { hex, C, sz, cu, auto, str, num, col, dims, bg, clear, border, topLine, flex, rot, gradient, merge, node, cssid, box, row, grid3, wrap, section, H, P, T, heading, button, textLink, img, headRow } from './v10-lib.mjs';

const root = new URL('../', import.meta.url);
const read = (p) => JSON.parse(fs.readFileSync(new URL(p, root), 'utf8'));
const homeHero = read('content-source/before-v10/21.json').meta._elementor_data[0]; // its background-video settings and controls
const IMG = { church: 545, aisle: 539, sanctuaryVbs: 540, courtyard: 541, vbsCrafts: 542, vbsWave: 543, vbsFloor: 544, caleb: 547, vbsBoy: 1340, pastorPh: 80, eventPh: 1324 };
const gap2 = (r, c) => ({ $$type: 'layout-direction', value: { row: sz(r), column: sz(c) } });
const split = (kids, style = {}) => row(merge({ 'align-items': str('center'), gap: gap2(40, 80) }, style), kids.map((k) => box({ flex: flex(1, 1, '24rem') }, [k].flat(), { title: 'column' })), { title: 'two columns' });

// ---------- shared interior pieces (all from the homepage) ----------
// Hero A: the homepage Welcome split (label, H1 + other-language line, lead, buttons; rounded photo on the right)
const heroSplit = ({ label, h1, lead, buttons, image }) => box({ width: sz(100, '%'), padding: dims('clamp(56px, 7vw, 96px)', 20, 'clamp(64px, 9vw, 104px)', 20) }, [wrap([
  row({ 'align-items': str('center'), gap: gap2(40, 72) }, [
    box({ flex: flex(1, 1, '28rem') }, [
      P(label, merge(T.label, { margin: dims(0, 0, 14, 0) })),
      H('h1', h1, merge(T.h2, { 'font-size': cu('clamp(2.6rem, 1.6rem + 3.6vw, 4.6rem)'), 'line-height': sz(1.04, 'em'), 'letter-spacing': sz(-0.025, 'em') })),
      P([h1[1], h1[0]], T.alt),
      P(lead, { margin: dims(28, 0, 0, 0), 'max-width': sz(38, 'em'), 'font-size': cu('clamp(20px, 1.05rem + 0.6vw, 24px)'), 'line-height': sz(1.45, 'em'), color: col(C.ink) }),
      row({ gap: sz(12), margin: dims(32, 0, 0, 0) }, buttons, { title: 'buttons' }),
    ], { title: 'hero text' }),
    box({ flex: flex(1, 1, '24rem'), 'border-radius': sz(24), overflow: str('hidden') }, [img(image, { 'aspect-ratio': str('4 / 3') }, 'hero photo')], { title: 'hero photo' }),
  ], { title: 'hero row' }),
])], { id: 'top', title: 'Hero (split)' });

// Hero B: the homepage hero, shorter, text bottom-left over the church's own video
const heroVideo = ({ label, h1, lead, facts, buttons }) => ({ ...structuredClone(homeHero), id: hex(4), settings: { ...structuredClone(homeHero.settings), _cssid: str('top') }, elements: [
  node('e-background-video-content', {}, {
    width: sz(100, '%'), 'min-height': cu('min(78svh, 760px)'), display: str('flex'), 'flex-direction': str('column'), 'justify-content': str('flex-end'),
    padding: dims(120, 20, 'clamp(48px, 6vw, 72px)', 20), position: str('relative'), 'z-index': num(1), color: col(C.white),
    background: gradient([['rgba(18,26,38,0.35)', 0], ['rgba(18,26,38,0.45)', 45], ['rgba(18,26,38,0.9)', 100]]),
  }, [wrap([
    P(label, merge(T.label, { color: col(C.nightText), margin: dims(0, 0, 12, 0) })),
    H('h1', h1, merge(T.h2, { 'font-size': cu('clamp(2.6rem, 1.6rem + 3.6vw, 4.6rem)'), color: col(C.white) })),
    P(lead, { margin: dims(20, 0, 0, 0), 'max-width': sz(38, 'em'), 'font-size': cu('clamp(20px, 1.05rem + 0.6vw, 24px)'), 'line-height': sz(1.45, 'em'), color: col(C.white88) }),
    row({ gap: gap2(8, 28), margin: dims(24, 0, 0, 0) }, facts.map(([n, s]) => row({ 'align-items': str('flex-end'), gap: sz(8), 'flex-wrap': str('nowrap') }, [
      P(n, { 'font-size': cu('clamp(1.75rem, 1.35rem + 1.3vw, 2.5rem)'), 'font-weight': str('500'), 'line-height': sz(1, 'em'), color: col(C.white) }),
      P(s, { 'font-size': sz(16), 'font-weight': str('500'), color: col(C.white) }),
    ], { title: `fact ${n}` })), { title: 'facts' }),
    row({ gap: sz(12), margin: dims(32, 0, 0, 0) }, buttons, { title: 'buttons' }),
  ])], 'Video content'),
  structuredClone(homeHero.elements.find((e) => e.elType === 'e-background-video-controls')),
] });

const card = { background: bg(C.surface), ...border(1, C.line), 'border-radius': sz(16) };
// homepage purpose chips; on a dark band they take a translucent white
const chips = (items, dark) => row({ gap: sz(8) }, items.map(([en, zh]) => row({ 'align-items': str('center'), gap: sz(8), padding: dims(10, 18), 'border-radius': sz(999), background: bg(dark ? C.white14 : C.fill), 'flex-wrap': str('nowrap') }, [
  P([en, zh], { 'font-weight': str('500'), 'line-height': sz(1.4, 'em'), color: col(dark ? C.white : C.ink) }),
  P([zh, en], { 'font-size': sz(15), 'line-height': sz(1.4, 'em'), color: col(dark ? C.nightText : C.muted) }),
], { title: `chip ${en}` })), { title: 'chips' });
// homepage "Plan a visit & prayer" cards; on a light band the second card is an ink outline
const invite = (href, title, sub, filled) => row(merge({ 'justify-content': str('space-between'), 'align-items': str('center'), gap: sz(20), padding: dims(24, 28), 'border-radius': sz(20), 'flex-wrap': str('nowrap'), 'text-decoration': str('none') },
  filled ? { background: bg(C.accent), hover: { background: bg(C.accentHover) } } : { ...border(1, C.ink), hover: { background: bg(C.fill) } }), [
  box({ gap: sz(4) }, [
    P(title, { 'font-size': sz(24), 'font-weight': str('500'), 'letter-spacing': sz(-0.01, 'em'), 'line-height': sz(1.3, 'em'), color: col(filled ? C.white : C.ink) }),
    P(sub, { 'font-size': sz(15), color: col(filled ? C.nightText : C.muted) }),
  ], { title: 'text' }),
  P('→', { 'font-size': sz(24), color: col(filled ? C.white : C.ink) }),
], { href, title: `invitation ${title[0]}` });
// the closing band: light, so the dark footer stands on its own
const closing = (id, kids) => section(id, 'Closing', { background: bg(C.surface), ...topLine(1, C.line) }, kids);
// Give, slim: the homepage airmail envelope at a third of its height (stripes and stamp frame in style.css), to close a page.
// Workbook section 10; "Why we give" differs between EN and 中 (Question 9), each shows its own.
const giveRow = (label, value) => box(merge({ padding: dims(10, 0), gap: sz(2) }, border({ 'block-start': 0, 'inline-end': 0, 'block-end': 1, 'inline-start': 0 }, C.line)), [P(label, merge(T.label, { color: col(C.muted) })), value], { title: `give ${label[0]}` });
const giveSlim = () => closing('give', [box({ width: sz(100, '%'), padding: sz(10), 'border-radius': sz(6) }, [
  box({ position: str('relative'), background: bg(C.surface), 'border-radius': sz(2), padding: dims('clamp(24px, 3vw, 36px)', 'clamp(20px, 3.5vw, 44px)') }, [
    box({}, [img(546, { width: sz(52), height: sz(66) }, 'steeple stamp')], { id: 'mpcbc-stamp-slim', title: 'stamp' }),
    row({ 'align-items': str('center'), gap: gap2(24, 48) }, [
      box({ flex: flex(1, 1, '18rem'), padding: dims(0, 72, 0, 0) }, [
        H('h2', ['Give', '奉獻'], T.h2), P(['奉獻', 'Give'], T.alt),
        P(['We are grateful for the tithes and offerings of our members and the donations of our community. Your giving ensures MPCBC can continue doing God’s work.', '奉獻是基督徒的權利、義務和蒙福的管道，信徒樂意的奉獻，必為神所悅納。'], { margin: dims(12, 0, 0, 0), 'max-width': sz(40, 'em'), color: col(C.muted) }),
      ], { title: 'why we give' }),
      box(merge({ flex: flex(1, 1, '22rem') }, border({ 'block-start': 1, 'inline-end': 0, 'block-end': 0, 'inline-start': 0 }, C.line)), [
        giveRow(['By check', '支票'], P(['Payable to MPCBC · mail to 302 W. Commonwealth Ave., Alhambra, CA 91801', '支票抬頭請寫 MPCBC · 郵寄至 302 W. Commonwealth Ave., Alhambra, CA 91801'], { 'font-size': sz(18), 'font-weight': str('500'), color: col(C.ink) })),
        giveRow(['Online', '網上奉獻'], row({ 'align-items': str('center'), gap: sz(6) }, [P(['PayPal ·', 'PayPal ·'], { 'font-size': sz(18), 'font-weight': str('500'), color: col(C.ink) }), textLink('mailto:donations@mpcbc.net', 'donations@mpcbc.net', C.ink, { 'font-size': sz(18), 'min-height': sz(32), 'text-decoration': str('underline') })], { title: 'paypal' })),
      ], { title: 'how to give' }),
      box({ flex: flex(0, 0, 'auto') }, [button('/give/', ['Give', '奉獻'])], { title: 'give button' }),
    ], { title: 'envelope content' }),
  ], { title: 'envelope paper' }),
], { id: 'mpcbc-envelope-slim', title: 'envelope (slim)' })]);

// =====================================================================================================
// About Us (/about/, workbook section 2: Our story → Our mission → What we believe)
// =====================================================================================================
const faith = [ // English: the current site's 8 articles; 繁: the current Chinese statement of faith (Question 2 pending)
  ['God', '神', 'God is the Creator and Ruler of the universe. He exists eternally as three persons: the Father, the Son and the Holy Spirit. These three are equal and are one God.', '神是宇宙的創造者與主宰，祂以三位一体永遠存在:聖父、聖子、聖靈。三位同等為一神。',
    'Genesis 1:1,26,27, 3:22, Psalms 90:2; Matthew 28:19; 1 Peter 1:2; 2 Corinthians 13:14', '創1:1,26,27, 3:22, 詩90:2; 太28:19; 彼前1:2; 林後13:14'],
  ['Jesus Christ', '耶穌基督', 'Jesus Christ is the Son of God and is equal with the Father. Jesus lived a sinless life on earth and offered Himself as the perfect sacrifice, dying on the cross for all humanity. He rose from the dead after three days, demonstrating His power over sin and death. He ascended to heavenly glory and will return to earth as King of kings and Lord of lords.',
    '耶穌基督是神的兒子，與父同等。耶穌曾在地上無罪活過人生，並將自己獻上成為最完美的祭，為所有的人類死在十字架上。三天後從死裡復活，彰顯祂勝過罪與死的權能。祂升到天上的榮耀，有一天要回到地上來作萬王之王、萬主之主。',
    'Matthew 1:22-23; Isaiah 9:6; John 1:1-5, 14:10-30; Hebrews 4:14,15; 1 Corinthians 15:3-4; Romans 1:3-4; Acts 1:9-11; 1 Timothy 6:14; Titus 2:13', '太1:22-23; 賽9:6; 約1:1-5, 14:10-30; 來4:14,15; 林前15:3-4; 羅1:3-4; 徒1:9-11; 提前6:14; 多2:13'],
  ['The Holy Spirit', '聖靈', 'The Holy Spirit is equal with the Father and the Son. He is present in the world to help people know Christ, and He lives in every Christian from the moment of salvation. He gives believers power for daily living, understanding of spiritual truth and guidance for the way ahead. He gives spiritual gifts to every believer. As Christians, we seek daily to live under the Holy Spirit’s direction.',
    '聖靈與聖父聖子同等。祂臨在世上為要幫助人認識基督。並且在人得救那一刻，祂便住在每一個基督徒裡面。祂供應每個基督徒每天生活的能力，對於屬靈真理的理解力，以及前行的引導。祂賞賜屬靈恩賜給每個得救的信徒。作為基督徒，我們每天尋求過一個聖靈管理的生活。',
    '2 Corinthians 3:17; John 16:7-13, 14:16-17; Acts 1:8; 1 Corinthians 2:12; 3:16 Ephesians 1:13,5:18; Galatians 5:25', '林後3:17; 約16:7-13, 14:16-17; 徒1:8; 林前2:12; 3:16 弗1:13,5:18; 加5:25'],
  ['The Bible', '聖經', 'The Bible is God’s Word to us, written by people under the supernatural guidance of the Holy Spirit. It is the highest authority for Christian faith and life.', '聖經是神給我們的話，是藉著聖靈超然的引導，由人寫成。是基督徒信仰與生活的最高準則。',
    '2 Timothy 3:16,1:13; 2 Peter 1:20-21; Psalms 119:105, 160, 12:6; Proverbs 30:5', '提後3:16,1:13; 彼後1:20-21; 詩119:105, 160, 12:6; 箴30:5'],
  ['Humanity', '人類', 'Human beings were created in God’s spiritual image, reflecting His character. Humanity is the pinnacle of God’s creation. Although every person has the potential for good, our sinful attitudes separate us from God and bring many problems into our lives.', '人類是照神屬靈的形象所造，本有神的屬性。人是神創造的極致。雖然人本有善的潛能，我們每個人卻都因著罪的態度與神隔絕，而使得生命陷入種種問題。',
    'Genesis 1:27; Psalms 8:3-6; Isaiah 53:6a; Romans 3:23; Isaiah 59:1-2', '創1:27; 詩8:3-6; 賽53:6a; 羅3:23; 賽59:1-2'],
  ['Salvation', '救恩', 'Salvation is God’s free gift, which we must receive. We can never atone for our sins through good works or self-improvement. Only through Jesus Christ and God’s forgiveness can we be delivered from the penalty of sin. We are saved when we turn in faith to Jesus from a self-centred life lived in our own strength. Eternal life begins when we receive the life of Jesus Christ by faith.',
    '救恩是神給我們白白的禮物，但需要我們去接受。我們永遠無法靠著善行與自我改善來贖我們的罪。惟有靠著耶穌基督，蒙神赦免，才能從罪的刑罰得贖。當我們從以自我為中心、靠自己的生命，憑信心轉向耶穌時，我們便得救。永遠的生命開始於我們憑信心接受耶穌基督的生命那一刻。',
    'Romans 6:23,5:1; Ephesians 2:8-9; John 14:6;1:12; Titus 3:5; Galatians 3:26', '羅6:23,5:1; 弗2:8-9; 約14:6;1:12; 多3:5; 加3:26'],
  ['Eternal security', '永遠的保障', 'Because God gives us eternal life through Jesus Christ, true believers have eternal assurance of salvation. Salvation comes through God’s power and grace, not through a Christian’s own efforts. His preserving power and grace give us this lasting assurance. Believers also have a responsibility to continue trusting God and living in His care.',
    '由於神通過耶穌基督賜給我們永遠的生命，真信徒有永遠的得救保障。救贖是藉著神的權能與恩典，不是憑基督徒自己的努力。神保守的權能與恩典使我們有這永遠的保障。但信徒也有責任不斷信靠神，活在神的保守之中。',
    'John 10:29; 2 Timothy 1:12; Hebrews 7:25, 10:10,14; 1 Peter 1:3-5; Hebrews 6:4-8; Hebrews 10:26-27; John 15:6', '約10:29; 提後1:12; 來7:25, 10:10,14; 彼前1:3-5; 來6:4-8; 來10:26-27; 約15:6'],
  ['Eternity', '永恆', 'People were created to exist forever. We will either be eternally separated from God because of sin, or live eternally with Him through forgiveness and salvation. Eternal separation from God is hell; eternal life is being with God forever.', '人被創造要存到永遠。我們或要因著罪與神永遠隔離，或是因著赦免與救恩與神永遠同在。與神永遠隔離便是地獄，與神永遠同在便是永遠的生命。',
    'John 3:16, 2:25, 5:11-13; Romans 6:23; Revelation 20:15; Matthew 1:8; Matthew 2:44,46', '約3:16, 2:25, 5:11-13; 羅6:23; 啟20:15; 太1:8; 太2:44,46'],
];
// native Atomic Tabs: the tab list styled as homepage chips (selected = ink), the panel as a homepage card
const tabStyle = { display: str('flex'), 'justify-content': str('space-between'), 'align-items': str('center'), gap: sz(12), 'min-height': sz(52), padding: dims(0, 20),
  'border-radius': sz(999), 'border-width': sz(0), background: bg(C.fill), color: col(C.ink), width: sz(100, '%'), m: { width: auto(), 'min-height': sz(44), padding: dims(0, 16) } };
const tab = ([en, zh]) => node('e-tab', {}, tabStyle, [row({ 'justify-content': str('space-between'), 'align-items': str('center'), gap: sz(12), width: sz(100, '%'), 'flex-wrap': str('nowrap') }, [
  // the labels inherit the tab's colour, so the selected tab (white on ink) recolours them
  P([en, zh], { 'font-weight': str('500'), color: { $$type: 'color', value: 'inherit' } }),
  P([zh, en], { 'font-size': sz(15), color: { $$type: 'color', value: 'inherit' }, m: { display: str('none') } }),
], { title: `tab ${en}` })], `tab ${en}`);
const selectTab = (n) => { // the selected tab: ink fill, white text (Atomic state e--selected)
  Object.values(n.styles)[0].variants.push({ meta: { breakpoint: 'desktop', state: 'e--selected' }, props: { background: bg(C.ink), color: col(C.white) }, custom_css: null });
  return n;
};
const faithTabs = node('e-tabs', { 'default-active-tab': num(0), ...cssid('mpcbc-faith') }, { display: str('grid'), 'grid-template-columns': str('minmax(0, 4fr) minmax(0, 8fr)'), gap: gap2(24, 48), width: sz(100, '%'), padding: sz(0), m: { 'grid-template-columns': str('minmax(0, 1fr)') } }, [
  node('e-tabs-menu', {}, { display: str('flex'), 'flex-direction': str('column'), gap: sz(6), padding: sz(0), m: { 'flex-direction': str('row'), 'flex-wrap': str('wrap') } }, faith.map((a) => selectTab(tab(a))), 'tab list'),
  node('e-tabs-content-area', {}, { padding: sz(0) }, faith.map(([en, zh, text, textZh, refs, refsZh]) => node('e-tab-content', {}, { padding: sz(0) }, [
    box(merge(card, { padding: cu('clamp(28px, 3vw, 44px)') }), [
      H('h3', [en, zh], T.card),
      P([zh, en], merge(T.alt, { 'font-size': sz(18), margin: dims(2, 0, 0, 0) })),
      P([text, textZh], { margin: dims(20, 0, 0, 0), 'max-width': sz(52, 'em'), 'font-size': cu('clamp(18px, 1rem + 0.3vw, 21px)'), 'line-height': sz(1.6, 'em'), color: col(C.ink) }),
      P([refs, refsZh], merge({ margin: dims(24, 0, 0, 0), padding: dims(16, 0, 0, 0), 'font-size': sz(15), color: col(C.muted) }, topLine(1, C.line))),
    ], { title: `article ${en}` }),
  ], `panel ${en}`)), 'panels'),
], 'Statement of faith (tabs)');

// story: the statement and two columns; <strong> / <em> are the highlights (style.css), English only — the 繁 slot is plain text
const leaders = [ // workbook section 3: full bios, EN and 繁 (photos for Ed and Lili still to come)
  [IMG.caleb, ['Senior Pastor', '主任牧師'], ['Rev. Caleb Cheung', '張健庭牧師'],
    ['Pastor Caleb Cheung is from Hong Kong. He studied theology at Fuller Theological Seminary, then pursued doctoral studies in theology and philosophy of religion at Claremont Graduate University. He stayed on to serve and shepherd MPCBC, where he is now Senior Pastor.',
      '張健庭牧師來自香港，多年前蒙召在福樂神學院（Fuller Theological Seminary）念神學，繼在克萊蒙研究大學（Claremont Graduate University）進修博士課程專修神學及宗教哲學，後留在本教會事奉與牧養，現任本教會主任牧師。']],
  [IMG.pastorPh, ['English Pastor', '英語牧師'], ['Rev. Edward Lu', '呂德勝牧師'],
    ['Ed seeks to nurture faith, equip leaders, and disciple the next generation through meaningful relationships and shared ministry. He holds a B.S. in Ministry Leadership and earned his Master of Divinity and Doctor of Ministry from Moody Theological Seminary.',
      '呂德勝牧師致力透過真誠的關係與同工事奉，培育信心、裝備領袖，並門訓下一代。他持有事工領導學士學位，並在慕迪神學院（Moody Theological Seminary）取得道學碩士及教牧學博士學位。']],
  [IMG.pastorPh, ['Family and Children Minister', '家庭及兒童事工傳道'], ['Min. Lili Tian', '田俐力傳道'],
    ['Min. Lili Tian graduated from Gateway Seminary in 2024 with a Master of Theological Studies. She had an obvious calling from God to serve in family and children’s ministry. God has granted her spiritual gifts and full passion for children and family. She is willing to be used by God greatly in serving every child and family.',
      '田俐力傳道於2024年畢業於金門神學院（Gateway Seminary）神學碩士專業。她受到神明確的呼召侍奉家庭與兒童事工。神恩賜給她屬靈的恩賜以及對於家庭和孩子的滿腔熱忱。她願意被神大大使用來服侍每一個孩子與家庭。']],
];
const deacons = [['Charles Leung', 'Charles Leung'], ['Jeff Cha', 'Jeff Cha'], ['Evelyn Hsu', 'Evelyn Hsu'], ['Esther Chan', '張陳一萍'], ['Morgan Lam', '林斯勤']]
  .map((n) => [n, ['[Role]', '[職分]']]);
const storyText = { 'font-size': cu('clamp(20px, 1.6vw, 24px)'), 'line-height': sz(1.5, 'em'), color: col(C.ink) };
const about = [
  heroSplit({ label: ['Since 1977', '自1977年'], h1: ['About Us', '關於我們'],
    lead: ['One church in three languages, Cantonese, Mandarin and English, united as one church in Christ.', '一個教會，三種語言：粵語、國語及英語，在基督裏合而為一。'],
    buttons: [button('/visit/', ['Plan a Visit', '計劃來訪']), button('#beliefs', ['Our Beliefs', '我們的信仰'], 'line')], image: IMG.church }),
  section('story', 'Our story', { background: bg(C.surface), ...topLine(1, C.line) }, [
    box({ margin: dims(0, 0, 'clamp(40px, 5vw, 64px)', 0) }, [H('h2', ['Our story', '教會歷史'], T.h2), P(['教會歷史', 'Our story'], T.alt)], { title: 'heading Our story' }),
    P(['Founded in <strong>1977</strong>, MPCBC began as a Cantonese-speaking congregation.', '教會成立於1977年。教會剛開始是使用粵語崇拜。'],
      { 'font-size': cu('clamp(1.5rem, 1rem + 1.4vw, 2.25rem)'), 'font-weight': str('500'), 'line-height': sz(1.25, 'em'), 'letter-spacing': sz(-0.01, 'em'), 'max-width': sz(30, 'em'), color: col(C.ink) }, 'mpcbc-story-lead'),
    row({ gap: gap2(24, 48), margin: dims('clamp(40px, 5vw, 64px)', 0, 0, 0), 'align-items': str('flex-start') }, [
      box({ flex: flex(1, 1, '22rem') }, [P(['During the 1980s, as the <em>Mandarin-speaking immigrant community</em> in Los Angeles grew rapidly, the church recognized the need to share the gospel and provide a spiritual home for these new immigrants. In response, MPCBC established its Mandarin Ministry, holding its <strong>first Mandarin worship service in 1988</strong>.',
        '但從八十年代起，教會看到講國語的新移民人數在洛杉磯迅速增長，考慮到新移民對基督福音的需要。於是，教會決定開展國語事工，為新移民群體預備一個屬靈的家，於1988年開始了國語崇拜。'], storyText, 'mpcbc-story-1')], { title: 'story column 1' }),
      box({ flex: flex(1, 1, '22rem'), gap: sz(16) }, [
        P(['In <strong>1991</strong>, MPCBC expanded its ministry by introducing an <em>English worship service</em>. Since then, the church has continued to worship in <em>three languages</em>, serving different language communities while remaining united as one church in Christ.',
          '之後，在1991年教會又增設了英語的崇拜，直到如今，教會一直保持有三種語言的崇拜。'], storyText, 'mpcbc-story-2'),
        row({}, [button('/visit/', ['Come and worship with us this Sunday.', '歡迎您這個主日與我們一同敬拜。'], 'accent', { 'min-height': sz(36), padding: dims(2, 8), 'border-radius': sz(0), 'font-size': cu('clamp(20px, 1.6vw, 24px)') })], { title: 'invitation' }),
      ], { title: 'story column 2' }),
    ], { title: 'story columns' }),
  ]),
  section('mission', 'Our mission', { background: bg(C.accent) }, [split([
    [H('h2', ['Our mission', '使命'], merge(T.h2, { color: col(C.white) })), P(['使命', 'Our mission'], merge(T.alt, { color: col(C.nightText) })),
      P(['Knowing God is our foundation, glorifying God is our goal.', '認識神為基礎，榮耀神是目標。'], { margin: dims(28, 0, 0, 0), 'max-width': sz(38, 'em'), 'font-size': cu('clamp(20px, 1.05rem + 0.6vw, 24px)'), 'line-height': sz(1.45, 'em'), color: col(C.white) })],
    [P(['Through worship, fellowship, discipleship, ministry, and evangelism, we seek to know God, and make His name known.', '我們藉著敬拜、團契、門徒訓練、事奉、傳福音來認識和榮耀神。'], { 'font-size': sz(19), 'max-width': sz(40, 'em'), color: col(C.white) }),
      box({ margin: dims(24, 0, 0, 0) }, [chips([['Worship', '敬拜'], ['Fellowship', '團契'], ['Discipleship', '門徒訓練'], ['Ministry', '事奉'], ['Evangelism', '傳福音']], true)], { title: 'purposes' })],
  ])]),
  section('beliefs', 'What we believe', {}, [
    headRow(['What we believe', '我們的信仰'], P(['We believe in Jesus, God’s only Son, who died on a cross for our sins, was raised to life, and will one day return. We believe in the Trinity: Father, Son, and Holy Spirit. We teach the Bible, believing that it is from God, entirely true, and relevant to our present age.', '[我們的信仰摘要（中文待提供）]'], { 'max-width': sz(46, 'em'), color: col(C.ink) })),
    faithTabs,
  ]),
  // Pastors & Leadership (workbook section 3, merged into About Us): the homepage pastor cards with the full bios, then the deacons
  section('leadership', 'Pastors & Leadership', { background: bg(C.surface), ...topLine(1, C.line) }, [
    headRow(['Pastors & Leadership', '教牧同工']),
    grid3(leaders.map(([imgId, role, [en, zh], bio]) => box(merge(card, { background: bg(C.bg), padding: dims(12, 12, 24, 12), gap: sz(16) }), [
      box({ 'border-radius': sz(10), overflow: str('hidden') }, [img(imgId, { 'aspect-ratio': str('1 / 1') }, `portrait ${en}`)], { title: 'photo' }),
      box({ padding: dims(0, 12), gap: sz(6) }, [
        P(role, T.label), H('h3', [en, zh], T.card),
        box({ height: sz(1), background: bg(C.line), margin: dims(10, 0, 6, 0) }, [], { title: 'divider' }),
        P(bio, { color: col(C.muted) }),
      ], { title: 'pastor text' }),
    ], { title: `pastor ${en}` }))),
    box(merge(card, { margin: dims(20, 0, 0, 0), padding: dims(18, 26) }), [
      row({ 'align-items': str('center'), gap: sz(10) }, [H('h3', ['Deacons', '執事'], { 'font-size': sz(19), 'font-weight': str('600'), color: col(C.ink) }), P(['[Show deacons? Question 4]', '[是否列出執事？問題 4]'], { color: col(C.muted) })], { title: 'deacons head' }),
      box({ margin: dims(12, 0, 0, 0) }, deacons.map(([name, role]) => row(merge({ 'justify-content': str('space-between'), gap: gap2(2, 16), padding: dims(12, 0) }, topLine(1, C.line)), [
        P(name, { 'font-weight': str('500'), color: col(C.ink) }), P(role, { color: col(C.muted) }),
      ], { title: `deacon ${name[0]}` })), { title: 'deacons list' }),
    ], { title: 'deacons' }),
  ]),
  closing('visit', [split([
    [H('h2', ['Plan a visit & prayer', '計劃來訪及代禱'], T.h2), P(['計劃來訪及代禱', 'Plan a visit & prayer'], T.alt),
      P(['Whether it’s your first Sunday or you need someone to pray with you, we’d love to hear from you.', '無論是第一次來訪，或需要有人與您一同禱告，我們都很樂意與您聯絡。'], { margin: dims(22, 0, 0, 0), 'max-width': sz(40, 'em'), 'font-size': cu('clamp(18px, 1rem + 0.35vw, 21px)'), color: col(C.ink) })],
    [box({ gap: sz(12) }, [
      invite('/visit/?reason=visit#contact', ['Plan a Visit', '計劃來訪'], ['Tell us you’re coming and we’ll look out for you.', '告訴我們您會來，我們會預備迎接您。'], true),
      invite('/visit/?reason=prayer#contact', ['Prayer request', '代禱'], ['A pastor reads every request and keeps it private.', '牧師會親自閱讀每一個代禱事項，並予以保密。'], false),
    ], { title: 'invitations' })],
  ])]),
];

// =====================================================================================================
// English Congregation (/english/, workbook section 5)
// =====================================================================================================
const groups = [ // workbook section 5 table (merged from the Fellowship & Ministries sub-page); places for the Sunday classes from the mockup; blanks stay bracketed
  [['Sun', '主日'], ['10:00–10:45 AM', '上午 10:00–10:45'], ['English Sunday School', '英語主日學'], ['All adults · Fellowship Hall', '所有成人 · 副堂']],
  [['Sun', '主日'], ['10:00–10:45 AM', '上午 10:00–10:45'], ['Youth Sunday School', '青少年主日學'], ['Middle & high school · Education Annex', '初中及高中 · 教育樓']],
  [['Sun', '主日'], ['11:00 AM–12:00 PM', '上午 11:00–12:00'], ['Jr. High Faith and Life Hour', '[Jr. High Faith and Life Hour 中文名稱]'], ['Junior high · Education Annex', '初中 · 教育樓']],
  [['Mon', '星期一'], ['6:30–9:00 PM', '晚上 6:30–9:00'], ['College/Career Fellowship', '[大專及在職團契中文名稱]'], ['College students & young adults · West Covina (please verify)', '大專生及青年 · West Covina（待確認）']],
  [['Thu', '星期四'], ['7:30–9:00 PM (please verify)', '晚上 7:30–9:00（待確認）'], ['Adult Bible Study and Prayer Meeting', '[成人查經及禱告會中文名稱]'], ['Adults · Online (please verify)', '成人 · 網上（待確認）']],
  [['Daily', '每日'], ['YouVersion app (please verify it is still running)', 'YouVersion 應用程式（請確認是否仍在進行）'], ['Bible in a Year', '[Bible in a Year 中文名稱]'], ['Everyone', '所有人']],
  [['[Day]', '[日期]'], ['[Time]', '[時間]'], ['Women’s Fellowship', '[婦女團契中文名稱]'], ['Women · [Place · contact]', '婦女 · [地點 · 聯絡]']],
];
// weekly timetable: one card, a block per day (the day large on the left), a slim row per group; rows stack on phones
const days = groups.reduce((acc, g) => { const last = acc.at(-1); if (last && last[0][0] === g[0][0]) last[1].push(g); else acc.push([g[0], [g]]); return acc; }, []);
const gRow = ([, time, name, sub], i) => row(merge({ 'align-items': str('flex-start'), gap: gap2(2, 24), padding: dims(12, 0) }, i ? topLine(1, C.line) : {}), [
  P(time, { flex: flex(0, 0, '11rem'), 'font-size': sz(15), 'font-weight': str('500'), color: col(C.accent), m: { flex: flex(1, 1, '100%') } }),
  H('h3', name, { flex: flex(1, 1, '14rem'), 'font-size': sz(19), 'font-weight': str('500'), 'line-height': sz(1.35, 'em'), color: col(C.ink) }),
  P(sub, { flex: flex(1, 1, '14rem'), color: col(C.muted) }),
], { title: 'group ' + name[0] });
const timetable = box(merge(card, { padding: dims(4, 'clamp(18px, 3vw, 32px)') }), days.map(([day, list], i) => row(merge({ gap: gap2(4, 24), padding: dims(16, 0) }, i ? topLine(1, C.line) : {}), [
  P(day, { flex: flex(0, 0, '6rem'), padding: dims(10, 0, 0, 0), 'font-size': sz(28), 'font-weight': str('500'), 'letter-spacing': sz(-0.02, 'em'), 'line-height': sz(1, 'em'), color: col(C.ink), m: { flex: flex(1, 1, '100%') } }),
  box({ flex: flex(1, 1, '24rem') }, list.map(gRow), { title: 'groups' }),
], { title: 'day ' + day[0] })), { id: 'mpcbc-timetable', title: 'weekly timetable' });
const eventCard = (n) => box(merge(card, { padding: dims(12, 12, 22, 12), gap: sz(14), 'text-decoration': str('none') }), [
  box({ 'border-radius': sz(10), overflow: str('hidden') }, [img(IMG.eventPh, { 'aspect-ratio': str('3 / 2') }, `event ${n} flyer`)], { title: 'flyer' }),
  box({ padding: dims(0, 10), gap: sz(6) }, [P(['[Date] · [Time] · [Place]', '[日期] · [時間] · [地點]'], T.label), H('h3', [`[Event ${n} title]`, `[活動 ${n} 標題]`], T.card), P(['[One sentence]', '[一句簡介]'], { color: col(C.muted) })], { title: 'event text' }),
], { href: '/news/', title: `event ${n}` });
const kidsBand = (id, kids, style = {}) => section(id, 'Kids band', merge({ background: bg(C.kids), position: str('relative'), padding: dims('clamp(104px, 12vw, 150px)', 20) }, style), kids); // waves: [id^="mpcbc-kids-band"] in style.css
const english = [
  heroVideo({ label: ['English Congregation · 英語堂', '英語堂 · English Congregation'], h1: ['Connect Ministry', 'Connect Ministry（英語堂）'],
    lead: ['A church family where you can know God, grow in faith, and belong.', '一個讓您認識神、在信心中成長、找到歸屬的教會家庭。'],
    facts: [['11:00', ['AM · Sunday Worship', '主日崇拜']], ['10:00', ['AM · Sunday School', '主日學']]],
    buttons: [button('/visit/', ['Plan a Visit', '計劃來訪']), button('#sermon', ['Sermons & Livestream', '講道及直播'], 'whiteLine')] }),
  // Who we are (workbook Intro + Who we are) beside a rounded photo
  section('who', 'Who we are', { background: bg(C.surface), ...topLine(1, C.line) }, [split([
    [H('h2', ['Who we are', '認識我們'], T.h2), P(['認識我們', 'Who we are'], T.alt),
      P(['Connect Ministry is the English-language congregation of Monterey Park Chinese Baptist Church. We are a multigenerational church family growing together through worship, biblical teaching, fellowship, and service.', 'Connect Ministry（英語堂）是慕道園華人浸信會的英語會眾。我們是一個跨越世代的教會家庭，透過敬拜、聖經教導、團契和服事一同成長。'],
        { margin: dims(24, 0, 0, 0), 'font-size': cu('clamp(19px, 1rem + 0.4vw, 22px)'), 'line-height': sz(1.5, 'em'), 'max-width': sz(46, 'em'), color: col(C.ink) }),
      P(['We seek to connect to our wonderful Lord Jesus through inspiring worship and the preaching of His Word, the Bible. We also seek to connect with each other through various Small Groups and Fellowships. Connect Ministry is a diverse group of varying ages, ethnicities, and backgrounds. We care about your forgiveness and restoration, and about your kids and youth.',
        '我們盼望透過激勵人心的敬拜和神話語——聖經——的宣講，與我們奇妙的主耶穌連結；也透過各小組和團契彼此連結。Connect Ministry 由不同年齡、族裔和背景的人組成。我們關心您能得著赦免與恢復，也關心您的孩子和青少年。'],
        { margin: dims(16, 0, 0, 0), 'max-width': sz(50, 'em'), color: col(C.muted) })],
    [box({ 'border-radius': sz(24), overflow: str('hidden') }, [img(IMG.sanctuaryVbs, { 'aspect-ratio': str('4 / 3') }, 'families in the sanctuary')], { title: 'photo' })],
  ])]),
  // Pastor welcome: a statement band on night blue
  section('welcome', 'Pastor welcome', { background: bg(C.night) }, [
    P('“', { 'font-size': sz(96), 'font-weight': str('700'), 'line-height': sz(0.6, 'em'), height: sz(34), color: col(C.gold) }),
    P(['Whether you are exploring Christianity, returning to church, or looking for a church family, we invite you to come as you are. Our desire is to help people grow in faith, build meaningful relationships, and use their gifts to serve God and others.',
      '無論您正在認識基督信仰、重回教會，或是在尋找一個教會家庭，我們都邀請您以真實的自己前來。我們盼望幫助人在信心中成長、建立有意義的關係，並運用恩賜服事神和他人。'],
      { margin: dims(16, 0, 0, 0), 'font-size': cu('clamp(1.6rem, 1.05rem + 1.9vw, 2.75rem)'), 'font-weight': str('500'), 'line-height': sz(1.3, 'em'), 'letter-spacing': sz(-0.015, 'em'), 'max-width': sz(28, 'em'), color: col(C.white) }),
    row({ 'align-items': str('center'), gap: sz(16), margin: dims(32, 0, 0, 0), 'flex-wrap': str('nowrap') }, [
      box({ width: sz(64), flex: flex(0, 0, 'auto'), 'border-radius': sz(999), overflow: str('hidden') }, [img(IMG.pastorPh, { 'aspect-ratio': str('1 / 1') }, 'Pastor Ed Lu (placeholder)')], { title: 'pastor photo' }),
      box({}, [P(['Pastor Ed Lu', '呂德勝牧師'], { 'font-weight': str('600'), color: col(C.white) }), P(['English Pastor', '英語牧師'], { color: col(C.nightText) })], { title: 'pastor name' }),
    ], { title: 'pastor' }),
  ]),
  section('groups', 'Fellowship & Ministries', {}, [
    headRow(['Fellowship & Ministries', '團契及事工']),
    timetable,
  ]),
  // on the night band (approved mockup): white heading, a translucent card
  section('sermon', 'Sermons & Livestream', { background: bg(C.night), ...topLine(1, C.white14) }, [
    row({ 'justify-content': str('space-between'), 'align-items': str('flex-end'), gap: gap2(16, 48), margin: dims(0, 0, 44, 0) }, [
      box({}, [H('h2', ['Sermons & Livestream', '講道及直播'], merge(T.h2, { color: col(C.white) })), P(['講道及直播', 'Sermons & Livestream'], merge(T.alt, { color: col(C.nightText) }))], { title: 'heading Sermons' }),
      textLink('/english/sermons/', ['Watch past sermons →', '重溫講道 →'], C.white),
    ], { title: 'section head' }),
    row({ background: { $$type: 'background', value: { color: { $$type: 'color', value: 'rgba(255,255,255,0.06)' } } }, ...border(1, C.white14), 'border-radius': sz(16), padding: dims(12, 12, 24, 12), 'align-items': str('center'), gap: gap2(24, 40) }, [
      box({ flex: flex(7, 1, '22rem') }, [{ id: hex(4), elType: 'widget', widgetType: 'shortcode', settings: { shortcode: '[mpcbc_live ministry="english" part="media"]' }, elements: [] }], { title: 'player' }),
      box({ flex: flex(5, 1, '18rem'), padding: dims(0, 12), gap: sz(6) }, [
        P(['11:00 AM · Fellowship Hall', '上午 11:00 · 副堂'], merge(T.label, { color: col(C.nightText) })),
        H('h3', ['English Worship', '英語崇拜'], merge(T.card, { 'font-size': sz(26), 'line-height': sz(1.2, 'em'), color: col(C.white) })),
        P(['Live here every Sunday at 11:00 AM; the latest service plays in between.', '每主日上午11時在此直播；其他時間播放最近一次崇拜。'], { margin: dims(10, 0, 0, 0), color: col(C.nightText) }),
      ], { title: 'service text' }),
    ], { title: 'service card' }),
  ]),
  // Upcoming events: the next 2–3 for this congregation (workbook: auto from News & Events by congregation tag; placeholders until then)
  section('events', 'Upcoming events', { background: bg(C.surface) }, [
    headRow(['Upcoming events', '本會眾最近活動'], textLink('/news/', ['News & Events →', '消息及活動 →'])),
    grid3([1, 2, 3].map(eventCard)),
  ]),
  kidsBand('mpcbc-kids-band', [split([
    [H('h2', ['Kids during the service', '崇拜期間的兒童事工'], T.h2), P(['崇拜期間的兒童事工', 'Kids during the service'], T.alt),
      P(['[Is MPCBC KIDS running during this service? e.g. “Children’s Sunday School runs during this service, 1st floor, Education Annex.”]', '[崇拜期間是否有兒童事工？例如：「兒童主日學在崇拜期間於教育樓一樓進行。」]'], { margin: dims(24, 0, 0, 0), 'font-size': sz(19), 'max-width': sz(44, 'em'), color: col(C.ink) }),
      row({ margin: dims(32, 0, 0, 0) }, [button('/kids/', 'MPCBC KIDS →', 'ink')], { title: 'button' })],
    [box({ background: bg(C.surface), padding: dims(12, 12, 18, 12), 'border-radius': sz(6), transform: rot(2.5) }, [img(IMG.vbsFloor, { 'aspect-ratio': str('4 / 3'), 'border-radius': sz(2) }, 'kids photo')], { id: 'mpcbc-kids-photo-3', title: 'photo frame' })],
  ])]),
  giveSlim(),
];

// =====================================================================================================
// MPCBC KIDS (/kids/, workbook section 8; hero line and facts from Home section 4)
// =====================================================================================================
// white photo frame, tilted (shadow + hover lift: [id^="mpcbc-kids-photo-"] in style.css)
const kframe = (id, kid, style = {}, pad = dims(12, 12, 18, 12)) => box(merge({ background: bg(C.surface), padding: pad, 'border-radius': sz(6) }, style), [kid], { id, title: `frame ${id}` });
// the big hero frame: a silent loop (placeholder: the homepage church video until a 5–10 s Kids clip exists); its pause button stays
const { classes: _c, ...clipSettings } = structuredClone(homeHero.settings);
const heroClip = node('e-background-video', { ...clipSettings, _cssid: str('mpcbc-kids-clip') }, { width: sz(100, '%'), 'aspect-ratio': str('4 / 3'), overflow: str('hidden'), position: str('relative'), padding: sz(0), 'border-radius': sz(2), display: str('flex') }, [
  node('e-background-video-content', {}, { width: sz(100, '%'), height: sz(100, '%'), padding: sz(0) }, [], 'clip content'),
  node('e-background-video-controls', {}, { position: str('absolute'), 'inset-inline-end': sz(10), 'inset-block-end': sz(10), 'z-index': num(3), padding: sz(0), display: str('flex') }, [], 'clip controls'),
], '[Kids clip, 5–10 s]');
const kidsStatement = { 'font-size': cu('clamp(1.75rem, 1.1rem + 2.2vw, 3.1rem)'), 'font-weight': str('500'), 'line-height': sz(1.12, 'em'), 'letter-spacing': sz(-0.02, 'em'), color: col(C.ink) };
const ktile = (color, kids, style = {}) => box(merge({ background: bg(color), 'border-radius': sz(24), padding: dims(26, 22), 'min-height': sz(150), 'justify-content': str('flex-end') }, style), kids, { title: 'tile' });
const word = (color, s, deg, dark) => ktile(color, [P(s, { 'font-size': cu('clamp(22px, 1.1rem + 0.8vw, 30px)'), 'font-weight': str('600'), 'letter-spacing': sz(-0.015, 'em'), 'line-height': sz(1.15, 'em'), color: col(dark ? C.white : C.ink) })], { transform: rot(deg) });
const pair = (label, a, b) => box({ gap: sz(10) }, [P(label, { 'font-size': sz(19), 'font-weight': str('600'), color: col(C.ink) }),
  node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(2, minmax(0, 1fr))'), gap: sz(12), padding: sz(0) }, [a, b], 'two words')], { title: `pair ${label[0]}` });
const plabel = (s) => P(s, merge(T.label, { color: col(C.ink), margin: dims('clamp(48px, 6vw, 80px)', 0, 14, 0) }));
const classTile = (color, grade, name, sub) => ktile(color, [
  P(grade, { 'font-size': cu('clamp(2.2rem, 1.6rem + 2vw, 3.2rem)'), 'font-weight': str('600'), 'letter-spacing': sz(-0.02, 'em'), 'line-height': sz(1, 'em'), color: col(C.ink), margin: dims(0, 0, 'auto', 0) }),
  H('h3', name, { 'font-size': sz(26), 'font-weight': str('600'), color: col(C.ink), margin: dims(40, 0, 0, 0) }), P(sub, { margin: dims(6, 0, 0, 0), color: col(C.ink) }),
], { padding: sz(28), 'min-height': sz(230), 'justify-content': str('space-between') });
const infoRow = (k, v) => row(merge({ 'justify-content': str('space-between'), gap: gap2(2, 16), padding: dims(12, 0) }, topLine(1, C.line)), [P(k, { 'font-weight': str('500'), color: col(C.ink) }), P(v, { color: col(C.muted) })], { title: `info ${k[0]}` });
const scrapPics = [IMG.vbsWave, IMG.vbsFloor, IMG.sanctuaryVbs, IMG.vbsCrafts, IMG.vbsBoy, IMG.courtyard, IMG.aisle]; // one big + a 3 × 2 grid
const team = [
  [['Family and Children Minister', '家庭及兒童事工傳道'], ['Min. Lili Tian', '田俐力傳道'], leaders[2][3]],
  [['Children Department Co-worker', '兒童部同工'], ['Connie So', '蘇婉儀'], ['She has a Master of Education, holds teaching credentials in Hong Kong and California, and is currently pursuing a Master of Arts in Christian Studies.', '教育學碩士，持有香港與美國加州教師執照，現修讀基督教研究碩士課程。']],
];
const sticker = (big, small, style) => box(merge({ position: str('absolute'), 'z-index': num(4), width: sz(128), height: sz(128), 'border-radius': sz(999), background: bg(C.kids), 'align-items': str('center'), 'justify-content': str('center'), m: { width: sz(92), height: sz(92) } }, style), [
  P(big, { 'font-size': sz(28), 'font-weight': str('600'), 'letter-spacing': sz(-0.02, 'em'), 'line-height': sz(1.05, 'em'), color: col(C.ink), 'text-align': str('center'), m: { 'font-size': sz(20) } }),
  P(small, { 'font-size': sz(14), 'font-weight': str('600'), 'line-height': sz(1.3, 'em'), color: col(C.ink), 'text-align': str('center'), m: { 'font-size': sz(12) } }),
], { id: 'mpcbc-kids-sticker-' + big[0].replace(/[^0-9a-z]/gi, ''), title: 'sticker ' + big[0] });
const kidsPage = [
  // Hero: white; name, the workbook line, one fact line; three frames of different sizes, the video biggest and on top
  section('top', 'Hero', { background: bg(C.surface), overflow: str('hidden'), padding: dims('clamp(48px, 6vw, 80px)', 20, 'clamp(64px, 7vw, 96px)', 20) }, [row({ 'align-items': str('center'), gap: gap2(40, 80) }, [
    box({ flex: flex(4, 1, '24rem') }, [
      H('h1', ['MPCBC KIDS', 'MPCBC KIDS 兒童事工'], merge(T.h2, { 'font-size': cu('clamp(3rem, 1.8rem + 3.4vw, 5rem)'), 'line-height': sz(1.04, 'em'), 'letter-spacing': sz(-0.025, 'em') }), 'mpcbc-kids-h1'),
      // three pieces so "spiritual family" carries the yellow highlight (the 繁 slot holds plain text only)
      row({ margin: dims(18, 0, 0, 0), 'align-items': str('flex-start') }, [
        P(['A', '所有孩子'], kidsStatement),
        P(['spiritual family', '屬靈的家'], merge(kidsStatement, { margin: dims(0, '0.25em') }), 'mpcbc-kids-family-hero'), // yellow underline in style.css
        P(['for all kids.', '。'], kidsStatement),
      ], { title: 'statement' }),
      row({ gap: sz(12), margin: dims(32, 0, 0, 0) }, [button('#classes', ['Sunday School classes', '主日學班級'], 'ink'), button('/visit/', ['Plan a Visit', '計劃來訪'], 'line')], { title: 'buttons' }),
    ], { title: 'hero text' }),
    // the frames sit on a sky panel that runs off the right edge (::before in style.css); the facts are two yellow stickers
    box({ flex: flex(5, 1, '30rem'), position: str('relative'), 'min-height': sz(580), m: { 'min-height': sz(380) } }, [
      sticker(['K–6th', '幼稚園'], ['grade', '至六年級'], { 'inset-inline-end': sz(2, '%'), 'inset-block-start': sz(2, '%'), transform: rot(8) }),
      sticker(['10:00', '10:00'], ['Sundays', '主日'], { 'inset-inline-start': sz(0), 'inset-block-end': sz(6, '%'), transform: rot(-8) }),
      kframe('mpcbc-kids-photo-hero-1', heroClip, { position: str('absolute'), width: sz(72, '%'), 'inset-inline-start': sz(12, '%'), 'inset-block-start': sz(15, '%'), transform: rot(-2), 'z-index': num(3) }),
      kframe('mpcbc-kids-photo-hero-2', img(IMG.vbsBoy, { 'aspect-ratio': str('4 / 3'), 'border-radius': sz(2) }, 'A boy smiling at VBS'), { position: str('absolute'), width: sz(36, '%'), 'inset-inline-start': sz(0), 'inset-block-start': sz(0), transform: rot(-6), 'z-index': num(1) }),
      kframe('mpcbc-kids-photo-hero-3', img(IMG.vbsCrafts, { 'aspect-ratio': str('4 / 3'), 'border-radius': sz(2) }, 'Children holding up paper flowers they made'), { position: str('absolute'), width: sz(46, '%'), 'inset-inline-end': sz(0), 'inset-block-end': sz(2, '%'), transform: rot(4), 'z-index': num(2) }),
    ], { id: 'mpcbc-kids-collage', title: 'photo collage' }),
  ])]),
  // Mission & Vision on yellow. H2 from the workbook row labels; 使命及異象 is our wording (異象 is not in the workbook): review.
  kidsBand('mpcbc-kids-band-purpose', [
    row({ 'align-items': str('center'), gap: gap2(40, 80) }, [
      box({ flex: flex(1, 1, '24rem') }, [
        H('h2', ['Mission & Vision', '使命及異象'], T.h2), P(['使命及異象', 'Mission & Vision'], T.alt),
        P(['Monterey Park Chinese Baptist Church is a Purpose Driven Church. The Children’s Ministry at our church is called “MPCBC KIDS”, and our mission is to balance the purposes of the church in our ministry to children.',
          '慕道園華人浸信會是一個以目標為導向的教會。兒童事工稱為「MPCBC KIDS」，我們的使命是將教會的目標，平衡地貫徹到兒童事工當中。'], { margin: dims(24, 0, 0, 0), 'max-width': sz(46, 'em'), 'font-size': cu('clamp(19px, 1rem + 0.4vw, 22px)'), 'line-height': sz(1.55, 'em'), color: col(C.ink) }),
        P(['Where do our purposes come from?', '我們的目標從何而來呢？'], { margin: dims(14, 0, 0, 0), 'font-size': cu('clamp(22px, 1.1rem + 0.7vw, 28px)'), 'font-weight': str('600'), 'letter-spacing': sz(-0.015, 'em'), 'line-height': sz(1.25, 'em'), color: col(C.ink) }),
      ], { title: 'intro' }),
      box({ flex: flex(1, 1, '24rem'), background: bg(C.surface), 'border-radius': sz(24), padding: cu('clamp(28px, 4vw, 52px)'), transform: rot(2) }, [
        P('“', { 'font-size': sz(96), 'font-weight': str('700'), 'line-height': sz(0.6, 'em'), height: sz(34), color: col(C.gold) }),
        P(['Train up a child in the way he should go: and when he is old, he will not depart from it.', '教養孩童，使他走當行的道，就是到老他也不偏離。'],
          { margin: dims(12, 0, 0, 0), 'font-size': cu('clamp(1.6rem, 1.1rem + 1.6vw, 2.6rem)'), 'font-weight': str('500'), 'line-height': sz(1.22, 'em'), 'letter-spacing': sz(-0.02, 'em'), color: col(C.ink) }),
        P(['Proverbs 22:6', '箴言 22:6'], { margin: dims(16, 0, 0, 0), 'font-weight': str('600'), color: col(C.muted) }),
      ], { id: 'mpcbc-kids-verse', title: 'verse card' }),
    ], { title: 'intro and verse' }),
    // the Mission sentence, in its own words, as two pairs of tiles (the sentence itself is the 繁 source for the pair labels)
    plabel(['Mission', '使命']),
    node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 26rem), 1fr))'), gap: gap2(24, 24), padding: sz(0) }, [
      pair(['Connect kids to God', '使孩童與神連結'], word(C.surface, ['Know Jesus', '認識耶穌'], -1.5), word(C.mint, ['Follow Jesus', '跟隨耶穌'], 1.5)),
      pair(['Connect kids to others', '使孩童與他人連結'], word(C.sky, ['Love others', '愛他人'], -1.5), word(C.ink, ['Grow together', '共同成長'], 1.5, true)),
    ], 'mission pairs'),
    plabel(['Vision', '異象']),
    ktile(C.surface, [P(['MPCBC KIDS is shaping kids to be joyful and bright witnesses of the GOSPEL in every circumstance they are in.', 'MPCBC KIDS 塑造孩童在任何處境中，成為福音的喜樂、陽光的見證人。'],
      { 'font-size': cu('clamp(22px, 1.1rem + 0.9vw, 32px)'), 'font-weight': str('600'), 'letter-spacing': sz(-0.015, 'em'), 'line-height': sz(1.25, 'em'), color: col(C.ink) })], { 'min-height': sz(0), transform: rot(-0.6) }),
  ], { padding: dims('clamp(104px, 12vw, 150px)', 20) }),
  section('classes', 'Sunday School classes', { background: bg(C.surface) }, [
    headRow(['Sunday School classes', '主日學班級']),
    node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 18rem), 1fr))'), gap: sz(16), padding: sz(0) }, [
      classTile(C.kids, 'K–1st', 'Alpha Group', ['Kindergarten–1st grade · [what the kids do]', '幼稚園至一年級 · [課堂內容]']),
      classTile(C.mint, '2nd–4th', 'Delta Group', ['2nd–4th grade · [what the kids do]', '二年級至四年級 · [課堂內容]']),
      classTile(C.sky, '5th–6th', 'Omega Group', ['5th–6th grade · [what the kids do]', '五年級至六年級 · [課堂內容]']),
    ], 'class tiles'),
    box(merge(card, { margin: dims(16, 0, 0, 0), padding: dims(2, 26) }), [
      infoRow(['Location', '地點'], ['1st floor, Education Annex', '教育樓一樓']),
      infoRow(['Check-in', '報到'], ['[How parents check children in, and who meets them]', '[家長如何為孩子報到，由誰接待]']),
      infoRow(['Pick-up', '接回'], ['[How and where parents pick up; who can pick up]', '[家長如何及在哪裡接孩子；誰可以接]']),
      infoRow(['Nursery', '幼兒室'], ['[Under kindergarten?]', '[幼稚園以下？]']),
    ], { id: 'mpcbc-kids-info', title: 'location, check-in, pick-up, nursery' }),
  ]),
  // KIDS Events: the next one in a yellow panel, the rest as cards (built for 3–5)
  section('events', 'KIDS Events', { background: bg(C.surface) }, [
    headRow(['KIDS Events', '兒童事工活動']),
    row({ background: bg(C.kids), 'border-radius': sz(24), padding: cu('clamp(24px, 3vw, 40px)'), 'align-items': str('center'), gap: gap2(32, 48) }, [
      box({ flex: flex(1, 1, '22rem') }, [
        P(['2027 VBS · Save the date', '2027 暑期聖經營 · 預留時間'], merge(T.label, { color: col(C.ink) })),
        P(['June 21–25', '6月21日至25日'], { margin: dims(10, 0, 0, 0), 'font-size': cu('clamp(3rem, 2rem + 4vw, 5.5rem)'), 'font-weight': str('600'), 'letter-spacing': sz(-0.03, 'em'), 'line-height': sz(0.95, 'em'), color: col(C.ink) }, 'mpcbc-kids-vbs-date'),
        H('h3', ['Take Flight VBS · Flight Plan: Discover God’s Faithfulness', '乘風飛翔 · 飛行計劃：探索神的信實'], { margin: dims(14, 0, 0, 0), 'font-size': sz(21), 'font-weight': str('500'), 'line-height': sz(1.35, 'em'), color: col(C.ink) }),
        P(['June 21–25, 5:30–8:30 PM · June 27, 11:00 AM–1:30 PM', '6月21日至25日，晚上5:30至8:30 · 6月27日，上午11:00至下午1:30'], { margin: dims(6, 0, 0, 0), color: col(C.ink) }),
        row({ margin: dims(24, 0, 0, 0) }, [button('#events', ['[Registration link]', '[報名連結]'], 'ink')], { title: 'button' }),
      ], { title: 'VBS text' }),
      box({ flex: flex(1, 1, '20rem') }, [kframe('mpcbc-kids-photo-vbs', img(IMG.vbsCrafts, { 'aspect-ratio': str('4 / 3'), 'border-radius': sz(2) }, 'Children at VBS'), { transform: rot(2) })], { title: 'VBS photo' }),
    ], { title: 'VBS panel' }),
    box({ margin: dims(20, 0, 0, 0) }, [grid3([2, 3, 4].map(eventCard))], { title: 'more events' }),
  ]),
  // Gallery on yellow: one big photo beside a 3 × 2 grid (layout: #mpcbc-scrap in style.css; photos are placeholders)
  kidsBand('mpcbc-kids-band-gallery', [
    headRow(['Gallery', '相簿'], textLink('/gallery/', ['Gallery →', '相簿 →'])),
    node('e-div-block', cssid('mpcbc-scrap'), { display: str('grid'), padding: sz(0) }, scrapPics.map((id, i) => kframe(`mpcbc-kids-photo-scrap-${i + 1}`, img(id, { 'border-radius': sz(2) }, `gallery photo ${i + 1}`), {}, dims(10, 10, 14, 10))), 'scrapbook'),
  ]),
  // Team (Lili: same bio as section 3) + Social Media; the light closing band before the footer
  closing('team', [
    headRow(['Team', '同工'], textLink('https://www.facebook.com/groups/1798735730160133', ['MPCBC KIDS on Facebook ↗', 'MPCBC KIDS Facebook ↗'])),
    node('e-div-block', {}, { display: str('grid'), 'grid-template-columns': str('repeat(auto-fit, minmax(min(100%, 26rem), 1fr))'), gap: sz(20), padding: sz(0) }, team.map(([role, name, bio]) => box(merge(card, { background: bg(C.bg), padding: dims(24, 24, 20, 24), gap: sz(8) }), [
      P(role, T.label), H('h3', name, T.card), P(bio, { color: col(C.muted) }),
    ], { title: `team ${name[0]}` })), 'team cards'),
  ]),
];

const pages = {
  about: { id: 64, title: 'About', elements: about,
    seo: ['Our Story & Beliefs | Monterey Park Chinese Baptist Church', '教會歷史及信仰 | 慕道園華人浸信會', 'Founded in 1977, MPCBC worships in Cantonese, Mandarin and English. Learn our story, mission and what we believe.', '慕道園華人浸信會成立於1977年，以粵語、國語及英語崇拜。了解我們的歷史、使命及信仰。'] },
  kids: { id: 62, title: 'MPCBC KIDS', elements: kidsPage,
    seo: ['Kids Ministry | Monterey Park Chinese Baptist Church', '兒童主日學 | 慕道園華人浸信會', 'MPCBC KIDS helps children from kindergarten to 6th grade know and follow Jesus, with Sunday School every Sunday morning in Alhambra.', 'MPCBC KIDS 兒童主日學每星期日為幼稚園至六年級的孩童而設，幫助孩子認識並跟隨耶穌。'] },
  english: { slug: 'english', title: 'English Congregation', elements: english,
    seo: ['Connect Ministry English Service | Monterey Park Chinese Baptist Church', '英語崇拜 | 慕道園華人浸信會', 'Connect Ministry is our English-speaking congregation in Alhambra. Join us Sundays at 11:00 AM.', 'Connect Ministry 是本教會的英語會眾，每星期日上午11時崇拜。'] },
};
// self-check: one H1 first, no skipped heading levels
for (const [k, p] of Object.entries(pages)) {
  const tags = []; (function w(n) { if (n.widgetType === 'e-heading') tags.push(n.settings.tag.value); (n.elements || []).forEach(w); })({ elements: p.elements });
  if (tags[0] !== 'h1' || tags.filter((t) => t === 'h1').length !== 1) throw new Error(`${k}: expected one H1 first: ${tags}`);
  for (let i = 1; i < tags.length; i++) if (+tags[i][1] > +tags[i - 1][1] + 1) throw new Error(`${k}: heading level skipped: ${tags}`);
}
fs.writeFileSync(new URL('content-source/v10-pages.json', root), JSON.stringify(pages, null, 1));
console.log(Object.entries(pages).map(([k, p]) => `${k}: ${p.elements.length} sections`).join(', '));
