<?php
/**
 * Template Name: Authenticated Redirect page
 */
?>
<?php get_header(); ?>
<div id="authenticated-redirect" class="container page-content">
  <div class="row">
    <div class="col-sm-3 col-xs-12">
      <img src="/wp-content/themes/makerfaire/img/makey-stickers-slanted.png"  class="img-responsive" />
    </div>
    <div class="col-sm-9 col-xs-12">
      <h2 class="text-center">Please wait while we complete the login process.</h2>
      <h3 class="text-center">You will be redirected to the page you were trying to access shortly.</h3>
      <h3 class="text-center billboard">Please wait while we get you back to the right place.</h3>
      <p class="text-center"><i>** This site utilizes cookies to log you in. Please ensure you have cookies enabled. **</i></p>
    </div>
  </div>
</div><!-- end .page-content -->

<div class="container">
    <div class="row">
      <div class="col-xs-12">
        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
          <?php endwhile; ?>
        <?php else: ?>
          <?php get_404_template(); ?>
        <?php endif; ?>
      </div>
    </div>
</div>
<?php get_footer();