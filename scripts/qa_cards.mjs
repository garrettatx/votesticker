// QA for votesticker.org (source: clients/hobby-sites/garrettn/docs/vote-cards/site/).
//
// Renders every state x design x palette x headline in a real browser and fails if any pixel
// outside the safe circle differs from the background (the badge ring is allowed at its radius),
// so no card loses content to a profile-picture crop. Also saves a contact sheet and page shots.
//
// Also checks every state page: the server-rendered state panel must match what app.js draws, and the
// state menu must go to the state's own page. SEO checks are in garrettn_vote_seo_qa.py.
//
//   Serve site/ (PHP) or dist/ (the static build):  php -S 127.0.0.1:8791 -t site
//   node scripts/garrettn_vote_cards_qa.mjs [--base http://127.0.0.1:8791/] [--out dir]
//   Live: --base https://votesticker.org/
import { chromium, webkit } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const args = process.argv.slice(2);
const opt = (k, d) => { const i = args.indexOf(k); return i >= 0 ? args[i + 1] : d; };
const BASE = opt('--base', 'http://127.0.0.1:8791/');
const OUT = opt('--out', '/tmp/vote-cards-qa');
fs.mkdirSync(OUT, { recursive: true });

let failed = 0;
for (const type of [chromium, webkit]) {
	const browser = await type.launch();
	const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
	const errors = [];
	page.on('pageerror', (e) => errors.push(String(e)));
	await page.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');

	const result = await page.evaluate(() => {
		const V = window.VOTE_CARDS, bad = [];
		const c = document.createElement('canvas');
		let n = 0;
		for (const st of window.VOTE_STATES) {
			// A state without verified early voting must never be offered an "early" headline.
			if (!(st.status === 'verified' && st.kind !== 'none')) {
				const wrong = V.headlinesFor(st).filter((k) => V.HEADLINES[k].early);
				if (wrong.length) bad.push(`${st.abbr}: offers early headlines ${wrong.join(', ')}`);
			}
			for (const d of V.DESIGNS) {
				for (const p of V.PALETTES) {
					for (const h of V.headlinesFor(st)) {
						const dateText = st.abbr === 'TX' ? 'OCT 24–NOV 1' : V.defaultDateLine(st, h);
						V.render(c, 540, { state: st.abbr, design: d.id, palette: p.id, headline: h, dateText });
						const px = c.getContext('2d').getImageData(0, 0, 540, 540).data;
						const bg = [px[0], px[1], px[2]];
						let outside = 0;
						for (let y = 0; y < 540; y += 2) {
							for (let x = 0; x < 540; x += 2) {
								const r = Math.hypot(x + 0.5 - 270, y + 0.5 - 270);
								if (r <= V.SAFE / 2 + 2) continue;
								if (d.id === 'badge' && Math.abs(r - 253) < 6) continue; // the ring
								const i = (y * 540 + x) * 4;
								if (Math.abs(px[i] - bg[0]) + Math.abs(px[i + 1] - bg[1]) + Math.abs(px[i + 2] - bg[2]) > 24) outside++;
							}
						}
						n++;
						if (outside) bad.push(`${st.abbr} ${d.id} ${p.id} ${h}: ${outside} px outside the safe circle`);
					}
				}
			}
		}
		return { n, bad };
	});
	console.log(`${type.name()}: ${result.n} cards rendered, ${result.bad.length} outside the safe circle`);
	result.bad.slice(0, 20).forEach((b) => console.log('  FAIL ' + b));
	if (errors.length) { console.log('  page errors: ' + errors.join(' | ')); }
	failed += result.bad.length + errors.length;

	if (type === chromium) {
		await page.screenshot({ path: path.join(OUT, 'page-desktop.png'), fullPage: true });
		const sheet = await page.evaluate((states) => {
			const V = window.VOTE_CARDS, t = 180, cols = 9;
			const items = [];
			for (const s of states) for (const d of V.DESIGNS) items.push([s, d.id, V.PALETTES[items.length % V.PALETTES.length].id]);
			const out = document.createElement('canvas');
			out.width = t * cols; out.height = t * Math.ceil(items.length / cols);
			const g = out.getContext('2d'), c = document.createElement('canvas');
			items.forEach(([s, d, p], i) => {
				const st = window.VOTE_STATES.find((x) => x.abbr === s);
				const h = V.defaultHeadline(st); V.render(c, t, { state: s, design: d, palette: p, headline: h, dateText: V.defaultDateLine(st, h) });
				g.drawImage(c, (i % cols) * t, Math.floor(i / cols) * t);
			});
			return out.toDataURL('image/png');
		}, ['TX', 'KS', 'MO', 'NY', 'WA', 'CA', 'CO', 'VA', 'DC', 'NC', 'PA', 'AL', 'NH', 'MS', 'MA', 'TN', 'MI', 'WV']);
		fs.writeFileSync(path.join(OUT, 'contact-sheet.png'), Buffer.from(sheet.split(',')[1], 'base64'));
		// Every page type at phone, tablet, laptop, and wide widths: no sideways scroll, finder and H1 on screen.
		let shots = 0;
		for (const w of [320, 375, 390, 768, 1024, 1440]) {
			const vp = await browser.newPage({ viewport: { width: w, height: 900 }, deviceScaleFactor: w < 800 ? 2 : 1 });
			for (const [name, url] of [['hub', ''], ['kansas', 'kansas/'], ['alabama', 'alabama/'], ['arizona', 'arizona/']]) {
				await vp.goto(BASE + url, { waitUntil: 'networkidle' });
				await vp.waitForTimeout(300);
				const bad = await vp.evaluate(() => {
					const out = [];
					if (document.documentElement.scrollWidth > window.innerWidth) { out.push('scrolls sideways by ' + (document.documentElement.scrollWidth - window.innerWidth) + 'px'); }
					const f = document.getElementById('state-find');
					if (!f || f.getBoundingClientRect().width < 160) { out.push('finder missing or too narrow'); }
					Array.prototype.forEach.call(document.querySelectorAll('button, .btn, a.share__btn, .finder input'), (el) => {
						const r = el.getBoundingClientRect();
						if (r.width && r.height < 40) { out.push('small target: ' + (el.id || el.className) + ' ' + Math.round(r.height) + 'px'); }
					});
					return out;
				});
				bad.forEach((m) => { console.log(`  FAIL ${name} at ${w}px: ${m}`); failed++; });
				await vp.screenshot({ path: path.join(OUT, `page-${name}-${w}.png`), fullPage: true });
				shots++;
			}
			await vp.close();
		}
		console.log(`  ${shots} page screenshots at 320 to 1440 px`);

		// The finder: "nor" lists North Carolina first, "tx" finds Texas, Enter goes to the page.
		const fp = await browser.newPage({ viewport: { width: 390, height: 844 } });
		await fp.goto(BASE, { waitUntil: 'networkidle' });
		await fp.fill('#state-find', 'tx');
		const first = await fp.textContent('#state-opt-0');
		if (!/^Texas/.test(first || '')) { console.log('  FAIL finder: "tx" first option is ' + first); failed++; }
		await fp.fill('#state-find', 'nor');
		const opts = await fp.$$eval('#state-options [role=option]', (els) => els.map((e) => e.firstChild.textContent));
		if (opts.join() !== 'North Carolina,North Dakota') { console.log('  FAIL finder: "nor" lists ' + opts.join()); failed++; }
		await fp.screenshot({ path: path.join(OUT, 'finder-open.png') });
		await fp.press('#state-find', 'Enter');
		await fp.waitForURL(BASE + 'north-carolina/');
		await fp.close();

		// Every state page: server panel == JS panel, gallery count matches, no page errors.
		const slug = (n) => n.toLowerCase().replace(/[^a-z]+/g, '-').replace(/^-|-$/g, '');
		const states = await page.evaluate(() => window.VOTE_STATES.map((s) => [s.abbr, s.name]));
		const sp = await browser.newPage();
		const spErr = [];
		sp.on('pageerror', (e) => spErr.push(String(e)));
		await sp.route('**/app.js*', (r) => r.abort()); // first pass: server HTML only
		const server = {};
		for (const [abbr, name] of states) {
			await sp.goto(BASE + slug(name) + '/', { waitUntil: 'domcontentloaded' });
			server[abbr] = [await sp.innerHTML('#info'), await sp.locator('#gallery-grid img').count()];
		}
		await sp.unroute('**/app.js*');
		let panel = 0;
		for (const [abbr, name] of states) {
			await sp.goto(BASE + slug(name) + '/', { waitUntil: 'networkidle' });
			const js = [await sp.innerHTML('#info'), await sp.locator('#gallery-grid img').count()];
			if (js[0] !== server[abbr][0]) { console.log(`  FAIL ${abbr}: server panel differs from app.js panel`); console.log('    php: ' + server[abbr][0].slice(0, 300)); console.log('    js:  ' + js[0].slice(0, 300)); failed++; }
			else if (js[1] !== server[abbr][1]) { console.log(`  FAIL ${abbr}: gallery ${server[abbr][1]} server vs ${js[1]} js`); failed++; }
			else { panel++; }
		}
		await sp.fill('#state-find', 'north car');
		await sp.click('#state-go');
		await sp.waitForURL(BASE + 'north-carolina/');
		console.log(`  ${panel} of ${states.length} state pages: server panel matches app.js; menu goes to ${new URL(sp.url()).pathname}`);
		if (spErr.length) { console.log('  state page errors: ' + spErr.slice(0, 3).join(' | ')); failed += spErr.length; }
	}
	await browser.close();
}
console.log(failed ? `FAILED (${failed})` : 'PASS');
console.log('Shots in ' + OUT);
process.exit(failed ? 1 : 0);
