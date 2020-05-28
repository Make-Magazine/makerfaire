<?php

/**
 * Generate the meta editing options for the plugin
 * 
 * @author appscreo
 * @since 5.0
 * @version 7.0
 * @package EasySocialShareButtons
 */

class ESSBMetaboxInterface {
	
	public static function draw_form_start($metabox_id) {		
		echo '<div class="essb-options essb-metabox-options essb-metabox7" id="'.esc_attr($metabox_id).'">';
	}
		
	public static function draw_sidebar($options = array(), $data_instance_id = '') {
		
		$data_instance_class = "";
		$data_instance_menu_class = "";
		if ($data_instance_id != '') {
			$data_instance_class = " essb-options-sidebar-".$data_instance_id;
			$data_instance_menu_class = " essb-options-group-menu-".$data_instance_id;
		}
		
		echo '<div class="essb-options-sidebar essb-settings-panel-navigation'.esc_attr($data_instance_class).'" id="essb-options-sidebar" data-instance="'.esc_attr($data_instance_id).'">';

		echo '<ul class="essb-plugin-menu essb-options-group-menu'.esc_attr($data_instance_menu_class).'" id="sticky-navigation">';
		foreach ($options as $single) {
			$type = $single['type'];
			$field_id = isset($single['field_id']) ? $single['field_id'] : '';
			$title = isset($single['title']) ? $single['title'] : '';
			$sub_menuaction = isset($single['action']) ? $single['action'] : '';
			$default_child = isset($single['default_child']) ? $single['default_child'] : '';
			$icon = isset($single['icon']) ? $single['icon'] : '';
			
			$description = isset($single['description']) ? $single['description'] : '';
			
			if ($description != '') {
				$title .= '<span class="description">'.$description.'</span>';
			}
			
			if ($icon == 'default') {
				$icon = 'gear';
			}
			
			if ($icon != '') {
				$icon = sprintf('<i class="essb-sidebar-icon fa fa-%1$s"></i>', $icon);
			}
			
			$css_class = "";
			switch ($type) {
				case "menu_item":
					$css_class = "essb-menu-item";
					
					if ($sub_menuaction == "activate_first") {
						$css_class .= " essb-activate-first";
					}
					break;
				case "sub_menu_item":
					$css_class = "essb-submenu-item";
					
					if ($sub_menuaction == 'menu') {
						$css_class .= " essb-submenu-menuitem";
					}
					break;
				case "heading":
					$css_class = "essb-title";
					break;
				default:
					$css_class = "essb-menu-item";
					break;
			}
			
			printf('<li class="%1$s" data-menu="%2$s" data-activate-child="%4$s" id="essb-menu-%2$s"><a href="#" class="essb-nav-tab">%5$s<span>%3$s</span></a></li>', $css_class, $field_id, $title, $default_child, $icon);
		}
		
		echo '</ul>';
		
		echo '</div>';
		
	}
	
	public static function draw_content_start($min_height = '300', $data_instance_id = '') {
		$data_instance_class = "";
		if ($data_instance_id != '') {
			$data_instance_class = " essb-options-container-".$data_instance_id;
		}
		echo '<div class="essb-options-container'.esc_attr($data_instance_class).'" style="min-height: '.esc_attr($min_height).'px;" data-instance="'.esc_attr($data_instance_id).'">';
	}
	
	public static function draw_content_end() {
		echo '</div>';
	}
	
	public static function draw_content_section_start($section = '') {
		printf('<div id="essb-container-%1$s" class="essb-data-container">',$section);
			
		echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">
		<col width="25%" />
		<col width="75%" />';
		
		
	}
	
	public static function draw_content_section_end() {
		
		echo '</table>';
			
		echo '</div>';
	}	
	
	public static function draw_first_menu_activate($data_instance_id = '') {
		if ($data_instance_id != '') {
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$(".essb-options-group-menu-<?php echo $data_instance_id; ?>").find(".essb-menu-item").first().addClass('active');
				var container_key = $(".essb-options-group-menu-<?php echo $data_instance_id; ?>").find(".essb-menu-item").first().attr('data-menu') || '';

				if (container_key != '') 
					$('#essb-container-'+container_key).fadeIn();
			});
			</script>
			<?php 
		}
	}
	
	public static function draw_content($options = array(), $min_height = '840') {
		echo '<div class="essb-options-container" style="min-height: '.esc_attr($min_height).'px;">';

		foreach($options as $section => $fields) {
			printf('<div id="essb-container-%1$s" class="essb-data-container">',$section);
			
			echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />';
			
			$section_options = $fields;
			
			ESSBOptionsFramework::reset_row_status();
			
			foreach ($section_options as $option) {
				ESSBMetaboxOptionsFramework::draw_options_field($option);
			}
			
			echo '</table>';
			
			echo '</div>';
		}
		
		echo '</div>';
	}	
	
	public static function draw_form_end() {
		echo '</div>';
	}
	
}

?>