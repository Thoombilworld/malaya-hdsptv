(function () {
  var btn = document.querySelector('[data-enable-notifications]');
  if (!btn) return;

  function setLabel(text) {
    btn.textContent = text;
  }

  if (!('Notification' in window) || !('serviceWorker' in navigator)) {
    btn.hidden = true;
    return;
  }

  btn.hidden = false;

  if (Notification.permission === 'granted') {
    setLabel('Notifications On');
  }

  btn.addEventListener('click', async function () {
    try {
      const permission = await Notification.requestPermission();
      if (permission !== 'granted') {
        setLabel('Notifications Blocked');
        return;
      }

      const registration = await navigator.serviceWorker.ready;
      await registration.showNotification('HDSPTV alerts enabled', {
        body: 'Breaking news notifications are now enabled on this device.',
        icon: '/assets/images/icons/icon-192.svg',
        badge: '/assets/images/icons/icon-192.svg',
        data: { url: '/' }
      });
      setLabel('Notifications On');
    } catch (error) {
      setLabel('Notification Error');
    }
  });
})();
