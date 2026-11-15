(function () {
  'use strict';
  function onScroll() {
    document.body.classList.toggle('is-scrolled', window.scrollY >= 40);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // One mobile reading-language control outside Hello's hamburger panel.
  // Labels and destinations stay editable in the existing WordPress menu.
  function initMobileLanguage() {
    var header = document.querySelector('.site-header');
    var inner = header && header.querySelector('.header-inner');
    var item = header && header.querySelector('.site-navigation .mpcbc-language-menu');
    var toggle = header && header.querySelector('.site-navigation-toggle-holder');
    if (!inner || !item || !toggle || header.querySelector('.mpcbc-mobile-language')) return;
    var links = Array.from(item.querySelectorAll('.sub-menu a[data-mpcbc-locale]'));
    if (!links.length) return;
    var select = document.createElement('select');
    select.className = 'mpcbc-mobile-language';
    select.setAttribute('aria-label', 'Language / 語言');
    links.forEach(function (link) {
      var option = document.createElement('option');
      option.value = link.dataset.mpcbcLocale;
      option.textContent = link.textContent.trim();
      option.selected = link.getAttribute('aria-current') === 'true';
      select.appendChild(option);
    });
    select.addEventListener('change', function () {
      var link = links.find(function (candidate) { return candidate.dataset.mpcbcLocale === select.value; });
      if (link) window.location.href = link.href;
    });
    inner.insertBefore(select, toggle);
    header.classList.add('mpcbc-mobile-language-ready');
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initMobileLanguage);
  else initMobileLanguage();

  // Progressive enhancement of native Elementor topic tabs. All labels and
  // articles come from editable widgets, including the adjacent-topic links.
  function initReaders() {
    // Keep the editor's native widget tree intact for volunteer editing.
    if (document.body.classList.contains('elementor-editor-active')) return;
    document.querySelectorAll('.mpcbc-topic-reader, #mpcbc-topic-reader').forEach(function (reader) {
      if (reader.dataset.readerReady) return;
      var heading = reader.querySelector('[role="tablist"]');
      if (!heading) return;
      var tabs = Array.from(heading.querySelectorAll('[role="tab"]'));
      if (!tabs.length) return;
      reader.dataset.readerReady = 'true';
      var select = document.createElement('select');
      select.className = 'mpcbc-topic-select';
      var intro = reader.previousElementSibling;
      var label = intro && intro.querySelector('.elementor-heading-title, h2, p');
      select.setAttribute('aria-label', label ? label.textContent : tabs[0].textContent.trim());
      tabs.forEach(function (tab, i) {
        var option = document.createElement('option');
        option.value = i; option.textContent = tab.textContent.trim(); select.appendChild(option);
      });
      heading.before(select);
      if (intro && (intro.classList.contains('mpcbc-topic-index') || intro.id === 'mpcbc-topic-index')) {
        var phone = window.matchMedia('(max-width: 767px)');
        var placeIntro = function () {
          if (phone.matches) reader.before(intro);
          else heading.prepend(intro);
        };
        phone.addEventListener('change', placeIntro);
        placeIntro();
      }
      function sync() {
        var i = tabs.findIndex(function (tab) { return tab.getAttribute('aria-selected') === 'true'; });
        if (i >= 0) select.value = i;
      }
      select.addEventListener('change', function () { tabs[Number(select.value)].click(); });
      new MutationObserver(sync).observe(heading, {subtree:true, attributes:true, attributeFilter:['aria-selected']});
      reader.querySelectorAll('.reader-unavailable, [id^="mpcbc-reader-unavailable-"]').forEach(function (widget) {
        var link = widget.matches('a') ? widget : widget.querySelector('a');
        if (link) { link.removeAttribute('href'); link.setAttribute('aria-disabled','true'); }
      });
      reader.addEventListener('click', function (e) {
        var link = e.target.closest('a');
        if (!link) return;
        var match = new URL(link.href, location.href).hash.match(/^#belief-topic-(\d+)$/);
        if (!match) return;
        e.preventDefault();
        var tab = tabs[Number(match[1])-1];
        if (!tab) return;
        tab.click();
        var panel = document.getElementById(tab.getAttribute('aria-controls'));
        if (panel) {
          panel.setAttribute('tabindex','-1'); panel.focus({preventScroll:true});
          var header = document.querySelector('.site-header');
          window.scrollTo({top: panel.getBoundingClientRect().top + window.scrollY - (header ? header.offsetHeight : 0) - 24, behavior:'instant'});
        }
      });
      sync();
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initReaders);
  else initReaders();
  window.addEventListener('load', initReaders);

  // Nested Tabs keyboard arrow switching
  document.addEventListener('keydown', function (e) {
    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;
    var activeBtn = document.activeElement;
    if (!activeBtn || activeBtn.getAttribute('role') !== 'tab') return;
    var heading = activeBtn.closest('[role="tablist"]');
    if (!heading) return;
    var tabs = Array.from(heading.querySelectorAll('[role="tab"]'));
    var idx = tabs.indexOf(activeBtn);
    if (idx === -1) return;
    e.preventDefault();
    e.stopPropagation();
    var nextIdx;
    if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
      nextIdx = (idx + 1) % tabs.length;
    } else {
      nextIdx = (idx - 1 + tabs.length) % tabs.length;
    }
    tabs[nextIdx].focus();
    tabs[nextIdx].click();
  }, true);
})();
