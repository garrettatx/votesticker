# VoteSticker.org

Free, nonpartisan profile pictures that show your state's voting dates for the November 3, 2026
election. Pick your state, save a ready-made design or make your own, and post it. Live at
**https://votesticker.org**.

Made by Garrett Nafzinger of [Garrett Digital](https://www.garrettdigital.com/).

## What It Does

- **One page per state** (`/texas/`, `/district-of-columbia/`) with that state's early voting,
  vote-by-mail, or Election Day dates and a link to the official election office.
- **Ready-made designs** for the most populous states, plus a maker with 18 designs (3 layouts by 6
  palettes) and short headlines that are true for the state ("VOTE EARLY" only where early voting is
  confirmed).
- **Nothing gets cropped.** Every picture keeps its content inside the circle that Facebook, Instagram,
  X, and LinkedIn crop profile pictures to. QA renders all 4,302 combinations and fails on any pixel
  outside it.
- **Private by design.** Pictures are drawn in the browser with canvas. Nothing is uploaded.

## Rules the Content Follows

- A state shows dates only after they were read on an official state page, with the source URL and
  check date stored in `site/states.js`. Unchecked states say Election Day, render with `noindex`, and
  stay out of the sitemap.
- Messages stay short: two-line headlines that read at 40 to 60 px.
- No site address on the images, no party symbols, no red-versus-blue themes.

## Layout

| Path | What |
|---|---|
| `site/states.js` | One row per state plus DC: dates, summary, official link, source, check date. **The only file to edit for dates** |
| `site/index.php` | Page template (hub and state pages): titles, descriptions, canonical, breadcrumbs, state panel, internal links |
| `site/app.js`, `site/app.css` | The maker: layouts, drawing, saving, sharing |
| `site/state-shapes.js` | Generated state outlines (US Census boundaries via us-atlas 3.0.1, public domain) |
| `site/assets/` | Fonts (Anton, Archivo Black; SIL Open Font License), icons, brand mark, share images, ready-made designs |
| `site/<state>/index.php` | Two-line stubs that render that state locally |
| `build/` | Static build, state stubs, share images, favicon, outline and crop-fit tools |
| `scripts/` | QA: `qa_cards.mjs` (every card in Chromium and WebKit, plus server-vs-browser panel parity) and `qa_seo.py` (52 pages: metadata, canonicals, links, sitemap, redirects) |
| `docs/` | Image prompts, date research prompts, launch posts, history |

## Build and Deploy

The live site is static HTML on Cloudflare Pages. PHP runs only at build time.

```
python3 -I build/state_pages.py        # after any states.js change: state folder stubs
node build/og_cards.mjs                 # after new designs or dates: 51 state share cards + the hub card (needs php -S 127.0.0.1:8791 -t site)
python3 -I build/build_static.py        # renders 52 pages into dist/, writes sitemap.xml and robots.txt
python3 -m http.server 8792 -d dist     # then QA locally:
python3 -I scripts/qa_seo.py --base http://127.0.0.1:8792/ --no-legacy
node scripts/qa_cards.mjs --base http://127.0.0.1:8792/
wrangler pages deploy dist --project-name votesticker --branch main
python3 -I scripts/qa_seo.py            # live, including the old garrettn.com/vote/ redirects
python3 -I scripts/qa_es.py             # Spanish pairs
node scripts/qa_devices.mjs --base https://votesticker.org/   # iPhone, iPad, Android, both languages
python3 -I scripts/indexnow.py          # tell Bing and other IndexNow engines which pages changed
```

IndexNow: two key files deploy to the site root, `site/f691b1b3d7674b378f82c72b1b27f5d8.txt` (this script, Bing) and `site/4fhy7yhf7perp8cdybzgtndafpuwd2wr.txt` (Ahrefs Site Audit auto-submission). The script
submits only URLs whose sitemap `<lastmod>` changed since the last run (`build/indexnow-sent.json`,
commit it too). `--dry-run` lists them, `--all` resends everything.

## Analytics and Verification

- **GA4** `G-PZWZTXDSDE` and **Ahrefs Web Analytics** (`analytics.ahrefs.com`) load from the template head.
- **Bing Webmaster Tools:** verified by the DNS-only CNAME `41b5c37d4b0f8e8d036bdff704183665` →
  `verify.bing.com`, plus the `msvalidate.01` meta on the English home page.
- **Ahrefs Bot Analytics** (connected October 20, 2026): Cloudflare Worker method, which works on the
  Free plan (Logpush needs Enterprise). Ahrefs deployed the worker
  `ahrefs--bot-analytics--c6a56ff243ba3a00f3280a31c64aff5e` with the zone route `*votesticker.org/*`,
  `request_limit_fail_open: true`. Every request runs the worker and counts toward the account's
  100,000 Workers requests a day; past that the site still serves and only bot data pauses. The setup
  token is Garrett's and is never stored in this repo. To remove: delete the route, then the worker.

`build/lastmod.json` keeps each page's content hash and the date it last changed, so the sitemap's
`<lastmod>` moves only when a page really changes. Commit it with each build.

## Spanish (/es/)

Every page has a Spanish pair at `/es/<state>/` with the same English slug. One template, one
dictionary: `site/i18n/es.php` holds the Spanish strings (keyed by the English), Spanish state names,
and the 30 state notes. Register, glossary, and reviewer questions: `docs/spanish-style.md`.

- Spanish pages show no ready-made sets (they are English images); they open on the maker, which
  defaults to Spanish messages. Header and showcase art are Spanish stickers drawn by `app.js`.
- Language switch on every page goes to the exact pair; the English home page suggests Spanish once to
  browsers that prefer it, and an explicit choice is remembered. Nothing redirects.
- **Indexing:** `VOTE_ES_INDEX` in `site/index.php` is `true` (Garrett turned it on October 19; native review to follow). Set it `false` to hide Spanish again:
  Spanish pages are then `noindex`, out of the sitemap, and no page carries hreflang. Flip it, rebuild,
  run QA, deploy.
- Spanish share cards: `node build/og_cards.mjs http://127.0.0.1:8791/ es`.
- QA: `python3 -I scripts/qa_es.py [--base]` checks every pair (lang, canonical, hreflang gate, switch,
  links staying in /es/, leftover English, structural parity, sitemap).

## Adding Ready-Made Designs

Review each image (spelling, en dash in ranges, accurate outline at true proportions, capital star in
the right place and clear of the letters, no headline inside the outline), then run
`build/fit_to_circle.py` on it into `site/assets/states/<abbr>/<abbr>-vote-N.jpg`. Any state folder there
shows as a gallery on that state's page with no code change. Prompts are in
`docs/round3-prompts-one-state.md`.

## Brand

Vote Sticker, "Make your plan. Share the sticker." Direction A, Civic Print Shop, chosen October 19, 2026
from three mockups. It takes its look from the "I Voted" sticker: a cream sticker with a peeled corner
and the capital star, in the navy (#14295E), red (#C1272D), cream (#FBF6E8), and gold (#F2C14E) of the
most popular designs. Anton for display type, a red ribbon for each state's key fact. The mark is
`site/assets/mark.svg`.

State pages fall into families by how the state votes (mail, early, Election Day only, not yet checked).
Each family has its own lead, ribbon, and related-states heading (`vote_meta()` in `site/index.php`).
The state finder is a type-ahead box (names or postal codes); the full list of state links below it is
the no-JavaScript path.

## License

Code: MIT. The ready-made images and the brand mark are not licensed for reuse.
