/**
 * ============================================================================
 * FILE     : public/assets/js/positions.js
 * MODULE   : Positions
 * PAGE     : public/positions.php
 * ENDPOINT : public/api/positions/list.php
 * ----------------------------------------------------------------------------
 * PURPOSE
 *   Loads the positions table (search, status filter, sort, pagination).
 *
 * ENDPOINTS USED (all POST via Atomicos.post)
 *   api/positions/list.php     list one page of rows + totals
 *   api/positions/export.php   used by table-export.js (all matching rows)
 *
 * FUNCTIONS
 *   statusBadge()   turns position_status (1/2) into an Active/Inactive badge
 *   messageRow()    one full-width row for loading / empty / error messages
 *   loadList()      requests a page from list.php, then renders table + footer
 *   renderTable()   fills <tbody>; row numbers continue across pages
 *   renderPager()   fills "Showing x to y of z" and the page buttons
 *
 * CHANGELOG
 *   2026-10-02  Merged initial list code with pagination
 * ============================================================================
 */
$(function () {

  const API = 'api/positions/';                         // relative to /public, NOT to this JS file

  const esc = v => $('<div>').text(v ?? '').html();     // escapes text so data can't inject HTML

  // Single source of truth for status display
  const STATUS = {
    1: { label: 'Active',   badge: 'text-bg-success'   },
    2: { label: 'Inactive', badge: 'text-bg-secondary' }
  };

  // ---------- state ----------
  const state = { page: 1, perPage: 10, sort: 'title', dir: 'asc' };   // sort = default column (th data-key)
  let lastRequest = 0;                                  // ignores slow, out-of-date responses

  const $tbody  = $('#dataTable tbody');
  const COLSPAN = $('#dataTable thead th').length;      // full-width rows follow the real column count

  function statusBadge(code) {
    const s = STATUS[code] || { label: 'Unknown', badge: 'text-bg-light' };   // safe fallback
    return `<span class="badge ${s.badge}">${s.label}</span>`;
  }

  function messageRow(html, cls = 'text-secondary') {
    $tbody.html(`<tr><td colspan="${COLSPAN}" class="text-center py-4 ${cls}">${html}</td></tr>`);
  }

  // ======================= LIST =======================
  function loadList(page) {
    if (page) state.page = page;                        // loadList() with no argument = stay on this page

    messageRow('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');

    const request = ++lastRequest;

    Atomicos.post(API + 'list.php', {
      search:   $('#tableSearch').val().trim(),
      status:   $('#filterStatus').val() || 0,         // 0 = all, 1 = Active, 2 = Inactive
      page:     state.page,
      per_page: state.perPage,
      sort:     state.sort,
      dir:      state.dir
    })
    .done(function (res) {
      if (request !== lastRequest) return;              // a newer request exists, ignore this one

      console.log(res);                                        // for debugging; remove in production

      const data = res.data;                               // { rows, total, page, pages }
      state.page = data.page;                              // the server may have corrected the page
      renderTable(data.rows, (data.page - 1) * state.perPage);
      renderPager(data);
    })
    .fail(function () {
      if (request !== lastRequest) return;
      messageRow('Failed to load records.', 'text-danger');   // the red toast is shown by app.js
    });
  }

  function renderTable(rows, offset) {
    if (!rows.length) {
      messageRow('No records found');
      return;
    }
    const html = rows.map((r, i) => `
      <tr data-id="${r.id}">
        <td>${offset + i + 1}</td>
        <td>${esc(r.title)}</td>
        <td>${Number(r.base_salary).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
        <td>${esc(r.created_at)}</td>
        <td>${statusBadge(r.position_status)}</td>
        <td class="text-end text-nowrap">
          <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`).join('');
    $tbody.html(html);
  }

  // ======================= FOOTER / PAGINATION =======================
  function renderPager(d) {
    const from = d.total ? (d.page - 1) * state.perPage + 1 : 0;
    const to   = Math.min(d.page * state.perPage, d.total);
    $('#pageInfo').text(`Showing ${from} to ${to} of ${d.total} entries`);

    const item = (label, p, disabled, active) =>
      `<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
         <a class="page-link" href="#" data-page="${p}">${label}</a></li>`;

    let html = item('&laquo;', d.page - 1, d.page <= 1);

    const start = Math.max(1, d.page - 2);              // up to 5 page numbers around the current page
    const end   = Math.min(d.pages, d.page + 2);
    if (start > 1) html += item(1, 1) + (start > 2 ? item('&hellip;', 1, true) : '');
    for (let p = start; p <= end; p++) html += item(p, p, false, p === d.page);
    if (end < d.pages) html += (end < d.pages - 1 ? item('&hellip;', d.pages, true) : '') + item(d.pages, d.pages);

    html += item('&raquo;', d.page + 1, d.page >= d.pages);
    $('#pager').html(html);
  }

  // page-size dropdown: 10, 20 ... 100
  const sizes = Array.from({ length: 10 }, (_, i) => (i + 1) * 10);
  $('#perPage').html(sizes.map(n => `<option value="${n}">${n}</option>`).join('')).val(state.perPage);

  $('#perPage').on('change', function () {
    state.perPage = Number(this.value);
    loadList(1);                                        // back to page 1 when the size changes
  });

  $(document).on('click', '#pager .page-link', function (e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled')) return;
    loadList(Number($(this).data('page')));
  });

  // ======================= SEARCH / FILTER / SORT / EXPORT =======================
  let timer;
  $('#tableSearch').on('input', () => { clearTimeout(timer); timer = setTimeout(() => loadList(1), 300); });
  $('#filterStatus').on('change', () => loadList(1));

  // table-sort.js (server mode) fires this when a header is clicked
  $('#dataTable').on('atm:sort', function (e, key, dir) {
    state.sort = key;
    state.dir  = dir;
    loadList(1);
  });

  // table-export.js reads these so the file contains ALL matching records, not just this page
  $('#dataTable').data('exportParams', () => ({
    search: $('#tableSearch').val().trim(),
    status: $('#filterStatus').val() || 0,
    sort:   state.sort,
    dir:    state.dir
  }));

  // ======================= ADD / EDIT / DELETE =======================
  // (not built yet; after a successful save or delete, call loadList() to refresh and stay on the same page)

  loadList(1);                                          // runs automatically on page load
});