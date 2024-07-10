<?php

namespace WPGMZA;

wpgmza_require_once(wpgmza_gold_get_basic_dir() . 'includes/class.factory.php');
wpgmza_require_once(wpgmza_gold_get_basic_dir() . 'includes/class.dom-document.php');

wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.gold-database.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.gold-script-loader.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.gold-rest-api.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.marker-rating.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'class.marker-rating-widget.php');

wpgmza_require_once(plugin_dir_path(__FILE__) . 'cache/interface.generic-cache.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'cache/class.cache.php');
wpgmza_require_once(plugin_dir_path(__FILE__) . 'cache/class.marker-cache.php');


#[\AllowDynamicProperties]
class GoldAddOn
{
	protected $scriptLoader;
	
	public function __construct()
	{
		global $wpgmza;
		
		$this->database = new GoldDatabase();
		$this->liveTracker = new LiveTracker();
		$this->restAPI = new GoldRestAPI();
		$this->cache = new Cache();
		
		add_action('wpgmza_enqueue_scripts', array($this, 'onPluginLoadScripts'));
		add_action('wpgmza_plugin_get_localized_data', array($this, 'onPluginGetLocalizedData'));
	}
	
	public function onPluginLoadScripts()
	{
		global $wpgmza;
		
		$self = $this;
		
		if(!$this->scriptLoader)
			$this->scriptLoader = new GoldScriptLoader();
		
		if($wpgmza->isInDeveloperMode())
			$this->scriptLoader->build();
		
		if(Plugin::$enqueueScriptsFired)
		{
			$this->scriptLoader->enqueueScripts();
			$this->scriptLoader->enqueueStyles();
		}
		else
		{
			foreach(Plugin::$enqueueScriptActions as $action)
			{
				add_action($action, function() use ($self) {
					$self->scriptLoader->enqueueScripts();
					$self->scriptLoader->enqueueStyles();
				});
			}
		}
	}
	
	public function onPluginGetLocalizedData($data)
	{
		$data['gold_version'] = $this->getVersion();
		return $data;
	}
	
	public function getVersion()
	{
		global $wpgmza_gold_version;
		return $wpgmza_gold_version;
	}

	/**
	 * Atlas Novus Shim 
	 * 
	 * Temporary filter-like method allowing the system to adapt to V9.0.0, specifically Atlas Novus
	 * 
	 * Consider it a shim while we develop V6.0.0 of Gold 
	 * 
	 * @return bool
	 */
	public function checkCoreAtlasNovus(){
		global $wpgmza;
		if(!empty($wpgmza)){
			if(class_exists("WPGMZA\\InternalEngine") && !$wpgmza->internalEngine->isLegacy()){
				return true;
			}
		}
		
		return false;
	}
}

add_action('plugins_loaded', function() {

	if(class_exists('WPGMZA\\Crud'))
	{
		require_once(plugin_dir_path(__FILE__) . 'class.live-tracker.php');
		require_once(plugin_dir_path(__FILE__) . 'class.live-tracking-device.php');
	}
	
	global $wpgmza;
	
	if(!$wpgmza)
	{
		add_action('admin_notices', function() {
			
			?>
			<div class='notice notice-error'>
				<p>
					<?php
					_e('<strong>WP Go Maps Gold add-on:</strong> We did not detect a compatible version of WP Go Maps running on this installation. Please ensure you have installed the latest version of WP Go Maps in order to use the Gold add-on.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
			
		});
		
		return;
	}

	if(!$wpgmza->isProVersion())
	{
		add_action('admin_notices', function() {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Go Maps - Gold add-on:</strong> This plugin requires WP Go Maps - Pro add-on. We did not detect the Pro add-on running on this installation. Please ensure you have installed and activated the Pro add-on in order to use the Gold add-on.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
		});
		
		return;
	}
	
	$wpgmza->goldAddOn = new GoldAddOn();
	
}, 12, 0);