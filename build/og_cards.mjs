// Link-preview images (1200 x 630), Direction A: one per state at site/assets/og/<slug>.jpg and the hub's
// at site/assets/og-hub.jpg. A state card is the state's sticker on cream paper, the state name, the red
// ribbon from its page (dates, mail deadline, or Election Day), and the Vote Sticker signature. No address.
// Serve site/ first:  php -S 127.0.0.1:8791 -t site      then:  node build/og_cards.mjs [base]
// Rerun after a states.js change or a new ready-made set; index.php versions each image by mtime.
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const BASE = process.argv[2] || 'http://127.0.0.1:8791/';
const SITE = path.join(path.dirname(new URL(import.meta.url).pathname), '../site');
fs.mkdirSync(path.join(SITE, 'assets/og'), { recursive: true });
const b = await chromium.launch(); const p = await b.newPage();
await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' }); await p.waitForTimeout(800);
await p.evaluate(() => document.fonts.ready);

const setup = () => {
	window.og = {};
	og.img = (src) => new Promise((ok, bad) => { const im = new Image(); im.onload = () => ok(im); im.onerror = bad; im.src = src; });
	og.fit = (g, text, px, font, max) => { g.font = px + 'px ' + font; const w = g.measureText(text).width; if (w > max) { g.font = Math.floor(px * max / w) + 'px ' + font; } };
	og.sticker = (g, im, cx, cy, r, deg) => {
		g.save(); g.translate(cx, cy); g.rotate(deg * Math.PI / 180);
		g.shadowColor = 'rgba(20,41,94,0.32)'; g.shadowBlur = 30; g.shadowOffsetY = 12;
		g.beginPath(); g.arc(0, 0, r, 0, Math.PI * 2); g.fillStyle = '#fff'; g.fill();
		g.shadowColor = 'transparent'; g.clip(); g.drawImage(im, -r, -r, r * 2, r * 2); g.restore();
	};
	og.sign = async (g, x, y, light) => {
		const mark = await og.img((window.VOTE_ASSETS || '/assets/') + 'mark.svg');
		g.save(); g.translate(x + 26, y); g.rotate(-8 * Math.PI / 180); g.drawImage(mark, -26, -26, 52, 52); g.restore();
		g.textAlign = 'left'; g.textBaseline = 'middle';
		g.font = '36px "Vote Display"'; g.fillStyle = light ? '#FBF6E8' : '#14295E'; g.fillText('VOTE ', x + 66, y + 2);
		const w = g.measureText('VOTE ').width; g.fillStyle = light ? '#F2C14E' : '#C1272D'; g.fillText('STICKER', x + 66 + w, y + 2);
	};
};
await p.evaluate(setup);

// State cards.
const states = await p.evaluate(() => Object.entries(window.VOTE_META).map(([abbr, m]) => ({ abbr, ...m })));
for (const st of states) {
	const url = await p.evaluate(async (st) => {
		const V = window.VOTE_CARDS, row = window.VOTE_STATES.find((s) => s.abbr === st.abbr);
		const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
		const g = out.getContext('2d');
		g.fillStyle = '#FBF6E8'; g.fillRect(0, 0, 1200, 630);
		const files = window.VOTE_GALLERY[st.abbr] || [];
		let im;
		if (files.length) { im = await og.img(files[0]); }
		else {
			im = document.createElement('canvas');
			const h = V.stateHeadline(row);
			V.render(im, 600, { state: st.abbr, design: 'sticker', palette: 'state', headline: h, dateText: V.defaultDateLine(row, h) });
		}
		og.sticker(g, im, 300, 315, 225, -6);
		g.textAlign = 'left'; g.textBaseline = 'alphabetic'; g.fillStyle = '#14295E';
		const name = st.crumb.toUpperCase();
		og.fit(g, name, 112, '"Vote Display"', 600); g.fillText(name, 580, 270);
		g.font = '44px "Vote Display"';
		let rw = g.measureText(st.ribbon).width;
		if (rw > 560) { g.font = Math.floor(44 * 560 / rw) + 'px "Vote Display"'; rw = 560; }
		g.fillStyle = '#C1272D'; g.beginPath(); g.roundRect(580, 300, rw + 36, 68, 10); g.fill();
		g.fillStyle = '#fff'; g.textBaseline = 'middle'; g.fillText(st.ribbon, 598, 336);
		await og.sign(g, 580, 460, false);
		g.textAlign = 'left'; g.fillStyle = '#3A4562'; g.font = '600 24px system-ui, sans-serif';
		g.fillText('Make your plan. Share the sticker.', 580, 512);
		return out.toDataURL('image/jpeg', 0.86);
	}, st);
	fs.writeFileSync(path.join(SITE, 'assets/og', st.slug + '.jpg'), Buffer.from(url.split(',')[1], 'base64'));
}

// Hub card.
const hub = await p.evaluate(async () => {
	const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
	const g = out.getContext('2d');
	g.fillStyle = '#14295E'; g.fillRect(0, 0, 1200, 630);
	await og.sign(g, 64, 92, true);
	g.textAlign = 'left'; g.textBaseline = 'alphabetic'; g.fillStyle = '#FBF6E8'; og.fit(g, 'MAKE YOUR PLAN.', 92, '"Vote Display"', 560);
	['MAKE YOUR PLAN.', 'SHARE THE', 'STICKER.'].forEach((t, i) => g.fillText(t, 64, 250 + i * 98));
	g.fillStyle = '#F2C14E'; g.font = '700 28px system-ui, sans-serif';
	g.fillText('Voting stickers for all 50 states and DC', 64, 560);
	const G = window.VOTE_GALLERY, pick = (a, n) => (G[a] || []).find((f) => f.indexOf(a.toLowerCase() + '-vote-' + n + '.jpg') > 0) || (G[a] || [])[0];
	const ims = await Promise.all([pick('TX', 2), pick('WA', 6), pick('PA', 4), pick('CA', 4)].map(og.img));
	[[835, 190, 135, -9], [1060, 170, 120, 7], [855, 450, 128, 5], [1075, 430, 115, -6]].forEach(([x, y, r, d], i) => og.sticker(g, ims[i], x, y, r, d));
	return out.toDataURL('image/jpeg', 0.86);
});
fs.writeFileSync(path.join(SITE, 'assets/og-hub.jpg'), Buffer.from(hub.split(',')[1], 'base64'));
console.log(`${states.length} state cards and the hub card written`);
await b.close();
