// DOM ready handler
function bindReady(handler) {
  var called = false;
  var ready = function() {
    if (called) return;
    called = true;
    handler();
  };
  if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', ready, false);
  } else if (document.attachEvent) {
    if (document.documentElement.doScroll && window == window.top) {
      var tryScroll = function() {
        if (called) return;
        if (!document.body) return;
        try {
          document.documentElement.doScroll('left');
          ready();
        } catch (e) {
          setTimeout(tryScroll, 0);
        }
      };
      tryScroll();
    }
    document.attachEvent('onreadystatechange', function() {
      if (document.readyState === 'complete') {
        ready();
      }
    });
  }
  if (window.addEventListener) window.addEventListener('load', ready, false);
  else if (window.attachEvent) window.attachEvent('onload', ready);
}
