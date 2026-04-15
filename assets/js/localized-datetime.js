(function () {
  var KEY_TZ = 'hdsptv_tz_pref';
  var KEY_LOCALE = 'hdsptv_locale_pref';
  var datetimeTargets = document.querySelectorAll('[data-localized-datetime]');
  if (!datetimeTargets.length) return;

  var tzSelect = document.querySelector('[data-timezone-override]');
  var localeSelect = document.querySelector('[data-locale-override]');

  function setCookie(name, value) {
    document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + (60 * 60 * 24 * 180);
  }

  function getLocalStorage(key, fallback) {
    try {
      var value = window.localStorage.getItem(key);
      return value || fallback;
    } catch (e) {
      return fallback;
    }
  }

  function setLocalStorage(key, value) {
    try {
      window.localStorage.setItem(key, value);
    } catch (e) {
      // Ignore persistence failures.
    }
  }

  function detectedTimezone() {
    try {
      return Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    } catch (e) {
      return 'UTC';
    }
  }

  function detectedLocale() {
    return (navigator.languages && navigator.languages[0]) || navigator.language || 'en-US';
  }

  function getSupportedTimezones() {
    if (Intl.supportedValuesOf) {
      try {
        return Intl.supportedValuesOf('timeZone');
      } catch (e) {
        // fallback list below
      }
    }
    return ['UTC', 'Asia/Kolkata', 'Asia/Dubai', 'Europe/London', 'America/New_York', 'America/Los_Angeles'];
  }

  function formatDate(value, locale, timezone) {
    var baseDate = value ? new Date(value) : new Date();
    if (isNaN(baseDate.getTime())) baseDate = new Date();
    return new Intl.DateTimeFormat(locale, {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: value ? undefined : '2-digit',
      timeZone: timezone,
      timeZoneName: 'short'
    }).format(baseDate);
  }

  function currentPreferences() {
    var tzPref = getLocalStorage(KEY_TZ, 'auto');
    var localePref = getLocalStorage(KEY_LOCALE, 'auto');
    return {
      timezone: tzPref === 'auto' ? detectedTimezone() : tzPref,
      locale: localePref === 'auto' ? detectedLocale() : localePref,
      rawTimezone: tzPref,
      rawLocale: localePref
    };
  }

  function render() {
    var prefs = currentPreferences();
    datetimeTargets.forEach(function (el) {
      var timestamp = el.getAttribute('data-timestamp');
      el.textContent = formatDate(timestamp, prefs.locale, prefs.timezone);
    });
  }

  function initTimezoneSelect() {
    if (!tzSelect) return;
    var zones = getSupportedTimezones();
    zones.slice(0, 400).forEach(function (tz) {
      var option = document.createElement('option');
      option.value = tz;
      option.textContent = tz;
      tzSelect.appendChild(option);
    });

    tzSelect.value = getLocalStorage(KEY_TZ, 'auto');
    tzSelect.addEventListener('change', function () {
      setLocalStorage(KEY_TZ, tzSelect.value || 'auto');
      setCookie('hs_tz_pref', tzSelect.value || 'auto');
      render();
    });
  }

  function initLocaleSelect() {
    if (!localeSelect) return;
    localeSelect.value = getLocalStorage(KEY_LOCALE, 'auto');
    localeSelect.addEventListener('change', function () {
      setLocalStorage(KEY_LOCALE, localeSelect.value || 'auto');
      setCookie('hs_locale_format_pref', localeSelect.value || 'auto');
      render();
    });
  }

  initTimezoneSelect();
  initLocaleSelect();
  render();
  setInterval(render, 1000);
})();
