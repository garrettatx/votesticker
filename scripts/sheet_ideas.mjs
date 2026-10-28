// October 27 ideation sheets: the 24 designs in window.VOTE_CARDS.IDEAS (12 with a state, 12 general),
// each across states, messages and palettes, at full size and as 60 and 40 px profile circles, plus one
// 1080 px render per design and an index.html to browse them on the LAN.
//   php -S 127.0.0.1:8791 -t site
//   node scripts/sheet_ideas.mjs <out dir>
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const OUT = process.argv[2];
fs.mkdirSync(path.join(OUT, 'big'), { recursive: true });
const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
const TX = 'http://127.0.0.1:8791/texas/';
await p.goto(TX, { waitUntil: 'networkidle' });
await p.waitForFunction(() => document.querySelector('#save')?.getAttribute('aria-disabled') !== 'true');

// rows: [{ design, palette? }], cols: [{ state, palette?, headline? }]. A column's palette wins; 'default'
// means the design's own.
async function sheet(spec, file) {
  const data = await p.evaluate((spec) => {
    const V = window.VOTE_CARDS, t = spec.t || 240, lab = spec.label ? 170 : 0, rows = spec.rows, cols = spec.cols;
    const out = document.createElement('canvas'); out.width = lab + t * cols.length; out.height = t * rows.length;
    const g = out.getContext('2d'); g.fillStyle = '#fff'; g.fillRect(0, 0, out.width, out.height); const c = document.createElement('canvas');
    rows.forEach((r, ri) => {
      const d = V.IDEAS.find((x) => x.id === r.design);
      if (lab) { g.fillStyle = '#1b1b1b'; g.font = 'bold 22px -apple-system, system-ui, sans-serif'; g.textBaseline = 'middle'; g.fillText((ri + 1) + '. ' + d.name, 12, ri * t + t / 2 - 14); g.font = '16px -apple-system, system-ui, sans-serif'; g.fillStyle = '#666'; g.fillText(d.style, 12, ri * t + t / 2 + 14); }
      cols.forEach((col, ci) => {
        const st = window.VOTE_STATES.find((x) => x.abbr === col.state);
        const pal = (col.palette && col.palette !== 'default') ? col.palette : d.palette;
        const h = col.headline || V.defaultHeadline(st);
        V.render(c, t, { state: col.state, design: r.design, palette: pal, headline: h, dateText: V.defaultDateLine(st, h) });
        g.drawImage(c, lab + ci * t, ri * t);
        if (spec.circle) { g.save(); g.beginPath(); g.rect(lab + ci * t, ri * t, t, t); g.arc(lab + ci * t + t / 2, ri * t + t / 2, t / 2, 0, Math.PI * 2, true); g.fillStyle = 'rgba(255,255,255,.85)'; g.fill(); g.restore(); }
      });
    });
    return out.toDataURL('image/png');
  }, spec);
  fs.writeFileSync(file, Buffer.from(data.split(',')[1], 'base64'));
}
const ideas = await p.evaluate(() => window.VOTE_CARDS.IDEAS);
const stateRows = ideas.filter((d) => d.group === 'state').map((d) => ({ design: d.id }));
const genRows = ideas.filter((d) => d.group === 'general').map((d) => ({ design: d.id }));
// State columns: the design's own palette on Texas, then other states, messages and colors.
const stateCols = [
  { state: 'TX', headline: 'early', palette: 'default' }, { state: 'TX', headline: 'ivoted', palette: 'state' },
  { state: 'CA', headline: 'mail', palette: 'cobalt' }, { state: 'NC', headline: 'voted', palette: 'teal' },
  { state: 'OH', headline: 'ivoted', palette: 'plum' }, { state: 'AL', headline: 'nov3', palette: 'cream' },
  { state: 'ID', headline: 'firsttime', palette: 'maroon' }, { state: 'FL', headline: 'futurevoter', palette: 'navy' }];
const genCols = [
  { state: 'TX', headline: 'ivoted', palette: 'default' }, { state: 'TX', headline: 'ivotedtoday', palette: 'navy' },
  { state: 'TX', headline: 'voted', palette: 'cream' }, { state: 'TX', headline: 'mailed', palette: 'cobalt' },
  { state: 'TX', headline: 'firsttime', palette: 'maroon' }, { state: 'TX', headline: 'futurevoter', palette: 'teal' },
  { state: 'TX', headline: 'nov3', palette: 'forest' }, { state: 'TX', headline: 'ivoted', palette: 'rust' }];
await sheet({ rows: stateRows, cols: stateCols, label: true }, OUT + '/state-designs.png');
await sheet({ rows: stateRows, cols: stateCols, label: true, t: 60, circle: true }, OUT + '/state-designs-60.png');
await sheet({ rows: stateRows, cols: stateCols, label: true, t: 40, circle: true }, OUT + '/state-designs-40.png');
await sheet({ rows: genRows, cols: genCols, label: true }, OUT + '/general-designs.png');
await sheet({ rows: genRows, cols: genCols, label: true, t: 60, circle: true }, OUT + '/general-designs-60.png');
await sheet({ rows: genRows, cols: genCols, label: true, t: 40, circle: true }, OUT + '/general-designs-40.png');
// The four new color combinations, every design, Texas I voted.
const palCols = ['midnight', 'black', 'olive', 'indigo', 'charcoal', 'cobalt'].map((x) => ({ state: 'TX', headline: 'ivoted', palette: x }));
await sheet({ rows: stateRows.concat(genRows), cols: palCols, label: true, t: 200 }, OUT + '/new-palettes.png');

// One full-size render per design, in its own palette, and a page to browse them.
const cards = [];
for (const d of ideas) {
  const st = d.group === 'state' ? 'TX' : 'TX', h = d.group === 'state' ? 'early' : 'ivoted';
  const data = await p.evaluate(({ d, st, h }) => {
    const V = window.VOTE_CARDS, s = window.VOTE_STATES.find((x) => x.abbr === st), c = document.createElement('canvas');
    V.render(c, 1080, { state: st, design: d.id, palette: d.palette, headline: h, dateText: V.defaultDateLine(s, h) });
    return c.toDataURL('image/png');
  }, { d, st, h });
  fs.writeFileSync(path.join(OUT, 'big', d.id + '.png'), Buffer.from(data.split(',')[1], 'base64'));
  cards.push(d);
}
const card = (d, n) => `<figure><img src="big/${d.id}.png" alt=""><span class="pp"><img src="big/${d.id}.png" alt=""></span><figcaption><b>${n}. ${d.name}</b> <i>${d.style}</i><br><small>${d.palette}</small></figcaption></figure>`;
const html = `<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Sticker ideas, October 27</title>
<style>body{font:16px -apple-system,system-ui,sans-serif;margin:24px;background:#f4f4f2;color:#1b1b1b}h1,h2{margin:0 0 12px}section{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;margin-bottom:40px}
figure{margin:0;background:#fff;padding:12px;border-radius:12px;position:relative}figure>img{width:100%;border-radius:8px;display:block}.pp{position:absolute;right:18px;top:18px;width:48px;height:48px;border-radius:50%;overflow:hidden;box-shadow:0 0 0 3px #fff}.pp img{width:100%;height:100%;display:block}figcaption{margin-top:8px;line-height:1.3}i{color:#666;font-style:normal}small{color:#888}
p.sheets a{margin-right:14px}</style>
<h1>Sticker ideas, October 27</h1><p class="sheets">Contact sheets: <a href="state-designs.png">state designs</a> <a href="state-designs-60.png">at 60 px</a> <a href="state-designs-40.png">at 40 px</a> · <a href="general-designs.png">general designs</a> <a href="general-designs-60.png">at 60 px</a> <a href="general-designs-40.png">at 40 px</a> · <a href="new-palettes.png">new color combinations</a></p>
<h2>State designs (12)</h2><section>${cards.filter((d) => d.group === 'state').map((d, i) => card(d, i + 1)).join('')}</section>
<h2>General designs (12)</h2><section>${cards.filter((d) => d.group === 'general').map((d, i) => card(d, i + 1)).join('')}</section>`;
fs.writeFileSync(path.join(OUT, 'index.html'), html);
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : 'ok');
await b.close();
