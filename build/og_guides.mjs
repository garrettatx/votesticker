// Link-preview images (1200 x 630) for the guides under /voting/, one per guide and language:
// site/assets/og/guides/<slug>.jpg and site/assets/og/guides/es/<slug>.jpg. index.php uses them when present
// and falls back to the hub card. A guide card is cream paper, the guide's title and promise, and a small
// chart drawn from the guide's state data (for runoffs, the vote share needed to win outright), so the
// preview shows real numbers from the page. Navy and gold only: red is never a data color.
// Serve site/ first:  php -S 127.0.0.1:8791 -t site      then:  node build/og_guides.mjs [base]
// Rerun after a guide's state data changes; index.php versions each image by mtime.
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const BASE = process.argv[2] || 'http://127.0.0.1:8791/';
const ROOT = path.join(path.dirname(new URL(import.meta.url).pathname), '..');
const SITE = path.join(ROOT, 'site');
const guides = fs.readdirSync(path.join(ROOT, 'build/guides')).filter((f) => f.endsWith('.json')).map((f) => JSON.parse(fs.readFileSync(path.join(ROOT, 'build/guides', f), 'utf8')));
const b = await chromium.launch(); const p = await b.newPage();
let n = 0;
for (const lang of ['en', 'es']) {
	for (const gd of guides) {
		await p.goto(BASE + (lang === 'es' ? 'es/' : '') + 'voting/' + gd.slug + '/', { waitUntil: 'networkidle' });
		await p.evaluate(() => document.fonts.ready);
		const url = await p.evaluate(async ({ gd, lang }) => {
			const es = lang === 'es', t = (o) => (o && (o[lang] || o.en)) || '';
			const NAVY = '#14295E', CREAM = '#FBF6E8', GOLD = '#F2C14E', INK = '#3A4562', MUTED = '#5B6475';
			const img = (src) => new Promise((ok, bad) => { const im = new Image(); im.onload = () => ok(im); im.onerror = bad; im.src = src; });
			const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
			const g = out.getContext('2d');
			g.fillStyle = CREAM; g.fillRect(0, 0, 1200, 630);
			// Signature, as on the state cards.
			const mark = await img((window.VOTE_ASSETS || '/assets/') + 'mark.svg');
			g.save(); g.translate(90, 78); g.rotate(-8 * Math.PI / 180); g.drawImage(mark, -26, -26, 52, 52); g.restore();
			g.textAlign = 'left'; g.textBaseline = 'middle'; g.font = '34px "Vote Display"';
			g.fillStyle = NAVY; g.fillText('VOTE ', 130, 80);
			g.fillStyle = '#C1272D'; g.fillText('STICKER', 130 + g.measureText('VOTE ').width, 80);
			// Kicker pill.
			const kick = es ? 'GUÍA' : 'GUIDE';
			g.font = '700 22px system-ui, sans-serif'; const kw = g.measureText(kick).width + 36;
			g.strokeStyle = NAVY; g.lineWidth = 2.5; g.beginPath(); g.roundRect(1136 - kw, 58, kw, 44, 22); g.stroke();
			g.fillStyle = NAVY; g.textAlign = 'center'; g.fillText(kick, 1136 - kw / 2, 81);
			// Title, fitted to one line, then the promise from the title tag (the part after the colon).
			g.textAlign = 'left'; g.textBaseline = 'alphabetic';
			const title = t(gd.meta.h1).toUpperCase();
			let px = 108; g.font = px + 'px "Vote Display"';
			const tw = g.measureText(title).width; if (tw > 1072) { px = Math.floor(px * 1072 / tw); g.font = px + 'px "Vote Display"'; }
			g.fillStyle = NAVY; g.fillText(title, 64, 220);
			const promise = (t(gd.meta.title).split('|')[0].split(':')[1] || '').trim();
			g.fillStyle = INK; g.font = '600 34px system-ui, sans-serif';
			g.fillText(promise.charAt(0).toUpperCase() + promise.slice(1), 64, 278);
			// Chart: one row per distinct threshold, label left, a 0 to 100% track, a dot at the value.
			const rows = [];
			for (const st of gd.states || []) {
				if (!st.threshold) continue;
				const k = st.threshold.op + st.threshold.value;
				let r = rows.find((x) => x.k === k);
				if (!r) { r = { k, v: st.threshold.value, words: t(st.threshold.words), states: [] }; rows.push(r); }
				r.states.push(st.abbr);
			}
			if (rows.length) {
				rows.sort((a, b2) => b2.v - a.v);
				const name = (a) => ((window.VOTE_META || {})[a] || {}).crumb || a;
				const X0 = 400, X1 = 1136, top = 396, gap = 54;
				g.fillStyle = MUTED; g.font = '600 24px system-ui, sans-serif';
				g.fillText(es ? 'Porcentaje de votos para ganar sin segunda vuelta' : 'Vote share needed to win without a runoff', 64, 352);
				rows.forEach((r, i) => {
					const y = top + i * gap, x = X0 + (X1 - X0) * r.v / 100;
					g.fillStyle = NAVY; g.font = '700 28px system-ui, sans-serif'; g.textBaseline = 'middle';
					g.fillText(r.states.length > 1 ? r.states.length + (es ? ' estados' : ' states') : name(r.states[0]), 64, y);
					g.strokeStyle = '#C9CDD6'; g.lineWidth = 4; g.beginPath(); g.moveTo(X0, y); g.lineTo(X1, y); g.stroke();
					g.strokeStyle = NAVY; g.lineWidth = 8; g.beginPath(); g.moveTo(X0, y); g.lineTo(x, y); g.stroke();
					g.beginPath(); g.arc(x, y, 15, 0, Math.PI * 2); g.fillStyle = GOLD; g.fill(); g.lineWidth = 4; g.strokeStyle = NAVY; g.stroke();
					g.font = '600 24px system-ui, sans-serif'; const ww = g.measureText(r.words).width;
					g.fillStyle = CREAM; g.fillRect(x + 20, y - 18, ww + 16, 36); g.fillStyle = INK; g.fillText(r.words, x + 28, y);
				});
				const ay = top + (rows.length - 1) * gap + 26;
				g.fillStyle = MUTED; g.font = '500 20px system-ui, sans-serif'; g.textBaseline = 'top';
				[[0, '0%'], [50, '50%'], [100, '100%']].forEach(([v, s]) => { const x = X0 + (X1 - X0) * v / 100; g.textAlign = v === 0 ? 'left' : v === 100 ? 'right' : 'center'; g.fillText(s, x, ay); });
			}
			g.textAlign = 'right'; g.textBaseline = 'alphabetic'; g.fillStyle = MUTED; g.font = '500 22px system-ui, sans-serif';
			g.fillText(es ? 'Con la ley de cada estado y la fecha en que la revisamos' : "Each state's law, with the date we checked it", 1136, 606);
			return out.toDataURL('image/jpeg', 0.88);
		}, { gd, lang });
		const dir = path.join(SITE, 'assets/og/guides', lang === 'es' ? 'es' : '');
		fs.mkdirSync(dir, { recursive: true });
		fs.writeFileSync(path.join(dir, gd.slug + '.jpg'), Buffer.from(url.split(',')[1], 'base64'));
		n++;
	}
}
console.log(`${n} guide cards written`);
await b.close();
