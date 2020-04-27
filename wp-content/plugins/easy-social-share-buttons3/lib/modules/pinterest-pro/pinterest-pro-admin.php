<?php

if (!class_exists('ESSBPinterestProAdmin')) {
	class ESSBPinterestProAdmin {
		
		public function __construct() {
			add_filter( 'attachment_fields_to_edit', array( $this, 'edit_custom_field'), 10, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save_custom_field'), 10, 2 );
			add_filter( 'image_send_to_editor', array( $this, 'add_pin_description'), 10, 8 );		
			
			if (!essb_option_bool_value('gutenberg_disable_pinterenst')) {
				add_action( 'enqueue_block_editor_assets', array($this, 'extend_block_example_enqueue_block_editor_assets' ));
			}
		}		
		
		
		public function extend_block_example_enqueue_block_editor_assets() {
			// Enqueue our script
			wp_enqueue_script(
					'essb-pinterest-images',
					esc_url( plugins_url( '/assets/essb-pinterest-images.js', __FILE__ ) ),
					array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
					'1.0.0',
					true // Enqueue the script in the footer.
			);
		}
		
		
		public function add_pin_description($html, $image_id, $caption, $title, $alignment, $url, $size, $alt) {
			$custom_desc = get_post_meta( $image_id, 'essb_pin_description', true );
			$pin_id = get_post_meta( $image_id, 'essb_pin_id', true);
			$pin_nopin = get_post_meta ($image_id, 'essb_pin_nopin', true);
			
			$data = '';
			
			if ($custom_desc != '') {
				$data .= ' data-pin-description="'.esc_attr($custom_desc).'" ';
			}
			if ($pin_id != '') {
				$data .= ' data-pin-id="'.esc_attr($pin_id).'" ';
			}
			
			if ($pin_nopin == 'true') {
				$data .= ' data-pin-nopin="true" ';
			}
			
			if ($data != '') {
				$html = str_replace( "<img src", "<img{$data}src", $html );
			}
			
			return $html;
		}
		
		public function edit_custom_field($form_fields, $post) {
			$form_fields['essb_pin_description'] = array(
					'label' => 'Pinterest Text',
					'input' => 'textarea',
					'value' => get_post_meta( $post->ID, 'essb_pin_description', true )
			);
			
			$form_fields['essb_pin_id'] = array(
					'label' => 'Pinterest Repin ID',
					'input' => 'text',
					'value' => get_post_meta( $post->ID, 'essb_pin_id', true )
			);
			
			$form_fields['essb_pin_nopin'] = array(
					'label' => 'Disable Pinning',
					'input' => 'html',
					'html' => "
<select name='attachments[{$post->ID}][essb_pin_nopin]' id='attachments[{$post->ID}][essb_pin_nopin]'>
    <option value='false'>No</option>
    <option value='true'>Yes</option>
</select>",
					'value' => (bool) get_post_meta( $post->ID, 'essb_pin_nopin', true )
			);
			
			return $form_fields;
		}
		
		public function save_custom_field($post, $attachment) {
			if (isset($attachment) && isset($attachment['essb_pin_description'])) {
				update_post_meta( $post['ID'], 'essb_pin_description', $attachment['essb_pin_description'] );
			}
			
			if (isset($attachment) && isset($attachment['essb_pin_id'])) {
				update_post_meta( $post['ID'], 'essb_pin_id', $attachment['essb_pin_id'] );
			}
			
			if (isset($attachment) && isset($attachment['essb_pin_nopin'])) {
				update_post_meta( $post['ID'], 'essb_pin_nopin', $attachment['essb_pin_nopin'] );
			}
			
			return $post;
		}
		
	}
	
	new ESSBPinterestProAdmin();
}