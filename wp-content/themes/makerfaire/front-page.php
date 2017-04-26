<?php
/**
 * Template Name: Home page
 */
get_header();
?>
<main id="main" class="quora front-page" role="main">

  <div class="carousel-holder">
    <div class="social-popup popup-active hidden-xs">
      <a class="open" href="#"><i class="icon-share"></i></a>
      <div class="popup">
        <a class="close" href="#"><i class="icon-close"></i></a>
        <ul class="social-list">
          <li class="facebook"><a href="http://www.facebook.com/makerfaire" target="_blank"><i class="icon-facebook"></i></a></li>
          <li class="twitter"><a href="http://twitter.com/makerfaire"><i class="icon-twitter" target="_blank"></i></a></li>
          <li class="instagram"><a href="//instagram.com/makerfaire" target="_blank"><i class="icon-instagram"></i></a></li>
          <li class="googleplus"><a href="http://plus.google.com/communities/105823492396218903971" target="_blank"><i class="icon-googleplus"></i></a></li>
        </ul>
      </div>
    </div>
    <div class="carousel-inner">
      <div class="mask">
        <div class="slideset">
          <?php
          $sorting = array( 'key' => 5, 'direction' => 'ASC' );
          $search_criteria['status'] = 'active';
          $entries = GFAPI::get_entries(24, $search_criteria, $sorting, array('offset' => 0, 'page_size' => 10));
          foreach ($entries as $entry):
          ?>
          <div class="slide" data-url="<?php echo $entry['4'] ?>">
            <div class="bg-stretch">
              <a href="<?php echo $entry['4'] ?>"><img src="<?php echo legacy_get_resized_remote_image_url($entry['1'],1274,370); ?>" alt="Maker Faire slide show image"></a>
            </div>
            <div class="text-box">
              <div class="container">
                <div class="row">
                  <div class="col-xs-12">
                  <a href="<?php echo $entry['4'] ?>" style="color:#FFF;">
                    <h1><?php echo $entry['2'] ?></h1>
                    <p><?php echo $entry['3'] ?> <span class="icon-arrow-right"></span></p>
                  </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="btn-box">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <a class="btn-prev" href="#"><span class="icon-arrow-left"></span></a>
              <a class="btn-next" href="#"><span class="icon-arrow-right"></span></a>
            </div>
          </div>
        </div>
      </div>
      <div class="pagination"></div>
    </div>
  </div>


  <div class="container">

    <div class="row upcoming-event-container">

      <?php if( have_rows('event_info') ): ?>
        <?php while( have_rows('event_info') ): the_row();

          $image = get_sub_field('image');
          $small_top_text = get_sub_field('small_top_text');
          $large_center_text = get_sub_field('large_center_text');
          $small_bottom_text = get_sub_field('small_bottom_text');
          $event_url = get_sub_field('event_url');
          $button_1_text = get_sub_field('button_1_text');
          $button_1_url = get_sub_field('button_1_url'); 
          $button_2_text = get_sub_field('button_2_text');
          $button_2_url = get_sub_field('button_2_url'); ?>

          <article class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="<?php echo $event_url; ?>">
              <?php if ($image) { ?>
                <img src="<?php echo $image; ?>" alt="Maker Faire Event Badge" class="img-responsive hidden-xs" />
              <?php } ?>
              <div class="home-event-text">
                <?php if ($small_top_text) { ?>
                  <h3><?php echo $small_top_text; ?></h3>
                <?php }
                if ($large_center_text) { ?>
                  <h2><?php echo $large_center_text; ?></h2>
                <?php }
                if ($small_bottom_text) { ?>
                  <h4><?php echo $small_bottom_text; ?></h4>
                <?php } ?>
              </div>
            </a>
            <?php if ($button_1_text && $button_1_url) { ?>
              <a class="btn btn-danger pull-right" href="<?php echo $button_1_url; ?>"><?php echo $button_1_text; ?></a>
            <?php }
            if ($button_2_text && $button_2_url) { ?>
              <a class="btn btn-danger pull-right" href="<?php echo $button_2_url; ?>"><?php echo $button_2_text; ?></a>
            <?php } ?>
          </article>

        <?php endwhile; ?>
      <?php endif; ?>

    </div>

    <hr class="double-line-hr">


    <div class="mmakers">
      <div class="row">
        <div class="col-xs-12">
          <p class="see-all pull-right"><?php echo get_field( "mtm_title" ); ?></p>
        </div>
      </div>
      <?php
      $mtm_shortcode = get_field( "mtm_shortcode" );
      echo do_shortcode($mtm_shortcode);
      ?>
    </div>


    <div class="mf-news">
      <div class="row">
        <div class="col-xs-12">
          <p class="see-all pull-right"><?php echo get_field( "mf_news_title" ); ?></p>
        </div>
      </div>
      <?php echo do_shortcode("[mf-news]"); ?>
    </div>


  </div>


  <div class="location-holder">
    <div class="container">
      <div class="picture-holder">
        <img alt="image description" height="74" src="/wp-content/uploads/2015/04/maker-robot.png" width="53">
      </div>
      <a href="/map/">There are Maker Faires all over the world. Find one near you! <i class="icon-arrow-right"></i></a>
    </div>
  </div>


  <div class="container mf-sumome">
    <h2>Share Your Maker Faire Experience</h2>
    <h4>#makerfaire and #MFBA17 on <i aria-hidden="true" class="fa fa-twitter"></i>, <i aria-hidden="true" class="fa fa-instagram"></i></h4>
    <div class="clearfix"></div>
    <script async src="https://d36hc0p18k1aoc.cloudfront.net/public/js/modules/tintembed.js"></script>
    <div class="tintup" data-columns="" data-id="makerfaire" data-infinitescroll="true" data-mobilescroll="true" data-personalization-id="764268" style="height:600px;width:100%;"></div><!-- END TINT SCRIPT -->
  </div>


  <div class="question-holder">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-lg-10 col-lg-offset-1">
          <div class="picture-holder"><img alt="image description" height="71" src="/wp-content/uploads/2015/04/maker-robot-textbox.png" width="66"></div>
          <h1>What is Maker Faire?</h1>We call it the Greatest Show (& Tell) on Earth. Maker Faire is part science fair, part county fair, and part something entirely new! As a celebration of the Maker Movement, it's a family-friendly showcase of invention, creativity, and resourcefulness. Faire gathers together tech enthusiasts, crafters, educators, tinkerers, food artisans, hobbyists, engineers, science clubs, artists, students, and commercial exhibitors. Makers come to show their creations and share their learnings. Attendees flock to Maker Faire to glimpse the future and find the inspiration to become Makers themselves.
        </div>
      </div>
    </div>
  </div>

</main>
<img height="1" src="https://www.facebook.com/tr?id=399923000199419&ev=PageView&noscript=1" style="display:none" width="1">
<?php get_footer(); ?>