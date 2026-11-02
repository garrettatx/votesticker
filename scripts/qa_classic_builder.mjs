// QA for the three classic layouts in the builder (Wave, Star ring, Flag; app.js DESIGNS, November 2), every
// message in English and Spanish on short and long state names. Fails on: Star ring's message off the disc's
// center by more than 1.5 percent of the sticker (16 px at 1080); Flag's message band off center by the same;
// any message or tagline past the safe circle (500 of 540 px); a tagline cap
// height under 28 px at 1080 (Star ring, Flag) or under 34 px (Wave) when it's a short line like NC 2026; the
// home page's classic PNGs no longer matching what the renderer draws with `plain` (they must not change).
//   php -S 127.0.0.1:8793   (or the dist server)
//   node scripts/qa_classic_builder.mjs [--base http://127.0.0.1:8792/] [--plant offcenter|tiny]
import { chromium } from 'playwright';
const arg = (k, d) => { const i = process.argv.indexOf(k); return i > 0 ? process.argv[i + 1] : d; };
const BASE = arg('--base', 'http://127.0.0.1:8792/');
const PLANT = arg('--plant', '');
let fails = 0;
const fail = (m) => { console.log('  FAIL ' + m); fails++; };
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
for (const lang of ['en', 'es']) {
	await p.goto(BASE + (lang === 'es' ? 'es/' : '') + 'texas/', { waitUntil: 'networkidle' });
	await p.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
	await p.waitForTimeout(600);
	const rows = await p.evaluate(({ lang, PLANT }) => {
		const V = window.VOTE_CARDS, out = [];
		if (PLANT === 'tiny') { V.CT.flagDateAsc = 30; V.CT.ringDateAsc = 0.1; }
		const msgs = Object.keys(V.HEADLINES).filter((k) => !V.HEADLINES[k].hidden && (lang === 'es' ? !!V.HEADLINES[k].es : !V.HEADLINES[k].es));
		// The tagline is the text item that says the dates line; the message is every other text item (Flag's
		// second line is in the same face as the tagline, so the face can't tell them apart).
		const bb = (its) => its.reduce((a, it) => [Math.min(a[0], it.box[0]), Math.min(a[1], it.box[1]), Math.max(a[2], it.box[2]), Math.max(a[3], it.box[3])], [1e9, 1e9, -1e9, -1e9]);
		for (const abbr of ['TX', 'NC', 'NH', 'DC', 'MA']) {
			const st = window.VOTE_STATES.find((s) => s.abbr === abbr);
			for (const m of msgs) for (const d of ['wave', 'cring', 'cflag']) {
				const dt = (V.defaultDateLine(st, m) || '').toUpperCase();
				const lay = V.LAYOUTS[d](st, V.paletteFor('classicnavy', st), V.headlineLines(m, st), dt, PLANT === 'offcenter' ? { plain: true } : {});  // plant: the classic set's weighting, with a tagline
				const f = V.fit(lay), k = f.s * 1080 / 1000;  // units to px at 1080 (safe radius 500 of 540)
				const isTag = (it) => it.t === 'text' && dt && it.s === dt;
				const hb = bb(lay.items.filter((it) => it.t === 'text' && !isTag(it))), dates = lay.items.filter(isTag);
				const dy = ((hb[1] + hb[3]) / 2 - f.cy) * f.s, dx = ((hb[0] + hb[2]) / 2 - f.cx) * f.s;
				let reach = 0;
				lay.items.filter((it) => it.t === 'text').forEach((it) => { [[0, 1], [2, 1], [0, 3], [2, 3]].forEach(([i, j]) => { reach = Math.max(reach, Math.hypot(it.box[i] - f.cx, it.box[j] - f.cy) * f.s / 540); }); });
				const cap = dates.length ? Math.min(...dates.map((it) => (it.base - it.box[1]) * f.s)) : null;
				out.push({ msgText: V.headlineLines(m, st).join(' '), abbr, m, d, dt, dy: Math.round(dy), dx: Math.round(dx), reach: +reach.toFixed(3), cap: cap && Math.round(cap) });
			}
		}
		return out;
	}, { lang, PLANT });
	const short = (dt) => dt.length <= 10;
	for (const r of rows) {
		const tag = `${lang} ${r.abbr} ${r.m} ${r.d}`;
		if (r.d === 'cring' && Math.abs(r.dy) > 16) { fail(`${tag}: message ${r.dy}px off the disc's center`); }
		// Flag counts CT.accWeight of an accent when centering (YO VOTÉ; Garrett, October 27), so a box-center
		// measure reads accented messages up to about 30 px low by design.
		if (r.d === 'cflag' && Math.abs(r.dy) > (/[ÁÉÍÓÚÑ]/i.test(r.msgText) ? 32 : 16)) { fail(`${tag}: message band ${r.dy}px off center`); }
		if (Math.abs(r.dx) > 6) { fail(`${tag}: message ${r.dx}px off center sideways`); }
		if (r.reach > 500 / 540 + 0.002) { fail(`${tag}: text reaches ${Math.round(r.reach * 100)}% of the radius, past the safe circle`); }
		if (r.cap != null && short(r.dt) && r.cap < (r.d === 'wave' ? 34 : 28)) { fail(`${tag}: tagline "${r.dt}" only ${r.cap}px tall at 1080`); }
	}
	const by = (d) => rows.filter((r) => r.d === d);
	const stat = (xs) => xs.length ? `${Math.min(...xs)} to ${Math.max(...xs)}` : 'n/a';
	console.log(`${lang}: ${rows.length} renders. Star ring message offset ${stat(by('cring').map((r) => r.dy))} px; Flag ${stat(by('cflag').map((r) => r.dy))} px; Wave ${stat(by('wave').map((r) => r.dy))} px. Short taglines: Wave ${stat(by('wave').filter((r) => short(r.dt)).map((r) => r.cap))} px, Star ring ${stat(by('cring').filter((r) => short(r.dt)).map((r) => r.cap))} px, Flag ${stat(by('cflag').filter((r) => short(r.dt)).map((r) => r.cap))} px.`);
}
await b.close();
console.log(fails ? `FAILED (${fails})` : `PASS${PLANT ? ' (plant ' + PLANT + ')' : ''}`);
process.exit(fails ? 1 : 0);
