window.addEventListener('load', function() {
  if (window.location.href.indexOf("buy-tickets") > -1){
        // Set this to your UA number to use a specific tracker Client ID. Defaults to
        // first tracker registered, which is fine for 99.9% of users.
        var targetTrackingId = '';
        if (!document.querySelector) return;
        var gaName = window.GoogleAnalyticsObject || 'ga';
        // Safely instantiate our GA queue.
        // We cheat and make a local reference named 'ga' here for simplicity
        var ga = window[gaName]=window[gaName]||function(){(window[gaName].q=window[gaName].q||[]).push(arguments)};window[gaName].l=+new Date;
        if(document.readyState !== 'loading') {
          init();
        } else {
          // On IE8 this fires on window.load, all other browsers will fire when DOM ready
          document.addEventListener ? 
            simpleAddEvent(document, 'DOMContentLoaded', init) : 
            simpleAddEvent(window, 'load', init);
        }
        function init() {
          ga(function () {
            // Update our reference
            ga = window[gaName];
            // Defer to the back of the queue if no tracker is ready
            // With a half-second delay to allow a 'create' command
            // to run.
            if (!ga.getAll().length) {
              setTimeout(function() {
                ga(updateIframes);
                ga(updateUrls);
              }, 500);
            } else {
              updateUrls();
              updateIframes();
            }
          });
        }
        function updateIframes() {
          var iframes = document.getElementsByTagName('iframe');
          var iframe,
              a, i;
          for (i = 0; i < iframes.length; i++) {
            iframe = iframes[i];
            if (iframe.src && iframe.src.indexOf('eventbrite') > -1) {
              a = document.createElement('a');
              a.href = iframe.src;
              checkAndAddEboga(a);
              if (iframe.src !== a.href) {
                iframe.src = a.href;
              }
            }
          }
        }
        function updateUrls() {
          var urls = document.querySelectorAll('a');
          var a, i;
          for (i = 0; i < urls.length; i++) {
            a = urls[i];
            checkAndAddEboga(a);
          }
        }
        function checkAndAddEboga(a) {
          var clientId = getClientId();
          var parameter = '_eboga=' + clientId;
          // If we're in debug mode and can't find a client 
          if (!clientId) {
            if (debugMode) {
              window.console && window.console.error('GTM Eventbrite Cross Domain: Unable to detect Client ID. Verify you are using Universal Analytics and the correct targetTrackingId is set, if any.');
            }
            return;
          }
          if (a.hostname.indexOf('eventbrite.') > -1 && a.search.indexOf('_eboga') === -1) {
            a.search = a.search ? a.search + '&' + parameter : '?' + parameter;
          }
        }
        function getClientId() {
          var trackers = ga.getAll();
          var len = trackers.length;
          var tracker,
            i;
          if (!targetTrackingId) return trackers[0].get('clientId');
          for (i = 0; i < len; i++) {
            tracker = trackers[i];
            if (tracker.get('trackingId') === targetTrackingId) {
              return tracker.get('clientId');
            }
          }
        }
        // Very simple event binding w/ support for attachEvent
        function simpleAddEvent(el, evt, handler) {
          if ('attachEvent' in document) {
            el.attachEvent('on' + evt, function(e) {
              handler.call(this, evt);
            });
          } else if ('addEventListener' in document) {
            el.addEventListener(evt, handler);
          }
        }
    }
});