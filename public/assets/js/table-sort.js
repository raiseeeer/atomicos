/**
 * Atomicos Table Sort: click a header to sort (asc <-> desc). Handles text, numbers and dates.
 *
 * MARKUP
 *   <table class="table table-sortable">                     client mode (sorts the rows on the page)
 *   <table class="table table-sortable" data-sort-mode="server">   server mode (fires an event, you reload from the API)
 *   <th data-sort="text|number|date" data-key="column_name">  sortable header (no data-sort = not sortable)
 *   <th ... data-sort-default="asc">                          shows the starting sort state
 *   <td data-value="45000">PHP 45,000.00</td>                 real value when the display text differs
 *   <td class="row-num">1</td>                                renumbered after each client sort
 *
 * SERVER MODE: listen with  $('#dataTable').on('atm:sort', function (e, key, dir) { ... });
 */
(function ($) {
  // one-time styles so this file is fully self-contained
  if (!document.getElementById('atm-sort-css')) {
    $('head').append(
      '<style id="atm-sort-css">' +
      'th[data-sort]{cursor:pointer;user-select:none;white-space:nowrap}' +
      'th[data-sort]:hover{background:rgba(45,226,192,.12)}' +
      'th[data-sort] .sort-icon{margin-left:6px;font-size:.7rem;opacity:.35;transition:opacity .15s}' +
      'th[data-sort][aria-sort="ascending"] .sort-icon,th[data-sort][aria-sort="descending"] .sort-icon{opacity:1;color:#0FBFA0}' +
      '</style>'
    );
  }

  const ICON = { none: 'bi-chevron-expand', asc: 'bi-caret-up-fill', desc: 'bi-caret-down-fill' };

  // Read a comparable value from a cell. Empty / invalid values return null (always sorted last).
  function cellValue($td, type) {
    let raw = $td.attr('data-value');
    if (raw === undefined) raw = $.trim($td.text());
    raw = String(raw).trim();
    if (raw === '') return null;

    if (type === 'number') {                         // "₱45,000.50" -> 45000.5
      const n = parseFloat(raw.replace(/[^0-9.\-]/g, ''));
      return isNaN(n) ? null : n;
    }
    if (type === 'date') {                           // "2024-01-15" or "Jan 15, 2024" -> timestamp
      const d = Date.parse(raw.replace(' ', 'T'));
      return isNaN(d) ? null : d;
    }
    return raw;                                      // text
  }

  function setIndicator($t, $th, dir) {
    $t.find('thead th[data-sort]').attr('aria-sort', 'none')
      .find('.sort-icon').attr('class', 'bi ' + ICON.none + ' sort-icon');
    $th.attr('aria-sort', dir === 'asc' ? 'ascending' : 'descending')
       .find('.sort-icon').attr('class', 'bi ' + ICON[dir] + ' sort-icon');
  }

  function sortRows($t, $th, dir) {
    const idx = $th.index(), type = $th.data('sort'), mult = dir === 'asc' ? 1 : -1;
    const $body = $t.find('tbody');
    const items = $body.children('tr').filter(function () { return $(this).children().length > 1; }) // skip "no records" row
      .get().map((tr, i) => ({ tr, i, v: cellValue($(tr).children().eq(idx), type) }));

    items.sort((a, b) => {
      if (a.v === null || b.v === null) return a.v === b.v ? a.i - b.i : (a.v === null ? 1 : -1); // blanks last
      const c = type === 'text' ? String(a.v).localeCompare(String(b.v), undefined, { numeric: true, sensitivity: 'base' })
                                : a.v - b.v;
      return c ? c * mult : a.i - b.i;               // ties keep their original order
    });

    items.forEach(it => $body.append(it.tr));
    $t.find('.row-num').each((i, el) => { el.textContent = i + 1; });
  }

  function init(table) {
    const $t = $(table), server = $t.data('sort-mode') === 'server';
    const $heads = $t.find('thead th[data-sort]');
    if (!$heads.length) return;

    $heads.each(function () {
      $(this).attr('aria-sort', 'none').append('<i class="bi ' + ICON.none + ' sort-icon"></i>');
    });

    $heads.on('click', function () {
      const $th = $(this);
      const dir = $th.attr('aria-sort') === 'ascending' ? 'desc' : 'asc';
      setIndicator($t, $th, dir);
      if (server) $t.trigger('atm:sort', [$th.data('key'), dir]);
      else sortRows($t, $th, dir);
    });

    const $def = $heads.filter('[data-sort-default]').first();   // starting state
    if ($def.length) {
      const dir = $def.data('sort-default') === 'desc' ? 'desc' : 'asc';
      setIndicator($t, $def, dir);
      if (!server) sortRows($t, $def, dir);
    }
  }

  window.TableSort = { init };
  $(function () { $('table.table-sortable').each(function () { init(this); }); });
})(jQuery);
