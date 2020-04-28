<?php
/**
 * Style Actions Library
 *
 * All functions located inside library are related to the new style library. The actions
 * download, update and create user based styles and plugin default styles.
 *
 * @package EasySocialShareButtons
 * @since 5.9
 */

class ESSBStyleLibraryManager {

	public static $option_name = 'essb_stylemaneger_user';

	private static $instance = null;


	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	public function __construct() {
		add_action ( 'wp_ajax_essb_style_library', array($this, 'request_parser') );
	}

	/**
	 * The request_parser function runs everytime when the style manager action is called.
	 * It will dispatch the event to the internal class function and return the required
	 * for front-end data
	 *
	 * @since 5.9
	 */
	public function request_parser() {

		/*if (! isset( $_REQUEST['essb_styleoptions_token'] ) || !wp_verify_nonce( $_REQUEST['essb_styleoptions_token'], 'essb_styleoptions_setup' )) {
			print 'Sorry, your nonce did not verify.';
			wp_die();
		}*/


		$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

		if ($cmd == 'get') {
			echo json_encode($this->get());
		}

		if ($cmd == 'save_style') {
			echo json_encode($this->process_save());
		}

		if ($cmd == 'import_style') {
			echo json_encode($this->process_import());
		}

		if ($cmd == 'remove_style') {
			echo json_encode($this->process_delete());
		}

		if ($cmd == 'apply') {
			echo json_encode($this->process_apply());
		}

		if ($cmd == 'convert_style') {
			echo json_encode($this->convert_style());
		}

		// exit command execution
		wp_die();
	}

	/**
	 * Convert existing options that are saved for location to a new style inside the user
	 * library
	 *
	 * @since 5.9
	 */
	public function convert_style() {

		global $essb_options;

		$output = array();

		$style_id = isset($_REQUEST['style_id']) ? $_REQUEST['style_id'] : '';
		$style_name = isset($_REQUEST['style_name']) ? $_REQUEST['style_name'] : '';
		$style_category = isset($_REQUEST['style_category']) ? $_REQUEST['style_category'] : '';
		$style_user = isset($_REQUEST['style_user']) ? $_REQUEST['style_user'] : '';
		$style_location = isset($_REQUEST['style_location']) ? $_REQUEST['style_location']: '';
		$style_tags = isset($_REQUEST['style_tags']) ? $_REQUEST['style_tags'] : '';
		$style_desc = isset($_REQUEST['style_desc']) ? $_REQUEST['style_desc'] : '';
		$original_location = isset($_REQUEST['original_location']) ? $_REQUEST['original_location'] : '';

		/**
		 * The list of fields that will be converted from the location settings.
		 */
		$fields = array('template', 'button_style', 'button_pos', 'button_size', 'nospace',
				'css_animations', 'show_counter', 'counter_pos', 'total_counter_pos',
				'button_width', 'fixed_width_value', 'fixed_width_align',
				'fullwidth_align', 'fullwidth_first_button', 'fullwidth_second_button',
				'fullwidth_share_buttons_correction', 'fullwidth_share_buttons_correction_mobile',
				'fullwidth_share_buttons_container', 'fullwidth_share_buttons_columns',
				'fullwidth_share_buttons_columns_align', 'flex_width_value', 'flex_width_align',
				'networks', 'more_button_func', 'more_button_icon', 'share_button_func',
				'share_button_icon', 'share_button_style', 'share_button_counter', 'code',
				'code_before', 'code_after');

		$output = array('id' => $style_id, 'name' => $style_name, 'status' => '');

		/**
		 * Begin conversion of the fields
		 */
		$style_options = array();
		$used_networks = array();
		foreach ($fields as $translate_field) {
			if ($original_location != '' && $original_location != 'site') {
				$value = isset($essb_options[$original_location.'_'.$translate_field]) ? $essb_options[$original_location.'_'.$translate_field] : '';
			}
			else {
				$value = isset($essb_options[$translate_field]) ? $essb_options[$translate_field] : '';

				if ($translate_field == 'template') {
					$value = isset($essb_options['style']) ? $essb_options['style'] : '';
				}
			}

			if ($value != '') {
				if ($translate_field == 'networks') {
					$used_networks = $value;

					$value = implode(',', $value);
				}

				$style_options[$translate_field] = $value;
			}
		}

		/**
		 * Converting network texts if filled inside the options
		 */
		if (is_array($used_networks)) {
			foreach ($used_networks as $network) {
				if ($original_location != '' && $original_location != 'site') {
					$key = $original_location.'_'.$network.'_name';
				}
				else {
					$key = 'user_network_name_' . $network;
				}
				//$key = $original_location.'_'.$network.'_name';
				$value = isset($essb_options[$key]) ? $essb_options[$key] : '';

				if ($value != '') {
					$style_options[$network.'_name'] = $value;
				}
			}
		}

		$user_styles = $this->get_user_styles();

		$user_styles[$style_id] = array(
				'name' => $style_name,
				'category' => $style_category,
				'options' => $style_options,
				'user' => ($style_user != '' ? 'true' : 'false'),
				'location' => $style_location,
				'tags' => $style_tags,
				'desc' => $style_desc
		);

		$this->save_user_styles($user_styles);
		$output['status'] = '200';

		return $output;
	}

	/**
	 * Apply selected style on location key. Once the style is applied it will also
	 * activate custom position option
	 *
	 * @since 5.9
	 */
	public function process_apply() {
		$style_id = isset($_REQUEST['style_id']) ? $_REQUEST['style_id'] : '';
		$style_position = isset($_REQUEST['style_position']) ? $_REQUEST['style_position'] : '';

		$output = array('position' => $style_position, 'style_id' => $style_id, 'status' => '');


		if ($style_id != '' && $style_position != '') {
			$saved_styles = $this->get();
			$styles_data = array();
			$styles_found = false;

			foreach ($saved_styles->styles as $key => $options) {
				if ($key == $style_id) {
					$styles_data = $options;
					$styles_found = true;
				}
			}

			if ($styles_found) {

				global $essb_options;

				if (is_object($styles_data)) {
					$styles_data = (array) $styles_data;
				}

				/**
				 * Apply the style on selected location and activate the option for this
				 */

				if ($style_position != 'site') {
					$essb_options[$style_position. '_activate'] = 'true';
				}

				foreach ($styles_data['options'] as $prop => $value) {

					if ($prop == 'networks') {
						$value = explode(',', $value);
					}

					if ($style_position != 'site') {
						$essb_options[$style_position. '_' . $prop] = $value;
					}
					else {
						$essb_options[$prop] = $value;
					}
				}

				/**
				 * Apply custom options if present inside plugin
				 */
				if (isset($styles_data['generalOptions'])) {
					foreach ($styles_data['generalOptions'] as $prop => $value) {
						$essb_options[$prop] = $value;
					}
				}

				/**
				 * Apply additional CSS code if provided
				 */
				if (isset($styles_data['css'])) {
					if (!isset($essb_options['customizer_css'])) {
						$essb_options['customizer_css'] = '';
					}

					$essb_options['customizer_css'] .= str_replace('%%position%%', $style_position, $styles_data['css']);
				}

				update_option ( ESSB3_OPTIONS_NAME, $essb_options );
				$output['status'] = '200';
			}
		}

		return $output;
	}

	/**
	 * Removing a saved style inside library. The remove can be done only for user
	 * based styles (the default bundled styles cannot be removed).
	 *
	 * @since 5.9
	 */
	public function process_delete() {
		$style_id = isset($_REQUEST['style_id']) ? $_REQUEST['style_id'] : '';

		$user_styles = $this->get_user_styles();

		if (isset($user_styles[$style_id])) {
			unset($user_styles[$style_id]);
		}

		$this->save_user_styles($user_styles);

		return array('code' => '100', 'text' => 'Success');
	}

	public function process_import() {
		$style_id = isset($_REQUEST['style_id']) ? $_REQUEST['style_id'] : '';
		$style_options = isset($_REQUEST['style_options']) ? $_REQUEST['style_options'] : '';

		$user_styles = $this->get_user_styles();

		$user_styles[$style_id] = $style_options;

		$output = $user_styles;

		$this->save_user_styles($user_styles);

		return $output;

	}

	/**
	 * Read the post requested data and save inside user settings the created by user styles.
	 * The function is used no matter style is called from the style builder or from saving a ready to use option
	 *
	 * @since 5.9
	 */
	public function process_save() {
		$output = array();

		$style_id = isset($_REQUEST['style_id']) ? $_REQUEST['style_id'] : '';
		$style_name = isset($_REQUEST['style_name']) ? $_REQUEST['style_name'] : '';
		$style_category = isset($_REQUEST['style_category']) ? $_REQUEST['style_category'] : '';
		$style_options = isset($_REQUEST['style_options']) ? $_REQUEST['style_options'] : '';
		$style_user = isset($_REQUEST['style_user']) ? $_REQUEST['style_user'] : '';
		$style_location = isset($_REQUEST['style_location']) ? $_REQUEST['style_location']: '';
		$style_tags = isset($_REQUEST['style_tags']) ? $_REQUEST['style_tags'] : '';
		$style_desc = isset($_REQUEST['style_desc']) ? $_REQUEST['style_desc'] : '';

		$user_styles = $this->get_user_styles();

		$user_styles[$style_id] = array(
				'name' => $style_name,
				'category' => $style_category,
				'options' => $style_options,
				'user' => ($style_user != '' ? 'true' : 'false'),
				'location' => $style_location,
				'tags' => $style_tags,
				'desc' => $style_desc
		);

		$output = $user_styles;

		$this->save_user_styles($user_styles);

		return $output;
	}

	/**
	 * Read and return as javascript object all packed and user-based
	 * styles
	 *
	 * @since 5.9
	 */
	public function get() {

		/**
		 * Loading the default styles that are bundled inside plugin. The styles will be as a ready to use
		 * json string inside plugin. Once loaded they will be merged with those that are generated by
		 * user.
		 */
		if (!function_exists('essb_get_preset_styles')) {
			include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/styles-library/styles-preset-json.php');
		}

		$base_json = $this->clear_output(essb_get_preset_styles());

		$r = json_decode($base_json);

		/**
		 * Additional attaching the user styles that are saved inside options
		 */
		$user = $this->get_user_styles();

		if (is_array($user)) {
			foreach ($user as $key => $data) {
				$r->styles->{$key} = $data;
			}
		}

		return $r;
	}

	public function get_all_styles($user = array()) {
		if (!function_exists('essb_get_preset_styles')) {
			include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/styles-library/styles-preset-json.php');
		}

		$base_json = $this->clear_output(essb_get_preset_styles());

		$r = json_decode($base_json);

		/**
		 * Additional attaching the user styles that are saved inside options
		 */
		if (is_array($user)) {
			foreach ($user as $key => $data) {
				$r->styles->{$key} = $data;
			}
		}

		return $r;
	}

	/**
	 * The function removes the extra spaces or new lines from a string. Typically used
	 * for minifaction of strings but here we are clearing the extra spaces to prevent encoding fail
	 *
	 * @param string $code
	 * @return string
	 *
	 * @since 5.9
	 */
	public function clear_output($code) {
		$code = trim(preg_replace('/\s+/', ' ', $code));

		return $code;
	}

	public function get_user_styles() {
		$options = get_option(self::$option_name);

		if (!isset($options) || !is_array($options)) {
			$options = array();
		}

		return $options;
	}

	public function save_user_styles($value) {
		update_option(self::$option_name, $value, 'no');
	}
}
