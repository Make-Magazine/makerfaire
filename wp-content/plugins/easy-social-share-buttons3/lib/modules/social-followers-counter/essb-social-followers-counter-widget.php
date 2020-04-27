<?php
/**
 * Register the plugin social followers counter widgets
 */
function essb_followers_counter_register_plugin_widgets() {
	register_widget( 'ESSBSocialFollowersCounterWidget' );
	register_widget( 'ESSBSocialFollowersCounterWidgetLayout' );
}

add_action( 'widgets_init', 'essb_followers_counter_register_plugin_widgets' );

/**
 * Social Followers Widget
 * 
 * @author appscreo
 * @package EasySocialShareButtons3
 * @since 3.4
 *
 */
class ESSBSocialFollowersCounterWidget extends WP_Widget {

	public function __construct() {

		$options = array( 'description' => esc_html__( 'Social Followers Counter' , 'essb' ) );
		parent::__construct( false , esc_html__( 'Easy Social Share Buttons: Followers Counter' , 'essb' ) , $options );
	}
	
	public function form( $instance ) {
	
		$defaults = ESSBSocialFollowersCounterHelper::default_instance_settings();	
		$instance = wp_parse_args( ( array ) $instance , $defaults );
	
		$widget_settings_fields = ESSBSocialFollowersCounterHelper::default_options_structure(true, $instance);
		
		foreach ($widget_settings_fields as $field => $options) {
			$field_type = isset($options['type']) ? $options['type'] : 'textbox';
			$field_title = isset($options['title']) ? $options['title'] : '';
			$field_description = isset($options['description']) ? $options['description'] : '';
			$field_values = isset($options['values']) ? $options['values'] : array();
			$field_default_value = isset($options['default_value']) ? $options['default_value'] : '';
			
			if ($field_type == "textbox") {
				$this->generate_textbox_field($field, $field_title, $field_description, $field_default_value);
			}
			if ($field_type == "checkbox") {
				$this->generate_checkbox_field($field, $field_title, $field_description, $field_default_value);
			}
			if ($field_type == "separator") {
				$this->generate_separator($field_title);
			}
			if ($field_type == "select") {
				$this->generate_select_field($field, $field_title, $field_description, $field_default_value, $field_values);
			}
		}
	}
	
	public function update( $new_instance , $old_instance ) {
		$instance = $old_instance;
		
		$widget_settings_fields = ESSBSocialFollowersCounterHelper::default_options_structure();
		
		foreach ($widget_settings_fields as $field => $options) {
			$instance[$field] = $new_instance[$field];
		}
		
		return $instance;
	}
	
	public function widget( $args , $instance ) {
		
		$before_widget = $args['before_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$after_widget  = $args['after_widget'];
				
		$title = isset($instance['title']) ? $instance['title'] : '';
		$hide_title = isset($instance['hide_title']) ? $instance['hide_title'] : 0;
		
		if (intval($hide_title) == 1) { $title = ""; }
		
		if (!empty($title)) {
			echo $before_widget . $before_title . $title . $after_title;
		}

		// draw follower buttons with title set to off - this will be handle by the widget setup
		ESSBSocialFollowersCounterDraw::draw_followers($instance, false);
		
		if (!empty($title)) {
			echo $after_widget;
		}
	}
	
	/*
	 * Widget Settings Draw Functions (Private Access)
	 */

	private function generate_select_field($field, $title, $description, $value, $list_of_values) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>';
		$output .= '<select name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat">';
		
		foreach ($list_of_values as $key => $text) {
			$output .= '<option value="'.esc_attr($key).'" '.($key == $value ? 'selected="selected"' : '').'>'.esc_attr($text).'</option>';
		}
		
		$output .= '</select>';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}
	
	private function generate_separator($title) {
		echo '<h5 class="essb-widget-title-separator">'.$title.'</h5>';
	}
	
	private function generate_textbox_field($field, $title, $description, $value) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>';
		$output .= '<input type="text" name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat" value="'.esc_attr($value).'" />';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}

	private function generate_checkbox_field($field, $title, $description, $value) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>&nbsp;';
		$output .= '<input type="checkbox" name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat" value="1" '.($value == 1 ? ' checked="checked"' : '').' />';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}
}


/**
 * Social Followers Widget
 *
 * @author appscreo
 * @package EasySocialShareButtons3
 * @since 3.4
 *
 */
class ESSBSocialFollowersCounterWidgetLayout extends WP_Widget {

	public function __construct() {

		$options = array( 'description' => esc_html__( 'Display Custom Layout Builder in Social Followers' , 'essb' ) );
		parent::__construct( false , esc_html__( 'Easy Social Share Buttons: Followers Counter (Custom Layout)' , 'essb' ) , $options );
	}

	public function form( $instance ) {

		$defaults = ESSBSocialFollowersCounterHelper::default_instance_settings();
		$instance = wp_parse_args( ( array ) $instance , $defaults );

		$widget_settings_fields = ESSBSocialFollowersCounterHelper::default_options_structure(true, $instance);

		foreach ($widget_settings_fields as $field => $options) {
			$field_type = isset($options['type']) ? $options['type'] : 'textbox';
			$field_title = isset($options['title']) ? $options['title'] : '';
			$field_description = isset($options['description']) ? $options['description'] : '';
			$field_values = isset($options['values']) ? $options['values'] : array();
			$field_default_value = isset($options['default_value']) ? $options['default_value'] : '';
			
			$field_hide_advanced = isset($options['hide_advanced']) ? $options['hide_advanced'] : '';
			if ($field_hide_advanced == 'true') {
				continue;
			}
				
			if ($field_type == "textbox") {
				$this->generate_textbox_field($field, $field_title, $field_description, $field_default_value);
			}
			if ($field_type == "checkbox") {
				$this->generate_checkbox_field($field, $field_title, $field_description, $field_default_value);
			}
			if ($field_type == "separator") {
				$this->generate_separator($field_title);
			}
			if ($field_type == "select") {
				$this->generate_select_field($field, $field_title, $field_description, $field_default_value, $field_values);
			}
		}
	}

	public function update( $new_instance , $old_instance ) {
		$instance = $old_instance;

		$widget_settings_fields = ESSBSocialFollowersCounterHelper::default_options_structure();

		foreach ($widget_settings_fields as $field => $options) {
			$instance[$field] = $new_instance[$field];
		}

		return $instance;
	}

	public function widget( $args , $instance ) {

		$before_widget = $args['before_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$after_widget  = $args['after_widget'];

		$title = isset($instance['title']) ? $instance['title'] : '';
		$hide_title = isset($instance['hide_title']) ? $instance['hide_title'] : 0;

		if (intval($hide_title) == 1) {
			$title = "";
		}

		if (!empty($title)) {
			echo $before_widget . $before_title . $title . $after_title;
		}

		// draw follower buttons with title set to off - this will be handle by the widget setup
		ESSBSocialFollowersCounterDraw::draw_followers($instance, false, true);

		if (!empty($title)) {
			echo $after_widget;
		}
	}

	/*
	 * Widget Settings Draw Functions (Private Access)
	*/

	private function generate_select_field($field, $title, $description, $value, $list_of_values) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>';
		$output .= '<select name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat">';
		
		foreach ($list_of_values as $key => $text) {
			$output .= '<option value="'.esc_attr($key).'" '.($key == $value ? 'selected="selected"' : '').'>'.esc_attr($text).'</option>';
		}
		
		$output .= '</select>';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}
	
	private function generate_separator($title) {
		echo '<h5 class="essb-widget-title-separator">'.$title.'</h5>';
	}
	
	private function generate_textbox_field($field, $title, $description, $value) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>';
		$output .= '<input type="text" name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat" value="'.esc_attr($value).'" />';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}

	private function generate_checkbox_field($field, $title, $description, $value) {
		$output = "";
		
		$output .= '<p>';
		$output .= '<label for="'.esc_attr($this->get_field_id($field)).'">'.$title.'</label>&nbsp;';
		$output .= '<input type="checkbox" name="'.esc_attr($this->get_field_name( $field )).'" id="'.esc_attr($this->get_field_id( $field )).'" class="widefat" value="1" '.($value == 1 ? ' checked="checked"' : '').' />';
		if (!empty($description)) {
			$output .= '<br /><em>'. esc_html__( $description , 'essb' ).'</em>';
		}
		$output .= '</p>';
		
		echo $output;
	}
}
