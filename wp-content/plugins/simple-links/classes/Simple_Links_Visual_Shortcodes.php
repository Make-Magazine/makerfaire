<?php
/**
 * Simple_Links_Visual_Shortcodes
 *
 * All the display the the rendered shortcodes within the
 * content editor.
 *
 * Uses the embed to convert [embed][simple-links][/embed] to <ul></ul>
 *
 * @see    I went this route because it was difficult to get the oembed api to
 *        work with non url based patterns. I explored the second option of
 *        tapping into wp.shortcodes and added an additional view, however this
 *        js structure is likely to change in the future so it is not very forward
 *        compatible. Also, simple-links will not work because of the hyphen and it
 *        would become a bear to convert everyone over to simplelinks.
 *
 * @todo Customize the "edit" Modal that pops up when clicking edit on the shortcode.
 *       Currently using the default WP embed which works but verbiage does not make sense. Even better, if I can make this open into the shortcode generation form, that would be really sweet
 *
 * @author Mat Lipe
 * @since  11/25/2015
 *
 */
class Simple_Links_Visual_Shortcodes {

	const SETTING = 'simple-links-visual-shortcodes-enabled';
	const ACTION = 'simple_links_visual_shortcode';


	private function __construct(){
		if( $this->is_visual_shortcodes_enabled() ){

			$this->add_simple_links_to_embed();
			$this->hooks();

		}
	}


	/**
	 * Actions and Filters
	 *
	 * @return void
	 */
	private function hooks(){
		add_filter( 'the_editor_content', array( $this, 'wrap_shortcodes_in_embed' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'strip_embed_wraps_upon_save' ) );
	}


	/**
	 * When we save the post we don't want the extra embeds to be lingering outside
	 * of the [simple-links] shortcode.
	 * We strip them out here as the post saves so anywhere else is none the wiser
	 * that the embeds ever existed
	 *
	 * @param array $post_data - wp_slashed array of post data
	 *
	 * @return array
	 */
	public function strip_embed_wraps_upon_save( $post_data ){
		$content = wp_unslash( $post_data[ 'post_content' ]);
		$content = preg_replace( "/\[embed\](\[simple-links([^\]]*)\])\[\/embed\]/", "$1", $content );
		$post_data[ 'post_content' ] = wp_slash( $content );

		return $post_data;
	}


	/**
	 * When rendering the mce editors we wrap the [simple-links]
	 * shortcodes in [embed][/embed] so they will render as embedded items
	 *
	 * @see $this->strip_embed_wraps_upon_save() This is where we later strip
	 *           these out so they are never saved.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function wrap_shortcodes_in_embed( $content ){
		$content = preg_replace( "/\[simple-links([^\]]*)\]/", "[embed]$0[/embed]", $content );
		return $content;
	}


	/**
	 * Add the [simple-links] shortcode to list of embed providers.
	 * If [simple-links] is wrapped with [embed][/embed] WP will
	 * parse and use our method to render the result
	 *
	 *
	 * @param $providers
	 *
	 * @return mixed
	 */
	public function add_simple_links_to_embed(){
		wp_embed_register_handler(
			'simple-links',
			"/\[simple-links[[:space:]]?(.*?)\]/",
			array(
				$this,
				'generate_oembed_results',
			)
		);
	}


	/**
	 * Render the [embed][simple-links][/embed] shortcode
	 * into the complete generated list
	 *
	 * @return string
	 */
	public function generate_oembed_results( $matches ){
		//because embed strips all shortcodes
		simple_links()->register_shortcode();

		$content = do_shortcode( $matches[ 0 ] );

		return $content;

	}


	/**
	 * Do have have the option set in settings to
	 * turn this feature on
	 *
	 * @return bool
	 */
	public function is_visual_shortcodes_enabled(){
		static $enabled = null;
		if( $enabled !== null ){
			return $enabled;
		}
		$enabled = get_option( self::SETTING );
		return (bool)$enabled;
	}


	//********** SINGLETON FUNCTIONS **********/

	/**
	 * Instance of this class for use as singleton
	 */
	private static $instance;


	/**
	 * Create the instance of the class
	 *
	 * @static
	 * @return void
	 */
	public static function init(){
		self::$instance = self::get_instance();
	}


	/**
	 * Get (and instantiate, if necessary) the instance of the
	 * class
	 *
	 * @static
	 * @return self
	 */
	public static function get_instance(){
		if( !is_a( self::$instance, __CLASS__ ) ){
			self::$instance = new self();
		}

		return self::$instance;
	}
}
