// Writes the v10 interior-page mockups (one shared header and footer). Run: node taste-mockup/build-v10-mockups.mjs
// WORDS: every piece of the MPCBC Website Content Workbook for these pages; my own wording only where the workbook has
// none (marked "// own wording" below); blanks stay [bracketed]. One language per view: this is the English view,
// Chinese comes from the language switcher (the line under each heading follows the DESIGN-v10 bilingual rule).
// EVENTS: built for 3–5 at a time (one featured, the rest in a grid that fills the row).
// LOOK: only homepage pieces (v10-interior.css).
import fs from 'node:fs';
const here = new URL('./', import.meta.url);

const nav = [['About Us', 'about-v10.html'], ['Plan a Visit', 'visit-v10.html'], ['Worship & Ministries', 'english-v10.html'], ['MPCBC KIDS', 'kids-v10.html'], ['News & Events', 'news-v10.html']];
const page = (title, desc, current, body) => `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>${title}</title>
<meta name="description" content="${desc}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="v10-interior.css">
<script>document.documentElement.classList.add('js')</script>
</head>
<body>
<header class="site-head">
  <div class="bar">
    <a class="mark" href="index.html" aria-label="Monterey Park Chinese Baptist Church, home"><b>MPCBC</b><span lang="zh-Hant">慕道園華人浸信會</span></a>
    <nav class="nav" aria-label="Main">${nav.map(([l, h]) => `<a href="${h}"${l === current ? ' aria-current="page"' : ''}>${l.replace('&', '&amp;')}</a>`).join('')}</nav>
    <div class="actions">
      <div class="lang" role="group" aria-label="Language"><a href="#" aria-current="true" aria-label="English">EN</a><a href="#" lang="zh-Hant" aria-label="繁體中文">繁</a><a href="#" lang="zh-Hans" aria-label="简体中文">简</a></div>
      <a class="btn btn-line watch" href="sermons-v10.html"><span class="dot" aria-hidden="true"></span>Watch Live</a>
      <a class="btn btn-ac" href="give-v10.html">Give</a>
    </div>
  </div>
</header>
<main>
${body}
</main>
<footer class="site-foot">
  <div class="wrap">
    <div class="cols">
      <div><b>Monterey Park Chinese Baptist Church</b><span lang="zh-Hant">慕道園華人浸信會</span><p style="margin-top: 12px">302 W. Commonwealth Ave.<br>Alhambra, CA 91801</p></div>
      <div><b>Sunday</b>Mandarin · 9:00 AM<br>Cantonese · 11:00 AM<br>English · 11:00 AM</div>
      <div><b>Contact</b><a href="tel:+16262891284">(626) 289-1284</a><br><a href="mailto:info@mpcbc.net">info@mpcbc.net</a><br>Office hours: [TBD]</div>
    </div>
    <div class="legal"><span>© MPCBC · Founded 1977</span></div>
  </div>
</footer>
<script>
const io = new IntersectionObserver((es) => es.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: 0.12 });
document.querySelectorAll('[data-rise]').forEach((el) => io.observe(el));
if (matchMedia('(prefers-reduced-motion: reduce)').matches) document.querySelectorAll('video[autoplay]').forEach((v) => { v.removeAttribute('autoplay'); v.pause(); }); // reduced motion: show the poster
</script>
</body>
</html>
`;

// ---------- shared pieces ----------
const head = (en, zh, extra = '') => `<div><h2 class="h2"${extra}>${en}</h2><p class="zh" lang="zh-Hant">${zh}</p></div>`;
const yt = (id) => `https://i.ytimg.com/vi/${id}/hqdefault.jpg`;
// an event card (workbook §9: title, date, time, place, one sentence, flyer, registration link, congregation)
const ev = (img, when, title, line = '[One sentence]') => `<a class="card ecard" href="#"><div class="pic">${img ? `<img src="${img}" alt="">` : '<div class="ph">[Flyer]</div>'}</div><div class="body"><p class="label">${when}</p><h3>${title}</h3><p style="color: var(--muted)">${line}</p></div></a>`;
const evPh = (n) => ev('', '[Date] · [Time] · [Place] · [Congregation]', `[Event ${n} title]`);
// sub-page close (workbook §5): "← Back to the congregation" and a button to the other sub-page, on a light band
const subClose = (other, href) => `  <section class="band b-surface b-close">
    <div class="wrap backbar" style="border: 0; padding: 0" data-rise><a class="link" href="english-v10.html">← Back to the congregation</a><a class="btn btn-ac" href="${href}">${other} →</a></div>
  </section>`;
const subTop = (other, href) => `  <div class="wrap backbar"><a class="link" href="english-v10.html">← Back to the congregation</a><a class="btn btn-line" href="${href}" style="min-height: 44px">${other} →</a></div>`;

// =========================== Visit & Contact (workbook §4) ===========================
const visit = `  <!-- Hero: full-width photo, title in a white card -->
  <section class="hero-card">
    <img src="assets/courtyard-lights.jpg" alt="The church courtyard under string lights">
    <div class="wrap" data-rise>
      <div class="card-in">
        <p class="label">Visit &amp; Contact</p>
        <h1 class="h1" style="margin-top: 10px">Plan Your Visit</h1><p class="zh" lang="zh-Hant">計劃來訪</p>
        <p style="margin-top: 16px; font-size: 19px">New here? We’d love to meet you. Here’s everything you need to know for your first Sunday at MPCBC.</p>
        <div class="btns"><a class="btn btn-ac" href="#contact">Get in touch</a><a class="btn btn-line" href="#">Get Directions ↗</a></div>
      </div>
    </div>
  </section>

  <!-- §0 Sunday worship + address: the big times on navy -->
  <section class="band b-accent">
    <div class="wrap" data-rise>
      <div class="head-row" style="margin-bottom: 0">${head('Sunday worship', '主日崇拜')}<p style="color: var(--night-text)">302 W. Commonwealth Ave., Alhambra, CA 91801 · <a href="#" style="color: #fff">Get Directions ↗</a></p></div>
      <div class="times">
        <div><b>9:00<small>AM</small></b><span>Mandarin Worship</span><em>Sanctuary</em></div>
        <div><b>11:00<small>AM</small></b><span>Cantonese Worship</span><em>Sanctuary</em></div>
        <div><b>11:00<small>AM</small></b><span>English Worship</span><em>Fellowship Hall</em></div>
      </div>
    </div>
  </section>

  <!-- 3 Your first Sunday + accessible parking & entrance -->
  <section class="band" id="first-sunday">
    <div class="wrap split" data-rise style="align-items: start">
      <div>${head('Your first Sunday', '您的第一個主日')}</div>
      <div class="card" style="padding: 14px 26px">
        <ul class="rows" style="margin-top: 0">
          <li><b>Come as you are; there’s no dress code.</b></li>
          <li><b>Street parking is available around the church.</b></li>
          <li><b>English Worship runs about 90 minutes.</b><span>[Cantonese and Mandarin service length]</span></li>
          <li><b>Accessible parking: [where]. Step-free entrance: [where].</b></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 4 Kids & youth + the Sunday School table, on the Kids band -->
  <section class="b-kids">
    <div class="wrap split" data-rise>
      <div>${head('Kids &amp; youth', '兒童及青少年')}
        <p style="margin-top: 20px; font-size: 19px; max-width: 44ch">MPCBC KIDS serves kindergarten to 6th grade. Teens join Youth Sunday School and the Jr. High Faith and Life Hour.</p>
        <p style="margin-top: 10px; color: var(--muted)">Nursery: [available? ages, where]</p>
        <div class="btns"><a class="btn btn-ink" href="kids-v10.html">MPCBC KIDS</a></div></div>
      <div class="card" style="padding: 20px 26px">
        <h3 style="font-size: 19px; font-weight: 600">Sunday School</h3>
        <ul class="rows">
          <li><b>English Sunday School</b><span>10:00–10:45 AM · Fellowship Hall</span></li>
          <li><b>Youth Sunday School</b><span>10:00–10:45 AM · Education Annex</span></li>
          <li><b>MPCBC KIDS Sunday School &amp; Children’s Worship</b><span>10:00 AM–12:30 PM · Education Annex</span></li>
          <li><b>Jr. High Faith and Life Hour</b><span>11:00 AM–12:00 PM · Education Annex</span></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 5 Watch online (one link per congregation), night band -->
  <section class="band b-night">
    <div class="wrap split" data-rise>
      <div>${head('Watch online', '線上崇拜')}
        <p style="margin-top: 20px; font-size: 19px; color: var(--night-text)">Can’t come in person? Watch our services live on YouTube.</p></div>
      <div class="invites">
        <a class="invite line" href="#"><span><b>English Worship</b><small>11:00 AM</small></span><i aria-hidden="true">↗</i></a>
        <a class="invite line" href="#"><span><b>Cantonese Worship</b><small>11:00 AM</small></span><i aria-hidden="true">↗</i></a>
        <a class="invite line" href="#"><span><b>Mandarin Worship</b><small>9:00 AM</small></span><i aria-hidden="true">↗</i></a>
      </div>
    </div>
  </section>

  <!-- 6 Get in touch + the contact form (§1 · 7) -->
  <section class="band b-surface b-close" id="contact">
    <div class="wrap split" data-rise style="align-items: start">
      <div>${head('Get in touch', '聯絡我們')}
        <p style="margin-top: 20px; font-size: 19px; max-width: 40ch">Want to be baptized, become a member, or need prayer? Talk to a pastor after the service or send us a message.</p>
        <p style="margin-top: 10px; color: var(--muted)">[Baptism or membership class, if there is one]</p>
        <p style="margin-top: 14px"><a class="link" href="tel:+16262891284">(626) 289-1284</a> · <a class="link" href="mailto:info@mpcbc.net">info@mpcbc.net</a></p></div>
      <form class="card form" onsubmit="return false">
        <div><label>Name</label><input></div>
        <div><label>Email or phone</label><input></div>
        <div><label>Preferred language</label><div class="opts"><span class="on">English</span><span>粵語</span><span>國語</span></div></div>
        <div><label>Preferred reply</label><div class="opts"><span class="on">Email</span><span>Phone</span><span>Text</span></div></div>
        <div><label>Reason</label><div class="opts"><span class="on">Plan a visit</span><span>Prayer request</span><span>Baptism or membership</span><span>Question</span></div></div>
        <div><label>Message</label><textarea></textarea></div>
        <div><button class="btn btn-ac" type="submit">Send</button><p class="note" style="margin-top: 10px">We only use your details to reply to you.</p></div>
      </form>
    </div>
  </section>`;

// =========================== Pastors & Leadership (workbook §3) ===========================
const pastor = (img, role, en, zh, bio, flip) => `      <article class="card profile${flip ? ' flip' : ''}">
        <div class="pic">${img ? `<img src="${img}" alt="${en}">` : `<div class="ph">[Photo]</div>`}</div>
        <div class="body"><p class="label">${role}</p><h3>${en}</h3><p class="zh" lang="zh-Hant" style="margin-top: 2px">${zh}</p><p class="bio">${bio}</p></div>
      </article>`;
const deacon = (en, zh) => `<li><b>${en}</b><span>${zh}</span></li>`;
const leadership = `  <section class="hero-text">
    <div class="wrap" data-rise>
      <p class="label">About Us</p>
      <h1 class="h1">Pastors &amp; Leadership</h1><p class="zh" lang="zh-Hant">教牧同工</p>
    </div>
  </section>
  <section class="band" style="padding-top: 0">
    <div class="wrap stack" data-rise>
${pastor('assets/pastor-caleb.jpg', 'Senior Pastor', 'Rev. Caleb Cheung', '張健庭牧師', 'Pastor Caleb Cheung is from Hong Kong. He studied theology at Fuller Theological Seminary, then pursued doctoral studies in theology and philosophy of religion at Claremont Graduate University. He stayed on to serve and shepherd MPCBC, where he is now Senior Pastor.')}
${pastor('', 'English Pastor', 'Rev. Edward Lu', '呂德勝牧師', 'Ed seeks to nurture faith, equip leaders, and disciple the next generation through meaningful relationships and shared ministry. He holds a B.S. in Ministry Leadership and earned his Master of Divinity and Doctor of Ministry from Moody Theological Seminary.', true)}
${pastor('', 'Family and Children Minister', 'Min. Lili Tian', '田俐力傳道', 'Min. Lili Tian graduated from Gateway Seminary in 2024 with a Master of Theological Studies. She had an obvious calling from God to serve in family and children’s ministry. God has granted her spiritual gifts and full passion for children and family. She is willing to be used by God greatly in serving every child and family.')}
    </div>
  </section>
  <!-- Deacons (workbook §3; whether to show them is Question 4) -->
  <section class="band b-surface b-close">
    <div class="wrap split" data-rise style="align-items: start">
      <div>${head('Deacons', '執事')}<p style="margin-top: 16px; color: var(--muted)">[Show the deacons? Question 4]</p></div>
      <div class="card" style="padding: 14px 26px"><ul class="rows" style="margin-top: 0">
        ${deacon('Charles Leung', '[中文姓名] · [Role]')}${deacon('Jeff Cha', '[中文姓名] · [Role]')}${deacon('Evelyn Hsu', '[中文姓名] · [Role]')}
        ${deacon('Esther Chan', '張陳一萍 · [Role]')}${deacon('Morgan Lam', '林斯勤 · [Role]')}
      </ul></div>
    </div>
  </section>`;

// =========================== MPCBC KIDS (workbook §8 + Home §4) ===========================
const kids = `  <!-- Hero: white, three tilted frames (the big one a short silent loop; placeholder = the church video until a Kids clip exists), then the name, the workbook line and one fact line -->
  <section class="kids-hero">
    <div class="wrap split" data-rise>
      <div>
        <h1 class="h1" style="font-size: clamp(3rem, 2rem + 3.6vw, 4.6rem)">MPCBC KIDS</h1>
        <p class="kids-statement">A <em>spiritual family</em> for all kids.</p>
        <p class="kids-when">Sundays 10:00 AM–12:30 PM · Kindergarten to 6th grade</p>
        <div class="btns"><a class="btn btn-ink" href="#classes">Sunday School classes</a><a class="btn btn-line" href="visit-v10.html">Plan a Visit</a></div>
      </div>
      <div class="photos3">
        <div class="frame clip"><video src="assets/hero-720.mp4" poster="assets/vbs-wave.jpg" autoplay muted loop playsinline aria-hidden="true"></video><span class="flag">[Kids clip, 5–10 s]</span></div>
        <div class="frame"><img src="assets/vbs-boy.jpg" alt="A boy smiling at VBS"></div>
        <div class="frame"><img src="assets/vbs-crafts.jpg" alt="Children holding up paper flowers they made"></div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision, one yellow band. Left: the workbook intro ends on its question; right: the verse answers on a pinned card.
       Below: the Mission as two pairs ("connect kids to God" / "to others", the sentence in its own words), the Vision in the same tile style.
       H2 from the workbook row labels; 使命及異象 is our wording (異象 is not in the workbook): flag for review. -->
  <section class="b-kids purpose">
    <div class="wrap">
      <div class="split purpose-top" data-rise>
        <div>${head('Mission &amp; Vision', '使命及異象')}
          <p class="purpose-intro">Monterey Park Chinese Baptist Church is a Purpose Driven Church. The Children’s Ministry at our church is called “MPCBC KIDS”, and our mission is to balance the purposes of the church in our ministry to children. <b>Where do our purposes come from?</b></p></div>
        <figure class="verse-card"><span class="mark-q" aria-hidden="true">“</span>
          <blockquote>Train up a child in the way he should go: and when he is old, he will not depart from it.</blockquote>
          <cite>Proverbs 22:6</cite></figure>
      </div>
      <p class="label purpose-label" data-rise>Mission</p>
      <div class="pairs" data-rise>
        <div class="pair"><p>Connect kids to God</p><div class="words"><div class="w1">Know Jesus</div><div class="w2">Follow Jesus</div></div></div>
        <div class="pair"><p>Connect kids to others</p><div class="words"><div class="w3">Love others</div><div class="w4">Grow together</div></div></div>
      </div>
      <p class="label purpose-label" data-rise>Vision</p>
      <div class="words vision-row" data-rise><div class="w5">MPCBC KIDS is shaping kids to be joyful and bright witnesses of the GOSPEL in every circumstance they are in.</div></div>
    </div>
  </section>

  <!-- Sunday School classes + location, check-in, pick-up, nursery -->
  <section class="band b-surface" id="classes">
    <div class="wrap">
      <div class="head-row" data-rise>${head('Sunday School classes', '主日學班級')}</div>
      <div class="ktiles" data-rise>
        <div class="ktile sun"><span class="grade">K–1st</span><div><h3>Alpha Group</h3><p>Kindergarten–1st grade · [what the kids do]</p></div></div>
        <div class="ktile mint"><span class="grade">2nd–4th</span><div><h3>Delta Group</h3><p>2nd–4th grade · [what the kids do]</p></div></div>
        <div class="ktile sky"><span class="grade">5th–6th</span><div><h3>Omega Group</h3><p>5th–6th grade · [what the kids do]</p></div></div>
      </div>
      <div class="card" data-rise style="margin-top: 16px; padding: 14px 26px">
        <ul class="rows" style="margin-top: 0">
          <li><b>Location</b><span>1st floor, Education Annex</span></li>
          <li><b>Check-in</b><span>[How parents check children in, and who meets them]</span></li>
          <li><b>Pick-up</b><span>[How and where parents pick up; who can pick up]</span></li>
          <li><b>Nursery</b><span>[Under kindergarten?]</span></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- KIDS Events: the next one in a yellow panel, the rest as cards (built for 3–5) -->
  <section class="band b-surface">
    <div class="wrap">
      <div class="head-row" data-rise>${head('KIDS Events', '兒童事工活動')}</div>
      <div class="ktile sun vbs" data-rise style="padding: clamp(24px, 3vw, 40px); flex-direction: row; flex-wrap: wrap; align-items: center">
        <div><p class="label" style="color: var(--ink)">2027 VBS · Save the date</p>
          <p class="big" style="margin-top: 10px">June 21–25</p>
          <p style="margin-top: 10px; font-size: 21px; font-weight: 500">Take Flight VBS · Flight Plan: Discover God’s Faithfulness</p>
          <p style="margin-top: 6px">June 21–25, 5:30–8:30 PM · June 27, 11:00 AM–1:30 PM</p>
          <div class="btns"><a class="btn btn-ink" href="#">[Registration link]</a></div></div>
        <div class="frame" style="rotate: 2deg"><img src="assets/vbs-crafts.jpg" alt="Children at VBS"></div>
      </div>
      <div class="grid3" data-rise style="margin-top: 20px">${evPh(2)}${evPh(3)}${evPh(4)}</div>
    </div>
  </section>

  <!-- Gallery: a scrapbook page on yellow (photos are placeholders) -->
  <section class="b-kids">
    <div class="wrap">
      <div class="head-row" data-rise>${head('Gallery', '相簿')}<a class="link" href="gallery-v10.html">Gallery →</a></div>
      <div class="scrap" data-rise>
        <div class="frame f1"><img src="assets/vbs-floor.jpg" alt=""></div><div class="frame f2"><img src="assets/sanctuary-vbs.jpg" alt=""></div>
        <div class="frame f3"><img src="assets/vbs-wave.jpg" alt=""></div><div class="frame f4"><img src="assets/vbs-crafts.jpg" alt=""></div>
        <div class="frame f5"><img src="assets/vbs-boy.jpg" alt=""></div><div class="frame f6"><img src="assets/courtyard-lights.jpg" alt=""></div>
        <div class="frame f7"><img src="assets/sanctuary-aisle.jpg" alt=""></div><div class="frame f8"><img src="assets/church-commonwealth.jpg" alt=""></div>
      </div>
    </div>
  </section>

  <!-- Team (Lili: same bio as §3) + Social Media -->
  <section class="band b-surface b-close">
    <div class="wrap">
      <div class="head-row" data-rise>${head('Team', '同工')}<a class="link" href="https://www.facebook.com/groups/1798735730160133">MPCBC KIDS on Facebook ↗</a></div>
      <div class="grid3" data-rise style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 26rem), 1fr))">
        <article class="card group"><p class="label">Family and Children Minister</p><h3>Min. Lili Tian</h3><p>Min. Lili Tian graduated from Gateway Seminary in 2024 with a Master of Theological Studies. She had an obvious calling from God to serve in family and children’s ministry. God has granted her spiritual gifts and full passion for children and family. She is willing to be used by God greatly in serving every child and family.</p></article>
        <article class="card group"><p class="label">Children Department Co-worker</p><h3>Connie So</h3><p>She has a Master of Education, holds teaching credentials in Hong Kong and California, and is currently pursuing a Master of Arts in Christian Studies.</p></article>
      </div>
    </div>
  </section>`;

// =========================== Give (workbook §10) ===========================
const give = `  <section class="hero-text">
    <div class="wrap" data-rise>
      <div class="envelope-slim big"><div class="paper" style="display: block">
        <div style="display: flex; flex-wrap: wrap-reverse; justify-content: space-between; gap: 20px 32px; align-items: flex-start">
          <div><h1 class="h1">Give</h1><p class="zh" lang="zh-Hant">奉獻</p>
            <p style="margin-top: 18px; max-width: 34ch; font-size: 19px; color: var(--muted)">We are grateful for the tithes and offerings of our members and the donations of our community. Your giving ensures MPCBC can continue doing God’s work.</p></div>
          <div style="display: flex; gap: 20px; align-items: flex-start" aria-hidden="true"><span class="postmark">ALHAMBRA<br>CA<br>91801</span>
            <span class="stamp" style="position: static; rotate: 3deg"><img src="assets/steeple-stamp.jpg" alt="" style="width: 76px; height: 96px"></span></div>
        </div>
      </div></div>
      <div class="grid3 b-close" style="margin-top: 20px">
        <article class="card group"><p class="label">By check</p><h3>Checks payable to MPCBC</h3><p>Mailed to 302 W. Commonwealth Ave., Alhambra, CA 91801</p></article>
        <article class="card group featured"><p class="label">Online</p><h3>PayPal</h3><p>donations@mpcbc.net</p><p class="when"><a class="btn btn-white" href="#" style="min-height: 44px">Give</a></p></article>
        <article class="card group"><p class="label">In person</p><h3>[Sunday offering]</h3><p>[How and where]</p></article>
      </div>
    </div>
  </section>`;

// =========================== News & Events (workbook §9) — built for 3–5 events ===========================
const news = `  <section class="hero-text">
    <div class="wrap" data-rise>
      <h1 class="h1">News &amp; Events</h1><p class="zh" lang="zh-Hant">消息及活動</p>
      <!-- the next event, featured; the rest follow in the grid -->
      <a class="feature" href="kids-v10.html" style="margin-top: 28px; text-decoration: none">
        <div class="pic"><img src="assets/vbs-wave.jpg" alt="Kids at VBS"></div>
        <div class="body"><p class="label">MPCBC KIDS · Save the date</p><span class="big">June 21–25</span><span style="font-size: 24px; font-weight: 500">2027 VBS: Take Flight</span><span style="color: var(--night-text)">Flight Plan: Discover God’s Faithfulness · 5:30–8:30 PM</span><span class="btn btn-white" style="align-self: flex-start; margin-top: 8px">[Registration link]</span></div>
      </a>
    </div>
  </section>

  <section class="band" style="padding-top: 0">
    <div class="wrap">
      <div class="head-row" data-rise>${head('Events', '活動')}</div>
      <div class="grid3" data-rise style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr))">${evPh(2)}${evPh(3)}${evPh(4)}${evPh(5)}</div>
    </div>
  </section>

  <section class="band b-surface">
    <div class="wrap split" data-rise style="align-items: start">
      <div>${head('Announcements', '報告')}</div>
      <div class="card" style="padding: 14px 26px"><ul class="rows" style="margin-top: 0">
        <li><b>[Announcement 1 title]</b><span>[Text] · until [take-down date]</span></li>
        <li><b>[Announcement 2 title]</b><span>[Text] · until [take-down date]</span></li>
      </ul></div>
    </div>
  </section>

  <!-- Latest newsletter (Home §5) + Gallery -->
  <section class="band b-close">
    <div class="wrap split" data-rise style="align-items: start">
      <a class="card" href="#" style="display: flex; gap: 22px; align-items: center; padding: 18px; background: var(--fill); border-color: var(--fill); text-decoration: none">
        <img src="assets/newsletter-2026-07.jpg" alt="Front page of the July 2026 newsletter" style="width: 120px; border-radius: 4px">
        <span><span class="label">Latest issue</span><span style="display: block; margin-top: 6px; font-size: 23px; font-weight: 500">July 2026</span></span></a>
      <a class="card" href="gallery-v10.html" style="display: flex; gap: 12px; align-items: center; padding: 18px; text-decoration: none">
        <img src="assets/courtyard-lights.jpg" alt="" style="width: 100px; aspect-ratio: 1; object-fit: cover; border-radius: 10px">
        <img src="assets/sanctuary-vbs.jpg" alt="" style="width: 100px; aspect-ratio: 1; object-fit: cover; border-radius: 10px">
        <span><span class="label">Gallery</span><span style="display: block; margin-top: 6px; font-size: 23px; font-weight: 500">→</span></span></a>
    </div>
  </section>`;

// =========================== Gallery (workbook §9) ===========================
const gallery = `  <section class="hero-text">
    <div class="wrap" data-rise><h1 class="h1">Gallery</h1><p class="zh" lang="zh-Hant">相簿</p></div>
  </section>
  <section class="band b-close" style="padding-top: 0">
    <div class="wrap">
      <div class="gallery lead-photo" data-rise>
        <figure><img src="assets/vbs-wave.jpg" alt=""></figure><figure><img src="assets/sanctuary-vbs.jpg" alt=""></figure>
        <figure><img src="assets/vbs-crafts.jpg" alt=""></figure><figure><img src="assets/courtyard-lights.jpg" alt=""></figure>
        <figure><img src="assets/vbs-floor.jpg" alt=""></figure><figure><img src="assets/vbs-boy.jpg" alt=""></figure>
        <figure><img src="assets/sanctuary-aisle.jpg" alt=""></figure><figure><img src="assets/church-commonwealth.jpg" alt=""></figure>
      </div>
      <p style="margin-top: 20px; color: var(--muted)">[Who manages photos · photo-consent policy]</p>
    </div>
  </section>`;

// =========================== English › Sermons & Livestream (§5; filled from the English YouTube channel) ===========================
const sermons = `${subTop('Fellowship &amp; Ministries', 'english-v10.html#groups')}
  <section class="band b-night">
    <div class="wrap">
      <div class="head-row" data-rise><div><p class="label">English Congregation</p><h1 class="h1" style="margin-top: 8px">Sermons &amp; Livestream</h1><p class="zh" lang="zh-Hant">講道及直播</p></div></div>
      <article class="card svc" data-rise>
        <button class="player" type="button"><img src="${yt('JMt-wR3pSfs')}" alt=""><span class="badge">Latest service</span><span class="play" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="#1D2430"/></svg></span></button>
        <div><p class="label">11:00 AM · Fellowship Hall</p><h3>English Worship</h3><p style="margin-top: 6px">[Title, date and speaker of the latest sermon, from YouTube]</p></div>
      </article>
    </div>
  </section>
  <section class="band">
    <div class="wrap">
      <div class="head-row" data-rise>${head('Past sermons', '講道重溫')}</div>
      <div class="grid3" data-rise>
        <a class="card ecard" href="#"><div class="pic wide"><img src="${yt('_zoNuJdu0tc')}" alt=""></div><div class="body"><p class="label">November 8, 2026 · Rev. Edward Lu</p><h3>Who Will Take The First Step?</h3></div></a>
        <a class="card ecard" href="#"><div class="pic wide"><img src="${yt('KikczBWW7Mc')}" alt=""></div><div class="body"><p class="label">November 1, 2026 · Rev. Edward Lu</p><h3>Who Has Been Paying Attention?</h3></div></a>
        <a class="card ecard" href="#"><div class="pic wide"><img src="${yt('JMt-wR3pSfs')}" alt=""></div><div class="body"><p class="label">November 13, 2026 · Rev. Dr. Caleb Cheung</p><h3>Gluttony and Self-Control</h3></div></a>
      </div>
    </div>
  </section>
${subClose('Fellowship &amp; Ministries', 'english-v10.html#groups')}`;

// =========================== English › Fellowship & Ministries (§5 table) ===========================
const day = (d, when, name, who, place, featured) => `<article class="card daycard${featured ? ' featured' : ''}"><span class="day"${featured ? ' style="color: #fff"' : ''}>${d}</span><p class="label">${when}</p><h3>${name}</h3><p>${who}${place ? ` · ${place}` : ''}</p></article>`;
const ministries = `${subTop('Sermons &amp; Livestream', 'sermons-v10.html')}
  <section class="band b-accent">
    <div class="wrap" data-rise>
      <p class="label">English Congregation</p>
      <h1 class="h1" style="margin-top: 8px">Fellowship &amp; Ministries</h1><p class="zh" lang="zh-Hant">團契及事工</p>
    </div>
  </section>
  <section class="band">
    <div class="wrap">
      <div class="grid3" data-rise>
        ${day('Sun', '10:00–10:45 AM', 'English Sunday School', 'All adults', 'Fellowship Hall')}
        ${day('Sun', '10:00–10:45 AM', 'Youth Sunday School', 'Middle &amp; high school', 'Education Annex')}
        ${day('Sun', '11:00 AM–12:00 PM', 'Jr. High Faith and Life Hour', 'Junior high', 'Education Annex')}
        ${day('Mon', '6:30–9:00 PM', 'College/Career Fellowship', 'College students &amp; young adults', 'West Covina (please verify)', true)}
        ${day('Thu', '7:30–9:00 PM (please verify)', 'Adult Bible Study and Prayer Meeting', 'Adults', 'Online (please verify)')}
        ${day('Daily', 'YouVersion app (please verify it is still running)', 'Bible in a Year', 'Everyone', '')}
        ${day('[Day]', '[Time]', 'Women’s Fellowship', 'Women', '[Place · contact]')}
      </div>
    </div>
  </section>
${subClose('Sermons &amp; Livestream', 'sermons-v10.html')}`;

const pages = [
  ['visit-v10.html', 'Plan Your Visit | Monterey Park Chinese Baptist Church', 'Planning your first visit? See Sunday service times, our address and parking, and what to expect. We’d love to meet you.', 'Plan a Visit', visit],
  // no SEO rows in the workbook for these: its "SEO fallback" (page name + church name; first paragraph, ~155 characters)
  ['kids-v10.html', 'Kids Ministry | Monterey Park Chinese Baptist Church', 'MPCBC KIDS helps children from kindergarten to 6th grade know and follow Jesus, with Sunday School every Sunday morning in Alhambra.', 'MPCBC KIDS', kids],
  ['give-v10.html', 'Give | Monterey Park Chinese Baptist Church', 'We are grateful for the tithes and offerings of our members and the donations of our community. Your giving ensures MPCBC can continue doing God’s work.', '', give],
  ['news-v10.html', 'News &amp; Events | Monterey Park Chinese Baptist Church', 'A church family in Alhambra, CA, with Mandarin, Cantonese and English worship every Sunday. Everyone is welcome. Visit us or watch online.', 'News & Events', news],
  ['gallery-v10.html', 'Gallery | Monterey Park Chinese Baptist Church', 'A church family in Alhambra, CA, with Mandarin, Cantonese and English worship every Sunday. Everyone is welcome. Visit us or watch online.', 'News & Events', gallery],
  ['sermons-v10.html', 'Sermons &amp; Livestream | Monterey Park Chinese Baptist Church', 'Connect Ministry is our English-speaking congregation in Alhambra. Join us Sundays at 11:00 AM.', 'Worship & Ministries', sermons],
];
for (const [file, title, desc, current, body] of pages) fs.writeFileSync(new URL(file, here), page(title, desc, current, body));
console.log(pages.map((p) => p[0]).join(' '));
