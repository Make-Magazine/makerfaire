<div class="span4">

  <div class="sidebar-bordered">

    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mr-makey.png" alt="Mr. Makey" class="makey pull-left">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/countdown.png" alt="Mr. Makey" class="counter pull-left">

    <div class="countdown">

      <script type="text/javascript">

        jQuery(document).ready(function() {
          mfba = new Date(2014, 9-1, 20, 9, 00);
          jQuery('.countdown').countdown({
            until: mfba,
            timezone: -5,
            format: 'DHMS',
            layout:'<div class="countdown-numbers"><table><tr><th>{dnn}</th><th>{sep}</td><th>{hnn}</th><th>{sep}</td><th>{mnn}</th><th>{sep}</td><th>{snn}</th></tr><tr class="time"><td>Days</td><td></td><td>Hours</td><td></td><td>Minutes</td><td></td><td>Seconds</td></tr></table></div>',
            timeSeparator:'<span class="separator">:</span>',
          });

        });
      </script>

    </div>

    <p class="buy-tickets"><a href="https://makerfaireny2014.eventbrite.com/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/buy-tickets.png" alt="Buy tickets for Maker Faire" class=""></a></p>

    <h5 class="count-down-sub">September 20 &amp; 21<br />
      New York Hall of Science</h4>
    <h5 class="count-down-sub-sub">Sat. 10 AM - 7 PM | Sun. 10 AM - 6 PM</h5>

  </div>


	<div class="sidebar-bordered-nl">

                <div class="soc_icons" style="padding-top:15px;">
                        <a class="footer-sprite ico-facebook" href="http://facebook.com/makerfaire" title="Facebook" target="_blank"></a>
                        <a class="footer-sprite ico-twitter"  href="http://twitter.com/makerfaire" title="Twitter" target="_blank"></a>
                        <a class="footer-sprite ico-google-plus" href="https://google.com/+MAKE/" title="Google+" target="_blank"></a>
                        <a class="footer-sprite ico-flickr" href="http://www.flickr.com/photos/tags/makerfaire/" title="Flickr" target="_blank"></a>
                        <a class="footer-sprite ico-instagram" href="http://instagram.com/makerfaire" title="Instagram" target="_blank"></a>
                        <a class="footer-sprite ico-youtube" href="http://youtube.com/makerfaire" title="Youtube" target="_blank"></a>
                        <a class="footer-sprite ico-rss" href="http://makezine.com/tag/maker-faire/feed/" title="RSS Feed" target="_blank"></a>
                <!-- END socialArea -->
                </div>

        </div>

        <div class="sidebar-bordered sponsored">
          <h3><a href="#">Presenting Sponsor</a></h3>
          <div id="myCarousel" class="carousel slide">
                       <div class="carousel-inner">
                            <a href="http://thewaltdisneycompany.com/citizenship" target="_blank">
                                 <img src="https://makerfaire.files.wordpress.com/2014/07/disney-sponsor-slider-01.gif" alt="Presented by Disney" />
                            </a>
                       </div>
               </div>

               <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Goldsmith Sponsors</a></h3>

               <div id="myCarousel" class="carousel slide">
                       <div class="carousel-inner">
                               <?php echo mf_sponsor_carousel( 'Goldsmith Sponsor' ); ?>
                       </div>
               </div>

               <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Silversmith Sponsors</a></h3>

               <div id="myCarousel" class="carousel slide">
                       <div class="carousel-inner">
                               <?php echo mf_sponsor_carousel( 'Silversmith Sponsor' ); ?>
                       </div>
               </div>

               <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Coppersmith Sponsors</a></h3>

               <div id="myCarousel" class="carousel slide">
                       <div class="carousel-inner">
                               <?php echo mf_sponsor_carousel( 'Coppersmith Sponsor' ); ?>
                       </div>

               </div>

       </div>
       
         <div class="center twitter">
          <a href="https://twitter.com/makerfaire" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @makerfaire</a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
          <a class="twitter-timeline" href="https://twitter.com/search?q=%23makerfaire" data-widget-id="322225978648698880">Tweets about "#makerfaire"</a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
       
        <div class="sidebar-bordered-nl">

                <h3 class="counter-title">Sign up for news and updates on all faires and calls for makers.</h3>

                <form class="form-inline" action="http://makermedia.createsend.com/t/r/s/jjuruj/" method="post" id="subForm">
                        <input type="text" placeholder="Enter your email" class="news-signup" name="cm-jjuruj-jjuruj" id="jjuruj-jjuruj">
                        <input type="image" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/images/go.png" value="Go!" class="btn">
                        <div class="control-group hide hidden">
                                <h5>Call For Makers</h5>
                                <label for="fielddduult-0"><input id="fielddduult-0" name="cm-fo-dduult" value="621683" type="checkbox" checked/> Inform me About the Call for Makers</label><br>
                                <h5>Preferred Faire?</h5>
                                <label for="fieldjdlthk-0"><input id="fieldjdlthk-0" name="cm-fo-jdlthk" value="188882" type="checkbox" checked/> Bay Area</label><br>
                                <label for="fieldjdlthk-2"><input id="fieldjdlthk-2" name="cm-fo-jdlthk" value="188884" type="checkbox" checked/> New York</label><br>
                                <h5>Join Another Newsletter</h5>
                                <label for="listjrsydu"><input id="listjrsydu" name="cm-ol-jrsydu" type="checkbox" /> Maker Pro Newsletter</label><br />
                                <label for="listttihir"><input id="listttihir" name="cm-ol-ttihir" type="checkbox" /> Maker Shed</label>
                                </p>
                        </div>
                </form>

                <script type="text/javascript">
                jQuery(document).ready(function(){
                        jQuery('.news-signup').focus(function() {
                                jQuery('.hidden').slideDown();
                        });
                });

                </script>
     </div>

	<!-- Beginning Sync AdSlot 2 for Ad unit header ### size: [[300,250]]  -->
	<div id='div-gpt-ad-664089004995786621-2' class="adblock">
		<script type='text/javascript'>
			googletag.cmd.push(function(){googletag.display('div-gpt-ad-664089004995786621-2')});
		</script>
	</div>
	<!-- End AdSlot 2 -->

	<!-- Beginning Sync AdSlot 3 for Ad unit header ### size: [[300,250]]  -->
	<div id='div-gpt-ad-664089004995786621-3' class="adblock">
		<script type='text/javascript'>
			googletag.cmd.push(function(){googletag.display('div-gpt-ad-664089004995786621-3')});
		</script>
	</div>
	<!-- End AdSlot 2 -->

</div>
