// Ready-made stickers drawn by the maker itself (October 25), for states whose own sets run out once early
// voting closes or after Election Day (index.php vote_file_current hides stickers that no longer say
// something true). Each state gets two I voted designs, I voted early (early voting states) or I voted by
// mail (mail states), and one Vote Nov 3, which the gallery shows only until the election.
// Files are named for what they say; alt text goes into site/assets/states/alts.json.
//   python3 -m http.server 8792 -d dist
//   node scripts/render_readymades.mjs [TX,NC,...]      (default: the states listed below)
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const DIR = path.join(ROOT, 'site', 'assets', 'states');
const ALTS = path.join(DIR, 'alts.json');
const STATES = (process.argv[2] || 'AK,CA,CO,DC,KS,MD,MN,MO,NC,ND,NM,NY,OH,OK,PA,RI,SC,SD,TN,TX,UT,VA,VT,WA,WI,WV,WY').split(',');
const SIZE = 1080;

const b = await chromium.launch();
const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
await p.goto('http://127.0.0.1:8792/texas/', { waitUntil: 'networkidle' });
await p.waitForFunction(() => window.VOTE_CARDS && document.fonts.status === 'loaded');

const alts = JSON.parse(fs.readFileSync(ALTS, 'utf8'));
let n = 0;
for (const abbr of STATES) {
  const out = await p.evaluate(({ abbr, SIZE }) => {
    const V = window.VOTE_CARDS, st = window.VOTE_STATES.find((x) => x.abbr === abbr);
    const early = st.status === 'verified' && st.kind === 'in-person', mail = st.status === 'verified' && st.kind === 'mail';
    const plan = [
      { file: 'i-voted-1', headline: 'ivoted', design: 'sticker', palette: 'state', says: 'I voted' },
      { file: 'i-voted-2', headline: 'ivoted', design: 'ringtext', palette: 'navy', says: 'I voted' },
      early ? { file: 'i-voted-early', headline: 'voted', design: 'type', palette: 'state', says: 'I voted early' } : null,
      mail ? { file: 'i-voted-by-mail', headline: 'mailed', design: 'type', palette: 'state', says: 'I voted by mail' } : null,
      { file: 'vote-nov-3', headline: 'nov3', design: 'shadow', palette: 'cream', says: 'Vote Nov 3' },
    ].filter(Boolean);
    const c = document.createElement('canvas');
    return plan.map((x) => {
      const date = V.defaultDateLine(st, x.headline);
      V.render(c, SIZE, { state: abbr, design: x.design, palette: x.palette, headline: x.headline, dateText: date, tilt: 0 });
      return { file: x.file, says: x.says, date, palette: x.palette, data: c.toDataURL('image/jpeg', 0.9) };
    });
  }, { abbr, SIZE });
  const k = abbr.toLowerCase(), name = await p.evaluate((a) => window.VOTE_STATES.find((x) => x.abbr === a).name, abbr);
  fs.mkdirSync(path.join(DIR, k), { recursive: true });
  for (const o of out) {
    const base = `${k}-${o.file}`;
    fs.writeFileSync(path.join(DIR, k, base + '.jpg'), Buffer.from(o.data.split(',')[1], 'base64'));
    // The date bar in title case ("OCT 19–NOV 2" to "Oct 19–Nov 2", "ELECTION DAY" to "Election Day").
    // A postal code stays in capitals ("PA 2026").
    const when = /^[A-Z]{2} \d{4}$/.test(o.date) ? ', ' + o.date : o.date ? ', ' + o.date.toLowerCase().replace(/(^|[\s–-])([a-z])/g, (m, a, c) => a + c.toUpperCase()) : '';
    alts[base] = `${o.palette === 'state' ? 'Sticker in ' + name + ' colors' : o.palette.charAt(0).toUpperCase() + o.palette.slice(1) + ' sticker'} reading ${o.says}${when}`;
    n++;
  }
}
// Same layout as the file already has: sorted, one entry per line (Python json.dumps(indent=0)).
fs.writeFileSync(ALTS, '{\n' + Object.keys(alts).sort().map((k) => JSON.stringify(k) + ': ' + JSON.stringify(alts[k])).join(',\n') + '\n}\n');
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : `ok: ${n} stickers for ${STATES.length} states`);
await b.close();
