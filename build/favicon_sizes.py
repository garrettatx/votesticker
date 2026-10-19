"""Writes the VoteSticker.org icon set from the 512 px master made by favicon.mjs.
Run: python3 -I favicon_sizes.py <master.png> <site/assets/icons/>"""
import sys
from PIL import Image

src, out = sys.argv[1], sys.argv[2].rstrip('/')
m = Image.open(src).convert('RGBA')
m.save(f'{out}/favicon.ico', sizes=[(16, 16), (32, 32), (48, 48)])
m.resize((32, 32), Image.LANCZOS).save(f'{out}/favicon-32.png', optimize=True)
m.resize((192, 192), Image.LANCZOS).save(f'{out}/icon-192.png', optimize=True)
m.save(f'{out}/icon-512.png', optimize=True)
# iOS draws its own rounded corners and shows transparency as black: fill the square with navy.
flat = Image.new('RGBA', m.size, (20, 41, 94, 255))
flat.alpha_composite(m)
flat.convert('RGB').resize((180, 180), Image.LANCZOS).save(f'{out}/apple-touch-icon.png', optimize=True)
