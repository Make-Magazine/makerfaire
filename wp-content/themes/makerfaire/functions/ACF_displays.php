<?php

function get_acf_content() {   
   $return = '';
   
   // Image Grid
   if (have_rows('image_grid')) {
      // loop through the rows of data
      while (have_rows('image_grid')) {
         the_row();
         $return .= '<div class="image_grid">';
         $return .= '<h2>' . get_sub_field('title') . '</h2>';
         //get list of images
         if (have_rows('image_section')) {
            // loop through the rows of data
            $return .= '<div class="row">';
            while (have_rows('image_section')) {
               the_row();
               $return .= '<div class="col-xs-4 col-sm-3 col-md-2 grid-padding">';
               $imageArr = get_sub_field('grid_image');
               
               $image_url = $imageArr['url'];
               $return .= '<a target="_blank" href="'.$image_url.'"><div class="grid-image" style="background-image: url('.$image_url.');"></div></a>';
               
               $return .= '<div class="img-size">' . $imageArr['width'] .' x ' .$imageArr['height']. '</div>';
               $return .= '<button class="btn universal-btn btn-info btn-copy-html" onclick="copyMe(\'img_'.$imageArr['id'].'\')">COPY HTML</button>';
               $return .= '<div class="copyDiv" id="img_'.$imageArr['id'].'"><a href="https://makerfaire.com/bay-area/"><img src="'.$imageArr['url'].'" alt="'.$imageArr['title'].'" width="'.$imageArr['width'].'" height="'.$imageArr['height'].'" border="0" /></a></div>';
               
               $return .= '</div>';
            }
            $return .= '</div>';
         }
         $return .= '</div>';      
      }
   }
   
   echo $return;
}
