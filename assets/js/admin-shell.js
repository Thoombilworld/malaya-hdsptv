(function () {
  var body = document.body;
  var sidebar = document.querySelector('[data-admin-sidebar]');
  var toggle = document.querySelector('[data-admin-menu-toggle]');
  var overlay = document.querySelector('[data-admin-overlay]');

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
})();
