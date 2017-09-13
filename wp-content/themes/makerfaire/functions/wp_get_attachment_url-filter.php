<?php

//
// Using wp_get_attachment_url filter, we can fix the dreaded mixed content browser warning
//

add_filter( 'wp_get_attachment_url', 'set_url_scheme' );