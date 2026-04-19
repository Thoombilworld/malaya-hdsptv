(function () {
  var body = document.body;
  var sidebar = document.querySelector('[data-admin-sidebar]');
  var toggle = document.querySelector('[data-admin-menu-toggle]');
  var overlay = document.querySelector('[data-admin-overlay]');
  var timeNode = document.querySelector('[data-admin-time]');
  var themeToggle = document.querySelector('[data-theme-toggle]');

  if (!sidebar || !toggle) return;

  function setOpen(open) {
    body.classList.toggle('admin-sidebar-open', open);
    toggle.setAttribute('aria-expanded', String(open));
  }

  toggle.addEventListener('click', function () {
    setOpen(!body.classList.contains('admin-sidebar-open'));
  });

  if (overlay) {
    overlay.addEventListener('click', function () {
      setOpen(false);
    });
  }

  window.addEventListener('resize', function () {
    if (window.innerWidth > 1100) setOpen(false);
  });

  if (timeNode) {
    function renderTime() {
      var now = new Date();
      timeNode.textContent = now.toLocaleString();
    }
    renderTime();
    setInterval(renderTime, 1000);
  }

  if (themeToggle) {
    var stored = localStorage.getItem('hs_admin_theme');
    if (stored === 'dark') {
      body.classList.add('theme-dark');
    }
    themeToggle.addEventListener('click', function () {
      body.classList.toggle('theme-dark');
      localStorage.setItem('hs_admin_theme', body.classList.contains('theme-dark') ? 'dark' : 'light');
    });
  }
})();
