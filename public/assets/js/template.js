/**
 * Module script: list, search, paginate, add, edit, delete.
 * Talks to:  api/<module>/list.php, save.php, delete.php, options.php
 *
 * HOW TO REUSE: change API and NAME below, then adjust renderTable() + fillForm() to your columns.
 */
$(function () {

  // ===== CONFIG (change per module) ========================================
  const API  = 'api/template/';     // e.g. 'api/positions/'
  const NAME = 'Position';          // used in modal titles and messages
  // =========================================================================

  const modal        = new bootstrap.Modal('#recordModal');
  const confirmModal = new bootstrap.Modal('#confirmModal');
  const $form        = $('#recordForm');

  let rows   = {};                          // id -> row object (used to fill the edit form)
  let state  = { page: 1, perPage: 10, sort: 'title', dir: 'asc' };   // sort = default column (th data-key)

  // ---------- small helpers ----------
  const esc   = v => $('<div>').text(v ?? '').html();   // escape text (prevents XSS)
  const money = v => Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const date  = v => v ? new Date(v.replace(' ', 'T')).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : '';

  // ======================= LIST =======================
  function loadList(page) {
    if (page) state.page = page;

    Atomicos.post(API + 'list.php', {
      search:   $('#tableSearch').val().trim(),
      page:     state.page,
      per_page: state.perPage,
      sort:     state.sort,
      dir:      state.dir
    }).done(res => {
      const d = res.data;
      state.page = d.page;
      rows = {};
      d.rows.forEach(r => rows[r.id] = r);
      renderTable(d.rows, (d.page - 1) * state.perPage);
      renderPager(d);
    });
  }

  // >>> CHANGE <<< one <td> per column
  function renderTable(list, offset) {
    if (!list.length) {
      $('#dataTable tbody').html('<tr><td colspan="5" class="text-center text-secondary py-5"><i class="bi bi-inbox fs-3 d-block mb-1"></i>No records found</td></tr>');
      return;
    }
    $('#dataTable tbody').html(list.map((r, i) => `
      <tr data-id="${r.id}">
        <td>${offset + i + 1}</td>
        <td class="fw-semibold">${esc(r.title)}</td>
        <td>&#8369;${money(r.base_salary)}</td>
        <td>${date(r.created_at)}</td>
        <td class="text-end text-nowrap">
          <button class="btn btn-sm btn-outline-primary btn-edit" title="Edit"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`).join(''));
  }

  function renderPager(d) {
    const from = d.total ? (d.page - 1) * state.perPage + 1 : 0;
    const to   = Math.min(d.page * state.perPage, d.total);
    $('#pageInfo').text(`Showing ${from} to ${to} of ${d.total} entries`);

    const item = (label, p, disabled, active) =>
      `<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${label}</a></li>`;

    let html = item('&laquo;', d.page - 1, d.page <= 1);
    for (let p = Math.max(1, d.page - 2); p <= Math.min(d.pages, d.page + 2); p++) {
      html += item(p, p, false, p === d.page);
    }
    html += item('&raquo;', d.page + 1, d.page >= d.pages);
    $('#pager').html(html);
  }

  $(document).on('click', '#pager .page-link', function (e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled')) return;
    loadList($(this).data('page'));
  });

  // sort: table-sort.js (server mode) fires this when a header is clicked
  $('#dataTable').on('atm:sort', function (e, key, dir) {
    state.sort = key;
    state.dir  = dir;
    loadList(1);
  });

  // search (waits 300ms after typing stops)
  let timer;
  $('#tableSearch').on('input', () => { clearTimeout(timer); timer = setTimeout(() => loadList(1), 300); });

  // ======================= ADD / EDIT =======================
  function clearErrors() {
    $form.removeClass('was-validated').find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').text('');
  }

  // show server-side validation messages under each field
  function showErrors(errors) {
    Object.keys(errors || {}).forEach(name => {
      const $f = $form.find(`[name="${name}"]`).addClass('is-invalid');
      $f.closest('.mb-3, .mb-0, .input-group').find('.invalid-feedback').first().text(errors[name]);
    });
  }

  $('#btnAdd').on('click', () => {
    clearErrors();
    $form[0].reset();
    $form.find('[name=id]').val('');
    $('#modalTitle').text('Add ' + NAME);
    modal.show();
    setTimeout(() => $form.find('input:visible:first').trigger('focus'), 300);
  });

  // >>> CHANGE <<< one line per form field
  $(document).on('click', '.btn-edit', function () {
    const r = rows[$(this).closest('tr').data('id')];
    clearErrors();
    $form.find('[name=id]').val(r.id);
    $form.find('[name=title]').val(r.title);
    $form.find('[name=base_salary]').val(r.base_salary);
    $('#modalTitle').text('Edit ' + NAME);
    modal.show();
  });

  $form.on('input change', '.is-invalid', function () { $(this).removeClass('is-invalid'); });

  $form.on('submit', function (e) {
    e.preventDefault();
    clearErrors();
    if (!this.checkValidity()) { $form.addClass('was-validated'); return; }   // browser-side check

    const $btn = $form.find('[type=submit]').prop('disabled', true);
    Atomicos.post(API + 'save.php', Object.fromEntries(new FormData(this)))
      .done(res => { Atomicos.toast(res.message); modal.hide(); loadList(); })
      .fail(xhr => showErrors(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.errors))
      .always(() => $btn.prop('disabled', false));
  });

  // ======================= DELETE =======================
  let deleteId = null;

  $(document).on('click', '.btn-delete', function () {
    const r = rows[$(this).closest('tr').data('id')];
    deleteId = r.id;
    $('#confirmText').text(`Delete "${r.title}"? This action cannot be undone.`);   // >>> CHANGE <<< label field
    confirmModal.show();
  });

  $('#btnConfirmDelete').on('click', function () {
    const $btn = $(this).prop('disabled', true);
    Atomicos.post(API + 'delete.php', { id: deleteId })
      .done(res => { Atomicos.toast(res.message); confirmModal.hide(); loadList(); })
      .fail(() => confirmModal.hide())       // the error toast already explains why (e.g. "in use")
      .always(() => $btn.prop('disabled', false));
  });

  // ======================= DROPDOWN OPTIONS (reusable) =======================
  // Fill any <select> from an options.php endpoint. Copy this into another module's JS, e.g.
  //   loadOptions('#positionSelect', 'api/positions/options.php');           // Employee form
  //   loadOptions('#positionSelect', 'api/positions/options.php', 3);        // pre-select id 3 when editing
  function loadOptions(select, endpoint, selectedId = '') {
    return Atomicos.post(endpoint, {}).done(res => {
      const opts = res.data.options.map(o => `<option value="${o.id}">${esc(o.label)}</option>`).join('');
      $(select).html('<option value="">-- Select --</option>' + opts).val(selectedId);
    });
  }

  // ======================= EXPORT =======================
  // table-export.js asks for these filters so the file contains ALL matching records (not just this page)
  $('#dataTable').data('exportParams', () => ({
    search: $('#tableSearch').val().trim(),
    sort:   state.sort,
    dir:    state.dir
  }));

  // ======================= START =======================
  loadList(1);
});
