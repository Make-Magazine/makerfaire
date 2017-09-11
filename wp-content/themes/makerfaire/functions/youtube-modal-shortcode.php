<?php
/**
* Adds the Youtube inside Fancybox modal
* To use: [youtube-modal "wnnWrLt_RCo"]
* Place YT id in shortcade
*/
add_shortcode('youtube-modal', 'youtube_shortcode_modal'); 

function youtube_shortcode_modal($atts){  

  if(!isset($atts[0])) return;
  $id = strip_tags($atts[0]);
  ob_start();
  ?>

    <a class="fancytube fancybox.iframe" href="https://www.youtube.com/embed/<?php echo $id; ?>?autoplay=1">
      <img class="img-responsive" src="https://img.youtube.com/vi/<?php echo $id; ?>/mqdefault.jpg" alt="Maker Faire Video" />
      <i class="fa fa-play-circle-o fa-3x" aria-hidden="true"></i>
    </a>

  <?php
  return ob_get_clean();
}