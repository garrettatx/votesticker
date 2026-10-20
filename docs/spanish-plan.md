# Spanish Version Plan (Not Yet Approved)

Written October 19, 2026. Election Day is November 3, so the plan favors one build pass over a
staged MVP.

## Decision Summary

- **URLs.** English stays at the root. Spanish lives at `/es/` and `/es/<state>/` with the same English
  slugs (`/es/texas/`). Every page links to its pair, and `hreflang` (en, es, x-default = English)
  connects them.
- **All 52 pages at once, not a Texas MVP.** Every page comes from one template and `states.js`, so a
  Spanish Texas page costs the same as all 51. A Texas-only pilot adds a second pass for no saving.
- **One template, a language switch.** `site/index.php` takes a language. Strings live in
  `site/i18n/en.php` and `site/i18n/es.php`. The maker's strings print from the same arrays into the
  page as JSON, so there is one source per language.
- **Cut the duplicated JavaScript text first.** `app.js` rebuilds the state panel and the post text that
  PHP already renders, and QA checks the two match. State pages are one state per page now, so the JS
  copies can go. That halves what has to be translated and removes a whole class of mismatch bugs.
- **Neutral U.S./Latin American Spanish**, tú form, with a locked glossary in
  `docs/spanish-style.md` before any page copy is written.
- **A native Spanish speaker reviews before Google indexes it.** The pages state voting rules and
  deadlines. Spanish pages ship with `noindex` and are left out of the sitemap until the reviewer signs
  off, then both flip in one deploy.

## What Gets Translated

| Item | Size |
|---|---|
| Page and maker interface strings | about 90 short strings |
| State facts (`note` and `summary` in `states.js`, 30 verified states) | 537 words |
| Title, description, lead, and ribbon formulas | 5 families, written once |
| FAQ, trust copy, footer, 404 | about 300 words |

Total: about 2,000 words. The state facts get `note_es` and `summary_es` fields next to the English
ones, so a date change and its translation sit on the same row.

## Glossary Decisions to Lock First

| English | Sticker text | Body copy | Note |
|---|---|---|---|
| Vote early / early voting | VOTA TEMPRANO | votación anticipada | |
| Vote by mail | VOTA POR CORREO | votar por correo | |
| Election Day | DÍA DE ELECCIONES | el Día de las Elecciones | Matches U.S. election office usage |
| Absentee ballot | | boleta de voto en ausencia | Check each state's own Spanish term where it has one |
| Polling place | | lugar de votación | |
| Profile picture | | foto de perfil | |
| Sticker | | sticker | Common in U.S. Spanish and matches the brand. Reviewer to confirm over "calcomanía" |
| Official voting information | | información oficial para votar | |

## Build Steps

1. **Style guide and glossary** (`docs/spanish-style.md`), from the research brief plus the table above.
2. **Refactor for language** in `index.php`, `render.php`, `build_static.py`, `state_pages.py`:
   strings out to `i18n/`, a `VOTE_LANG` constant, Spanish date formatting ("del 19 al 30 de
   octubre"), `/es/` stubs and output folders.
3. **Remove the JS panel and post-text copies** from `app.js`; the maker reads server text.
4. **Head tags:** `lang`, self canonical, reciprocal hreflang, `og:locale` (en_US, es_US), Spanish
   titles and descriptions, `inLanguage` in the JSON-LD, Spanish breadcrumbs ("Inicio").
5. **Spanish share cards** at `assets/og/es/<slug>.jpg` from `og_cards.mjs` with a language flag
   (English files keep their current names, so live shares don't break).
6. **Sitemap** with `xhtml:link` alternates for each pair; `robots.txt` unchanged.
7. **Language switch:** "Español" or "English" in the header, to the paired page. On the English
   homepage only, a dismissible "¿Prefieres español?" bar when the browser prefers Spanish. No
   redirects. A click saves the choice in the browser.
8. **Maker in Spanish:** labels, buttons, tips, file names, alt text, and the Spanish headlines first in
   the menu on Spanish pages. Ready-made sets are English images; Spanish pages show them under
   "Diseños en inglés" until Spanish sets exist.
9. **Official links:** where a state's election site has a Spanish page, link it; otherwise label the
   link "(en inglés)". This takes one Chrome pass over the 30 verified states.
10. **QA:** the SEO script learns pairs (hreflang both ways, self canonicals, `lang`, no English strings
    on Spanish pages); the card and device scripts run on both languages; screenshots at phone and
    desktop for the hub and three state families.

## Needs From Garrett

1. **Who reviews the Spanish.** About an hour of a native speaker's time, ideally someone from the
   audience (Mexican American or broader U.S. Latino). Without a reviewer, the pages stay `noindex`.
2. **"Sticker" or "calcomanía"** in body copy, if the reviewer has a view.
3. **Spanish ready-made sets:** yes or later. The ChatGPT prompts can take Spanish headlines with no
   other change.

## What I Left Out of the Research

- The 10-prompt sequence: the architecture is already known, so one build pass replaces prompts 1 to 9.
- Renaming English share images to `-en`: it breaks every existing share for no gain.
- A `FAQPage` schema block: Google no longer shows FAQ rich results for sites like this.
- Regional variants (`es-MX`, `es-419`): `hreflang` doesn't take `es-419`, and one neutral Spanish
  serves the audience.
