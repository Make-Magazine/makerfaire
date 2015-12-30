jQuery(function() {
  mfba = new Date(2015, 5 - 1, 16, 9, 0);
  jQuery('.countdown').countdown({
    until: mfba,
    timezone: -8,
    format: 'DHMS',
    layout: '<div class="countdown-numbers"><table><tr><th>{dnn}{sep}</th><th>{hnn}{sep}</th><th>{mnn}{sep}</th><th>{snn}</th></tr><tr class="time"><td>Days</td><td>Hours</td><td>Minutes</td><td>Seconds</td></tr></table></div>',
    timeSeparator: '<span class="separator">:</span>'
  });
});
