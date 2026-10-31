// QA for the history page's timeline overview (/history/, /es/history/), at phone to desktop widths.
// Fails on: sideways scroll of the page or the overview; overview links (or era headings, gap labels) whose boxes
// overlap; track dots that overlap; a link under 44px tall below 960px (touch layouts); an overview link with no
// entry to land on; an entry with no overview link.
//   node scripts/qa_history.mjs [--base http://127.0.0.1:8792/] [--plant overlap|overflow|dots]
// --plant injects a known defect, to prove the check fires (it must then FAIL).
import { chromium } from 'playwright';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/');
const PLANT = arg('--plant', '');
const PLANTS = {
	overlap: '.tl-era__list li + li { margin-top: -26px !important; }',
	overflow: '.tl-eras { min-width: 1500px; }',
	dots: '.tl-dot { left: 50% !important; }',
};
const WIDTHS = [320, 375, 414, 768, 1024, 1280, 1440];
let fails = 0;
const fail = (m) => { console.log('  FAIL ' + m); fails++; };
const b = await chromium.launch();
for (const path of ['history/', 'es/history/']) {
	for (const w of WIDTHS) {
		const p = await b.newPage({ viewport: { width: w, height: 900 } });
		await p.goto(BASE + path, { waitUntil: 'networkidle' });
		if (PLANT) { await p.addStyleTag({ content: PLANTS[PLANT] }); }
		const r = await p.evaluate(() => {
			const box = (el) => { const r = el.getBoundingClientRect(); return { l: r.left, t: r.top, r: r.right, b: r.bottom, h: r.height, txt: el.textContent.trim().slice(0, 30) }; };
			const hit = (a, c, tol) => a.l < c.r - tol && c.l < a.r - tol && a.t < c.b - tol && c.t < a.b - tol;
			const out = { pageOver: document.documentElement.scrollWidth - innerWidth, wrapOver: 0, overlaps: [], dotHits: [], small: [], noTarget: [], noLink: [] };
			const wrap = document.querySelector('.tl-wrap');
			if (!wrap) { out.missing = true; return out; }
			out.wrapOver = Math.max(wrap.scrollWidth - wrap.clientWidth, ...[...wrap.querySelectorAll('.tl-eras, .tl-era')].map((e) => e.scrollWidth - e.clientWidth));
			const labels = [...wrap.querySelectorAll('.tl-i, .tl-era__h, .tl-gap, .tl-era__track')].filter((e) => e.offsetParent).map(box);
			for (let i = 0; i < labels.length; i++) for (let j = i + 1; j < labels.length; j++) if (hit(labels[i], labels[j], 0.5)) out.overlaps.push(labels[i].txt + ' / ' + labels[j].txt);
			for (const tr of wrap.querySelectorAll('.tl-era__track')) {
				const d = [...tr.querySelectorAll('.tl-dot svg')].map(box);
				for (let i = 1; i < d.length; i++) if (d[i].l < d[i - 1].r - 0.5) out.dotHits.push(Math.round(d[i - 1].l) + ',' + Math.round(d[i].l));
			}
			const links = [...wrap.querySelectorAll('a.tl-i')];
			if (innerWidth < 960) for (const a of links) if (a.getBoundingClientRect().height < 44) out.small.push(a.textContent.trim().slice(0, 20));
			for (const a of links) if (!document.getElementById(a.hash.slice(1))) out.noTarget.push(a.hash);
			const hrefs = new Set(links.map((a) => a.hash.slice(1)));
			for (const li of document.querySelectorAll('.tl__item')) if (!hrefs.has(li.id)) out.noLink.push(li.id);
			out.links = links.length;
			return out;
		});
		const tag = `/${path} @${w}`;
		if (r.missing) { fail(`${tag}: no timeline overview`); await p.close(); continue; }
		if (r.pageOver > 0) fail(`${tag}: page scrolls sideways ${r.pageOver}px`);
		if (r.wrapOver > 0) fail(`${tag}: overview overflows its panel by ${r.wrapOver}px`);
		if (r.overlaps.length) fail(`${tag}: ${r.overlaps.length} overlapping label boxes, e.g. ${r.overlaps[0]}`);
		if (r.dotHits.length) fail(`${tag}: ${r.dotHits.length} overlapping track dots`);
		if (r.small.length) fail(`${tag}: ${r.small.length} links under 44px tall, e.g. ${r.small[0]}`);
		if (r.noTarget.length) fail(`${tag}: links with no entry: ${r.noTarget.join(' ')}`);
		if (r.noLink.length) fail(`${tag}: entries with no overview link: ${r.noLink.join(' ')}`);
		await p.close();
	}
	console.log(`${path}: checked ${WIDTHS.length} widths`);
}
await b.close();
console.log(fails ? `qa_history: ${fails} FAIL` : 'qa_history: OK');
process.exit(fails ? 1 : 0);
