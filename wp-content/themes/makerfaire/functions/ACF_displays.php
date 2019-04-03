<?php


function do_image_grid($args) {
   // echo '<pre style="display: none;">';
   // var_dump($args);
   // echo '</pre>';
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

};


function do_featured_presenter_grid($args) {
   // echo '<pre style="display: none;">';
   // var_dump($args);
   // echo '</pre>';
   $content = '';
   $content .= '<div class="featured-image-grid">';
   // $content .= '<div class="row">';
   // $content .= '<div class="col-xs-12 grid-inner">';
   foreach($args as $key => $value) {
      $content .= '<div class="grid-item" style="background-image: url('.$value['pres_image'].')">';

      $content .= '  <div class="grid-item-title-block">';
      $content .= '     <h3>'.$value['pres_name'].'</h3>';
      $content .= '     <p>'.$value['pres_title'].'</p>';
      $content .= '  </div>';

      if(!empty($value['button_url'])) {
         $content .= '<a class="grid-item-desc" href="'.$value['button_url'].'">';
      } else {
         $content .= '<div class="grid-item-desc">';
      }
      if(!empty($value['event_title'])) {
         $content .= '     <h4>'.$value['event_title'].'</h4>';
      }
      if(!empty($value['event_datetime'])) {
         $content .= '     <p class="dates">'.$value['event_datetime'].'</p>';
      }
      $desc = $value['event_desc'];

      if(!empty($value['button_url']) && strlen($desc) > 230 ) {
         $breakpoint = strpos($desc, ' ', 220);
         if($breakpoint > 0) {
            $desc = substr($desc, 0, $breakpoint) . '&hellip;';
         }
      }
      else if(strlen($desc) > 300) {
         $breakpoint = strpos($desc, ' ', 290);
         if($breakpoint > 0) {
            $desc = substr($desc, 0, $breakpoint) . '&hellip;';
         }
      }

      $content .= '        <p class="desc-body">'.$desc.'</p>';

      if(!empty($value['button_url']) && !empty($value['button_text'])) {
         $content .= '     <p class="btn btn-blue read-more-link">'.$value['button_text'].'</p>';
      }
      
      if(!empty($value['button_url'])) {
         $content .= '  </a>'; // end desc
      } else {
         $content .= '</div>';  // end desc
      }
      
      $content .= '</div>';
   }
   // $content .= '</div>'; // end col
   // $content .= '</div>'; // end row
   $content .= '</div>'; // end container

   echo $content;
};




function get_acf_content() {
   $mappings = array(
      'image_grid' => 'do_image_grid',
      'featured_presenter_grid' => 'do_featured_presenter_grid'
   );
   $all_fields = get_fields();
   // echo '<pre style="display: none;">';
   // var_dump($all_fields);
   // echo '</pre>';

   foreach($all_fields as $key => $value) {
      if(is_array($value)) {
         //echo 'handle array ' . $key . ' ' . $mappings[$key] . '<br />';
         if(!empty($mappings[$key])) {
            //echo 'handler ' . $mappings[$key] . '<br />';
            $mappings[$key]($value);
         }
      } else {
         //echo $value . '<br />';
      }
   };


};

