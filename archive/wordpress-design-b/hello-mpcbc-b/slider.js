/**
 * slider.js - Announcement slider for .mp-slider containers
 */
document.addEventListener('DOMContentLoaded', function() {
  'use strict';
  if (document.body.classList.contains('elementor-editor-active')) return;

  var sliders = document.querySelectorAll('.mp-slider');
  sliders.forEach(function(slider) {
    // Select all immediate child containers representing slides
    var slides = Array.from(slider.children).filter(function(el) {
      return !el.classList.contains('mp-slider-nav');
    });

    if (slides.length <= 1) return;

    var current = 0;
    slides.forEach(function(s, idx) {
      s.classList.add('mp-slide');
      if (idx === 0) s.classList.add('is-active');
    });

    // Create navigation controls matching v5A: dots + ← → buttons
    var nav = document.createElement('div');
    nav.className = 'mp-slider-nav';
    nav.setAttribute('role', 'region');
    nav.setAttribute('aria-label', 'Announcement controls');

    var dotsWrap = document.createElement('div');
    dotsWrap.className = 'mp-slider-dots';

    var dots = [];
    slides.forEach(function(_, idx) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'mp-slider-dot' + (idx === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Go to announcement ' + (idx + 1));
      dot.addEventListener('click', function() { goTo(idx); });
      dotsWrap.appendChild(dot);
      dots.push(dot);
    });

    var prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'mp-slider-btn';
    prevBtn.setAttribute('aria-label', 'Previous announcement');
    prevBtn.textContent = '←';
    prevBtn.addEventListener('click', function() { goTo((current - 1 + slides.length) % slides.length); });

    var nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'mp-slider-btn';
    nextBtn.setAttribute('aria-label', 'Next announcement');
    nextBtn.textContent = '→';
    nextBtn.addEventListener('click', function() { goTo((current + 1) % slides.length); });

    nav.appendChild(dotsWrap);
    nav.appendChild(prevBtn);
    nav.appendChild(nextBtn);

    // Place nav at the top-right of the slider or target placeholder if present
    var targetNavMount = slider.querySelector('.mp-slider-nav-mount');
    if (targetNavMount) {
      targetNavMount.appendChild(nav);
    } else {
      slider.insertBefore(nav, slider.firstChild);
    }

    function goTo(idx) {
      slides[current].classList.remove('is-active');
      dots[current].classList.remove('is-active');
      current = idx;
      slides[current].classList.add('is-active');
      dots[current].classList.add('is-active');
    }

    slider.setAttribute('tabindex', '0');
    slider.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') {
        goTo((current - 1 + slides.length) % slides.length);
      } else if (e.key === 'ArrowRight') {
        goTo((current + 1) % slides.length);
      }
    });
  });
});
