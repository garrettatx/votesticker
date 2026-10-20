// Device emulation QA for votesticker.org: iPhone, iPad, and Android profiles load the hub and one page
// per state family without sideways scroll or script errors, and the state finder works by touch.
//   node scripts/qa_devices.mjs [--base http://127.0.0.1:8792/]
import { chromium, webkit, devices } from 'playwright';
const i = process.argv.indexOf('--base');
const BASE = i > 0 ? process.argv[i + 1] : 'http://127.0.0.1:8792/';
let fails = 0;
for (const [type, dev] of [[webkit, 'iPhone 13'], [webkit, 'iPhone SE'], [webkit, 'iPad Mini'], [chromium, 'Pixel 7'], [chromium, 'Galaxy S9+']]) {
	const b = await type.launch(); const ctx = await b.newContext({ ...devices[dev] }); const p = await ctx.newPage();
	const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
	for (const u of ['', 'texas/', 'oregon/', 'alabama/', 'arizona/', 'es/', 'es/texas/', 'es/oregon/', 'es/new-york/']) {
		await p.goto(BASE + u, { waitUntil: 'networkidle' });
		const over = await p.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
		if (over > 0) { console.log(`  FAIL ${dev} /${u} scrolls sideways ${over}px`); fails++; }
	}
	await p.goto(BASE, { waitUntil: 'networkidle' });
	await p.tap('#state-find'); await p.fill('#state-find', 'ore');
	await p.tap('#state-opt-0'); await p.waitForURL(BASE + 'oregon/');
	// Spanish: the finder takes Spanish names without accents, and the maker defaults to Spanish.
	await p.goto(BASE + 'es/', { waitUntil: 'networkidle' });
	await p.tap('#state-find'); await p.fill('#state-find', 'nueva y');
	const first = await p.textContent('#state-opt-0');
	if (!/^New York/.test(first || '')) { console.log(`  FAIL ${dev} es finder: "nueva y" first is ${first}`); fails++; }
	await p.tap('#state-opt-0'); await p.waitForURL(BASE + 'es/new-york/');
	const head = await p.$eval('#headline', (s) => s.options[s.selectedIndex].textContent);
	if (head !== 'Vota temprano') { console.log(`  FAIL ${dev} es default message is ${head}`); fails++; }
	const art = await p.$eval('.state-hero__sticker', (i) => i.src.slice(0, 22)).catch(() => 'none');
	if (art !== 'data:image/png;base64,' && (await p.evaluate(() => innerWidth)) >= 860) { console.log(`  FAIL ${dev} es header sticker not drawn (${art})`); fails++; }
	if (errs.length) { console.log(`  FAIL ${dev} errors: ${errs[0]}`); fails++; }
	console.log(`${dev}: ${fails ? 'checked' : 'ok'}`);
	await b.close();
}
console.log(fails ? `FAILED (${fails})` : 'PASS');
process.exit(fails ? 1 : 0);
