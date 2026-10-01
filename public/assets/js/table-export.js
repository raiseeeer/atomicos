/**
 * Atomicos Table Export: CSV, Excel (.xlsx), PDF and Word (.doc) from any table.
 * Libraries are loaded from a CDN only when needed (SheetJS for Excel, jsPDF + AutoTable for PDF).
 *
 * BUTTON    <a href="#" data-export-as="csv|xlsx|pdf|doc" data-export-target="#dataTable">
 * TABLE     data-export-title="Employees"       title shown in PDF/Word and the sheet name
 *           data-export-filename="employees"    file name (the date is added automatically)
 *           data-export-url="api/x/export.php"  SERVER MODE: fetch ALL records from the API (see below)
 * HEADERS   <th data-no-export>                 leave this column out (e.g. # and Actions)
 *           <th data-sort="number|date|text">   column type (same attribute the sort plugin uses)
 *           <th data-key="col" ...>             SERVER MODE: the field name in the API rows
 *           <th data-decimals="2">              optional decimals for number columns in PDF/Word
 * CELLS     value used = data-export-value, else data-value, else the cell text
 *
 * CLIENT MODE (no data-export-url): exports every row currently in the table body.
 * SERVER MODE: exports ALL matching records, not just the current page. Your module tells the plugin
 *   which filters are active:   $('#dataTable').data('exportParams', () => ({ search: ..., sort: ..., dir: ... }));
 */
(function ($) {
  const CDN = {
    xlsx: ['https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js'],
    pdf:  ['https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js',
           'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js']
  };
  const loaded = {};
  const ISO = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/;
  const clean = s => String(s ?? '').replace(/\s+/g, ' ').trim();
  const pad   = n => String(n).padStart(2, '0');
  const isoDate = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  const slug  = s => clean(s).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'export';
  const escHtml = s => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

  function loadScript(src) {
    return new Promise((resolve, reject) => {
      if (loaded[src]) return resolve();
      const s = document.createElement('script');
      s.src = src;
      s.onload = () => { loaded[src] = true; resolve(); };
      s.onerror = () => reject(new Error('Could not load an export library. Check your internet connection.'));
      document.head.appendChild(s);
    });
  }
  async function loadLibs(list) { for (const src of list) await loadScript(src); }   // in order

  function download(blob, filename) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 500);
  }

  // ---------- 1. READ THE DATA ----------
  function normalize(v, type) {
    if (v === null || v === undefined) return type === 'number' ? null : '';
    if (type === 'number') { const n = parseFloat(String(v).replace(/[^0-9.\-]/g, '')); return isNaN(n) ? null : n; }
    if (type === 'date') {
      const s = clean(v);
      if (ISO.test(s)) return s.replace('T', ' ');
      const t = Date.parse(s);
      return isNaN(t) ? s : isoDate(new Date(t));
    }
    return clean(v);
  }

  function getColumns($t, server) {
    const cols = [];
    $t.find('thead th').each(function (i) {
      const $th = $(this);
      if ($th.is('[data-no-export]')) return;
      const key = $th.data('key');
      if (server && !key) return;                       // server mode needs a field name
      cols.push({ idx: i, key, label: clean($th.text()), type: $th.data('sort') || 'text', decimals: $th.data('decimals') });
    });
    return cols;
  }

  function readClientRows($t, cols) {
    const rows = [];
    $t.find('tbody tr').each(function () {
      const $tds = $(this).children();
      if ($tds.length < 2 || $(this).css('display') === 'none') return;      // skip "no records" and hidden rows
      rows.push(cols.map(c => {
        const $td = $tds.eq(c.idx);
        let v = $td.attr('data-export-value');
        if (v === undefined) v = $td.attr('data-value');
        if (v === undefined) v = $td.text();
        return normalize(v, c.type);
      }));
    });
    return rows;
  }

  // work out decimals / time-part per column once we know the data
  function finalize(cols, rows) {
    cols.forEach((c, j) => {
      const vals = rows.map(r => r[j]);
      if (c.type === 'number' && c.decimals === undefined) c.decimals = vals.some(v => v !== null && !Number.isInteger(v)) ? 2 : 0;
      if (c.type === 'date') c.time = vals.some(v => { const m = ISO.exec(v || ''); return m && m[4] && (m[4] !== '00' || m[5] !== '00'); });
    });
  }

  // human-friendly text (used by PDF and Word)
  function display(v, c) {
    if (c.type === 'number') return v === null ? '' : v.toLocaleString('en-PH', { minimumFractionDigits: c.decimals, maximumFractionDigits: c.decimals });
    if (c.type === 'date') {
      const m = ISO.exec(v || '');
      if (!m) return v || '';
      const d = new Date(+m[1], m[2] - 1, +m[3], +(m[4] || 0), +(m[5] || 0));
      return d.toLocaleDateString('en-PH', { month: 'short', day: '2-digit', year: 'numeric' }) +
             (c.time ? ' ' + d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' }) : '');
    }
    return v;
  }

  // ---------- 2. THE FOUR EXPORTERS ----------
  function exportCSV(cols, rows, file) {
    const cell = (v, text) => {
      if (v === null || v === '') return '';
      let s = String(v);
      if (text && /^[=+\-@]/.test(s)) s = "'" + s;                  // blocks spreadsheet formula injection
      return /[",\r\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
    };
    const lines = [cols.map(c => cell(c.label, false)).join(',')]
      .concat(rows.map(r => r.map((v, j) => cell(v, cols[j].type === 'text')).join(',')));
    download(new Blob(['\uFEFF' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8' }), file + '.csv');   // BOM: Excel reads UTF-8
  }

  async function exportXLSX(cols, rows, file, title) {
    await loadLibs(CDN.xlsx);
    const toSerial = v => {                                          // ISO date -> real Excel date number
      const m = ISO.exec(v || '');
      return m ? Date.UTC(+m[1], m[2] - 1, +m[3], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0)) / 86400000 + 25569 : v;
    };
    const aoa = [cols.map(c => c.label)].concat(rows.map(r => r.map((v, j) =>
      cols[j].type === 'date' && v ? toSerial(v) : (v === '' ? null : v))));
    const ws = XLSX.utils.aoa_to_sheet(aoa);

    rows.forEach((r, i) => cols.forEach((c, j) => {
      const cell = ws[XLSX.utils.encode_cell({ r: i + 1, c: j })];
      if (!cell) return;
      if (c.type === 'date' && typeof cell.v === 'number') cell.z = c.time ? 'yyyy-mm-dd hh:mm' : 'yyyy-mm-dd';
      if (c.type === 'number') cell.z = c.decimals ? '#,##0.' + '0'.repeat(c.decimals) : '#,##0';
    }));
    ws['!cols'] = cols.map((c, j) => ({ wch: Math.min(40, Math.max(c.label.length, ...rows.map(r => String(display(r[j], c)).length)) + 2) }));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, title.replace(/[\\\/?*\[\]:]/g, '').substring(0, 31) || 'Sheet1');
    XLSX.writeFile(wb, file + '.xlsx');
  }

  async function exportPDF(cols, rows, file, title) {
    await loadLibs(CDN.pdf);
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: cols.length > 6 ? 'landscape' : 'portrait', unit: 'pt', format: 'a4' });
    const W = doc.internal.pageSize.getWidth(), H = doc.internal.pageSize.getHeight();

    doc.setFont('helvetica', 'bold'); doc.setFontSize(16); doc.setTextColor(11, 16, 32);
    doc.text(title, 40, 42);
    doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.setTextColor(120);
    doc.text('Generated ' + new Date().toLocaleString('en-PH') + '  |  ' + rows.length + ' record(s)', 40, 58);

    const right = {};
    cols.forEach((c, j) => { if (c.type === 'number') right[j] = { halign: 'right' }; });
    doc.autoTable({
      head: [cols.map(c => c.label)],
      body: rows.map(r => r.map((v, j) => display(v, cols[j]))),
      startY: 72, margin: { left: 40, right: 40, bottom: 40 },
      styles: { fontSize: 9, cellPadding: 5 },
      headStyles: { fillColor: [11, 16, 32], textColor: [45, 226, 192] },
      alternateRowStyles: { fillColor: [244, 246, 251] },
      columnStyles: right
    });
    const n = doc.internal.getNumberOfPages();
    for (let i = 1; i <= n; i++) {                                   // "Page x of y" footer
      doc.setPage(i); doc.setFontSize(8); doc.setTextColor(140);
      doc.text(`Page ${i} of ${n}`, W - 40, H - 20, { align: 'right' });
    }
    doc.save(file + '.pdf');
  }

  // Word: an HTML document saved as .doc (Word opens it as a formatted table, no library needed)
  function exportDOC(cols, rows, file, title) {
    const land = cols.length > 6;
    const th = cols.map(c => `<th style="background:#0B1020;color:#2DE2C0;border:1px solid #999;padding:5px;text-align:left">${escHtml(c.label)}</th>`).join('');
    const body = rows.map((r, i) => '<tr>' + r.map((v, j) =>
      `<td style="border:1px solid #bbb;padding:4px;${cols[j].type === 'number' ? 'text-align:right;' : ''}${i % 2 ? 'background:#F4F6FB;' : ''}">${escHtml(display(v, cols[j]))}</td>`).join('') + '</tr>').join('');
    const html = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="utf-8"><title>${escHtml(title)}</title>
<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View></w:WordDocument></xml><![endif]-->
<style>@page Section1{size:${land ? '841.9pt 595.3pt' : '595.3pt 841.9pt'};mso-page-orientation:${land ? 'landscape' : 'portrait'};margin:36pt}
div.Section1{page:Section1} body{font-family:Calibri,Arial,sans-serif;font-size:10pt} table{border-collapse:collapse;width:100%}</style></head>
<body><div class="Section1"><h2 style="margin:0">${escHtml(title)}</h2>
<p style="color:#777;font-size:9pt">Generated ${escHtml(new Date().toLocaleString('en-PH'))} | ${rows.length} record(s)</p>
<table><thead><tr>${th}</tr></thead><tbody>${body}</tbody></table></div></body></html>`;
    download(new Blob(['\uFEFF' + html], { type: 'application/msword;charset=utf-8' }), file + '.doc');
  }

  // ---------- 3. RUN ----------
  async function run(format, $t, $trigger) {
    const url = $t.data('export-url'), server = !!url;
    const cols = getColumns($t, server);
    const title = $t.data('export-title') || clean($('.atm-page-title').first().text()) || 'Export';
    const file  = ($t.data('export-filename') || slug(title)) + '_' + isoDate(new Date());
    const $tog  = $trigger.closest('.dropdown').find('.dropdown-toggle').prop('disabled', true);
    const label = $tog.html();
    $tog.html('<span class="spinner-border spinner-border-sm me-1"></span>Exporting...');

    try {
      let rows;
      if (server) {
        const params = ($t.data('exportParams') || (() => ({})))();
        const res = await Atomicos.post(url, params);
        rows = res.data.rows.map(r => cols.map(c => normalize(r[c.key], c.type)));
        if (res.data.total > rows.length) Atomicos.toast('Only the first ' + rows.length + ' of ' + res.data.total + ' records were exported.', 'warning');
      } else {
        rows = readClientRows($t, cols);
      }
      if (!rows.length) return Atomicos.toast('Nothing to export.', 'warning');

      finalize(cols, rows);
      if (format === 'csv')  exportCSV(cols, rows, file);
      if (format === 'xlsx') await exportXLSX(cols, rows, file, title);
      if (format === 'pdf')  await exportPDF(cols, rows, file, title);
      if (format === 'doc')  exportDOC(cols, rows, file, title);
      Atomicos.toast('Exported ' + rows.length + ' record(s).');
    } catch (err) {
      if (err instanceof Error) Atomicos.toast(err.message, 'danger');       // (AJAX errors already show a toast)
    } finally {
      $tog.prop('disabled', false).html(label);
    }
  }

  window.TableExport = { run };
  $(document).on('click', '[data-export-as]', function (e) {
    e.preventDefault();
    run($(this).data('export-as'), $($(this).data('export-target')), $(this));
  });
})(jQuery);
