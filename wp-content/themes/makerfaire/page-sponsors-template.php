<?php
/*
* Template name: Sponsors Landing Page
*/
get_header(); 

?>

<div class="sponsors-landing">
  <div class="row padbottom">
    <div class="col-xs-12">
      <h2 class="pull-left"><?php echo get_the_title(); ?> </h2>
      <a class="sponsors-btn-top" href="/sponsors">BECOME A SPONSOR</a>
    </div>
  </div>

<!-- New sponsors repeating -->
  <?php 
  if( have_rows('sponsors') ) {
    while( have_rows('sponsors') ) {
      the_row();
      $sponsor_label = get_sub_field('sponsor_level_label');
      $logo_size     = get_sub_field('logo_size');
      ?>
      <div class="row sponsors-row">
        <div class="col-xs-12">
          <?php if($sponsor_label!=''){
            echo '<h3 class="sponsors-type text-center">'.$sponsor_label.'</h3>';
          } 
          ?>
          <div class="faire-sponsors-box">
            <?php      
            //sponsors list
            if( have_rows('sponsor_list') ) {
              while( have_rows('sponsor_list') ) {
                the_row();
                $sponsor_logo = get_sub_field('sponsor_logo'); //Photo
                $sponsor_link = get_sub_field('sponsor_link'); //URL
                
                echo '<div class="sponsors-box-'.$logo_size.'">';
                  if( $sponsor_link !='') {
                    echo '<a href="' . $sponsor_link . '" target="_blank">';
                  }
                  echo '<img src="' . $sponsor_logo['url'] . '" alt="Maker Faire sponsor logo" class="img-responsive" />';
                  if( $sponsor_link !='') {
                    echo '</a>';
                  }
                echo '</div>';                
              }
            }
            ?>
          </div>
        </div>
      </div>  
      <?php      
    } 
  } ?>
  
  <!-- PRESENTING SPONSORS -->
  <?php if( have_rows('presenting_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">PRESENTING SPONSORS</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('presenting_sponsors') ): the_row();
            $sub_field_1 = get_sub_field('image'); //Photo
            $sub_field_2 = get_sub_field('url'); //URL

            echo '<div class="sponsors-box-xl">';
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
  <?php endif; ?>

  <!-- PLATINUM SPONSORS -->
  <?php if( have_rows('platinum_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">PLATINUM SPONSOR</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('platinum_sponsors') ): the_row();
            $sub_field_1 = get_sub_field('image'); //Photo
            $sub_field_2 = get_sub_field('url'); //URL

            echo '<div class="sponsors-box-lg">';
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
  <?php endif; ?>

  <!-- GOLDSMITH SPONSORS -->
  <?php if( have_rows('goldsmith_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">GOLDSMITH SPONSORS</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('goldsmith_sponsors') ): the_row();
            $sub_field_1 = get_sub_field('image'); //Photo
            $sub_field_2 = get_sub_field('url'); //URL

            echo '<div class="sponsors-box-lg">';
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
  <?php endif; ?>

  <!-- SILVERSMITH SPONSORS -->
  <?php if( have_rows('silversmith_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">SILVERSMITH SPONSORS</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('silversmith_sponsors') ): the_row();
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
  <?php endif; ?>

  <!-- COPPERSMITH SPONSORS -->
  <?php if( have_rows('coppersmith_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">COPPERSMITH SPONSORS</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('coppersmith_sponsors') ): the_row();
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
  <?php endif; ?>

  <!-- MEDIA SPONSORS -->
  <?php if( have_rows('media_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">MEDIA AND COMMUNITY SPONSORS</h3>
        <div class="faire-sponsors-box">

        <?php
          while( have_rows('media_sponsors') ): the_row();
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
  <?php endif; ?>

  <!-- BLACKSMITH SPONSORS -->
  <?php if( have_rows('blacksmith_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">BLACKSMITH SPONSORS</h3>
        <div class="sponsors-text-box">

        <?php
          while( have_rows('blacksmith_sponsors') ): the_row();
            $sub_field_1 = get_sub_field('name'); //Name
            $sub_field_2 = get_sub_field('url'); //URL

            echo '<div class="sponsors-text-box-inner">';
            if( get_sub_field('url') ):
              echo '<a href="' . $sub_field_2 . '" target="_blank">';
            endif;
            echo '<p>' . $sub_field_1 . '</p>';
            if( get_sub_field('url') ):
              echo '</a>';
            endif;
            echo '</div>';
          endwhile; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- STARTUP SPONSORS -->
  <?php if( have_rows('startup_sponsors') ): ?>
  <div class="row sponsors-row">
    <div class="col-xs-12">
      <h3 class="sponsors-type text-center">STARTUP SPONSORS</h3>
        <div class="sponsors-text-box">

        <?php
          while( have_rows('startup_sponsors') ): the_row();
            $sub_field_1 = get_sub_field('name'); //Name
            $sub_field_2 = get_sub_field('url'); //URL

            echo '<div class="sponsors-text-box-inner">';
            if( get_sub_field('url') ):
              echo '<a href="' . $sub_field_2 . '" target="_blank">';
            endif;
            echo '<p>' . $sub_field_1 . '</p>';
            if( get_sub_field('url') ):
              echo '</a>';
            endif;
            echo '</div>';
          endwhile; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-xs-12 text-center">
      <a class="sponsors-btn-bottom" href="/sponsors">BECOME A SPONSOR</a>
    </div>
  </div>

</div>

<?php get_footer(); ?>
