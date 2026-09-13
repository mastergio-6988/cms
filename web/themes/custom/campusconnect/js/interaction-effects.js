(function (Drupal, once) {
  'use strict';
  Drupal.behaviors.campusConnectInteractionEffects = {
    attach: function (context) {
      once('cc-rainbow-click-sparks', 'body', context).forEach(function (body) {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        body.addEventListener('pointerdown', function (event) {
          if (event.button && event.button !== 0) return;
          var spark = document.createElement('span');
          spark.className = 'cc-click-spark';
          spark.style.left = event.clientX + 'px';
          spark.style.top = event.clientY + 'px';
          for (var index = 0; index < 9; index += 1) {
            var bolt = document.createElement('i');
            bolt.style.setProperty('--cc-angle', (index * 40) + 'deg');
            spark.appendChild(bolt);
          }
          document.body.appendChild(spark);
          window.setTimeout(function () { spark.remove(); }, 700);
        }, { passive: true });
      });
    }
  };
})(Drupal, once);

(function () {
  'use strict';

  var traceSelector = [
    '.cc-reference-hero', '.cc-hero', '.cc-announcement-hero', '.cc-people-hero', '.cc-reference-card', '.cc-home-hero', '.cc-home-quicklink',
    '.cc-home-feature', '.cc-home-snapshot-card', '.cc-event-card', '.cc-department-card',
    '.cc-departments-card', '.department-card', '.cc-event-modal-dialog',
    '.cc-department-modal-dialog', '.cc-search-panel', '.cc-menu-panel'
  ].join(',');

  function addTraces(root) {
    root.querySelectorAll(traceSelector).forEach(function (element) {
      if (element.classList.contains('cc-hover-trace')) return;
      element.classList.add('cc-hover-trace');
      var trace = document.createElement('span');
      trace.className = 'cc-border-trace';
      trace.setAttribute('aria-hidden', 'true');
      element.appendChild(trace);
      element.addEventListener('pointermove', function (event) {
        var rect = element.getBoundingClientRect();
        element.style.setProperty('--cc-mx', (event.clientX - rect.left) + 'px');
        element.style.setProperty('--cc-my', (event.clientY - rect.top) + 'px');
      }, { passive: true });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { addTraces(document); });
  } else {
    addTraces(document);
  }

  new MutationObserver(function () { addTraces(document); }).observe(document.documentElement, { childList: true, subtree: true });
})();

/* Keep the shared reference header active state correct on every destination. */
(function () {
  var current = window.location.pathname.replace(/\/+$/, '') || '/';
  document.querySelectorAll('.cc-reference-menu a').forEach(function (link) {
    var path = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
    var active = path === current;
    link.classList.toggle('is-active', active);
    if (active) link.setAttribute('aria-current', 'page');
    else link.removeAttribute('aria-current');
  });
})();

document.addEventListener('DOMContentLoaded', function () {
  var current = window.location.pathname.replace(/\/+$/, '') || '/';
  document.querySelectorAll('.cc-reference-menu a').forEach(function (link) {
    var path = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
    var active = path === current;
    link.classList.toggle('is-active', active);
    if (active) link.setAttribute('aria-current', 'page');
    else link.removeAttribute('aria-current');
  });
});
