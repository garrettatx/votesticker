import { chromium } from 'playwright';
const [, , out, ...specs] = process.argv;
const b = await chromium.launch();
for (const spec of specs) {
	const [url, w, name] = spec.split(',');
	const p = await b.newPage({ viewport: { width: +w, height: 900 } });
	await p.goto(url, { waitUntil: 'networkidle' }); await p.waitForTimeout(300);
	await p.screenshot({ path: `${out}/${name}.png`, fullPage: true });
	await p.close();
}
await b.close();
