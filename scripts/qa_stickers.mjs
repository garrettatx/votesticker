// QA for the All Stickers page (/stickers/, /es/stickers/; index.php vote_stickers_page), at phone to desktop
// widths. Fails on: a missing state section or jump link; a state whose row doesn't hold the same stickers as its
// own gallery; a tile without a full-size JPG to save, a download name or alt text; a thumbnail that doesn't load;
// two stickers of the same color side by side; the classic set missing or not saving its PNG; the state box
// not jumping to the row; sideways scroll; a tap that doesn't download the JPG; a page with
// no link to /stickers/ (home, a state, history); a sticker without a thumbnail file (build/thumbs.py --check).
//   node scripts/qa_stickers.mjs [--base http://127.0.0.1:8792/] [--plant overflow|order|nothumb]
// --plant injects a known defect, to prove the check fires (it must then FAIL).
import { chromium } from 'playwright';
import { execFileSync } from 'node:child_process';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/');
const PLANT = arg('--plant', '');
let fails = 0;
const fail = (m) => { console.log('  FAIL ' + m); fails++; };
try { execFileSync('python3', ['build/thumbs.py', '--check'], { stdio: 'pipe' }); } catch (e) { fail('thumbnails: ' + String(e.stdout).trim().split('\n').pop()); }
const b = await chromium.launch();
const plant = async (p) => {
	if (PLANT === 'overflow') { await p.addStyleTag({ content: '.grid--all { min-width: 900px; }' }); }
	// Sorting each row by its alt text puts look-alikes together, which the order check must catch.
	if (PLANT === 'order') { await p.evaluate(() => document.querySelectorAll('.grid--all').forEach((g) => [...g.children].sort((x, y) => x.querySelector('img').alt.localeCompare(y.querySelector('img').alt)).forEach((t) => g.appendChild(t)))); }
	if (PLANT === 'nothumb') { await p.evaluate(() => { const i = document.querySelector('.grid--all img'); i.loading = 'eager'; i.src = '/assets/thumbs/none.webp'; }); await p.waitForTimeout(500); }
};
for (const lang of ['', 'es/']) {
	const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
	const res = await p.goto(BASE + lang + 'stickers/', { waitUntil: 'networkidle' });
	if (!res || res.status() !== 200) { fail(`${lang}stickers/: HTTP ${res && res.status()}`); await p.close(); continue; }
	await plant(p);
	const r = await p.evaluate(() => {
		const out = { sections: [], jump: document.querySelectorAll('.all__az a').length, bad: [], same: [], classic: [...document.querySelectorAll('#classic-grid a.pick')].map((a) => [a.getAttribute('href').split('?')[0], a.getAttribute('download'), (a.querySelector('img') || {}).alt || '']) };
		const color = (alt) => (alt.match(/^(.*?) (sticker|de |con )/i) || [, alt])[1].toLowerCase();
		document.querySelectorAll('.all__state:not(.all__state--classic)').forEach((sec) => {
			const tiles = [...sec.querySelectorAll('a.tile')];
			out.sections.push({ id: sec.id, state: sec.querySelector('.grid--all').dataset.state, files: tiles.map((a) => a.getAttribute('href').split('?')[0]) });
			tiles.forEach((a, i) => {
				const img = a.querySelector('img');
				if (!/\/assets\/states\/.+\.jpg$/.test(a.getAttribute('href').split('?')[0])) { out.bad.push(sec.id + ' tile ' + i + ': href is not a sticker JPG'); }
				if (!a.getAttribute('download')) { out.bad.push(sec.id + ' tile ' + i + ': no download name'); }
				if (!img || !img.alt) { out.bad.push(sec.id + ' tile ' + i + ': no alt text'); }
				if (i && img && color(img.alt) === color(tiles[i - 1].querySelector('img').alt)) { out.same.push(sec.id + ' ' + i + ' (' + color(img.alt) + ')'); }
			});
		});
		return out;
	});
	if (r.classic.length < 40) { fail(`${lang}stickers/: ${r.classic.length} classic stickers, want 42`); }
	r.classic.filter((c) => !/\/assets\/classic\/.+\.png$/.test(c[0]) || !c[1] || !c[2]).slice(0, 3).forEach((c) => fail(`${lang}stickers/ classic ${c[0]}: missing PNG link, download name or alt`));
	if (r.sections.length !== 51) { fail(`${lang}stickers/: ${r.sections.length} state sections, want 51`); }
	if (r.jump !== r.sections.length) { fail(`${lang}stickers/: ${r.jump} jump links for ${r.sections.length} sections`); }
	r.bad.slice(0, 5).forEach((m) => fail(`${lang}stickers/ ${m}`));
	if (r.same.length) { fail(`${lang}stickers/: ${r.same.length} same-color neighbors, e.g. ${r.same.slice(0, 3).join(', ')}`); }
	// Thumbnails: every image in the first three rows and in Texas's row must load.
	await p.evaluate(() => { document.querySelectorAll('#st-classic img, #st-alabama img, #st-alaska img, #st-arizona img, #st-texas img').forEach((i) => { i.loading = 'eager'; }); });
	await p.waitForTimeout(1500);
	const broken = await p.evaluate(() => [...document.querySelectorAll('#st-classic img, #st-alabama img, #st-alaska img, #st-arizona img, #st-texas img')].filter((i) => !i.complete || !i.naturalWidth).map((i) => i.getAttribute('src')));
	if (broken.length) { fail(`${lang}stickers/: ${broken.length} thumbnails didn't load, e.g. ${broken[0]}`); }
	// Same stickers as the state's own gallery, for four states.
	for (const slug of ['texas', 'california', 'district-of-columbia', 'colorado']) {
		const row = (r.sections.find((s) => s.id === 'st-' + slug) || { files: [] }).files.slice().sort();
		const q = await b.newPage();
		await q.goto(BASE + lang + slug + '/', { waitUntil: 'domcontentloaded' });
		const gal = (await q.evaluate(() => [...document.querySelectorAll('#gallery-grid a.tile')].map((a) => a.getAttribute('href').split('?')[0]))).sort();
		const links = await q.evaluate(() => [...document.querySelectorAll('a[href*="stickers/"]')].map((a) => a.getAttribute('href')));
		await q.close();
		if (JSON.stringify(row) !== JSON.stringify(gal)) { fail(`${lang}stickers/ #st-${slug}: ${row.length} stickers, its gallery has ${gal.length} (or different files)`); }
		if (!links.some((h) => h.includes('stickers/#st-' + slug))) { fail(`${lang}${slug}/: no link to its row on the All Stickers page`); }
	}
	// The state box jumps to the row: type, Enter, land on Texas with the hash set; no navigation away.
	await p.evaluate(() => scrollTo(0, 0));
	await p.fill('#state-find', 'Tex');
	await p.keyboard.press('Enter');
	await p.waitForTimeout(600);
	const j = await p.evaluate(() => ({ path: location.pathname, hash: location.hash, top: Math.round(document.getElementById('st-texas').getBoundingClientRect().top) }));
	if (!/stickers\/$/.test(j.path) || j.hash !== '#st-texas' || j.top < -2 || j.top > 60) { fail(`${lang}stickers/: state box jump landed at ${j.path}${j.hash}, row top ${j.top}px`); }
	// A tap downloads the full file (desktop path; iOS uses the share sheet, tested in qa_save.mjs).
	if (!lang) {
		const c = p.locator('#classic-grid a.pick').first();
		await c.scrollIntoViewIfNeeded();
		const [cd] = await Promise.all([p.waitForEvent('download', { timeout: 8000 }).catch(() => null), c.click()]);
		if (!cd || !/\.png$/.test(cd.suggestedFilename())) { fail(`stickers/: tapping a classic sticker gave ${cd ? cd.suggestedFilename() : 'no download'}`); }
		const first = p.locator('#st-texas a.tile').first();
		await first.scrollIntoViewIfNeeded();
		const [dl] = await Promise.all([p.waitForEvent('download', { timeout: 8000 }).catch(() => null), first.click()]);
		if (!dl) { fail('stickers/: tapping a Texas sticker started no download'); }
		else if (!/^vote-tx-\d+\.jpg$/.test(dl.suggestedFilename())) { fail(`stickers/: download named ${dl.suggestedFilename()}`); }
	}
	await p.close();
	for (const w of [320, 390, 768, 1280]) {
		const q = await b.newPage({ viewport: { width: w, height: 800 } });
		await q.goto(BASE + lang + 'stickers/', { waitUntil: 'domcontentloaded' });
		await plant(q);
		const over = await q.evaluate(() => document.documentElement.scrollWidth - innerWidth);
		if (over > 0) { fail(`${lang}stickers/ @${w}: page scrolls sideways by ${over}px`); }
		await q.close();
	}
}
// Links in: the footer on every page type, plus the home page's state list.
for (const path of ['', 'texas/', 'history/', 'about/', 'es/']) {
	const q = await b.newPage();
	await q.goto(BASE + path, { waitUntil: 'domcontentloaded' });
	const foot = await q.evaluate(() => [...document.querySelectorAll('.foot__links a')].some((a) => /\/stickers\/$/.test(a.getAttribute('href'))));
	if (!foot) { fail(`${path || '/'}: no All stickers link in the footer`); }
	await q.close();
}
await b.close();
console.log(fails ? `FAILED (${fails})` : `PASS${PLANT ? ' (plant ' + PLANT + ')' : ''}`);
process.exit(fails ? 1 : 0);
