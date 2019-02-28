<?php

function get_acf_content() {   
   $return = '<input type="hidden" class="copied"/>';
   // Image Grid
   if (have_rows('image_grid')) {
      // loop through the rows of data
      while (have_rows('image_grid')) {
         the_row();
         $return .= '<div>';
         $return .= '<h2>' . get_sub_field('title') . '</h2>';
         //get list of images
         if (have_rows('image_section')) {
            // loop through the rows of data
            $return .= '<div class="row">';
            while (have_rows('image_section')) {
               the_row();
               $return .= '<div class="col-md-2 grid-padding">';               
               $imageArr = get_sub_field('grid_image');               
               
               $image_url = $imageArr['url'];
               $return .= '<div class="grid-image" style="background-image: url('.$image_url.');"></div>';
               //$return .= '<p>' . get_sub_field('image_name') . '</p>';
               $return .= '<p>' . $imageArr['width'] .' x ' .$imageArr['height']. '</p>';
               $return .= '<button class="btn universal-btn btn-info" onclick="copyMe(\'img_'.$imageArr['id'].'\')">COPY HTML</button>';
               $return .= '<div class="copyDiv" id="img_'.$imageArr['id'].'">&lt;a href="https://makerfaire.com/bay-area/"&gt;&lt;img src="'.$imageArr['url'].'" alt="'.$imageArr['title'].'" width="'.$imageArr['width'].'" height="'.$imageArr['height'].'" border=0" /&gt;&lt;/a&gt;</div>';
               $return .= '</div>';
            }
            $return .= '</div>';
         }
         $return .= '</div>';      
      }
   }
   $return .=
           '
      <style type="text/css">
      .copyDiv {
      opacity: 0; position: absolute; z-index: -1;
      }
      .grid-padding {
      padding: 20px;
      text-align: center;
      }
      .grid-padding .btn {
       width: 150px;
      }
      .grid-image {
    width: 150px;
    height: 150px;
    background-size: contain;
    background-repeat: no-repeat;
    background-color: black;
    background-position: center;
      }
      </style>
      <script>
function copyMe(elmnt) {  
  var n = jQuery("#"+elmnt).text();
  n = jQuery.trim(n);
  //alert("copying "+ elmnt+" "+n);
  jQuery(".copied").attr("value", n).select();
  document.execCommand("copy");    
}
</script>';
   echo $return;
}
