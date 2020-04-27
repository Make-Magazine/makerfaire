<?php
/**
 * Managing custom plugin styles and scripts
 *
 * @package EasySocialShareButtons
 * @since 5.9
 */

function essb_stylemanager_clear_positions($options) {
	if (isset($options['content_manual'])) unset($options['content_manual']);
	if (isset($options['widget'])) unset($options['widget']);

	return $options;
}

function essb_stylemanager_dropdown_positions($values) {
	$r = array();
	$r['content'] = esc_html__('Content Positions', 'essb');
	$r['horizonal'] = esc_html__('Horizontal Layout', 'essb');
	$r['vertical'] = esc_html__('Vertical Layout', 'essb');

	foreach ($values as $key => $data) {
		$r[$key] = $data['label'];
	}

	$positions_source = essb5_available_button_positions_mobile();
	foreach ($positions_source as $key => $data) {
		if (!isset($r[$key])) {
			$r[$key] = $data['label'];
		}
	}

	return $r;
}

function essb_stylemanager_get_all_positions() {
	$r = array();

	$r['content'] = esc_html__('Content Positions', 'essb');

	$positions_source = essb_stylemanager_clear_positions(essb5_available_content_positions(true));
	foreach ($positions_source as $key => $data) {

		$key = str_replace('content_', '', $key);

		$r[$key] = $data['label'];
	}

	$positions_source = essb_stylemanager_clear_positions(essb5_available_button_positions(true));
	foreach ($positions_source as $key => $data) {
		$r[$key] = $data['label'];
	}

	$positions_source = essb5_available_button_positions_mobile();
	foreach ($positions_source as $key => $data) {
		if (!isset($r[$key])) {
			$r[$key] = $data['label'];
		}
	}

	return $r;
}

function essb_stylemanager_get_all_tempates() {
	$r = array();
	$all_templates = essb_available_tempaltes4();

	foreach ($all_templates as $key => $name) {
		$class_key = essb_template_folder($key);

		$r[$key] = $class_key;
	}

	return $r;
}

function essb_stylemanager_inject_site($values) {
	$r = array();
	$r ['site'] = array ('image' => 'assets/images/display-positions-09.png', 'label' => 'Global Styles', 'desc' => 'Use this if you wish to apply the style over the global styles of site' );

	foreach ($values as $key => $data) {
		$r[$key] = $data;
	}

	return $r;
}

function essb_stylemanager_generate_scripts() {
	$opts_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 'essb_options';
	
	$code = '
	var essb_styles_ajaxurl = "'. esc_url(admin_url ('admin-ajax.php')).'",
		essb_styles_reloadurl = "'.esc_url(admin_url ('admin.php?page='.esc_attr($opts_page))).'",
		essb_styles_positions_source = '.json_encode(essb_stylemanager_get_all_positions()).',
		essb_styles_templates_source = '.json_encode(essb_stylemanager_get_all_tempates()).';';
	
	return $code;
}

wp_nonce_field( 'essb_styleoptions_setup', 'essb_styleoptions_token' );
wp_add_inline_script('essb-admin5', essb_stylemanager_generate_scripts());

?>

<!-- notifications -->
<div class="styles-modal"></div>
<div class="essb-helper-popup" id="essb-styleselect" data-width="1200" data-height="auto">
	<div class="essb-helper-popup-title">
		<i class="ti-sharethis"></i> Styles Library by Easy Social Share Buttons
		<div class="actions">
			<a href="#" class="styleselect-close" title="Close the window"><i class="ti-close"></i></a>
		</div>

	</div>
	<div class="essb-helper-popup-content">
		<?php
		wp_nonce_field( 'essb_styles_setup', 'essb_styles_token' );
		?>
		<!-- Styles Screen -->

		<div id="styles-list-screen">
			<div class="styles-cats">
				<div class="title">Categories</div>
				<div class="list"></div>
				<div class="create-new style-btn">
					<i class="ti-widget-alt"></i> <span>Create New</span>
				</div>
				<div class="import-new style-btn">
					<i class="ti-upload"></i> <span>Import Style</span>
				</div>
			</div>
			<div class="styles-content">
				<div class="grid-container" id="style-grid-container">
				</div>
			</div>
		</div>

		<!-- end: Styles Screen -->

		<!-- begin: Create New Style for Location Settings -->
		<div id="styles-manage-new" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-new-save style-btn inline"><i class="ti-save"></i><span>Save</span></a>
				<a href="#" class="manage-new-close style-btn inline"><i class="ti-close"></i><span>Cancel</span></a>
			</div>
			<div class="title-func">Creating Style</div>
			<div class="inner-content-title">Style Name & Options</div>
			<div class="inner-content">
				<input type="text" class="input-element stretched" placeholder="Enter style name ..." id="managestyle-new-name" />
				<input type="hidden" id="managestyle-new-cat" value="" />
				<input type="hidden" id="managestyle-new-action" value="" />

				<div class="one-half">
					<div class="inner-content-title">Recommended Position</div>
					<select class="input-element stretched" id="managestyle-new-recommend">
						<option value=""></option>

						<?php
						$positions = essb_stylemanager_clear_positions(essb_stylemanager_dropdown_positions(essb5_available_button_positions(true)));
						foreach ($positions as $key => $value) {
							echo '<option value="'.esc_attr($key).'">'.$value.'</option>';
						}

						?>

					</select>
				</div>
				<div class="one-half">
					<div class="inner-content-title">Tags</div>
					<input type="text" class="input-element stretched" id="managestyle-new-tags" placeholder="example: tag1,tag2 ..."/>
				</div>

				<div class="one">
					<div class="inner-content-title">Description</div>
					<textarea class="input-element stretched" id="managestyle-mew-desc"></textarea>
				</div>
			</div>
		</div>
		<!-- end: -->

		<!-- begin: Styles Create/Edit Screen -->
		<div id="styles-manage" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-save style-btn inline"><i class="ti-save"></i><span>Save</span></a>
				<a href="#" class="manage-back style-btn inline"><i class="ti-close"></i><span>Cancel</span></a>
			</div>
			<div class="title-func">Managing Style</div>

			<div class="inner-content-title">Style Name & Options</div>
			<div class="inner-content">
				<input type="text" class="input-element stretched" placeholder="Enter style name ..." id="managestyle-name" />
				<input type="hidden" id="managestyle-cat" value="" />
				<input type="hidden" id="managestyle-action" value="" />

				<div class="one-half">
					<div class="inner-content-title">Recommended Position</div>
					<select class="input-element stretched" id="managestyle-recommend">
						<option value=""></option>

						<?php
						$positions = essb_stylemanager_clear_positions(essb_stylemanager_dropdown_positions(essb5_available_button_positions(true)));
						foreach ($positions as $key => $value) {
							echo '<option value="'.esc_attr($key).'">'.$value.'</option>';
						}

						?>

					</select>
				</div>
				<div class="one-half">
					<div class="inner-content-title">Tags</div>
					<input type="text" class="input-element stretched" id="managestyle-tags" placeholder="example: tag1,tag2 ..."/>
				</div>

				<div class="one">
					<div class="inner-content-title">Description</div>
					<textarea class="input-element stretched" id="managestyle-desc"></textarea>
				</div>
			</div>

			<div class="inner-content-title">Template & Button Styles</div>
			<div class="inner-content">
				<?php
				ESSBOptionsFramework::draw_structure_row_start('visual-setup');

				// column 1
				ESSBOptionsFramework::draw_structure_section_start('c6');
				ESSBOptionsFramework::draw_title(esc_html__('Template', 'essb'), '', 'inner-row');
				essb_component_template_select('managestyle', 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Button Style', 'essb'), '', 'inner-row');
				essb_component_buttonstyle_select('managestyle', 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Button Align', 'essb'), '', 'inner-row');
				$select_values = array('' => array('title' => 'Left', 'content' => '<i class="ti-align-left"></i>'),
						'center' => array('title' => 'Center', 'content' => '<i class="ti-align-center"></i>'),
						'right' => array('title' => 'Right', 'content' => '<i class="ti-align-right"></i>'));
				ESSBOptionsFramework::draw_toggle_field('managestyle_button_pos', $select_values, 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Button Size', 'essb'), '', 'inner-row');
				$select_values = array('' => array('title' => 'Default', 'content' => 'Default', 'isText'=>true),
						'xs' => array('title' => 'Extra Small', 'content' => 'XS', 'isText'=>true),
						's' => array('title' => 'Small', 'content' => 'S', 'isText'=>true),
						'm' => array('title' => 'Medium', 'content' => 'M', 'isText'=>true),
						'l' => array('title' => 'Large', 'content' => 'L', 'isText'=>true),
						'xl' => array('title' => 'Extra Large', 'content' => 'XL', 'isText'=>true),
						'xxl' => array('title' => 'Extra Extra Large', 'content' => 'XXL', 'isText'=>true)
				);
				ESSBOptionsFramework::draw_toggle_field('managestyle_button_size', $select_values, 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Animations', 'essb'), '', 'inner-row');
				essb_component_animation_select('managestyle', 'manage_style');

				ESSBOptionsFramework::draw_options_row_start(esc_html__('Without Space Between Share Buttons', 'essb'), '', '', '8');
				ESSBOptionsFramework::draw_switch_field('managestyle_nospace', 'manage_style');
				ESSBOptionsFramework::draw_options_row_end();

				ESSBOptionsFramework::draw_structure_section_end();

				// column 2
				ESSBOptionsFramework::draw_structure_section_start('c6');

				ESSBOptionsFramework::draw_options_row_start(esc_html__('Show Share Counter', 'essb'), '', '', '8');
				ESSBOptionsFramework::draw_switch_field('managestyle_show_counter', 'manage_style');
				ESSBOptionsFramework::draw_options_row_end();

				ESSBOptionsFramework::draw_title(esc_html__('Individual Button Share Counter Position', 'essb'), '', 'inner-row');
				essb_component_counterpos_select('managestyle', 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Total Share Counter Position', 'essb'), '', 'inner-row');
				essb_component_totalcounterpos_select('managestyle', 'manage_style');

				ESSBOptionsFramework::draw_title(esc_html__('Button Width', 'essb'), '', 'inner-row');
				$select_values = array('' => array('title' => 'Automatic Width', 'content' => 'AUTO', 'isText'=>true),
					'fixed' => array('title' => 'Fixed Width', 'content' => 'Fixed', 'isText'=>true),
					'full' => array('title' => 'Full Width', 'content' => 'Full', 'isText'=>true),
					'flex' => array('title' => 'Fluid', 'content' => 'Fluid', 'isText'=>true),
					'column' => array('title' => 'Columns', 'content' => 'Columns', 'isText'=>true),);
				ESSBOptionsFramework::draw_toggle_field('managestyle_button_width', $select_values, 'manage_style');

				//-- width controlers --
				// fixed
				ESSBOptionsFramework::draw_holder_start(array('class' => 'managestyle-essb-fixed-width essb-hidden-open', 'user_id' => 'managestyle-essb-fixed-width'));
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Custom Button Width', 'essb'));
				ESSBOptionsFramework::draw_input_field('managestyle_fixed_width_value', false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Alignment', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_fixed_width_align', array("" => "Left", "center" => "Center", "right" => "Right"), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_holder_end();

				// full
				ESSBOptionsFramework::draw_holder_start(array('class' => 'managestyle-essb-full-width essb-hidden-open', 'user_id' => 'managestyle-essb-full-width'));
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Button Width Correction', 'essb'));
				ESSBOptionsFramework::draw_input_field('managestyle_fullwidth_share_buttons_correction', false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Alignment', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_fullwidth_align', array("" => "Left", "center" => "Center", "right" => "Right"), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_holder_end();

				// columns
				ESSBOptionsFramework::draw_holder_start(array('class' => 'managestyle-essb-column-width essb-hidden-open', 'user_id' => 'managestyle-essb-column-width'));
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Columns', 'essb'));
				$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10");
				ESSBOptionsFramework::draw_select_field('managestyle_fullwidth_share_buttons_columns', $listOfOptions, false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Alignment', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_fullwidth_share_buttons_columns_align', array("" => "Left", "center" => "Center", "right" => "Right"), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_holder_end();

				// flex
				ESSBOptionsFramework::draw_holder_start(array('class' => 'managestyle-essb-flex-width essb-hidden-open', 'user_id' => 'managestyle-essb-flex-width'));
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Preserve Space For Total Counter Area (%)', 'essb'));
				ESSBOptionsFramework::draw_input_field('managestyle_flex_width_value', false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Assign a Specific Button Width (%)', 'essb'));
				ESSBOptionsFramework::draw_input_field('managestyle_flex_button_value', false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Alignment', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_flex_width_align', array("" => "Left", "center" => "Center", "right" => "Right"), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_holder_end();

				ESSBOptionsFramework::draw_structure_section_end();

				ESSBOptionsFramework::draw_structure_row_end();
				?>
			</div>

			<div class="inner-content-title">Style Preview</div>
			<div class="inner-content">
				<?php
				$position = 'managestyle';

				$code = '<div class="essb-component-buttons-livepreview" data-settings="essb_'.$position.'_global_preview">';
				$code .= '</div>';

				$code .= "<script type=\"text/javascript\">

				var essb_".$position."_global_preview = {
				'networks': [ {'key': 'facebook', 'name': 'Facebook'}, {'key': 'twitter', 'name': 'Twitter'}, {'key': 'google', 'name': 'Google'}, {'key': 'pinterest', 'name': 'Pinterest'}, {'key': 'linkedin', 'name': 'LinkedIn'}],
				'template': 'essb_field_".$position."_template',
				'button_style': 'essb_field_".$position."_button_style',
				'button_size': 'essb_options_".$position."_button_size',
				'align': 'essb_options_".$position."_button_pos',
				'nospace': 'essb_field_".$position."_nospace',
				'counter': 'essb_field_".$position."_show_counter',
				'counter_pos': 'essb_field_".$position."_counter_pos',
				'total_counter_pos': 'essb_field_".$position."_total_counter_pos',
				'width': 'essb_options_".$position."_button_width',
				'animation': 'essb_field_".$position."_css_animations',
				'fixed_width': 'essb_options_".$position."_fixed_width_value',
				'fixed_align': 'essb_options_".$position."_fixed_width_align',
				'columns_count': 'essb_options_".$position."_fullwidth_share_buttons_columns',
				'columns_align': 'essb_options_".$position."_fullwidth_share_buttons_columns_align',
				'full_button': 'essb_options_".$position."_fullwidth_share_buttons_correction',
				'full_align': 'essb_options_".$position."_fullwidth_align',
				'full_first': 'essb_options_".$position."_fullwidth_first_button',
				'full_second': 'essb_options_".$position."_fullwidth_second_button',
				'flex_align': 'essb_options_".$position."_flex_width_align',
				'flex_width': 'essb_options_".$position."_flex_width_value',
				'flex_button': 'essb_options_".$position."_flex_button_value',
				'code_before': 'essb_options_".$position."_code_before',
				'code_after': 'essb_options_".$position."_code_after',
				};

				</script>";

				echo $code;
				?>
			</div>

			<div class="inner-content-title">Used Networks</div>
			<div class="inner-content">
				<?php
				essb_component_network_selection('managestyle', 'manage_style');

				?>
			</div>

			<div class="inner-content-title">Additional Network Options</div>
			<div class="inner-content">
				<?php
				ESSBOptionsFramework::draw_title(esc_html__('More Button', 'essb'), '', 'inner-row');
				ESSBOptionsFramework::draw_holder_start();

				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('More Button Action', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_more_button_func', essb_available_more_button_commands(), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');

				$select_values = array('plus' => array('title' => 'Plus Icon', 'content' => '<i class="essb_icon_more"></i>'),
						'dots' => array('title' => 'Dots Icon', 'content' => '<i class="essb_icon_more_dots"></i>'));
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('More Button Icon', 'essb'));
				ESSBOptionsFramework::draw_toggle_field('managestyle_more_button_icon', $select_values, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');
				ESSBOptionsFramework::draw_holder_end();


				ESSBOptionsFramework::draw_title(esc_html__('Share Button', 'essb'), '', 'inner-row');
				ESSBOptionsFramework::draw_holder_start();
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Share Button Action', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_share_button_func', essb_available_more_button_commands(), false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');

				$select_values = array('plus' => array('title' => '', 'content' => '<i class="essb_icon_more"></i>'),
						'dots' => array('title' => '', 'content' => '<i class="essb_icon_more_dots"></i>'),
						'share' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
						'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
						'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
						'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
						'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
				);
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Share Button Icon', 'essb'));
				ESSBOptionsFramework::draw_toggle_field('managestyle_share_button_icon', $select_values, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');

				$more_options = array ("" => "Default from settings (like other share buttons)", "icon" => "Icon only", "button" => "Button", "text" => "Text only" );
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Share Button Style', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_share_button_style', $more_options, false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');

				$share_counter_pos = array("hidden" => "No counter", "inside" => "Inside button without text", "insidename" => "Inside button after text", "insidebeforename" => "Inside button before text", "topn" => "Top", "bottom" => "Bottom");
				ESSBOptionsFramework::draw_settings_panel_start(esc_html__('Share Button Counter', 'essb'));
				ESSBOptionsFramework::draw_select_field('managestyle_share_button_counter', $share_counter_pos, false, 'manage_style');
				ESSBOptionsFramework::draw_settings_panel_end('', '');

				ESSBOptionsFramework::draw_holder_end();
				?>
			</div>

			<div class="inner-content-title">Additional Code Before/After Share Buttons</div>
			<div class="inner-content">
				<?php
				ESSBOptionsFramework::draw_options_row_start(esc_html__('Use Custom Code', 'essb'), '', '', '8');
				ESSBOptionsFramework::draw_switch_field('managestyle_code', 'manage_style');
				ESSBOptionsFramework::draw_options_row_end();

				ESSBOptionsFramework::draw_options_row_start(esc_html__('Custon Code Above', 'essb'), '', '', '');
				ESSBOptionsFramework::draw_textarea_field('managestyle_code_before', 'manage_style');
				ESSBOptionsFramework::draw_options_row_end();

				ESSBOptionsFramework::draw_options_row_start(esc_html__('Custon Code Below', 'essb'), '', '', '');
				ESSBOptionsFramework::draw_textarea_field('managestyle_code_after', 'manage_style');
				ESSBOptionsFramework::draw_options_row_end();
				?>
			</div>
		</div>

		<!-- begin: Location choose for style apply -->
		<div id="style-location-choose" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-apply style-btn inline"><i class="ti-check"></i><span>Apply on Selection</span></a>
				<a href="#" class="manage-back style-btn inline"><i class="ti-close"></i><span>Cancel</span></a>
			</div>
			<div class="title-func">Apply Style on Selected Positions</div>

			<div class="inner-content-title">Content Positions</div>
			<div class="inner-content">
				<?php
				essb_component_multi_position_select(essb_stylemanager_inject_site(essb_stylemanager_clear_positions(essb5_available_content_positions(true))), 'managestyle_content_positions', 'manage_style');
				?>
			</div>

			<div class="inner-content-title">Sitewide Positions</div>
			<div class="inner-content">
				<?php
				essb_component_multi_position_select(essb_stylemanager_clear_positions(essb5_available_button_positions(true)), 'managestyle_button_positions', 'manage_style');
				?>
			</div>
		</div>

		<!-- begin: Style Live Preview  -->
		<div id="style-preview-real" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-apply-select style-btn inline"><i class="ti-check"></i><span>Apply This Style</span></a>
				<a href="#" class="manage-back style-btn inline"><i class="ti-close"></i><span>Cancel</span></a>
			</div>
			<div class="title-func" id="style-preview-title">Apply Style on Selected Positions</div>

			<!-- hidden style preview values  -->
			<div style="display: none;" id="style-preview-real-content">
				<input type="text" id="essb_field_managepreview_template" />
				<input type="text" id="essb_field_managepreview_button_style" />
				<input type="text" id="essb_options_managepreview_button_size" />
				<input type="text" id="essb_options_managepreview_button_pos" />
				<input type="checkbox" id="essb_field_managepreview_nospace" />
				<input type="checkbox" id="essb_field_managepreview_show_counter" />
				<input type="text" id="essb_field_managepreview_counter_pos" />
				<input type="text" id="essb_field_managepreview_total_counter_pos" />
				<input type="text" id="essb_options_managepreview_button_width" />
				<input type="text" id="essb_field_managepreview_css_animations" />
				<input type="text" id="essb_options_managepreview_fixed_width_value" />
				<input type="text" id="essb_options_managepreview_fixed_width_align" />
				<input type="text" id="essb_options_managepreview_fullwidth_share_buttons_columns" />
				<input type="text" id="essb_options_managepreview_fullwidth_share_buttons_columns_align" />
				<input type="text" id="essb_options_managepreview_fullwidth_share_buttons_correction" />
				<input type="text" id="essb_options_managepreview_fullwidth_align" />
				<input type="text" id="essb_options_managepreview_fullwidth_first_button" />
				<input type="text" id="essb_options_managepreview_fullwidth_second_button" />
				<input type="text" id="essb_options_managepreview_flex_width_align" />
				<input type="text" id="essb_options_managepreview_flex_width_value" />
				<input type="text" id="essb_options_managepreview_code_before" />
				<input type="text" id="essb_options_managepreview_code_after" />
			</div>


			<div class="inner-content-title">Real Time Preview</div>
			<div class="inner-content">
				<div class="inner-content-title">Change Button Orientation</div>
				<div class="orientation-change">
					<div class="essb-component-toggleselect essb-component-change-orientation">
						<input type="hidden" name="" id="essb_options_change-orientation" value="" class="toggleselect-holder">
						<span class="toggleselect-item active" data-value="" title="Horizontal"><i class="ti-arrows-horizontal"></i></span>
						<span class="toggleselect-item" data-value="vertical" title="Vertical"><i class="ti-arrows-vertical"></i></span>
					</div>
				</div>
				<div class="hint">
					<?php esc_html_e('The style preview may be different from actual preview on site. For a best result you can apply this on a location and test the front-end display of site.', 'essb'); ?>
				</div>
				<?php
				$position = 'managepreview';

				$code = '<div class="essb-component-buttons-livepreview essb-style-livepreview" data-settings="essb_'.$position.'_global_preview">';
				$code .= '</div>';

				$code .= "<script type=\"text/javascript\">

				var essb_".$position."_global_preview = {
				'networks': [ {'key': 'facebook', 'name': 'Facebook'}, {'key': 'twitter', 'name': 'Twitter'}, {'key': 'google', 'name': 'Google'}, {'key': 'pinterest', 'name': 'Pinterest'}, {'key': 'linkedin', 'name': 'LinkedIn'}],
				'template': 'essb_field_".$position."_template',
				'button_style': 'essb_field_".$position."_button_style',
				'button_size': 'essb_options_".$position."_button_size',
				'align': 'essb_options_".$position."_button_pos',
				'nospace': 'essb_field_".$position."_nospace',
				'counter': 'essb_field_".$position."_show_counter',
				'counter_pos': 'essb_field_".$position."_counter_pos',
				'total_counter_pos': 'essb_field_".$position."_total_counter_pos',
				'width': 'essb_options_".$position."_button_width',
				'animation': 'essb_field_".$position."_css_animations',
				'fixed_width': 'essb_options_".$position."_fixed_width_value',
				'fixed_align': 'essb_options_".$position."_fixed_width_align',
				'columns_count': 'essb_options_".$position."_fullwidth_share_buttons_columns',
				'columns_align': 'essb_options_".$position."_fullwidth_share_buttons_columns_align',
				'full_button': 'essb_options_".$position."_fullwidth_share_buttons_correction',
				'full_align': 'essb_options_".$position."_fullwidth_align',
				'full_first': 'essb_options_".$position."_fullwidth_first_button',
				'full_second': 'essb_options_".$position."_fullwidth_second_button',
				'flex_align': 'essb_options_".$position."_flex_width_align',
				'flex_width': 'essb_options_".$position."_flex_width_value',
				'flex_button': 'essb_options_".$position."_flex_button_value',
				'code_before': 'essb_options_".$position."_code_before',
				'code_after': 'essb_options_".$position."_code_after'
				};

				</script>";

				echo $code;
				?>
			</div>


		</div>
    <!-- end: Style preview -->

		<!-- begin Style Import -->
		<div id="style-export" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-back style-btn inline"><i class="ti-close"></i><span>Back</span></a>
			</div>
			<div class="title-func" id="style-preview-title"><?php esc_html_e('Export Style', 'essb'); ?></div>

			<div class="inner-content-title">Style Content</div>
			<div class="inner-content">
				<div class="inner-content-title">Copy generated code to import field in the style library in order to move that style from one site to another</div>
				<textarea id="export-style-content" style="width:100%; height: 350px;"></textarea>
			</div>
		</div>
		<!-- end: Style Import -->

		<!-- begin Importing style -->
		<div id="style-import" style="display: none;">
			<div class="float-right">
				<a href="#" class="manage-import-style style-btn inline"><i class="ti-check"></i><span>Import Style</span></a>
				<a href="#" class="manage-back style-btn inline"><i class="ti-close"></i><span>Back</span></a>
			</div>
			<div class="title-func" id="style-preview-title"><?php esc_html_e('Import Style', 'essb'); ?></div>

			<div class="inner-content-title">Style Content</div>
			<div class="inner-content">
				<div class="inner-content-title">Paste Generated Code for Style to Import in The Library</div>
				<textarea id="import-style-content"></textarea>
		</div>
		</div>
		<!-- end importing style -->
	</div> <!--end: content -->
</div> <!-- end: modal -->
<div id="styles-preloader">
  <div id="styles-loader"></div>
</div>
