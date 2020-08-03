<?php
function essb_add_shareinfo_column() {
    add_action ( "manage_posts_custom_column", 'essb_generate_shareinfo_column');
    add_filter ( "manage_posts_columns", 'essb_register_shareinfo_column');
    add_action ( "manage_pages_custom_column", 'essb_generate_shareinfo_column');
    add_filter ( "manage_pages_columns", 'essb_register_shareinfo_column');
}


function essb_register_shareinfo_column($defaults) {
    $defaults['essb_shareinfo'] = '<span title="Display the customizations in the post or page share options. The column can be disabled from Advanced -> Administrative">' . esc_html__('Social', 'essb') . '</span>';
    
    return $defaults;
}

function essb_generate_shareinfo_column($column_name) {
    if ($column_name == 'essb_shareinfo') {
        
        $og_values = false;
        $custom_tweet = false;
        $custom_pin = false;
        $custom_sharing = false;
        
        /**
         * Open Graph Values
         */        
        if (get_post_meta (get_the_ID(), 'essb_post_og_desc', true) != '') {
            $og_values = true;
        }
        
        if (get_post_meta (get_the_ID(), 'essb_post_og_title', true) != '') {
            $og_values = true;
        }
        
        if (get_post_meta (get_the_ID(), 'essb_post_og_image', true) != '') {
            $og_values = true;
        }
        
        if (get_post_meta (get_the_ID(), 'essb_post_og_url', true) != '') {
            $og_values = true;
        }
        
        if (get_post_meta (get_the_ID(), 'essb_post_og_author', true) != '') {
            $og_values = true;
        }
        
        /**
         * Twitter
         */
        if (get_post_meta (get_the_ID(), 'essb_post_twitter_tweet', true) != '') {
            $custom_tweet = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_twitter_username', true) != '') {
            $custom_tweet = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_twitter_hashtags', true) != '') {
            $custom_tweet = true;
        }
        
        /**
         * Pinterest
         */
        if (get_post_meta (get_the_ID(), 'essb_post_pin_image', true) != '') {
            $custom_pin = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_pin_desc', true) != '') {
            $custom_pin = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_pin_id', true) != '') {
            $custom_pin = true;
        }
        
        /**
         * Custom Sharing
         */
        if (get_post_meta (get_the_ID(), 'essb_post_share_message', true) != '') {
            $custom_sharing = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_share_url', true) != '') {
            $custom_sharing = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_share_image', true) != '') {
            $custom_sharing = true;
        }
        if (get_post_meta (get_the_ID(), 'essb_post_share_text', true) != '') {
            $custom_sharing = true;
        }
        
        if ($og_values) {
            echo '<span class="essb-status-icon" title="Social Media Optimization"><svg enable-background="new 0 0 24 24" height="512" viewBox="0 0 24 24" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m21 0h-18c-1.655 0-3 1.345-3 3v18c0 1.654 1.345 3 3 3h18c1.654 0 3-1.346 3-3v-18c0-1.655-1.346-3-3-3z" fill="#3b5999"/><path d="m16.5 12v-3c0-.828.672-.75 1.5-.75h1.5v-3.75h-3c-2.486 0-4.5 2.014-4.5 4.5v3h-3v3.75h3v8.25h4.5v-8.25h2.25l1.5-3.75z" fill="#fff"/></svg></span>';
        }

        if ($custom_tweet) {
            echo '<span class="essb-status-icon" title="Custom Tweet"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <path style="fill:#03A9F4;" d="M512,97.248c-19.04,8.352-39.328,13.888-60.48,16.576c21.76-12.992,38.368-33.408,46.176-58.016 c-20.288,12.096-42.688,20.64-66.56,25.408C411.872,60.704,384.416,48,354.464,48c-58.112,0-104.896,47.168-104.896,104.992 c0,8.32,0.704,16.32,2.432,23.936c-87.264-4.256-164.48-46.08-216.352-109.792c-9.056,15.712-14.368,33.696-14.368,53.056 c0,36.352,18.72,68.576,46.624,87.232c-16.864-0.32-33.408-5.216-47.424-12.928c0,0.32,0,0.736,0,1.152 c0,51.008,36.384,93.376,84.096,103.136c-8.544,2.336-17.856,3.456-27.52,3.456c-6.72,0-13.504-0.384-19.872-1.792 c13.6,41.568,52.192,72.128,98.08,73.12c-35.712,27.936-81.056,44.768-130.144,44.768c-8.608,0-16.864-0.384-25.12-1.44 C46.496,446.88,101.6,464,161.024,464c193.152,0,298.752-160,298.752-298.688c0-4.64-0.16-9.12-0.384-13.568 C480.224,136.96,497.728,118.496,512,97.248z"/> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></span>';
        }
        
        if ($custom_pin) {
            echo '<span class="essb-status-icon" title="Custom Pin"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.883 512.883" style="enable-background:new 0 0 512.883 512.883;" xml:space="preserve"> <path style="fill:#CB1F24;" d="M256.441,0c-141.241,0-256,114.759-256,256c0,105.048,62.676,195.09,153.6,234.814 c-0.883-17.655,0-39.724,4.414-59.145c5.297-21.186,32.662-139.476,32.662-139.476s-7.945-16.772-7.945-40.607 c0-37.959,22.069-66.207,49.434-66.207c22.952,0,34.428,17.655,34.428,38.841c0,22.952-15.007,58.262-22.952,90.924 c-6.179,27.366,13.241,49.434,40.607,49.434c48.552,0,81.214-62.676,81.214-135.945c0-56.497-37.959-97.986-106.814-97.986 c-77.683,0-126.234,58.262-126.234,122.703c0,22.069,6.179,37.959,16.772,50.317c4.414,5.297,5.297,7.945,3.531,14.124 c-0.883,4.414-4.414,15.89-5.297,20.303c-1.766,6.179-7.062,8.828-13.241,6.179c-36.193-15.007-52.083-53.848-52.083-97.986 c0-72.386,60.91-159.779,182.731-159.779c97.986,0,162.428,70.621,162.428,146.538c0,100.634-55.614,175.669-137.71,175.669 c-27.366,0-53.848-15.007-62.676-31.779c0,0-15.007,59.145-17.655,70.621c-5.297,19.421-15.89,39.724-25.6,54.731 c22.952,7.062,47.669,10.593,72.386,10.593c141.241,0,256-114.759,256-256S397.683,0,256.441,0"/> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></span>';
        }
        if ($custom_sharing) {
            echo '<span class="essb-status-icon" title="Custom Share Data"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g> <polygon style="fill:#0095FF;" points="177.47,244.428 162.118,218.646 333.856,115.459 349.208,141.24 	"/> <polygon style="fill:#0095FF;" points="332.274,401.58 166.791,317.011 180.326,290.233 345.81,374.803 	"/> </g> <g> <path style="fill:#00ABE9;" d="M406,0c-49.501,0-90,40.499-90,90c0,49.499,40.499,90,90,90s90-40.501,90-90 C496,40.499,455.501,0,406,0z"/> <path style="fill:#00ABE9;" d="M406,332c-49.501,0-90,40.499-90,90c0,49.499,40.499,90,90,90s90-40.501,90-90 C496,372.499,455.501,332,406,332z"/> <path style="fill:#00ABE9;" d="M106,181c-49.501,0-90,40.499-90,90c0,49.499,40.499,90,90,90s90-40.501,90-90 C196,221.499,155.501,181,106,181z"/> </g> <g> <path style="fill:#0095FF;" d="M496,422c0,49.499-40.499,90-90,90V332C455.501,332,496,372.499,496,422z"/> <path style="fill:#0095FF;" d="M406,180V0c49.501,0,90,40.499,90,90C496,139.499,455.501,180,406,180z"/> <path style="fill:#0095FF;" d="M196,271c0,49.499-40.499,90-90,90V181C155.501,181,196,221.499,196,271z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></span>';
        }
        
    }
}
