/*!
 * Websource Theme — front-end behaviours (no dependency, no jQuery of its own):
 * mobile search panel, drawers (menu, shop filters), quantity buttons, cart counter.
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;

  function closest(el, selector) {
    return el && el.closest ? el.closest(selector) : null;
  }

  /* ---- Mobile search panel (magnifier in the header) ---- */
  function toggleSearch(btn) {
    var hdr = closest(btn, '.wsx-hdr');
    if (!hdr) { return; }
    var open = !hdr.classList.contains('is-search-open');
    hdr.classList.toggle('is-search-open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      var input = hdr.querySelector('.wsx-hdr__search input[type=search], .wsx-hdr__search input[type=text]');
      if (input) { setTimeout(function () { input.focus(); }, 60); }
    }
  }

  /* ---- Drawers ---- */
  var lastOpener = null;

  function setExpanded(id, value) {
    doc.querySelectorAll('[data-wsx-drawer-open="' + id + '"]').forEach(function (b) {
      b.setAttribute('aria-expanded', value ? 'true' : 'false');
    });
  }

  function openDrawer(id, opener) {
    var el = doc.getElementById(id);
    if (!el) { return; }
    lastOpener = opener || null;
    if (el.classList.contains('wsx-drawer')) {
      el.hidden = false;
    } else {
      el.classList.add('is-open');
    }
    el.setAttribute('data-wsx-open', '1');
    root.classList.add('wsx-no-scroll');
    setExpanded(id, true);
    var focusable = el.querySelector('[data-wsx-drawer-close], a, button');
    if (focusable) { focusable.focus(); }
  }

  function closeDrawers() {
    doc.querySelectorAll('[data-wsx-open]').forEach(function (el) {
      if (el.classList.contains('wsx-drawer')) {
        el.hidden = true;
      } else {
        el.classList.remove('is-open');
      }
      el.removeAttribute('data-wsx-open');
      setExpanded(el.id, false);
    });
    root.classList.remove('wsx-no-scroll');
    if (lastOpener && lastOpener.focus) { lastOpener.focus(); }
    lastOpener = null;
  }

  /* ---- Quantity buttons ---- */
  function stepQuantity(btn) {
    var wrap = closest(btn, '.quantity');
    var input = wrap ? wrap.querySelector('input.qty') : null;
    if (!input || input.disabled || input.readOnly) { return; }
    var step = parseFloat(input.step) || 1;
    var min = input.min === '' ? 0 : parseFloat(input.min);
    var max = input.max === '' ? Infinity : parseFloat(input.max);
    var value = parseFloat(input.value);
    if (isNaN(value)) { value = min || 0; }
    var next = value + step * parseInt(btn.getAttribute('data-wsx-qty'), 10);
    next = Math.min(max, Math.max(min, next));
    var decimals = (String(step).split('.')[1] || '').length;
    input.value = next.toFixed(decimals);
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
    // The classic cart enables its “Update cart” button through a jQuery handler.
    if (window.jQuery) { window.jQuery(input).trigger('change'); }
  }

  /* ---- Cart counter ---- */
  function setCount(count) {
    doc.querySelectorAll('[data-wsx-cart-count]').forEach(function (el) {
      el.textContent = String(count);
      el.hidden = !(count > 0);
    });
  }

  var refreshTimer = null;
  function refreshCount() {
    var cfg = window.websourceTheme || {};
    if (!cfg.cartUrl || !window.fetch) { return; }
    clearTimeout(refreshTimer);
    refreshTimer = setTimeout(function () {
      fetch(cfg.cartUrl, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (cart) { if (cart && typeof cart.items_count === 'number') { setCount(cart.items_count); } })
        .catch(function () {});
    }, 150);
  }

  doc.addEventListener('click', function (e) {
    var t = e.target;
    var loupe = closest(t, '.wsx-loupe');
    if (loupe) { e.preventDefault(); toggleSearch(loupe); return; }
    var opener = closest(t, '[data-wsx-drawer-open]');
    if (opener) { e.preventDefault(); openDrawer(opener.getAttribute('data-wsx-drawer-open'), opener); return; }
    if (closest(t, '[data-wsx-drawer-close]') || (t.classList && t.classList.contains('wsx-drawer'))) { e.preventDefault(); closeDrawers(); return; }
    var qty = closest(t, '[data-wsx-qty]');
    if (qty) { e.preventDefault(); stepQuantity(qty); }
  });

  doc.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeDrawers(); }
  });

  // WooCommerce blocks (Cart, Checkout, Add to cart buttons) announce cart changes with these events.
  ['wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart'].forEach(function (name) {
    doc.body.addEventListener(name, refreshCount);
  });
  // Classic scripts announce them through jQuery events.
  function bindJquery() {
    if (!window.jQuery) { return; }
    window.jQuery(doc.body).on('added_to_cart removed_from_cart updated_cart_totals wc_fragments_refreshed', refreshCount);
  }
  if (doc.readyState === 'loading') {
    doc.addEventListener('DOMContentLoaded', bindJquery);
  } else {
    bindJquery();
  }
  // Blocks cart: quantity changed inside the block.
  if (doc.querySelector('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout, .wc-block-mini-cart')) {
    var obs = new MutationObserver(refreshCount);
    var target = doc.querySelector('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout');
    if (target) { obs.observe(target, { childList: true, subtree: true, characterData: true }); }
  }
})();
