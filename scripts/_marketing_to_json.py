"""Parse the marketing row + TVs from prod backup into a JSON dump for PHP restore."""
import re, sys, json
sys.stdout.reconfigure(encoding='utf-8')

ROW_FILE = 'h:/666/TEST/SITE/ObrProfi_FULL/scripts/_marketing_row.txt'
TV_FILE  = 'h:/666/TEST/SITE/ObrProfi_FULL/scripts/_marketing_tvs.txt'
SQL = 'h:/666/TEST/SITE/ObrProfi_FULL/backup/backup_prod_20260506_195358.sql'
OUT = 'h:/666/TEST/SITE/ObrProfi_FULL/scripts/_marketing.json'

# Get column order
with open(SQL, encoding='utf-8', errors='ignore') as f:
    cap = False
    block = []
    for line in f:
        if 'CREATE TABLE `modx_site_content`' in line:
            cap = True
        if cap:
            block.append(line)
            if line.strip().startswith(') ENGINE'):
                break
cols = []
for L in block:
    m = re.match(r'\s*`([^`]+)`', L)
    if m:
        cols.append(m.group(1))

def split_top(s):
    parts = []
    cur = []
    depth = 0
    in_str = False
    esc = False
    BS = chr(92); SQ = chr(39)
    for c in s:
        if esc:
            cur.append(c); esc = False; continue
        if c == BS:
            cur.append(c); esc = True; continue
        if c == SQ and not esc:
            in_str = not in_str
            cur.append(c); continue
        if in_str:
            cur.append(c); continue
        if c == '(':
            depth += 1; cur.append(c); continue
        if c == ')':
            depth -= 1; cur.append(c); continue
        if c == ',' and depth == 0:
            parts.append(''.join(cur).strip())
            cur = []; continue
        cur.append(c)
    if cur:
        parts.append(''.join(cur).strip())
    return parts

def unquote_sql(s):
    s = s.strip()
    if s == 'NULL':
        return None
    if s.startswith("'") and s.endswith("'"):
        s = s[1:-1]
        # Unescape standard mysql escapes
        s = s.replace("\\\\", chr(0))  # placeholder for literal backslash
        s = s.replace("\\'", "'")
        s = s.replace('\\"', '"')
        s = s.replace("\\n", "\n")
        s = s.replace("\\r", "\r")
        s = s.replace("\\t", "\t")
        s = s.replace(chr(0), "\\")
        return s
    if re.match(r'^-?\d+$', s):
        return int(s)
    if re.match(r'^-?\d+\.\d+$', s):
        return float(s)
    return s

row = open(ROW_FILE, encoding='utf-8').read()
inner = row[1:-1]
fields = split_top(inner)
data = {}
for col, val in zip(cols, fields):
    data[col] = unquote_sql(val)
print("Resource fields parsed: " + str(len(data)))

# TVs
tv_data = []
with open(TV_FILE, encoding='utf-8') as f:
    for L in f:
        L = L.strip()
        if not L: continue
        inner = L[1:-1]
        parts = split_top(inner)
        if len(parts) >= 4:
            tv_data.append({
                'tmplvarid': int(parts[1]),
                'value': unquote_sql(parts[3]),
            })
print("TV values parsed: " + str(len(tv_data)))

with open(OUT, 'w', encoding='utf-8') as f:
    json.dump({'resource': data, 'tvs': tv_data}, f, ensure_ascii=False, indent=2)
print("Saved to " + OUT)
print("\nRESOURCE PREVIEW:")
print("  pagetitle:", data.get('pagetitle'))
print("  alias:", data.get('alias'))
print("  parent:", data.get('parent'))
print("  template:", data.get('template'))
print("  class_key:", data.get('class_key'))
print("  uri:", data.get('uri'))
print("  content length:", len(data.get('content', '') or ''))
print("\nTVs:")
for tv in tv_data:
    v = tv['value']
    if isinstance(v, str):
        v = v[:80].replace('\n',' ')
    print("  tmplvarid=" + str(tv['tmplvarid']) + " value=" + str(v))
