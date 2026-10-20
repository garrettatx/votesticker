"""
Fit a hand-made square image inside the profile-picture circle and save it for the site.

Finds the background from the image's border: one flat color, or a field split into two colors along a
straight line (October 20, Minnesota's diagonal). Lifts the artwork off that background, centers its
bounding box, scales it down about the center until its farthest pixel sits inside SAFE x the
half-width, and lays it back on the same background, so a split field keeps its line with no seam.
Images already inside are only centered and resized. Writes a 1080 px JPG.

Run: python3 -I fit_to_circle.py <source.png> <dest.jpg>   (prints the scale applied)
"""
import sys
from collections import Counter
import numpy as np
from PIL import Image

SAFE = 0.46     # content radius as a share of the image width (crop circle is 0.5)
OUT = 1080
QUALITY = 92
TOL = 60        # summed RGB difference that still counts as background


def background(a):
    """Return (bg image, list of bg colors) built from the border pixels."""
    h, w, _ = a.shape
    edge = [(0, x) for x in range(0, w, 2)] + [(y, w - 1) for y in range(0, h, 2)] + \
           [(h - 1, x) for x in range(w - 1, -1, -2)] + [(y, 0) for y in range(h - 1, -1, -2)]
    q = Counter(tuple(int(v) // 24 for v in a[y, x]) for y, x in edge)
    picks = [k for k, n in q.most_common(2) if n > 0.15 * len(edge)]
    cols = []
    for k in picks:
        sel = np.array([a[y, x] for y, x in edge if tuple(int(v) // 24 for v in a[y, x]) == k])
        cols.append(np.median(sel, axis=0))
    if len(cols) < 2 or np.abs(cols[0] - cols[1]).sum() < TOL:
        return np.broadcast_to(cols[0], a.shape).copy(), cols[:1]
    # two-color field: the dividing line runs between the two places the border changes color
    lab = [int(np.abs(a[y, x] - cols[0]).sum() > np.abs(a[y, x] - cols[1]).sum()) for y, x in edge]
    flips = [i for i in range(len(lab)) if lab[i] != lab[i - 1]]
    if len(flips) < 2:
        return np.broadcast_to(cols[0], a.shape).copy(), cols[:1]
    # the two longest runs bound the halves; take the flips that start them
    runs = sorted(((flips[(j + 1) % len(flips)] - f) % len(lab), f) for j, f in enumerate(flips))[-2:]
    (y1, x1), (y2, x2) = edge[runs[0][1]], edge[runs[1][1]]
    yy, xx = np.mgrid[0:h, 0:w]
    side = ((x2 - x1) * (yy - y1) - (y2 - y1) * (xx - x1)) > 0
    ref = edge[(runs[0][1] + runs[0][0] // 2) % len(lab)]
    ref_side = ((x2 - x1) * (ref[0] - y1) - (y2 - y1) * (ref[1] - x1)) > 0
    first = lab[(runs[0][1] + runs[0][0] // 2) % len(lab)]
    bg = np.where((side == ref_side)[..., None], cols[first], cols[1 - first])
    return bg, cols


def main(src, dest):
    a = np.asarray(Image.open(src).convert('RGB')).astype(float)
    h, w, _ = a.shape
    bg, cols = background(a)
    art = np.abs(a - bg).sum(2) > TOL
    if len(cols) > 1:   # ignore the antialiased seam of the split itself
        art &= np.min([np.abs(a - c).sum(2) for c in cols], axis=0) > TOL
    grown = art.copy()
    for _ in range(2):  # keep the artwork's antialiased edge
        grown = grown | np.roll(grown, 1, 0) | np.roll(grown, -1, 0) | np.roll(grown, 1, 1) | np.roll(grown, -1, 1)
    ys, xs = np.nonzero(art)
    scale, dx, dy = 1.0, 0, 0
    if len(xs):
        dx = w / 2 - (xs.min() + xs.max()) / 2
        dy = h / 2 - (ys.min() + ys.max()) / 2
        if abs(dx) <= w * 0.01 and abs(dy) <= h * 0.01:
            dx = dy = 0
        far = np.sqrt((xs + dx - w / 2) ** 2 + (ys + dy - h / 2) ** 2).max()
        scale = min(1.0, SAFE * w / far)
    out = Image.fromarray(bg.astype('uint8'))
    if len(xs):
        layer = Image.fromarray(a.astype('uint8'))
        mask = Image.fromarray((grown * 255).astype('uint8'))
        sw, sh = round(w * scale), round(h * scale)
        layer, mask = layer.resize((sw, sh), Image.LANCZOS), mask.resize((sw, sh), Image.LANCZOS)
        out.paste(layer, (round((w - sw) / 2 + dx * scale), round((h - sh) / 2 + dy * scale)), mask)
    out.resize((OUT, OUT), Image.LANCZOS).save(dest, 'JPEG', quality=QUALITY, optimize=True, progressive=True)
    print(f'{dest.split("/")[-1]} scale={scale:.3f}' + (' split field' if len(cols) > 1 else ''))


if __name__ == '__main__':
    main(sys.argv[1], sys.argv[2])
