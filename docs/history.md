# History and Build Notes

The working notes from launch day, October 19, 2026, kept for the record. Paths that say `/vote/` are
from the first home at garrettn.com/vote/, which now redirects to votesticker.org.

Started October 19, 2026, the day Texas early voting opened. Pick a state, pick one of 18 designs
(3 layouts by 6 palettes) and a headline, and save a 1080 px PNG. Texas also offers Garrett's 9
ChatGPT images as ready-made downloads. Modeled on the Softball Austin card maker
(`clients/softballaustin/docs/card-maker/`), but standalone: no shared code.

## Files

| Path | What |
|---|---|
| `site/` | Everything that goes in the server's `vote/` folder |
| `site/index.php` | The page. PHP sends `no-cache` and versions asset URLs by modified time (the Redis cache still stores the HTML; purge after deploys) |
| `site/app.js` | Layouts, drawing, saving |
| `site/states.js` | One row per state plus DC. **The only file to edit for dates** |
| `site/state-shapes.js` | Generated outlines; do not edit |
| `site/assets/fonts/` | Anton (headline) and Archivo Black (date line), both SIL Open Font License |
| `site/assets/states/<abbr>/` | Ready-made images per state (`tx`, `ks`, `mo`, `wa`, `ny`, `ca`, `co`, `va`, `pa`, `dc`, `nc`), 1080 px JPG. Any folder here shows as "Ready-Made [State] Designs" above the customizer for that state; index.php lists them, no code change |
| `site/assets/icons/` | Favicon set (V with a red star on navy), from `build/favicon.mjs` + `build/favicon_sizes.py` |
| `site/assets/og-vote.jpg` | 1200 x 630 link preview (headline + one ready-made design each from TX, KS, MO, WA), from `build/og_image.mjs`; also the photo and featured image of the garrettn.com post. Rename the file when it changes, so Facebook fetches the new one |
| `build/fit_to_circle.py` | Shrinks a hand-made image until everything is inside the crop circle, pads with its background, writes the 1080 JPG. Run on every new ChatGPT image |
| `chatgpt-image-prompts.md` | Setup and per-state prompts for the ready-made sets |
| `build/build_shapes.py` | Rebuilds `state-shapes.js` from `build/us-atlas-states-10m.json` (US Census boundaries via us-atlas 3.0.1, public domain) |
| `perplexity-prompts.md` | Prompts for the state dates (round 1 and round 2) |
| `build/verified-*.json` | What each official page said when a row was verified in Chrome |
| `social-posts.md` | Launch posts by platform |

## Rules

- **Only verified rows show dates.** A row with `"status": "unchecked"` makes cards that say
  "Election Day Nov 3" and links to Vote411. Mark a row verified only after reading the official
  page (Chrome, not Firecrawl), and fill `sourceUrl` and `checked`.
- **Nothing is cropped.** All content stays inside a 500 px radius of the center of the 1080 px
  canvas; the circle crop on Facebook, Instagram, X and LinkedIn is 540. The badge ring sits at 506.
  The QA renders every state, design, palette and headline and fails on any pixel outside.
- **Messages stay short and true for the state** (Garrett, October 19). Every headline is two short
  lines so it reads at 40 to 60 px. Defaults: VOTE EARLY + dates where early voting is verified,
  VOTE BY MAIL + DUE NOV 3 in all-mail states, VOTE NOV 3 + ELECTION DAY in states with no early
  voting (AL, NH, MS) and in unchecked states. "Early" headlines are never offered where early
  voting isn't verified (the QA fails if they are). Big type is the default design; it reads best small.
- No site address on the images or in the suggested post text.
- Nothing is uploaded; the picture is drawn in the browser.

## QA

```
cd clients/hobby-sites/garrettn/docs/vote-cards/site && php -S 127.0.0.1:8790
node scripts/garrettn_vote_cards_qa.mjs        # from the repo root; Chromium and WebKit
```

Passes October 19, 2026, local and live: 4,302 cards per browser (each state with only its own headlines), none outside the safe circle, no page errors,
no sideways scroll at 390 px.

## Deploy (garrettn.com Era)

October 19, 2026 only: the tool first ran as PHP in a folder on garrettn.com, uploaded through the
WordPress site. It moved to votesticker.org on Cloudflare Pages the same evening; see the README.

## Open

- 30 rows verified October 19: 29 states and DC (AL, NH, MS as no early voting). Still unchecked: AZ, AR, CT, GA, IL,
  IN, KY, LA, ME, MA, MT, NM, ND, OH, SD, TN, UT, VT, WV, WI, WY. Perplexity gave dates for several (AZ, CT, GA, IN, TN,
  WV, WI, VT, UT, OH, ME) that the official pages reachable in Chrome did not show; they stay on
  Election Day until a page does.
- Check the Vote411 link pattern for multi-word states (`vote411.org/new-york`) and DC.
- An `og:image` for link previews (1200 x 630).
- Possible: a "your photo plus a badge" option.

## Ready-Made Images

Adding a state: review the ChatGPT images (spelling, en dash, outline, capital star, near-duplicates),
run `build/fit_to_circle.py` on each into `site/assets/states/<abbr>/<abbr>-vote-N.jpg`, deploy, purge.
Then **move** the source PNGs to `../../content/vote-cards/originals/<abbr>/<abbr>-vote-N.png` (same
number as the site file) and delete the Downloads folder: Garrett wants one copy on the Mac. October 19: Texas 9 (7 of Garrett's originals had their date line outside the crop
circle; all refit), Kansas 10 of 11 (`kansas5` dropped as a near-duplicate of `kansas`). Missouri 10 of 10 (`05` and `08`
refit; in `01`, `04`, `06`, `09` the star sits near St. Louis, not Jefferson City, kept anyway).
Washington 10 of 10 (`09` refit most, 0.92). New York 7 of 10: `01`, `06`, `09` trashed for a
distorted state outline; four date bars refit.

Round 2, October 19 (42 of 60 kept). **Reject any design that sets the headline inside the state
outline**: ChatGPT squeezes the letters to fit and often stretches the outline too (NC's came out
taller than the real state). Trashed for that: CA, VA, NC `01`, `04`, `07`, `09`; CO `05`, `08`; PA `08`.
Also trashed: CO `01` and PA `01`, `05`, where the capital star cut into a letter ("VOTTE", "EARI.Y").
Result: CO 7, CA 6, VA 6, PA 7, DC 10, NC 6. Rejects are in `~/.Trash/vote-rejects-20261019/`.

## Pages, Brand and Metadata (October 19, evening)

The tool is branded **Vote Circle** (`VOTE_BRAND` in `index.php`: header lockup, breadcrumbs, `og:site_name`,
footer). The footer's only link out is the Garrett Digital logo (`site/assets/gd-logo.svg`, copied from
`clients/garrettdigital/content/source-medium-channel-campaign/`); Garrett asked for no links to the
WordPress site, and the SEO QA fails on any link to the root.

**URLs.** Hub at `/vote/`, one page per state at `/vote/<state-name>/` (`/vote/district-of-columbia/` for DC).
Each state folder holds a two-line `index.php` stub that sets `VOTE_SLUG` and loads the shared
`index.php`; `build/state_pages.py` writes the 51 stubs and `site/sitemap.xml` from `states.js`. Old
`?state=xx` links 301 to the state page (unknown states to the hub); unknown paths 404. Run
`state_pages.py` after any `states.js` change, then deploy the folders and sitemap.

**Each state page is rendered by PHP** (title, description, self canonical, H1, lead, the state panel,
the ready-made gallery, breadcrumbs with BreadcrumbList JSON-LD, its own share image) so it works and
indexes without JS; `app.js` draws the same panel for the maker. The state menu navigates to the
state's page. The hub shows the showcase (`$vote_showcase`: one design each for CA, TX, NY, PA, NC, VA,
WA, CO), How It Works, and the all-states list.

**Metadata formula** (`vote_meta()`): title "[State] Vote Profile Pictures: [Early Voting Oct 19–30 |
Early Voting Through Oct 27 | Vote by Mail, Due Nov 3 | Vote on Election Day, Nov 3 | Election Day Nov 3]";
H1 "[State] Vote Profile Pictures"; lead and description from the same fact plus the count of ready-made
designs. Unchecked states render `noindex, follow` and stay out of the sitemap until verified.

**Internal links on a state page:** same-rule states (other mail states, other no-early states, or
states whose early voting opens the same day), bordering states with each one's status
(`VOTE_NEIGHBORS`, both directions checked by QA), previous/next in list order, and all states with the
current one unlinked.

**Share.** `build/og_states.mjs` writes `site/assets/og/<slug>.jpg` (1200 x 630: state name, the title's
fact, three designs in circles; ready-made where the state has 3+, else drawn by the tool). Rerun after
new sets or date changes. Every page has a "Copy link" button in the header (share sheet on phones,
clipboard elsewhere).

**QA.** `scripts/garrettn_vote_seo_qa.py [--base]` checks all 52 pages: status, unique titles and
descriptions with length limits, one H1, canonical, noindex rules, breadcrumb JSON-LD, no `?state=` links,
no root links, every internal link 200, neighbor links both ways, the previous/next chain, legacy
redirects, 404 on unknown paths, per-page `og:image` loads, copy-link button, GD logo link, and the
sitemap equals the indexable pages. `scripts/garrettn_vote_cards_qa.mjs` also loads every state page
with and without `app.js` and fails if the server panel differs from the JS one. Serve locally with a
folder whose `vote/` symlinks to `site/`: `php -S 127.0.0.1:8791 -t <dir>`.

Still to do: submit `https://www.garrettn.com/vote/sitemap.xml` in Search Console (the WordPress
robots.txt and Rank Math sitemap don't know about `/vote/`).

## garrettn.com Post

Post 11773, "Make a Vote Profile Picture for Your State", published by Garrett October 19, 2026 at
https://www.garrettn.com/2026/10/19/vote-profile-picture/ (his edit lists TX, KS, MO, NY, WA, CA "and
other states" as having extra designs; NY and CA sets were still being made). Photo and featured image
are media 11775 (`og-vote.jpg`), linked to /vote/. Media 11772 (the first, generator-made version) is
unused. Built from `build/post-20261019.php`.
