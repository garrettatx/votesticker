# VoteSticker Visibility Strategy

October 26, 2026, eight days before the election. Data: Search Console and GA4 from the BigQuery archive
(October 19 to 25, the site's first week), Ahrefs Keywords Explorer and the "i voted sticker" results
page (US, pulled today). The working list is `docs/LOOP-visibility.md`.

## Where We Stand

- **Search Console:** 5,506 impressions and 609 clicks in seven days, an 11 percent click rate, at an
  average position of about 5. About 80 percent of impressions come from queries Google doesn't show.
- **GA4:** 594 organic sessions, 60 percent engaged. Organic is the largest channel, ahead of direct
  (359). Tracked saves started October 23: 35 so far.
- **What Google shows us for:** sticker searches, almost entirely. "[State] i voted sticker" is the top
  visible query on nearly every state page. Texas (780 impressions), New York, California, North
  Carolina, Washington, Florida and Oregon lead, and so does the home page (306).
- **National searches land on state pages.** "i voted early sticker" (199 impressions) was spread over
  23 pages, with Texas taking 79. "voting stickers 2026" and "voting sticker" also went to Texas.
- **The biggest phrase isn't reaching us yet.** "i voted sticker" gets 6,300 searches a month with a
  difficulty of 1, and the home page doesn't show for it. The current results mix county sticker
  contests, Amazon, FVAP's free downloadable sticker, Ballotpedia and Wikipedia, and three label
  printers rated DR 21 to 36. Nearly every result shows image thumbnails. A free, well-labeled sticker
  page with images can compete here.

Other national phrases (Ahrefs, monthly): i voted stickers 600, voting sticker 450, voting stickers 250,
i voted sticker png 150, i voted today sticker 150, i voted early sticker 70, i voted stickers by state
50, i voted by mail sticker 30, yo vote sticker 20.

## The Pages With the Fewest Impressions

| Page | Impressions | Position | Read |
|---|---|---|---|
| North Dakota | 0 | | No voter registration and a small market |
| Wyoming | 2 | 1.5 | Ranks well when it shows; low demand |
| Kentucky | 3 | 10.7 | Page 2; check the title and sticker story |
| New Hampshire | 3 | 6.3 | No early voting, so no early sticker searches |
| West Virginia, South Dakota | 8 each | 3 to 5 | Low demand |
| Idaho | 12 | 3.8 | Low demand |
| Georgia | 15 | 7.9 | The outlier: a state of 11 million. The Secretary of State released a new 2026 sticker; check whether that announcement takes the searches |
| Delaware, Maine, Montana | 16 to 20 | 3.6 to 7 | Low demand; Delaware and Maine have no sticker story |

These pages have the same depth of facts as the strong ones (14 to 23 sourced facts each), so length
isn't the cause. Most of the gap is population and search demand, which content can't change. Where
content can help is the sticker story (eight states lack one: AR, AZ, DE, IN, ME, NE, NJ, WI) and pages
that miss what their state is known for, which may be Georgia's case.

## What the Strong Pages Tell Us

The top pages' queries show what searchers want from a state page, in order: the state's I voted
sticker (often with the year or the state's abbreviation: "nc", "sc", "mn"), the early voting version,
then the facts around voting (early voting dates, ID, ballot photos). The state pages already cover all
three. The improvement is in matching the sticker side more closely: each state's own sticker story, and
the home page as the clear national answer.

## The Plan

**Today (done or built):**
1. The Classic I Voted Stickers on the home page are live: 18 per language, with image sitemap entries.
2. Built, shipping with the next push: three classic stickers on every state page, each linking to the
   home set, with one text link, "See all 18 classic I voted stickers." Every state page then points to
   the home page for the national phrase in the same words. That tells Google which page answers "i
   voted sticker" without taking anything away from the state pages.

**Before November 3:**
3. A home page FAQ, "Where can I get an I voted sticker?", answered plainly. Polling places hand them out
   after you vote; anyone who voted by mail or missed one can save one here. This answers two queries
   now sitting at positions 9 to 11 on state pages.
4. Sticker stories for the eight states without one, from state or county sources only.
5. Georgia: name the state's new 2026 sticker and link the Secretary of State's announcement.

**Election week (November 2 to 4):** the existing switch to "I voted" mode. "i voted today sticker" and
"i voted sticker png" peak on Election Day, and the classic set and cut-out PNG cover both.

**The weeks after:** a weekly pass on the lowest-impression pages (cycle in the loop file); the history
page for "i voted sticker history" (133 impressions at 8.7, where Ballotpedia and Wikipedia rank); and
watching the Spanish pages, which have their first ranking query ("sticker de votacion" at 2.7).

## What We Won't Do

- No keyword lists, repeated phrases or text written for search engines. Each page has one H1, a title
  that says what it is, and plain answers.
- No new pages for phrases a current page can answer. The home page answers the national phrases; state
  pages answer their states.
- No claims without an official source, and no reaction to one week of data as if it were a trend. The
  site is a week old and positions move daily.

## How We'll Know

- **Within two weeks of each change's recrawl:** the home page shows for "i voted sticker" and "voting
  sticker", and "i voted early sticker" impressions move from state pages to the home page.
- **On state pages:** clicks hold.
- **In GA4:** `sticker_save` with `sticker_source: classic` shows whether people use the set.
