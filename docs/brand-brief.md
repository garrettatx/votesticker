# Vote Sticker Brand Brief

Set October 21, 2026, from Garrett's positioning research, adapted to the site as built. Every page, post,
button, facts file and writing prompt follows it. The humanizer pass runs under it, never over it.

## Position

Vote Sticker is the easy way to show your friends you're voting and nudge them to vote too. It is a
sticker maker second.

> For people who plan to vote and want their friends to show up too, VoteSticker.org is the free,
> nonpartisan way to show you're voting and share your state's real dates. Private, accurate, and made
> for profile pictures.

What the research supports, and how far we take it (details and sources in `docs/post-text-rules.md`):

- **Friends move friends.** A personal "I voted" post is the signal that spreads. So the sticker is a
  signal to friends, not a decoration, and the post text always asks one friend.
- **A plan beats a pep talk.** Asking when and where someone will vote raises turnout; "please vote"
  doesn't. So the site asks for a plan and lets people put it on the sticker (the date line is free
  text: "VOTING SAT A.M.").
- **Being seen matters, pride not pressure.** We celebrate people who voted. We never imply anyone's
  record is being watched, and we never say the sticker raises turnout. We say what the research found
  about friends and plans, not what the tool proves.

## Pillars

1. **Social.** The sticker is a signal to friends.
2. **Accurate.** Every date and rule comes from an official source, with one "last checked" date per page.
3. **Private.** No account, and nothing you make is uploaded.
4. **Nonpartisan.** Pro-voting, never pro-candidate.
5. **Made by people.** Built in Austin by Garrett Digital.

## Voice

A friendly neighbor who checked the facts.

| Do | Don't |
|---|---|
| Short, plain sentences | Slogans in pairs ("Make your plan. Share the sticker.") |
| Concrete details: dates, days, places | Vague lines ("what's true where you vote") |
| "You" and "your friends" | "Users," "citizens," "voters" in the abstract |
| Warm, a little playful | Guilt, shame, panic or fake urgency |
| Name the next step | Explain how the site works behind the scenes |
| Celebrate people who voted | Imply anyone's voting record is being watched |
| Lead with what the voter does ("Leave the campaign shirts at home") | Lead with the statute |

Words to use: plan, show, your friends, your state, official, free, private, on your way, done.

Words to avoid: civic duty, make your voice heard, democracy depends on you, seamless, empower,
leverage, unlock, don't miss out. No em dashes anywhere.

Headings read the way people search or think, in Title Case to match the site: "What ID to Bring",
"Who Can Vote by Mail", "Questions People Ask". The page title and H1 carry the state name; the headings
under them don't need it.

Where the brief and search pull apart, search wins in the H1 and title (they carry "I voted sticker"
and the state), and the brief wins in the lead and everything below.

## Facts

- Every date and rule comes from an official state or county source, read on a date we show. The
  facts files (`build/state-facts/`) carry the URL, the check date and the source sentence, and
  `build/facts_text.py --check` rejects anything else.
- One "last checked" date per page, and one source line per section with the source's name
  ("From the Texas Secretary of State and VoteTexas.gov"), never a stamp under every paragraph.
- A rule we haven't verified doesn't go on the page. The official link does.
- A passed deadline gets a next step, not a dead end ("Passed. Not sure you're registered? Check My
  Voter Portal.").
- Say each date once on a page: the ribbon and the dates table. Nowhere else.

## Calls to Action

In priority order, first person on buttons: Get my sticker, Make my plan, Share with a friend, Find my
polling place (always the official site), Switch to I voted.

## Messages by Phase

| Phase | Message | Example |
|---|---|---|
| Before early voting | Make a plan | "Early voting starts Monday. Pick your day." |
| During early voting | Show it and ask a friend | "I'm voting Saturday morning. Who's coming?" |
| Final days | Gentle urgency with facts | "Early voting ends Friday at 7 p.m." |
| Election Day | Hours and location | "Polls are open 7 a.m. to 7 p.m. Find yours." |
| After voting | Celebrate and pass it on | "Done. Now go remind one friend." |

Closing times come from the state's verified hours, never from memory. The site only says "ends in 3
days" when the build date and the verified dates make it true (the static build must run daily for
that; see `docs/content-strategy-state-pages.md`, Phase 3).

## Access

Plain language at about an 8th-grade reading level. Real alt text on every image. Spanish written by
a person following `docs/spanish-style.md` and the es-mx-localization skill, not run through a
translator, with Garrett's native review still to come. Nothing that only works with a mouse.

## Tool Ideas That Fit

Done: free-text date line for a plan; four post versions that ask a friend; Spanish throughout.
Open: a "Done" line after switching to I voted ("Send this to someone who hasn't voted yet"); an
add-to-calendar file made in the browser; a true-only "early voting ends in N days" line.

## Examples

Side by side, the fastest way to show the voice. Writers and AI tools follow these better than rules.

| Where | Don't | Do |
|---|---|---|
| Hero | Make your plan. Share the sticker. | Show your friends you're voting. |
| Hero | Your vote matters more than ever | Early voting is open. Pick your day. |
| Button | Submit / Learn more / Share | Get my sticker / See Texas dates / Send to a friend |
| Motivation | It's your civic duty to vote. | What day works for you? Saturday? Your lunch break? |
| Motivation | Millions of people don't vote. Don't be one of them. | Your friends are voting early this week. |
| Friends | I voted. Did you? | I voted. Took 15 minutes. Who's next? |
| Friends | Share this with your network. | Send this to one friend who hasn't voted yet. |
| Urgency | Hurry! Time is running out! | Early voting ends Friday. Polls close at 7 p.m. |
| Urgency | Last chance to vote! (four days out) | 3 days of early voting left in Texas. |
| Facts | Hours may vary. | Early voting hours vary by county. Check your county's site. |
| Facts | You may need ID. | Bring one of these seven IDs. |
| Sources | Source: sos.state.tx.us, checked October 21, 2026. (every paragraph) | From the Texas Secretary of State. Last checked October 21, 2026. (once per section) |
| Deadline passed | That deadline has passed. | Registration closed October 5. Already registered? Check here. |
| No early voting | Early voting unavailable. | Alabama votes on Election Day, Nov. 3. Plan your time. |
| Privacy | We value your privacy. | No account. Nothing you make is uploaded. |
| After voting | Thank you for fulfilling your civic duty. | You did it. Switch to I voted. Who's one friend you can text right now? |
| Alt text | Texas vote picture, hand-made design 1 | Plum circle sticker reading VOTE EARLY, Texas, Oct. 19–30 |

"Did you?" sounds like a test; "Who's next?" sounds like an invitation. Only say "last" on the last day,
with the date. On colors: the brand cores pair red, navy and cream, the civic combination; what reads
as partisan is a sticker that is all red or all blue, so the maker's defaults lean plum, gold, teal
and cobalt.

## For AI Writing Prompts

Paste at the top of any writing request:

> You're writing for VoteSticker.org, a free, nonpartisan, private tool that puts a state's real
> voting dates on a profile-picture sticker. Goal: help the reader make a voting plan, share it, and
> get one friend to vote. Voice: a friendly neighbor who checked the facts. Short, plain sentences,
> concrete dates, "you" and "your friends." Lead with what the reader does, then the rule. No slogans
> in pairs, no guilt or shame, no fake urgency, no "civic duty," "make your voice heard," "seamless,"
> or "empower," no em dashes. Never state a voting rule unless the official source is provided. End
> with one clear next step.

## Links to Resist Now

Garrett, October 21: link to a Resist Now Learn guide only where it answers the next question a reader
of that section has, in a natural line of text. Three today, one per guide section, through `vote_rn()`
in `site/index.php`: voter registration (under the dates table), voter ID laws (under What ID to Bring)
and mail-in voting (under the mail section). Spanish pages mark them "(en inglés)". Learn pages only,
factual reference pages only; nothing from issue hubs, briefs or action pages, and nothing whose framing
argues a position, because Vote Sticker's own copy stays nonpartisan. Ranked-choice voting is a
candidate for Alaska and Maine once their guides exist.

## Writing Rules From the History Page Reviews (October 22)

These apply sitewide, not just to /history/.

- **One claim, one detail, one reason it matters.** Split a sentence that stacks three facts.
- **Concrete verbs.** "Order" beats "buy," "handed out" beats "made," "visited" beats "came."
  Avoid vague connectors like "went the other way" or "point to"; say what happened.
- **Put uncertainty in the sentence**, calmly: "the earliest reference located so far," "the original
  page hasn't been available to review," "documented so far." Never a badge, never an apology, and
  never the word "first" without unusually strong proof.
- **No status notes in public copy.** Words like "source check," "being checked," "will not be
  published" belong in the build, not on the page.
- **Don't undercut your own facts.** Cut "even" in lines like "has even written about."
- **Say who and where.** Name the person in each card, the county for a county story, and the
  program by its name (Clearie Award, Federal Voting Assistance Program).
- **Style:** "I Voted" in quotes when it means the phrase or a sticker's wording; Election Day
  capitalized; "polling place" for the location; "mail ballot" or "absentee ballot" as the state
  says it; "$1" not "a dollar"; Title Case headings; no em dashes.
- **Trust lines:** "Last reviewed [date] · Suggest a correction." Invite, don't defend.
- The page should sound like someone who went down an archive rabbit hole and is showing you the
  best parts, not an institution defending a white paper.
