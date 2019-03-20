<?php


   function get_first_image_url($html) {
      if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
      return $matches[1];
      }
   }

   function do_news_block($tag) {

      //$tag = $attrs['tag'] ? $attrs['tag'] : 'maker-faire';
      //$url = 'https://makezine.com/tag/maker-faire/feed/';
      $url = 'https://makezine.com/tag/'.$tag.'/feed';
      $rss = fetch_feed( $url);
      
      // Figure out how many total items there are, but limit it to 5.
      $maxitems = $rss->get_item_quantity( 3 );
      // Build an array of all the items, starting with element 0 (first element).
      $rss_items = $rss->get_items( 0, $maxitems );
      // NOTE (ts): add a little sanity check
      if(count($rss_items) === 0) {
         return '<p>No Items Available</p>';
      }
      //image #3
      $description = $rss_items[2]->get_description();
      $image = get_first_image_url($description);
      $description = strip_tags($description);
      $desc_length = iconv_strlen($description, 'UTF-8');
      if ($desc_length > 200) {
         $description = substr($description, 0, 200) . '...';
      }
      $title = esc_html( $rss_items[2]->get_title() );
      $url = esc_url( $rss_items[2]->get_permalink());
      $output  = '<div class="mf-news-cont">'
               . '  <a class="mf-news-big-img" href="'.$url.'" style="background: url(' . legacy_get_resized_remote_image_url($image,622,402) . ');">'
               . '     <div class="mf-news-text-box">'
               . '       <h2>' . $title . '</h2>'
               . '       <p>' . $description . '</p>'
               . '     </div>'
               . '  </a>';

      //image #1
      $description = $rss_items[0]->get_description();
      $image = get_first_image_url($description);
      $description = strip_tags($description);
      $desc_length = iconv_strlen($description, 'UTF-8');
      if ($desc_length > 200) {
         $description = substr($description, 0, 200) . '...';
      }
      $title = esc_html( $rss_items[0]->get_title() );
      $url = esc_url( $rss_items[0]->get_permalink());
      $output .= '  <div class="mf-news-sm-img">'
               . '     <a href="'.$url . '" style="background: url(' . legacy_get_resized_remote_image_url($image,622,402) . ');">'
               . '       <div class="mf-news-text-box mf-news-text-box-sm">'
               . '         <h2>' . $title . '</h2>'
               . '         <p>' . $description . '</p>'
               . '       </div>'
               . '     </a>';

      //image #2
      $description = $rss_items[1]->get_description();
      $image = get_first_image_url($description);
      $description = strip_tags($description);
      $desc_length = iconv_strlen($description, 'UTF-8');
      if ($desc_length > 200) {
         $description = substr($description, 0, 200) . '...';
      }
      $title = esc_html( $rss_items[1]->get_title() );
      $url = esc_url( $rss_items[1]->get_permalink());
      $output .= '    <a href="'.$url . '" style="background: url(' . legacy_get_resized_remote_image_url($image,622,402) . ');">'
               . '       <div class="mf-news-text-box mf-news-text-box-sm">'
               . '         <h2>' . $title . '</h2>'
               . '         <p>' . $description . '</p>'
               . '       </div>'
               . '     </a>'
               . '   </div>'
               . '</div>';
      return $output;

   }


