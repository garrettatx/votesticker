// The 1200 x 630 link-preview image for /vote/ (also the picture in the garrettn.com post): what the page
// does, then four ready-made state designs in their profile-picture circles. No address on it.
// node og_image.mjs <out.jpg>   (needs the local server: php -S 127.0.0.1:8790 in site/)
import { chromium } from 'playwright';
import fs from 'node:fs';
const b = await chromium.launch(); const p = await b.newPage();
await p.goto('http://127.0.0.1:8790/?state=tx'); await p.waitForTimeout(1500);
const url = await p.evaluate(async () => {
	const V = window.VOTE_CARDS, out = document.createElement('canvas');
	out.width = 1200; out.height = 630;
	const g = out.getContext('2d');
	g.fillStyle = '#F3EEE2'; g.fillRect(0, 0, 1200, 630);
	// What the page does, in words, above the cards: a shared link has to say it on its own (October 19).
	g.fillStyle = '#14295E'; g.textAlign = 'center'; g.textBaseline = 'alphabetic';
	g.font = '84px "Vote Display"';
	g.fillText('MAKE A VOTE PROFILE PICTURE', 600, 112);
	g.fillText('FOR YOUR STATE', 600, 200);
	g.fillStyle = '#B3262C'; g.font = '25px "Vote Date"';
	g.fillText('YOUR STATE\u2019S DATES  \u2022  FITS THE PROFILE CIRCLE  \u2022  FREE & NONPARTISAN', 600, 250);
	// The cards are ready-made designs, one per state (Garrett, October 19: "use some of our best designs").
	const picks = ['assets/states/tx/tx-vote-6.jpg', 'assets/states/ks/ks-vote-6.jpg', 'assets/states/mo/mo-vote-5.jpg', 'assets/states/wa/wa-vote-1.jpg'];
	const imgs = await Promise.all(picks.map((src) => new Promise((ok, bad) => { const im = new Image(); im.onload = () => ok(im); im.onerror = bad; im.src = src; })));
	const size = 250, gap = 36, x0 = (1200 - (size * 4 + gap * 3)) / 2;
	const y = 292;
	imgs.forEach((im, i) => {
		const x = x0 + i * (size + gap);
		g.save(); g.beginPath(); g.arc(x + size / 2, y + size / 2, size / 2, 0, Math.PI * 2); g.clip();
		g.drawImage(im, x, y, size, size); g.restore();
	});
	return out.toDataURL('image/jpeg', 0.9);
});
fs.writeFileSync(process.argv[2], Buffer.from(url.split(',')[1], 'base64')); await b.close();
