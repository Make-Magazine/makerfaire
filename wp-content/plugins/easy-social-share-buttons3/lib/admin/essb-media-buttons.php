<?php

/**
 * Register the shortcode generation button from the classic editor
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 7.0
 */
class ESSBEditorMediaButtons {
	
	/**
	 * Register the buttons
	 */
	public function __construct() {	
	    
	    if (!essb_option_bool_value('classic_editor_disable_buttons')) {	    
    		add_action ( 'admin_init', array ($this, 'tinymce_loader' ) );
    		add_action ( 'media_buttons', array( $this, 'media_button' ), 20 );
	    }
	}
	
	/**
	 * Add media share button over the editor
	 * 
	 * @param unknown_type $editor
	 */
	public function media_button($editor) {
		// Setup the icon
		$icon = '<span class="mb-essb-sc-generator"></span>';
		
		printf( '<a href="#" class="button essb-mce-button" data-editor="%s" title="%s">%s %s</a>',
				esc_attr( $editor ),
				esc_attr__( 'Social Media Shortcodes by Easy Social Share Buttons', 'essb' ),
				$icon,
				__( 'Social Media', 'essb' )
		);
	}
	
	/**
	 * Add media share button to the editor (icon)
	 */
	public function tinymce_loader() {
		$can_use = true;
			
		if (essb_option_bool_value('limit_editor_fields') && function_exists('essb_editor_capability_can')) {
			$can_use = essb_editor_capability_can();
		}
			
		if ($can_use) {
			add_filter ( 'mce_external_plugins', array ($this, 'tinymce_core' ) );
			add_filter ( 'mce_buttons', array ($this, 'tinymce_buttons' ) );
		}
	}
	
	public function tinymce_core($plugin_array) {
		// add our JS file
		$plugin_array ['essb_media_shortcodes'] = ESSB3_PLUGIN_URL . '/assets/admin/essb-admin-editor.js';
			
		// return the array
		return $plugin_array;
	}

	public function tinymce_buttons($buttons) {
		array_push ( $buttons, 'essb_media_shortcodes' );
		
		return $buttons;
	}
}

new ESSBEditorMediaButtons();