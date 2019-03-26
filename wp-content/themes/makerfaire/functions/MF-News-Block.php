<?php

   function get_first_image_url($html) {
      if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
      return $matches[1];
      }
   }

   function do_news_block($args) {
      if(empty($args['tag'])) {
         echo '<pre>Tag not passed to "do_news_block()"</pre>';
         return false;
         //throw new Error('Arguments not passed to "do_news_block()"');
      };
      $url = 'https://makezine.com/tag/'.$args["tag"].'/feed';
      $rss = fetch_feed( $url);
      
      // Figure out how many total items there are, but limit it to 5.
      $maxitems = $rss->get_item_quantity( 3 );
      // Build an array of all the items, starting with element 0 (first element).
      $rss_items = $rss->get_items( 0, $maxitems );
      // NOTE (ts): add a little sanity check
      if(count($rss_items) === 0) {
         return '<div class="container"><div class="row"><div class="col-xs-12"><p style="color: red; font-size: 24px; padding: 1em 1em 1em 0;">Invalid news tag, or no items available</p></div></div></div>';
      }
      $output = '<div class="container">';
      $output  .= '<!-- Makerfaire news section -->';
      $output  .= '<div class="mf-news">';
      $output  .= '   <div class="row">';
      $output  .= '      <div class="col-xs-12">';
      if(!empty($args['title'])) {
         $output  .= '<h2>'.$args['title'].'</h2>';
      }
      if(!empty($args['link'])) {
         $output  .= '         <p class="see-all">'.html_entity_decode($args['link']).'</p>';
      }
      $output  .= '      </div>';
      $output  .= '   </div>';

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
      $output  .= '<div class="mf-news-cont">'
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


      $output  .= '</div>'; // close `mf-news`
      $output  .= '</div>'; // close `container`
      return $output;
   }
