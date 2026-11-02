// QA for the Election Day bar above the header (index.php vote_eday_bar, November 2 and 3), at phone to desktop
// widths, English and Spanish. Fails on: the bar missing (or present outside November 2 and 3); the old yellow
// .eday banner still on a page; the bar not above the header; sideways scroll; a bar taller than 110px on a
// phone or 60px from 768px; a close button under 44 x 44 or without a name; a link on the home page (its state
// box is right below); a missing link elsewhere, or one that lands nowhere; a state page jump that doesn't bring
// "When, Where, and What to Bring" to the top; closing it not keeping it closed on other pages, the other
// language, an open tab and a reload; the bar showing before the close is applied (flash); keyboard focus left on
// the hidden button; a close button with no script behind it (JavaScript off).
//   node scripts/qa_eday_bar.mjs [--base http://127.0.0.1:8792/] [--expect on|off] [--plant small|overflow|nostore]
// --expect defaults to "on" on November 2 and 3 (Central) and "off" otherwise. --plant injects a known defect,
// to prove the check fires (it must then FAIL).
import { chromium } from 'playwright';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/');
const PLANT = arg('--plant', '');
const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'America/Chicago' }).format(new Date());
const EXPECT = arg('--expect', ['2026-11-02', '2026-11-03'].includes(today) ? 'on' : 'off');
const PLANTS = {
	small: '.notice__x { width: 20px !important; height: 20px !important; }',
	overflow: '.notice p { min-width: 700px; }',
};
const KEY = 'vote-sticker:eday-bar-2026';
const PAGES = ['', 'es/', 'pennsylvania/', 'es/pennsylvania/', 'washington/', 'texas/', 'history/', 'es/history/', 'about/', 'es/about/', 'stickers/', 'es/stickers/'];
const WIDTHS = [320, 390, 768, 1280];
let fails = 0;
const fail = (m) => { console.log('  FAIL ' + m); fails++; };
const b = await chromium.launch();
const plantPage = async (p) => { if (PLANTS[PLANT]) { await p.addStyleTag({ content: PLANTS[PLANT] }); } };
const noStore = () => { Storage.prototype.setItem = function () {}; };

for (const path of PAGES) {
	for (const w of WIDTHS) {
		const ctx = await b.newContext({ viewport: { width: w, height: 844 } });
		const p = await ctx.newPage();
		await p.goto(BASE + path, { waitUntil: 'networkidle' });
		await plantPage(p);
		const r = await p.evaluate(() => {
			const bar = document.getElementById('eday-bar');
			const out = { bar: !!bar, old: document.querySelectorAll('section.eday').length, over: document.documentElement.scrollWidth - innerWidth };
			if (!bar) { return out; }
			const bb = bar.getBoundingClientRect();
			const x = document.getElementById('eday-bar-x');
			const xb = x.getBoundingClientRect();
			const links = bar.querySelectorAll('a');
			const head = document.querySelector('header.top');
			out.h = Math.round(bb.height);
			out.aboveHeader = !!head && (bar.compareDocumentPosition(head) & Node.DOCUMENT_POSITION_FOLLOWING) > 0 && bb.bottom <= head.getBoundingClientRect().top + 1;
			out.x = [Math.round(xb.width), Math.round(xb.height)];
			out.xName = x.getAttribute('aria-label') || '';
			out.xVisible = !x.hidden && xb.width > 0;
			out.links = Array.prototype.map.call(links, (a) => a.getAttribute('href'));
			out.isState = !!document.getElementById('before-h');
			return out;
		});
		const tag = `${path || '/'} @${w}`;
		if (EXPECT === 'off') { if (r.bar) { fail(`${tag}: bar shows outside November 2 and 3`); } await ctx.close(); continue; }
		if (!r.bar) { fail(`${tag}: no bar`); await ctx.close(); continue; }
		if (r.old) { fail(`${tag}: old yellow .eday banner still on the page`); }
		if (!r.aboveHeader) { fail(`${tag}: bar is not above the header`); }
		if (r.over > 0) { fail(`${tag}: page scrolls sideways by ${r.over}px`); }
		if (r.h > (w < 768 ? 110 : 60)) { fail(`${tag}: bar is ${r.h}px tall`); }
		if (!r.xVisible) { fail(`${tag}: close button not shown`); }
		if (r.x[0] < 44 || r.x[1] < 44) { fail(`${tag}: close button ${r.x.join('x')}, under 44 x 44`); }
		if (!r.xName) { fail(`${tag}: close button has no accessible name`); }
		const hub = path === '' || path === 'es/';
		if (hub && r.links.length) { fail(`${tag}: home page bar has a link (${r.links})`); }
		if (!hub && r.links.length !== 1) { fail(`${tag}: ${r.links.length} links in the bar, want 1`); }
		if (!hub && r.links.length === 1) {
			const href = r.links[0];
			const want = r.isState ? '#before-h' : (path.startsWith('es/') ? '/es/#state-find' : '/#state-find');
			if (href !== want) { fail(`${tag}: link goes to ${href}, want ${want}`); }
			if (r.isState) {
				await p.click('#eday-bar a');
				await p.waitForTimeout(900);
				const top = await p.evaluate(() => Math.round(document.getElementById('before-h').getBoundingClientRect().top));
				if (top < -2 || top > 48) { fail(`${tag}: jump puts "When, Where, and What to Bring" at ${top}px`); }
			}
		}
		await ctx.close();
	}
}

if (EXPECT === 'on') {
	// Closing: once, by keyboard, on a state page; then every other page, the other language, an open tab, a reload.
	const ctx = await b.newContext({ viewport: { width: 390, height: 844 } });
	if (PLANT === 'nostore') { await ctx.addInitScript(noStore); }
	const open = await ctx.newPage();
	await open.goto(BASE + 'history/', { waitUntil: 'networkidle' });
	const p = await ctx.newPage();
	await p.goto(BASE + 'pennsylvania/', { waitUntil: 'networkidle' });
	await p.focus('#eday-bar-x');
	await p.keyboard.press('Enter');
	const after = await p.evaluate(() => ({ hidden: document.getElementById('eday-bar').hidden, focus: document.activeElement && document.activeElement.className }));
	if (!after.hidden) { fail('close: bar still shown after the close button'); }
	if (after.focus !== 'brand') { fail(`close: focus went to "${after.focus}", want the logo link`); }
	await open.waitForTimeout(300);
	if (await open.evaluate(() => getComputedStyle(document.getElementById('eday-bar')).display) !== 'none') { fail('close: an already-open tab still shows the bar'); }
	for (const path of ['', 'es/', 'texas/', 'es/pennsylvania/', 'about/']) {
		await p.goto(BASE + path, { waitUntil: 'domcontentloaded' });
		const s = await p.evaluate(() => ({ cls: document.documentElement.classList.contains('eday-bar-closed'), d: getComputedStyle(document.getElementById('eday-bar')).display }));
		if (!s.cls || s.d !== 'none') { fail(`close: ${path || '/'} shows the bar again (head class ${s.cls}, display ${s.d})`); }
	}
	// Flash: the head script must hide it before the bar is parsed, so the first frame never has it.
	const seen = await p.evaluate(async (url) => new Promise((res) => {
		const f = document.createElement('iframe'); f.style.cssText = 'position:fixed;left:0;top:0;width:390px;height:400px';
		let hit = false;
		f.onload = () => res(hit);
		document.body.appendChild(f);
		const tick = () => { try { const d = f.contentDocument; const bar = d && d.getElementById('eday-bar'); if (bar && f.contentWindow.getComputedStyle(bar).display !== 'none' && bar.getBoundingClientRect().height > 0) { hit = true; } } catch (e) { /* not ready */ } if (f.contentDocument && f.contentDocument.readyState !== 'complete') { requestAnimationFrame(tick); } };
		f.src = url; requestAnimationFrame(tick);
	}), BASE + 'washington/');
	if (seen) { fail('flash: a closed bar was drawn before it was hidden'); }
	await ctx.close();
	// JavaScript off: the bar shows (the link still works), the close button doesn't.
	const nojs = await b.newContext({ javaScriptEnabled: false });
	const q = await nojs.newPage();
	await q.goto(BASE + 'pennsylvania/');
	if (!(await q.isVisible('#eday-bar'))) { fail('no JavaScript: bar hidden'); }
	if (await q.isVisible('#eday-bar-x')) { fail('no JavaScript: close button shown with no script behind it'); }
	await nojs.close();
}
await b.close();
console.log(fails ? `FAILED (${fails})` : `PASS (expect ${EXPECT}${PLANT ? ', plant ' + PLANT : ''})`);
process.exit(fails ? 1 : 0);
