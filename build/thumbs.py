"""
Write a 360px WebP thumbnail for every sticker in site/assets/states/<abbr>/ to site/assets/thumbs/<abbr>/
(the All Stickers page, /stickers/, November 2). The page shows the thumbnail and a tap still saves the full
1080px JPG, so a visitor scrolling every state downloads about 13 MB, not 84.

    python3 build/thumbs.py          # new or changed stickers only (needs cwebp: brew install webp)
    python3 build/thumbs.py --check  # exit 1 if any sticker has no thumbnail, or a thumbnail has no sticker

The thumbnails are committed; the CI build doesn't make them, and index.php falls back to the JPG when one is
missing, so a sticker added without running this still shows (and scripts/qa_stickers.mjs flags it).
"""
import subprocess, sys
from pathlib import Path
SITE = Path(__file__).resolve().parent.parent / 'site'
SRC, OUT = SITE / 'assets' / 'states', SITE / 'assets' / 'thumbs'
jpgs = sorted(SRC.glob('*/*.jpg'))
want = {OUT / j.parent.name / (j.stem + '.webp'): j for j in jpgs}
if '--check' in sys.argv:
    missing = [str(t.relative_to(SITE)) for t in want if not t.exists()]
    extra = [str(t.relative_to(SITE)) for t in OUT.glob('*/*.webp') if t not in want]
    for m in missing: print('missing thumbnail', m)
    for e in extra: print('thumbnail with no sticker', e)
    print(f'{len(want)} stickers, {len(missing)} missing, {len(extra)} orphaned')
    sys.exit(1 if missing or extra else 0)
made = 0
for t, j in want.items():
    if t.exists() and t.stat().st_mtime >= j.stat().st_mtime:
        continue
    t.parent.mkdir(parents=True, exist_ok=True)
    subprocess.run(['cwebp', '-quiet', '-q', '72', '-resize', '360', '0', str(j), '-o', str(t)], check=True)
    made += 1
for t in OUT.glob('*/*.webp'):
    if t not in want:
        t.unlink()
print(f'{made} thumbnails written, {len(want)} stickers')
