/*
 * FontResize Event
 */
FontResizeEvent = (function(window, document) {
  var randomID = 'font-resize-frame-' + Math.floor(Math.random() * 1000);
  var resizeFrame = document.createElement('iframe');
  resizeFrame.id = randomID;
  resizeFrame.className = 'font-resize-helper';
  resizeFrame.style.cssText = 'position:absolute;width:100em;height:10px;top:-9999px;left:-9999px;border-width:0';

  // wait for page load
  function onPageReady() {
    document.body.appendChild(resizeFrame);

    // use native IE resize event if possible
    if (/MSIE (6|7|8)/.test(navigator.userAgent)) {
      resizeFrame.onresize = function() {
        window.FontResizeEvent.trigger(resizeFrame.offsetWidth / 100);
      };
    }
    // use script inside the iframe to detect resize for other browsers
    else {
      var doc = resizeFrame.contentWindow.document;
      doc.open();
      doc.write('<scri' + 'pt>window.onload = function(){var em = parent.document.getElementById("' + randomID + '");window.onresize = function(){if(parent.FontResizeEvent){parent.FontResizeEvent.trigger(em.offsetWidth / 100);}}};</scri' + 'pt>');
      doc.close();
    }
  }
  if (window.addEventListener) window.addEventListener('load', onPageReady, false);
  else if (window.attachEvent) window.attachEvent('onload', onPageReady);

  // public interface
  var callbacks = [];
  return {
    onChange: function(f) {
      if (typeof f === 'function') {
        callbacks.push(f);
      }
    },
    trigger: function(em) {
      for (var i = 0; i < callbacks.length; i++) {
        callbacks[i](em);
      }
    }
  };
}(this, document));
