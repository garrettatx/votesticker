"""
Finish a Codex ready-made set: find the state outline in each image, move the capital star to its
true spot (site/state-shapes.js), open the gap between the outline and the headline, then center and
fit each image into the profile circle (fit_to_circle.py) and write a review sheet.

    python3 -I build/codex_finish.py <ABBR> <out_dir> <png> [<png> ...]

Writes <out_dir>/<abbr>-vote-N.jpg in the order given, <out_dir>/fixed/*.png and <out_dir>/sheet.png.
Needs a flat background. An image where no outline matches is fitted unchanged and reported.
Made October 20 from the Mississippi pass; see docs/codex-ready-made-brief.md.
"""
import json, math, re, subprocess, sys
from collections import deque
from pathlib import Path
import numpy as np
from PIL import Image, ImageDraw

ROOT = Path(__file__).resolve().parent.parent
STEP = 3          # component search runs on every third pixel
GAP = 0.05        # outline-to-headline gap, share of image width


def shape_mask(abbr):
    sh = json.loads(re.search(r'VOTE_SHAPES=(\{.*\})', (ROOT / 'site/state-shapes.js').read_text()).group(1))[abbr]
    im = Image.new('L', (sh['w'], sh['h']), 0); d = ImageDraw.Draw(im)
    for part in re.split(r'M', sh['d'])[1:]:
        pts = [tuple(map(float, p.split(','))) for p in re.findall(r'-?[\d.]+,-?[\d.]+', part)]
        if len(pts) > 2: d.polygon(pts, fill=255)
    return np.asarray(im) > 0, sh['cap'][0] / sh['w'], sh['cap'][1] / sh['h']


def flood(mask, seeds):
    h, w = mask.shape; seen = np.zeros_like(mask); q = deque()
    for s in seeds:
        if mask[s] and not seen[s]: seen[s] = True; q.append(s)
    while q:
        y, x = q.popleft()
        for dy, dx in ((1, 0), (-1, 0), (0, 1), (0, -1)):
            ny, nx = y + dy, x + dx
            if 0 <= ny < h and 0 <= nx < w and mask[ny, nx] and not seen[ny, nx]:
                seen[ny, nx] = True; q.append((ny, nx))
    return seen


def fill_holes(mask):
    h, w = mask.shape
    border = [(0, x) for x in range(w)] + [(h - 1, x) for x in range(w)] + [(y, 0) for y in range(h)] + [(y, w - 1) for y in range(h)]
    return ~flood(~mask, border)


def grow(m, n, within=None):
    for _ in range(n):
        m = m | np.roll(m, 1, 0) | np.roll(m, -1, 0) | np.roll(m, 1, 1) | np.roll(m, -1, 1)
        if within is not None: m &= within
    return m


def shrink(m, n):
    for _ in range(n):
        m = m & np.roll(m, 1, 0) & np.roll(m, -1, 0) & np.roll(m, 1, 1) & np.roll(m, -1, 1)
    return m


def find_outline(nonbg, shape):
    """Return the full-resolution mask of the state outline (holes filled), or None."""
    s = nonbg[::STEP, ::STEP]; H, W = s.shape
    lab = np.zeros(s.shape, int); comps = []
    for y, x in zip(*np.nonzero(s)):
        if lab[y, x]: continue
        c = flood(s, [(y, x)]); n = len(comps) + 1; lab[c] = n
        ys, xs = np.nonzero(c); comps.append((n, c.sum(), xs.min(), ys.min(), xs.max() + 1, ys.max() + 1))
    sh_h, sh_w = shape.shape
    # the shape's largest part, to anchor the scale from one component
    parts = []; seen = np.zeros_like(shape)
    for y, x in zip(*np.nonzero(shape[::4, ::4])):
        y *= 4; x *= 4
        if seen[y, x] or not shape[y, x]: continue
        c = flood(shape, [(y, x)]); seen |= c; parts.append(c)
    big = max(parts, key=lambda c: c.sum()); py, px = np.nonzero(big)
    pb = (px.min(), py.min(), px.max() + 1, py.max() + 1)
    best = (0.0, None)
    for n, area, x0, y0, x1, y1 in comps:
        if area < 150: continue
        sx = (x1 - x0) / (pb[2] - pb[0]); sy = (y1 - y0) / (pb[3] - pb[1])
        if not 0.7 < sx / sy < 1.4: continue
        fx0 = x0 - pb[0] * sx; fy0 = y0 - pb[1] * sy; fw, fh = sh_w * sx, sh_h * sy
        fx0, fy0 = int(round(fx0)), int(round(fy0)); fx1, fy1 = int(round(fx0 + fw)), int(round(fy0 + fh))
        if fx0 < -3 or fy0 < -3 or fx1 > W + 3 or fy1 > H + 3: continue
        fx0, fy0, fx1, fy1 = max(fx0, 0), max(fy0, 0), min(fx1, W), min(fy1, H)
        ids = {m for m, a2, a, b, c, d in comps if a >= fx0 - 2 and b >= fy0 - 2 and c <= fx1 + 2 and d <= fy1 + 2} | {n}
        # score the anchor against the shape's largest part alone: generated maps place a second part
        # (Michigan's Upper Peninsula) only roughly, so a whole-shape score undersells a good match
        got = fill_holes(lab[y0:y1, x0:x1] == n)
        part = big[pb[1]:pb[3], pb[0]:pb[2]]
        want = np.asarray(Image.fromarray(part).resize((x1 - x0, y1 - y0), Image.NEAREST))
        iou = (got & want).sum() / max((got | want).sum(), 1)
        if iou > best[0]: best = (iou, ids)
    if best[0] < 0.6: return None, best[0]
    full = np.isin(np.kron(lab, np.ones((STEP, STEP), int))[:nonbg.shape[0], :nonbg.shape[1]], list(best[1]))
    return fill_holes(grow(full, 2, nonbg)), best[0]


def fix(src, dst, abbr, shape, capx, capy):
    a = np.asarray(Image.open(src).convert('RGB')).astype(int).copy(); H, W, _ = a.shape
    bg = np.median(np.array([a[6, 6], a[6, W - 7], a[H - 7, 6], a[H - 7, W - 7]]), axis=0)
    nonbg = np.abs(a - bg).sum(2) > 60
    R, iou = find_outline(nonbg, shape)
    if R is None or R.sum() < 500:
        Image.fromarray(a.astype('uint8')).save(dst); return f'no outline matched (best {iou:.2f}); unchanged'
    ry, rx = np.nonzero(R); mx0, my0, mx1, my1 = rx.min(), ry.min(), rx.max(), ry.max()
    # true proportions: widen (or heighten) a squeezed outline when open background allows it
    note_p = ''
    want = shape.shape[1] / shape.shape[0]; have = (mx1 - mx0) / max(my1 - my0, 1)
    if abs(have / want - 1) > 0.08:
        bw, bh = mx1 - mx0 + 1, my1 - my0 + 1
        nw, nh = (round(bh * want), bh) if have < want else (bw, round(bw / want))
        cx0 = round((mx0 + mx1) / 2 - nw / 2); cy0 = my1 + 1 - nh if nh > bh else my0
        box = np.zeros_like(R); box[max(cy0 - 12, 0):cy0 + nh + 12, max(cx0 - 12, 0):cx0 + nw + 12] = True
        if cx0 >= 0 and cy0 >= 0 and cx0 + nw <= W and not (nonbg & box & ~grow(R, 3)).any():
            layer = Image.fromarray(a[my0:my1 + 1, mx0:mx1 + 1].astype('uint8')).resize((nw, nh), Image.LANCZOS)
            lm = Image.fromarray((grow(R, 2)[my0:my1 + 1, mx0:mx1 + 1] * 255).astype('uint8')).resize((nw, nh), Image.LANCZOS)
            a[grow(R, 3)] = bg
            img = Image.fromarray(a.astype('uint8')); img.paste(layer, (cx0, cy0), lm)
            a = np.asarray(img).astype(int).copy(); nonbg = np.abs(a - bg).sum(2) > 60
            R, iou = find_outline(nonbg, shape)
            if R is None or R.sum() < 500:
                Image.fromarray(a.astype('uint8')).save(dst); return 'proportions fixed; outline lost after'
            ry, rx = np.nonzero(R); mx0, my0, mx1, my1 = rx.min(), ry.min(), rx.max(), ry.max()
            note_p = f', proportions {have / want:.2f} -> 1.00'
        else:
            note_p = f', proportions {have / want:.2f} (no room to fix)'
    mapc = np.median(a[shrink(R, 6)], axis=0)
    diff = np.abs(a - mapc).sum(2)
    S0 = shrink(R, 6) & (diff > 90); note = f'outline {iou:.2f}' + note_p
    if S0.any():
        best = None; left = S0.copy()
        while left.any():
            y, x = map(int, np.argwhere(left)[0]); c = flood(S0, [(y, x)]); left &= ~c
            if best is None or c.sum() > best.sum(): best = c
        S = grow(best, 4, R & (diff > 40))
        sy, sx = np.nonzero(S); r = max(sx.max() - sx.min(), sy.max() - sy.min()) / 2
        starc = np.median(a[best], axis=0)
        a[grow(S, 3, R)] = mapc
        cx = mx0 + capx * (mx1 - mx0); cy = my0 + capy * (my1 - my0)
        img = Image.fromarray(a.astype('uint8')); k = 4
        layer = Image.new('L', (W * k, H * k), 0)
        ImageDraw.Draw(layer).polygon([(cx * k + (r * k if i % 2 == 0 else r * k * 0.42) * math.cos(-math.pi / 2 + i * math.pi / 5),
                                        cy * k + (r * k if i % 2 == 0 else r * k * 0.42) * math.sin(-math.pi / 2 + i * math.pi / 5)) for i in range(10)], fill=255)
        img = Image.composite(Image.new('RGB', (W, H), tuple(int(v) for v in starc)), img, layer.resize((W, H), Image.LANCZOS))
        a = np.asarray(img).astype(int).copy(); note += f', star moved ({r:.0f}px)'
    else:
        note += ', no star found'
    nb = np.abs(a - bg).sum(2) > 60
    X0, X1 = max(mx0 - 20, 0), min(mx1 + 21, W)
    tt = next((y for y in range(my1 + 1, H - 12) if all(nb[y + j, X0:X1].sum() > 0.3 * (X1 - X0) for j in range(12))), None)
    if tt:
        while tt > my1 + 1 and nb[tt - 1, X0:X1].any(): tt -= 1
        rows = np.nonzero(nb[my1 + 1:tt, X0:X1].any(1))[0]
        gap = tt - (my1 + 1 + rows.max() if len(rows) else my1) - 1
        # type above the outline too (an arched headline): split the space instead of crowding it
        above = next((my0 - y - 1 for y in range(my0 - 1, 0, -1) if nb[y, X0:X1].any() and not R[y, X0:X1].any()), my0)
        room = above if above < my0 else my0 - round(0.06 * W)
        want = round(GAP * W) if above >= my0 else min(round(GAP * W), (above + gap) // 2)
        shift = min(max(0, want - gap), max(0, room - want if above < my0 else room))
        if shift:
            band = a[my0 - 10:tt, X0:X1].copy(); a[my0 - 10 - shift:tt - shift, X0:X1] = band; a[tt - shift:tt, X0:X1] = bg
        note += f', gap {gap}px +{shift}' + (f' (type above, {above}px)' if above < my0 else '')
    Image.fromarray(a.astype('uint8')).save(dst)
    return note


def main(abbr, out, files):
    out = Path(out); (out / 'fixed').mkdir(parents=True, exist_ok=True)
    shape, capx, capy = shape_mask(abbr.upper())
    k = abbr.lower(); finals = []
    for i, f in enumerate(files, 1):
        fx = out / 'fixed' / f'{k}-{i:02d}.png'
        print(f'{i:2d} {Path(f).name}: {fix(f, fx, abbr.upper(), shape, capx, capy)}')
        jpg = out / f'{k}-vote-{i}.jpg'
        subprocess.run([sys.executable, '-I', str(ROOT / 'build/fit_to_circle.py'), str(fx), str(jpg)], check=True, capture_output=True)
        finals.append(jpg)
    T = 360; cols = 5; rows = (len(finals) + cols - 1) // cols
    sheet = Image.new('RGB', (cols * (T + 70), rows * (T + 20)), 'white')
    mask = Image.new('L', (T, T), 0); ImageDraw.Draw(mask).ellipse((0, 0, T - 1, T - 1), fill=255)
    m48 = Image.new('L', (48, 48), 0); ImageDraw.Draw(m48).ellipse((0, 0, 47, 47), fill=255)
    for i, f in enumerate(finals):
        im = Image.open(f); x = (i % cols) * (T + 70); y = (i // cols) * (T + 20)
        sheet.paste(im.resize((T, T), Image.LANCZOS), (x + 5, y + 10), mask)
        sheet.paste(im.resize((48, 48), Image.LANCZOS), (x + T + 12, y + T - 40), m48)
    sheet.save(out / 'sheet.png'); print('sheet', out / 'sheet.png')


if __name__ == '__main__':
    main(sys.argv[1], sys.argv[2], sys.argv[3:])
