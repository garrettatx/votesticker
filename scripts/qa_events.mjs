// Analytics QA for votesticker.org (October 23). window.gtag is replaced with a recorder, then each thing
// someone can finish is done once, and the run fails on any of these:
//   - a missing event, a wrong parameter, or an event outside the list in docs/analytics.md
//   - more than one event for one action (a tile or swatch click must send nothing)
//   - Google or Ahrefs analytics loading in an automated browser or off votesticker.org
//   - a post text link that doesn't go through /go/<state>/<version>
//
//   php -S 127.0.0.1:8791 -t site      (or serve dist/)
//   node scripts/qa_events.mjs [--base http://127.0.0.1:8791/]
import { webkit, chromium, devices } from 'playwright';

const args = process.argv.slice(2);
const BASE = (() => { const i = args.indexOf('--base'); return i >= 0 ? args[i + 1] : 'http://127.0.0.1:8791/'; })();
const ALLOWED = ['sticker_save', 'sticker_save_cancel', 'post_text_copy', 'share', 'state_search_miss'];
let failed = 0;
const fail = (m) => { failed++; console.log('  FAIL ' + m); };
const ok = (m) => console.log('  ok   ' + m);
const check = (cond, m) => (cond ? ok : fail)(m);

const record = (mode) => {
	window.__ev = [];
	window.gtag = (...a) => { if (a[0] === 'event') { window.__ev.push({ name: a[1], p: a[2] || {} }); } };
	if (mode) {
		Object.defineProperty(navigator, 'canShare', { configurable: true, value: () => true });
		Object.defineProperty(navigator, 'share', { configurable: true, value: () => mode === 'abort'
			? Promise.reject(Object.assign(new Error('closed'), { name: 'AbortError' })) : Promise.resolve() });
	}
};

async function open(type, device, url, mode) {
	const browser = await type.launch();
	const ctx = await browser.newContext({ ...device, acceptDownloads: true });
	await ctx.addInitScript(record, mode || null);
	const page = await ctx.newPage();
	const errors = [], tags = [];
	page.on('pageerror', (e) => errors.push(String(e)));
	page.on('request', (r) => { if (/googletagmanager|google-analytics|analytics\.ahrefs/.test(r.url())) { tags.push(r.url()); } });
	await page.goto(BASE + url, { waitUntil: 'networkidle' });
	return { browser, ctx, page, errors, tags };
}
const events = async (page) => {
	const ev = await page.evaluate(() => window.__ev.splice(0));
	ev.filter((e) => !ALLOWED.includes(e.name)).forEach((e) => fail(`event not in docs/analytics.md: ${e.name}`));
	return ev;
};
const sameParams = (got, want) => Object.keys(want).every((k) => got[k] === want[k]) && Object.keys(got).length === Object.keys(want).length;
const one = (list, name, want, label) => {
	check(list.length === 1 && list[0].name === name && sameParams(list[0].p, want), `${label}: ${JSON.stringify(list)}`);
};

console.log('Desktop Chrome, Texas');
{
	const { browser, ctx, page, errors, tags } = await open(chromium, { viewport: { width: 1280, height: 900 } }, 'texas/');
	await ctx.grantPermissions(['clipboard-read', 'clipboard-write']);
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	check(tags.length === 0, `no analytics scripts in an automated browser (${tags.length})`);
	check((await events(page)).length === 0, 'nothing on page load');
	// Picking a design or a color is not an event.
	await page.locator('#grid .tile').nth(2).click();
	await page.locator('#swatches .swatch').nth(3).click();
	await page.waitForTimeout(200);
	check((await events(page)).length === 0, 'design and color picks send nothing');

	const tile = page.locator('#gallery-grid a.tile').first();
	await tile.scrollIntoViewIfNeeded();
	await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), tile.click()]);
	one(await events(page), 'sticker_save', { state: 'TX', sticker_source: 'ready_made', design: 'ready_1', save_method: 'download' }, 'ready-made save');

	const want = await page.evaluate(() => ({ design: document.querySelector('#grid .tile[aria-pressed="true"]').dataset.design, message: document.getElementById('headline').value }));
	await page.locator('#save').scrollIntoViewIfNeeded();
	await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), page.click('#save')]);
	const ms = await events(page);
	one(ms, 'sticker_save', { state: 'TX', sticker_source: 'maker', design: want.design, palette: ms[0] && ms[0].p.palette, message: want.message, save_method: 'download' }, 'maker save');
	check(ms[0] && /^[a-z]+$/.test(ms[0].p.palette || ''), `maker save names the palette (${ms[0] && ms[0].p.palette})`);

	const post = await page.inputValue('#post-text');
	check(/https:\/\/votesticker\.org\/go\/texas\/plan\b/.test(post) && !/votesticker\.org\/texas\b/.test(post), `post text links through /go/texas/plan`);
	await page.click('#copy');
	await page.waitForTimeout(200);
	one(await events(page), 'post_text_copy', { state: 'TX', message: want.message, post_variant: 'plan', post_edited: 'no' }, 'copy post text');
	await page.click('#post-picks .pick-chip[data-variant="friends"]');
	check(/votesticker\.org\/go\/texas\/friends\b/.test(await page.inputValue('#post-text')), 'friends version links /go/texas/friends');
	await page.fill('#post-text', (await page.inputValue('#post-text')) + ' See you there.');
	await page.click('#copy');
	await page.waitForTimeout(200);
	one(await events(page), 'post_text_copy', { state: 'TX', message: want.message, post_variant: 'friends', post_edited: 'yes' }, 'copy an edited friends version');

	await page.click('#share-link');
	await page.waitForTimeout(200);
	one(await events(page), 'share', { method: 'copy_link', content_type: 'page', item_id: 'TX' }, 'copy page link');
	check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
	await browser.close();
}

console.log('Desktop Chrome, home page finder');
{
	const { browser, page, errors } = await open(chromium, { viewport: { width: 1280, height: 900 } }, '');
	await page.fill('#state-find', 'tex');
	await page.press('#state-find', 'Tab');
	check((await events(page)).length === 0, 'a search that matches sends nothing');
	await page.fill('#state-find', 'Texsa!');
	await page.press('#state-find', 'Tab');
	await page.waitForTimeout(200);
	one(await events(page), 'state_search_miss', { search_term: 'texsa' }, 'no-match search, folded');
	await page.fill('#state-find', 'texsa');
	await page.press('#state-find', 'Enter');
	await page.press('#state-find', 'Tab');
	check((await events(page)).length === 0, 'the same miss is sent once per page');
	await page.fill('#state-find', 'x');
	await page.press('#state-find', 'Tab');
	check((await events(page)).length === 0, 'one letter sends nothing');
	check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
	await browser.close();
}

for (const [mode, method, name] of [['ok', 'share_sheet', 'sticker_save'], ['abort', 'cancelled', 'sticker_save_cancel'], [null, 'press_hold', 'sticker_save']]) {
	for (const url of ['texas/', 'es/texas/']) {
		console.log(`iPhone, share ${mode || 'missing'} (${url})`);
		const { browser, page, errors } = await open(webkit, devices['iPhone 15'], url, mode);
		await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
		if (!mode) { await page.evaluate(() => { Object.defineProperty(navigator, 'share', { value: undefined, configurable: true }); }); }
		await page.locator('#save').scrollIntoViewIfNeeded();
		await page.click('#save');
		await page.waitForTimeout(300);
		const ev = await events(page);
		check(ev.length === 1 && ev[0].name === name && ev[0].p.save_method === method && ev[0].p.sticker_source === 'maker' && ev[0].p.state === 'TX', `maker save on iPhone: ${JSON.stringify(ev)}`);
		check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
		await browser.close();
	}
}

console.log('Spanish post text');
{
	const { browser, page } = await open(chromium, { viewport: { width: 1280, height: 900 } }, 'es/texas/');
	check(/https:\/\/votesticker\.org\/es\/go\/texas\/plan\b/.test(await page.inputValue('#post-text')), 'Spanish post links /es/go/texas/plan');
	await browser.close();
}

console.log(failed ? `FAILED (${failed})` : 'PASS');
process.exit(failed ? 1 : 0);
