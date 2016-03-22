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
      <div class="unit"> '+days+'\
        <div class="marker">Days</div>\
      </div>\
      <div class="unit">'+hours+'\
        <div class="marker">Hours</div>\
      </div>\
      <div class="unit">'+minutes+'\
        <div class="marker">Minutes</div>\
      </div>\
    </div>';
    document.getElementById('mfbaCountdown').innerHTML = markup;
  }

  jQuery().ready(function() {
    setCountdownHTML();
  });
})();
