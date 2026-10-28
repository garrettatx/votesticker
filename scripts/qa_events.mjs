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
const ALLOWED = ['sticker_save', 'sticker_save_cancel', 'sticker_hold_open', 'sticker_builder_start', 'post_text_copy', 'share', 'state_search_miss', 'save_hint_open'];
let failed = 0;
const fail = (m) => { failed++; console.log('  FAIL ' + m); };
const ok = (m) => console.log('  ok   ' + m);
const check = (cond, m) => (cond ? ok : fail)(m);

const record = (mode) => {
	window.__ev = [];
	window.gtag = (...a) => { if (a[0] === 'event') { window.__ev.push({ name: a[1], p: a[2] || {} }); } };
	if (mode) {
		Object.defineProperty(navigator, 'canShare', { configurable: true, value: () => true });
		// window.__share switches the outcome mid-page: ok, abort, error (NotAllowedError) or pending (never settles).
		window.__share = mode;
		Object.defineProperty(navigator, 'share', { configurable: true, value: () => {
			const m = window.__share;
			if (m === 'abort') { return Promise.reject(Object.assign(new Error('closed'), { name: 'AbortError' })); }
			if (m === 'error') { return Promise.reject(Object.assign(new Error('denied'), { name: 'NotAllowedError' })); }
			if (m === 'pending') { return new Promise(() => {}); }
			return Promise.resolve();
		} });
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
	// The first deliberate maker change sends sticker_builder_start once; later picks send nothing.
	await page.locator('#grid .tile').nth(2).click();
	await page.waitForTimeout(200);
	one(await events(page), 'sticker_builder_start', { state: 'TX', first_action: 'layout' }, 'first layout pick starts the builder');
	await page.locator('#swatches .swatch').nth(3).click();
	await page.locator('#grid .tile').nth(4).click();
	await page.selectOption('#headline', { index: 1 });
	await page.waitForTimeout(200);
	check((await events(page)).length === 0, 'later design, color and message picks send nothing');

	const tile = page.locator('#gallery-grid a.tile').first();
	await tile.scrollIntoViewIfNeeded();
	await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), tile.click()]);
	one(await events(page), 'sticker_save', { state: 'TX', sticker_source: 'ready_made', design: 'ready_1', save_method: 'download', attempt: 'first', prev_result: 'none' }, 'ready-made save');
	// A double click on the same tile starts the download twice: one attempt.
	await tile.dblclick();
	await page.waitForTimeout(300);
	check((await events(page)).length === 0, 'a double click within 1.5 s is not a second attempt');
	await page.waitForTimeout(1300);
	// A bespoke sticker (October 26): its own source value and the design from its file name.
	await page.click('#gallery-grid a.tile[href*="/texas-i-voted-masthead-sticker"]');
	one(await events(page), 'sticker_save', { state: 'TX', sticker_source: 'bespoke', design: 'masthead', save_method: 'download', attempt: 'second', prev_result: 'download' }, 'bespoke save');

	const want = await page.evaluate(() => ({ design: document.querySelector('#grid .tile[aria-pressed="true"]').dataset.design, message: document.getElementById('headline').value }));
	await page.locator('#save').scrollIntoViewIfNeeded();
	await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), page.click('#save')]);
	const ms = await events(page);
	one(ms, 'sticker_save', { state: 'TX', sticker_source: 'maker', design: want.design, palette: ms[0] && ms[0].p.palette, message: want.message, save_method: 'download', attempt: 'third_plus', prev_result: 'download' }, 'maker save');
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

for (const [mode, method, name] of [['ok', 'share_sheet', 'sticker_save'], ['abort', 'cancelled', 'sticker_save_cancel'], ['error', 'share_error', 'sticker_hold_open'], [null, 'press_hold', 'sticker_hold_open']]) {
	for (const url of ['texas/', 'es/texas/']) {
		console.log(`iPhone, share ${mode || 'missing'} (${url})`);
		const { browser, page, errors } = await open(webkit, devices['iPhone 15'], url, mode);
		await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
		if (!mode) { await page.evaluate(() => { Object.defineProperty(navigator, 'share', { value: undefined, configurable: true }); }); }
		await page.locator('#save').scrollIntoViewIfNeeded();
		await page.click('#save');
		await page.waitForTimeout(300);
		const ev = await events(page);
		check(ev.length === 1 && ev[0].name === name && ev[0].p.save_method === method && ev[0].p.sticker_source === 'maker' && ev[0].p.state === 'TX' && ev[0].p.attempt === 'first' && ev[0].p.prev_result === 'none', `maker save on iPhone: ${JSON.stringify(ev)}`);
		const held = await page.evaluate(() => { const b = document.getElementById('hold-save'); return !!(b && !b.hidden); });
		check(held === (name === 'sticker_hold_open'), `press and hold shown only on error or no sharing (${held})`);
		check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
		await browser.close();
	}
}

console.log('iPhone, retry and double tap (texas/)');
{
	const { browser, page, errors } = await open(webkit, devices['iPhone 15'], 'texas/', 'abort');
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	await page.locator('#save').scrollIntoViewIfNeeded();
	await page.click('#save');
	await page.waitForTimeout(300);
	await page.evaluate(() => { window.__share = 'ok'; });
	await page.click('#save');
	await page.waitForTimeout(300);
	const ev = await events(page);
	check(ev.length === 2 && ev[0].name === 'sticker_save_cancel' && ev[1].name === 'sticker_save' && ev[1].p.attempt === 'second' && ev[1].p.prev_result === 'aborted', `cancel then resolve: ${JSON.stringify(ev)}`);
	check(!(await page.evaluate(() => { const b = document.getElementById('hold-save'); return !!(b && !b.hidden); })), 'a cancel never forces press and hold');
	await page.evaluate(() => { window.__share = 'pending'; });
	await page.click('#save');
	await page.click('#save', { force: true });
	await page.waitForTimeout(300);
	check((await events(page)).length === 0 && (await page.evaluate(() => window.VOTE_LAST_SAVE)) === 'busy', 'a second tap while the sheet is open sends nothing');
	check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
	await browser.close();
}

// October 28: after a closed share sheet, one line under what was tapped (the maker's Save, or the gallery),
// in the page's language; its button opens press and hold and sends save_hint_open; the next try hides it.
for (const [url, word] of [['texas/', "Didn't save?"], ['es/texas/', '¿No se guardó?']]) {
	console.log(`iPhone, hint after a closed share sheet (${url})`);
	const { browser, page, errors } = await open(webkit, devices['iPhone 15'], url, 'abort');
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	await page.locator('#save').scrollIntoViewIfNeeded();
	await page.click('#save');
	await page.waitForTimeout(300);
	await events(page);
	const h = await page.evaluate(() => { const p = document.getElementById('save-cancel'); return p && { shown: !p.hidden && p.offsetHeight > 0, text: p.textContent, after: p.previousElementSibling && p.previousElementSibling.id }; });
	check(h && h.shown && h.text.startsWith(word) && h.after === 'tip', `maker: hint shown after #tip in the page language ${JSON.stringify(h)}`);
	await page.click('#save-cancel button');
	await page.waitForTimeout(200);
	check(await page.evaluate(() => { const b = document.getElementById('hold-save'); return !!(b && !b.hidden); }), 'hint button opens press and hold');
	one(await events(page), 'save_hint_open', {}, 'hint button event');
	await page.click('.hold-save__close');
	const tile = page.locator('#gallery-grid a.tile').first();
	await tile.scrollIntoViewIfNeeded();
	await page.waitForTimeout(500);
	await tile.click();
	await page.waitForTimeout(300);
	const g = await page.evaluate(() => { const p = document.getElementById('save-cancel'); return p && { shown: !p.hidden, after: p.previousElementSibling && p.previousElementSibling.id }; });
	check(g && g.shown && g.after === 'gallery-grid', `gallery: hint moves under the gallery ${JSON.stringify(g)}`);
	await page.evaluate(() => { window.__share = 'ok'; });
	await tile.click();
	await page.waitForTimeout(300);
	check(await page.evaluate(() => document.getElementById('save-cancel').hidden), 'the next try hides the hint');
	check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
	await browser.close();
}

console.log('Desktop Chrome, analytics blocked');
{
	const { browser, page, errors } = await open(chromium, { viewport: { width: 1280, height: 900 } }, 'texas/');
	await page.evaluate(() => { window.gtag = undefined; });
	await page.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	await page.locator('#grid .tile').nth(1).click();
	await page.locator('#save').scrollIntoViewIfNeeded();
	const dl = await Promise.all([page.waitForEvent('download', { timeout: 5000 }).catch(() => null), page.click('#save')]);
	check(!!dl[0], 'maker download still works without gtag');
	check(errors.length === 0, `no page errors ${errors.join(' | ')}`);
	await browser.close();
}

console.log('Spanish post text');
{
	const { browser, page } = await open(chromium, { viewport: { width: 1280, height: 900 } }, 'es/texas/');
	check(/https:\/\/votesticker\.org\/es\/go\/texas\/plan\b/.test(await page.inputValue('#post-text')), 'Spanish post links /es/go/texas/plan');
	await browser.close();
}

console.log(failed ? `FAILED (${failed})` : 'PASS');
process.exit(failed ? 1 : 0);
