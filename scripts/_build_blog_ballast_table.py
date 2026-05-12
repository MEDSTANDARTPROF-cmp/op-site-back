"""
Собирает читаемую HTML-таблицу всех 299 статей-балласта блога.
Отсортировано по визитам убывая. Колонки: URL (кликабельный), визиты, отказ,
время на странице, глубина, заявки, причина.

Сохраняет: ОБРПРОФИ_АНАЛИТИКА/ОТЧЁТ_БЛОГ_БАЛЛАСТ.html
"""
import csv, sys, html
sys.stdout.reconfigure(encoding='utf-8')

ROOT = 'h:/666/TEST/SITE/ObrProfi_FULL/'

with open(ROOT+'temp_back/blog_audit/blog_балласт.csv', encoding='utf-8', errors='replace') as f:
    rows = list(csv.DictReader(f))

rows.sort(key=lambda r: int(r.get('Visits', 0)), reverse=True)

total_visits = sum(int(r['Visits']) for r in rows)
total_subs = sum(int(r.get('FormSubmit',0)) for r in rows) + sum(int(r.get('Shems',0)) for r in rows)
zero_v = sum(1 for r in rows if int(r['Visits']) == 0)
one_v = sum(1 for r in rows if int(r['Visits']) == 1)
mid_v = sum(1 for r in rows if 2 <= int(r['Visits']) <= 5)
hi_v = sum(1 for r in rows if int(r['Visits']) > 5)

def bounce_class(b):
    b = float(b)
    if b < 30: return 'bn-low'
    if b < 60: return 'bn-med'
    return 'bn-high'

table_rows = []
for r in rows:
    url = r['URL']
    visits = r['Visits']
    bounce = r['Bounce%']
    t = r['AvgTime']
    depth = r['PageDepth']
    subs = int(r.get('FormSubmit',0)) + int(r.get('Shems',0))
    reason = r.get('Reason','').strip('"')
    # decode mojibake in Reason if any
    try:
        reason = reason.encode('cp1252', errors='ignore').decode('utf-8', errors='ignore') if '�' in reason else reason
    except: pass
    table_rows.append(f"""
<tr>
  <td><a href="https://obrprofi.ru{html.escape(url)}" target="_blank">{html.escape(url)}</a></td>
  <td class="num">{visits}</td>
  <td class="num {bounce_class(bounce)}">{bounce}%</td>
  <td class="num">{t}s</td>
  <td class="num">{depth}</td>
  <td class="num">{subs}</td>
  <td class="reason">{html.escape(reason)}</td>
</tr>""")

out = f"""<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>БАЛЛАСТ блога — все 299 статей</title>
<style>
body {{ font-family: -apple-system, "Segoe UI", Roboto, sans-serif; max-width: 1400px; margin: 30px auto; padding: 0 20px; color: #1a1a2e; line-height: 1.5; }}
h1 {{ color: #0a3d62; border-bottom: 3px solid #16a085; padding-bottom: 8px; }}
.summary {{ background: #f0f7f5; padding: 16px 22px; border-left: 4px solid #16a085; border-radius: 4px; margin: 20px 0; }}
.summary p {{ margin: 6px 0; }}
.summary b {{ color: #0a3d62; }}
table {{ width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }}
th {{ background: #0a3d62; color: #fff; padding: 10px 8px; text-align: left; position: sticky; top: 0; z-index: 10; }}
td {{ padding: 7px 8px; border-bottom: 1px solid #eee; vertical-align: top; }}
tr:nth-child(even) td {{ background: #f7f8fa; }}
td.num {{ text-align: right; font-variant-numeric: tabular-nums; }}
td.reason {{ color: #555; font-size: 12px; max-width: 360px; }}
.bn-low {{ background: #d4edda !important; }}
.bn-med {{ background: #fff3cd !important; }}
.bn-high {{ background: #f8d7da !important; }}
a {{ color: #0a3d62; text-decoration: none; }}
a:hover {{ text-decoration: underline; }}
.controls {{ margin: 10px 0; }}
.controls input {{ padding: 8px 12px; font-size: 14px; border: 1px solid #d4dbe4; border-radius: 4px; width: 320px; }}
</style>
</head>
<body>
<h1>БАЛЛАСТ блога — все 299 статей</h1>
<p style="color:#666;">Период данных: 7 февраля – 6 мая 2026 (90 дней) · Метрика 75081295</p>

<div class="summary">
<p><b>Всего балластных статей:</b> 299</p>
<p><b>Суммарно визитов за 90 дней:</b> {total_visits} (≈{total_visits//90} визитов/день на 299 статей суммарно)</p>
<p><b>Заявок:</b> {total_subs}</p>
<p><b>Распределение по визитам:</b> 1 визит — {one_v} статей · 2-5 — {mid_v} · 6+ — {hi_v}</p>
<p><b>Доля от трафика блога:</b> {(total_visits/11545*100):.1f}% (всего блог 11 545 визитов за 90 дней)</p>
<p><b>Критерии попадания в балласт:</b> &lt;7 визитов за 90 дней <i>И</i> 0 заявок <i>И</i> (отказы &gt;60% <i>ИЛИ</i> время &lt;30 сек)</p>
</div>

<div class="controls">
<input type="text" id="filter" placeholder="Фильтр по URL (введите часть пути)..." onkeyup="filterTable()">
</div>

<table id="ballast">
<thead>
<tr>
  <th>URL</th>
  <th>Визиты</th>
  <th>Отказы</th>
  <th>Время</th>
  <th>Глубина</th>
  <th>Заявки</th>
  <th>Причина балласта</th>
</tr>
</thead>
<tbody>
{''.join(table_rows)}
</tbody>
</table>

<script>
function filterTable() {{
  var q = document.getElementById('filter').value.toLowerCase();
  var rows = document.querySelectorAll('#ballast tbody tr');
  rows.forEach(r => {{
    var a = r.querySelector('a');
    r.style.display = a && a.href.toLowerCase().includes(q) ? '' : 'none';
  }});
}}
</script>
</body>
</html>
"""

out_path = ROOT + 'ОБРПРОФИ_АНАЛИТИКА/ОТЧЁТ_БЛОГ_БАЛЛАСТ.html'
with open(out_path, 'w', encoding='utf-8') as f:
    f.write(out)
print(f"Saved: {out_path}")
print(f"Articles: {len(rows)} | Total visits: {total_visits} | Total subs: {total_subs}")
