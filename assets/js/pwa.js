(function () {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/service-worker.js').catch(function () {
        // Ignore registration errors in unsupported environments.
      });
    });
  }

  var deferredPrompt = null;
  var installButtons = document.querySelectorAll('[data-install-app]');

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    installButtons.forEach(function (btn) {
      btn.hidden = false;
    });
  });

  installButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      deferredPrompt.userChoice.finally(function () {
        deferredPrompt = null;
        installButtons.forEach(function (installBtn) {
          installBtn.hidden = true;
        });
      });
    });
  });

  var navToggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-top-nav]');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      var expanded = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', String(!expanded));
      nav.classList.toggle('is-open');
    });
  }
})();
