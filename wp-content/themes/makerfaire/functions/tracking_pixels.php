<?php

// ------------------------------------------------------------------
// Per-site tracking configuration.
// gdpr.js at universal-assets/v2 reads window.trackingConfig to know which scripts to load
// after consent is given. Add or remove keys as needed per site.
// ------------------------------------------------------------------
function add_tracking_config() {

    // Generate page unique ID for Pinterest event deduplication
    $pageUniq = trim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
    if (is_front_page()) {
        $pageUniq = "home";
    }
    ?>
    <script type="text/javascript">
        var templateUrl = '<?= get_site_url(); ?>';
        var logoutURL   = '<?php echo wp_logout_url(home_url()); ?>';

        var trackingConfig = {
            gtm: 'GTM-PCDDDV',
            ga4: 'G-51PP9YXQ8B',
            fbPixel: '2071714643070701',
            linkedin: '545404',
            activeCampaign: {
                accountId: '1000801328',
                trackByDefault: true
            },
            adroll: {
                advId: 'QZ72KCGOPBGLLLPAE3SDSI',
                pixId: 'RGZKRB7CHJF5RBMNCUJREU'
            },
            gtmIsAnalytics: true  // GTM contains only analytics tags on this site
        };
    </script>
    <?php
}
add_action('wp_head', 'add_tracking_config', 1); // Priority 1 — must run before gdpr.js loads


// ------------------------------------------------------------------
// Body pixels — GTM noscript must stay here, PHP-gated.
// The noscript iframe is the non-JS fallback for GTM and cannot
// be injected dynamically via JavaScript.
// ------------------------------------------------------------------
function add_body_pixels() {
    if (isset($_COOKIE['cookielawinfo-checkbox-analytics']) &&
        $_COOKIE['cookielawinfo-checkbox-analytics'] == 'yes') {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PCDDDV"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <?php
    }
    if (isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) &&
        $_COOKIE['cookielawinfo-checkbox-non-necessary'] == 'yes') {
        ?>
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=2071714643070701&ev=PageView&noscript=1" />
        </noscript>
        <?php
    }
}
add_action('wp_body_open', 'add_body_pixels');

// tiktok requires cookies, so will be blocked and require user to allow cookies
add_filter('embed_oembed_html', function($html, $url, $attr, $post_id) {
    if (strpos($url, 'tiktok.com') !== false) {
        // Store the raw URL for consent-gated display
        $placeholder = '<div class="tiktok-consent-wrapper" data-url="' . esc_attr($url) . '">';
        $placeholder .= '<div class="embed-placeholder">';
        $placeholder .= '<p>This TikTok video uses advertising cookies.</p>';
        $placeholder .= '<button class="universal-btn tiktok-consent-btn">Accept cookies to view</button>';
        $placeholder .= '</div>';
        $placeholder .= '<div class="tiktok-embed-container" style="display:none;">' . $html . '</div>';
        $placeholder .= '</div>';
        
        if (isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) && 
            $_COOKIE['cookielawinfo-checkbox-non-necessary'] == 'yes') {
            return $html; // Already consented — show embed directly
        }
        return $placeholder;
    }
    return $html;
}, 10, 4);

add_filter('oembed_ttl', function($ttl, $url) {
    if (strpos($url, 'tiktok.com') !== false) {
        return DAY_IN_SECONDS;
    }
    return $ttl;
}, 10, 2);