<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Vars {
	public function version() { return SWVER; }

	public function plugin_dir() { return SBSW_PLUGIN_DIR; }

	public function plugin_url() { return SBSW_PLUGIN_URL; }

	public function plugin_basename() { return SBSW_PLUGIN_BASENAME; }

	public function cron_update_cache_time() { return SBSW_CRON_UPDATE_CACHE_TIME; }

	public function max_records() { return SBSW_MAX_RECORDS; }

	public function text_domain() { return SBSW_TEXT_DOMAIN; }

	public function slug() { return SBSW_SLUG; }

	public function plugin_name() { return SBSW_PLUGIN_NAME; }

	public function setup_url() { return SBSW_SETUP_URL; }

	public function support_url() { return SBSW_SUPPORT_URL; }
}