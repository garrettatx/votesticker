// Save QA for votesticker.org (October 22, after ready-made stickers went to Files instead of Photos on
// iOS 27). Taps a ready-made sticker and the maker's save button as an iPhone, an Android phone and a
// desktop, and fails on any of these:
//   - iOS: any `download` event (that is what sends a picture to Files), a share without exactly one
//     image file of the right type, name and size, or a failed share that doesn't fall back to press
//     and hold.
//   - Android and desktop: no download, or a download with the wrong name.
// The share sheet itself can't run headless, so navigator.share is stubbed per case: supported,
// missing (in-app browsers), refused (NotAllowedError) and closed (AbortError).
//
//   php -S 127.0.0.1:8791 -t site      (or serve dist/)
//   node scripts/qa_save.mjs [--base http://127.0.0.1:8791/]
import { webkit, chromium, devices } from 'playwright';

const args = process.argv.slice(2);
const BASE = (() => { const i = args.indexOf('--base'); return i >= 0 ? args[i + 1] : 'http://127.0.0.1:8791/'; })();
let failed = 0;
const fail = (m) => { failed++; console.log('  FAIL ' + m); };
const ok = (m) => console.log('  ok   ' + m);

// mode: 'ok' | 'missing' | 'refused' | 'abort'
const stub = (mode) => {
	window.__shared = [];
	const proto = Navigator.prototype;
	if (mode === 'missing') {
		try { delete proto.share; delete proto.canShare; } catch (e) {}
		Object.defineProperty(navigator, 'share', { value: undefined, configurable: true });
		Object.defineProperty(navigator, 'canShare', { value: undefined, configurable: true });
		return;
	}
	Object.defineProperty(navigator, 'canShare', { configurable: true, value: (d) => !!(d && d.files && d.files.every((f) => /^image\//.test(f.type))) });
	Object.defineProperty(navigator, 'share', { configurable: true, value: (d) => {
		window.__shared.push((d.files || []).map((f) => ({ name: f.name, type: f.type, size: f.size })));
		if (mode === 'refused') { return Promise.reject(Object.assign(new Error('no'), { name: 'NotAllowedError' })); }
		if (mode === 'abort') { return Promise.reject(Object.assign(new Error('closed'), { name: 'AbortError' })); }
		return Promise.resolve();
	} });
};

async function open(type, device, mode, url) {
	const browser = await type.launch();
	const ctx = await browser.newContext({ ...device, acceptDownloads: true });
	if (mode) { await ctx.addInitScript(stub, mode); }
	const page = await ctx.newPage();
	const errors = [], downloads = [];
	page.on('pageerror', (e) => errors.push(String(e)));
	page.on('download', (d) => downloads.push(d.suggestedFilename()));
	await page.goto(BASE + url, { waitUntil: 'networkidle' });
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	return { browser, page, errors, downloads };
}

// Bring the first ready-made into view and wait until its file is in hand (as after a person scrolls to it).
async function readyTile(page) {
	if (!(await page.locator('#gallery-grid a.tile').count())) { return null; }
	const tile = page.locator('#gallery-grid a.tile').first();
	await tile.scrollIntoViewIfNeeded();
	await page.waitForFunction(() => { const i = document.querySelector('#gallery-grid a.tile img'); return i && i.complete && i.naturalWidth > 0; });
	return tile;
}

const IPHONE = devices['iPhone 15'];
const ANDROID = devices['Pixel 7'];
const DESKTOP = { viewport: { width: 1280, height: 900 } };

for (const [url, lang] of [['texas/', 'en'], ['es/texas/', 'es']]) {
	console.log(`iPhone, share sheet available (${lang})`);
	{
		const { browser, page, errors, downloads } = await open(webkit, IPHONE, 'ok', url);
		const label = await page.textContent('#save');
		(/Save to Photos|Guardar en Fotos/.test(label) ? ok : fail)(`maker button reads "${label}"`);
		const tile = await readyTile(page);
		if (tile) {
			await page.waitForFunction(() => window.VOTE_CARDS.readyCount() > 0, null, { timeout: 5000 }).catch(() => fail('ready-made file never fetched'));
			await tile.click();
			await page.waitForTimeout(300);
			const s0 = await page.evaluate(() => window.__shared);
			const f = s0[0] && s0[0][0];
			(s0.length === 1 && s0[0].length === 1 && f.type === 'image/jpeg' && /^vote-tx-1\.jpg$/.test(f.name) && f.size > 20000 ? ok : fail)(`ready-made shares one JPEG file: ${JSON.stringify(s0)}`);
		}
		const before = (await page.evaluate(() => window.__shared)).length;
		await page.locator('#save').scrollIntoViewIfNeeded();
		await page.click('#save');
		await page.waitForTimeout(300);
		const s = await page.evaluate(() => window.__shared);
		const m = s[before] && s[before][0];
		(s.length === before + 1 && m && m.type === 'image/png' && /^vote-early-tx-.+\.png$/.test(m.name) && m.size > 20000 ? ok : fail)(`maker shares one PNG file: ${JSON.stringify(s[before])}`);
		(downloads.length === 0 ? ok : fail)(`no downloads (${downloads.join(', ') || 'none'})`);
		(await page.locator('#hold-save').count() === 0 ? ok : fail)('no press-and-hold overlay when sharing works');
		(errors.length === 0 ? ok : fail)(`no page errors ${errors.join(' | ')}`);
		await browser.close();
	}

	for (const mode of ['missing', 'refused']) {
		console.log(`iPhone, share ${mode === 'missing' ? 'not available (in-app browser)' : 'refused'} (${lang})`);
		const { browser, page, errors, downloads } = await open(webkit, IPHONE, mode, url);
		const tile = await readyTile(page);
		if (tile) {
			await page.waitForTimeout(400);
			const src = await tile.getAttribute('href');
			await tile.click();
			await page.waitForTimeout(300);
			const shown = await page.evaluate(() => { const b = document.getElementById('hold-save'); return b && !b.hidden ? { src: b.querySelector('img').getAttribute('src'), alt: b.querySelector('img').alt } : null; });
			(shown && shown.src === src && shown.alt ? ok : fail)(`ready-made opens press and hold with the picture: ${JSON.stringify(shown)}`);
			await page.click('.hold-save__close');
			(await page.locator('#hold-save').isHidden() ? ok : fail)('Done closes it');
		}
		await page.locator('#save').scrollIntoViewIfNeeded();
		await page.click('#save');
		await page.waitForTimeout(300);
		const shown2 = await page.evaluate(() => { const b = document.getElementById('hold-save'); return b && !b.hidden ? b.querySelector('img').getAttribute('src') : null; });
		(shown2 && /^blob:/.test(shown2) ? ok : fail)(`maker opens press and hold with its picture: ${shown2}`);
		const text = await page.textContent('.hold-save__h');
		((lang === 'es' ? /Mantén presionada/ : /Press and hold/).test(text || '') ? ok : fail)(`press-and-hold text in ${lang}: "${text}"`);
		(downloads.length === 0 ? ok : fail)(`no downloads (${downloads.join(', ') || 'none'})`);
		(errors.length === 0 ? ok : fail)(`no page errors ${errors.join(' | ')}`);
		await browser.close();
	}

	console.log(`iPhone, share sheet closed without saving (${lang})`);
	{
		const { browser, page, downloads } = await open(webkit, IPHONE, 'abort', url);
		await page.locator('#save').scrollIntoViewIfNeeded();
		await page.click('#save');
		await page.waitForTimeout(300);
		const vis = await page.evaluate(() => { const b = document.getElementById('hold-save'); return !!(b && !b.hidden); });
		(!vis && downloads.length === 0 ? ok : fail)('closing the share sheet does nothing else');
		await browser.close();
	}
}

console.log('iPhone, tap before the picture has loaded');
{
	const { browser, page, downloads } = await open(webkit, IPHONE, 'ok', 'texas/');
	// The last tile, never scrolled to, so its lazy image (and file) isn't loaded yet.
	const last = page.locator('#gallery-grid a.tile').last();
	const how = await last.evaluate((a) => { a.click(); return window.VOTE_LAST_SAVE; });
	(how === 'hold' || how === 'share' ? ok : fail)(`saves by ${how}`);
	(downloads.length === 0 ? ok : fail)('no download');
	await browser.close();
}

for (const [name, type, device] of [['Android', chromium, ANDROID], ['Desktop Chrome', chromium, DESKTOP], ['Desktop Safari', webkit, DESKTOP]]) {
	console.log(name);
	const { browser, page, errors, downloads } = await open(type, device, null, 'texas/');
	const tile = await readyTile(page);
	const [d1] = await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), tile.click()]);
	(d1 && d1.suggestedFilename() === 'vote-tx-1.jpg' ? ok : fail)(`ready-made downloads as ${d1 && d1.suggestedFilename()}`);
	await page.locator('#save').scrollIntoViewIfNeeded();
	const [d2] = await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), page.click('#save')]);
	(d2 && /^vote-early-tx-.+\.png$/.test(d2.suggestedFilename()) ? ok : fail)(`maker downloads as ${d2 && d2.suggestedFilename()}`);
	(await page.locator('#hold-save').count() === 0 ? ok : fail)('no press-and-hold overlay');
	(errors.length === 0 ? ok : fail)(`no page errors ${errors.join(' | ')}`);
	await browser.close();
}

console.log(failed ? `FAILED (${failed})` : 'PASS');
process.exit(failed ? 1 : 0);
