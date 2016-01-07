<?php 
/* Retrieve the faire slug.  The Entries page are not under a specific faire parent, so the slug is set on that page */
global $slug; 
?>
<div class="col-md-4">
    <!--    <div class="sidebar-bordered">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mr-makey.png" alt="Mr. Makey" class="makey pull-left">
        <h3 class="counter-title">Maker Faire Bay Area<br />May 16 &amp; 17, 2015</h3>
        
        <div class="countdown">
            <script type="text/javascript">
                    jQuery(document).ready(function() {
                            mfba = new Date(2015, 5-1, 16, 9, 00);
                            jQuery('.countdown').countdown({
                                    until: mfba,
                                    timezone: -8,
                                    format: 'DHMS',
                                    layout:'<div class="countdown-numbers"><table><tr><th>{dnn}</th><th>{sep}</td><th>{hnn}</th><th>{sep}</td><th>{mnn}</th><th>{sep}</td><th>{snn}</th></tr><tr class="time"><td>Days</td><td></td><td>Hours</td><td></td><td>Minutes</td><td></td><td>Seconds</td></tr></table></div>',
                                    timeSeparator:'<span class="separator">:</span>',
                            });
                    });
            </script>
        </div>
        </div>
        
        <div class="center">
        <a href="https://mfba2015.eventbrite.com/" target="_blank">
        <img class="img-responsive" width="100%" src="<?php echo get_stylesheet_directory_uri(); ?>/images/MF15_BA_300x250.gif" />
        </a>
        </div>
        -->
      <?php 
            $goldSmith = mf_sponsor_carousel( 'Goldsmith Sponsor',$slug ); 
            $silverSmith = mf_sponsor_carousel( 'Silversmith Sponsor',$slug ); 
            $copperSmith = mf_sponsor_carousel( 'Coppersmith Sponsor',$slug ); 
        if($goldSmith!=FALSE || $silverSmith!=FALSE || $copperSmith!=FALSE){
        ?>      
    <div class="sidebar-bordered sponsored">    
        <?php if($goldSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Goldsmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $goldSmith; ?>
            </div>
        </div>
        <?php }?>
        <?php if($silverSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Silversmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $silverSmith; ?>
            </div>
        </div>
        <?php }?>
        <?php if($copperSmith!=FALSE){?>
        <h3><a href="<?php echo esc_url( home_url( '/sponsors' ) ); ?>">Coppersmith Sponsors</a></h3>
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <?php echo $copperSmith; ?>
            </div>
        </div>
        <?php }?>
    </div>
        <?php }?>
    <div class="center twitter">
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
        <a class="twitter-timeline" href="https://twitter.com/search?q=%23makerfaire" data-widget-id="322225978648698880">Tweets about "#makerfaire"</a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        <a href="https://twitter.com/makerfaire" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @makerfaire</a>
    </div>
    <!-- Beginning Sync AdSlot 2 for Ad unit header ### size: [[300,250]]  -->
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-12">
            <div id='div-gpt-ad-664089004995786621-2' class="adblock">
                <script type='text/javascript'>
                    googletag.cmd.push(function(){googletag.display('div-gpt-ad-664089004995786621-2')});
                </script>
            </div>
        </div>
    </div>
        <!-- End AdSlot 2 -->
        <!-- Beginning Sync AdSlot 3 for Ad unit header ### size: [[300,250]]  -->
            <div class="row">

        <div class="col-xs-12 col-sm-6 col-md-12">
            <div id='div-gpt-ad-664089004995786621-3' class="adblock">
                <script type='text/javascript'>
                    googletag.cmd.push(function(){googletag.display('div-gpt-ad-664089004995786621-3')});
                </script>
            </div>
        </div>
    </div>
    <!-- End AdSlot 2 -->
</div>
