<?php

if (!class_exists('ESSBPinterestProAdmin')) {
	class ESSBPinterestProAdmin {
		
		public function __construct() {
			add_action ( 'admin_init', array ($this, 'tinymce_loader' ) );
			add_action ( 'admin_enqueue_scripts', array ($this, 'tinymce_css' ), 10 );
				
			add_filter( 'attachment_fields_to_edit', array( $this, 'edit_custom_field'), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save_custom_field'), 10, 2 );
			add_filter( 'image_send_to_editor', array( $this, 'add_pin_description'), 10, 8 );
		}
		
		public function add_pin_description($html, $image_id, $caption, $title, $alignment, $url, $size, $alt) {
			$custom_desc = get_post_meta( $image_id, 'essb_pin_description', true );
			
			if ($custom_desc == '') {
				return $html;
			}
			else {
				$data = ' data-pin-description="'.esc_attr($custom_desc).'" ';
				$html = str_replace( "<img src", "<img{$data}src", $html );
				return $html;
			}
		}
		
		public function edit_custom_field($form_fields, $post) {
			$form_fields['essb_pin_description'] = array(
					'label' => 'Pin Message for Easy Social Share Buttons',
					'input' => 'textarea',
					'value' => get_post_meta( $post->ID, 'essb_pin_description', true )
			);
			
			return $form_fields;
		}
		
		public function save_custom_field($post, $attachment) {
			if (isset($attachment) && isset($attachment['essb_pin_description'])) {
				update_post_meta( $post['ID'], 'essb_pin_description', $attachment['essb_pin_description'] );
			}
			
			return $post;
		}
		
		/**
		 * load our CSS file
		 *
		 * @return [type] [description]
		 */
		public function tinymce_css() {
				
			wp_enqueue_style ( 'essb-pp-admin', plugins_url ( '/assets/essb-pp-admin.css', __FILE__ ), array (), null, 'all' );
		}
		
		/**
		 * load the TinyMCE button
		 *
		 * @return [type] [description]
		 */
		public function tinymce_loader() {
			$can_use = true;
				
			if (essb_option_bool_value('limit_editor_fields') && function_exists('essb_editor_capability_can')) {
				$can_use = essb_editor_capability_can();
			}
			
			if ($can_use) {
				add_filter ( 'mce_external_plugins', array (__class__, 'essb_pp_tinymce_core' ) );
				add_filter ( 'mce_buttons', array (__class__, 'essb_pp_tinymce_buttons' ) );
			}
		}
		
		/**
		 * loader for the required JS
		 *
		 * @param $plugin_array [type]
		 *       	 [description]
		 * @return [type] [description]
		 */
		public static function essb_pp_tinymce_core($plugin_array) {
				
			// add our JS file
			$plugin_array ['essb_pp'] = plugins_url ( '/assets/tinymce-essb-pp.js', __FILE__ );
			$plugin_array ['essb_ppg'] = plugins_url ( '/assets/tinymce-essb-ppg.js', __FILE__ );
				
			// return the array
			return $plugin_array;
		}
		
		/**
		 * Add the button key for event link via JS
		 *
		 * @param $buttons [type]
		 *       	 [description]
		 * @return [type] [description]
		 */
		public static function essb_pp_tinymce_buttons($buttons) {
				
			// push our buttons to the end
			array_push ( $buttons, 'essb_pp' );
			array_push ( $buttons, 'essb_ppg' );
				
			// now add back the sink
			// send them back
			return $buttons;
		}
		
	}
	
	new ESSBPinterestProAdmin();
}