/**
 * video-toggle.js - small Pause/Play button on Elementor background videos (WCAG 2.2.2).
 */
(function () {
  'use strict';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.querySelectorAll('.elementor-background-video-container').forEach(function (wrap) {
    var video = wrap.querySelector('video');
    if (!video) return;
    var userStarted = false;
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'mpcbc-video-toggle';

    function sync() {
      btn.classList.toggle('is-paused', video.paused);
      btn.setAttribute('aria-label', video.paused ? 'Play background video' : 'Pause background video');
    }

    btn.addEventListener('click', function () {
      userStarted = true;
      if (video.paused) { video.play(); } else { video.pause(); }
    });
    video.addEventListener('play', function () {
      // Visitors who ask for reduced motion start paused until they press Play.
      if (reduceMotion && !userStarted) { video.pause(); }
      sync();
    });
    video.addEventListener('pause', sync);
    sync();
    wrap.parentElement.appendChild(btn);
  });
})();
