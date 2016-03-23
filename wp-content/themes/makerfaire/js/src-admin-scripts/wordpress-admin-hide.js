(function($) {
  $(function() {
    // fix conflict with wordpress leaving class="hidden" after jQuery.show
    // conflicting with bootstrap's .hidden { display: none !important; }
    $.each(['show'], function(i, ev) {
      var el = $.fn[ev];
      $.fn[ev] = function() {
        this.trigger(ev);
        return el.apply(this, arguments);
      };
    });
    $('.hidden').on('show', function() {
      $(this).removeClass('hidden');
    });
  });
})(jQuery);
