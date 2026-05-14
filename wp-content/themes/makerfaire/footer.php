</div> <!-- end of site-content -->

<?php
   echo basicCurl(UNIVERSAL_MAKEHUB_ASSET_URL_PREFIX . '/wp-content/universal-assets/v2/page-elements/universal-footer.html');
?>

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

</div> <!-- end of .site-container -->
</body>

</html>
