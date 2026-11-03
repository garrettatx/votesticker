// Whole-site sweep (November 2, Garrett: "sweep our pages to find any remaining errors or issues with layout or
// wording"). Every page in the sitemap, English and Spanish, at 390 and 1280 px. Fails on: sideways scroll; a
// visible element running past the viewport; an image that failed to load; a script error; a link with no
// name; a skipped heading level; leftover template text (undefined, NaN, %s, {{, "Array"); a doubled word;
// a space before punctuation; an em dash; "tomorrow" or "tonight" (pages are built ahead and read later).
//   node scripts/qa_sweep.mjs [--base http://127.0.0.1:8792/] [--only texas/,es/] [--plant text|overflow]
import { chromium } from 'playwright';
import { readFileSync } from 'node:fs';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/');
const PLANT = arg('--plant', '');
const only = arg('--only', '');
const sitemap = readFileSync(new URL('../dist/sitemap.xml', import.meta.url), 'utf8');
let paths = [...sitemap.matchAll(/<loc>https:\/\/votesticker\.org\/([^<]*)<\/loc>/g)].map((m) => m[1]);
if (only) { paths = only.split(','); }
let fails = 0;
const seen = new Map();
const fail = (key, where) => { if (!seen.has(key)) { seen.set(key, []); } seen.get(key).push(where); fails++; };
const b = await chromium.launch();
for (const w of [390, 1280]) {
	const ctx = await b.newContext({ viewport: { width: w, height: 900 }, reducedMotion: 'reduce' });
	for (const path of paths) {
		const p = await ctx.newPage();
		const errs = [];
		p.on('pageerror', (e) => errs.push(String(e).slice(0, 120)));
		const res = await p.goto(BASE + path, { waitUntil: 'networkidle' }).catch((e) => null);
		const where = `${path || '/'} @${w}`;
		if (!res || res.status() !== 200) { fail(`HTTP ${res && res.status()}`, where); await p.close(); continue; }
		if (PLANT === 'text') { await p.evaluate(() => { const x = document.querySelector('main p'); if (x) { x.textContent += ' the the undefined ,'; } }); }
		if (PLANT === 'overflow') { await p.addStyleTag({ content: 'main p { min-width: 1600px; }' }); }
		// Lazy images: bring the eager-loaded ones in; lazy ones are checked only if the browser fetched them.
		const r = await p.evaluate(() => {
			const out = [];
			const W = innerWidth;
			if (document.documentElement.scrollWidth - W > 0) { out.push('sideways scroll ' + (document.documentElement.scrollWidth - W) + 'px'); }
			document.querySelectorAll('main *, header *, footer *').forEach((el) => {
				const cs = getComputedStyle(el);
				if (cs.display === 'none' || cs.visibility === 'hidden' || cs.position === 'fixed') { return; }
				if (el.closest('[hidden], details:not([open]) > :not(summary), .grid, .scroller, svg, [aria-hidden="true"]')) { return; }
				const r = el.getBoundingClientRect();
				if (r.width && r.right > W + 1 && !(el.parentElement && getComputedStyle(el.parentElement).overflowX.match(/auto|scroll|hidden/))) { out.push('past the right edge: <' + el.tagName.toLowerCase() + (el.className && typeof el.className === 'string' ? '.' + el.className.split(' ')[0] : '') + '> ' + Math.round(r.right - W) + 'px'); }
			});
			document.querySelectorAll('img').forEach((i) => { if (!i.closest('[hidden]') && i.getAttribute('src') !== 'data:,' && i.complete && i.currentSrc && !i.naturalWidth && i.loading !== 'lazy') { out.push('image failed: ' + i.getAttribute('src').slice(0, 60)); } });
			const folded = (el) => { const d = el.closest('details:not([open])'); return d && !el.closest('summary'); };
			// /cdn-cgi/ links are Cloudflare's hidden bot-trap links, injected for automated browsers on the live site.
			document.querySelectorAll('a[href]:not([href*="/cdn-cgi/"])').forEach((a) => { if (!a.closest('[hidden]') && !folded(a) && !(a.innerText || '').trim() && !a.getAttribute('aria-label') && !a.querySelector('img[alt]:not([alt=""]), svg[aria-label]')) { out.push('link with no name: ' + a.getAttribute('href').slice(0, 50)); } });
			let last = 0;
			document.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach((h) => {
				if (h.closest('[hidden]')) { return; }
				const n = +h.tagName[1];
				if (last && n > last + 1) { out.push('heading skips h' + last + ' to h' + n + ': ' + h.innerText.slice(0, 40)); }
				last = n;
			});
			const text = (document.querySelector('main') || document.body).innerText + '\n' + document.title + '\n' + (document.querySelector('meta[name=description]') || {}).content;
			const rules = [
				[/\bundefined\b|\bNaN\b|%s|\{\{|\bArray\b|\bnull\b/, 'template text'],
				[/\b(\w{3,}) +\1\b/i, 'doubled word'],  // same line only: a heading ending in a word the next line starts with is fine
				[/[^\s] +[,;:!?](?!\d)|[a-z] +\.(?![\d.])/, 'space before punctuation'],
				[/—/, 'em dash'],
				[/\b(tomorrow|tonight|esta noche)\b|(?<!(en|de|por) la )\bmañana\b/i, 'tomorrow/tonight'],  // "en la mañana" is "in the morning"
			];
			rules.forEach(([re, name]) => { const m = text.match(re); if (m) { const i = text.indexOf(m[0]); out.push(name + ': "' + text.slice(Math.max(0, i - 40), i + m[0].length + 30).replace(/\s+/g, ' ') + '"'); } });
			return out;
		});
		r.forEach((m) => fail(m, where));
		errs.forEach((e) => fail('script error: ' + e, where));
		await p.close();
	}
	await ctx.close();
}
await b.close();
for (const [k, v] of seen) { console.log(`  FAIL ${k}  (${v.length}: ${v.slice(0, 4).join(', ')}${v.length > 4 ? ', ...' : ''})`); }
console.log(fails ? `FAILED (${seen.size} distinct, ${fails} total)` : `PASS (${paths.length} pages x 2 widths${PLANT ? ', plant ' + PLANT : ''})`);
process.exit(fails ? 1 : 0);
