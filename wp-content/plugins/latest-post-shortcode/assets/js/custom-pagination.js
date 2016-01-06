(function ($) {
    window.LPS_check_ajax_pagination = {
        config: {},
        init: function () {
            LPS_check_ajax_pagination.initEvents();
        },
        initEvents: function () {
            LPS_check_ajax_pagination.sectionsSetup();
        },
        sectionsSetup: function () {
            jQuery('section.latest-post-selection.ajax_pagination').each(function () {
                var lps_section_id = this.id;
                var lps_section_args = jQuery('#' + lps_section_id + '-wrap').attr('data-args');
                jQuery('ul.pages.' + lps_section_id + '>li>a').on('click', function (e) {
                    e.preventDefault();
                    LPS_check_ajax_pagination.lpsNavigate(
                        '#' + lps_section_id + '-wrap',
                        jQuery(this).attr('data-page'),
                        lps_section_args
                    );
                });
            });
        },
        lpsNavigate: function (id, page, args) {
            jQuery.ajax({
                type: "POST",
                url: LPS.ajaxurl,
                data: {
                    action: 'lps_navigate_to_page',
                    page: page,
                    args: args,
                    lps_ajax: 1,
                },
                cache: false,
            }).success(function (response) {
                    jQuery(id).html(response);
                    LPS_check_ajax_pagination.init();
                });
        }
    };

    $(document).ready(function () {
        LPS_check_ajax_pagination.init();
    });

})(jQuery);
