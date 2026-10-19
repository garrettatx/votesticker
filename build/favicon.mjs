// Draws the VoteSticker.org favicon master (512 px PNG) from site/assets/mark.svg: the peeled sticker
// with the capital star, on a navy rounded square so it reads on light and dark browser tabs.
// node build/favicon.mjs <out.png>; then python3 -I build/favicon_sizes.py <out.png> site/assets/icons/
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const svg = fs.readFileSync(path.join(path.dirname(new URL(import.meta.url).pathname), '../site/assets/mark.svg'), 'utf8')
	.replace('width="40" height="40"', 'width="430" height="430"');
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 512, height: 512 } });
await p.setContent(`<body style="margin:0"><div style="width:512px;height:512px;border-radius:112px;background:#14295E;display:flex;align-items:center;justify-content:center">${svg}</div></body>`);
await p.screenshot({ path: process.argv[2], omitBackground: true, clip: { x: 0, y: 0, width: 512, height: 512 } });
await b.close();
