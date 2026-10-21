import { chromium } from 'playwright';
import fs from 'node:fs';
const OUT = process.argv[2];
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
async function sheet(url, spec, file, pre) {
  await p.goto(url, { waitUntil: 'networkidle' });
  await p.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
  if (pre) { await p.evaluate(pre[0], pre[1]); }
  const data = await p.evaluate((spec) => {
    const V = window.VOTE_CARDS, t = 170; const rows = spec.rows, cols = spec.cols;
    const out = document.createElement('canvas'); out.width = t * cols.length; out.height = t * rows.length;
    const g = out.getContext('2d'); g.fillStyle = '#fff'; g.fillRect(0, 0, out.width, out.height); const c = document.createElement('canvas');
    rows.forEach((d, ri) => cols.forEach((col, ci) => {
      const st = window.VOTE_STATES.find((x) => x.abbr === col.state);
      const pal = col.palette || V.DESIGNS.find((x) => x.id === d).palette;
      const h = col.headline || V.defaultHeadline(st);
      V.render(c, t, { state: col.state, design: d, palette: pal, headline: h, dateText: V.defaultDateLine(st, h) });
      g.drawImage(c, ci * t, ri * t);
    }));
    return out.toDataURL('image/png');
  }, spec);
  fs.writeFileSync(file, Buffer.from(data.split(',')[1], 'base64'));
}
const designs = ['tilt','type','brush','side','arch','plate','split','below','sticker','poster','stamp'];
const pals = ['state','cream','navy','forest','cobalt','maroon','plum','teal','rust','charcoal'];
await sheet('http://127.0.0.1:8791/texas/', { rows: designs, cols: pals.map((x) => ({ state: 'TX', palette: x })) }, OUT + '/sheet-palettes.png');
const cols = [
  { state: 'TX', headline: 'early' }, { state: 'TX', headline: 'nov3' }, { state: 'TX', headline: 'ivoted' }, { state: 'TX', headline: 'voted' },
  { state: 'WA', headline: 'mail' }, { state: 'WA', headline: 'mailed' }, { state: 'AL', headline: 'nov3' }, { state: 'TN', headline: 'ivoted' }, { state: 'ID', headline: 'ivoted' }];
await sheet('http://127.0.0.1:8791/texas/', { rows: designs, cols }, OUT + '/sheet-headlines.png');
const es = [{ state: 'WA', headline: 'votacorreo' }, { state: 'WA', headline: 'yavotecorreo' }, { state: 'TX', headline: 'votatemprano' }, { state: 'TX', headline: 'yavote' }, { state: 'AL', headline: 'voyavotar' }, { state: 'AL', headline: 'vota3nov' }];
await sheet('http://127.0.0.1:8791/es/washington/', { rows: designs, cols: es }, OUT + '/sheet-es.png');
await p.goto('http://127.0.0.1:8791/texas/', { waitUntil: 'networkidle' }); await p.waitForTimeout(600);
await p.locator('.maker').screenshot({ path: OUT + '/maker.png' });
// brush font comparison: the brush row in each face
const faces = process.argv[3] ? process.argv[3].split(',') : [];
for (const f of faces) { await sheet('http://127.0.0.1:8791/texas/', { rows: ['brush', 'tilt'], cols: cols.concat(es) }, OUT + '/brush-' + f.replace(/\W/g, '') + '.png', [(f) => window.VOTE_CARDS.setBrush('Brush ' + f, '/assets/fonts/' + f + '.woff2'), f]); }
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : 'ok');
await b.close();
