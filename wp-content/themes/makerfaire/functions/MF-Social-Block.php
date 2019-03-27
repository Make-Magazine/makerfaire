<?php

   function do_social_block($args) {

      $hashtags = empty($args['hashtags']) ? '#makerfaire' : $args['hashtags'];
      $personlization_id = empty($args['personlization_id']) ? '764268' : $args['personlization_id'];
      $title = empty($args['title']) ? 'Share Your Maker Faire Experience' : $args['title'];

      $content = '<div class="container mf-tint-social">';
      $content .= ' <div class="row"><div class="col-xs-12">';
      $content .= '  <h2>'.$title.'</h2>';
      $content .= '  <h4>'.$hashtags.' on';
      $content .= '     <a href="https://twitter.com/makerfaire"><i aria-hidden="true" class="fa fa-twitter"></i></a>';
      $content .= '     <a href="https://www.instagram.com/makerfaire/"><i aria-hidden="true" class="fa fa-instagram"></i></a>';
      $content .= '     <a href="https://www.facebook.com/makerfaire"><i class="fa fa-facebook" aria-hidden="true"></i></a></h4>';
      $content .= '  <div class="clearfix"></div>';
      $content .= '  <script async src="https://d36hc0p18k1aoc.cloudfront.net/public/js/modules/tintembed.js"></script>';
      $content .= '  <div class="tintup" data-columns="" data-id="makerfaire" data-infinitescroll="true" data-mobilescroll="true" data-personalization-id="'.$personlization_id.'"></div><!-- END TINT SCRIPT -->';
      $content .= '</div></div>'; // end row/col
      $content .= '</div>'; // end container

      return $content;
   }


?>