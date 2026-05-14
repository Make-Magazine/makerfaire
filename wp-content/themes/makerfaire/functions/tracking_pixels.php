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
            gtm:              'GTM-PCDDDV',
            activeCampaign: {
                accountId: '1000801328',
                trackByDefault: true
            },
            adroll: {
                advId: 'QZ72KCGOPBGLLLPAE3SDSI',
                pixId: 'RGZKRB7CHJF5RBMNCUJREU'
            },
            ga4: 'G-51PP9YXQ8B',
            fbPixel: '399923000199419'
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
    if (isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) &&
        $_COOKIE['cookielawinfo-checkbox-non-necessary'] == 'yes') {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PCDDDV"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}
add_action('wp_body_open', 'add_body_pixels');
