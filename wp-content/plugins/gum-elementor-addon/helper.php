<?php
defined('ABSPATH') or die();
/* 
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.0
 */

final class Gum_Elementor_Helper{


  public static  function _get_nav_menu( $main_menu_id = ""){

    $menuParams=array(
      'theme_location' => '',
      'echo' => false,
      'container_class'=>'',
      'container_id'=>'gum-main-menu',
      'menu_class'=>'gum-menu toggle-collapse',
      'container'=>'',
      'before' => '',
      'after' => '',
      'fallback_cb'=>false,
      'menu' => absint($main_menu_id),
      'walker'  => new gum_mainmenu_walker(),
      'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
    );

    if($menu=wp_nav_menu($menuParams)){
        return $menu;
    }

    $menuParams['container'] = "ul";
    $menuParams['fallback_cb']="wp_page_menu";
    $menuParams['walker']= new gum_mainmenu_page_walker();

    $menu=wp_nav_menu($menuParams);

    if(!$menu || is_wp_error($menu))
      return "";
    return $menu;
  }


  public static function get_get_config( $key  ) {

    global $gum_helper;

    if(!isset($gum_helper) || !isset( $gum_helper[$key] )){
      $gum_helper[$key] = array();
    }

    return $gum_helper[$key];

  } 


  public static function add_to_config( $key,  $value ) {

    global $gum_helper;

    if(!isset($gum_helper) || !isset( $gum_helper[$key] )){
      $gum_helper[$key] = array();
    }

    $gum_helper[$key] = $value;

  }

  public static function blog_pagination( $args ) {

    $defaults=array(
      'max_num_pages' => false,
      'before' => "",
      'after' => "",
      'base_url' => "",
      'navigation_type' => '',
      'wrapper'=> "<div class=\"grid-posts %s\" dir=\"ltr\">%s</div>"
      );

    if(!isset($args) || !is_array( $args )){
      $args = array();
    }

    $args=wp_parse_args($args,$defaults);


    if ($args['max_num_pages'] === false) {
      global $wp_query;
      $args['max_num_pages'] = $wp_query -> max_num_pages;
    }

    $links = array();
    $type = $args['navigation_type'];

    $base = str_replace( 999999999, '%#%', esc_url( Gum_Elementor_Helper::get_pagenum_link_from_page( 999999999,  $args['base_url'] ) ) );

    $current = max( 1, get_query_var('paged'));
    $next = $current + 1;
    $previous = $current - 1;
    $is_rtl = is_rtl();

    if($type == 'arrow'){

      if($previous > 0 || is_admin() ){
        $previous_link = str_replace('%#%', $previous , $base);
        $links[] = '<a class="newest-post nav-button'.($is_rtl ? " rtl":"").'" href="'.esc_url($previous_link).'"><span>'.esc_html__('Newest Post','gum-elementor-addon').'</span></a>';
      }

      if( $next <= $args['max_num_pages'] || is_admin() ){
        $next_link = str_replace('%#%', $next , $base);
        $links[] = '<a class="older-post nav-button'.($is_rtl ? " rtl":"").'" href="'.esc_url($next_link).'"><span>'.esc_html__('Older Post','gum-elementor-addon').'</span></a>';
      }


    }
    else{

      $links = paginate_links( array(
        'base' => $base,
        'format' => '?paged=%#%',
        'current' => $current,
        'total' => $args['max_num_pages'],
        'prev_next'   => true,
        'prev_text'   => $is_rtl ? '<span>'.esc_html__('Previous','gum-elementor-addon').'</span>' : '<span>'.esc_html__('Previous','gum-elementor-addon').'</span>',
        'next_text'   => $is_rtl ? '<span>'.esc_html__('Next','gum-elementor-addon').'</span>' : '<span>'.esc_html__('Next','gum-elementor-addon').'</span>',
        'end_size'    => 0,
        'mid_size'    => 1,
        'before_page_number' => '<span>',
        'after_page_number' => '</span>',
        'type'      => 'array',
      ) );


    }

    $output = '';

    if ($links && count($links)): 
        $pagination_links= $args["before"].join($args["after"].$args["before"],is_rtl()? array_reverse($links) : $links).$args["after"];

         $output =  !empty($args['wrapper']) ? sprintf( $args['wrapper'] , $type , $pagination_links) :  sprintf( "<div class=\"%s\">%s</div>" , $type , $pagination_links ); 
    endif;

    return $output;
  }

  public static function get_pagenum_link_from_page($pagenum = 1, $base_url=null, $escape = true ) {
    
    global $wp_rewrite;

    $pagenum = (int) $pagenum;

    if($base_url){
      $request = remove_query_arg( 'paged' , $base_url );
      $request = preg_replace('|^'. home_url() . '|i', '', $request);
    }
    else{
      $request = remove_query_arg( 'paged');
    }

    $home_root = parse_url(home_url('/'));
    $home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
    $home_root = preg_quote( $home_root, '|' );

    $request = preg_replace('|^'. $home_root . '|i', '', $request);
    $request = preg_replace('|^/+|', '', $request);

    if ( !$wp_rewrite->using_permalinks() || is_admin() ) {
      $base = trailingslashit( home_url() );

      if ( $pagenum > 1 ) {
        $result = add_query_arg( 'paged', $pagenum, $base . $request );
      } else {
        $result = $base . $request;
      }

    } else {

      $qs_regex = '|\?.*?$|';
      preg_match( $qs_regex, $request, $qs_match );

      if ( !empty( $qs_match[0] ) ) {
        $query_string = $qs_match[0];
        $request = preg_replace( $qs_regex, '', $request );
      } else {
        $query_string = '';
      }


      $request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
      $request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request);
      $request = ltrim($request, '/');


      $base = trailingslashit( home_url('/') );

      if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
        $base .= $wp_rewrite->index . '/';

      if ( $pagenum > 1 ) {
        $request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
      }


      $result = $base . $request . $query_string;
    }

    /**
     * Filters the page number link for the current request.
     *
     * @since 2.5.0
     *
     * @param string $result The page number link.
     */

    $result = apply_filters( 'get_pagenum_link', $result );

    if ( $escape )
      return esc_url( $result );
    else
      return esc_url_raw( $result );
  } 

}

/* make menu dropdown like bootstrap */

if(class_exists('Walker_Nav_Menu')){

  class gum_mainmenu_walker extends Walker_Nav_Menu{

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
        $t = '';
        $n = '';
      } else {
        $t = "\t";
        $n = "\n";
      }
      $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

      $classes = empty( $item->classes ) ? array() : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;


      /**
       * Filters the arguments for a single nav menu item.
       *
       * @since 4.4.0
       *
       * @param stdClass $args  An object of wp_nav_menu() arguments.
       * @param WP_Post  $item  Menu item data object.
       * @param int      $depth Depth of menu item. Used for padding.
       */
      $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

      /**
       * Filters the CSS class(es) applied to a menu item's list item element.
       *
       * @since 3.0.0
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
       * @param WP_Post  $item    The current menu item.
       * @param stdClass $args    An object of wp_nav_menu() arguments.
       * @param int      $depth   Depth of menu item. Used for padding.
       */
      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
      $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

      /**
       * Filters the ID applied to a menu item's list item element.
       *
       * @since 3.0.1
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
       * @param WP_Post  $item    The current menu item.
       * @param stdClass $args    An object of wp_nav_menu() arguments.
       * @param int      $depth   Depth of menu item. Used for padding.
       */
      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
      $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

      $output .= $indent . '<li' . $id . $class_names .'>';

      $atts = array();
      $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
      $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
      $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
      $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

      /**
       * Filters the HTML attributes applied to a menu item's anchor element.
       *
       * @since 3.6.0
       * @since 4.1.0 The `$depth` parameter was added.
       *
       * @param array $atts {
       *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
       *
       *     @type string $title  Title attribute.
       *     @type string $target Target attribute.
       *     @type string $rel    The rel attribute.
       *     @type string $href   The href attribute.
       * }
       * @param WP_Post  $item  The current menu item.
       * @param stdClass $args  An object of wp_nav_menu() arguments.
       * @param int      $depth Depth of menu item. Used for padding.
       */
      $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

      $attributes = '';
      foreach ( $atts as $attr => $value ) {
        if ( ! empty( $value ) ) {
          $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
          $attributes .= ' ' . $attr . '="' . $value . '"';
        }
      }


      $title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

      $item_output = $args->before;
      $item_output .= '<a'. $attributes .'>';
      $item_output .= $args->link_before . $title . $args->link_after;

      /**
      * Add caret at dropdown menu
      * @package Petro
      * @since   1.0.0
      */

      if(in_array('menu-item-has-children', $classes)){
            $item_output .= '<span class="arrow"><span></span></span>';
      }

      $item_output .= '</a>';
      $item_output .= $args->after;

      /**
       * Filters a menu item's starting output.
       *
       * The menu item's starting output only includes `$args->before`, the opening `<a>`,
       * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
       * no filter for modifying the opening and closing `<li>` for a menu item.
       *
       * @since 3.0.0
       *
       * @param string   $item_output The menu item's starting HTML output.
       * @param WP_Post  $item        Menu item data object.
       * @param int      $depth       Depth of menu item. Used for padding.
       * @param stdClass $args        An object of wp_nav_menu() arguments.
       */
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .="\t$indent<div class=\"sub-menu-container\"><ul class=\"sub-menu\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent</ul></div>\n";
    }

  }
}

if( class_exists( 'Walker_Page' )){

  class gum_mainmenu_page_walker extends Walker_Page{

    /**
     * Outputs the beginning of the current element in the tree.
     *
     * @see Walker::start_el()
     * @since 2.1.0
     * @access public
     *
     * @param string  $output       Used to append additional content. Passed by reference.
     * @param WP_Post $page         Page data object.
     * @param int     $depth        Optional. Depth of page. Used for padding. Default 0.
     * @param array   $args         Optional. Array of arguments. Default empty array.
     * @param int     $current_page Optional. Page ID. Default 0.
     */
    public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
      if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
        $t = "\t";
        $n = "\n";
      } else {
        $t = '';
        $n = '';
      }
      if ( $depth ) {
        $indent = str_repeat( $t, $depth );
      } else {
        $indent = '';
      }

      $css_class = array( 'page_item', 'page-item-' . $page->ID );

      /**
      * Add caret at dropdown menu
      * @package Petro
      * @since   1.0.0
      */
      $caret  = '';

      if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
        $css_class[] = 'page_item_has_children';
        $caret = '<span class="arrow"></span>';
      }

      if ( ! empty( $current_page ) ) {
        $_current_page = get_post( $current_page );
        if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
          $css_class[] = 'current_page_ancestor';
        }
        if ( $page->ID == $current_page ) {
          $css_class[] = 'current_page_item';
        } elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
          $css_class[] = 'current_page_parent';
        }
      } elseif ( $page->ID == get_option('page_for_posts') ) {
        $css_class[] = 'current_page_parent';
      }

      /**
       * Filters the list of CSS classes to include with each page item in the list.
       *
       * @since 2.8.0
       *
       * @see wp_list_pages()
       *
       * @param array   $css_class    An array of CSS classes to be applied
       *                              to each list item.
       * @param WP_Post $page         Page data object.
       * @param int     $depth        Depth of page, used for padding.
       * @param array   $args         An array of arguments.
       * @param int     $current_page ID of the current page.
       */
      $css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

      if ( '' === $page->post_title ) {
        /* translators: %d: ID of a post */
        $page->post_title = sprintf( __( '#%d (no title)','petro' ), $page->ID );
      }

      $args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
      $args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

      $title = apply_filters( 'page_menu_item_title', $page->post_title, $page, $args, $depth );

      $output .= $indent . sprintf(
        '<li class="%s"><a href="%s">%s%s%s%s</a>',
        $css_classes,
        get_permalink( $page->ID ),
        $args['link_before'],
        $title,
        $caret,
        $args['link_after']
      );

      if ( ! empty( $args['show_date'] ) ) {
        if ( 'modified' == $args['show_date'] ) {
          $time = $page->post_modified;
        } else {
          $time = $page->post_date;
        }

        $date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
        $output .= " " . mysql2date( $date_format, $time );
      }
    }

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "\n$indent<div class=\"sub-menu-container\"><ul class='sub-menu'>\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent</ul></div>\n";
    }

  }
}