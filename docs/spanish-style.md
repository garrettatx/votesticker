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
| I voted | YA VOTÉ | |
| I'm voting | VOY A VOTAR | |
| Absentee ballot | | boleta de voto en ausencia; votar en ausencia |
| Polling place | | lugar de votación |
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
3. **"Ya voté" or "Yo voté"** on the sticker?
4. **"votación anticipada" vs "votación temprana"** in headings: we use anticipada.
5. **State names:** decided, English everywhere (Garrett, October 19).
6. **"Para no correr riesgos"** (Oregon's "to be safe"): natural enough?
