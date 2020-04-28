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
				add_filter ( 'jetpack_enable_opengraph', '__return_false', 99 );
				add_filter ( 'jetpack_enable_open_graph', '__return_false', 99 );
			}
			
			// try to stop Yoast SEO from generating double tags
			if (defined('WPSEO_VERSION')) {
				global $wpseo_og;
				if (isset($wpseo_og)) {
					remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );
				}
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
		$this->title = get_post_meta ( $post_id, 'essb_post_og_title', true );
			
		if (empty($this->title) && essb_option_bool_value('activate_sw_bridge')) {
			$this->title = $this->sw_value('og_title');
		}
			
		// import SEO details
		if (empty($this->title) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
			$this->title = get_post_meta( $post_id, '_yoast_wpseo_opengraph-title' , true );
		}
		if (empty($this->title) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
			$this->title = get_post_meta( $post_id, '_yoast_wpseo_title' , true );
		}
			
		// include WPSEO replace vars
		if ($this->wpseo_detected() && strpos($this->title, '%%') !== false && function_exists('wpseo_replace_vars')) {
		
			$this->title = wpseo_replace_vars($this->title, get_post($post_id));
		}
			
		if (empty($this->title)) {
			$this->title = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode(get_the_title ())));;
		}
		
		return $this->title;
	}
	
	public function title() {
		
		if (!isset($this->title)) {
			if (is_front_page()) {
				$this->title = essb_option_value('sso_frontpage_title');
				
				if (empty($this->title)) {
					$this->title = get_bloginfo('name');
				}
			}
			else if (is_single () || is_page ()) {
				 $this->title = get_post_meta ( get_the_ID(), 'essb_post_og_title', true );
				 
				 if (empty($this->title) && essb_option_bool_value('activate_sw_bridge')) {
				 	$this->title = $this->sw_value('og_title');
				 }
				 
				 // import SEO details
				 if (empty($this->title) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
				 	$this->title = get_post_meta( get_the_ID(), '_yoast_wpseo_opengraph-title' , true );
				 }
				 if (empty($this->title) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
				 	$this->title = get_post_meta( get_the_ID(), '_yoast_wpseo_title' , true );
				 }
				 
				 // include WPSEO replace vars
				 if ($this->wpseo_detected() && strpos($this->title, '%%') !== false && function_exists('wpseo_replace_vars')) {
				 	
				 	$this->title = wpseo_replace_vars($this->title, get_post(get_the_ID()));
				 }
				 
				 if (empty($this->title)) {
				 	$this->title = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode(get_the_title ())));;
				 }
			}
			elseif ( is_search() ) {
				$this->title = sprintf( __( 'Search for "%s"', 'essb' ), esc_html( get_search_query() ) );
			}
			elseif ( is_category() || is_tag() || is_tax() ) {
				if ( is_category() ) {
					$this->title = single_cat_title( '', false );
				}
				elseif ( is_tag() ) {
					$this->title = single_tag_title( '', false );
				}
				else {
					$this->title = single_term_title( '', false );
					if ( $this->title === '' ) {
						$term       = $GLOBALS['wp_query']->get_queried_object();
						$this->title = $term->name;
					}
				}
				
				$custom = $this->get_term_custom_data('title');
				if ($custom != '') {
					$this->title = $custom;
				}
			}
			elseif ( is_author() ) {
				$this->title = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			}
			else {
				$this->title = get_bloginfo('name');
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
			if (is_front_page()) {
				$this->url = get_bloginfo('url');
			}
			else if (is_single () || is_page ()) {
				$this->url = get_permalink(get_the_ID());
				
				$custom_og_url = get_post_meta ( get_the_ID(), 'essb_post_og_url', true );
				if ($custom_og_url != '') {
					$this->url = $custom_og_url;
				}
			}
			else if ( is_search() ) {
				$search_query = get_search_query();

				// Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
				if ( ! empty( $search_query ) && ! preg_match( '|^page/\d+$|', $search_query ) ) {
					$this->url = get_search_link();
				}
			}
			elseif ( is_tax() || is_tag() || is_category() ) {
			
				$term = get_queried_object();
			
				if ( ! empty( $term ) ) {
			
					$term_link = get_term_link( $term, $term->taxonomy );
			
					if ( ! is_wp_error( $term_link ) ) {
						$this->url = $term_link;
					}
					else {
						$this->url = get_bloginfo('url');
					}
				}
			}
			elseif ( is_author() ) {
				$this->url = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
			}
			elseif (is_home()) {
				$this->url = get_permalink( get_option( 'page_for_posts' ) );
			}
			else {
				$this->url = get_bloginfo('url');
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
			if (is_front_page()) {
				$this->description = essb_option_value('sso_frontpage_description');
				
				if (empty($this->description)) {
					$this->description = get_bloginfo('description');
				}
			}
			else if (is_single () || is_page ()) {
				 $this->description = get_post_meta ( get_the_ID(), 'essb_post_og_desc', true );
				 
				 if (empty($this->description) && essb_option_bool_value('activate_sw_bridge')) {
				 	$this->description = $this->sw_value('og_description');
				 }
				 
				 // import SEO details
				 if (empty($this->description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
				 	$this->description = get_post_meta( get_the_ID(), '_yoast_wpseo_opengraph-description' , true );
				 }
				 if (empty($this->description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
				 	$this->description = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc' , true );
				 }
				 
				 if (empty($this->description)) {
				 	easy_share_deactivate();
				 	$this->description = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode(essb_core_get_post_excerpt(get_the_ID()))));
				 	easy_share_reactivate();
				 }
			}
			elseif ( is_category() || is_tag() || is_tax() ) {
				$this->description = strip_tags(term_description());
				
				$custom = $this->get_term_custom_data('description');
				if ($custom != '') {
					$this->description = $custom;
				}
			}
			elseif ( is_author() ) {
				$this->description = get_the_author_meta( 'description', get_query_var( 'author' ) );
			}
			else {
				$this->description = get_bloginfo('description');
			}
		}

		return esc_html( wp_strip_all_tags( stripslashes( $this->description ), true ) );
	}
	
	public function single_description($post_id) {
		$this->description = get_post_meta ( $post_id, 'essb_post_og_desc', true );
			
		if (empty($this->description) && essb_option_bool_value('activate_sw_bridge')) {
			$this->description = $this->sw_value('og_description');
		}
			
		// import SEO details
		if (empty($this->description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
			$this->description = get_post_meta( $post_id, '_yoast_wpseo_opengraph-description' , true );
		}
		if (empty($this->description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
			$this->description = get_post_meta( $post_id, '_yoast_wpseo_metadesc' , true );
		}
			
		if (empty($this->description)) {
			easy_share_deactivate();
			$this->description = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode(essb_core_get_post_excerpt($post_id))));
			easy_share_reactivate();
		}
		
		return esc_html( wp_strip_all_tags( stripslashes( $this->description ), true ) );
	}
	
	/**
	 * Generate Image
	 * 
	 * @return string
	 */
	public function image() {
		if (!isset($this->image)) {
			if (is_front_page()) {
				$this->image = essb_option_value('sso_frontpage_image');
			}
			else if (is_single () || is_page ()) {
				$this->image = get_post_meta ( get_the_ID(), 'essb_post_og_image', true );
					
				if (empty($this->image) && essb_option_bool_value('activate_sw_bridge')) {
					$this->image = $this->sw_value('og_image');
				}
				
				// import SEO details
				if (empty($this->image) && $this->wpseo_detected()) {
					$this->image = get_post_meta( get_the_ID(), '_yoast_wpseo_opengraph-image' , true );
				}

				if (empty($this->image)) {
					$this->image = essb_core_get_post_featured_image(get_the_ID());
				}
			}
			else {
				$this->image = essb_option_value('sso_frontpage_image');
				
				if (is_category() || is_tag() || is_tax()) {
					$custom = $this->get_term_custom_data('image');
					if ($custom != '') {
						$this->image = $custom;
					}
				}
			}
		}
		
		if ($this->image == '') {
			$this->image = essb_option_value('sso_default_image');
		}
		
		return $this->image;
	}
	
	public function single_image($post_id) {
		$this->image = get_post_meta ( $post_id, 'essb_post_og_image', true );
			
		if (empty($this->image) && essb_option_bool_value('activate_sw_bridge')) {
			$this->image = $this->sw_value('og_image');
		}
		
		// import SEO details
		if (empty($this->image) && $this->wpseo_detected()) {
			$this->image = get_post_meta( $post_id, '_yoast_wpseo_opengraph-image' , true );
		}
		
		if (empty($this->image)) {
			$this->image = essb_core_get_post_featured_image($post_id);
		}
		
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