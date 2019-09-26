<?php
   $context = null;
   if(UNIVERSAL_ASSET_USER && UNIVERSAL_ASSET_PASS) {
      $context = stream_context_create(array(
            'http' => array(
               'header'  => "Authorization: Basic " . base64_encode(UNIVERSAL_ASSET_USER.':'.UNIVERSAL_ASSET_PASS)
            )
      ));
   }
   echo file_get_contents( UNIVERSAL_ASSET_URL_PREFIX . '/wp-content/themes/memberships/universal-nav/universal-footer.html', false, $context);
?>

<div class="fancybox-thx" style="display:none;">
  <div class="nl-modal-cont nl-thx-p2">
    <div class="col-sm-4 hidden-xs nl-modal">
      <span class="fa-stack fa-4x">
        <i class="fa fa-circle-thin fa-stack-2x"></i>
        <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
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
  jQuery(".magical").click(function(event){
    event.preventDefault();
    jQuery.getScript("<?php echo get_template_directory_uri() . '/magical/js/jquery.magical.js' ?>", function() {
      jQuery(".magical").unicornblast();
    });
  });
</script>

<!-- Clear the WP admin bar when in mobile fixed header -->
<script>
  jQuery(document).ready(function(){
    if ((jQuery("#wpadminbar").length > 0) && (jQuery(window).width() < 768)) {
      jQuery(".quora .navbar").css( "margin-top", 46 );
    }
  });
</script>
<!-- Quora dropdown toggle stuff -->
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('.dropdown-toggle').dropdown();
    jQuery('#north').tab('show');
    jQuery('#featuredMakers').carousel({
      interval: 5000
    });
    jQuery('#mf-featured-slider').carousel({
      interval: 8000
    });
    jQuery( ".carousel" ).each( function() {
      jQuery(this).carousel({
        interval: 4000
      });
    });
  });
</script>
<div id="fb-root"></div>
<script>
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=216859768380573";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>
<!-- Quantcast Tag -->
<script type="text/javascript">
  var _qevents = _qevents || [];
  (function() {
  var elem = document.createElement('script');
  elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
  elem.async = true;
  elem.type = "text/javascript";
  var scpt = document.getElementsByTagName('script')[0];
  scpt.parentNode.insertBefore(elem, scpt);
  })();
  _qevents.push({
  qacct:"p-c0y51yWFFvFCY"
  });
</script>
<noscript>
  <div style="display:none;">
    <img src="//pixel.quantserve.com/pixel/p-c0y51yWFFvFCY.gif" border="0" height="1" width="1" alt="Quantcast"/>
  </div>
</noscript>
<!-- End Quantcast tag -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=131038253638769";
  fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery('.wp-navigation a').addClass('btn');
    jQuery(".scroll").click(function(event) {
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

<!-- Media Math Tracking Pixel -->
<script language='JavaScript1.1' async src='//pixel.mathtag.com/event/js?mt_id=1311192&mt_adid=208330&mt_exem=&mt_excl=&v1=&v2=&v3=&s1=&s2=&s3='></script>

<!-- Crazy Egg tracking
  <?php ?>
  <script type="text/javascript">
  setTimeout(function(){var a=document.createElement("script");
  var b=document.getElementsByTagName("script")[0];
  a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0013/2533.js?"+Math.floor(new Date().getTime()/3600000);
  a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
  </script>-->

<?php wp_footer(); ?>
<script type="text/javascript">
	
  (function() {
    window._pa = window._pa || {};
    // _pa.orderId = ""; // OPTIONAL: attach unique conversion identifier to conversions
    // _pa.revenue = ""; // OPTIONAL: attach dynamic purchase values to conversions
    // _pa.productId = ""; // OPTIONAL: Include product ID for use with dynamic ads
    var pa = document.createElement('script'); pa.type = 'text/javascript'; pa.async = true;
    pa.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + "//tag.marinsm.com/serve/558d98991eb60ba078000001.js";
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(pa, s);
  })();
</script>


<iframe id="auth0Logout" style="display: none;" ></iframe>

</body>

</html>