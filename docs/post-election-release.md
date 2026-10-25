# Post-Election Release: Checklist and Copy

The `post-election` build from `docs/election-lifecycle-plan-20261025.md`, from Garrett's October 25
checklist and copy rounds. A change to the existing static build: no redesign, archive, results or
new assets. Spanish for every line goes through `docs/spanish-style.md` before release.

## Changes From the Pasted Checklist

- **Build time.** The daily build is 11:05 UTC (5:05 a.m. Central after November 1), not 6:05.
- **No cache step.** A Cloudflare Pages deploy replaces the site in one step; there is nothing to
  purge. A manual purge would be a Cloudflare change, which needs Garrett's OK.
- **Home page title stays** "I Voted Sticker Maker: Free Vote Stickers by State". It is already
  evergreen and is at position 7 to 8 for "i voted stickers by state"; the suggested replacement used
  an em dash and adds nothing.
- **lastmod.** Fixed October 25: the daily countdown label no longer counts as a content change.
  The post-election switch is a real change, so those pages get a new date once.

## Checklist

**Ready**
- [ ] Activation time recorded in UTC, after the last verified scheduled cutoff (from loop batch 3).
- [ ] Official extensions checked that evening; any state that still needs voting instructions gets a
      reviewed exception.
- [ ] The run sets `VOTE_TODAY=2026-11-04`; the November 4 daily build is the second chance.
- [ ] Who checks the live output, and the manual commands (in the lifecycle plan) if a run fails.

**State pages**
- [ ] Banner replaced by the post-election intro; no "vote today", "voting today?", poll hours or
      return countdowns.
- [ ] The countdown line: the date stays, the "Today"/"Tomorrow"/"N days away" label goes.
- [ ] Gallery right after the intro, unless a verified follow-up notice belongs first.
- [ ] Official election office link kept; polling place lookup kept lower down as "Official election
      information".
- [ ] Ballot tracker only where `ballot_tracker_url` is verified, labeled for what it shows (receipt
      or status, never "counted").
- [ ] Provisional or cure instructions only where verified and still applicable; no inferred deadlines.
- [ ] No empty buttons or sections.
- [ ] Dates table below the gallery, labeled "November 3, 2026 election: past voting dates". Any
      still-applicable follow-up stays outside it.
- [ ] "Last checked" dates unchanged by the build.

**Gallery and maker**
- [ ] "I voted" selected; "I voted early" and "I voted by mail" kept.
- [ ] "Vote today" and dated reminders out of featured spots. If a dated reminder is still offered, its
      label sits beside it: "Past-election reminder · November 2026. The dates shown are historical."
- [ ] Image URLs unchanged; no 2026 image reused for another election.
- [ ] Maker links don't preload expired dates; preview matches the saved file; ready-mades save without
      opening the maker; nothing uploaded.

**Post text and sharing**
- [ ] Every message option checked, not just the default.
- [ ] No "still time", "vote today" or early voting invitations.
- [ ] General share buttons neutral; first-person text only after someone picks a completed-voter
      message.
- [ ] "My ballot is in" never becomes "accepted" or "counted".

**Home page**
- [ ] Banner replaced; state picker prominent; stickers the main action.
- [ ] No "early voting open" badges, no national count of states voting.
- [ ] FAQ, cards, nav and footer checked for stale invitations.

**Metadata**
- [ ] State titles per the lifecycle plan ("[State] I Voted and I Voted Early Stickers").
- [ ] Descriptions and share images carry no expired action.
- [ ] Canonicals and redirects unchanged; image sitemap lists only images shown on each page.

**Spanish**
- [ ] Built from the same mode; same labels, dates and qualifications; maker defaults and post text
      checked; the language switch never lands on an Election Day version.

**Preview and QA**
- [ ] `VOTE_TODAY=2026-11-04` preview: an in-person state, a mail state, towns set hours, two time
      zones, a state with a tracker, one without, one with a still-applicable follow-up deadline.
- [ ] Facts, SEO, Spanish (`--base` local) and card QA pass; built HTML inspected, not just the browser.
- [ ] Search the output for "vote today", "voting today", "polls close", "early voting is open",
      "still time", "tomorrow", "today" (English and Spanish). Quoted sticker wording and historical
      lines can stay; interface copy can't.
- [ ] Phone layout, keyboard, a ready-made save and a maker save.

**Deploy**
- [ ] Deploy; check home and representative English and Spanish state pages live.
- [ ] Log build, deploy and live check as separate times in `docs/change-log.csv`.
- [ ] If the deploy fails: Garrett is alerted and the manual post-election build goes out. Never
      restore an old build that still invites people to vote.
- [ ] The November 4 daily build doesn't revert the mode.
- [ ] Review follow-up items before `between-elections`; December 1 is a review date, not a cutoff.

**Release gate:** every surface agrees. No expired invitation anywhere, stickers and maker easy to
use, applicable official links still there, past dates labeled, English and Spanish match, and a
failed run has a recovery path.

## Copy

Draft English. Each line ships only where the page actually offers what it names.

### Home Page

- Eyebrow: FREE VOTING STICKERS BY STATE
- Heading: **Voted? Get your sticker.**
- Intro: Pick your state, choose a design, and save it. Get an "I voted", "I voted early" or "I voted by
  mail" sticker, or make your own.
- Button: Choose your state. Link: Browse the stickers.
- Privacy line (keep the current wording if it already says this): Your picture stays in your browser.
  Nothing is uploaded.
- Gallery note: These designs are made by VoteSticker. They're separate from the stickers election
  offices hand out.
- Official information: **Looking for ballot information?** Your state page links to official election
  resources, including ballot tracking where your state has it.
- Past dates: **About the November 2026 dates.** The voting dates for the November 3, 2026 election are
  now past. State pages keep them, labeled, below the stickers. Check your election office for upcoming
  elections and anything that still applies to your ballot.
- Description: Get a free I voted, I voted early or I voted by mail sticker. Choose your state, save a
  design or make your own.
- Share image title: Voted? Get your sticker. Reuse the current share image unless its artwork shows an
  expired instruction.

### State Pages

- Intro: **Voted? Get your sticker.** Pick a [State] design, save it and share it. Ready-made stickers
  are below, or make your own.
- Tracker line, only with a verified tracker: Returned a mail ballot? Check its status with
  [tracker name].
- Description: Download free [State] I voted and I voted early stickers, or make your own. Find official
  ballot resources for [State]. (Add "and local sticker stories" only where the page has one.)
- Share title: Get your [State] voting sticker.

Link labels: "Check my ballot status" (verified tracker), "Check ballot help instructions" (verified
cure or provisional steps), "Contact my election office", "Official election information".

### Post Text and Share Buttons

- Home share button, neutral: Want a voting sticker? Choose your state and save a free design, or make
  your own. https://votesticker.org/
- After "I voted": I voted. Here's where to get a free sticker or make your own: [state link]
- After "I voted early": I voted early. Got my sticker here: [state link]
- After "I voted by mail": I voted by mail. Get a sticker for your state: [state link]
- State share button: Free [State] voting stickers. Pick a design, save it or make your own. [state link]
- After saving: **Your sticker is ready.** Add it to a post or use it as your profile picture.

### Home Page FAQ (Six)

1. **Can I still get an I voted sticker after the election?** Yes. Choose your state and save a free
   sticker, or make one with your own picture. The designs stay up after Election Day.
2. **I voted early or by mail. Which sticker should I use?** Pick the one that fits: "I voted", "I voted
   early" or "I voted by mail".
3. **Are these the stickers handed out at the polls?** No. VoteSticker makes these designs. Election
   offices make their own; when we show one, we name the office and credit the designer where it's
   documented.
4. **Can I still get a paper sticker?** Your state page lists the pickup and mail-ballot sticker
   details we could confirm with an official source. A place that had stickers during voting may not
   have extras now, so call the office before you go.
5. **How do I save and share my sticker?** Pick a design and save it. Use it as your profile picture or
   add it to a post. To add your own picture, open the maker.
6. **Do I need an account? Is my picture uploaded?** No account. Your picture stays in your browser;
   nothing is uploaded.

Ballot status ("Does getting a sticker mean my ballot was counted? No...") goes beside the official
links, not in the FAQ. Archive questions wait for the archive.

### State Page FAQ, Only Where It Adds Something

- **Where can I get a paper sticker in [State]?** Only for a state with a verified answer, worded for
  the election it covers: "For the November 2026 election, [office] offered stickers [how]. We haven't
  confirmed whether it has extras now; contact the office before you go." States without a verified
  answer get no question.
- **How do I check my mail ballot?** Only with a verified tracker: "Use [tracker] to check your mail
  ballot. It explains what each status means; if something's unclear, contact [office]."
- **Why are the November 2026 dates still here?** Only if the dates section raises the question.

### Launch Post (Garrett or VoteSticker)

> Voted? Get your sticker.
>
> VoteSticker has free "I voted", "I voted early" and "I voted by mail" designs for every state. Pick
> yours and save it, or make one with your own picture. Your picture stays in your browser.
>
> https://votesticker.org/

Social posts need Garrett's OK before they go anywhere.
