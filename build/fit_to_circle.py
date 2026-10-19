"""
Fit a hand-made square image inside the profile-picture circle and save it for the site.

Finds the farthest pixel that differs from the background (sampled from the four corners), then
scales the whole image down about its center, padding with the background color, so that pixel
sits inside SAFE x the half-width. Images already inside are only resized. Writes a 1080 px JPG.

Run: python3 -I fit_to_circle.py <source.png> <dest.jpg>   (prints the scale applied)
"""
import sys
from PIL import Image

SAFE = 0.46     # content radius as a share of the image width (crop circle is 0.5)
OUT = 1080
QUALITY = 92


def main(src, dest):
    im = Image.open(src).convert('RGB')
    w, h = im.size
    px = im.load()
    corners = [px[6, 6], px[w - 7, 6], px[6, h - 7], px[w - 7, h - 7]]
    bg = tuple(sorted(c[k] for c in corners)[1] for k in range(3))
    cx, cy, far = w / 2, h / 2, 0.0
    for y in range(0, h, 2):
        for x in range(0, w, 2):
            p = px[x, y]
            if abs(p[0] - bg[0]) + abs(p[1] - bg[1]) + abs(p[2] - bg[2]) > 60:
                d = ((x - cx) ** 2 + (y - cy) ** 2) ** 0.5
                if d > far:
                    far = d
    scale = min(1.0, SAFE * w / far) if far else 1.0
    if scale < 1.0:
        canvas = Image.new('RGB', (w, h), bg)
        inner = im.resize((round(w * scale), round(h * scale)), Image.LANCZOS)
        canvas.paste(inner, ((w - inner.width) // 2, (h - inner.height) // 2))
        im = canvas
    im.resize((OUT, OUT), Image.LANCZOS).save(dest, 'JPEG', quality=QUALITY, optimize=True, progressive=True)
    print(f'{dest.split("/")[-1]} scale={scale:.3f}')


if __name__ == '__main__':
    main(sys.argv[1], sys.argv[2])
