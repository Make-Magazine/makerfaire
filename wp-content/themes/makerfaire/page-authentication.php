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
    
function shuffle(array) {
    let counter = array.length;
    // While there are elements in the array
    while (counter > 0) {
        // Pick a random index
        let index = Math.floor(Math.random() * counter);

        // Decrease counter by 1
        counter--;

        // And swap the last element with it
        let temp = array[counter];
        array[counter] = array[index];
        array[index] = temp;
    }
    return array;
}
    
function billboard(element, options) {
    var defaults = {
            messages: [],
            interval: 3000
        },
        plugin = this,
        currentIndex = 0,
        $element = jQuery(element);
    plugin.settings = {};
    var displayNext = function () {
        clearTimeout(plugin.timerId);
        if (currentIndex >= plugin.settings.messages.length - 1) {
            currentIndex = 0;
        } else {
            currentIndex++;
        }
        jQuery(element).fadeOut("fast", function () {
            jQuery(element).text(plugin.settings.messages[currentIndex]);
        });
        jQuery(element).fadeIn("fast");
        plugin.timerId = setTimeout(displayNext, plugin.settings.interval);
    };
    var start = function () {
        plugin.timerId = setTimeout(displayNext, plugin.settings.interval);
    };
    plugin.init = function () {
        plugin.settings = jQuery.extend({}, defaults, options);
        $element.on('click', function(){
            displayNext();
        });
        start();
    };
    plugin.init();
};

jQuery.fn.billboard = function (options) {
    return this.each(function () {
        if (undefined == jQuery(this).data('billboard')) {
            var plugin = new billboard(this, options);
            jQuery(this).data('billboard', plugin);
        }
    });
};
    
var brief_messages = [
      'Looking for Makey.',
      'Plugging in the router.',
      'Someone tripped over the power cord.',
      'Searching for the cookies.',
      'Is it Faire time yet?',
      'Updating the countdown clock.',
      'Looking for the lost drones.',
      'Adding neon to the tubes.',
      'Greeting our speakers.',
      'Checking the mics.',
      'Thanking our sponsors.',
      'Looking at projects on Maker Share.',
      'Reading Make: magazine.',
      'Thanking our Make: members.',
      'Are we there yet?',
      'I\'m still working on it.'
    ];
    
jQuery('.billboard').billboard({
    messages: shuffle(brief_messages),
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