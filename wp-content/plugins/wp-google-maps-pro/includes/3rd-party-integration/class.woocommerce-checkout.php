<?php

namespace WPGMZA\Integration;

class WooCommerceCheckout{
	public function __construct(){
		/* Woo action relays */
		add_action('woocommerce_after_checkout_billing_form', array($this, 'afterBillingForm'));
		add_action('woocommerce_after_checkout_shipping_form', array($this, 'afterShippingForm'));
		add_action('woocommerce_review_order_after_payment', array($this, 'afterOrderForm'));

		add_action('woocommerce_checkout_update_order_meta', array($this, 'saveCheckoutCoordinates'));

		add_action('add_meta_boxes', array($this, 'registerLocationViewerMetabox'));
	}

	/**
	 * Check if the module is enabled
	 * 
	 * Conditions:
	 * - Woo installed
	 * - Setting enabled, and map selected
	 * 
	 * @return bool
	*/
	public function isEnabled(){
		global $wpgmza;

		if(class_exists("WooCommerce")){
			if(!empty($wpgmza->settings->woo_checkout_map_enabled) && !empty($wpgmza->settings->woo_checkout_map_id)){
				return true;
			}
		}
		return false;
	}

	/**
	 * Get normalized woo checkout placement option
	 * 
	 * @return string
	*/
	public function getPlacement(){
		global $wpgmza;
		$placement = 'after_order';
		if(!empty($wpgmza->settings->woo_checkout_map_placement)){
			$placement = $wpgmza->settings->woo_checkout_map_placement;
		}

		return $placement;
	}

	/**
	 * Relay for after billing form
	 * 
	 * @return void
	*/
	public function afterBillingForm(){
		$placement = $this->getPlacement();
		if($placement === 'after_billing'){
			$this->renderCheckoutMap();
		}
	}

	/**
	 * Relay for after shipping form
	 * 
	 * @return void
	*/
	public function afterShippingForm(){
		$placement = $this->getPlacement();
		if($placement === 'after_shipping'){
			$this->renderCheckoutMap();
		}
	}

	/**
	 * Relay for before place order button
	 * 
	 * @return void
	*/
	public function afterOrderForm(){
		$placement = $this->getPlacement();
		if($placement === 'after_order'){
			$this->renderCheckoutMap();
		}
	}

	/**
	 * Renders the checkout map, and appends some local data to trigger the woo integration scripts
	 * 
	 * @return void
	*/
	public function renderCheckoutMap(){
		global $wpgmza;
		if($this->isEnabled()){
			$mapId = !empty($wpgmza->settings->woo_checkout_map_id) ? intval($wpgmza->settings->woo_checkout_map_id) : 1;
			echo "<div class='wpgmza-woo-checkout-map-wrapper'>";

			/* Developer Hook (Filter) - Modify HTML before checkout map */
			echo apply_filters("wpgmza_woo_checkout_map_above", "");
			
			echo do_shortcode("[wpgmza id='{$mapId}']");

			/* Developer Hook (Filter) - Modify HTML after checkout map */
			echo apply_filters("wpgmza_woo_checkout_map_below", "");

			/* Developer Hook (Filter) - Modify checkout map hint */
			echo "<small class='wpgmza-woo-checout-map-hint'>" . apply_filters("wpgmza_woo_checkout_hint", __("Note: Right-click to mark your location", "wp-google-maps")) . "</small>";
			echo "<input type='hidden' name='_wpgmza_wcc_coords'>";
			echo "</div>"; 
		}
	}

	
	/**
	 * Store the raw coordinates to the order for later reference 
	 * 
	 * @param int $orderId The order ID to store to
	 * 
	 * @return void
	*/
	public function saveCheckoutCoordinates($orderId){
		if(!empty($orderId) && !empty($_POST['_wpgmza_wcc_coords'])){
			$coords = sanitize_text_field($_POST['_wpgmza_wcc_coords']);
			update_post_meta($orderId, '_wpgmza_wcc_coords', $coords);
		}
	}

	/**
	 * Registers the order location meta box for viewing locations
	 * 
	 * @return void
	*/
	public function registerLocationViewerMetabox(){
		global $wpgmza;

		if(!empty($wpgmza->settings->woo_checkout_map_enabled)){
			add_meta_box(
		        'wpgmza_wco_location_viewer',
		        __('Order Location Viewer (WP Go Maps)', 'wp-google-maps'),
		        array($this, 'renderLocationViewerMetabox'),
		        'shop_order'
		    );
		}
	}

	/**
	 * Renders the product editor meta box for adding product locatios
	 * 
	 * @param object $post
	 * 
	 * @return string
	*/
	public function renderLocationViewerMetabox($post){
		global $wpgmza, $wpdb, $WPGMZA_TABLE_NAME_MAPS;

		$coords = false;
		if(!empty($post) && !empty($post->ID)){
			$coords = get_post_meta($post->ID, '_wpgmza_wcc_coords', true);
		}

		$metabox = new \WPGMZA\DOMDocument();
		$metabox->loadHTML("<div class='wpgmza-wco-viewer'></div>");
		$wrapper = $metabox->querySelector('.wpgmza-wco-viewer');

		if(!empty($coords)){
			/* Map wrapper */
			$mapWrapper = $metabox->createElement('div');
			$mapWrapper->setAttribute('id', 'wpgmza-wco-map-container');

			$mapWrapper->setAttribute('data-coords', $coords);

			/* Map */
			$maps = $wpdb->get_results("SELECT id FROM $WPGMZA_TABLE_NAME_MAPS WHERE active = 0 ORDER BY id ASC LIMIT 1");
			if(!empty($maps)){
				$firstMap = array_pop($maps);
				$mapId = !empty($firstMap) ? $firstMap->id : false;

				if(!empty($mapId)){
					$map = \WPGMZA\Map::createInstance($mapId);

					$map->element->setInlineStyle('min-height', '400px');	// Safeguard for map edit page zero height
					$map->element->setAttribute('id', 'wpgmza_map');	

					$settings = $map->element->getAttribute('data-settings');

					if(!empty($settings)){
						$settings = json_decode($settings);
						if(empty($settings->autoFetchFeatures)){
							/* Disable the data loading */
							$settings->autoFetchFeatures = false;

							/* Now rebake */
							$map->element->setAttribute('data-settings', json_encode($settings));
						}
					}

					$mapWrapper->import($map->element);
				}
			}

			/* Append all to wrapper */
			$wrapper->appendChild($mapWrapper);
		} else {
			$notice = $metabox->createElement("div");
			$notice->addClass("wpgmza-pad-10");
			$notice->appendText(__("No WP Go Maps coordinates found", "wp-google-maps"));

			$wrapper->appendChild($notice);
		}

		echo $metabox->html;

		/* Load base scripts */
		$wpgmza->loadScripts(true);
	}
}