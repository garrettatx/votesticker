# Perplexity Answer 1 (Search mode, Oct 31 2026, 5:43 p.m.)

Thread: https://www.perplexity.ai/search/d6429a5a-b809-4d70-8057-e65bf8519e35 (started inside the
"VoteSticker.org Research" project). Deep Research was locked twice ("Upgrade for advanced research
capabilities. Upgrade to Max for enhanced access to Perplexity deep research."), so this ran in regular
Search. 79 sources listed. Ideas only; no fact below is used on the page without our own source.

Its own claim that the live page has 21 timeline entries is wrong: the page renders 26 (`site/history-data.php`
events; checked in the rendered list).

## Summary of the Answer

**1. Overview: three eras, not one squeezed axis.** Rebuild the overview as an index: three era panels with
HTML labels and links, small decorative SVG marks, vertical layout when tight. Panels about 1fr 1fr 2fr at wide
widths. Within a panel, position by sequence, not elapsed years; say so under the title ("spacing does not
represent elapsed time"). A visible // between panels, explained as a change of section, not "nothing happened".
Don't mix equal spacing with year ticks; print the year on each item instead. Breakpoints to test: about 1,050px+
three panels side by side; 700 to 1,049 three stacked bands; under 700 one vertical overview. Apply the
"too many items" rule per era, not to all 26.

**2. Labels and clusters.** Give every link a visible identifier (no anonymous dots). Two levels: headline
milestones (year plus short label) and supporting events (compact year-and-topic links). Proposed headline labels:
1888 Secret ballots; 1920 Voting tags and ribbon; 1950 Miami reference, unverified; 1985 Phoenix distribution;
1987 Flag oval; 2008 to 2010 Voting goes online; 2019 to 2020 Mail and digital versions; 2022 Ulster County design;
2024 to 2026 Michigan contests. Cluster same-year events under one year heading with individual links. If SVG is
kept: two label lanes (a third only on wide layouts), lanes assigned in data, labels in HTML, short leader lines,
group a collision instead of adding a fourth lane, change layout before shrinking type, test Spanish separately.
The key mixes three things (format, importance, evidence status); separate them and don't make readers infer
uncertainty from a dashed outline alone. Key inside the panel.

**3. Accessibility.** Don't aria-hide the linked overview; it is navigation, the list below is the record. Hide
only decorative marks and lines (`aria-hidden="true" focusable="false"` on the SVG). 44 x 44 targets (AAA; AA is
24 x 24). Label and mark one link; don't grow invisible hit areas into neighbors. Visible names like "1987 Flag
oval". Strong focus outline on the whole link, chronological DOM order. No animation by default; smooth scroll
only under `prefers-reduced-motion: no-preference`. No second sr-only copy of the chronology. Optional "skip the
overview" link.

**4. Page layout.** Narrow reading column interrupted by a few wider exhibits. Tokens it suggested: page max 76rem,
measure 66ch, section gap clamp(2.5rem, 6vw, 5rem). Entries: year column on desktop, year above the heading on
mobile, thin separators instead of boxes, evidence badge beside the source, precise source types. Figures: one
caption template (object, date, place, creator; what it establishes; holder and rights). "What we still don't
know" as a research ledger (question, current evidence, missing evidence, what would change the account).

**5. References it named.** Smithsonian Institution Archives timeline (date-first, self-contained entries);
Flourish responsive timeline guidance (design mobile separately); Flourish qualitative-data examples (horizontal
and vertical as alternatives); Datawrapper interview with C.J. Sinner, Star Tribune (edit labels per screen);
W3C WAI complex images tutorial; Chalkbeat "Showing what's not there" (gaps in data vs gaps in history). It said
it had not verified specific NYT, Washington Post, Pudding, Reuters, OWID or BBC timelines.

**6. Density strip and then/now.** A density strip can read as "sticker popularity"; if used, title it
"Documented examples included on this page" and say counts reflect the selection. A simpler option: a count
beside each era heading. Then/now as an HTML comparison "What changed, and what stayed the same" (format,
distribution, authorship, public signal), each cell sourced.

**7. 10x ideas (page).** Competitors differ (AARP: Boudreau; Time: uncertain origins; Mental Floss: 1950 and
distribution; Vox: logistics, law, behavior; Smithsonian: Anthony grave conservation). Ideas: separate the four
origin questions (earliest located reference, earliest reviewed artifact, organized distribution, origin of the
familiar design); a claim-to-source trail showing when later articles cite each other; exact wording beside early
objects and a tag vs adhesive sticker distinction; "What this proves / what it does not prove" per entry; richer
source records (page, collection id, archive, transcription); use date vs reporting date; keep the "studies did not
test a paper sticker" boundary beside the research; human stories through documented decisions; ask for specific
evidence (dated photo, object with provenance, newspaper scan, purchasing record); a visible revision ledger
shared by both languages.

**8. Build order and acceptance tests.** One dataset; HTML era index with decorative SVG; narrow layout; restyle
chronology and source lines; research ledger; density strip last. Test EN and ES at 320, 375, 640, 768, 1,024,
1,440 and 200% text. Accept: no clipped labels, no horizontal scroll, no overlapping hit areas, understandable
link names reaching the right entry, chronological keyboard order, visible focus, decorative SVG out of the
accessibility tree, reduced motion respected, no uncertainty by shape or color alone, no overview item without
an entry.

It also flagged that the legal and election-office sections need current official verification before being
presented as applicable in 2026 (outside this pass; those sections are dated history, not 2026 guidance).
