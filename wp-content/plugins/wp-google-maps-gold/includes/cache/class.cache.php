<?php

namespace WPGMZA;

class Cache {

    public $caches;
    public $loaded;

    public $notices;

    private $_isClearing;

    public function __construct(){
        $this->loaded = false;
        $this->_isClearing = false;

        $this->caches = (object) array(
            'markers' => new MarkerCache()
        );

        $this->notices = array();

        $this->hook();
    }

    /**
     * Hook to core
     * 
     * @return void
     */
    public function hook(){
        global $wpgmza;

        add_filter('wpgmza_global_settings_tabs', array($this, 'globalSettingsTabs'));
		add_filter('wpgmza_global_settings_tab_content', array($this, 'globalSettingsTabContent'));


        if(!empty($wpgmza->settings->enable_caching)){
            add_action('admin_init', array($this, 'process'));

            /* Hook into core to auto-clear the cache when appropriate (For now, only when you save your map, or manual) */
            add_action('admin_post_wpgmza_save_map', array($this, 'clear'), 99);

            add_action("wpgmza_script_loader_localize_data_complete", array($this, 'localize'));
            add_action("wpgmza_script_loader_enqueue_custom_js", array($this, 'javascript'));

            add_action('admin_bar_menu', array($this, 'adminBar'), 99);
            add_action('admin_notices', array($this, 'renderNotices'));
        }
    }


    /**
     * Add tabs to the global settings area for managing the caching system
     * 
     * @param string $content The current content
     * 
     * @return string
     */
    public function globalSettingsTabs($content){
		global $wpgmza;
		$style = "style='margin-right: 3px;'";
		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
			$style = "";
		}
		return $content . "<li {$style}><a href=\"#tabs-caching\">" . __("Cache Settings","wp-google-maps") . "</a></li>";
	}

    /**
     * Add the tab content to the global settings area for managing the settings
     * 
     * @param string $content The current additional tabs 
     * 
     * @return string
     */
    public function globalSettingsTabContent($content){
		require_once(plugin_dir_path(__FILE__) . 'class.cache-settings-panel.php');
		
		$document = new CacheSettingsPanel();
		return $content . "<div id='tabs-caching'>" . $document->html . "</div>";
	}



    /**
     * Add a custom admin bar for map caching
     * 
     * @return void
     */
    public function adminBar($bar){
        $bar->add_node(
            array(
                'id' => 'wpgmza-cache-parent',
                'title' => '<span class="ab-icon dashicons dashicons-location" style="position: relative; top: 3px;"></span>' . __("Map Caching", "wp-google-maps"),
                'href' =>  admin_url('admin.php?page=wp-google-maps-menu-settings#tabs-caching'),
            )
        );

        $bar->add_node(
            array(
                'id' => 'wpgmza-cache-settings',
                'parent' => 'wpgmza-cache-parent',
                'title' => __("Settings", "wp-google-maps"),
                'href' =>  admin_url('admin.php?page=wp-google-maps-menu-settings#tabs-caching'),
            )
        );

        $bar->add_node(
            array(
                'id' => 'wpgmza-cache-clear',
                'parent' => 'wpgmza-cache-parent',
                'title' => __("Clear Cache", "wp-google-maps"),
                'href' =>  wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=clear#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'),
            )
        );

        $bar->add_node(
            array(
                'id' => 'wpgmza-cache-preload',
                'parent' => 'wpgmza-cache-parent',
                'title' => __("Preload Cache", "wp-google-maps"),
                'href' =>  wp_nonce_url(admin_url('admin.php?page=wp-google-maps-menu-settings&_wpmgza_cache_action=preload#tabs-caching'), 'wpgmza_caching', 'wpgmza_caching'),
            )
        );
    }

    /**
     * Process any user actions on a global level
     * 
     * @return void
     */
    public function process(){
        if(!empty($_GET) && !empty($_GET['wpgmza_caching']) && !empty($_GET['_wpmgza_cache_action'])){
            if(wp_verify_nonce($_GET['wpgmza_caching'], 'wpgmza_caching')){
                switch($_GET['_wpmgza_cache_action']){
                    case 'clear':
                        $this->clear();
                        $this->notice("Map cache cleared", "Your cache will automatically be regenerated the next time a map is loaded.");
                        break;
                    case 'preload':
                        $this->clear();
                        $this->preload();
                        $this->notice("Map cache preloaded", "Your cache was cleared and regenerated with the latest data.");
                        break;
                    case 'view':
                        $data = array();

                        foreach($this->caches as $key => $instance){
                            $data[$key] = $instance->localize();
                        }

                        $info = !empty($data) ? json_encode($data, JSON_PRETTY_PRINT) : 'Nothing cached...';
                        $this->notice("Map cached data", "<textarea style='min-height: 300px; max-height: 300px; width: 100%; outline: none; border: none; background: #333; color: #fff' disabled>{$info}</textarea>", 'info');
                        break;
                }
            }
        }
    }

    /**
     * Clear all caches
     * 
     * @return void
     */
    public function clear(){
        $this->_isClearing = true;
        foreach($this->caches as $instance){
            $instance->clear();
        }
    }

    /**
     * Preload all caches 
     * 
     * @return void
     */
    public function preload(){
        foreach($this->caches as $instance){
            $instance->preload();
        }
    }

    /**
     * Enqueue scripts 
     * 
     * @return void
     */
    public function javascript(){
        $scriptArgs = apply_filters('wpgmza-get-scripts-arguments', array());

        /* We only do it this way for beta. In the future, it would be included in the combined/min files with a conditional, and timing in place */
		wp_enqueue_script('wpgmza-cache-fetcher', WPGMZA_GOLD_DIR_URL . 'js/v8/caching/fetcher.js', false, false, $scriptArgs);
    }

    /**
     * Localize the data
     * 
     * @return void
     */
    public function localize(){
        if(!is_admin() && empty($this->loaded) && empty($this->_isClearing)){
            $data = array();

            foreach($this->caches as $key => $instance){
                $data[$key] = $instance->localize();
            }

            wp_localize_script('wpgmza', 'WPGMZA_CACHE', $data);
            $this->loaded = true;
        }
    }

    /**
     * Prepare a report of the cache data
     * 
     * @return array
     */
    public function report(){
        global $wpgmza;

        $data = array();
        
        if(!empty($wpgmza->settings->enable_caching)){
            foreach($this->caches as $key => $instance){
                $data[$key] = $instance->report();
            }
        }

        return $data;
    }

    /**
     * Show an admin notice
     * 
     * @param string $title The title to show
     * @param string $content The content to show
     * @param string $type The type of notice 
     * 
     * @return void
     */
    public function notice($title, $content = false, $type = 'success'){
        $notice = array();
        $notice[] = "<div class='notice notice-{$type}' style='padding: 10px;'>";

        $notice[] = "<h4 style='margin-top:0; margin-bottom: 0;'>{$title}</h4>";
        if(!empty($content)){
            $notice[] = "<div style='margin-top: 5px'>{$content}</div>";
        }

        $notice[] = "</div>";
        
        $this->notices[] = implode("", $notice);
    }

    /**
     * Render notices loaded into the notice object during runtime 
     * 
     * @return void
    */
    public function renderNotices(){
        if(!empty($this->notices)){
            foreach($this->notices as $html){
                echo $html;
            }
        }
    }
}