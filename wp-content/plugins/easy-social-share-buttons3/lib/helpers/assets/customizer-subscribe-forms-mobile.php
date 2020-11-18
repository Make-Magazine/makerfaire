<?php
if (! function_exists ( 'essb_register_dynamic_subscribe_mobile_hide' )) {
    /**
     * Register the dynamic mobile breakdown to hide the subscribe forms (when enabled)
     * 
     * @since 7.3.2
     */
    function essb_register_dynamic_subscribe_mobile_hide() {
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-subscribe-form', 'display', 'none', '', true, 'static', '', '768');        
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-subscribe-poweredby', 'display', 'none', '', true, 'static', '', '768');
    }
}