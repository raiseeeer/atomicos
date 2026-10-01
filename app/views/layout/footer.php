      </div><!-- /container-fluid -->
    </div><!-- /app-content -->
  </main>

  <footer class="app-footer">
    <div class="float-end d-none d-sm-inline">HR Module</div>
    <strong>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>.</strong> All rights reserved.
  </footer>
</div><!-- /app-wrapper -->

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastBox"></div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
<script>
  // Live clock in the navbar
  (function tick() {
    var el = document.getElementById('atmClock');
    if (el) el.textContent = new Date().toLocaleString('en-PH', { weekday: 'short', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    setTimeout(tick, 1000);
  })();
</script>
<?php foreach (($pageScripts ?? []) as $js): ?>
<script src="<?= url('assets/js/' . $js) ?>"></script>
<?php endforeach; ?>
</body>
</html>
