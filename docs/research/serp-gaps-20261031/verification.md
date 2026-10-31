# Verification, Pass 8 (October 31, 2026)

Every line added to the site, the official page it rests on, and how it was read.

| State | Added | Source | Read |
|---|---|---|---|
| OH | "It's round, with a red outline of Ohio that reads OHIO VOTED, a gold America ribbon between 1776 and 2026, and a big 250 at the bottom." | https://www.ohiosos.gov/office/media-center/categories/press-releases/2026-06-12 (image alt "ohio-voted-250-sticker") | Chrome, image looked at. Same image on Butler County BOE https://elections.bcohio.gov/news_detail_T8_R45.php |
| LA | "It's round and turquoise, with a gold trumpet, yellow magnolias and Mardi Gras beads above the words I VOTED in purple." | LA SOS Facebook, July 28: "Here it is! The new "I Voted" sticker! This artwork was created by New Orleans artist Terrance Osborne." https://www.facebook.com/Louisianasos/posts/here-it-is-the-new-i-voted-sticker-this-artwork-was-created-by-new-orleans-artis/1564587452362831/ | Chrome, logged out, image looked at. The sos.la.gov release (the linked source) has no image; noted in `la.json` |
| GA | "It's round, with the gold dome of the State Capitol painted in front of the Georgia flag and "I voted" in white brush letters." | https://sos.ga.gov/news/georgia-debuts-new-i-voted-sticker-2026-midterm-elections, "View the new "I Voted" Sticker here." (links a 600x600 PNG on mcusercontent.com) | Chrome (curl is blocked), image downloaded and looked at |
| GA | "Penley said he made a design Georgians can take home with them from the polls." | Same release: "I'm honored to create a design that Georgians can take home with them from polls," said Steve Penley | Chrome |
| GA | Pickens County entry (classes drew custom America 250 stickers; voters get one; Stormi, sixth grade, Jasper Middle School) | https://pickensgaelections.com/2026-general-election: "Across Pickens County, classrooms stepped up to celebrate democracy and America 250. Vote to receive one of these custom "I Voted" Stickers!" Home page: "by Stormi Frix, Jasper Middle School 6th Grade" | curl; the home page reads "Welcome to Pickens County Board of Elections and Registration" with the office address. Added to ALLOW_HOSTS |
| AK | "Do I get an I voted sticker if I vote early in Alaska? Yes. During early voting, the Division of Elections told voters to find their voting location, vote, and get their "I Voted" sticker." | Alaska Division of Elections Facebook, October 20, 2026, 2:00 PM: "#remember Early voting is underway across Alaska. If you want to vote early, find your location on our website, vote, and get your "I Voted" sticker." https://www.facebook.com/akelections/posts/remember-early-voting-is-underway-across-alaska-if-you-want-to-vote-early-find-y/1420496263601916/ | Chrome, logged out. Added to ALLOW_ACCOUNTS |

Checked and found nothing new: Ohio SOS statewide digital sticker for 2026 (Lorain County's Facebook post is a year
old; `site:ohiosos.gov` shows only the America 250 release, week-in-review items and manuals). Louisiana: no official
line on where voters get it or on absentee voters. Georgia and Alaska: no official line on mail voters.
