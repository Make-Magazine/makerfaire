<?php /* Template Name: Flagship Faire Landing Page */  ?>
<?php get_header(); ?>
<?php  $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full' ); ?>
</style>
<?php the_content(); ?>

<?php
if(get_field('sponsors_page_url'))
{
  $url = get_field('sponsors_page_url');
  $year = get_field('sponsors_page_year');
  $id = url_to_postid( $url ); ?>

  <!-- IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS -->
  <?php if( have_rows('goldsmith_sponsors', $id) || have_rows('silversmith_sponsors', $id) || have_rows('coppersmith_sponsors', $id) || have_rows('media_sponsors', $id) ): ?>
  <div class="sponsor-slide">
    <div class="container">
      <div class="row">
        <div class="col-sm-7">
          <h4 class="sponsor-slide-title"><?php if($year){echo $year . ' ';} ?>Maker Faire Sponsors: <span class="sponsor-slide-cat"></span></h4>
        </div>
        <div class="col-sm-5">
          <h5><a href="/sponsors">Become a sponsor</a></h5>
          <h5><a href="<? echo $url; ?>">All sponsors</a></h5>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">

          <div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">

              <!-- PLATINUM SPONSORS -->
              <?php if( have_rows('platinum_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">Platinum</h3>
                      <div class="faire-sponsors-box">
                      <?php
                        while( have_rows('platinum_sponsors', $id) ): the_row();
                          $sub_field_1 = get_sub_field('image'); //Photo
                          $sub_field_2 = get_sub_field('url'); //URL

                          echo '<div class="sponsors-box-md">';
                          if( get_sub_field('url') ):
                            echo '<a href="' . $sub_field_2 . '" target="_blank">';
                          endif;
                          echo '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                          if( get_sub_field('url') ):
                            echo '</a>';
                          endif;
                          echo '</div>';
                        endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- GOLDSMITH SPONSORS -->
              <?php if( have_rows('goldsmith_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">GOLDSMITH</h3>
                      <div class="faire-sponsors-box">
                      <?php
                        while( have_rows('goldsmith_sponsors', $id) ): the_row();
                          $sub_field_1 = get_sub_field('image'); //Photo
                          $sub_field_2 = get_sub_field('url'); //URL

                          echo '<div class="sponsors-box-md">';
                          if( get_sub_field('url') ):
                            echo '<a href="' . $sub_field_2 . '" target="_blank">';
                          endif;
                          echo '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                          if( get_sub_field('url') ):
                            echo '</a>';
                          endif;
                          echo '</div>';
                        endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- SILVERSMITH SPONSORS -->
              <?php if( have_rows('silversmith_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">SILVERSMITH</h3>
                      <div class="faire-sponsors-box">
                      <?php
                        while( have_rows('silversmith_sponsors', $id) ): the_row();
                          $sub_field_1 = get_sub_field('image'); //Photo
                          $sub_field_2 = get_sub_field('url'); //URL

                          echo '<div class="sponsors-box-md">';
                          if( get_sub_field('url') ):
                            echo '<a href="' . $sub_field_2 . '" target="_blank">';
                          endif;
                          echo '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                          if( get_sub_field('url') ):
                            echo '</a>';
                          endif;
                          echo '</div>';
                        endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- COPPERSMITH SPONSORS -->
              <?php if( have_rows('coppersmith_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">COPPERSMITH</h3>
                      <div class="faire-sponsors-box">
                      <?php
                        while( have_rows('coppersmith_sponsors', $id) ): the_row();
                          $sub_field_1 = get_sub_field('image'); //Photo
                          $sub_field_2 = get_sub_field('url'); //URL

                          echo '<div class="sponsors-box-md">';
                          if( get_sub_field('url') ):
                            echo '<a href="' . $sub_field_2 . '" target="_blank">';
                          endif;
                          echo '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                          if( get_sub_field('url') ):
                            echo '</a>';
                          endif;
                          echo '</div>';
                        endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- MEDIA SPONSORS -->
              <?php if( have_rows('media_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">MEDIA</h3>
                      <div class="faire-sponsors-box">
                      <?php
                        while( have_rows('media_sponsors', $id) ): the_row();
                          $sub_field_1 = get_sub_field('image'); //Photo
                          $sub_field_2 = get_sub_field('url'); //URL

                          echo '<div class="sponsors-box-md">';
                          if( get_sub_field('url') ):
                            echo '<a href="' . $sub_field_2 . '" target="_blank">';
                          endif;
                          echo '<img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                          if( get_sub_field('url') ):
                            echo '</a>';
                          endif;
                          echo '</div>';
                        endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <!-- END SPONSOR SLIDER -->
<?php } ?>

<script>
(function() { // Random background image from 'flagship-background' custom fields:
  var img = [];
  <?php foreach (get_post_meta($post->ID, 'flagship-background', false) as $i) {
    echo 'img.push(\'' . $i . '\');'; // push all meta images src to js array
  } ?>
  document.getElementById('flagship-faire-wrp').style.backgroundImage = 'url(' + img[Math.floor(Math.random() * img.length)] + ')';
})();

// Update the sponsor slide title each time the slide changes
jQuery('.carousel-inner .item:first-child').addClass('active');
jQuery(function() {
  var title = jQuery('.item.active .sponsors-type').html();
  jQuery('.sponsor-slide-cat').text(title);
  jQuery('#carousel-sponsors-slider').on('slid.bs.carousel', function () {
    var title = jQuery('.item.active .sponsors-type').html();
    jQuery('.sponsor-slide-cat').text(title);
  })
});
</script>
<?php get_footer(); ?>
