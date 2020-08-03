<?php
/**
 * Import/Export Plugin Setting
 *
 * @package EasySocialShareButtons
 * @since 1.0
 */

if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_sidebar_section_menu('import', 'backup', esc_html__('Export Settings', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('import', 'backupimport', esc_html__('Import Settings', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('import', 'reset', esc_html__('Reset Settings & Data', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('import', 'rollback', esc_html__('Rollback Settings', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('import', 'backup2', esc_html__('Export Followers Settings', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('import', 'backupimport2', esc_html__('Import Followers Settings', 'essb'));
}

ESSBOptionsStructureHelper::menu_item('import', 'backup', esc_html__('Export Settings', 'essb'), 'download');
ESSBOptionsStructureHelper::menu_item('import', 'backupimport', esc_html__('Import Settings', 'essb'), 'upload');
ESSBOptionsStructureHelper::menu_item('import', 'reset', esc_html__('Reset Settings & Data', 'essb'), 'upload');
ESSBOptionsStructureHelper::menu_item('import', 'rollback', esc_html__('Rollback Settings', 'essb'), 'upload');

ESSBOptionsStructureHelper::menu_item('import', 'backup2', esc_html__('Export Followers Settings', 'essb'), 'database');
ESSBOptionsStructureHelper::menu_item('import', 'backupimport2', esc_html__('Import Followers Settings', 'essb'), 'database');

ESSBOptionsStructureHelper::help('import', 'import', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/import-export-plugin-settings-reset-plugin-settings-or-data/'));
ESSBOptionsStructureHelper::field_heading('import', 'import', 'heading5', esc_html__('Export Plugin Settings', 'essb'));
ESSBOptionsStructureHelper::help('import', 'backup', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/import-export-plugin-settings-reset-plugin-settings-or-data/'));
ESSBOptionsStructureHelper::field_func('import', 'backup', 'essb3_text_backup', esc_html__('Export plugin settings', 'essb'), '');
ESSBOptionsStructureHelper::field_func('import', 'backup', 'essb3_text_backup1', esc_html__('Save plugin settings to file', 'essb'), '');
ESSBOptionsStructureHelper::field_heading('import', 'backupimport', 'heading5', esc_html__('Import Plugin Settings', 'essb'));
ESSBOptionsStructureHelper::field_func('import', 'backupimport', 'essb3_text_backup_import', esc_html__('Import plugin settings', 'essb'), '');
ESSBOptionsStructureHelper::field_func('import', 'backupimport', 'essb3_text_backup_import1', esc_html__('Import plugin settings from file', 'essb'), '');

ESSBOptionsStructureHelper::field_heading('import', 'backup2', 'heading5', esc_html__('Export Followers Counter Settings', 'essb'));
ESSBOptionsStructureHelper::field_func('import', 'backup2', 'essb3_text_backup2', esc_html__('Export Followers Counter Settings', 'essb'), '');
ESSBOptionsStructureHelper::field_heading('import', 'backupimport2', 'heading5', esc_html__('Import Followers Counter Settings', 'essb'));
ESSBOptionsStructureHelper::field_func('import', 'backupimport2', 'essb3_text_backup_import2', esc_html__('Import Followers Counter Settings', 'essb'), '');

ESSBOptionsStructureHelper::help('import', 'rollback', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/import-export-plugin-settings-reset-plugin-settings-or-data/#Rollback_Settings'));
ESSBOptionsStructureHelper::title('import', 'rollback', esc_html__('Rollback previous configuration', 'essb'), esc_html__('Easy Social Share Buttons for WordPress stores last 10 saved settin configuration. In case you made a miskate in changing option you can easy return back to previous setup.', 'essb'));
ESSBOptionsStructureHelper::field_func('import', 'rollback', 'essb3_text_roolback', '', '');

ESSBOptionsStructureHelper::help('import', 'reset', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/import-export-plugin-settings-reset-plugin-settings-or-data/#Reset_Settings_&_Data'));
ESSBOptionsStructureHelper::field_component('import', 'reset', 'essb5_reset_settings_actions');

function essb3_text_roolback() {
	$history_container = get_option(ESSB5_SETTINGS_ROLLBACK);
	
	if (!is_array($history_container)) {
		$history_container = array();
	}
	
	foreach ($history_container as $key => $data) {
		echo '<div class="essb-history-settings" onclick="essb_restore_settings(\''.$key.'\', \''.date(DATE_RFC822, $key).'\');">';
		echo '<span>'.date(DATE_RFC822, $key).'</span>';
		echo '</div>';
	}
}

function essb3_text_backup() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}

	?>
	
	<textarea id="essb_options_configuration" name="essb_backup[configuration]" class="input-element stretched" rows="15"><?php echo $backup_string; ?></textarea>
	<a href="<?php echo $goback; ?>" class="essb-btn essb-btn-blue">Export Settings</a>
	
	<?php 
}

function essb3_text_backup2() {
	global $essb_socialfans_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import&section=backup2'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	if (!$essb_socialfans_options) {
		$essb_socialfans_options = get_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
	}
	
	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_socialfans_options);
	}
	?>
	
	<textarea id="essb_options_configuration" name="essb_backup[configuration]" class="input-element stretched" rows="15"><?php echo $backup_string; ?></textarea>
	<a href="<?php echo $goback; ?>" class="essb-btn essb-btn-blue">Export Settings</a>
	
	<?php 
}


function essb3_text_backup_import() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}
	?>
	
	<textarea id="essb_options_configuration1" name="essb_backup[configuration1]" class="input-element stretched" rows="15"></textarea>
	<input type="Submit" name="Submit" value="Import Settings" class="essb-btn essb-btn-blue">
		<?php 
}

function essb3_text_backup_import2() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}
	?>
	
	<textarea id="essb_options_configuration1" name="essb_backup2[configuration1]" class="input-element stretched" rows="15"></textarea>
	<input type="Submit" name="Submit" value="Import Settings" class="essb-btn essb-btn-blue">
		<?php 
}


function essb3_text_backup_import1() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}
	?>
	
	<input type="file" name="essb_backup_file" class="essb-btn essb-btn-light"/>
	<input type="Submit" name="Submit" value="Import Settings From File" class="essb-btn essb-btn-orange">
		<?php 
}


function essb3_text_backup1() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}
	$download_settings = "admin-ajax.php?action=essb_settings_save";
	?>
	
	<a href="<?php echo esc_url_raw($download_settings); ?>" class="essb-btn essb-btn-orange">Save Plugin Settings To File</a>&nbsp;
	<?php 
}

function essb5_reset_settings_actions() {
	?>
<div class="advanced-grid col3 advanced-grid-additional">
<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-database"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Plugin Settings', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The function will remove settings created by plugin and restore the default options.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetsettings" data-title="<?php esc_html_e('Plugin Settings', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Settings', 'essb'); ?></a>
	</div>
</div>	

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-heart-o"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Followers Settings', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The function will remove followers counter setup you have made.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetfollowerssettings" data-title="<?php esc_html_e('Followers Settings', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Settings', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-bar-chart"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Analytics Data', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('Remove the internal stored analytics data (if function is active and used).', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetanalytics" data-title="<?php esc_html_e('Analytics Data', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-refresh"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Internal Counters', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The function will remove all stored internal counters and reset values to zero.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetinternal" data-title="<?php esc_html_e('Internal Counters', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-clock-o"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Counters Last Update', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The function will clear the counters update period. This will settle and immediate share counter update for all content of site.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetcounter" data-title="<?php esc_html_e('Counters Last Update', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-cut"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Short URL Cache & Image Cache', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The function will clear the generated cached share images and it will also clear the cached short URLs (if function used).', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetimage" data-title="<?php esc_html_e('Counters Last Update', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-spinner"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('All Counter Information', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('This function will remove all stored from plugin counter-information (internal and official). It will also clear the share counter update period.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="resetcounterall" data-title="<?php esc_html_e('All Counter Information', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-envelope"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Remove Custom Subscribe Form Designs', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('This will remove any user based designs for subscribe forms you have made. It will not touch the integrated designs and their customizations.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="removeforms" data-title="<?php esc_html_e('Remove Custom Subscribe Form Designs', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-heart"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Reset Love Button Counter', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('The button will remove the values of the Love this counter.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="removelove" data-title="<?php esc_html_e('Remove Love This Counter', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>

<div class="advancedoptions-tile advancedoptions-smalltile center-c">
	<div class="advnacedoptions-tile-icon">
		<i class="fa fa-instagram"></i>
	</div>
	<div class="advnacedoptions-tile-subtitle">
		<h3><?php esc_html_e('Remove Instagram Cached Data', 'essb'); ?></h3>
	</div>
	<div class="advancedoptions-tile-body">
		<?php esc_html_e('Remove all stored data for Instagram feeds. This will force all feeds used on the website to make an immediate update.', 'essb'); ?>
	</div>
	<div class="advancedoptions-tile-foot">
		<a href="#" class="essb-btn tile-deactivate essb-reset-settings" data-clear="instagramtransients" data-title="<?php esc_html_e('Clear Instagram Data', 'essb'); ?>"><i class="fa fa-times"></i><?php esc_html_e('Reset Data', 'essb'); ?></a>
	</div>
</div>
	
</div>
	<?php 
}