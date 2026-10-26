// Bespoke sets (October 26, Garrett: "different combos for each state page"): six stickers per state drawn by
// the maker, combinations only the maker makes (Varsity, Masthead, sunburst, tilt, Spanish, Future and
// First-time voter). Designs and palettes rotate by state, so neighbors don't share a set. Messages are
// the ones that stay true after Election Day. Files are named for people from the first render
// (texas-i-voted-varsity-sticker.jpg), and the gallery lists them after the state's own ready-mades.
// Alt text goes into site/assets/states/alts.json in the format build/image_names.py reads.
//   python3 -m http.server 8792 -d dist
//   node scripts/render_bespoke.mjs [TX,NC,...]      (default: all 51)
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const DIR = path.join(ROOT, 'site', 'assets', 'states');
const ALTS = path.join(DIR, 'alts.json');
const SIZE = 1080;
// Shadow left out: Varsity does the same thing, better (backlog/maker-bespoke).
const DESIGNS = ['varsity', 'slab', 'sticker', 'ringtext', 'arch', 'tilt', 'brush', 'type', 'plate', 'side', 'below', 'half', 'split'];
const PALETTES = ['navy', 'forest', 'cobalt', 'maroon', 'plum', 'teal', 'rust', 'charcoal', 'cream'];
// How each design looks, for alt text ("Navy sticker with 3D block letters, reading I voted, Texas 2026").
const LOOK = {
  varsity: 'with 3D block letters', slab: 'in a heavy slab serif', sticker: 'with rings like a round sticker',
  ringtext: 'with the state name around a ring', arch: 'with the first word arched over the state', tilt: 'with a slanted headline',
  brush: 'in brush lettering', type: 'in big block letters', plate: 'with one word on a color plate', side: 'with the state beside the words',
  below: 'with the state beside the second line', half: 'with a color field over the state', split: 'on a two-tone diagonal',
};
const SLUG = { ivoted: 'i-voted', firsttime: 'first-time-voter', futurevoter: 'future-voter', yavote: 'ya-vote', voted: 'i-voted-early', mailed: 'i-voted-by-mail',
  miprimervoto: 'mi-primer-voto', yotambien: 'yo-tambien-votare', yavotetemprano: 'ya-vote-temprano', yavotecorreo: 'ya-vote-por-correo' };
const SAYS = { ivoted: 'I voted', firsttime: 'First-time voter', futurevoter: 'Future voter', yavote: 'Ya voté', voted: 'I voted early', mailed: 'I voted by mail',
  miprimervoto: 'Mi primer voto', yotambien: 'Yo también votaré', yavotetemprano: 'Ya voté temprano', yavotecorreo: 'Ya voté por correo' };
// Spanish alt text for the Spanish pages (tú register, docs/spanish-style.md), stored as "<stem>@es".
const LOOK_ES = {
  varsity: 'con letras en bloque en 3D', slab: 'con letras de periódico', sticker: 'con anillos, como un sticker redondo',
  ringtext: 'con el nombre del estado alrededor de un anillo', arch: 'con la primera palabra en arco sobre el estado', tilt: 'con el título inclinado',
  brush: 'con letras de pincel', type: 'con letras grandes', plate: 'con una palabra sobre una placa de color', side: 'con el estado junto a las palabras',
  below: 'con el estado junto a la segunda línea', half: 'con un campo de color sobre el estado', split: 'en dos tonos con una diagonal',
};
const COLOR_ES = { navy: 'azul marino', forest: 'verde bosque', cobalt: 'azul cobalto', maroon: 'guinda', plum: 'ciruela', teal: 'verde azulado', rust: 'naranja quemado', charcoal: 'gris carbón', cream: 'crema' };

const b = await chromium.launch();
const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
await p.goto('http://127.0.0.1:8792/texas/', { waitUntil: 'networkidle' });
await p.waitForFunction(() => window.VOTE_CARDS && document.fonts.status === 'loaded');
const all = await p.evaluate(() => window.VOTE_STATES.map((s) => ({ abbr: s.abbr, name: s.name, early: s.status === 'verified' && s.kind === 'in-person', mail: s.status === 'verified' && s.kind === 'mail' })));
all.sort((a, c) => a.name.localeCompare(c.name));
const only = process.argv[2] ? process.argv[2].split(',') : null;

const alts = JSON.parse(fs.readFileSync(ALTS, 'utf8'));
const MANIFEST = path.join(DIR, 'bespoke.json');
const langs = fs.existsSync(MANIFEST) ? JSON.parse(fs.readFileSync(MANIFEST, 'utf8')) : {};
let n = 0;
for (const [i, st] of all.entries()) {
  if (only && !only.includes(st.abbr)) { continue; }
  // Six slots. Design: step 2 through the 13 designs, so a state's six are all different and the next
  // state starts one further along. Palette: state colors first, then a rotation.
  const d = (j) => DESIGNS[(i + j * 2) % DESIGNS.length];
  // Step 1 through the 9 palettes (step 3 repeated every third slot: 4 colorways in 6 English stickers,
  // 3 in the Spanish six, Garrett caught it Oct 26). Now each set of six has six colorways.
  const pal = (j) => PALETTES[(i * 2 + j) % PALETTES.length];
  const plan = [
    { headline: 'ivoted', design: d(0), palette: 'state' },
    { headline: 'firsttime', design: d(1), palette: pal(1), rays: i % 2 === 1 },
    { headline: 'futurevoter', design: d(2), palette: pal(2), rays: i % 2 === 0 },
    { headline: 'yavote', design: d(3), palette: pal(3), lang: 'both' },
    { headline: st.early ? 'voted' : st.mail ? 'mailed' : 'ivoted', design: d(4), palette: pal(4) },
    { headline: 'ivoted', design: d(5), palette: pal(5), tilt: i % 2 ? 0.12 : -0.12, rays: i % 3 === 0 },
    // Spanish set (Garrett, October 26: "across English and Spanish"): five more in designs the English six
    // don't use, shown on the Spanish page with the shared YA VOTÉ above.
    { headline: 'miprimervoto', design: d(6), palette: pal(6), lang: 'es' },
    { headline: 'yotambien', design: d(7), palette: pal(7), rays: true, lang: 'es' },
    { headline: st.early ? 'yavotetemprano' : st.mail ? 'yavotecorreo' : 'yavote', design: d(8), palette: 'state', lang: 'es' },
    { headline: 'yavote', design: d(9), palette: pal(9), tilt: i % 2 ? -0.12 : 0.12, lang: 'es' },
    { headline: 'yavote', design: d(10), palette: pal(10), rays: true, lang: 'es' },
  ];
  const out = await p.evaluate(({ abbr, plan, SIZE }) => {
    const V = window.VOTE_CARDS, s = window.VOTE_STATES.find((x) => x.abbr === abbr), c = document.createElement('canvas');
    return plan.map((x) => {
      const date = V.defaultDateLine(s, x.headline);
      V.render(c, SIZE, { state: abbr, design: x.design, palette: x.palette, headline: x.headline, dateText: date, tilt: x.tilt || 0, rays: !!x.rays });
      return { date, data: c.toDataURL('image/jpeg', 0.86) };
    });
  }, { abbr: st.abbr, plan, SIZE });
  const k = st.abbr.toLowerCase(), slug = st.name.toLowerCase().replace(/[^a-z]+/g, '-').replace(/^-|-$/g, '');
  // Remove this state's earlier bespoke render first, so a changed plan never leaves an orphan file.
  for (const f of fs.readdirSync(path.join(DIR, k))) { if (f.startsWith(slug + '-') && f.endsWith('.jpg')) { fs.unlinkSync(path.join(DIR, k, f)); delete alts[f.slice(0, -4)]; delete alts[f.slice(0, -4) + '@es']; delete langs[f.slice(0, -4)]; } }
  const used = new Set();
  plan.forEach((x, j) => {
    let base = `${slug}-${SLUG[x.headline]}-${x.design === 'slab' ? 'masthead' : x.design === 'sticker' ? 'round' : x.design === 'ringtext' ? 'ring' : x.design === 'type' ? 'big-type' : x.design}-sticker`;
    if (used.has(base)) { base += '-2'; }
    used.add(base);
    fs.writeFileSync(path.join(DIR, k, base + '.jpg'), Buffer.from(out[j].data.split(',')[1], 'base64'));
    const date = out[j].date;
    const when = /^[A-Z]{2} \d{4}$/.test(date) ? ', ' + date : date ? ', ' + date.toLowerCase().replace(/(^|[\s–-])([a-zà-ú])/g, (m, a, c) => a + c.toUpperCase()) : '';
    const color = x.palette === 'state' ? 'Sticker in ' + st.name + ' colors' : x.palette.charAt(0).toUpperCase() + x.palette.slice(1) + ' sticker';
    // The Tilt design already says its headline is slanted; the whole-sticker tilt is only named on the others.
    const tilted = x.tilt && x.design !== 'tilt';
    const extra = (x.rays ? ' on a sunburst' : '') + (tilted ? ', tilted' : '');
    alts[base] = `${color} ${LOOK[x.design]}${extra}, reading ${SAYS[x.headline]}${when}`;
    // The full recipe, so any sticker can be rebuilt or reused (homepage picks, the hero after Election Day).
    langs[base] = { lang: x.lang || 'en', slot: j, design: x.design, palette: x.palette, headline: x.headline, tilt: x.tilt || 0, rays: !!x.rays };
    if (x.lang) {
      const colorEs = x.palette === 'state' ? 'Sticker en los colores de ' + st.name : 'Sticker ' + COLOR_ES[x.palette];
      const extraEs = (x.rays ? ' sobre rayos de sol' : '') + (tilted ? ', inclinado' : '');
      alts[base + '@es'] = `${colorEs} ${LOOK_ES[x.design]}${extraEs}, que dice: ${SAYS[x.headline]}${when}`;
    }
    n++;
  });
}
fs.writeFileSync(MANIFEST, '{\n' + Object.keys(langs).sort().map((k) => JSON.stringify(k) + ': ' + JSON.stringify(langs[k])).join(',\n') + '\n}\n');
fs.writeFileSync(ALTS, '{\n' + Object.keys(alts).sort().map((k) => JSON.stringify(k) + ': ' + JSON.stringify(alts[k])).join(',\n') + '\n}\n');
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : `ok: ${n} bespoke stickers (${Object.values(langs).filter((x) => x.lang === 'es').length} Spanish-only)`);
await b.close();
