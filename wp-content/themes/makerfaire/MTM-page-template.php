<?php
/**
 * Template Name: Meet the Makers page
 */
  get_header();

  $par_post = get_post($post->post_parent);
  $slug = $par_post->post_name;

  function mf_get_topics( ) {
    global $faire;

    //change to pull topics by taxonomy not category
    $cats_tags = get_terms('makerfaire_category',array('hide_empty'=>false));
    $output = '<ul class="columns list-unstyled">';
    foreach ($cats_tags as $cat) {
      if ($cat->slug != 'uncategorized') {
        // $atts['faire'] has been deprecated and will be removed once the production server has been updated.
        // Why? Include both if $atts['faire_url'] needed JE 8.27.14
        $output .= '<li><a href="topics/' .  $cat->slug . '?faire='.$faire.'">' . esc_html( $cat->name ) . '</a></li>';
      }
    }
    $output .= '</ul>';
    return $output;
   }
 ?>

 <div id="wrapper" class="quora">
  <main id="main" role="main">

<!-- The header section with a fullwidth image-->
<?php

  $search_criteria['field_filters'][] = array( 'key' => '304', 'value' => 'Featured Maker');
  $search_criteria['field_filters'][] = array( 'key' => '303', 'value' => 'Accepted');

  $faireArray  = $faireName = '';
  $faire_forms = get_post_meta($post->ID, 'faire-forms', true);
  $faireArray  = explode(',',$faire_forms);

  $faire     = get_post_meta($post->ID, 'faire', true);
  $results = $wpdb->get_results('SELECT * FROM wp_mf_faire where faire= "'.strtoupper($faire).'"');
  $faireName = $results[0]->faire_name;

  $entries = GFAPI::get_entries($faireArray, $search_criteria, null, array('offset' => 0, 'page_size' => 60));

  $randEntryKey = array_rand($entries);
  $randEntry = $entries[$randEntryKey];
  $randEntryId = $randEntry['id'];

  $randPhoto = $randEntry['22'];
  //find out if there is an override image for this page
  $overrideImg = findOverride($randEntry['id'],'mtm');
  if($overrideImg!='') $randPhoto = $overrideImg;
?>

<!-- The slider -->
<div class="featured-holder">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h1>Featured <?php echo $faireName;?> Makers: </h1>
				<div class="gallery-holder">
					<div class="cycle-gallery carousel-gallery">

						<div class="mask">
							<div class="slideset">
                <div class="slide">
                  <a href="/maker/entry/<?php echo $randEntry['id']; ?>">
                    <span class="maker-slider-btn">Learn More About This Maker</span>
                    <img class="img-responsive cycle-gallery-slide" src="<?php echo legacy_get_resized_remote_image_url($randPhoto,1134,442); ?>" alt="Slide Show from Maker Faire <?php echo $faireName;?>">
                  </a>
									<a href="/maker/entry/<?php echo $randEntry['id']; ?>">
                    <div class="text-holder">
				   						<strong class="title">Featured Maker Story</strong>
				   						<p><mark><?php echo $randEntry['151']; ?>: </mark><?php echo $randEntry['16']; ?></p>
                    </div>
                  </a>
                </div>
                <?php for ($i = 0; $i < count($entries); $i++) { if ($i == $randEntryKey) { continue; }  ?>
	              <div class="slide">
                  <a href="/maker/entry/<?php echo $entries[$i]['id']; ?>">
                    <span class="maker-slider-btn">Learn More About This Maker</span>
                    <?php
                    //find out if there is an override image for this page
                    $overrideImg = findOverride($entries[$i]['id'],'mtm');
                    $projPhoto = ($overrideImg==''?$entries[$i]['22']:$overrideImg);?>
                    <img class="img-responsive cycle-gallery-slide" src="<?php echo legacy_get_resized_remote_image_url($projPhoto,1134,442); ?>" alt="Slide Show from Maker Faire <?php echo $faireName;?>">
                  </a>
                  <a href="/maker/entry/<?php echo $entries[$i]['id']; ?>">
					  				<div class="text-holder">
                      <strong class="title">Featured Maker Story</strong>
                      <p><mark><?php echo $entries[$i]['151']; ?>: </mark><?php echo $entries[$i]['16']; ?></p>
                    </div>
                  </a>
                </div>
                <?php } // end for ?>
							</div>
						</div>
            <div class="top-buttons">
              <a class="btn-prev" href="#"><i class="icon-arrow-left"></i></a>
              <a class="btn-next" href="#"><i class="icon-arrow-right"></i></a>
            </div>
					</div>
					<div class="carousel">
						<div class="mask">
							<div class="slideset">
                <div class="slide">
				     			<a href="#"><img class="cycle-gallery-thumb" src="<?php echo legacy_get_resized_remote_image_url($randPhoto,95,95); ?>" alt="Slide gallery thumbnail"></a>
				 				</div>
                <?php
                for ($i = 0; $i < count($entries); $i++) {
                  if ($i == $randEntryKey) { continue; }  ?>
					 				<div class="slide">
                    <?php
                    //find out if there is an override image for this page
                    $overrideImg = findOverride($entries[$i]['id'],'mtm');
                    $projPhoto = ($overrideImg==''?$entries[$i]['22']:$overrideImg);?>
					     			<a href="#"><img class="cycle-gallery-thumb" src="<?php echo legacy_get_resized_remote_image_url($projPhoto,95,95); ?>" alt="Slide gallery thumbnail"></a>
					 				</div>
                <?php
                } // end for ?>
							</div>
						</div>
						<a class="btn-prev" href="#"><i class="icon-arrow-left"></i></a>
						<a class="btn-next" href="#"><i class="icon-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--End of slider -->

<div class="search-box">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-8 text-center padbottom">
				<strong>Looking for a specific Maker? Search by Keyword:</strong>
			</div>
			<div class="col-sm-6 col-md-4 text-center">
				<div class="form-group visible-xs-inline-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block">
        	<form role="search" method="get" class="form-search" id="searchform" action="search/">
						<input type="text"  name="s_term" id="s_term" class="form-control evilquora" />
            <input type="hidden"  name="faire" value="<?php echo $faire;?>" />
						<button type="submit" id="searchsubmit" value="Search"><i class="icon-search"></i></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="browse-box">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				Browse by Topic
				<?php echo mf_get_topics();?>
			</div>
		</div>
	</div>
</div>

<?php the_content(); ?>

<?php
if(get_field('sponsors_page_url')) {
  $url = get_field('sponsors_page_url');
  $id  = url_to_postid( $url ); ?>

  <!-- IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS -->
  <?php if( have_rows('goldsmith_sponsors', $id) || have_rows('silversmith_sponsors', $id) || have_rows('coppersmith_sponsors', $id) || have_rows('media_sponsors', $id) ): ?>
  <div class="sponsor-slide">
    <div class="container">
      <div class="row">
        <div class="col-sm-7">
          <h4 class="sponsor-slide-title">2016 Maker Faire Sponsors: <span class="sponsor-slide-cat"></span></h4>
        </div>
        <div class="col-sm-5">
          <h5><a href="/sponsors">Become a sponsor</a></h5>
          <h5><a href="/bay-area-2016/sponsors">All sponsors</a></h5>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">

          <div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
              <!-- GOLDSMITH SPONSORS -->
              <?php if( have_rows('goldsmith_sponsors', $id) ): ?>
              <div class="item">
                <div class="row spnosors-row">
                  <div class="col-xs-12">
                    <h3 class="sponsors-type text-center">GOLDSMITH</h3>
                      <div class="sponsors-box">
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
                      <div class="sponsors-box">
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
                      <div class="sponsors-box">
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
                      <div class="sponsors-box">
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

</main>
</div> <!-- end of wrapper -->
<?php get_footer(); ?>