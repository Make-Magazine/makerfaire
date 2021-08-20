<?php


function essb_profiles_register_widget() {
	register_widget( 'ESSBSocialProfilesWidget' );
}

add_action( 'widgets_init', 'essb_profiles_register_widget' );

if (!defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
	define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
	
	$template_url = ESSBSocialProfilesHelper::get_stylesheet_url();
	essb_resource_builder()->add_static_footer_css($template_url, 'essb-social-followers-counter');
}

class ESSBSocialProfilesWidget extends WP_Widget {
	
	protected $widget_slug = "easy-social-profile-buttons";

	public function __construct() {

		$options = array( 
				'description' => esc_html__( 'Social Profiles' , 'essb' ), 
				'classname' => $this->widget_slug."-class" );

		parent::__construct( false , esc_html__( 'Easy Social Share Buttons: Social Profiles' , 'essb' ) , $options );

	}
	
	public function form( $instance ) {
		
		$defaults = array(
				'title' => esc_html__('Follow us', 'essb') ,
				'template' => 'flat' ,
				'animation' => '' ,
				'nospace' => 0,
				'show_title' => 1,
				'cta' => 0,
				'cta_vertical' => 0,
				'custom_list' => 0
		);
		
		$profile_networks = array();
		$profile_networks = essb_advanced_array_to_simple_array(essb_available_social_profiles());
		
		foreach ($profile_networks as $network) {
			$defaults['profile_'.$network] = '';
			$defaults['profile_text_'.$network] = '';
		}

		$instance = wp_parse_args( ( array ) $instance , $defaults );
		
		$instance_template = isset($instance['template']) ? $instance['template'] : '';
		$instance_animation = isset($instance['animation']) ? $instance['animation'] : '';
		$instance_size = isset($instance['size']) ? $instance['size'] : '';
		$instance_align = isset($instance['align']) ? $instance['align'] : '';
		$instance_columns = isset($instance['columns']) ? $instance['columns'] : '';	
		
		?>
		
<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo esc_html__( 'Title' , 'essb' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" value="<?php echo $instance['title']; ?>" />
</p>
	
<p>
  <label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php echo esc_html__( 'Display widget title' , 'essb' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" id="<?php echo $this->get_field_id( 'show_title' ); ?>" value="1" <?php if ( 1 == $instance['show_title'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php echo esc_html__( 'Template' , 'essb' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'template' ); ?>" id="<?php echo $this->get_field_id( 'template' ); ?>" class="widefat">
<?php 
foreach (ESSBSocialProfilesHelper::available_templates() as $key => $text) {
	$selected = ($key == $instance_template) ? " selected='selected'" : '';
	
	printf('<option value="%1$s" %2$s>%3$s</option>', $key, $selected, $text);
}
?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'align' ); ?>"><?php echo esc_html__( 'Align' , 'essb' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'align' ); ?>" id="<?php echo $this->get_field_id( 'align' ); ?>" class="widefat">
<?php 
foreach (ESSBSocialProfilesHelper::available_alignments() as $key => $text) {
	$selected = ($key == $instance_align) ? " selected='selected'" : '';
	
	printf('<option value="%1$s" %2$s>%3$s</option>', $key, $selected, $text);
}
?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php echo esc_html__( 'Size' , 'essb' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'size' ); ?>" id="<?php echo $this->get_field_id( 'size' ); ?>" class="widefat">
<?php 
foreach (ESSBSocialProfilesHelper::available_sizes() as $key => $text) {
	$selected = ($key == $instance_size) ? " selected='selected'" : '';
	
	printf('<option value="%1$s" %2$s>%3$s</option>', $key, $selected, $text);
}
?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'animation' ); ?>"><?php echo esc_html__( 'Animation' , 'essb' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'animation' ); ?>" id="<?php echo $this->get_field_id( 'animation' ); ?>" class="widefat">
<?php 
foreach (ESSBSocialProfilesHelper::available_animations() as $key => $text) {
	$selected = ($key == $instance_animation) ? " selected='selected'" : '';
	
	printf('<option value="%1$s" %2$s>%3$s</option>', $key, $selected, $text);
}
?>
  </select>
</p>


<p>
  <label for="<?php echo $this->get_field_id( 'nospace' ); ?>"><?php echo esc_html__( 'Remove space between buttons' , 'essb' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'nospace' ); ?>" id="<?php echo $this->get_field_id( 'nospace' ); ?>" value="1" <?php if ( 1 == $instance['nospace'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php echo esc_html__( 'Columns' , 'essb' ); ?>:</label>
  <select name="<?php echo $this->get_field_name( 'columns' ); ?>" id="<?php echo $this->get_field_id( 'columns' ); ?>" class="widefat">
<?php 
$columns = array(
						'' => esc_html__('Don\'t show in columns (automatic width)', 'essb'),
						'1' => esc_html__('1 Column', 'essb'),
						'2' => esc_html__('2 Columns', 'essb'),
						'3' => esc_html__('3 Columns', 'essb'),
						'4' => esc_html__('4 Columns', 'essb'),
						'5' => esc_html__('5 Columns', 'essb'),
						'6' => esc_html__('6 Columns', 'essb'),
				);
foreach ($columns as $key => $text) {
	$selected = ($key == $instance_columns) ? " selected='selected'" : '';
	
	printf('<option value="%1$s" %2$s>%3$s</option>', $key, $selected, $text);
}
?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'cta' ); ?>"><?php echo esc_html__( 'Show texts with the buttons' , 'essb' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'cta' ); ?>" id="<?php echo $this->get_field_id( 'cta' ); ?>" value="1" <?php if ( 1 == $instance['cta'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'cta_vertical' ); ?>"><?php echo esc_html__( 'Vertical text layout' , 'essb' ); ?>:</label>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'cta_vertical' ); ?>" id="<?php echo $this->get_field_id( 'cta_vertical' ); ?>" value="1" <?php if ( 1 == $instance['cta_vertical'] ) { echo ' checked="checked"'; } ?> />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'custom_list' ); ?>"><?php echo esc_html__( 'Custom network list' , 'essb' ); ?>:</label>
  <input type="checkbox" class="essb-profiles-widget-trigger-all" name="<?php echo $this->get_field_name( 'custom_list' ); ?>" id="<?php echo $this->get_field_id( 'custom_list' ); ?>" value="1" <?php if ( 1 == $instance['custom_list'] ) { echo ' checked="checked"'; } ?> />
</p>

<div class="essb-profiles-widget-all-networks-list <?php  if ( 1 != $instance['custom_list'] ) { echo 'essb-global-hidden'; }?>">
		<?php

		foreach (essb_available_social_profiles() as $network => $display) {
			$network_value = $instance['profile_'.$network];
			$network_text = $instance['profile_text_'.$network];
			?>
<p>
  <label for="<?php echo $this->get_field_id('profile_'.$network ); ?>"><?php echo esc_html__( $display , 'essb' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'profile_'.$network ); ?>" id="<?php echo $this->get_field_id( 'profile_'.$network ); ?>" class="widefat" value="<?php echo $network_value ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id('profile_text_'.$network ); ?>"><?php echo $display . esc_html__( ' custom text' , 'essb' ); ?>:</label>
  <input type="text" name="<?php echo $this->get_field_name( 'profile_text_'.$network ); ?>" id="<?php echo $this->get_field_id( 'profile_text_'.$network ); ?>" class="widefat" value="<?php echo $network_text ?>" />
</p>


			<?php 
		}
		
		?>
		
</div>

		<?php 
	}
	
	public function update( $new_instance , $old_instance ) {
		
		$instance = $old_instance;
		
		$profile_networks = array();
		$profile_networks = essb_advanced_array_to_simple_array(essb_available_social_profiles());
		
		$instance['title'] = $new_instance['title'];
		$instance['template'] = $new_instance['template'];
		$instance['animation'] = $new_instance['animation'];
		$instance['nospace'] = $new_instance['nospace'];
		$instance['show_title'] = $new_instance['show_title'];
		$instance['align'] = $new_instance['align'];
		$instance['size'] = $new_instance['size'];
		$instance['columns'] = $new_instance['columns'];
		$instance['cta'] = $new_instance['cta'];
		$instance['cta_vertical'] = $new_instance['cta_vertical'];
		$instance['custom_list'] = $new_instance['custom_list'];		
		
		foreach ($profile_networks as $network) {
			$instance['profile_'.$network] = $new_instance['profile_'.$network];
			$instance['profile_text_'.$network] = $new_instance['profile_text_'.$network];
		}

		
		return $instance;
	}
	
	public function widget( $args, $instance ) {
		global $essb_options;
		
		if (essb_is_module_deactivated_on('profiles')) {
			return "";
		}
		
		extract($args);
		
		$before_widget = $args['before_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$after_widget  = $args['after_widget'];
		
		$show_title = $instance['show_title'];
		$title = $instance['title'];
		
		$sc_template = isset($instance['template']) ? $instance['template'] : 'flat';
		$sc_animation = isset($instance['animation']) ? $instance['animation'] : '';
		$sc_size = isset($instance['size']) ? $instance['size'] : '';
		$sc_align = isset($instance['align']) ? $instance['align'] : '';
		$sc_columns = isset($instance['columns']) ? $instance['columns'] : '';
		$sc_nospace = $instance['nospace'];
		
		$sc_cta = $instance['cta'];
		$sc_cta_vertical = $instance['cta_vertical'];
		
		if (!empty($sc_nospace) && $sc_nospace != '0') {
			$sc_nospace = "true";
		}
		else {
			$sc_nospace = "false";
		}
		$sc_nospace = essb_unified_true($sc_nospace);
		
		
		if (!empty($sc_cta) && $sc_cta != '0') {
			$sc_cta = "yes";
		}
		else {
			$sc_cta = "no";
		}
		
		if (!empty($sc_cta_vertical) && $sc_cta_vertical != '0') {
			$sc_cta_vertical = "yes";
		}
		else {
			$sc_cta_vertical = "no";
		}
		
		$profile_networks = array();
		$profile_networks = essb_advanced_array_to_simple_array(essb_available_social_profiles());
		
		$profile_active_networks = array();
		
		$profiles_order = essb_option_value('profile_networks_order');
		$profiles_order = ESSBSocialProfilesHelper::simplify_order_list($profiles_order);
		
		if ($instance['custom_list'] != 1) {
		    $profile_networks = ESSBSocialProfilesHelper::get_active_networks();
		    $profile_active_networks = $profile_networks;
		    
		    if (!is_array($profile_networks)) {
		        $profile_networks = array();
		    }
		    
		    $profiles_order = ESSBSocialProfilesHelper::get_active_networks_order();
		    
		    if (!is_array($profiles_order)) {
		        $profiles_order = array();
		    }
		}
		
		if (is_array($profiles_order)) {
			
			foreach ($profile_networks as $key) {
				if (!in_array($key, $profiles_order)) {
					$profiles_order[] = $key;
				}
			}
			
			$profile_networks = $profiles_order;
		}
		
		$sc_network_address = array();
		$sc_network_texts = array();
		foreach ($profile_networks as $network) {
			$value = isset($instance['profile_'.$network]) ? $instance['profile_'.$network] : '';
			$text = isset($instance['profile_text_'.$network]) ? $instance['profile_text_'.$network] : '';
			
			if ($instance['custom_list'] != 1) {
				if (empty($value)) {
					$value = essb_sanitize_option_value('profile_'.$network);
				}
				
				if (!in_array($network, $profile_active_networks)) {
				    continue;
				}
			}
			
			if (!empty($value)) {
				$sc_network_address[$network] = $value;
			}
			
			if (!empty($text)) {
				$sc_network_texts[$network] = $text;
			}
			
		}
		
		
		if (!empty($show_title)) {
			echo $before_widget . $before_title . $title . $after_title;
		}
		
		// if module is not activated include the code		
		$options = array(
				'position' => '',
				'template' => $sc_template,
				'animation' => $sc_animation,
				'nospace' => $sc_nospace,
				'networks' => $sc_network_address,
				'networks_text' => $sc_network_texts,
				'columns' => $sc_columns,
				'cta' => $sc_cta,
				'cta_vertical' => $sc_cta_vertical,
				'size' => $sc_size,
				'align' => $sc_align
		);

		
		echo ESSBSocialProfiles::draw_social_profiles($options);
		
		if (!empty($show_title)) {
			echo $after_widget;
		}
	}
}

?>