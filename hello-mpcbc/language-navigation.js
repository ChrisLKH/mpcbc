/* Preserve the reading language without changing congregation or external media. */
(function () {
  'use strict';
  var config = window.mpcbcLanguages;
  if (!config) return;
  var choices = { en: 'English', 'zh-hant': '繁體中文', 'zh-hans': '简体中文' };
  var home = new URL(config.home);
  var here = new URL(window.location.href);
  var storageKey = 'mpcbc-reading-language';
  function valid(value) { return Object.prototype.hasOwnProperty.call(choices, value); }
  function remember(value) {
    try { window.localStorage.setItem(storageKey, value); } catch (_) { /* Private storage may be unavailable. */ }
  }
  var saved;
  try { saved = window.localStorage.getItem(storageKey); } catch (_) { saved = null; }
  var requested = here.searchParams.get('lang');
  var locale = valid(requested) ? requested : valid(saved) ? saved : valid(config.locale) ? config.locale : 'en';
  remember(locale);
  document.documentElement.setAttribute('data-mpcbc-reading-language', locale);
  function path(url) { return url.pathname.replace(/\/+$/, '') || '/'; }
  var topics = {};
  Object.keys(config.routes).forEach(function (topic) {
    Object.keys(config.routes[topic]).forEach(function (language) {
      topics[path(new URL(config.routes[topic][language]))] = topic;
    });
  });
  topics[path(new URL('zh/', home))] = 'index';
  if (config.routes.home) topics[path(home)] = 'home';
  function internal(url) {
    return url.origin === home.origin && url.pathname.indexOf(home.pathname) === 0 &&
      !/\/(?:wp-admin|wp-json|wp-content|wp-includes)(?:\/|$)|\/wp-login\.php$|\.[a-z0-9]{2,5}$/i.test(url.pathname);
  }
  function translated(url, language) {
    var target = new URL(url.href);
    if (!internal(target)) return target;
    var topic = topics[path(target)];
    // Home anchors keep their actual section; untranslated pages retain the preference.
    if (topic && !(topic === 'index' && path(target) === path(home))) {
      var counterpart = config.routes[topic][language];
      if (counterpart) target.pathname = new URL(counterpart).pathname;
    }
    if (language === 'en') target.searchParams.delete('lang');
    else target.searchParams.set('lang', language);
    return target;
  }
  // Resolve remembered-language visits before the content is displayed. Leave editor previews alone.
  if (!here.searchParams.has('elementor-preview') && !here.searchParams.has('preview')) {
    var destination = translated(here, locale);
    if (destination.href !== here.href) {
      if (destination.pathname === here.pathname) {
        window.history.replaceState(null, '', destination.href);
        here = destination;
      } else {
        window.location.replace(destination.href);
        return;
      }
    }
  }
  function localizeLinks() {
    Object.keys(choices).forEach(function (language) {
      document.querySelectorAll('.mpcbc-locale-' + language + ' > a').forEach(function (link) {
        link.dataset.mpcbcLocale = language;
        // Resolve from the current page, never from an old menu destination.
        var url = translated(here, language);
        link.href = url.href;
        link.hreflang = language === 'zh-hant' ? 'zh-Hant' : language === 'zh-hans' ? 'zh-Hans' : 'en';
        if (language === locale) link.setAttribute('aria-current', 'true');
        else link.removeAttribute('aria-current');
      });
    });
    document.querySelectorAll('a[href]').forEach(function (link) {
      if (link.dataset.mpcbcLocale || link.hasAttribute('download')) return;
      var url;
      try { url = new URL(link.getAttribute('href'), here); } catch (_) { return; }
      if (internal(url)) link.href = translated(url, locale).href;
    });
    document.querySelectorAll('.mpcbc-language-menu > a').forEach(function (link) {
      var node = Array.from(link.childNodes).find(function (child) { return child.nodeType === 3; });
      if (node) node.textContent = choices[locale];
      link.href = here.href;
      link.setAttribute('aria-label', choices[locale]);
    });
  }
  // Chinese is written in Traditional; 简体 converts it here. Hand-written Simplified ([data-zh-hans]) is left alone.
  var converterPromise;
  function converter() {
    if (!converterPromise) converterPromise = new Promise(function(resolve) {
      function ready() { resolve(window.OpenCC ? window.OpenCC.Converter({ from: 'hk', to: 'cn' }) : null); }
      if (window.OpenCC) { ready(); return; }
      var script = document.createElement('script');
      script.src = config.converter; script.async = true;
      script.onload = ready; script.onerror = function() { resolve(null); };
      document.head.appendChild(script);
    });
    return converterPromise;
  }
  if (locale === 'zh-hans') converter();
  var han = /[㐀-鿿]/;
  var skip = 'script, style, textarea, option, [data-zh-hans], .mpcbc-language-menu, .mpcbc-mobile-language';
  var traditional = new Map(); // text node -> { original, simplified }
  function chineseTextNodes() {
    var nodes = [];
    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      var node = walker.currentNode;
      if ((traditional.has(node) || han.test(node.nodeValue)) && !node.parentElement.closest(skip)) nodes.push(node);
    }
    return nodes;
  }
  function markLanguage(nodes, tag) {
    // Font choice follows lang; skip mixed English/Chinese elements so their Latin font stays.
    nodes.forEach(function(node) {
      var parent = node.parentElement;
      if (/^zh/.test(parent.getAttribute('lang') || '') || !/[A-Za-z]/.test(parent.textContent)) parent.setAttribute('lang', tag);
    });
  }
  function localizeChineseDisplay() {
    if (document.body.classList.contains('elementor-editor-active') || here.searchParams.has('elementor-preview')) return;
    var nodes = chineseTextNodes();
    if (locale !== 'zh-hans') {
      traditional.forEach(function(text, node) { if (node.nodeValue === text.simplified) node.nodeValue = text.original; });
      traditional.clear();
      markLanguage(nodes, 'zh-Hant');
      return;
    }
    converter().then(function(simplify) {
      if (!simplify || locale !== 'zh-hans') return;
      nodes.forEach(function(node) {
        var original = traditional.has(node) ? traditional.get(node).original : node.nodeValue;
        node.nodeValue = simplify(original);
        traditional.set(node, { original: original, simplified: node.nodeValue });
      });
      markLanguage(nodes, 'zh-Hans');
    });
  }
  function initializeLanguage() {
    localizeLinks(); localizeChineseDisplay();
  }
  function switchTo(language) {
    if (!valid(language)) return;
    var destination = translated(here, language);
    remember(language);
    if (destination.href === here.href) return;
    // Shared Home/footer switch native Chinese variants without reloading.
    if (destination.pathname === here.pathname) {
      locale = language;
      here = destination;
      document.documentElement.setAttribute('data-mpcbc-reading-language', locale);
      window.history.replaceState(null, '', destination.href);
      localizeLinks(); localizeChineseDisplay();
      document.querySelectorAll('.mpcbc-mobile-language').forEach(function(select) { select.value = language; });
      return;
    }
    window.location.assign(destination.href);
  }
  window.mpcbcLanguageNavigation = { switchTo: switchTo };
  document.addEventListener('click', function (event) {
    var parent = event.target.closest('.mpcbc-language-menu > a');
    if (parent) { event.preventDefault(); return; }
    var link = event.target.closest('a[data-mpcbc-locale]');
    if (!link || !valid(link.dataset.mpcbcLocale)) return;
    remember(link.dataset.mpcbcLocale);
    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
    event.preventDefault();
    switchTo(link.dataset.mpcbcLocale);
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeLanguage);
  else initializeLanguage();
})();
