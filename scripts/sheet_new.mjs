// Review sheets for layouts not in the tile row (window.VOTE_CARDS.REVIEW): every headline, every
// palette, Spanish, and 60 and 40 px circle crops.
//   php -S 127.0.0.1:8791 -t site
//   node scripts/sheet_new.mjs <out dir> [design,design,...]
import { chromium } from 'playwright';
import fs from 'node:fs';
const OUT = process.argv[2];
fs.mkdirSync(OUT, { recursive: true });
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));

// rows: [{ design, palette?, tilt? }], cols: [{ state, palette?, headline?, date? }]
async function sheet(url, spec, file, pre) {
  await p.goto(url, { waitUntil: 'networkidle' });
  await p.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');
  if (pre) { await p.evaluate(pre[0], pre[1]); }
  const data = await p.evaluate((spec) => {
    const V = window.VOTE_CARDS, t = spec.t || 220, lab = spec.label ? 150 : 0; const rows = spec.rows, cols = spec.cols;
    const out = document.createElement('canvas'); out.width = lab + t * cols.length; out.height = t * rows.length;
    const g = out.getContext('2d'); g.fillStyle = '#fff'; g.fillRect(0, 0, out.width, out.height); const c = document.createElement('canvas');
    const all = V.DESIGNS.concat(V.REVIEW);
    rows.forEach((r, ri) => {
      if (lab) { g.fillStyle = '#1b1b1b'; g.font = 'bold 22px -apple-system, system-ui, sans-serif'; g.textBaseline = 'middle'; g.fillText((ri + 1) + '. ' + (all.find((x) => x.id === r.design) || { name: r.design }).name, 12, ri * t + t / 2); }
      cols.forEach((col, ci) => {
        const st = window.VOTE_STATES.find((x) => x.abbr === col.state);
        const pal = col.palette || r.palette || (all.find((x) => x.id === r.design) || {}).palette || 'navy';
        const h = col.headline || V.defaultHeadline(st);
        V.render(c, t, { state: col.state, design: r.design, palette: pal, headline: h, dateText: col.date || V.defaultDateLine(st, h), tilt: r.tilt || 0 });
        g.drawImage(c, lab + ci * t, ri * t);
        if (spec.circle) {
          g.save(); g.beginPath(); g.rect(lab + ci * t, ri * t, t, t); g.arc(lab + ci * t + t / 2, ri * t + t / 2, t / 2, 0, Math.PI * 2, true); g.fillStyle = 'rgba(255,255,255,.85)'; g.fill(); g.restore();
        }
      });
    });
    return out.toDataURL('image/png');
  }, spec);
  fs.writeFileSync(file, Buffer.from(data.split(',')[1], 'base64'));
}
const ids = process.argv[3] ? process.argv[3].split(',') : ['band', 'tone', 'shadow', 'stack', 'ringtext', 'check', 'label', 'diag', 'pills', 'hollow', 'half', 'mono'];
const designs = ids.map((design) => ({ design }));
const pals = ['state', 'cream', 'navy', 'forest', 'cobalt', 'maroon', 'plum', 'teal', 'rust', 'charcoal'];
const TX = 'http://127.0.0.1:8791/texas/';
const cols = [
  { state: 'TX', headline: 'early' }, { state: 'TX', headline: 'nov3' }, { state: 'TX', headline: 'ivoted' }, { state: 'TX', headline: 'voted' },
  { state: 'WA', headline: 'mail' }, { state: 'WA', headline: 'mailed' }, { state: 'WA', headline: 'ballotin' }, { state: 'AL', headline: 'nov3' }, { state: 'TN', headline: 'ivoted' }, { state: 'ID', headline: 'ivoted' }, { state: 'NC', headline: 'early' }];
await sheet(TX, { rows: designs, cols, label: true }, OUT + '/review-headlines.png');
await sheet(TX, { rows: designs, cols: pals.map((x) => ({ state: 'TX', palette: x })), label: true }, OUT + '/review-palettes.png');
const es = [{ state: 'WA', headline: 'votacorreo' }, { state: 'WA', headline: 'yavotecorreo' }, { state: 'TX', headline: 'votatemprano' }, { state: 'TX', headline: 'yavote' }, { state: 'TX', headline: 'yavotetemprano' }, { state: 'AL', headline: 'voyavotar' }, { state: 'AL', headline: 'vota3nov' }];
await sheet('http://127.0.0.1:8791/es/washington/', { rows: designs, cols: es, label: true }, OUT + '/review-es.png');
await sheet(TX, { rows: designs, cols, t: 60, circle: true, label: true }, OUT + '/review-60.png');
await sheet(TX, { rows: designs, cols, t: 40, circle: true, label: true }, OUT + '/review-40.png');
await sheet(TX, { rows: designs, cols: [{ state: 'TX', headline: 'early' }, { state: 'TX', headline: 'ivoted', palette: 'navy' }, { state: 'WA', headline: 'mail', palette: 'cream' }, { state: 'NC', headline: 'early', palette: 'state' }], t: 400, label: true }, OUT + '/review-big.png');
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : 'ok');
await b.close();
