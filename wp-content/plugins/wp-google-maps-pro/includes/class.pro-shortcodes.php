<?php 
namespace WPGMZA;

class ProShortcodes extends Shortcodes{
	const CATEGORY_FILTER = "category_filter";
	const CATEGORY_LEGENDS = "category_legends";
	const INFOWINDOW = "infowindow";
	const MARKER_LISTING = "marker_listing";
	const DIRECTIONS = "directions";

	public function __construct($internalEngine){
		Shortcodes::__construct($internalEngine);

	}

	/**
	 * Interal hoo for shortcode regitration
	 *
	 * @return void
	*/
	public function register(){
		parent::register();

		add_shortcode(self::SLUG . "_" . self::CATEGORY_FILTER, array($this, "categoryFilter"));
		add_shortcode(self::SLUG . "_" . self::CATEGORY_LEGENDS, array($this, "categoryLegends"));
		add_shortcode(self::SLUG . "_" . self::INFOWINDOW, array($this, "infowindow"));
		add_shortcode(self::SLUG . "_" . self::MARKER_LISTING, array($this, "markerListing"));
		add_shortcode(self::SLUG . "_" . self::DIRECTIONS, array($this, "directions"));
	}

	/**
	 * Internal hook for actions before returning content for a shortcode
	 * 
	 * @param string $slug The shortcode slug which triggered this event
	 * 
	 * @return void
	*/
	public function beforeOutput($slug){
		global $wpgmza;

		parent::beforeOutput($slug);

		$version_string = $wpgmza->getBasicVersion();
		if(method_exists($wpgmza, 'getProVersion')){
			$version_string .= '+pro-' . $wpgmza->getProVersion();
		}

		wp_enqueue_style("wpgmza-ui-components-pro", WPGMZA_PRO_DIR_URL . "css/atlas-novus/components.css", array(), $version_string);
	}

	/**
	 * Gets the default attributes for a specific shortcode
	 * 
	 * @return array
	*/
	public function getDefaults($slug){
		$attributes = parent::getDefaults($slug);
		switch($slug){
			case self::SLUG:
				$attributes = array_merge($attributes, 
					array(
				        'mashup' => false,
				        'mashup_ids' => false,
				        'cat' => false,
				        'type' => 'default',
				        'parent_id' => false,
				        'lat' => false,
				        'lng' => false,
				        'mark_center' => false,
				        'enable_category' => false,
				        'directions_from' => false,
				        'directions_to' => false,
				        'directions_waypoints' => false,
				        'directions_auto' => false,
						'disable_vgm_form' => false,
						'redirect_to' => false
					)
				);
				break;
			case self::SLUG . "_" . self::CATEGORY_FILTER:
				$attributes = array_merge($attributes, 
					array(
						"id" => "1"
					)
				);

				/* Only element which can't be custom classed */
				if(isset($attributes['classname'])){
					unset($attributes['classname']);
				}
				break;
			case self::SLUG . "_" . self::CATEGORY_LEGENDS:
				$attributes = array_merge($attributes, 
					array(
						"id" => "1"
					)
				);
				break;
			case self::SLUG . "_" . self::INFOWINDOW:
				$attributes = array_merge($attributes, 
					array(
						"id" => "1",
						"hide_title" => false,
						"hide_address" => false,
						"hide_description" => false,
						"hide_category" => false,
						"hide_links" => false,
						"hide_custom_fields" => false,
						"hide_image" => false,
					)
				);
				break;
			case self::SLUG . "_" . self::MARKER_LISTING:
				$attributes = array_merge($attributes, 
					array(
						"id" => "1",
						"style_id" => "1",
						"style_slug" => false,
				        'mashup_ids' => false,
					)
				);
				break;
			case self::SLUG . "_" . self::DIRECTIONS:
				$attributes = array_merge($attributes,
					array(
						"id" => "1",
						"default_from" => false,
						"default_to" => false,
						"auto_run" => false
					)
				);
		}

		return $attributes;
	}

	/**
	 * Adds to the data attributes pushed into the map 
	 * 
	 * @param array $elemAttributes The current input element attributes, before globally filtered
	 * @param object $shortcodeAttributes The current shortcode attributes helpful for logic blocks
	 * 
	 * @return array
	 * 
	*/
	public function filterMapElementAttributes($elemAttributes, $shortcodeAttributes = false){
		if(!empty($shortcodeAttributes)){
			if(!empty($shortcodeAttributes->mashup_ids)){
				$elemAttributes['data-mashup'] = "true";
				$elemAttributes['data-mashup-ids'] = esc_attr($shortcodeAttributes->mashup_ids);
			}

		}

		return $elemAttributes;
	}

	/**
	 * Adds pro components to be output by the shortcode handler
	 * 
	 * @param object $map The map this call applies to, allowing for more scale
	 * 
	 * @return object
	*/
	public function getMapComponents($map){
		global $wpgmza;

		$components = parent::getMapComponents($map);

		$anchors = UI\ComponentAnchorControl::getAnchors();
	    $anchorMap = array_flip($anchors);

	    $attributes = (object) $map->shortcodeAttributes;

		if((!empty(intval($map->filterbycat)) || !empty($attributes->enable_category)) && $map->categoryFilterWidget){
			if(isset($attributes->enable_category) && is_string($attributes->enable_category) && intval($attributes->enable_category) === 0){
				/* Legacy support for enable_category="0" - To disable */
				/* We don't do anything, but we have this block for historical */
			} else {
				$html = $this->categoryFilterComponent($map);

				if($map->category_filter_component_anchor == UI\ComponentAnchorControl::ABOVE){
					$components->before[] = $html;
				} else if ($map->category_filter_component_anchor == UI\ComponentAnchorControl::BELOW){
					$components->after[] = $html;
				} else {
					/* Inside container */
					if(array_key_exists($map->category_filter_component_anchor, $anchorMap)){
						$anchor = strtolower($anchorMap[$map->category_filter_component_anchor]);
						if(array_key_exists($anchor, $components->inside)){
							$components->inside[$anchor][] = $html; 
						}
					}

				}
			}
		}

		/* This is not DRY as it repeats the code block above almost identically, so maybe it's time for a helper method? I dunno */
		if(!empty($map->category_legends_enabled) && $map->categoryLegends){
			$html = $map->categoryLegends->html;
			if(!empty($html)){
				if($map->category_legends_component_anchor == UI\ComponentAnchorControl::ABOVE){
					$components->before[] = $html;
				} else if ($map->category_legends_component_anchor == UI\ComponentAnchorControl::BELOW){
					$components->after[] = $html;
				} else {
					/* Inside container */
					if(array_key_exists($map->category_legends_component_anchor, $anchorMap)){
						$anchor = strtolower($anchorMap[$map->category_legends_component_anchor]);
						if(array_key_exists($anchor, $components->inside)){
							$components->inside[$anchor][] = $html;
						}
					}
				}
			}
		}

		/* Developer Hook (Filter) - Modify modern panel structure, atlas novus only */
		$panels = (object) apply_filters("wpgmza_map_panel_groups", 
			array(
				"left" => array(),
				"right" => array()
			)
		);

		$markerListingStyle = !empty(intval($map->wpgmza_listmarkers_by)) ? intval($map->wpgmza_listmarkers_by) : false;

		if(!empty($markerListingStyle) && empty($map->marker_listing_component_anchor)){
			/* This map may have been setup in the legacy option, it's definitely enabled, to let's try and migrate their old setup to Atlas Novus */
			/* This is considered a shim for the legacy users who 'try' atlas novus -> Should be safe */
			if(isset($map->wpgmza_marker_listing_position)){
				if(!empty($map->wpgmza_marker_listing_position)){
					$map->marker_listing_component_anchor = UI\ComponentAnchorControl::ABOVE;
				} else {
					$map->marker_listing_component_anchor = UI\ComponentAnchorControl::BELOW;
				}
			}
		}

		if(!empty($markerListingStyle) && !empty($map->marker_listing_component_anchor)){
			$listingParams = array(
				'map_id' => $map->id
			);

			if(!empty($attributes->mashup_ids)){
				$listingParams['mashup_ids'] = esc_attr($attributes->mashup_ids);
			}

			$listing = MarkerListing::createInstanceFromStyle($markerListingStyle, $map->id);
			$listing->setAjaxParameters($listingParams);

			$html = $listing->html();

			if($map->marker_listing_component_anchor == UI\ComponentAnchorControl::ABOVE){
	    		$components->before[] = $html;
	    	} else if ($map->marker_listing_component_anchor == UI\ComponentAnchorControl::BELOW){
	    		$components->after[] = $html;
	    	} else {
	    		/* Inside container */
	    		if($markerListingStyle === MarkerListing::STYLE_PANEL){
		    		if ($map->marker_listing_component_anchor == UI\ComponentAnchorControl::LEFT){
		    			$panels->left['listing'] = $html;
		    		} else if ($map->marker_listing_component_anchor == UI\ComponentAnchorControl::RIGHT){
	    				$panels->right['listing'] = $html;
		    		}
		    	} else {
					/* Generic placement of a legacy style */
					/* Still only supports left/right */		    		
		    		if(array_key_exists($map->marker_listing_component_anchor, $anchorMap)){
		    			$anchor = strtolower($anchorMap[$map->marker_listing_component_anchor]);
		    			if(array_key_exists($anchor, $components->inside)){
		    				$wrapperClass = strtolower(basename(get_class($listing)));
		    				$components->inside[$anchor][] = $this->legacyListingAdapterComponent($html, $wrapperClass); 
		    			}
		    		}
	    		}
	    	}
		}

		if($map->isDirectionsEnabled()){
			if($map->directions_box_component_anchor == UI\ComponentAnchorControl::ABOVE){
	    		$components->before[] = $map->directionsBox->html;
	    	} else if ($map->directions_box_component_anchor == UI\ComponentAnchorControl::BELOW){
	    		$components->after[] = $map->directionsBox->html;
	    	} else if ($map->directions_box_component_anchor == UI\ComponentAnchorControl::LEFT){
				$panels->left['directions'] = $map->directionsBox->html;
	    	} else if ($map->directions_box_component_anchor == UI\ComponentAnchorControl::RIGHT){
				$panels->right['directions'] = $map->directionsBox->html;
			}
		}

		/* This should be a constant comparison, but we don't seem to have that available now */
		$infoWindowStyle = intval($map->wpgmza_iw_type);
		if($infoWindowStyle < 0){
			if(!empty($wpgmza->settings->wpgmza_iw_type)){
				/* On global option */
				$infoWindowStyle = intval($wpgmza->settings->wpgmza_iw_type);
			}
		}

		if($infoWindowStyle === 4){
			$infoWindowTemplate = new \WPGMZA\DOMDocument();
			$infoWindowTemplate->loadPHPFile($wpgmza->internalEngine->getTemplate('info-window/panel.html.php', WPGMZA_PRO_DIR_PATH));
			$infoWindowTemplate->querySelector('.wpgmza-panel-info-window')->setAttribute('data-map', $map->id);

			if(!empty($panels->right) && empty($panels->left)){
				$panels->right['infowindow'] = $infoWindowTemplate->html;
			} else {
				$panels->left['infowindow'] = $infoWindowTemplate->html;
			}
		}


		foreach($panels as $key => $elements){
			/* Developer Hook (Filter) - Modify elements for a speciic panel blocks */
			$elements = apply_filters("wpgmza_map_panel_elements_{$key}", $elements);
			if(!empty($elements)){
				if(array_key_exists($key, $components->inside)){
					$components->inside[$key][] = $this->panelComponent($elements);
				}
			}
		}

		return $components;
	}

	/** 
	 * Category Filter Shortcode Handler
	 * 
	 * @param array $attributes The shortcode attributes
	 * 
	 * @return string
	*/
	public function categoryFilter($attributes){
		global $wpgmza;

		$html = "";

		/* Developer Hook (Filter) - Modify default shortcode attributes for category filter shortcode shortcode */
		$defaults = apply_filters("wpgmza_cf_shortcode_get_default_attributes", $this->getDefaults(self::SLUG . "_" . self::CATEGORY_FILTER));
		$attributes = shortcode_atts($defaults, $attributes);

		$attributes = (object) $attributes;
		
		$id = !empty($attributes->id) && !empty(intval($attributes->id)) ? intval($attributes->id) : 1;

		$map = Map::createInstance($id);
		if($map->element !== null && $map->categoryFilterWidget){
			$html = $this->standaloneComponent($this->categoryFilterComponent($map));
		} else {
			$html = __("Error: The map ID", "wp-google-maps") . " (" . $id . ") " . __("does not exist", "wp-google-maps");
		}

		$this->beforeOutput(self::SLUG. "_" . self::CATEGORY_FILTER);

		return $html;
	}

	/**
	 * Category Legends Shortcode Handler
	 * 
	 * @param array $attributes The shortcode attributes
	 * 
	 * @return string
	*/
	public function categoryLegends($attributes){
		global $wpgmza;

		$html = "";

		/* Developer Hook (Filter) - Modify default shortcode attributes for category legends shortcode */
		$defaults = apply_filters("wpgmza_cl_shortcode_get_default_attributes", $this->getDefaults(self::SLUG . "_" . self::CATEGORY_LEGENDS));
		$attributes = shortcode_atts($defaults, $attributes);

		$attributes = (object) $attributes;
		
		$id = !empty($attributes->id) && !empty(intval($attributes->id)) ? intval($attributes->id) : 1;

		$map = Map::createInstance($id);
		if($map->element !== null){
			$categoryLegends = $map->categoryLegends;

			if(!empty($attributes->classname)){
				$categoryLegends->wrapper->addClass( esc_attr($attributes->classname) );
			}

			$html = $this->standaloneComponent($categoryLegends->html);
		} else {
			$html = __("Error: The map ID", "wp-google-maps") . " (" . $id . ") " . __("does not exist", "wp-google-maps");
		}

		$this->beforeOutput(self::SLUG. "_" . self::CATEGORY_LEGENDS);

		return $html;
	}

	/** 
	 * Infowindow Shortcode Handler
	 * 
	 * @param array $attributes The shortcode attributes
	 * 
	 * @return string
	*/
	public function infowindow($attributes){
		global $wpgmza;

		$html = "";

		/* Developer Hook (Filter) - Modify default shortcode attributes for info window shortcode */
		$defaults = apply_filters("wpgmza_iw_shortcode_get_default_attributes", $this->getDefaults(self::SLUG . "_" . self::INFOWINDOW));
		$attributes = shortcode_atts($defaults, $attributes);

		$attributes = (object) $attributes;
		
		$id = !empty($attributes->id) && !empty(intval($attributes->id)) ? intval($attributes->id) : 1;

		$map = Map::createInstance($id);
		if($map->element !== null){
			$infoWindowTemplate = new \WPGMZA\DOMDocument();
			$infoWindowTemplate->loadPHPFile($wpgmza->internalEngine->getTemplate('info-window/panel.html.php', WPGMZA_PRO_DIR_PATH));
			$infoWindowTemplate->querySelector('.wpgmza-panel-info-window')->setAttribute('data-map', $map->id);

			if(!empty($attributes->classname)){
				$infoWindowTemplate->querySelector('.wpgmza-panel-info-window')->addClass( esc_attr($attributes->classname) );
			}

			if(!empty($attributes->hide_title)){
				$infoWindowTemplate->querySelector('.wpgmza-title')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_address)){
				$infoWindowTemplate->querySelector('.wpgmza-address')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_description)){
				$infoWindowTemplate->querySelector('.wpgmza-description')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_category)){
				$infoWindowTemplate->querySelector('.wpgmza-categories')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_links)){
				// $infoWindowTemplate->querySelector('.wpgmza-categories')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_custom_fields)){
				$infoWindowTemplate->querySelector('.wpgmza-custom-fields')->addClass('wpgmza-hidden');
			}

			if(!empty($attributes->hide_image)){
				$infoWindowTemplate->querySelector('.wpgmza-gallery-container')->addClass('wpgmza-hidden');
			}

			/* Hide by default */
			$infoWindowTemplate->querySelector('.wpgmza-panel-info-window')->addClass('wpgmza-hidden');

			$html = $this->standaloneComponent($infoWindowTemplate->html);
		} else {
			$html = __("Error: The map ID", "wp-google-maps") . " (" . $id . ") " . __("does not exist", "wp-google-maps");
		}

		$this->beforeOutput(self::SLUG. "_" . self::INFOWINDOW);

		return $html;
	}

	/** 
	 * Marker Listing Shortcode Handler
	 * 
	 * @param array $attributes The shortcode attributes
	 * 
	 * @return string
	*/
	public function markerListing($attributes){
		global $wpgmza;

		$html = "";

		/* Developer Hook (Filter) - Modify default shortcode attributes for marker listing shortcode */
		$defaults = apply_filters("wpgmza_ml_shortcode_get_default_attributes", $this->getDefaults(self::SLUG . "_" . self::MARKER_LISTING));
		$attributes = shortcode_atts($defaults, $attributes);

		$attributes = (object) $attributes;
		
		$id = !empty($attributes->id) && !empty(intval($attributes->id)) ? intval($attributes->id) : 1;

		$map = Map::createInstance($id);
		if($map->element !== null){
			$style = intval($attributes->style_id);
			
			if(!empty($attributes->style_slug)){
				$slug = str_replace(" ", "-", trim(strtolower($attributes->style_slug)));
				$slug = str_replace("_", "-", $slug);
				switch($slug){
					case "basic-list":
						$style = \WPGMZA\MarkerListing::STYLE_BASIC_LIST;
						break;
					case "basic-table":
						$style = \WPGMZA\MarkerListing::STYLE_BASIC_TABLE;
						break;
					case "advanced-table":
						$style = \WPGMZA\MarkerListing::STYLE_ADVANCED_TABLE;
						break;
					case "carousel":
						$style = \WPGMZA\MarkerListing::STYLE_CAROUSEL;
						break;
					case "grid":
						$style = \WPGMZA\MarkerListing::STYLE_GRID;
						break;
					case "panel":
						$style = \WPGMZA\MarkerListing::STYLE_PANEL;
						break;
				}
			}

			$listingParams = array(
				'map_id' => $map->id
			);

			if(!empty($attributes->mashup_ids)){
				$listingParams['mashup_ids'] = esc_attr($attributes->mashup_ids);
			}

			$listing = MarkerListing::createInstanceFromStyle($style, $map->id);
			$listing->setAjaxParameters($listingParams);

			$listing->element->setAttribute('data-map-id', $map->id);
			$listing->element->setAttribute('data-standalone-listing-style', $style);
			$listing->element->setAttribute('data-map-settings', json_encode($map->getDataSettingsObject()));

			if(!empty($attributes->classname)){
				$listing->element->addClass(  esc_attr($attributes->classname) );
			}

			$html = $this->standaloneComponent($listing->html());
		} else {
			$html = __("Error: The map ID", "wp-google-maps") . " (" . $id . ") " . __("does not exist", "wp-google-maps");
		}

		$this->beforeOutput(self::SLUG. "_" . self::MARKER_LISTING);

		return $html;

	}

	/** 
	 * Directions Shortcode Handler
	 * 
	 * @param array $attributes The shortcode attributes
	 * 
	 * @return string
	*/
	public function directions($attributes){
		global $wpgmza;

		$html = "";

		/* Developer Hook (Filter) - Modify default shortcode attributes for get directions shortcode */
		$defaults = apply_filters("wpgmza_gd_shortcode_get_default_attributes", $this->getDefaults(self::SLUG . "_" . self::DIRECTIONS));
		$attributes = shortcode_atts($defaults, $attributes);

		$attributes = (object) $attributes;
		
		$id = !empty($attributes->id) && !empty(intval($attributes->id)) ? intval($attributes->id) : 1;

		$map = Map::createInstance($id);
		if($map->element !== null){
			$directionsBox = new DirectionsBox($map);

			if(!empty($attributes->classname)){
				$directionsBox->querySelector('.wpgmza-directions-box')->addClass( esc_attr($attributes->classname) );
			}

			if(!empty($attributes->default_from)){
				$directionsBox->querySelector('input.wpgmza-directions-from')->setValue($attributes->default_from);
			}

			if(!empty($attributes->default_to)){
				$directionsBox->querySelector('input.wpgmza-directions-to')->setValue($attributes->default_to);
			}

			if(!empty($attributes->auto_run)){
				$directionsBox->querySelector('.wpgmza-directions-box')->setAttribute('data-auto-run', 'true');
			}

			$html = $this->standaloneComponent($directionsBox->html);
		} else {
			$html = __("Error: The map ID", "wp-google-maps") . " (" . $id . ") " . __("does not exist", "wp-google-maps");
		}

		$this->beforeOutput(self::SLUG. "_" . self::DIRECTIONS);

		return $html;
	}


	/**
	 * Wraps the category filter in style/event related tags
	 * 
	 * This method exists to standardize behaviour between the map shortcode component loop
	 * and the category filter shortcode handler
	 * 
	 * This will also facilitate turning 'filter by' text into a setting soon
	 * 
	 * Note: We could probably move to using DomDocument here, but for now this will do
	 * 
	 * @param Map $map The map instance this relates to 
	 * 
	 * @return string
	*/
	public function categoryFilterComponent($map){
		global $wpgmza;

		$class = "wpgmza-dropdown";
		$style = intval($wpgmza->settings->wpgmza_settings_filterbycat_type); 
		if(!empty($style) && $style === 2){
			$class = "wpgmza-list";
		}

		$html = "<div class='wpgmza-marker-listing-category-filter {$class}' data-map-id='{$map->id}' id='wpgmza_filter_{$map->id}'>";
		$html .= "<label>" . __("Filter by","wp-google-maps") . "</label>";
		
		if($map->categoryFilterWidget){
			$html .= $map->categoryFilterWidget->html;
		}

		$html .= "</div>";

		return $html;
	} 

	/**
	 * Wraps legacy listing styles to be placed within the map anchor points
	 * 
	 * This is purely to support older marker listing styles to be placed within the map easily
	 * 
	 * @param string $html The current HTML of the marker listing
	 * 
	 * @return string
	*/
	public function legacyListingAdapterComponent($html, $wrapper){
		$classlist = array(
			'legacy-listing-adapter'
		);

		$classlist[] = $wrapper;
		
		/* Developer Hook (Filter) - Modify wrapper classlist for listing adapter components */
		$classlist = apply_filters("wpgmza_legacy_listing_adapter_component_classlist", $classlist);
		if(!empty($html) && !empty($classlist)){
			$classlist = implode(" ", $classlist);
			$html = "<div class='{$classlist}'>{$html}</div>";
		}
		return $html;
	}

	/**
	 * Wraps panel elements into groupings 
	 * 
	 * @param array $elements
	 * 
	 * @return string
	*/
	public function panelComponent($elements){
		$html = "";

		/* Developer Hook (Filter) - Modify feature panel dependencies  */
		$featureDependedPanels = apply_filters("wpgmza_panel_component_wrap_feature_dependent_items", array("infowindow"));
		if(!empty($elements)){
			$html .= "<div class='grouping'>";

			foreach($elements as $tag => $content){
				$requiresFeature = in_array($tag, $featureDependedPanels) ? "data-requires-feature='true'" : "";
				$html .= "<div class='grouping-item' data-component='{$tag}' {$requiresFeature}>{$content}</div>";
			}

			$html .= "</div>";
			$html .= "<div class='grouping-handle'><div class='icon'></div></div>";
		}

		return $html;
	}
}

add_filter('wpgmza_create_WPGMZA\\Shortcodes', function($internalEngine) {
	return new ProShortcodes($internalEngine);
}, 10, 1);
