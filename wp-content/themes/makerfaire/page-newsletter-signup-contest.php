<?php
/**
 * Template Name: Newsletter Signup Contest
 */
get_header(); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="newsletter-landing-page">

      <div class="row nlp-top">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <h2>Thanks for coming to Maker Faire!</h2>
            <p>Enter your email to sign up for the Maker Faire newsletter and register to win a drone.</p>
            <input type="email" id="nlp-input" class="form-control" placeholder="Enter your email address" data-toggle="tooltip" data-placement="right" title="Please enter your email" />

          </div>

        </div>

      </div>


      <div class="row nlp-bottom">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <form id="nlp-form" class="nlp-form" action="http://whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid_1" value="6B5869DC547D3D46E66DEF1987C64E7A" /><!-- MakerFaire Newsletter -->
              <input type="hidden" name="slid_2" value="6B5869DC547D3D46941051CC68679543" /><!-- Maker Media Newsletter -->
              <input type="hidden" name="cmd" value="subscribe" />
              <input type="hidden" name="multiadd" value="1" />
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
            <script>
              jQuery(document).on('submit', '#nlp-form', function (e) {
                e.preventDefault();
                // Now get the email into the form and send
                var nlpEmail = jQuery('#nlp-input').val();
                jQuery('#nlp-form #email').val(nlpEmail);
                if (jQuery('#nlp-form #email').val() == '') {
                  jQuery('#nlp-input').tooltip()
                  jQuery('#nlp-input').tooltip('show')
                  return false;
                }
                else {
                  jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('#nlp-form').serialize());
                  jQuery('.fancybox-thx').trigger('click');
                  jQuery('.nl-thx-p1').hide();
                  jQuery('.nl-thx-p2').show();
                  jQuery('#nlp-input').val('');
                }
              });
            </script>

          </div>

        </div>

      </div>

    </div>

  <?php endwhile; else: ?>
  
    <div class="container">
      <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
    </div>  
  
  <?php endif; ?>

<?php get_footer(); ?>