# Spanish Style and Glossary (votesticker.org/es/)

Set October 19, 2026. Strings live in `site/i18n/es.php`; the template and maker read them from there.
Built with the global `es-mx-localization` skill. This file is the project override the skill defers to.

## Locale and Register

- **Audience:** Mexican, Mexican-American, and broader U.S. Latino voters. es-MX vocabulary, readable
  across Latin America. Never Spain Spanish (no vosotros, móvil, ordenador, vale).
- **Register: tú, everywhere.** Garrett's project glossary (October 19) chose tú for this casual,
  social tool, and it matches the sticker text people post ("VOTA TEMPRANO", "YA VOTÉ"). The skill's
  usted default yields to it. The skill's `check-es.mjs` will flag "tú forms in an usted document";
  for this project that error is expected.
- **Buttons:** infinitives where natural ("Buscar", "Descargar imagen", "Copiar enlace").
- **Headings:** Spanish sentence case ("Stickers para votar en Texas"), not English Title Case.
- **Official titles:** Spanish rendering with the English in brackets when a reader may need to match
  it on an English-only site: "Supervisor de Elecciones (Supervisor of Elections)", "la junta electoral
  de tu condado (county board of elections)".

## Glossary

| English | Sticker text | Body copy |
|---|---|---|
| Vote early / early voting | VOTA TEMPRANO | votación anticipada; votar por anticipado |
| Vote by mail | VOTA POR CORREO | votar por correo; boleta por correo |
| Election Day | DÍA DE LAS ELECCIONES | el Día de las Elecciones |
| Vote Nov 3 | VOTA 3 DE NOV | el martes 3 de noviembre |
| I voted | YO VOTÉ (maker and classic set, since Oct 31; the bespoke state images still read YA VOTÉ until re-rendered) | Yo voté |
| I'm voting | VOY A VOTAR | |
| Absentee ballot | | boleta de voto en ausencia; votar en ausencia |
| Polling place | | lugar de votación (centro de votación where the office calls it a vote center) |
| When the polls close | | cuando cierra la votación; antes de que cierre la votación; los lugares de votación cierran a las 7 p. m. (not "casillas", Oct 31) |
| Early voting center | | centro de votación anticipada |
| Ballot drop box | | buzón de entrega de boletas (then: buzón de entrega) |
| Register / registration | | registrarte; registro de votante |
| Election office | | oficina electoral (de tu estado, de tu condado, local) |
| Secretary of State | | Secretaría de Estado (Secretary of State) |
| Official voting information | | información oficial para votar |
| Nonpartisan | | no partidista |
| Ready-made stickers | | stickers listos para usar |
| Profile picture | | foto de perfil |
| Copy link | | Copiar enlace |
| Suggested post text | | Texto sugerido para publicar |
| Browse all states | | Explora todos los estados |

Dates: day first, lowercase months. Short: "19–30 oct", "24 oct–1 nov". Long: "del 19 al 30 de
octubre", "el martes 3 de noviembre". Times: "7 p. m.", "8:30 a. m.".

## State Names

Every state keeps its English name on Spanish pages: North Carolina, South Carolina, West Virginia,
Mississippi, New York, Washington, DC (Garrett, October 19). Voters see and search them that way on
ballots and official sites. URLs keep English slugs (`/es/new-york/`).

## What Spanish Pages Leave Out on Purpose

- **Ready-made sets.** They are English images (Garrett, October 19). Spanish pages open on the maker,
  which draws Spanish stickers; header and showcase art are drawn in Spanish by `app.js`.
- **The "¿Prefieres español?" bar** appears only on the English home page.

## Release Gate

`VOTE_ES_INDEX` in `site/index.php` was turned on by Garrett on October 19, ahead of the native review. Set it `false` to pull Spanish from search again. While false,
Spanish pages are `noindex`, out of the sitemap, and no page carries hreflang. Flip it, rebuild, run
`scripts/qa_es.py`, deploy.

## Open Questions for the Reviewer

1. **tú or usted** for the site's own voice? (Stickers stay tú either way.)
2. **"sticker" or "calcomanía"** in body copy?
3. **"Ya voté" or "Yo voté"** on the sticker? Decided Oct 31: **Yo voté** (official stickers and US search; evidence in `LOOP-county-and-mail.md`, pass 5).
4. **"votación anticipada" vs "votación temprana"** in headings: we use anticipada.
5. **State names:** decided, English everywhere (Garrett, October 19).
6. **"Para no correr riesgos"** (Oregon's "to be safe"): natural enough?
7. **"casillas" or "lugar de votación"** for polling place? Decided Oct 31: **lugar de votación**, and "cierra la votación" for polls closing (vote.gov, EAC, CA, FL, Miami-Dade; Texas alone says "casilla"; evidence in `LOOP-county-and-mail.md`, pass 5). "Casilla" stays only for a checkbox. The glossary says "lugar de votación", but "cierren las
   casillas" is in about 20 state files and one FAQ question (natural Mexican Spanish; flagged Oct 29 review).
8. **County clerk:** "secretario de tu condado (county clerk)" site-wide, but NM and IL use "la secretaría del
   condado" consistently. Pick one.
9. **Generic masculine "registrado"** ("si estás registrado") site-wide: keep, or rewrite neutrally?
10. **Smaller terms:** "pareja de hecho" (NM; "pareja" or "unión libre" is more Mexican), "notarizar" (MO, SD),
    "asilo de ancianos" for nursing home, Indiana's "junta de votación en ausencia" for traveling board.
