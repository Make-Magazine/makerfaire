<?php
$context = stream_context_create(array(
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    ),
));

if (UNIVERSAL_ASSET_USER && UNIVERSAL_ASSET_PASS) {
    $context = stream_context_create(array(
        'http' => array(
            'header' => "Authorization: Basic " . base64_encode(UNIVERSAL_ASSET_USER . ':' . UNIVERSAL_ASSET_PASS)
        )
    ));
}
echo file_get_contents(UNIVERSAL_ASSET_URL_PREFIX . '/wp-content/themes/memberships/universal-nav/universal-footer.html', false, $context);
?>

<div class="fancybox-thx" style="display:none;">
    <div class="nl-modal-cont nl-thx-p2">
        <div class="col-sm-4 hidden-xs nl-modal">
            <span class="fa-stack fa-4x">
                <i class="far fa-circle fa-stack-2x"></i>
                <i class="far fa-thumbs-up fa-stack-1x"></i>
            </span>
        </div>
        <div class="col-sm-8 col-xs-12 nl-modal">
            <h3>Awesome!</h3>
            <p style="color:#333;text-align:center;margin-top:20px;">Thanks for signing up.</p>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="nl-modal-error" style="display:none;">
    <div class="col-xs-12 nl-modal padtop">
        <p class="lead">The reCAPTCHA box was not checked. Please try again.</p>
    </div>
    <div class="clearfix"></div>
</div>

<script>
    jQuery(".magical").click(function (event) {
        event.preventDefault();
        jQuery.getScript("<?php echo get_template_directory_uri() . '/magical/js/jquery.magical.js' ?>", function () {
            jQuery(".magical").unicornblast();
        });
    });
</script>

<!-- Clear the WP admin bar when in mobile fixed header -->
<script>
    jQuery(document).ready(function () {
        if ((jQuery("#wpadminbar").length > 0) && (jQuery(window).width() < 768)) {
            jQuery(".quora .navbar").css("margin-top", 46);
        }
    });
</script>
<!-- Quora dropdown toggle stuff -->
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.dropdown-toggle').dropdown();
        jQuery('#north').tab('show');
        jQuery('#featuredMakers').carousel({
            interval: 5000
        });
        jQuery('#mf-featured-slider').carousel({
            interval: 8000
        });
        jQuery(".carousel").each(function () {
            jQuery(this).carousel({
                interval: 4000
            });
        });
    });
</script>

<?php
// Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
    ?>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=131038253638769";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <!-- Start Active Campaign Pixel -->
    <script type="text/javascript">
        (function (e, t, o, n, p, r, i) {
            e.visitorGlobalObjectAlias = n;
            e[e.visitorGlobalObjectAlias] = e[e.visitorGlobalObjectAlias] || function () {
                (e[e.visitorGlobalObjectAlias].q = e[e.visitorGlobalObjectAlias].q || []).push(arguments)};e[e.visitorGlobalObjectAlias].l = (new Date).getTime();
            r = t.createElement("script");
            r.src = o;
            r.async = true;
            i = t.getElementsByTagName("script")[0];
            i.parentNode.insertBefore(r, i)
        })(window, document, "https://diffuser-cdn.app-us1.com/diffuser/diffuser.js", "vgo");
        vgo('setAccount', '1000801328');
        vgo('setTrackByDefault', true);
        vgo('process');
    </script>
    <!-- Start Active Campaign Pixel -->

<?php } // end of cookie law if  ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.wp-navigation a').addClass('btn');
        jQuery(".scroll").click(function (event) {
            //prevent the default action for the click event
            event.preventDefault();

            //get the full url - like mysitecom/index.htm#home
            var full_url = this.href;

            //split the url by # and get the anchor target name - home in mysitecom/index.htm#home
            var parts = full_url.split("#");
            var trgt = parts[1];

            //get the top offset of the target anchor
            var target_offset = jQuery("#" + trgt).offset();
            var target_top = target_offset.top;

            //goto that anchor by setting the body scroll top to anchor top
            jQuery('html, body').animate({
                scrollTop: target_top - 50
            }, 1000);

        });
        var exists = jQuery('td.has-video').length;
        if (exists === 0) {
            jQuery('td.no-video').remove();
        }
        jQuery('table.schedule').slideDown('slow');
    });
</script>


<?php wp_footer(); ?>



<iframe id="auth0Logout" style="display: none;" ></iframe>

</body>

</html>