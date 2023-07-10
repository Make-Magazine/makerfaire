<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

echo '<div class="essb-floating-shortcodegenerator" data-shortcode="easy-profiles">';

echo '<div class="shortcode-button">';
echo '<a href="#" class="ao-options-btn ao-generate-shortcode-btn">'.esc_html__('Generate Shortcode', 'essb').'</a>';
echo '</div>';

echo '<div class="shortcode-result">';
echo '</div>';

echo '<div class="shortcode-options">';

essb_depend_load_function('essb_get_shortcode_options_easy_profiles', 'lib/admin/settings/shortcode-options/easy-profiles.php');

$default_options = essb_get_shortcode_options_easy_profiles();
foreach ($default_options as $key => $setup) {
	$value = '';
	$type = $setup['type'];
	$title = isset($setup['title']) ? $setup['title'] : '';
	$description = isset($setup['description']) ? $setup['description'] : '';
	$options = isset($setup['options']) ? $setup['options'] : array();
		
	if ($type == 'text') {
		echo '<p>';
		echo '<label for="instagramfeed_shortcode_'.$key.'"><strong>'.$title.'</strong></label>';
		echo '<input class="widefat" id="instagramfeed_shortcode_'.$key.'" name="instagramfeed_shortcode_'.$key.'" type="text" value="'.esc_attr($value).'" data-param="'.esc_attr($key).'" />';

		if ($description != '') {
			echo '<em>'.$description.'</em>';
		}

		echo '</p>';
	}
		
	if ($type == 'select') {
		echo '<p>';
		echo '<label for="instagramfeed_shortcode_'.$key.'"><strong>'.$title.'</strong></label>';
		echo '<select class="widefat" id="instagramfeed_shortcode_'.$key.'" name="instagramfeed_shortcode_'.$key.'" value="'.esc_attr($value).'" data-param="'.esc_attr($key).'" >';
		foreach ($options as $opt_key => $opt_value) {
			echo '<option value="'.$opt_key.'" '.($opt_key == $value ? 'selected': '').'>'.$opt_value.'</option>';
		}
		echo '</select>';

		if ($description != '') {
			echo '<em>'.$description.'</em>';
		}

		echo '</p>';
	}
	
	if ($type == 'checkbox') {
	    $options = array("no" => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb'));
	    echo '<p>';
	    echo '<label for="instagramfeed_shortcode_'.$key.'"><strong>'.$title.'</strong></label>';
	    echo '<select class="widefat" id="instagramfeed_shortcode_'.$key.'" name="instagramfeed_shortcode_'.$key.'" value="'.esc_attr($value).'" data-param="'.esc_attr($key).'" >';
	    foreach ($options as $opt_key => $opt_value) {
	        echo '<option value="'.$opt_key.'" '.($opt_key == $value ? 'selected': '').'>'.$opt_value.'</option>';
	    }
	    echo '</select>';
	    
	    if ($description != '') {
	        echo '<em>'.$description.'</em>';
	    }
	    
	    echo '</p>';
	}
	
	if ($type == 'separator') {
	    echo '<div class="shortcode-options-separator">';
	    
	    if ($title != '') {
	        echo '<label><strong>' . $title . '</strong></label>';
	        if ($description != '') {
	            echo '<em>'.$description.'</em>';
	        }
	    }
	    
	    echo '</div>';
	}
	
	if ($type == 'separator-small') {
	    echo '<div class="shortcode-options-separator-small">';
	    
	    if ($title != '') {
	        echo '<label><strong>' . $title . '</strong></label>';
	        if ($description != '') {
	            echo '<em>'.$description.'</em>';
	        }
	    }
	    
	    echo '</div>';
	}
	
	if ($type == 'section-open') {
	    echo '<div class="shortcode-options-section'.($title != '' ? ' '. $title : '').'">';
	}
	
	if ($type == 'section-close') {
	    echo '</div>';
	}
}
echo '</div>'; // options
echo '</div>'; // generator