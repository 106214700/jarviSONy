#!/usr/bin/env bash
# check-site.sh — Automated contract & rubric audit for COS10026 Part 1 (jarviSONy)
set -u

PAGES=("index.html" "jobs.html" "apply.html" "about.html")
FAIL=0

pass() {
  echo "PASS: $1"
}

fail() {
  echo "FAIL: $1"
  FAIL=1
}

# 1. Required files exist
for f in "${PAGES[@]}" "styles.css"; do
  if [[ -f "$f" ]]; then
    pass "$f exists"
  else
    fail "$f is missing"
  fi
done

# 2. Pure HTML5 & CSS3 (no <script>, no !important)
for f in "${PAGES[@]}"; do
  if grep -qi '<script' "$f"; then
    fail "$f contains a <script> tag"
  else
    pass "$f has zero <script> tags"
  fi
done

if grep -q '!important' styles.css "${PAGES[@]}"; then
  fail "!important found in CSS or HTML"
else
  pass "Zero !important across styles.css and HTML pages"
fi

# 3. Single H1, skip-link, landmarks, 1 <style> block, >=1 inline style=""
for f in "${PAGES[@]}"; do
  h1_count=$(grep -oi '<h1[ >]' "$f" | wc -l | tr -d ' ')
  if [[ "$h1_count" -eq 1 ]]; then
    pass "$f has exactly one <h1>"
  else
    fail "$f has $h1_count <h1> elements (expected 1)"
  fi

  style_block_count=$(grep -oi '<style>' "$f" | wc -l | tr -d ' ')
  if [[ "$style_block_count" -eq 1 ]]; then
    pass "$f has exactly one embedded <style> block"
  else
    fail "$f has $style_block_count <style> blocks (expected 1)"
  fi

  inline_style_count=$(grep -oi 'style="' "$f" | wc -l | tr -d ' ')
  if [[ "$inline_style_count" -ge 1 ]]; then
    pass "$f has $inline_style_count inline style attribute(s)"
  else
    fail "$f has zero inline style attributes"
  fi

  for landmark in '<header' '<nav' '<main id="main"' '<section' '<footer'; do
    if grep -q "$landmark" "$f"; then
      pass "$f contains $landmark landmark"
    else
      fail "$f is missing $landmark landmark"
    fi
  done
done

# 4. Byte-for-byte identical <header> (modulo aria-current="page") and <footer>
extract_header() {
  sed -n '/<header class="site-header">/,/<\/header>/p' "$1" | tr -d '\r' | sed 's/ aria-current="page"//g'
}

extract_footer() {
  sed -n '/<footer class="site-footer">/,/<\/footer>/p' "$1" | tr -d '\r'
}

ref_header=$(extract_header "index.html")
ref_footer=$(extract_footer "index.html")

for f in "${PAGES[@]}"; do
  cur_header=$(extract_header "$f")
  if [[ "$cur_header" == "$ref_header" ]]; then
    pass "$f <header> matches index.html"
  else
    fail "$f <header> differs from index.html"
  fi

  cur_footer=$(extract_footer "$f")
  if [[ "$cur_footer" == "$ref_footer" ]]; then
    pass "$f <footer> matches index.html byte-for-byte"
  else
    fail "$f <footer> differs from index.html"
  fi
done

# 5. Image existence and <= 300 KB size check for all referenced images
referenced_imgs=$(grep -oh 'images/[A-Za-z0-9_.-]*' "${PAGES[@]}" | sort -u)
for img in $referenced_imgs; do
  if [[ -f "$img" ]]; then
    size=$(wc -c < "$img" | tr -d ' ')
    if [[ "$size" -le 307200 ]]; then
      pass "$img exists and is under 300 KB ($size bytes)"
    else
      fail "$img exceeds 300 KB ($size bytes)"
    fi
  else
    fail "Referenced image $img does not exist"
  fi
done

# 6. About page & Section 10 specific checks
dt_count=$(grep -c '<dt>' about.html | tr -d ' ')
dd_count=$(grep -c '<dd>' about.html | tr -d ' ')
if [[ "$dt_count" -eq 4 && "$dd_count" -eq 8 ]]; then
  pass "about.html <dl> has 4 <dt> and 8 <dd> elements"
else
  fail "about.html <dl> has $dt_count <dt> and $dd_count <dd> (expected 4 and 8)"
fi

for sel in 'dt + dd' 'dd ~ dd' 'blockquote\[lang="vi"\]' '\.about-id::before' 'tr:nth-child(even)' 'tr:hover'; do
  if grep -Eq "$sel" styles.css; then
    pass "styles.css Section 10 includes selector matching $sel"
  else
    fail "styles.css Section 10 missing selector matching $sel"
  fi
done

echo "----------------------------------------"
if [[ "$FAIL" -eq 0 ]]; then
  echo "ALL CHECKS PASSED"
  exit 0
else
  echo "SOME CHECKS FAILED"
  exit 1
fi
