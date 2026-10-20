#!/bin/zsh
# Put a finished Codex set live: retire the state's current ready-mades to originals/<abbr>/retired-<date>/,
# copy the kept images from incoming/codex/<abbr>/review-claude/ (renumbered 1..N), name the hero,
# rebuild, deploy, QA, ping IndexNow, commit and push.
#   build/install_set.sh <abbr> <slug> <hero N after renumbering> <kept review numbers...>
#   build/install_set.sh al alabama 6 1 2 3 4 5 6 7 8 9 10
set -e
cd "${0:A:h}/.."
abbr=$1 slug=$2 hero=$3; shift 3
R=incoming/codex/$abbr/review-claude
day=$(date +%Y%m%d)
mkdir -p originals/$abbr/retired-$day originals/$abbr/fixed
if ls site/assets/states/$abbr/*.jpg >/dev/null 2>&1; then
  cp site/assets/states/$abbr/*.jpg originals/$abbr/retired-$day/
  git rm -q --ignore-unmatch site/assets/states/$abbr/*.jpg
  rm -f site/assets/states/$abbr/*.jpg
fi
mkdir -p site/assets/states/$abbr
n=1
for i in "$@"; do cp $R/$abbr-vote-$i.jpg site/assets/states/$abbr/$abbr-vote-$n.jpg; n=$((n+1)); done
cp $R/fixed/*.png originals/$abbr/fixed/ 2>/dev/null || true
up=${(U)abbr}
python3 - "$up" "$hero" <<'EOF'
import re, sys
up, hero = sys.argv[1], sys.argv[2]
p = 'site/index.php'; s = open(p).read()
m = re.search(r"const VOTE_HERO = array\( (.*?) \);", s)
items = [x for x in m.group(1).split(', ') if not x.startswith(f"'{up}'")] + [f"'{up}' => {hero}"]
s = s[:m.start(1)] + ', '.join(items) + s[m.end(1):]
open(p, 'w').write(s)
EOF
python3 -I build/build_static.py | tail -1
wrangler pages deploy dist --project-name votesticker --branch main --commit-dirty=true 2>&1 | tail -1
sleep 6
python3 -I scripts/qa_seo.py --no-legacy | tail -1
curl -s https://votesticker.org/$slug/ | grep -o 'Save one of [0-9]* ready-made' | head -1
curl -s -m 30 -o /dev/null -w "IndexNow %{http_code}\n" -H "Content-Type: application/json; charset=utf-8" \
  -d "{\"host\":\"votesticker.org\",\"key\":\"f691b1b3d7674b378f82c72b1b27f5d8\",\"keyLocation\":\"https://votesticker.org/f691b1b3d7674b378f82c72b1b27f5d8.txt\",\"urlList\":[\"https://votesticker.org/$slug/\"]}" \
  https://api.indexnow.org/indexnow
git add site/assets/states/$abbr site/index.php build/lastmod.json
git commit -q -m "${(C)slug//-/ }: $(($n-1)) new ready-mades from Codex; retire the earlier set

Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>
Claude-Session: https://claude.ai/code/session_01U4jJBSt3CnGjJYrtPzNn5q"
git push -q
git log --oneline -1
