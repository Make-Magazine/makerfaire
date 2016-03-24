/* jshint multistr:true */
// Faire Countdown Clock
(function(){
  'use strict';
  var mfbaDate = new Date('2016-05-20T13:00:00-07:00');
  var now = new Date();

  function pad(n, width) {
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
  }

  // get total seconds between the times
  var delta = Math.abs(mfbaDate - now) / 1000;
  var days = Math.floor(delta / 86400);
  delta -= days * 86400;
  var hours = Math.floor(delta / 3600) % 24;
  delta -= hours * 3600;
  var minutes = Math.floor(delta / 60) % 60;
  delta -= minutes * 60;
  // pad with zero:
  days = pad(days, 2);
  hours = pad(hours, 2);
  minutes = pad(minutes, 2);

  function setCountdownHTML() {
    var markup = '<div class="countdown font-narrower">\
      <div class="unit">\
        <div class="number">'+days+'</div>\
        <div class="key">Days</div>\
      </div>\
      <div class="unit">\
        <div class="number">'+hours+'</div>\
        <div class="key">Hours</div>\
      </div>\
      <div class="unit">\
        <div class="number">'+minutes+'</div>\
        <div class="key">Minutes</div>\
      </div>\
    </div>';
    var element = document.getElementById('mfbaCountdown');
    if (element != null) {
      element.innerHTML = markup;
    }
  }

  jQuery().ready(function() {
    setCountdownHTML();
  });
})();
