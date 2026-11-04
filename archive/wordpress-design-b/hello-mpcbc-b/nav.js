/**
 * nav.js - Sticky fading nav scroll observer for Hello MPCBC B
 */
(function() {
  'use strict';
  function updateNav() {
    if (window.scrollY >= 40) {
      document.body.classList.add('is-scrolled');
    } else {
      document.body.classList.remove('is-scrolled');
    }
  }

  window.addEventListener('scroll', updateNav, { passive: true });
  window.addEventListener('DOMContentLoaded', updateNav);
  updateNav();
})();
