<?php
/**
 * Adds the subscribe header return path overlay
 */
function subscribe_return_path_overlay() { ?>
  <div class="overlay-div overlay-slidedown hidden-xs">
    <div class="container-fluid-overlay">
      <div class="container">
        <div class="row">
          <div class="col-sm-4 overlay-1">
            <img class="img-responsive" src="<?php echo get_template_directory_uri() . '/img/Make-magazine-cover-55-for-overlay.jpg'; ?>" alt="Make: magazine cover, subscribe here" />
          </div>
          <div class="col-sm-4 overlay-2">
            <h2>Get the Magazine</h2>
            <p>Make: is the voice of the Maker Movement, empowering, inspiring, and connecting Makers worldwide to tinker and hack. Subscribe to Make Magazine Today!</p>
            <a class="black-overlay-btn" target="_blank" href="https://readerservices.makezine.com/mk/default.aspx?utm_source=makerfaire.com&utm_medium=brand+bar&utm_campaign=mag+sub&pc=MK&pk=M7GMFE">SUBSCRIBE</a>
          </div>
          <div class="col-sm-4 overlay-3">
            <h2>Sign Up for the Maker Faire Newsletter</h2>
            <p>Keep informed, stay inspired.</p>
            <form class="sub-form whatcounts-signup1o" action="https://secure.whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid" value="6B5869DC547D3D4690C43FE9E066FBC6" /><!-- Confirmation -->
              <input type="hidden" name="custom_list_makerfaire" value="yes" />
              <input type="hidden" name="custom_list_makermedia" value="yes" />
              <input type="hidden" name="cmd" value="subscribe"/>
              <input type="hidden" name="custom_source" value="Subscribe return path overlay"/>
              <input type="hidden" name="custom_incentive" value="none"/>
              <input type="hidden" name="custom_url" value=""/>
              <input type="hidden" id="format_mime" name="format" value="mime"/>
              <input type="hidden" name="goto" value="//makerfaire.com/thanks-for-signing-up"/>
              <input type="hidden" name="custom_host" value="makerfaire.com" />
              <input type="hidden" name="errors_to" value=""/>
              <input name="email" id="wc-email-o" class="overlay-input" placeholder="Enter your email" required type="email"><br>
              <input value="GO" class="black-overlay-btn" type="submit">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    jQuery('#trigger-overlay, .overlay-div').hover(
      function () {
          jQuery('.overlay-div').stop().addClass( 'open' );
          jQuery( 'body' ).addClass( 'modal-open' );
      },
      function () {
          jQuery('.overlay-div').stop().removeClass( 'open' );
          jQuery( 'body' ).removeClass( 'modal-open' );
      }
    );
  </script>
<?php }

/**
 * Adds the newsletter subscribe modal html to the end of the body
 */
function display_thank_you_modal_if_signed_up() { ?>
  <div class="fancybox-thx" style="display:none;">
    <div class="nl-modal-extra-cont nl-thx-p1">
      <div class="nl-modal-div1">
        <div class="col-sm-8 col-xs-12">
          <h4>Welcome to the Make: Community!</h4>
          <p><span class="nl-modal-email-address"></span> you are now signed up to the Maker Faire newsletter.</p>
        </div>
        <div class="col-sm-4 hidden-xs text-center">
          <i class="fa fa-check-square-o fa-5x"></i>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="nl-modal-div2">
        <div class="col-xs-12">
          <?php
            $isSecure = "http://";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $isSecure = "https://";
            }
            ?>
          <h4>You might also like these newsletters:</h4>
          <form class="whatcounts-signup2" action="https://secure.whatcounts.com/bin/listctrl" method="POST">
            <input type="hidden" name="slid" value="6B5869DC547D3D4690C43FE9E066FBC6" /><!-- Confirmation -->
            <input type="hidden" name="cmd" value="subscribe" />
            <input type="hidden" id="email" name="email" value="" />
            <input type="hidden" id="format_mime" name="format" value="mime" />
            <input type="hidden" name="custom_source" value="footer" />
            <input type="hidden" name="custom_incentive" value="none" />
            <input type="hidden" name="custom_url" value="<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
            <input type="hidden" name="goto" value="" />
            <input type="hidden" name="custom_host" value="<?php echo $_SERVER["HTTP_HOST"]; ?>" />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D46B52F3516A785F101_yes" name="custom_list_makenewsletter" value="yes" />
              <span for="list_6B5869DC547D3D46B52F3516A785F101_yes" class="newcheckbox"></span>
            </label>
            <h4>Make: Weekly Digest</h4>
            <p>The best stuff each week from Make: magazine</p>
            <hr />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" name="custom_list_makeeducation" value="yes" />
              <span for="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" class="newcheckbox"></span>
            </label>
            <h4>Make: Education</h4>
            <p>How making is transforming learning</p>
            <hr />
            <label class="list-radio pull-right">
              <input type="checkbox" id="list_6B5869DC547D3D467B33E192ADD9BE4B_yes" name="custom_list_makerpro" value="yes" />
              <span for="list_6B5869DC547D3D467B33E192ADD9BE4B_yes" class="newcheckbox"></span>
            </label>
            <h4>Maker Pro</h4>
            <p>The latest news about startups, products, incubators, and innovators</p>
            <hr />
            <input class="ghost-button-black pull-right" type="submit" value="Submit" />
            <div class="clearfix"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="nl-modal-cont nl-thx-p2" style="display:none;">
    <div class="col-sm-4 hidden-xs nl-modal">
      <span class="fa-stack fa-4x">
      <i class="fa fa-circle-thin fa-stack-2x"></i>
      <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
      </span>
    </div>
    <div class="col-sm-8 col-xs-12 nl-modal">
      <h3>Awesome!</h3>
      <p>Thanks for signing up. Please check your email to confirm.</p>
    </div>
    <div class="clearfix"></div>
  </div>
  <script>
  // Footer newsletter sign up form and modal
  jQuery(document).ready(function(){
    jQuery(".fancybox-thx").fancybox({
      autoSize : false,
      width  : 400,
      autoHeight : true,
      padding : 0,
      afterLoad   : function() {
          this.content = this.content.html();
      }
    });
    jQuery(".nl-thx-p2").fancybox({
      autoSize : false,
      width  : 400,
      autoHeight : true,
      padding : 0,
      afterLoad   : function() {
          this.content = this.content.html();
      }
    });
    // Desktop
    jQuery(document).on('submit', '.whatcounts-signup1', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    // Mobile
    jQuery(document).on('submit', '.whatcounts-signup1m', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email-m').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1m').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    // Header Overlay
    jQuery(document).on('submit', '.whatcounts-signup1o', function (e) {
      e.preventDefault();
      var bla = jQuery('#wc-email-o').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1o').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    });
    jQuery(document).on('submit', '.whatcounts-signup2', function (e) {
      e.preventDefault();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup2').serialize());
      jQuery('.fancybox-thx').hide();
      jQuery('.nl-thx-p2').trigger('click');
    });
    jQuery('input[type="checkbox"]').click(function(e){
      e.stopPropagation();
    });
  });
  </script>
<?php }


