// Link-preview images (1200 x 630), Direction A: one per state at site/assets/og/<slug>.jpg and the hub's
// at site/assets/og-hub.jpg. A state card is the state's sticker on cream paper, the state name, the red
// ribbon from its page (dates, mail deadline, or Election Day), and the Vote Sticker signature. No address.
// Serve site/ first:  php -S 127.0.0.1:8791 -t site      then:  node build/og_cards.mjs [base]
// Rerun after a states.js change or a new ready-made set; index.php versions each image by mtime.
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const BASE = process.argv[2] || 'http://127.0.0.1:8791/';
// Spanish cards (October 19): node build/og_cards.mjs <base> es  writes assets/og/es/<slug>.jpg and og-hub-es.jpg.
const LANG = process.argv[3] === 'es' ? 'es' : 'en';
const ES = LANG === 'es';
const SITE = path.join(path.dirname(new URL(import.meta.url).pathname), '../site');
// OG_SET=ended or OG_SET=post writes the phase's set (index.php picks it): serve a build made with
// VOTE_TODAY=2026-11-03 for 'ended' and VOTE_TODAY=2026-11-04 for 'post'. The hub card is redrawn only
// for 'post' (og-hub-post.jpg); 'ended' cards cover only states whose early voting has closed.
const SET = ['ended', 'post'].includes(process.env.OG_SET) ? process.env.OG_SET : '';
const OGDIR = path.join(SITE, ES ? 'assets/og/es' : 'assets/og', SET);
fs.mkdirSync(OGDIR, { recursive: true });
const b = await chromium.launch(); const p = await b.newPage();
await p.goto(BASE + (ES ? 'es/' : '') + 'texas/', { waitUntil: 'networkidle' }); await p.waitForTimeout(800);
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
const states = (await p.evaluate(() => Object.entries(window.VOTE_META).map(([abbr, m]) => ({ abbr, ...m })))).filter((st) => SET !== 'ended' || st.family === 'early_over');
for (const st of states) {
	const url = await p.evaluate(async (st) => {
		const V = window.VOTE_CARDS, row = window.VOTE_STATES.find((s) => s.abbr === st.abbr);
		const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
		const g = out.getContext('2d');
		g.fillStyle = '#FBF6E8'; g.fillRect(0, 0, 1200, 630);
		// Spanish cards always show a Spanish sticker from the maker; the ready-made sets are in English.
		const es = window.VOTE_LANG === 'es';
		const files = es ? [] : window.VOTE_GALLERY[st.abbr] || [];
		// The state's hero sticker, big, with two more from its set peeking out behind it (October 21), so
		// the preview shows the set, not one design. Spanish cards draw three maker designs instead.
		let im, extras;
		const draw = (design, palette) => {
			const c = document.createElement('canvas'), h = es ? V.defaultHeadline(row) : V.stateHeadline(row);
			V.render(c, 600, { state: st.abbr, design, palette, headline: h, dateText: V.defaultDateLine(row, h) });
			return c;
		};
		if (files.length) {
			const hero = (window.VOTE_HEROES || {})[st.abbr] || files[0];
			im = await og.img(hero);
			extras = await Promise.all(files.filter((f) => f !== hero).slice(0, 2).map(og.img));
		} else {
			im = draw('tilt', 'plum');
			extras = [draw('brush', 'rust'), draw('arch', 'maroon')];
		}
		if (extras[0]) { og.sticker(g, extras[0], 118, 478, 88, -12); }
		if (extras[1]) { og.sticker(g, extras[1], 478, 150, 82, 9); }
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
		const post = window.VOTE_PHASE === 'post';
		g.fillText(window.VOTE_LANG === 'es' ? (post ? 'Muéstrales a tus amigos que ya votaste.' : 'Muéstrales a tus amigos que vas a votar.') : (post ? 'Show your friends you voted.' : "Show your friends you're voting."), 580, 512);
		g.fillStyle = '#6B7280'; g.font = '500 22px system-ui, sans-serif';
		g.fillText(window.VOTE_LANG === 'es' ? 'Gratis y no partidista. 50 estados y DC.' : 'Free and nonpartisan. 50 states and DC.', 580, 548);
		return out.toDataURL('image/jpeg', 0.86);
	}, st);
	fs.writeFileSync(path.join(OGDIR, st.slug + '.jpg'), Buffer.from(url.split(',')[1], 'base64'));
}

// Hub card (the early voting one, or og-hub-post.jpg for the post set).
if (SET !== 'ended') {
const hub = await p.evaluate(async () => {
	const out = document.createElement('canvas'); out.width = 1200; out.height = 630;
	const g = out.getContext('2d');
	g.fillStyle = '#14295E'; g.fillRect(0, 0, 1200, 630);
	await og.sign(g, 64, 92, true);
	g.textAlign = 'left'; g.textBaseline = 'alphabetic'; g.fillStyle = '#FBF6E8';
	const es = window.VOTE_LANG === 'es';
	const post = window.VOTE_PHASE === 'post';
	const lines = post ? (es ? ['¿YA VOTASTE?', 'CONSIGUE', 'TU STICKER.'] : ['VOTED?', 'GET YOUR', 'STICKER.']) : es ? ['MUÉSTRALES', 'A TUS AMIGOS', 'QUE VAS A VOTAR.'] : ['SHOW YOUR', 'FRIENDS', "YOU'RE VOTING."];
	og.fit(g, post ? (es ? '¿YA VOTASTE?' : 'GET YOUR') : es ? 'QUE VAS A VOTAR.' : "YOU'RE VOTING.", 92, '"Vote Display"', 560);
	lines.forEach((t, i) => g.fillText(t, 64, 250 + i * 98));
	g.fillStyle = '#F2C14E'; g.font = '700 28px system-ui, sans-serif';
	g.fillText(es ? 'Stickers para votar en los 50 estados y DC' : 'Voting stickers for all 50 states and DC', 64, 560);
	const G = window.VOTE_GALLERY, pick = (a, n) => (G[a] || []).find((f) => f.indexOf(a.toLowerCase() + '-vote-' + n + '.jpg') > 0) || (G[a] || [])[0];
	const draw = (abbr) => {
		const c = document.createElement('canvas'), V = window.VOTE_CARDS, row = window.VOTE_STATES.find((x) => x.abbr === abbr), h = V.defaultHeadline(row);
		V.render(c, 600, { state: abbr, design: 'sticker', palette: 'state', headline: h, dateText: V.defaultDateLine(row, h) });
		return c;
	};
	const ims = es ? ['TX', 'WA', 'NC', 'CA'].map(draw) : await Promise.all([pick('TX', 2), pick('WA', 6), pick('PA', 4), pick('CA', 4)].map(og.img));
	[[835, 190, 135, -9], [1060, 170, 120, 7], [855, 450, 128, 5], [1075, 430, 115, -6]].forEach(([x, y, r, d], i) => og.sticker(g, ims[i], x, y, r, d));
	return out.toDataURL('image/jpeg', 0.86);
});
fs.writeFileSync(path.join(SITE, (ES ? 'assets/og-hub-es' : 'assets/og-hub') + (SET === 'post' ? '-post' : '') + '.jpg'), Buffer.from(hub.split(',')[1], 'base64'));
}
console.log(`${states.length} state cards${SET === 'ended' ? '' : ' and the hub card'} written`);
await b.close();
