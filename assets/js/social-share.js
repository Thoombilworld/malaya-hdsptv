(function () {
  function setFeedback(message) {
    var feedback = document.querySelector('[data-share-feedback]');
    if (feedback) {
      feedback.textContent = message;
    }
  }

  var nativeBtn = document.querySelector('[data-native-share]');
  if (nativeBtn) {
    nativeBtn.addEventListener('click', function () {
      var url = nativeBtn.getAttribute('data-share-url') || window.location.href;
      var title = nativeBtn.getAttribute('data-share-title') || document.title;
      var text = nativeBtn.getAttribute('data-share-text') || '';

      if (navigator.share) {
        navigator.share({ title: title, text: text, url: url })
          .then(function () { setFeedback('Shared successfully.'); })
          .catch(function () { setFeedback('Share canceled.'); });
      } else {
        setFeedback('Native sharing is not supported on this device.');
      }
    });
  }

  var copyBtn = document.querySelector('[data-copy-share-url]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var url = copyBtn.getAttribute('data-copy-share-url') || window.location.href;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url)
          .then(function () { setFeedback('Link copied to clipboard.'); })
          .catch(function () { setFeedback('Unable to copy link.'); });
      } else {
        setFeedback('Clipboard is not supported on this browser.');
      }
    });
  }
})();
