<?php
function routes_image($attachment_id, $size, $return_attrs = false) {
   $return = '';
   if ($attachment_id) {
      $image = wp_get_attachment_image_src($attachment_id, $size);
      if ($image) {
         if ($return_attrs) {
            return $image;
         }
         return $image[0];
      }
   }
   return $return;
}

function routes_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}
