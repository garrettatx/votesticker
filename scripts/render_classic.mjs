// Classic national stickers (October 26): the home page's "no state needed" set. Three layouts Garrett kept
// from five (Stars and stripes, Star ring, Banded) times six messages, in English and Spanish: 36 round
// PNGs with transparent corners, 1080 px, drawn by the maker's renderer. Writes site/assets/classic/<stem>.png
// and site/assets/classic/classic.json (one entry per sticker: stem, lang, pos, layout, headline, says,
// alt), which index.php reads. Spanish says YO VOTÉ, what US counties print on bilingual stickers.
//   php -S 127.0.0.1:8791 -t site     (or the dist server on 8792)
//   node scripts/render_classic.mjs [base url]
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..');
const DIR = path.join(ROOT, 'site', 'assets', 'classic');
const BASE = process.argv[2] || 'http://127.0.0.1:8792/';
const SIZE = 1080;
const LAYOUTS = { cflag: 'stars-and-stripes', cring: 'star-ring', cband: 'red-white-and-blue-band' };
// Messages in page order, each with its Spanish counterpart and the sticker's lines in sentence case (the
// alt text names which line is where). Spanish uses YO VOTÉ, as on US counties' bilingual stickers.
const MESSAGES = [
  { en: 'ivotedtoday', es: 'yovotehoy', enSlug: 'i-voted-today', esSlug: 'yo-vote-hoy', enLines: ['I voted', 'today'], esLines: ['Yo voté', 'hoy'] },
  { en: 'ivoted', es: 'yovote', enSlug: 'i-voted', esSlug: 'yo-vote', enLines: ['I voted'], esLines: ['Yo voté'] },
  { en: 'voted', es: 'yovotetemprano', enSlug: 'i-voted-early', esSlug: 'yo-vote-temprano', enLines: ['I voted', 'early'], esLines: ['Yo voté', 'temprano'] },
  { en: 'mailed', es: 'yovotecorreo', enSlug: 'i-voted-by-mail', esSlug: 'yo-vote-por-correo', enLines: ['I voted', 'by mail'], esLines: ['Yo voté', 'por correo'] },
  { en: 'firsttime', es: 'miprimervoto', enSlug: 'first-time-voter', esSlug: 'mi-primer-voto', enLines: ['First-time', 'voter'], esLines: ['Mi primer', 'voto'] },
  { en: 'futurevoter', es: 'yotambien', enSlug: 'future-voter', esSlug: 'yo-tambien-votare', enLines: ['Future', 'voter'], esLines: ['Yo también', 'votaré'] },
];
const LAYOUT_IDS = Object.keys(LAYOUTS);

// Alt text says what the sticker says, then what it looks like, accurately per layout: which line is in
// which color and where (October 26, Garrett: "helpful and accurate alt text").
const says = (lines) => lines.join(' ').replace(/^./, (c) => c.toUpperCase());
const q = (s) => `“${s}”`;
function altEn(layout, lines) {
  const two = lines.length > 1;
  if (layout === 'cflag') { return `Round sticker that says ${says(lines)}, in ${two ? 'red and navy' : 'red'} letters on a white band, with white stars on navy above and red stripes below`; }
  if (layout === 'cring') { return `Round sticker that says ${says(lines)}, in ${two ? 'red and navy' : 'red'} letters inside a navy ring of white stars`; }
  return two
    ? `Round sticker that says ${says(lines)}: ${q(lines[0])} in navy on a white band and ${q(lines[1])} in white on a red base, with white stars on navy across the top`
    : `Round sticker that says ${says(lines)}, in navy letters on a white band, with white stars on navy across the top and a red base`;
}
function altEs(layout, lines) {
  const two = lines.length > 1;
  if (layout === 'cflag') { return `Sticker redondo que dice ${says(lines)}, con letras ${two ? 'rojas y azul marino' : 'rojas'} sobre una franja blanca, estrellas blancas sobre azul marino arriba y franjas rojas abajo`; }
  if (layout === 'cring') { return `Sticker redondo que dice ${says(lines)}, con letras ${two ? 'rojas y azul marino' : 'rojas'} dentro de un anillo azul marino con estrellas blancas`; }
  return two
    ? `Sticker redondo que dice ${says(lines)}: ${q(lines[0])} en azul marino sobre una franja blanca y ${q(lines[1])} en blanco sobre la parte de abajo, que es roja, con estrellas blancas sobre azul marino arriba`
    : `Sticker redondo que dice ${says(lines)}, con letras azul marino sobre una franja blanca, estrellas blancas sobre azul marino arriba y la parte de abajo roja`;
}

// Page order (Garrett: "so we don't have [similar] images next to one another"): the 18 sit in a grid of 3
// columns on phones and 6 on wider screens. Search for an order where no two neighbors, side by side or
// one above the other, share a layout or a message in either grid.
function arrange() {
  const cells = [];
  MESSAGES.forEach((m, mi) => LAYOUT_IDS.forEach((l, li) => cells.push({ mi, li })));
  const n = cells.length, order = [], used = new Array(n).fill(false);
  const clash = (a, b) => a.mi === b.mi || a.li === b.li;
  // The first six (shown before See all) are the six messages, two in each layout.
  const ok = (i, c) => !(i < 6 && order.slice(0, i).some((o) => o.mi === c.mi)) && [[3, 1], [3, 3], [6, 1], [6, 6]].every(([cols, k]) => {
    const j = i - k;
    if (j < 0 || (k === 1 && i % cols === 0)) { return true; }
    return !clash(order[j], c);
  });
  const go = (i) => {
    if (i === n) { return true; }
    for (let x = 0; x < n; x++) {
      if (used[x] || !ok(i, cells[x])) { continue; }
      used[x] = true; order[i] = cells[x];
      if (go(i + 1)) { return true; }
      used[x] = false;
    }
    return false;
  };
  if (!go(0)) { throw new Error('no arrangement found'); }
  return order;
}

const plan = [];
arrange().forEach(({ mi, li }, pos) => {
  const m = MESSAGES[mi], l = LAYOUT_IDS[li];
  plan.push({ stem: `${m.enSlug}-${LAYOUTS[l]}-sticker`, lang: 'en', pos, layout: l, headline: m.en, says: says(m.enLines), alt: altEn(l, m.enLines) });
  plan.push({ stem: `${m.esSlug}-${LAYOUTS[l]}-sticker`, lang: 'es', pos, layout: l, headline: m.es, says: says(m.esLines), alt: altEs(l, m.esLines) });
});

const b = await chromium.launch();
const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
const errs = []; p.on('pageerror', (e) => errs.push(String(e)));
await p.goto(BASE + 'texas/', { waitUntil: 'networkidle' });
await p.waitForFunction(() => window.VOTE_CARDS && document.fonts.status === 'loaded');
const out = await p.evaluate(({ plan, SIZE }) => plan.map((x) => {
  const V = window.VOTE_CARDS, c = document.createElement('canvas');
  V.render(c, SIZE, { state: 'DC', design: x.layout, palette: 'navy', headline: x.headline, dateText: '', tilt: 0 });
  // Round with transparent corners: the sticker is the circle.
  const g = c.getContext('2d');
  // An opaque fill: the renderer can leave fillStyle transparent (the classic layouts' invisible frame marks).
  g.setTransform(1, 0, 0, 1, 0, 0); g.globalAlpha = 1; g.fillStyle = '#000'; g.globalCompositeOperation = 'destination-in';
  g.beginPath(); g.arc(SIZE / 2, SIZE / 2, SIZE / 2, 0, Math.PI * 2); g.fill();
  return c.toDataURL('image/png');
}), { plan, SIZE });
fs.rmSync(DIR, { recursive: true, force: true });
fs.mkdirSync(DIR, { recursive: true });
plan.forEach((x, i) => fs.writeFileSync(path.join(DIR, x.stem + '.png'), Buffer.from(out[i].split(',')[1], 'base64')));
fs.writeFileSync(path.join(DIR, 'classic.json'), JSON.stringify(plan, null, 1) + '\n');
console.log(errs.length ? 'ERRORS ' + errs.join(' | ') : `ok: ${plan.length} classic stickers`);
await b.close();
