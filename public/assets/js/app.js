/* Global helpers shared by every page */
const Atomicos = {
  appUrl: $('meta[name="app-url"]').attr('content') || '',
  csrf:   $('meta[name="csrf-token"]').attr('content'),

  url(path) { return this.appUrl + '/' + path.replace(/^\//, ''); },

  // POST helper: always sends the CSRF token and expects JSON back
  post(path, data = {}) {
    return $.ajax({
      url: this.url(path),
      method: 'POST',
      data: Object.assign({ csrf_token: this.csrf }, data),
      dataType: 'json'
    }).fail(xhr => {
      const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong.';
      this.toast(msg, 'danger');
    });
  },

  // Bootstrap toast (needs #toastBox from footer.php)
  toast(message, type = 'success') {
    const $box = $('#toastBox');
    if (!$box.length) return;
    const $t = $(`<div class="toast align-items-center text-bg-${type} border-0" role="alert">
      <div class="d-flex"><div class="toast-body"></div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);
    $t.find('.toast-body').text(message);
    $box.append($t);
    new bootstrap.Toast($t[0], { delay: 3500 }).show();
  }
};

// Logout (works on any page that has #btnLogout)
$(document).on('click', '#btnLogout', function (e) {
  e.preventDefault();
  Atomicos.post('api/auth/logout.php').done(res => { window.location.href = res.data.redirect; });
});
