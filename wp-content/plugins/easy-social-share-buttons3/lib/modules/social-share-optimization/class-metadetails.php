<?php
/**
 * @package EasySocialShareButtons\SocialShareOptimization
 * @author appscreo
 * @since 4.2
 * @version 4.0
 *
 * Generate and store require from social share optimization tags post details: title,
 * description and image
 */


class ESSB_FrontMetaDetails {

	/**
	 * Title
	 * @var string
	 */
	public $title = null;
	
	/**
	 * Description
	 * @var string
	 */
	public $description = null;
	
	/**
	 * Image URL
	 * @var string
	 */
	public $image = null;
	
	/**
	 * URL
	 * @var string
	 */
	public $url = null;

	
	public static $instance;
	
	public function __construct() {

		// code runs only when we are not inside WordPress administration
		if (!is_admin()) {
			// stop Jetpack tags
			if (class_exists ( 'JetPack' )) {
				add_filter ( 'jetpack_enable_opengraph', '__return_false' );
				add_filter ( 'jetpack_enable_open_graph', '__return_false' );
			}
		}
	}	
	
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	/**
	 * Detect running WordPress SEO plugin to get settings that are set for SEO on post
	 * 
	 * @return boolean
	 */
	public function wpseo_detected () {
		return defined('WPSEO_VERSION') ? true: false;
	}
	
	/**
	 * Generate title
	 * 
	 * @return string
	 */
	public function single_title($post_id) {
	    $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
	    $this->title = $post_details->opengraph_value('title');	    
	    return $this->title;
	}
	
	public function title() {
		
		if (!isset($this->title)) {
		    $type = ESSB_Site_Share_Information::type();
		    
		    if ($type == 'single') {
		        $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
		        $this->title = $post_details->opengraph_value('title');
		    }
		    else {
		        $this->title = ESSB_Site_Share_Information::get_title_by_type($type);
		    }		    
		}
		
		return esc_html( wp_strip_all_tags( stripslashes( $this->title ), true ) );
	}
	
	/**
	 * Generate URL
	 * 
	 * @return string
	 */
	public function url() {
		if (!isset($this->url)) {
		    $type = ESSB_Site_Share_Information::type();
		    
		    if ($type == 'single') {
		        $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
		        $this->url = $post_details->opengraph_value('url');
		    }
		    else {
		        $this->url = ESSB_Site_Share_Information::get_url_by_type($type);
		    }
		}
		
		return $this->url;
	}
	
	/**
	 * Generate description
	 * 
	 * @return string
	 */
	public function description() {
		if (!isset($this->description)) {
		    $type = ESSB_Site_Share_Information::type();
		    
		    if ($type == 'single') {
		        $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
		        $this->description = $post_details->opengraph_value('description');	
		    }
		    else {
		        $this->description = ESSB_Site_Share_Information::get_description_by_type($type);
		    }
		}

		return esc_html( wp_strip_all_tags( stripslashes( $this->description ), true ) );
	}
	
	public function single_description($post_id) {
	    $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
	    $this->description = $post_details->opengraph_value('description');
	}
	
	/**
	 * Generate Image
	 * 
	 * @return string
	 */
	public function image() {
		if (!isset($this->image)) {
		    $type = ESSB_Site_Share_Information::type();
		    
		    if ($type == 'single') {
		        $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
		        $this->image = $post_details->opengraph_value('image');
		    }
		    else {
		        $this->image = ESSB_Site_Share_Information::get_image_by_type($type);
		    }
		}
		
		if ($this->image == '') {
			$this->image = essb_option_value('sso_default_image');
		}
		
		return $this->image;
	}
	
	public function single_image($post_id) {
	    $post_details = ESSB_Runtime_Cache::get_post_sharing_data(get_the_ID());
	    $this->image = $post_details->opengraph_value('image');
	    
	    return $this->image;
	}
	
	/**
	 * Generate additional images that customer can choose on post
	 * @return array
	 */
	public function additional_images() {
		$image_list = array();
		
		if (is_single () || is_page ()) {
			$fb_image1 = get_post_meta ( get_the_ID(), 'essb_post_og_image1', true );
			$fb_image2 = get_post_meta ( get_the_ID(), 'essb_post_og_image2', true );
			$fb_image3 = get_post_meta ( get_the_ID(), 'essb_post_og_image3', true );
			$fb_image4 = get_post_meta ( get_the_ID(), 'essb_post_og_image4', true );
			
			if (!empty($fb_image1) && is_string($fb_image1)) {
				$image_list[] = $fb_image1;
			}

			if (!empty($fb_image2) && is_string($fb_image2)) {
				$image_list[] = $fb_image2;
			}
			
			if (!empty($fb_image3) && is_string($fb_image3)) {
				$image_list[] = $fb_image3;
			}
				
			if (!empty($fb_image4) && is_string($fb_image4)) {
				$image_list[] = $fb_image4;
			}
				
		}
		
		return $image_list;
	}
	
	public function sw_value($key = '') {
		if (essb_option_bool_value('activate_sw_bridge') && function_exists('essb_sw_custom_data')) {
			$sw_setup = essb_sw_custom_data();
				
			return isset($sw_setup[$key]) ? $sw_setup[$key] : '';
		}
		else {
			return '';
		}
	}
	
	public function get_term_custom_data($data = 'title') {
		$term = get_queried_object();
		$r = '';
		$field = 'sso_title';
		
		if ($data == 'description') {
			$field = 'sso_desc';
		}
		else if ($data == 'image') {
			$field = 'sso_image';
		}
		
		if ( ! empty( $term ) ) {
			$r = htmlspecialchars(stripcslashes(get_term_meta($term->term_id, $field, true)));
		}
		
		return $r;
	}
}