/* UI Kit helpers: builds the "copy code" source from the live demo markup */
$(function () {

  function dedent(html) {
    let lines = html.replace(/\t/g, '  ').split('\n');
    while (lines.length && !lines[0].trim()) lines.shift();
    while (lines.length && !lines[lines.length - 1].trim()) lines.pop();
    const min = Math.min(...lines.filter(l => l.trim()).map(l => l.match(/^ */)[0].length));
    return lines.map(l => l.slice(min)).join('\n');
  }

  // 1) Capture source BEFORE Bootstrap alters any attributes
  $('.kit-block').each(function () {
    const $demo = $(this).find('.kit-demo');
    if ($demo.length) $(this).find('.kit-pre').text(dedent($demo.html()));
  });

  // 2) Copy / show code
  $(document).on('click', '.kit-toggle', function () {
    $(this).closest('.kit-block').find('.kit-pre').toggleClass('d-none');
  });
  $(document).on('click', '.kit-copy', function () {
    const $btn = $(this), text = $btn.closest('.kit-block').find('.kit-pre').text();
    const done = () => { const old = $btn.html(); $btn.html('<i class="bi bi-check2"></i> Copied'); setTimeout(() => $btn.html(old), 1500); };
    if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); }
    else { const t = $('<textarea>').val(text).appendTo('body').select(); document.execCommand('copy'); t.remove(); done(); }
  });

  // 3) Quick search over blocks
  $('#kitSearch').on('input', function () {
    const q = this.value.toLowerCase().trim();
    $('.kit-block').each(function () { $(this).toggle(!q || $(this).data('title').indexOf(q) > -1); });
    $('.kit-group').toggle(!q);
  });

  // 4) Make demos work
  $(document).on('show.bs.modal', '.modal', function () { $(this).appendTo('body'); });
  $('[data-bs-toggle="tooltip"]').each(function () { new bootstrap.Tooltip(this); });
  $(document).on('click', '.kit-pw-toggle', function () {
    const $i = $(this).closest('.input-group').find('input');
    $i.attr('type', $i.attr('type') === 'password' ? 'text' : 'password');
    $(this).find('i').toggleClass('bi-eye bi-eye-slash');
  });
  $(document).on('click', '.kit-demo form', function () {}); // forms in demos never submit
  $(document).on('submit', '.kit-demo form', function (e) { e.preventDefault(); Atomicos.toast('Demo form submitted (no request sent).', 'info'); });
});
