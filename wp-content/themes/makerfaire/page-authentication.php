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
      <h2 class="text-center">You are Now Logged In</h2>
      <h3 class="text-center billboard">Please wait while we get you back to the right place.</h3>
    </div>
  </div>
</div><!-- end .page-content -->
<script type="text/javascript">
 $('.billboard').billboard({
    messages: [
      'Looking for Makey.',
      'Plugging in the router.',
      'Someone tripped over the power cord.',
      'Searching for the cookies.',
      'Is it faire time yet?.',
      'Updating the countdown clock.',
      'Looking for the lost drones.',
      'adding neon to the tubes.',
      'Greeting our speakers.',
      'Checking the mics.',
      'Thanking our sponsors.',
      'Looking at projects on MakerShare.',
      'Reading Make: magazine.',
      'Thanking our Make: members.',
      'Are we there yet?.',
      'I\'m still working on it.'
    ]
});
</script>

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