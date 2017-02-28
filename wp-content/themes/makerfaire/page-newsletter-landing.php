<?php
/**
 * Template Name: Newsletter Subscribe Landing Page
 *
 * @package    makeblog
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */
get_header('version-2'); ?>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="newsletter-landing-page">

      <div class="nlp-top">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <h2>Maker Faire Updates</h2>
            <p>Enter your email to keep up with the Greatest Show (&amp; Tell) on Earth</p>
            <input type="email" id="nlp-input" class="form-control" placeholder="Enter your email address" data-toggle="tooltip" data-placement="right" title="Please enter your email" />

          </div>

        </div>

      </div>

      <div class="nlp-bottom">

        <div class="container">

          <div class="col-sm-8 col-md-6 col-lg-4 col-sm-offset-2 col-md-offset-3 col-lg-offset-4">

            <form id="nlp-form" class="nlp-form" action="http://whatcounts.com/bin/listctrl" method="POST">
              <input type="hidden" name="slid_1" value="6B5869DC547D3D46941051CC68679543" /><!-- Maker Media Newsletter -->
              <input type="hidden" name="cmd" value="subscribe" />
              <input type="hidden" name="multiadd" value="1" />
              <input type="hidden" id="email" name="email" value="" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="goto" value="" />
              <input type="hidden" name="custom_source" value="landing-page_signup" />
              <input type="hidden" name="custom_incentive" value="none" />
              <input type="hidden" name="custom_url" value="makezine.com/join" />
              <input type="hidden" id="format_mime" name="format" value="mime" />
              <input type="hidden" name="custom_host" value="makezine.com" />

              <label class="list-radio pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                <input type="checkbox" id="list_6B5869DC547D3D46B52F3516A785F101_yes" name="slid_2" value="6B5869DC547D3D46B52F3516A785F101" />
                <span for="list_6B5869DC547D3D46B52F3516A785F101_yes" class="newcheckbox"></span>
              </label>
              <h4>Make: Community</h4><p>News and information from and about makers</p>
              <hr />

              <div class="list-row">
                <h4>Flagship Maker Faires</h4><p>Event-specific updates from our annual flagship Faires</p>

                <label class="list-radio pull-right">
                  <input type="checkbox" id="list_6B5869DC547D3D4679B07245D96C075A_yes" name="slid_3" value="6B5869DC547D3D4679B07245D96C075A" />
                  <span for="list_6B5869DC547D3D4679B07245D96C075A_yes" class="newcheckbox"></span>
                </label>
                <h5>Chicago</h5>

                <label class="list-radio pull-right">
                  <input type="checkbox" id="list_6B5869DC547D3D461285274DDB064BAC_yes" name="slid_4" value="6B5869DC547D3D461285274DDB064BAC" />
                  <span for="list_6B5869DC547D3D461285274DDB064BAC_yes" class="newcheckbox"></span>
                </label>
                <h5>Bay Area</h5>

                <label class="list-radio pull-right">
                  <input type="checkbox" id="list_6B5869DC547D3D4641ADFD288D8C7739_yes" name="slid_5" value="6B5869DC547D3D4641ADFD288D8C7739" />
                  <span for="list_6B5869DC547D3D4641ADFD288D8C7739_yes" class="newcheckbox"></span>
                </label>
                <h5>New York</h5>

              </div>
              <hr />

              <label class="list-radio pull-right" data-toggle="tooltip" data-placement="right" title="Please choose at least one checkbox">
                <input type="checkbox" id="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" name="slid_6" value="6B5869DC547D3D4637EA6E33C6C8170D" />
                <span for="list_6B5869DC547D3D4637EA6E33C6C8170D_yes" class="newcheckbox"></span>
              </label>
              <h4>Local Maker Faires</h4><p>Find out about faires near you</p>
              <hr />

              <input class="btn-cyan" type="submit" value="Submit" />
              <div class="clearfix"></div>
            </form>
            <script>
              jQuery(document).on('submit', '#nlp-form', function (e) {
                e.preventDefault();
                // First check if any checkboxes are checked
                var anyBoxesChecked = false;
                jQuery('#nlp-form input[type="checkbox"]').each(function() {
                  if (jQuery(this).is(":checked")) {
                    anyBoxesChecked = true;
                  }
                });
                if (anyBoxesChecked == false) {
                  jQuery('.list-radio[data-toggle="tooltip"]').tooltip()
                  jQuery('.list-radio[data-toggle="tooltip"]').tooltip('show')
                  return false;
                }
                // Now get the email into the form and send
                else {
                  var nlpEmail = jQuery('#nlp-input').val();
                  jQuery('#nlp-form #email').val(nlpEmail);
                  if (jQuery('#nlp-form #email').val() == '') {
                    jQuery('#nlp-input').tooltip()
                    jQuery('#nlp-input').tooltip('show')
                    return false;
                  }
                  else {
                    jQuery.post('http://whatcounts.com/bin/listctrl', jQuery('#nlp-form').serialize());
                    var nlpDomain = document.domain;
                    location.href = '/?subscribed-to-make-newsletter';
                  }
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