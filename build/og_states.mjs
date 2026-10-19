// One 1200 x 630 link-preview image per state page (site/assets/og/<slug>.jpg): the state's name, the
// one fact a voter needs (from the page's own title), and three of its designs in profile circles.
// Ready-made designs where the state has them, otherwise cards drawn by the tool. No address on it.
// node og_states.mjs [base]   (base default http://127.0.0.1:8791/; serve site/ with php -S 127.0.0.1:8791 -t site)
// Rerun after any states.js change or new ready-made set; index.php versions each image by mtime.
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const BASE = process.argv[2] || 'http://127.0.0.1:8791/';
const OUT = path.join(path.dirname(new URL(import.meta.url).pathname), '../site/assets/og');
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch(); const p = await b.newPage();
await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' }); await p.waitForTimeout(800);
const states = await p.evaluate(() => Object.entries(window.VOTE_META).map(([abbr, m]) => ({ abbr, ...m })));
for (const st of states) {
	const url = await p.evaluate(async (st) => {
		const V = window.VOTE_CARDS, row = window.VOTE_STATES.find((s) => s.abbr === st.abbr);
		const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
		const g = out.getContext('2d');
		g.fillStyle = '#F3EEE2'; g.fillRect(0, 0, 1200, 630);
		g.textAlign = 'center'; g.textBaseline = 'alphabetic'; g.fillStyle = '#14295E';
		const fit = (text, px, font, max) => { g.font = px + 'px ' + font; const w = g.measureText(text).width; if (w > max) { g.font = Math.floor(px * max / w) + 'px ' + font; } };
		fit(st.crumb.toUpperCase(), 96, '"Vote Display"', 1120); g.fillText(st.crumb.toUpperCase(), 600, 112);
		fit('VOTE PROFILE PICTURES', 64, '"Vote Display"', 1120); g.fillText('VOTE PROFILE PICTURES', 600, 186);
		const tag = st.title.split(': ')[1].toUpperCase().replace(/,/g, '');
		g.fillStyle = '#B3262C'; fit(tag + '  •  FITS THE PROFILE CIRCLE  •  FREE', 25, '"Vote Date"', 1120);
		g.fillText(tag + '  •  FITS THE PROFILE CIRCLE  •  FREE', 600, 240);
		const files = (window.VOTE_GALLERY[st.abbr] || []);
		let cards;
		if (files.length >= 3) {
			const pick = [0, Math.floor(files.length / 2), files.length - 1].map((i) => files[i]);
			cards = await Promise.all(pick.map((src) => new Promise((ok, bad) => { const im = new Image(); im.onload = () => ok(im); im.onerror = bad; im.src = src; })));
		} else {
			const h = V.defaultHeadline(row);
			cards = [['type', 'navy'], ['shape', 'cream'], ['badge', 'forest']].map(([d, pal]) => {
				const c = document.createElement('canvas');
				V.render(c, 500, { state: st.abbr, design: d, palette: pal, headline: h, dateText: V.defaultDateLine(row, h) });
				return c;
			});
		}
		const size = 300, gap = 40, x0 = (1200 - (size * 3 + gap * 2)) / 2, y = 290;
		cards.forEach((im, i) => {
			const x = x0 + i * (size + gap);
			g.save(); g.beginPath(); g.arc(x + size / 2, y + size / 2, size / 2, 0, Math.PI * 2); g.clip();
			g.drawImage(im, x, y, size, size); g.restore();
		});
		return out.toDataURL('image/jpeg', 0.86);
	}, st);
	fs.writeFileSync(path.join(OUT, st.slug + '.jpg'), Buffer.from(url.split(',')[1], 'base64'));
}
console.log(`${states.length} images in ${OUT}`);
await b.close();
