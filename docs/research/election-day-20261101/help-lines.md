# Pass 14: Voter Help Lines (Nov 1, 2026)

Who-to-call answers (`faq.problem`) for the 40 states pass 12 didn't cover. Each number read on the state election
office's own page, Nov 1, with curl or Claude in Chrome (no Firecrawl). WebSearch was used only to find which page
lists the number; no Perplexity this pass. Advocacy hotlines (866-OUR-VOTE and others) are not used as a state answer.

Demand: Search Console, Oct 18 to 31, page impressions (EN page).

## Batch 1 (top 8)

| State | GSC impr. | Number(s) | Source | Read with | Quote |
|---|---|---|---|---|---|
| Georgia | 223 | 404-656-2881 | https://sos.ga.gov/elections-division-georgia-secretary-states-office | Chrome (curl 403) | "Contact the Elections Division Address 2 MLK Jr. Drive Suite 802 Floyd West Tower Atlanta, Georgia 30334 Phone 404-656-2881" |
| Pennsylvania | 178 | 1-877-868-3772 | https://www.pa.gov/agencies/vote/contact-us | curl | "Pennsylvanians who need information or encounter problems at their polling place should call our official election hotline toll free at: 1-877-868-3772. ... Interpreters are available if English is not your first language." |
| Louisiana | 424 | 1-800-883-2805 | https://www.sos.la.gov/elections-voting/contact-us | curl | "Elections hotline : 1.800.883.2805" |
| Tennessee | 302 | 1-877-850-4959, 615-741-7956 | https://sos.tn.gov/contact | Chrome (curl got no text) | "Elections 312 Rosa L. Parks Avenue 7th Floor, Snodgrass Tower Nashville, TN 37243-1102 1-877-850-4959 (615)-741-7956" |
| Arizona | 237 | 1-877-843-8683, 602-542-8683 | https://azsos.gov/elections | Chrome (curl 403) | "Contact Elections Office of the Secretary of State Elections Division 1700 W Washington St Fl 7 Phoenix AZ 85007-2808 602-542-8683 1-877-THE VOTE ... contact the Voter Outreach Coordinator by phone toll-free at 1-877-843-8683" |
| Michigan | 272 | 800-292-5973 | https://www.michigan.gov/sos/elections | curl | "Bureau of Elections contact information Contact us by phone: 800-292-5973 By email: Elections@Michigan.gov Monday through Friday, 8 a.m. - 4:45 p.m. (hours may change during elections and deadline periods)" |
| Virginia | 232 | 800-552-9745, 804-864-8901 | https://www.elections.virginia.gov/contact-us/ | curl | "Department Contact Information Telephone: (804) 864-8901 Toll Free: (800) 552-9745" |
| New Jersey | 346 | 1-877-658-6837 | https://www.nj.gov/state/elections/vote.shtml | curl | "Voting Questions? Call 1-877-NJ-VOTER (1-877-658-6837) ... Voting Information & Assistance 1-877-NJ-VOTER (1-877-658-6837)" |

Notes:
- Louisiana: the hotline only. The parish office that handles Election Day problems (registrar of voters or clerk of
  court) was not read on an official page today, so no local office is named.
- Pennsylvania: the hotline page itself says it is for "problems at their polling place" and has interpreters, so the
  answer uses those words instead of the generic "for anything they can't fix".
- Arizona: the page shows "1-877-THE VOTE"; the same page gives the Voter Outreach line as "1-877-843-8683", the same digits.
- Tennessee and Michigan: numbers without "toll free", since the pages don't say it.


## Batch 2 (the other 32, by impressions)

| State | GSC impr. | Number(s) | Source | Read with | Quote |
|---|---|---|---|---|---|
| South Carolina | 344 | 803-734-9060 | https://scvotes.gov/contact/ | curl | "State Election Commission P.O. Box 5987 Columbia, SC 29250-5987 Main: (803) 734-9060" |
| Utah | 327 | 801-538-1041 | https://vote.utah.gov/additional-resources-for-voters | curl | "Can't find the information you are looking for? Contact the Lieutenant Governor's Office through this form or call 801-538-1041." |
| Colorado | 293 | 303-894-2200 | https://www.coloradosos.gov/pubs/info_center/contact.html | curl | "Elections & Voting 303-894-2200 303-869-4861 (FAX) elections@coloradosos.gov" |
| Minnesota | 283 | 651-215-1440, 1-877-600-8683 | https://www.sos.mn.gov/ | Chrome (curl blocked or empty) | "ELECTIONS PHONE NUMBERS Metro Area: 651-215-1440 Greater MN: 1-877-600-VOTE (8683)" |
| Massachusetts | 244 | 1-800-462-8683, 617-727-2828 | https://www.sec.state.ma.us/divisions/elections/elections-and-voting.htm | Chrome (curl blocked or empty) | "Secretary of the Commonwealth of Massachusetts Elections Division 1 Ashburton Place Room 1705 Boston, MA 02108 1-800-462-VOTE (8683) 617-727-2828" |
| New Mexico | 218 | 1-800-477-3632, 505-827-3600 | https://www.sos.nm.gov/contact-us/ | curl | "Election Day Voter Hotline Toll Free: 1-800-477-3632 Santa Fe: 505-827-3600 Option 2 The Election Day Voter Hotline is available on Election Day ... to assist voters with questions or concerns." |
| Wisconsin | 160 | 1-866-868-3947, 608-266-8005 | https://elections.wi.gov/contact-us | Chrome (curl blocked or empty) | "For general inquiries, call us at (608) 266-8005. ... Call us toll-free at 1-866-VOTE-WIS" |
| Kansas | 152 | 800-262-8683 | https://sos.ks.gov/elections/elections.html | curl | "Elections Division 800-262-VOTE(8683) election@ks.gov" |
| Alabama | 147 | 1-800-274-8683 | https://www.sos.alabama.gov/alabama-votes | curl | "Alabama Votes Secretary of State Wes Allen Call Toll Free: 1-800-274-8683" |
| Hawaii | 142 | 808-453-8683, 1-800-442-8683 | https://elections.hawaii.gov/about-us/contact-us/ | curl | "Office of Elections 802 Lehua Avenue Pearl City, Hawaii 96782 Phone (808) 453-VOTE (8683) Neighbor Island Toll Free 1-800-442-VOTE (8683)" |
| Indiana | 140 | 317-232-3939 | https://www.in.gov/sos/elections/ | curl | "THE NEXT INDIANA GENERAL ELECTION DAY WILL BE ON TUESDAY, NOVEMBER 3, 2026 Please email us at elections@iec.in.gov or call us at 317-232-3939." |
| Missouri | 140 | 800-669-8683, 573-751-2301 | https://www.sos.mo.gov/elections | curl | "Elections & Voting Phone: (573) 751-2301 Toll Free: (800) 669-8683" |
| Connecticut | 132 | 860-509-6100 | https://portal.ct.gov/SOTS/Election-Services/Voter-Information/Voter-Fact-Sheet | curl | "Legislation and Elections Administration 860-509-6100 Lead@ct.gov" |
| Oklahoma | 130 | 405-521-2391 | https://oklahoma.gov/elections/about-us/contact-us.html | curl | "Mailing Address: PO Box 53156 Oklahoma City, OK 73152-3156 Local: (405) 521-2391" |
| Nevada | 126 | 775-684-5708 | https://www.nvsos.gov/elections | Chrome (curl blocked or empty) | "Contact Us 101 N Carson Street Suite 3 Carson City, NV 89701 Ph: (775) 684-5708" |
| Arkansas | 120 | 501-682-5070, 800-482-1127 | https://www.sos.arkansas.gov/about-the-office/contact-us | curl | "Elections Division Phone: 501-682-5070 Toll free outside the Little Rock area: (800) 482-1127 Email: electionsemail@sos.arkansas.gov" |
| District of Columbia | 97 | 202-727-2525, 1-866-328-6837 | https://www.dcboe.org/ | curl | "DC Board of Elections 1015 Half Street, SE, Suite 750 Washington, DC 20003 Monday to Friday 8:15 AM to 4:45 PM Tel: (202) 727-2525 Tollfree: 1-866-DC-VOTES" |
| Idaho | 85 | 208-334-2852 | https://voteidaho.gov/ | curl | "Capitol Building East Wing, Second Floor 700 W Jefferson, #E205 Boise, ID 83702 Hours Monday – Friday 8:00 a.m. – 5:00 p.m. Contact (208) 334-2852" |
| Nebraska | 78 | 402-471-2554 | https://sos.nebraska.gov/elections | curl | "Nebraska Secretary of State Phone 402.471.2554 Fax 402.471.3237 Mailing Address P.O. Box 94608 Lincoln, NE 68509-4608" |
| Kentucky | 77 | 502-573-7100 | https://elect.ky.gov/About-Us/Pages/Contact-Us.aspx | curl | "140 Walnut Street Frankfort, Kentucky 40601 502-573-7100 Hours: 8:00 a.m. – 4:30 p.m. EST State Board of Elections" |
| Maine | 76 | 207-624-7650 | https://www.maine.gov/sos/cec/elec/upcoming/index.html | curl | "Questions about a municipality's local elections or town meetings should be directed to the municipal clerk. Contact the Elections Division 207-624-7650 cec.elections@maine.gov" |
| Mississippi | 65 | 800-829-6786 | https://www.sos.ms.gov/elections-voting | curl | "Contact Elections & Voting 401 Mississippi Street Jackson, MS 39201 P.O. Box 136 Jackson, MS 39205- 0136 Hotline: 800-829-6786 Call Center: 601-576-2550" |
| Rhode Island | 65 | 401-222-2340 | https://vote.sos.ri.gov/DataInformation/ContactUs | Chrome (curl blocked or empty) | "Rhode Island Department of State Elections Division 148 West River St. Providence, RI 02904 401.222.2340 elections@sos.ri.gov" |
| Montana | 56 | 406-444-9608 | https://sosmt.gov/contact-feedback/ | Chrome (curl blocked or empty) | "Elections & Voter Services Questions regarding voter services and elections: Phone: 406-444-9608 Email: soselections@mt.gov" |
| Vermont | 47 | 802-828-2363 | https://sos.vermont.gov/elections/ | curl | "Elections Division 128 State Street Montpelier, VT 05633 Office Hours: 7:45 to 4:30, Monday - Friday (Except Holidays) 802-828-2363" |
| Iowa | 46 | 1-888-767-8683 | https://sos.iowa.gov/voter-hotline | Chrome (curl blocked or empty) | "If you have a pertinent voting related question or if you witness questionable activity related to an election, please fill out the Voter Hotline Form below. You may also call our Voter Hotline at 1-888-SOS-VOTE." |
| West Virginia | 38 | 304-558-6000 | https://sos.wv.gov/contact-wvsos | Chrome (curl blocked or empty) | "Call Centers Business & Licensing Call Center (304) 558-8000 Secretary of State Call Center (304) 558-6000" |
| Delaware | 37 | 302-577-3464, 302-739-4498, 302-856-5367, 302-739-4277 | https://elections.delaware.gov/locations.html | curl | "State Election Commissioner ... Telephone: (302) 739-4277 ... Kent County Office ... Phone: (302) 739-4498 ... 820 North French St Suite 400 Wilmington DE 19801 ... Phone: (302) 577-3464 ... 119 N. Race St P.O. Box 457 Georgetown DE 19947 ... Phone: (302) 856-5367" |
| New Hampshire | 29 | 603-271-3242 | https://www.sos.nh.gov/elections/election-integrity | Chrome (curl blocked or empty) | "If you have witnessed efforts to commit any kind of fraud or corruption in the voting process, you may report this to: The Moderator at your polling place ... The Secretary of State's Office at 603-271-3242 or elections@sos.nh.gov" |
| Wyoming | 23 | 307-777-5860 | https://sos.wyo.gov/ContactUs.aspx | curl | "Election Division Herschler Building East 122 West 25th Street Suite 100 Cheyenne, WY 82002-0020 Ph. 307.777.5860 Email: Elections@wyo.gov" |
| South Dakota | 21 | 605-773-3537 | https://sdsos.gov/about-the-office/assets/Press%20Releases/2026/2026VoterRegistrationDeadlinePrimaryreminder.pdf | curl (pdftotext) | "500 EAST CAPITOL AVENUE, PIERRE, SD 57501-5070 | TELEPHONE: (605) 773-3537 | FAX: (605) 773-6580" |
| North Dakota | 10 | 1-800-352-0867, 701-328-2900 | https://www.sos.nd.gov/elections | curl | "North Dakota Office of the Secretary of State 600 E Boulevard Avenue Dept 108 | Bismarck, ND 58505 Phone: 701-328-2900 | 800-352-0867" |

Notes:
- No state fell back to a county office alone: every state's own office lists a phone on its own page. Where the local
  office is the first stop (Hawaii, like Oregon and Washington in pass 12), the answer says to call the county first.
- Delaware runs elections through state-run county offices, so the answer lists all three county numbers from the
  Department of Elections' locations page, plus the State Election Commissioner.
- Alabama and Louisiana: state line only. The county office that handles Election Day problems was not read on an
  official page, so none is named.
- New Mexico: the Election Day Voter Hotline line on sos.nm.gov reads "available on Election Day, November 2, from
  7:00 AM to 7:00 PM". The date is the office's typo (Election Day is November 3), so the answer gives the numbers only.
- Connecticut: the SEEC Election Day hotline (1-866-733-2463) was announced only for the August 11 primary; no
  November release yet (press releases read through Oct 31). The answer uses the Secretary of the State's elections
  division line from the Voter Fact Sheet instead. If a November hotline release appears, swap it in.
- South Dakota: no phone in the sdsos.gov HTML pages (script-rendered); the number is on the office's own 2026 press
  release letterhead (May 18, 2026), read with pdftotext.
- Nebraska, Nevada, Idaho, North Dakota, West Virginia, South Dakota: the office's main or call-center line, the
  only voter phone the office lists. Utah: the Lieutenant Governor's Office, which runs Utah elections, as vote.utah.gov
  gives it.
- Vanity numbers spelled out from the letters (1-800-462-VOTE = 1-800-462-8683, 1-866-VOTE-WIS = 1-866-868-3947,
  1-866-DC-VOTES = 1-866-328-6837, 1-888-SOS-VOTE = 1-888-767-8683); the letters stay in the answer as the office
  shows them.
- Spanish: no state office page read here lists a separate Spanish voter line, so none is given (Pennsylvania offers
  interpreters on its hotline, which the answer says).
- Leads checked but not used: Connecticut SEEC hotline (primary only), NH Attorney General election line (the SOS line
  is the elections office), WV fraud hotline 877-372-8398 (fraud only), Iowa 515-281-0145 (not on the page read).
