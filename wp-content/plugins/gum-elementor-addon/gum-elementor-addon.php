<?php
defined('ABSPATH') or die();
/* 
 * Plugin Name: Gum Elementor Addon
 * Plugin URI: http://themegum.com/
 * Description: Addon widget for Elementor. Slideshow, pricing table, icon list, recent post, blog term, post term, post share, post meta, post related, post adjacent, blog grid, post slider,navigation menu, image box, popover and to top button
 * Version: 1.3.6
 * Author: TemeGUM
 * Author URI: http://themegum.com
 * Domain Path: /languages/
 * Text Domain: gum-elementor-addon
 * Requires at least: 3.7
 * Tested up to: 6.5.5
 * Elementor tested up to: 3.22.3
 */

require_once plugin_dir_path(__FILE__). '/helper.php';

final class Gum_Elementor_Addon{

  private static $_instance = null;

  public static function instance() {

    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;

  }

	public function __construct() {

        define('GUM_ELEMENTOR_URL', trailingslashit(plugin_dir_url( __FILE__ )));
        define('GUM_ELEMENTOR_DIR',plugin_dir_path(__FILE__));

        load_plugin_textdomain('gum-elementor-addon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

        if(!function_exists('is_plugin_active')){
      		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
      	}

      	if(is_plugin_active('elementor/elementor.php')){
                   
      			$this->init();
      	}
      	else{
      			add_action( 'admin_notices', array( $this, 'deactive_notice'), 10 );
	      		$this->deactive();
      	}

	}

	public function init(){

      require_once( GUM_ELEMENTOR_DIR."widgets/section.php" );
      require_once( GUM_ELEMENTOR_DIR."widgets/icon_list.php" );
      require_once( GUM_ELEMENTOR_DIR."widgets/accordion.php" );
      require_once( GUM_ELEMENTOR_DIR."widgets/counter.php" );
      require_once( GUM_ELEMENTOR_DIR."widgets/progress.php" );
      require_once( GUM_ELEMENTOR_DIR."widgets/button.php" );

      add_action( 'elementor/init',array( $this, '_register_elementor_category' ) );
      add_action( 'elementor/widgets/widgets_registered', array($this, '_elementor_widget_register'), 9999 );

      add_action('wp_head',array($this,'add_count_post_view'));
	}

  public function _register_elementor_category($manager){


      \Elementor\Plugin::$instance->elements_manager->add_category(
        'temegum',
        [
          'title' => 'TemeGUM',
          'icon' => 'feicon-font',
        ],
        1
      );

      \Elementor\Plugin::$instance->elements_manager->add_category(
        'temegum_blog',
        [
          'title' => 'TemeGUM Blog',
          'icon' => 'feicon-font',
        ],
        1
      );

  }

  public function _elementor_widget_register($widgets_manager){


      if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){

        require_once( GUM_ELEMENTOR_DIR."widgets/slideshow.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/popover_btn.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/heading.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_lists.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_post_meta.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_post_adjacent.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_post_related.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_post_share.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_term.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/site_navigation.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_grid.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/post_slider.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/blog_image.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/totop_btn.php" );
        require_once( GUM_ELEMENTOR_DIR."widgets/carousel_ibox.php" );

        if(!class_exists('Month_Anual_Pricetable_Widget')){
          require_once( GUM_ELEMENTOR_DIR."widgets/pricetable.php" );          
        }
       
         if(!class_exists('Month_Anual_Pricetable_TogglePeriod_Elementor_Widget')){
          require_once( GUM_ELEMENTOR_DIR."widgets/toggle_period.php" );          
        }
      }
  }

	function deactive_notice(){

		echo "<div class='error'>" .  esc_html__( 'Gum Elementor Addon deactivated. The plugin need Elementor plugin, please install the plugin first.' ,'gum-elementor-addon'). "</div>";

	}

	function deactive(){
		deactivate_plugins( array('gum-elementor-addon/gum-elementor-addon.php'), true, is_network_admin() );
	}

  public static function get_image_size( $image_id,$img_size="thumbnail"){

      global $_wp_additional_image_sizes;

      if(''==$img_size)
          $img_size="thumbnail";

      if(''==$image_id)
          return false;

      if(in_array($img_size, array('thumbnail','thumb','small', 'medium', 'large','full'))){

          if ( $img_size == 'thumb' ||  $img_size == 'small' || $img_size == 'thumbnail' ) {

              $image=wp_get_attachment_image_src($image_id,'thumbnail');
          }
          elseif ( $img_size == 'medium' ) {
              $image=wp_get_attachment_image_src($image_id,'medium');

          }
          elseif ( $img_size == 'large' ) {
              $image=wp_get_attachment_image_src($image_id,'large');
          }else{

              $image=wp_get_attachment_image_src($image_id,'full');

          }

      }
      elseif(!empty($_wp_additional_image_sizes[$img_size]) && is_array($_wp_additional_image_sizes[$img_size])){

          $width=$_wp_additional_image_sizes[$img_size]['width'];
          $height=$_wp_additional_image_sizes[$img_size]['height'];

          $img_url = wp_get_attachment_image_src($image_id,'full',false); 
          $image= self::aq_resize( $img_url[0],$width, $height, true,false ) ;


      }
      else{

          preg_match_all('/\d+/', $img_size, $thumb_matches);

          if(isset($thumb_matches[0])) {
              $thumb_size = array();
              if(count($thumb_matches[0]) > 1) {
                  $thumb_size[] = $thumb_matches[0][0]; // width
                  $thumb_size[] = $thumb_matches[0][1]; // height
              } elseif(count($thumb_matches[0]) > 0 && count($thumb_matches[0]) < 2) {
                  $thumb_size[] = $thumb_matches[0][0]; // width
                  $thumb_size[] = $thumb_matches[0][0]; // height
              } else {
                  $thumb_size = false;
              }
          }

          if($thumb_size){

            $img_url = wp_get_attachment_image_src($image_id,'full',false); 
            $image= self::aq_resize( $img_url[0],$thumb_size[0], $thumb_size[1], true,false ) ;
          }
          else{
            return false;
          }
      }

      return $image;
  }

  public static function aq_resize( $url, $width, $height = null, $crop = null, $single = true ) {

      if(!$url OR !($width || $height)) return false;

      //define upload path & dir
      $upload_info = wp_upload_dir();
      $upload_dir = $upload_info['basedir'];
      $upload_url = $upload_info['baseurl'];
      
      //check if $img_url is local
      /* Gray this out because WPML doesn't like it.
      if(strpos( $url, home_url() ) === false) return false;
      */
      
      //define path of image
      $rel_path = str_replace( str_replace( array( 'http://', 'https://' ),"",$upload_url), '', str_replace( array( 'http://', 'https://' ),"",$url));
      $img_path = $upload_dir . $rel_path;
      
      //check if img path exists, and is an image indeed
      if( !file_exists($img_path) OR !getimagesize($img_path) ) return false;
      
      //get image info
      $info = pathinfo($img_path);
      $ext = $info['extension'];
      list($orig_w,$orig_h) = getimagesize($img_path);
      
      $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
      if(!$dims){
        return $single?$url:array('0'=>$url,'1'=>$orig_w,'2'=>$orig_h);
      }

      $dst_w = $dims[4];
      $dst_h = $dims[5];

      //use this to check if cropped image already exists, so we can return that instead
      $suffix = "{$dst_w}x{$dst_h}";
      $dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
      $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

      //if orig size is smaller
      if($width >= $orig_w) {

        if(!$dst_h) :
          //can't resize, so return original url
          $img_url = $url;
          $dst_w = $orig_w;
          $dst_h = $orig_h;
          
        else :
          //else check if cache exists
          if(file_exists($destfilename) && getimagesize($destfilename)) {
            $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
          } 
          else {

            $imageEditor=wp_get_image_editor( $img_path );

            if(!is_wp_error($imageEditor)){

                $imageEditor->resize($width, $height, $crop );
                $imageEditor->save($destfilename);

                $resized_rel_path = str_replace( $upload_dir, '', $destfilename);
                $img_url = $upload_url . $resized_rel_path;


            }
            else{
                $img_url = $url;
                $dst_w = $orig_w;
                $dst_h = $orig_h;
            }

          }
          
        endif;
        
      }
      //else check if cache exists
      elseif(file_exists($destfilename) && getimagesize($destfilename)) {
        $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
      } 
      else {

        $imageEditor=wp_get_image_editor( $img_path );

        if(!is_wp_error($imageEditor)){
            $imageEditor->resize($width, $height, $crop );
            $imageEditor->save($destfilename);

            $resized_rel_path = str_replace( $upload_dir, '', $destfilename);
            $img_url = $upload_url . $resized_rel_path;
        }
        else{
            $img_url = $url;
            $dst_w = $orig_w;
            $dst_h = $orig_h;
        }


      }
      
      if(!$single) {
        $image = array (
          '0' => $img_url,
          '1' => $dst_w,
          '2' => $dst_h
        );
        
      } else {
        $image = $img_url;
      }
      
      return $image;
    }

    public static function add_count_post_view(){

      if(is_single()){

        $post_id = get_the_ID();

        $count = get_post_meta($post_id, '_post_views_count', true);

        if($count==''){
            $count = 0;
        }else{
            $count++;
        }

        return update_post_meta($post_id, '_post_views_count', $count);
      }

    }

}

Gum_Elementor_Addon::instance();
