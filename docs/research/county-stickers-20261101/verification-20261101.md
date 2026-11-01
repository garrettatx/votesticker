# Verification, Nov 1, 2026 (Pass 13)

Every entry's source was read by me on the official page (curl, or Chrome where curl was blocked). Quotes are exact.

| State | Place | Demand | Source | How read | Quote |
|---|---|---|---|---|---|
| NY | New York City | GSC Oct 15-31: 52 impr. ("nyc early voting stickers" 24, "nyc voting stickers" 14, "nyc voting sticker" 6, "nyc i voted sticker" 4, "i voted sticker nyc" 3, "nyc voted sticker" 1); Ahrefs 30 + 30 | facebook.com/theNYCBoardofElections post 1534236132071631 (verified badge, posted Oct 31, 6:50 a.m.) | Chrome, caption expanded | "First 300 voters get a NEW DESIGN Limited-Edition Halloween Sticker! Polls open 9-5" |
| CO | El Paso County | 0 | clerkandrecorder.elpasoco.com, Sept 29, 2026 | curl | "The winning sticker designs will be included in all election ballot packets sent to active, registered voters Monday, October 5." |
| CO | Jefferson County | 0 | jeffco.us news flash 2572, Mar 23, 2026 | curl | "Marissa's design, along with Alyssa’s, will be included in ballot envelopes across the county this November, so keep an eye out for their artwork!" |
| CO | Larimer County | 0 | larimer.gov/clerk/elections/i-voted-sticker-contest | Chrome (curl 403) | "Kylie's sticker will be included on your voter instructions with your mail ballot. Finalist stickers will be available at vote centers across Larimer County starting October 19 while supplies last." |
| CO | Mesa County | 0 | mesacounty.us news, Jan 14, 2026 | curl | "Their artwork will be featured on official “I Voted” stickers distributed during the 2026 election season." |
| PA | Montgomery County | 0 | montgomerycountypa.gov Voter Services contest page | curl | "Winning sticker designs will be distributed Countywide to voters who cast ballots in person at polling places, receive mail-in ballots, visit One Montgomery Plaza, or use satellite election offices, including the Voter Services Mobile Outreach Van." |
| PA | Erie County | 0 | eriecountycouncilpa.gov/i-voted-sticker-contest (+ Jan 15, 2026 release PDF) | curl, pdftotext | "2026 Winners 1st Place Colin - Peninsula I Voted Inclusion Sticker General McLane High School"; PDF: "In 2026, that tradition is getting a fresh new look. Voters will receive an “I Voted” sticker created by a local student through the design contest." |
| OR | Deschutes County | 0 | deschutescounty.gov/1294 | curl | "Rowan's "I Voted" sticker will be featured on the November 3, 2026 General Election Voters' Pamphlet cover." / "Rowan's "I Voted" sticker will be available in November." |
| VA | City of Williamsburg | 0 | williamsburgva.gov news flash 1282, July 9, 2026 | curl | "The winning designs will be used as official vote stickers during the City of Williamsburg’s 2026 election cycle this fall, ..." |
| MD | Washington County | 0 | washingtoncountymdvotes.gov/?page_id=1711 (+ washco-md.net release Jan 5, 2026) | curl | "Congratulations to our 2026 “I Voted” Sticker Contest Winners"; release: "...the artwork that will be featured on future “I Voted” stickers used during elections in Washington County." |

Host checks: clerkandrecorder.elpasoco.com added to ALLOW_HOSTS (elpasoco.com home: "Home - El Paso County, Colorado"; the
clerk home lists the Oct 5, 2026 ballot mailing). NYC BOE Facebook page added to ALLOW_ACCOUNTS. All other hosts pass OFFICIAL.

## Dropped
- Alameda County (GSC 7 impr.), Chicago (GSC 7, Ahrefs 0), New Orleans (GSC 9; the statewide Osborne sticker is already the LA
  story), Wake County (GSC 1; unicorn early-voting sticker is 2024), Durham (GSC 2; already the NC story), Austin/Travis (GSC 2):
  no official 2026 local sticker statement found (Perplexity p1, WebSearch).
- NYC marathon-day sticker: news only (Time Out, UnoTV); no BOE post found on Nov 1.
- Fayette County IL: winners blank, "an upcoming election". Camden County NJ: March call, "upcoming elections", no winners.
- Warren County NY: launch says stickers "beginning with the 2026 General Election", but winners are not on the county site.
- Wilkes County NC (wilkescounty.net, not in ALLOW_HOSTS; winners in a document only), Sedgwick KS (already the KS story),
  Johnson County IA (contest call only), Sarasota FL (already the FL story; site blocks curl), Multnomah OR (already the OR mail
  answer), Snohomish WA (already the WA story), Kitsap and Thurston WA (2025 designs; Thurston is already the WA mail answer).
