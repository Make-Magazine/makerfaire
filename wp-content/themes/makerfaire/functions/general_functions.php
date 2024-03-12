<?php
function mf_clean_title($title) {
  $title = str_replace('&nbsp;', ' ', $title);
  return $title;
}
add_filter('the_title', 'mf_clean_title', 10, 2);


/**
 * Modal Window Builder
 */
function make_modal_builder($atts, $content = null) {
  extract(shortcode_atts(array(
    'launch'  => 'Launch Window',
    'title'   => 'Modal Title',
    'btn_class' => '',
    'embed' => ''
  ), $atts));

  $number = mt_rand();
  $output = '<a class="btn  ' . esc_attr($btn_class) . '" data-toggle="modal" href="#modal-' . $number . '">' . esc_html($launch) . '</a>';
  $output .= '<div id="modal-' . $number . '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
  $output .= '  <div class="modal-header">';
  $output .= '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
  $output .= '    <h3>' . esc_html($title) . '</h3>';
  $output .= '  </div>';
  $output .= '  <div class="modal-body">';
  if (legacy_is_valid_domain($embed,  array('fora.tv', 'ustream.com', 'ustream.tv'))) {
    $output .= '<iframe src="' . esc_url($embed) . '" width="530" height="320" frameborder="0"></iframe>';
  } else {
    $output .= (!empty($embed)) ? wp_oembed_get(esc_url($embed), array('width' => 530)) : '';
  }
  $output .=      wp_kses_post($content);
  $output .= '  </div>';
  $output .= '  <div class="modal-footer">';
  $output .= '    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>';
  $output .= '  </div>';
  $output .= '</div>';

  return $output;
}
add_shortcode('modal', 'make_modal_builder');

add_filter('wp_kses_allowed_html', 'mf_allow_data_atts', 10, 2);
function mf_allow_data_atts($allowedposttags, $context) {
  $tags = array('div', 'a', 'li');
  $new_attributes = array('data-toggle' => true);

  foreach ($tags as $tag) {
    if (isset($allowedposttags[$tag]) && is_array($allowedposttags[$tag]))
      $allowedposttags[$tag] = array_merge($allowedposttags[$tag], $new_attributes);
  }

  return $allowedposttags;
}


add_filter('tiny_mce_before_init', 'mf_filter_tiny_mce_before_init');
function mf_filter_tiny_mce_before_init($options) {

  if (!isset($options['extended_valid_elements']))
    $options['extended_valid_elements'] = '';

  $options['extended_valid_elements'] .= ',a[data*|class|id|style|href]';
  $options['extended_valid_elements'] .= ',li[data*|class|id|style]';
  $options['extended_valid_elements'] .= ',div[data*|class|id|style]';

  return $options;
}

add_filter('jetpack_open_graph_tags', function ($tags) {
  global $post;
  if ($post->post_type == 'mf_form') {
    $json = json_decode($post->post_content);
    $tags['og:description'] = $json->public_description;
  } else {
    setup_postdata($post);
    $tags['og:description'] = get_the_excerpt();
  }

  return $tags;
}, 10);


// show admin bar only for admins and editors
if (!current_user_can('edit_posts')) {
  add_filter('show_admin_bar', '__return_false');
}

/**
 *  Check if input string is a valid YouTube URL
 *  and try to extract the YouTube Video ID from it.
 *  @author  Rio Roth-Barreiro <rio@make.co>
 *  @param   $url   string   The string that shall be checked.
 *  @return  mixed           Returns YouTube Video ID, or (boolean) false.
 */
function parse_yturl($url) {
  $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
  preg_match($pattern, $url, $matches);
  return (isset($matches[1])) ? $matches[1] : false;
}

/**
 *  Check if input string is a valid YouTube or Vimeo URL
 *  @author  Rio Roth-Barreiro <rio@make.co>
 *  @param   $url   string   The string that shall be checked.
 *  @return  true if we are good
 */
function is_valid_video($url) {
  if ((preg_match('#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/shorts/|/playlist\?list=|/watch))(.*?)#x', $url) || preg_match('#^https?://(.+\.)?vimeo\.com/.*#', $url))) {
    return true;
  }
}

// turn a normal url into a youtube embed url
function getYoutubeEmbedUrl($url) {
  $youtube_id = '';
  $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
  $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

  if (preg_match($longUrlRegex, $url, $matches)) {
    $youtube_id = $matches[count($matches) - 1];
  }

  if (preg_match($shortUrlRegex, $url, $matches)) {
    $youtube_id = $matches[count($matches) - 1];
  }

  if ($youtube_id !== '') {
    return 'https://www.youtube.com/embed/' . $youtube_id;
  } else {
    return;
  }
}


/**
 * Allow HTML in WordPress Custom Menu Descriptions
 *
 * Create HTML list of nav menu items and allow HTML tags.
 * Replacement for the native menu Walker, echoing the description.
 * This is the ONLY known way to display the Description field.
 *
 * @see http://wordpress.stackexchange.com/questions/51609/
 *
 */
class Description_Walker extends Walker_Nav_Menu {

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $classes     = empty($item->classes) ? array() : (array) $item->classes;

    $class_names = join(
      ' ',
      apply_filters(
        'nav_menu_css_class',
        array_filter($classes),
        $item
      )
    );

    !empty($class_names)
      and $class_names = ' class="' . esc_attr($class_names) . '"';

    // Build default menu items
    $output .= "<li id='menu-item-$item->ID' $class_names>";

    $attributes  = '';

    !empty($item->attr_title)
      and $attributes .= ' title="'  . esc_attr($item->attr_title) . '"';
    !empty($item->target)
      and $attributes .= ' target="' . esc_attr($item->target) . '"';
    !empty($item->xfn)
      and $attributes .= ' rel="'    . esc_attr($item->xfn) . '"';
    !empty($item->url)
      and $attributes .= ' href="'   . esc_attr($item->url) . '"';

    // Build the description (you may need to change the depth to 0, 1, or 2)
    $description = (!empty($item->description) and 1 == $depth)
      ? '<span class="nav_desc">' .  $item->description . '</span>' : '';

    $title = apply_filters('the_title', $item->title, $item->ID);

    $item_output = $args->before
      . "<a $attributes>"
      . $args->link_before
      . $title
      . '</a> '
      . $args->link_after
      . $description
      . $args->after;

    // Since $output is called by reference we don't need to return anything.
    $output .= apply_filters(
      'walker_nav_menu_start_el',
      $item_output,
      $item,
      $depth,
      $args
    );
  }
}
// Allow HTML descriptions in WordPress Menu
remove_filter('nav_menu_description', 'strip_tags');
add_filter('wp_setup_nav_menu_item', 'cus_wp_setup_nav_menu_item');
function cus_wp_setup_nav_menu_item($menu_item) {
  $menu_item->description = apply_filters('nav_menu_description', $menu_item->post_content);
  return $menu_item;
}
    //and then use this in your template:
    //wp_nav_menu( array( 'walker' => new Description_Walker ));
