// Click-to-play for the Home slips: swap the thumbnail link for the YouTube player.
// Without this script the link simply opens the video on YouTube.
document.addEventListener('click', function (e) {
  var a = e.target.closest('[data-mpcbc-play]');
  if (!a || e.ctrlKey || e.metaKey || e.shiftKey) return; // modified clicks still open a tab
  e.preventDefault();
  var f = document.createElement('iframe');
  f.className = 'mpcbc-slip__player';
  f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(a.dataset.mpcbcPlay) + '?autoplay=1&rel=0';
  f.title = a.dataset.title || 'YouTube video';
  f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
  f.allowFullscreen = true;
  a.replaceWith(f);
  f.focus();
});
