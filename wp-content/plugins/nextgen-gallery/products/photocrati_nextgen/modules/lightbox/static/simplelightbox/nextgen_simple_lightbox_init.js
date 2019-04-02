jQuery(function($) {

    var selector = null;

    var nextgen_simplelightbox_init = function() {
        selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
        selector.simpleLightbox({
            history: false,
            animationSlide: false,
            animationSpeed: 100
        });
    };

    nextgen_simplelightbox_init();

    $(window).bind('refreshed', function() {
        var gallery = selector.simpleLightbox();
        gallery.refresh();
    });
});