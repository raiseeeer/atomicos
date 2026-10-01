/* Atomicos login: constellation canvas, rotating headline, form logic */
$(function () {

  /* 1) Constellation: drifting nodes that connect and flee from the cursor */
  (function () {
    const c = document.getElementById('lgCanvas'), g = c.getContext('2d');
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let w, h, dpr, pts = [], mouse = { x: -9999, y: -9999 };

    function resize() {
      dpr = Math.min(window.devicePixelRatio || 1, 2);
      w = c.width = innerWidth * dpr; h = c.height = innerHeight * dpr;
      c.style.width = innerWidth + 'px'; c.style.height = innerHeight + 'px';
      const n = Math.min(90, Math.floor(innerWidth * innerHeight / 15000));
      pts = Array.from({ length: n }, () => ({
        x: Math.random() * w, y: Math.random() * h,
        vx: (Math.random() - .5) * .4 * dpr, vy: (Math.random() - .5) * .4 * dpr,
        r: (Math.random() * 1.6 + .8) * dpr, teal: Math.random() < .3
      }));
    }

    function frame() {
      g.clearRect(0, 0, w, h);
      for (const p of pts) {
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0 || p.x > w) p.vx *= -1;
        if (p.y < 0 || p.y > h) p.vy *= -1;
        const dx = p.x - mouse.x * dpr, dy = p.y - mouse.y * dpr, d = Math.hypot(dx, dy);
        if (d > 0 && d < 150 * dpr) { p.x += dx / d * 1.4; p.y += dy / d * 1.4; }
        g.beginPath(); g.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        g.fillStyle = p.teal ? '#2DE2C0' : '#8B7DFF'; g.fill();
      }
      const max = 135 * dpr;
      for (let i = 0; i < pts.length; i++) {
        for (let j = i + 1; j < pts.length; j++) {
          const d = Math.hypot(pts[i].x - pts[j].x, pts[i].y - pts[j].y);
          if (d < max) {
            g.strokeStyle = 'rgba(139,125,255,' + ((1 - d / max) * .32) + ')';
            g.lineWidth = dpr * .8;
            g.beginPath(); g.moveTo(pts[i].x, pts[i].y); g.lineTo(pts[j].x, pts[j].y); g.stroke();
          }
        }
      }
      if (!reduce) requestAnimationFrame(frame);
    }

    window.addEventListener('resize', resize);
    window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });
    window.addEventListener('mouseleave', () => { mouse.x = mouse.y = -9999; });
    resize(); frame();
  })();

  /* 2) Rotating headline word */
  (function () {
    const words = ['people', 'time', 'talent', 'growth'], el = document.getElementById('lgRotator');
    if (!el) return;
    let i = 0;
    setInterval(() => {
      el.classList.add('out');
      setTimeout(() => { i = (i + 1) % words.length; el.textContent = words[i]; el.classList.remove('out'); }, 350);
    }, 2600);
  })();

  /* 3) Form */
  const $alert = $('#loginAlert'), $card = $('#lgCard'), $btn = $('#btnLogin');

  $('#togglePw').on('click', function () {
    const $pw = $('#password'), show = $pw.attr('type') === 'password';
    $pw.attr('type', show ? 'text' : 'password');
    $(this).find('i').toggleClass('bi-eye bi-eye-slash');
  });

  function fail(msg) {
    $alert.text(msg).removeClass('d-none');
    $card.removeClass('lg-shake'); void $card[0].offsetWidth; $card.addClass('lg-shake'); // restart animation
    $btn.prop('disabled', false).find('.lg-btn-text').text('Sign in');
    $btn.find('.spin').remove(); $btn.find('i').show();
  }

  $('#loginForm').on('submit', function (e) {
    e.preventDefault();
    $alert.addClass('d-none');

    const username = $('#username').val().trim(), password = $('#password').val();
    if (!username || !password) return fail('Please enter your username and password.');

    $btn.prop('disabled', true).find('.lg-btn-text').text('Signing in');
    $btn.find('i').hide().after('<span class="spin"></span>');

    Atomicos.post('api/auth/login.php', { username, password })
      .done(res => {
        $card.addClass('lg-leave');
        setTimeout(() => { window.location.href = res.data.redirect; }, 420);
      })
      .fail(xhr => fail((xhr.responseJSON && xhr.responseJSON.message) || 'Login failed. Please try again.'));
  });
});
