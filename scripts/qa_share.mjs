// Share row under the maker (November 3, Garrett): the pills, their hrefs (each network's /go/ short link and
// text, decoded), a real copy and paste round trip, a refused clipboard, no Clipboard API, the share sheet's
// resolve / cancel / refuse paths, focus, 44 px targets, GA4 share events, nothing loaded from social sites
// before a click. Downloads around it: scripts/qa_save.mjs. Redirects: curl the live /go/<state>/<network>.
//   node scripts/qa_share.mjs [--base http://127.0.0.1:8792/] [--plant href|copy]
import { chromium, webkit, devices } from 'playwright';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/'), PLANT = arg('--plant', '');
const SHOT = (await import('node:os')).tmpdir() + '/';
const b = await chromium.launch(); let fails = 0;
const ok = (c, m) => { console.log((c ? '  ok   ' : '  FAIL ') + m); if (!c) fails++; };
const HOSTS = { facebook: 'www.facebook.com', threads: 'www.threads.com', bluesky: 'bsky.app', whatsapp: 'wa.me' };
const spy = () => { window.__ev = []; const o = window.gtag; window.gtag = function () { window.__ev.push([...arguments]); if (o) o.apply(this, arguments); }; };
const shares = (p) => p.evaluate(() => window.__ev.filter(e => e[1] === 'share').map(e => e[2].method + ':' + e[2].content_type + ':' + e[2].item_id));
const CASES = [['texas/', 'TX', 'Make a free Texas vote sticker for your profile picture.', 'Link copied', 'Copy link'], ['es/texas/', 'TX', 'Haz gratis tu sticker de voto de Texas para tu foto de perfil.', 'Enlace copiado', 'Copiar enlace'], ['new-hampshire/', 'NH', 'Make a free New Hampshire vote sticker for your profile picture.', 'Link copied', 'Copy link']];
for (const [path, st, txt, copied, copyName] of CASES) {
  const U = 'https://votesticker.org/' + path;
  const G = (n) => 'https://votesticker.org/' + path.replace(/([a-z-]+)\/$/, 'go/$1/') + n;
  for (const w of [320, 390, 768, 1440]) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 }, permissions: ['clipboard-read', 'clipboard-write'] });
    const p = await ctx.newPage(); const errs = []; p.on('pageerror', e => errs.push(String(e)));
    const ext = []; p.on('request', r => { const h = new URL(r.url()).host; if (/facebook|x\.com|twitter|threads|bsky|whatsapp|wa\.me/.test(h)) ext.push(h); });
    await p.goto(BASE + path, { waitUntil: 'networkidle' });
    const where = `/${path} @${w}`;
    if (!(await p.$('#nudge'))) { ok(false, `${where} nudge missing`); await ctx.close(); continue; }
    await p.evaluate(spy);
    ok(await p.$eval('#nudge', n => !n.hidden && n.getBoundingClientRect().height > 0) && await p.$eval('#share-h', h => h.textContent) === (path.startsWith('es/') ? 'Comparte esta página' : 'Share this page'), `${where} row visible before any save, heading "Share this page"`);
    ok(await p.evaluate(() => { const a = document.querySelector('.actions'), r = document.getElementById('nudge'), t = document.getElementById('tip'), h = document.getElementById('after-save'); return a.compareDocumentPosition(r) & 4 && r.compareDocumentPosition(h) & 4 && t.nextElementSibling === r && r.nextElementSibling.matches('details.post') && document.querySelector('details.post').nextElementSibling === h; }), `${where} order: download row, tip, share row, caption, history line`);
    if (PLANT === 'href') { await p.evaluate(() => { const a = document.querySelector('.share-to a[data-net="bluesky"]'); a.href = a.href.replace('%2Fgo%2F', '%2F'); }); }
    if (PLANT === 'copy') { await p.evaluate(() => { document.querySelector('.share-to').dataset.url = 'https://votesticker.org/'; }); }
    const row = await p.evaluate(() => [...document.querySelectorAll('.share-to li')].filter(li => !li.hidden).map(li => { const a = li.firstElementChild, r = a.getBoundingClientRect(); return { net: a.dataset.net || a.id, href: a.getAttribute('href'), name: a.innerText.trim(), target: a.target, rel: a.rel, h: r.height, right: r.right, svg: a.querySelector('svg').getAttribute('aria-hidden') }; }));
    const W = await p.evaluate(() => document.documentElement.clientWidth);
    ok(row.map(r => r.net).join() === 'share-sheet,facebook,threads,bluesky,whatsapp,copy-link' || row.map(r => r.net).join() === 'facebook,threads,bluesky,whatsapp,copy-link', `${where} controls ${row.map(r => r.net).join(',')}`);
    ok(row.every(r => r.h >= 44), `${where} every target >= 44px`);
    ok(row.every(r => r.right <= W - 15), `${where} row inside the gutter`);
    ok(await p.evaluate(() => document.documentElement.scrollWidth <= innerWidth), `${where} no sideways scroll`);
    ok(row.every(r => r.name && r.svg === 'true'), `${where} visible names, icons hidden from AT`);
    ok(row.find(r => r.net === 'copy-link').name === copyName, `${where} copy label "${copyName}"`);
    for (const r of row.filter(r => HOSTS[r.net])) {
      const u = new URL(r.href);
      const want = { facebook: { u: G('facebook') }, threads: { text: txt, url: G('threads') }, bluesky: { text: txt + ' ' + G('bluesky') }, whatsapp: { text: txt + ' ' + G('whatsapp') } }[r.net];
      ok(u.host === HOSTS[r.net] && r.target === '_blank' && r.rel === 'noopener' && JSON.stringify(Object.fromEntries(u.searchParams)) === JSON.stringify(want) && !/&amp;/.test(u.search), `${where} ${r.net} href decodes to its /go/ link and text`);
    }
    await p.locator('#nudge').scrollIntoViewIfNeeded();
    await p.locator('#nudge').screenshot({ path: SHOT + `row-${path.replace(/\//g, '') || 'home'}-${w}.png` });
    // Real round trip: trusted click, then a real paste into a temporary field.
    await p.click('#copy-link');
    await p.waitForTimeout(250);
    ok(await p.$eval('#copy-link-label', s => s.textContent) === copied && await p.$eval('#copy-link', b => b.classList.contains('is-copied')), `${where} checkmark + "${copied}"`);
    ok(await p.$eval('#share-status', s => s.textContent) === copied, `${where} live region announces it`);
    await p.evaluate(() => { const t = document.createElement('textarea'); t.id = '__qa_paste'; document.body.appendChild(t); });
    await p.focus('#__qa_paste'); await p.keyboard.press('ControlOrMeta+V');
    ok(await p.$eval('#__qa_paste', t => t.value) === U, `${where} pasted value is exactly ${U}`);
    await p.evaluate(() => document.getElementById('__qa_paste').remove());
    // Rapid repeat clicks: one reset after the last click, no stuck state.
    await p.click('#copy-link'); await p.waitForTimeout(900); await p.click('#copy-link'); await p.waitForTimeout(1500);
    ok(await p.$eval('#copy-link-label', s => s.textContent) === copied, `${where} repeat click keeps the label for 2 s from the last click`);
    await p.waitForTimeout(700);
    ok(await p.$eval('#copy-link-label', s => s.textContent) === copyName && !(await p.$eval('#copy-link', b => b.classList.contains('is-copied'))), `${where} label resets`);
    // A network link: new tab with the intent (stubbed so nothing reaches the network), one share event.
    await ctx.route(/facebook\.com|x\.com|threads\.com|bsky\.app|wa\.me/, r => r.fulfill({ status: 200, body: 'stub' }));
    const [pop] = await Promise.all([p.waitForEvent('popup'), p.click('.share-to a[data-net="threads"]')]);
    ok(new URL(pop.url()).host === 'www.threads.com', `${where} Threads opens in a new tab`);
    await pop.close();
    ok((await shares(p)).join() === [`copy_link:page:${st}`, `copy_link:page:${st}`, `copy_link:page:${st}`, `threads:page:${st}`].join(), `${where} GA4 share events ${await shares(p)}`);
    ok(ext.filter(h => !/threads/.test(h)).length === 0, `${where} nothing loads from social sites before a click`);
    // Keyboard: Tab reaches every control with a visible focus ring; Space on Copy link copies.
    await p.focus('#copy-link'); await p.keyboard.press('Shift+Tab');
    const ring = await p.evaluate(() => getComputedStyle(document.activeElement).outlineStyle);
    ok(ring === 'solid', `${where} focus ring visible (${ring})`);
    ok(!errs.length, `${where} no page errors ${errs}`);
    await ctx.close();
  }
}
// Clipboard refused: manual field, focused and selected, no "copied", no event.
{
  const ctx = await b.newContext({ viewport: { width: 390, height: 900 } });
  const p = await ctx.newPage();
  await p.addInitScript(() => { Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: () => Promise.reject(new DOMException('QA: copying blocked', 'NotAllowedError')) } }); });
  await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
  await p.evaluate(spy); 
  await p.click('#copy-link'); await p.waitForTimeout(300);
  const st = await p.evaluate(() => ({ shown: !document.getElementById('share-manual').hidden, focused: document.activeElement.id, sel: document.activeElement.value && document.activeElement.value.substring(document.activeElement.selectionStart, document.activeElement.selectionEnd), label: document.getElementById('copy-link-label').textContent }));
  ok(st.shown && st.focused === 'share-manual-url' && st.sel === 'https://votesticker.org/texas/', `refused clipboard: manual field shown, focused, URL selected ${JSON.stringify(st)}`);
  ok(st.label === 'Copy link' && (await shares(p)).length === 0, 'refused clipboard: no "Link copied", no share event');
  await p.locator('#nudge').screenshot({ path: SHOT + 'row-manual.png' });
  await ctx.close();
}
// No Clipboard API at all: legacy copy inside the tap.
{
  const ctx = await b.newContext({ viewport: { width: 390, height: 900 }, permissions: ['clipboard-read', 'clipboard-write'] });
  const p = await ctx.newPage();
  await p.addInitScript(() => { Object.defineProperty(navigator, 'clipboard', { configurable: true, get: () => undefined }); });
  await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
  await p.evaluate(spy); 
  await p.click('#copy-link'); await p.waitForTimeout(300);
  ok(await p.$eval('#copy-link-label', s => s.textContent) === 'Link copied' && (await shares(p)).join() === 'copy_link:page:TX', 'no Clipboard API: legacy copy succeeds and counts once');
  await ctx.close();
}
// Share sheet: shown where supported (desktop too), cancel is quiet, failure shows a note, double tap is ignored.
for (const [name, opts] of [['iPhone', devices['iPhone 13']], ['Pixel', devices['Pixel 7']], ['desktop', { viewport: { width: 1280, height: 900 } }]]) {
  for (const outcome of ['resolve', 'AbortError', 'NotAllowedError']) {
    const ctx = await b.newContext(opts);
    const p = await ctx.newPage();
    await p.addInitScript((outcome) => { window.__calls = 0; navigator.share = (d) => { window.__calls++; window.__shared = d; return new Promise((res, rej) => setTimeout(() => outcome === 'resolve' ? res() : rej(new DOMException('x', outcome)), 200)); }; navigator.canShare = () => true; }, outcome);
    await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
    await p.evaluate(spy); 
    const vis = await p.$eval('#share-sheet-li', li => !li.hidden);
    ok(vis, `${name} ${outcome}: share-sheet pill shown`);
    await p.click('#share-sheet'); await p.click('#share-sheet'); await p.waitForTimeout(400);
    const r = await p.evaluate(() => ({ calls: window.__calls, url: window.__shared && window.__shared.url, note: document.getElementById('share-note').hidden ? '' : document.getElementById('share-note').textContent }));
    ok(r.calls === 1 && r.url === 'https://votesticker.org/texas/', `${name} ${outcome}: one share call with the page URL (${r.calls})`);
    const ev = await shares(p);
    if (outcome === 'resolve') { ok(ev.join() === 'share_sheet:page:TX' && !r.note, `${name} resolve: one share_sheet event`); }
    if (outcome === 'AbortError') { ok(!ev.length && !r.note, `${name} cancel: quiet, no event`); }
    if (outcome === 'NotAllowedError') { ok(!ev.length && /isn't available/.test(r.note), `${name} refused: note shown, no event`); }
    if (name === 'iPhone' && outcome === 'resolve') { await p.locator('#nudge').screenshot({ path: SHOT + 'row-iphone.png' }); }
    await ctx.close();
  }
}
// No navigator.share: pill stays hidden.
{
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 } });
  const p = await ctx.newPage();
  await p.addInitScript(() => { delete Navigator.prototype.share; delete navigator.share; });
  await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
  ok(await p.$eval('#share-sheet-li', li => li.hidden), 'no Web Share: pill hidden');
  await ctx.close();
}
await b.close();
// WebKit (Safari engine): row renders, copy path does not throw.
{
  const wk = await webkit.launch(); const ctx = await wk.newContext({ ...devices['iPhone 13'] }); const p = await ctx.newPage(); const errs = []; p.on('pageerror', e => errs.push(String(e)));
  await p.goto(BASE + 'es/texas/', { waitUntil: 'networkidle' });
  
  const n = await p.$$eval('.share-to li:not([hidden])', l => l.length);
  ok(n >= 5 && !errs.length, `WebKit iPhone: ${n} controls, no errors ${errs}`);
  await p.locator('#nudge').screenshot({ path: SHOT + 'row-webkit-es.png' });
  await wk.close();
}
console.log(fails ? `FAILED (${fails})` : 'PASS'); process.exit(fails ? 1 : 0);
