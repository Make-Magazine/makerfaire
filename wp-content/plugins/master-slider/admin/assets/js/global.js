(function($, window, document, undefined){
    "use strict";
    $('.ms-close-notice').on('click', function(event){
        event.preventDefault();
        var $this  = $(this),
            _id    = $this.data('id');

        jQuery.post(
            ajaxurl,
            {
                action    : 'msp_dismiss_notice', // the handler
                msnonce   : __MS_GLOBAL.dismiss_nonce,
                _id       : _id
            },
            function(res){
                if( ! res.success ){
                    alert( res.data.message );
                } else {
                    $this.closest('.msp-notice-wrapper').hide();
                }
            }
        );

    });

    $('.rate-btn').on('click', function(event){
        var $this  = $(this);
        var delay = $this.hasClass('delay') ? 'delay' : '';
        jQuery.post(
            ajaxurl,
            {
                action    : 'msp_dismiss_rate_notice', // the handler
                msnonce   : __MS_GLOBAL.dismiss_nonce,
                delay     : delay
            },
            function(res){
                if( ! res.success ){
                    alert( res.data.message );
                } else {
                    $this.parents('.msp-rate').find('button.notice-dismiss').click();
                }
            }
        );

    });
})(jQuery, window, document);
