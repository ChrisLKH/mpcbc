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
  function path(url) { return url.pathname.replace(/\/+$/, '') || '/'; }
  var topics = {};
  Object.keys(config.routes).forEach(function (topic) {
    Object.keys(config.routes[topic]).forEach(function (language) {
      topics[path(new URL(config.routes[topic][language]))] = topic;
    });
  });
  topics[path(new URL('zh/', home))] = 'index';
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
      window.location.replace(destination.href);
      return;
    }
  }
  function localizeLinks() {
    Object.keys(choices).forEach(function (language) {
      document.querySelectorAll('.mpcbc-locale-' + language + ' > a').forEach(function (link) {
        link.dataset.mpcbcLocale = language;
        var url = new URL(link.href);
        url.searchParams.set('lang', language);
        // Preserve the section when switching on a translated page.
        if (config.topic && config.topic !== 'index' && here.hash) url.hash = here.hash;
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
  document.addEventListener('click', function (event) {
    var link = event.target.closest('a[data-mpcbc-locale]');
    if (link && valid(link.dataset.mpcbcLocale)) remember(link.dataset.mpcbcLocale);
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', localizeLinks);
  else localizeLinks();
})();
