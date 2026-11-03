// Downloads around the share row (November 3): maker saves, an edit then a second save, a save after opening a
// composer, two ready-made files, and a refused clipboard; each file is read for size and pixel dimensions.

import { chromium } from 'playwright';
import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';
const BASE = process.argv[2] || 'http://127.0.0.1:8792/'; const OUT = (await import('node:os')).tmpdir() + '/vs-dl/'; (await import('node:fs')).mkdirSync(OUT, { recursive: true });
const b = await chromium.launch(); let fails = 0;
const ok = (c, m) => { console.log((c ? '  ok   ' : '  FAIL ') + m); if (!c) fails++; };
const dims = (buf) => buf[0] === 0x89 ? [buf.readUInt32BE(16), buf.readUInt32BE(20)] : (() => { let i = 2; while (i < buf.length) { const m = buf[i + 1], l = buf.readUInt16BE(i + 2); if (m >= 0xC0 && m <= 0xC2) return [buf.readUInt16BE(i + 7), buf.readUInt16BE(i + 5)]; i += 2 + l; } return null; })();
for (const path of ['north-carolina/', 'es/north-carolina/']) {
  const ctx = await b.newContext({ viewport: { width: 1280, height: 900 }, acceptDownloads: true, permissions: ['clipboard-read', 'clipboard-write'] });
  const p = await ctx.newPage(); const errs = []; p.on('pageerror', e => errs.push(String(e)));
  await p.goto(BASE + path, { waitUntil: 'networkidle' });
  const save = async (label) => { await p.waitForFunction(() => document.getElementById('save').getAttribute('aria-disabled') !== 'true'); const [d] = await Promise.all([p.waitForEvent('download'), p.click('#save')]); const f = OUT + path.replace(/\//g, '_') + label + '-' + d.suggestedFilename(); await d.saveAs(f); const buf = readFileSync(f); return { name: d.suggestedFilename(), size: buf.length, dims: dims(buf), hash: createHash('md5').update(buf).digest('hex').slice(0, 8) }; };
  const opts = await p.$$eval('#headline option', o => o.map(x => x.value));
  const iv = opts.find(v => /^(ivoted|yavote)$/.test(v));
  await p.selectOption('#headline', iv);
  const a = await save('default');
  ok(a.size > 20000 && a.dims && a.dims[0] >= 800, `${path} custom download ${a.name} ${a.dims} ${a.size}B`);
  await p.waitForTimeout(800);
  ok(/(hasn't voted|todavía no ha votado)/.test(await p.$eval('#share-h', h => h.textContent)), `${path} heading becomes the nudge after the I voted save`);
  // Caption: folded at first, every version carries its own /go/ link, copy pastes the whole caption.
  ok(await p.$eval('details.post', d => !d.open), `${path} caption folded at first`);
  await p.click('details.post > summary');
  const slug = path.replace(/^es\//, '').replace(/\/$/, ''), es = path.startsWith('es/') ? 'es/' : '';
  const previewBefore = await p.evaluate(() => document.querySelector('canvas') && document.querySelector('canvas').toDataURL().length);
  for (const v of await p.$$eval('#post-picks [data-variant]', b => b.map(x => x.dataset.variant))) {
    await p.click(`#post-picks [data-variant="${v}"]`);
    const t = await p.$eval('#post-text', t => t.value);
    ok(t.includes(`https://votesticker.org/${es}go/${slug}/${v}`), `${path} caption ${v} has /${es}go/${slug}/${v}`);
  }
  ok(previewBefore === await p.evaluate(() => document.querySelector('canvas') && document.querySelector('canvas').toDataURL().length), `${path} switching captions leaves the preview alone`);
  const cap = await p.$eval('#post-text', t => t.value);
  await p.click('#copy'); await p.waitForTimeout(200);
  await p.evaluate(() => { const t = document.createElement('textarea'); t.id = '__qa'; document.body.appendChild(t); });
  await p.focus('#__qa'); await p.keyboard.press('ControlOrMeta+V');
  ok(await p.$eval('#__qa', t => t.value) === cap && /#/.test(cap), `${path} Copy caption and link pastes the full caption with hashtags`);
  await p.evaluate(() => document.getElementById('__qa').remove());
  // copy link, then change design, download again: not stale.
  await p.click('#copy-link'); await p.waitForTimeout(200);
  const tiles = await p.$$('[data-design]'); if (tiles[3]) { await tiles[3].click(); }
  const sw = await p.$$('[data-palette]'); if (sw[2]) { await sw[2].click(); }
  await p.waitForTimeout(500);
  const c = await save('changed');
  ok(c.hash !== a.hash && c.dims && c.size > 20000, `${path} second download differs after edits (${a.hash} -> ${c.hash})`);
  // open a composer (stubbed), come back, download again
  await ctx.route(/facebook\.com|threads\.com|bsky\.app|wa\.me/, r => r.fulfill({ status: 200, body: 'stub' }));
  const [pop] = await Promise.all([p.waitForEvent('popup'), p.click('.share-to a[data-net="facebook"]')]); await pop.close();
  const d2 = await save('after-composer');
  ok(d2.hash === c.hash, `${path} download after opening a composer matches the preview's file`);
  // prebuilt: two gallery downloads differ
  const links = await p.$$eval('#gallery-grid a[download], #gallery-grid a[href$=".jpg"], #gallery-grid a[href$=".png"]', l => l.slice(0, 2).map(a => a.getAttribute('href')));
  ok(links.length === 2 && links[0] !== links[1], `${path} two different ready-made links ${links}`);
  for (const h of links) { const r = await p.request.get(new URL(h, BASE + path).href); const buf = await r.body(); ok(r.status() === 200 && dims(buf) && buf.length > 20000, `${path} ready-made ${h.split('/').pop()} ${dims(buf)}`); }
  ok(!errs.length, `${path} no page errors ${errs}`);
  await ctx.close();
}
// share script blocked: maker still downloads
{
  const ctx = await b.newContext({ viewport: { width: 390, height: 900 }, acceptDownloads: true });
  const p = await ctx.newPage(); const errs = []; p.on('pageerror', e => errs.push(String(e)));
  await p.addInitScript(() => { Object.defineProperty(navigator, 'clipboard', { configurable: true, value: { writeText: () => Promise.reject(new DOMException('x', 'NotAllowedError')) } }); });
  await p.goto(BASE + 'north-carolina/', { waitUntil: 'networkidle' });
  await p.waitForFunction(() => document.getElementById('save').getAttribute('aria-disabled') !== 'true');
  await p.click('#copy-link', { force: true }).catch(() => {});
  const [d] = await Promise.all([p.waitForEvent('download'), p.click('#save')]);
  ok(!!d.suggestedFilename() && !errs.length, `clipboard refused, then download still works (${d.suggestedFilename()})`);
  await ctx.close();
}
await b.close(); console.log(fails ? `FAILED (${fails})` : 'PASS'); process.exit(fails ? 1 : 0);
