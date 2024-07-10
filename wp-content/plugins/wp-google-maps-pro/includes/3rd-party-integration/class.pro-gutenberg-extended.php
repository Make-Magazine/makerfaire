<?php

namespace WPGMZA\Integration;

if(!class_exists('WPGMZA\\Integration\\GutenbergExtended'))
	return;

class ProGutenbergExtended extends GutenbergExtended {
	/**
	 * Constructor
	*/
	public function __construct(){
		GutenbergExtended::__construct();
	}

	/**
     * Prepare blocks
     * 
     * @return void
    */
    public function prepareBlocks(){
    	GutenbergExtended::prepareBlocks();

    	$base = rtrim(WPGMZA_PRO_DIR_URL, '/');
        $basePath = rtrim(WPGMZA_PRO_DIR_PATH, '/');

        $this->prepareBlock('category-legends', $base, $basePath);
        $this->prepareBlock('category-filter', $base, $basePath);
        $this->prepareBlock('infowindow', $base, $basePath);
        $this->prepareBlock('directions', $base, $basePath);
        $this->prepareBlock('marker-listing', $base, $basePath);
    }

	/**
     * On block assets enqueue delegate for Pro
     * 
     * This method calls the base, and then extends shortcodes that have Pro features
     * 
     * @return void
    */
    public function onEnqueueBlockAssets(){
        GutenbergExtended::onEnqueueBlockAssets();

        if(!is_admin()){
			return;
		}

        $versionString = $this->getVersion();
        
        $basePath = rtrim(WPGMZA_PRO_DIR_PATH, '/');
        $baseUrl = rtrim(WPGMZA_PRO_DIR_URL, '/');

        foreach($this->blocks as $block){
            if(!empty($block->slug)){
            	$proModule = "pro-{$block->slug}";
            	$extendPath = "/js/v8/3rd-party-integration/gutenberg/blocks/{$proModule}/block.js";

            	if(file_exists($basePath . $extendPath)){
	                /* 
	                 * This block has a pro extension to be loaded
					 *
	                 * Don't confuse this, this is not a Pro block, but rather a pro extension of a basic block. This is a key difference as outright pro blocks are
	                 * loaded independently via the prepare methods
	                 *
	                 * This simply loads some JS to extend a basic block further 
	                */

                    $blockAssets = array(
                        "wp-blocks", 
                        "wp-i18n",
                        "wpgmza",
                        "wpgmza-gutenberg-{$block->slug}"
                    );

                    if(!wp_script_is('wp-edit-widgets') && !wp_script_is('wp-customize-widgets')){
                        $blockAssets[] = "wp-editor";
                    }

	                wp_enqueue_script(
	                    "wpgmza-pro-gutenberg-{$block->slug}", 
	                    $baseUrl . $extendPath, 
	                    $blockAssets,
	                    $versionString
	                );
            	}


            }
        }
    }

    /**
     * Specifically render the category legends
     * 
     * @param array $attr
     * 
     * @return string
    */
    public function onRenderCategoryLegends($attr){
        return $this->onRender(\WPGMZA\Shortcodes::SLUG . "_" . \WPGMZA\ProShortcodes::CATEGORY_LEGENDS, $attr);
    }

    /**
     * Specifically render the category filters
     * 
     * @param array $attr
     * 
     * @return string
    */
    public function onRenderCategoryFilter($attr){
        return $this->onRender(\WPGMZA\Shortcodes::SLUG . "_" . \WPGMZA\ProShortcodes::CATEGORY_FILTER, $attr);
    }

    /**
     * Specifically render the infowindow
     * 
     * @param array $attr
     * 
     * @return string
    */
    public function onRenderInfowindow($attr){
        return $this->onRender(\WPGMZA\Shortcodes::SLUG . "_" . \WPGMZA\ProShortcodes::INFOWINDOW, $attr);
    }

    /**
     * Specifically render the directions
     * 
     * @param array $attr
     * 
     * @return string
    */
    public function onRenderDirections($attr){
        return $this->onRender(\WPGMZA\Shortcodes::SLUG . "_" . \WPGMZA\ProShortcodes::DIRECTIONS, $attr);
    }

    /**
     * Specifically render the marker listings 
     * 
     * @param array $attr 
     * 
     * @return string
    */
    public function onRenderMarkerListing($attr){
        return $this->onRender(\WPGMZA\Shortcodes::SLUG . "_" . \WPGMZA\ProShortcodes::MARKER_LISTING, $attr);
    }
}


add_filter('wpgmza_create_WPGMZA\\Integration\\GutenbergExtended', function($input) {
	return new ProGutenbergExtended();
}, 10, 1);