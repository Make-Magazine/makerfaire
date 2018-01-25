<?php
/**
 * Template Name: Newsletter Signup Contest
 */
get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="newsletter-landing-page">

      <div class="nlp-top">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <h2>Thanks for coming to Maker Faire!</h2>
            <p>Enter your email below to sign up for the Maker Faire newsletter.</p>
            <input type="email" id="nlp-input" class="form-control" placeholder="Enter your email address" data-toggle="tooltip" data-placement="right" title="Please enter your email" />

          </div>

        </div>

      </div>


      <div class="nlp-bottom">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <form id="nlp-contest" class="nlp-form" action="https://secure.whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid" value="6B5869DC547D3D46E66DEF1987C64E7A" /><!-- Maker Faire -->
              <input type="hidden" name="cmd" value="subscribe" />
              <div id="recapcha-contest" class="g-recaptcha" style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;margin-bottom:-8px;"></div>
              <input type="hidden" id="email" name="email" value="" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="goto" value="" />
              <input type="hidden" name="custom_source" value="faire-contest" />
              <input type="hidden" name="custom_incentive" value="win-drone" />
              <input type="hidden" name="custom_url" value="makerfaire.com/win" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="custom_host" value="makerfaire.com" />
              <input class="btn-cyan" type="submit" value="Submit" />
              <div class="clearfix"></div>
            </form>

          </div>

        </div>

      </div>

    </div>

    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">
          <p class="text-muted"><small>You will receive an email with survey link on signing up. Please follow the link to enter the contest. Contest restricted to U.S. only. No purchase necessary. Duplicate entries will be discarded. Winner will be randomly chosen and alerted via email. Prize is an Extreme Fliers MicroDrone 2.0 set with Quadcopter, HD camera, sensors, remote control, and spare motor set. Retail value: $207.98</small></p>
        </div>
      </div>
    </div>

  <?php endwhile; else: ?>
  
    <div class="container">
      <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
    </div>  
  
  <?php endif; ?>

  <div class="fancybox-nl-contest" style="display:none;">
    <div class="col-sm-4 hidden-xs nl-modal">
      <span class="fa-stack fa-4x">
      <i class="fa fa-circle-thin fa-stack-2x"></i>
      <i class="fa fa-thumbs-o-up fa-stack-1x"></i>
      </span>
    </div>
    <div class="col-sm-8 col-xs-12 nl-modal">
      <h3>Awesome!</h3>
      <p>Follow the survey link in your confirmation email to register for the drone giveaway.</p>
    </div>
    <div class="clearfix"></div>
  </div>

<?php get_footer(); ?>