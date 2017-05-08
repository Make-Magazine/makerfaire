<footer id="footer" class="new-footer">
  <div class="container">
    <div class="row social-foot-desktop hidden-xs">
      <div class="col-sm-12 col-sm-6 col-md-3 social-foot-col" >
        <a href="/"><img class="footer_logo" src="/wp-content/uploads/2016/01/mf_logo.jpg" alt="Maker Faire Logo"></a>
        <ul class="list-unstyled">
          <li><a href="/makerfairehistory">About Maker Faire</a></li>
          <li><a href="/map">Find a Faire Near You</a></li>
          <li><a href="/be-a-maker">Be a Maker</a></li>
          <li><a href="//help.makermedia.com/hc/en-us/categories/200333245-Maker-Faire" target="_blank">Maker Faire FAQs</a></li>
          <li><a href="//help.makermedia.com/hc/en-us/sections/201008995-Maker-Faire-Support" target="_blank">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-sm-12 col-sm-6 col-md-3 social-foot-col" >
        <h4>Explore Making</h4>
        <ul class="list-unstyled">
          <li><a href="//makezine.com/blog?utm_source=makerfaire.com&utm_medium=footer&utm_term=Make+News" target="_blank">Make: News &amp; Projects</a></li>
          <li><a href="//www.makershed.com/?utm_source=makerfaire.com&utm_medium=footer&utm_term=Maker+Shed" target="_blank">Maker Shed</a></li>
          <li><a href="//makercamp.com/?utm_source=makerfaire.com&utm_medium=footer&utm_term=makercamp" target="_blank">Maker Camp</a></li>
          <li><a href="https://readerservices.makezine.com/mk/default.aspx?utm_source=makerfaire.com&utm_medium=footer&utm_term=subscribe+to+make" target="_blank">Subscribe to Make:</a></li>
          <li><a href="/maker-movement">Maker Movement</a></li>
        </ul>
      </div>
      <div class="visible-sm-block clearfix"></div>
      <div class="col-sm-12 col-sm-6 col-md-3 social-foot-col">
        <h4>Our Company</h4>
        <ul class="list-unstyled">
          <li><a class="magical" href="#">is Magical?</a></li>
          <li><a href="//makermedia.com/work-with-us/advertising" target="_blank">Advertise with Us</a></li>
          <li><a href="//makermedia.com/work-with-us/job-openings" target="_blank">Careers</a></li>
          <li><a href="//help.makermedia.com/hc/en-us" target="_blank">Help</a></li>
          <li><a href="//makermedia.com/privacy" target="_blank">Privacy</a></li>
        </ul>
      </div>
      <div class="col-sm-6 col-md-3 social-foot-col">
        <h4 class="stay-connected">Follow Us</h4>
        <div class="social-network-container">
          <ul class="social-network social-circle">
              <li><a href="//www.facebook.com/makerfaire" class="icoFacebook" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
              <li><a href="//twitter.com/makerfaire" class="icoTwitter" title="Twitter" target="_blank"><i class="fa fa-twitter" target="_blank"></i></a></li>
              <li><a href="//www.instagram.com/makerfaire/" class="icoInstagram" title="Instagram" target="_blank"><i class="fa fa-instagram" target="_blank"></i></a></li>
              <li><a href="//www.youtube.com/makerfaire" class="icoYoutube" title="YouTube" target="_blank"><i class="fa fa-youtube" target="_blank"></i></a></li>
          </ul>
        </div>
        <div class="clearfix"></div>

        <div class="mz-footer-subscribe">
          <?php
            $isSecure = "http://";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
              $isSecure = "https://";
            }
            $server = $_SERVER['HTTP_HOST'];
          ?>
          <h4>Sign Up</h4>
          <p>Stay inspired and get fresh updates</p>
          <form class="sub-form whatcounts-signup1" action="http://whatcounts.com/bin/listctrl" method="POST">
            <input type="hidden" name="slid_1" value="6B5869DC547D3D46E66DEF1987C64E7A" /><!-- Maker Faire Newsletter -->
            <input type="hidden" name="slid_2" value="6B5869DC547D3D46941051CC68679543" /><!-- Maker Media Newsletter -->
            <input type="hidden" name="multiadd" value="1" />
            <input type="hidden" name="cmd" value="subscribe" />
            <input type="hidden" name="custom_source" value="footer" />
            <input type="hidden" name="custom_incentive" value="none" />
            <input type="hidden" name="custom_url" value="<?php echo $server. $_SERVER['REQUEST_URI']; ?>" />
            <input type="hidden" id="format_mime" name="format" value="mime" />
            <input type="hidden" name="goto" value="" />
            <input type="hidden" name="custom_host" value="<?php echo $server; ?>" />
            <input type="hidden" name="errors_to" value="" />
            <div class="mz-form-horizontal">
              <input id="wc-email" name="email" placeholder="Enter your Email" required type="text"><br>
              <input value="GO" class="btn-cyan" type="submit">
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END desktop row -->
    <!-- Add back in when the site is responsive -->
    <div class="row social-foot-mobile visible-xs-block">
      <div class="col-xs-12 social-foot-col">
        <h4 class="stay-connected">Follow Us</h4>
        <div class="social-network-container">
          <ul class="social-network social-circle">
              <li><a href="//www.facebook.com/makerfaire" class="icoFacebook" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
              <li><a href="//twitter.com/makerfaire" class="icoTwitter" title="Twitter" target="_blank"><i class="fa fa-twitter" target="_blank"></i></a></li>
              <li><a href="//www.instagram.com/makerfaire/" class="icoInstagram" title="Instagram" target="_blank"><i class="fa fa-instagram" target="_blank"></i></a></li>
              <li><a href="//www.youtube.com/makerfaire" class="icoYoutube" title="YouTube" target="_blank"><i class="fa fa-youtube" target="_blank"></i></a></li>
          </ul>
        </div>
        <div class="clearfix"></div>
        <div class="mz-footer-subscribe">
          <?php
            $isSecure = "http://";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
              $isSecure = "https://";
            }
          ?>
          <h4>Sign Up</h4>
          <p>Stay inspired and get fresh updates</p>
          <form class="sub-form whatcounts-signup1m" action="http://whatcounts.com/bin/listctrl" method="POST">
            <input type="hidden" name="slid_1" value="6B5869DC547D3D46E66DEF1987C64E7A" /><!-- Maker Faire Newsletter -->
            <input type="hidden" name="slid_2" value="6B5869DC547D3D46941051CC68679543" /><!-- Maker Media Newsletter -->
            <input type="hidden" name="multiadd" value="1" />
            <input type="hidden" name="cmd" value="subscribe" />
            <input type="hidden" name="custom_source" value="footer" />
            <input type="hidden" name="custom_incentive" value="none" />
            <input type="hidden" name="custom_url" value="<?php echo $server . $_SERVER["REQUEST_URI"]; ?>" />
            <input type="hidden" id="format_mime" name="format" value="mime" />
            <input type="hidden" name="goto" value="" />
            <input type="hidden" name="custom_host" value="<?php echo $server; ?>" />
            <input type="hidden" name="errors_to" value="" />
            <div class="mz-form-horizontal">
              <input id="wc-email-m" name="email" placeholder="Enter your Email" required type="email"><br>
              <input value="GO" class="btn-cyan" type="submit">
            </div>
          </form>
        </div>
      </div>
      <div class="col-xs-12 panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="heading1">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="false" aria-controls="collapse1">
              <h4 class="panel-title">Make:</h4>
            </a>
          </div>
          <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
            <div class="panel-body">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="/makerfairehistory">About Maker Faire</a></li>
                <li><a href="/map">Find a Faire Near You</a></li>
                <li><a href="/be-a-maker">Be a Maker</a></li>
                <li><a href="//help.makermedia.com/hc/en-us/categories/200333245-Maker-Faire" target="_blank">Maker Faire FAQs</a></li>
                <li><a href="//help.makermedia.com/hc/en-us/sections/201008995-Maker-Faire-Support" target="_blank">Contact Us</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="heading2">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
              <h4 class="panel-title">Explore Making</h4>
            </a>
          </div>
          <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
            <div class="panel-body">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="/blog">Make: News</a></li>
                <li><a href="//www.makershed.com" target="_blank">Maker Shed</a></li>
                <li><a href="//makercamp.com" target="_blank">Maker Camp</a></li>
                <li><a href="//readerservices.makezine.com/mk/default.aspx?" target="_blank">Subscribe to Make:</a></li>
                <li><a href="/maker-movement">Maker Movement</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="heading3">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
              <h4 class="panel-title">Our Company</h4>
            </a>
          </div>
          <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
            <div class="panel-body">
              <ul class="nav nav-pills nav-stacked">
                <li><a class="magical" href="#">is Magical?</a></li>
                <li><a href="//makermedia.com/work-with-us/advertising" target="_blank">Advertise with Us</a></li>
                <li><a href="//makermedia.com/work-with-us/job-openings" target="_blank">Careers</a></li>
                <li><a href="//help.makermedia.com/hc/en-us" target="_blank">Help</a></li>
                <li><a href="//makermedia.com/privacy" target="_blank">Privacy</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End social-foot-mobile -->
  </div>
  <!-- END container -->
  <?php echo make_copyright_footer(); ?>
</footer>
<!-- END new-footer -->

<script>
  jQuery(".magical").click(function(event){
    event.preventDefault();
    jQuery.getScript("<?php echo get_template_directory_uri() . '/magical/js/jquery.magical.js' ?>", function() {
      jQuery(".magical").unicornblast();
    });
  });
</script>

<!-- Subscribe return path overlay -->
<?php echo subscribe_return_path_overlay(); ?>

<!-- Email newsletter subscribe modal -->
<?php echo display_thank_you_modal_if_signed_up(); ?>

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
<script type='text/javascript'>
  (function (d, t) {
    var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
    bh.type = 'text/javascript';
    bh.src = '//www.bugherd.com/sidebarv2.js?apikey=3pkvtpykrj9qwq4qt9rmuq';
    s.parentNode.insertBefore(bh, s);
    })(document, 'script');
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
<!-- Crazy Egg tracking
  <?php ?>
  <script type="text/javascript">
  setTimeout(function(){var a=document.createElement("script");
  var b=document.getElementsByTagName("script")[0];
  a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0013/2533.js?"+Math.floor(new Date().getTime()/3600000);
  a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
  </script>-->
<!-- Start pop up modal for school page -->
<?php if ( is_page( '459885' ) ) { ?>
  <script>
    function getCookie(name) {
      var dc = document.cookie;
      var prefix = name + "=";
      var begin = dc.indexOf("; " + prefix);
      if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
      } else {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
          end = dc.length;
        }
      }
      return unescape(dc.substring(begin + prefix.length, end));
    }
    jQuery(function() {
      if (document.location.href.indexOf("campaign") > -1) {
        var date = new Date();
        date.setTime(date.getTime() + (60 * 24 * 60 * 60 * 1000));
        date = date.toGMTString();
        document.cookie = "Newsletter-signup=; expires=" + date + "; path=/";
      }
    });
    jQuery(function() {
      var news_close = getCookie("Newsletter-closed");
      var news_signup = getCookie("Newsletter-signup");

      if (news_signup == null) {
        if (news_close == null) {
          jQuery(".fancybox").fancybox({
            openEffect: "fade",
            closeEffect: "none",
            autoSize: false,
            width: 500,
            height: 225,
            beforeClose: function() {
              var date = new Date();
              date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
              date = date.toGMTString();
              document.cookie = "Newsletter-closed=; expires=" + date + "; path=/";
            },
            afterLoad: function() {
              this.content = this.content.html();
            }
          }).trigger("click");

          jQuery(".newsletter-set-cookie").click(function() {
            var date = new Date();
            date.setTime(date.getTime() + (60 * 24 * 60 * 60 * 1000));
            date = date.toGMTString();
            document.cookie = "Newsletter-signup=; expires=" + date + "; path=/";
          });
        }
      }
    });
    jQuery(document).ready(function() {
      if (window.location.href.indexOf("?thankyou") > -1) {
        jQuery.fancybox("<h2>Thank you</h2><h3>for signing up.</h3>", {
          width: 500,
          height: 200,
          closeBtn: false,
          afterLoad: function() {
            setTimeout(function() {
              jQuery.fancybox.close();
            },
            3000); // 3 secs
          }
        });
      }
    });
  </script>
  <div class="fancybox" style="display:none;">
    <h3>Yes, I'm interested in staying in touch with the School Maker Faire Program</h3>
    <form name="MailingList" action="http://whatcounts.com/bin/listctrl" method="POST">
      <input type=hidden name="slid" value="6B5869DC547D3D4637EA6E33C6C8170D" />
      <input type="hidden" name="cmd" value="subscribe" />
      <input type="hidden" name="custom_host" value="makerfaire.com" />
      <input type="hidden" name="custom_incentive" value="none" />
      <input type="hidden" name="custom_source" value="modal" />
      <input type="hidden" name="goto" value="http://makerfaire.com/global/school/?thankyou" />
      <input type="hidden" name="custom_url" value="makerfaire.com/global/school" />
      <label>Your Email:</label>
      <input type="email" id="titllrt-titllrt" name="email" required>
      <input type="submit" name="Submit" id="newsletter-set-cookie" value="Sign Me Up" class="btn-cyan btn-modal newsletter-set-cookie">
      <input type="hidden" id="format_mime" name="format" value="mime" />
    </form>
  </div>
<?php } ?>
<!-- End pop up modal for school page -->
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
</body>
</html>
