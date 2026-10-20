# Suggested Post Text Rules

Set October 19, 2026. The suggested post is the text people copy when they post their sticker. It
lives in `vote_post()` in `site/index.php`. The maker swaps versions when the sticker's message
changes, and stops once the person edits the text.

## What the Research Supports

- **Friends move friends.** In a 61-million-person Facebook experiment during the 2010 midterms, a
  message showing which friends had voted raised turnout. Most of the effect spread between close
  friends rather than coming from the message itself ([Bond et al. 2012, Nature](https://sparq.stanford.edu/sites/g/files/sbiybj19021/files/media/file/bond_et_al._2012_-_a_61-million-person_experiment_in_social_influence_political_mobilization.pdf)).
  A personal "I voted" post is that signal.
- **A plan beats a plea.** Helping people say when and where they will vote raised turnout by 4.1
  points; a plain encouragement call had no significant effect ([Nickerson and Rogers 2010,
  Psychological Science](https://toddrogers.scholars.harvard.edu/publications/do-you-have-voting-plan-implementation-intentions-voter-turnout-and-organic)).
- **Bait gets demoted.** Facebook announced in December 2017 that it demotes "tag a friend", "comment
  YES", share bait, and vote-by-reaction posts ([Search Engine Journal coverage](https://www.searchenginejournal.com/facebook-demoting-engagement-bait/228071)).
  Posts asking for help or a cause were exempt; a genuine question is not bait.

Untested, so not used as rules: best posting times, ideal length, link placement in comments, hashtag
counts beyond keeping them few. See the global `social-posts` skill.

## The Rules

1. **First person, matching the sticker.** "I'm voting early." for VOTE EARLY, "I voted." for I
   VOTED. Never #IVoted before someone has voted.
2. **One useful state fact.** The dates and the official site, from the same verified data as the
   page. A friend should be able to act on the post without clicking anything.
3. **One plan question.** "What's your plan to vote?" or, in all-mail states, "When are you sending
   yours back?" Never "tag a friend", "share if", or "comment YES".
4. **One invite, with the full link.** "Want one? Make your own free Texas vote sticker:
   https://votesticker.org/texas". The full https:// URL links on LinkedIn and in email, where a bare
   domain may not. Instagram captions never link, so the post has to work without it.
5. **Three hashtags, matched to the message.** The message tag, #VoteSticker, and the state tag
   (#VoteTexas, #VoteDC).
6. **Nonpartisan, always.** No candidates, parties, issues, or measures.
7. **The person's own words win.** Once they type in the box, the maker never overwrites it.

## The Six Versions

| Sticker message | Opening | Question | First hashtag |
|---|---|---|---|
| Vote early | I'm voting early. | What's your plan to vote? | #VoteEarly |
| Vote by mail | I'm getting my ballot in early. | When are you sending yours back? | #VoteByMail |
| Vote Nov 3 | I'm voting on Election Day. | What's your plan to vote? | #ElectionDay |
| I voted | I voted. | What's your plan to vote? | #IVoted |
| I voted early | I voted early. | What's your plan to vote? | #IVoted |
| I voted by mail | My ballot is in. | What's your plan to vote? | #IVoted |

Spanish pages use the same six (tú, `docs/spanish-style.md`): Voy a votar temprano, Voy a entregar mi
boleta con tiempo, Voy a votar el Día de las Elecciones, Ya voté, Ya voté temprano, Ya entregué mi
boleta. Questions: ¿Ya tienes tu plan para votar?, ¿Cuándo vas a devolver tu boleta?, and after voting
¿Y tú, ya tienes tu plan para votar? Tags: #VotaTemprano, #VotaPorCorreo, #YoVoto, #YaVoté.

The post text follows the page's language. An English page with a Spanish sticker message gets the
English post.
