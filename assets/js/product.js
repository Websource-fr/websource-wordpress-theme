/*!
 * Websource Theme — product page: details shown as tabs.
 * Mode read from [data-details-mode] on .wsx-details.
 */
(function () {
  'use strict';

  var box = document.querySelector('[data-wsx-details]');
  if (!box) { return; }

  var mode = box.getAttribute('data-details-mode') || 'tabs';
  var tabs = Array.prototype.slice.call(box.querySelectorAll('.wsx-dtabs__btn'));
  var panels = Array.prototype.slice.call(box.querySelectorAll('.wsx-dpanel'));

  function body(panel) { return panel.querySelector('.wsx-dpanel__body'); }
  function button(panel) { return panel.querySelector('.wsx-dpanel__btn'); }

  function setOpen(panel, open) {
    body(panel).hidden = !open;
    var btn = button(panel);
    if (btn) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
  }

  function select(key, focus) {
    panels.forEach(function (panel) {
      setOpen(panel, panel.getAttribute('data-wsx-panel') === key);
    });
    tabs.forEach(function (tab) {
      var on = tab.getAttribute('data-wsx-navtab') === key;
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.tabIndex = on ? 0 : -1;
      if (on && focus) { tab.focus(); }
    });
  }

  function show(key, scroll) {
    var panel = box.querySelector('[data-wsx-panel="' + key + '"]');
    if (!panel) { return false; }
    select(key, false);
    if (scroll) { panel.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    return true;
  }

  if (mode === 'tabs') {
    body(panels[0]).setAttribute('role', 'tabpanel');
    panels.forEach(function (panel) {
      body(panel).setAttribute('role', 'tabpanel');
      body(panel).setAttribute('aria-labelledby', 'tab-title-' + panel.getAttribute('data-wsx-panel'));
    });
    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () { select(tab.getAttribute('data-wsx-navtab'), false); });
      tab.addEventListener('keydown', function (e) {
        var next = null;
        var rtl = document.documentElement.dir === 'rtl';
        if (e.key === 'ArrowRight') { next = tabs[(index + (rtl ? tabs.length - 1 : 1)) % tabs.length]; }
        if (e.key === 'ArrowLeft') { next = tabs[(index + (rtl ? 1 : tabs.length - 1)) % tabs.length]; }
        if (e.key === 'Home') { next = tabs[0]; }
        if (e.key === 'End') { next = tabs[tabs.length - 1]; }
        if (next) { e.preventDefault(); select(next.getAttribute('data-wsx-navtab'), true); }
      });
    });
  }

  // Links to the reviews (rating under the title) and deep links (#reviews, #tab-description, #comment-12).
  function fromHash(hash, scroll) {
    if (!hash || hash.length < 2) { return; }
    var id = hash.slice(1);
    var key = id.indexOf('tab-') === 0 ? id.slice(4) : (id === 'reviews' || id === 'respond' || id.indexOf('comment-') === 0 ? 'reviews' : '');
    if (key) { show(key, scroll); }
  }
  document.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('a.woocommerce-review-link, a[href$="#reviews"]') : null;
    if (link) { e.preventDefault(); show('reviews', true); }
  });
  window.addEventListener('hashchange', function () { fromHash(location.hash, true); });
  fromHash(location.hash, true);
})();
