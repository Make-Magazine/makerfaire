<?php

class ESSBInstagramFeedWidget extends WP_Widget {

	/**
	 * Sets up a new Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array('classname' => 'widget_essb_instagramfeed', 'description' => esc_html__( "Show Instagram feed for user or tag.") );
		parent::__construct('easy-instagramfeed-widget', esc_html__('Easy Social Share Buttons: Instagram Feed'), $widget_ops);
		$this->alt_option_name = 'widget_essb_instagramfeed';
	}

	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance ) {		
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		
		/**
		 * @since 7.7 Widget title compatibility with WPML
		 */
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance );
		
		$instance['widget'] = 'true';
		
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if (!empty($title)) {
			echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
		}
		
		if (function_exists('essb_instagram_feed')) {
			echo essb_instagram_feed()->generate_shortcode($instance);
		}
		
		if (!empty($title)) {
			echo $args['after_widget'];
		}
		
	}

	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		if (function_exists('essb_instagram_feed')) {
		  $default_options = essb_instagram_feed()->get_settings();
		}
		else {
		    $default_options = array();
		}
		
		foreach ($default_options as $key => $setup) {
			$instance[$key] = sanitize_text_field( $new_instance[$key] );
		}
		
		$instance['title'] = sanitize_text_field($new_instance['title']);
		
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Posts widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		
	    if (function_exists('essb_instagram_feed')) {
	        $default_options = essb_instagram_feed()->get_settings();
	    }
	    else {
	        $default_options = array();
	    }
	    
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<?php 
		
		$values = array();
		foreach ($default_options as $key => $setup) {
			$value = isset($instance[$key]) ? esc_attr($instance[$key]) : '';
			$type = $setup['type'];
			$title = isset($setup['title']) ? $setup['title'] : '';
			$description = isset($setup['description']) ? $setup['description'] : '';
			$options = isset($setup['options']) ? $setup['options'] : array();
			
			if ($type == 'text') {
				echo '<p>';
				echo '<label for="'.$this->get_field_id($key).'">'.$title.'</label>';
				echo '<input class="widefat" id="'.$this->get_field_id( $key ).'" name="'.$this->get_field_name( $key ).'" type="text" value="'.esc_attr($value).'" />';
				
				if ($description != '') {
					echo '<em>'.$description.'</em>';
				}
				
				echo '</p>';
			}
			
			if ($type == 'select') {
				echo '<p>';
				echo '<label for="'.$this->get_field_id($key).'">'.$title.'</label>';
				echo '<select class="widefat" id="'.$this->get_field_id( $key ).'" name="'.$this->get_field_name( $key ).'" type="text" value="'.esc_attr($value).'">';
				foreach ($options as $opt_key => $opt_value) {
					echo '<option value="'.$opt_key.'" '.($opt_key == $value ? 'selected': '').'>'.$opt_value.'</option>';
				}
				echo '</select>';
				
				if ($description != '') {
					echo '<em>'.$description.'</em>';
				}
				
				echo '</p>';
			}
		}		
	}
}

  function essb_instagramfeed_init_wp_widget() {
    register_widget( 'ESSBInstagramFeedWidget' );
  }

  add_action( 'widgets_init', 'essb_instagramfeed_init_wp_widget' );

