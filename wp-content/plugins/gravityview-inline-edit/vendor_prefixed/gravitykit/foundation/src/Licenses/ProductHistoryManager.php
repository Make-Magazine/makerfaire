<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses;

use GravityKit\GravityEdit\Foundation\Helpers\Arr;

class ProductHistoryManager {
	const DB_OPTION_NAME = '_gk_foundation_products_history';

	/**
	 * @since 1.2.2
	 *
	 * @var string Products history.
	 */
	private $_products_history;

	/**
	 * @since 1.2.2
	 *
	 * @var ProductHistoryManager Class instance.
	 */
	private static $_instance;

	/**
	 * Returns class instance.
	 *
	 * @since 1.2.2
	 *
	 * @return ProductHistoryManager
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the class.
	 *
	 * @since 1.2.2
	 *
	 * @return void
	 */
	public function init() {
		static $initialized;

		if ( $initialized ) {
			return;
		}

		/**
		 * @filter `gk/foundation/products/disable-history` Controls whether to track product history, such as installation, activation, deactivation, update, and deletion events.
		 *
		 * @since  1.2.2
		 *
		 * @param bool $disable_history Whether to disable product history. Defaults to false.
		 */
		if ( apply_filters( 'gk/foundation/products/disable-history', false ) ) {
			$initialized = true;

			return;
		}

		add_action( 'deleted_plugin', [ $this, 'record_product_deletion_event' ], 10, 2 );

		add_action( 'upgrader_process_complete', [ $this, 'record_product_installation_event' ], 10, 2 );

		add_action( 'upgrader_process_complete', [ $this, 'record_product_update_event' ], 10, 2 );

		add_action( 'activated_plugin', [ $this, 'record_product_activation_event' ] );

		add_action( 'deactivated_plugin', [ $this, 'record_product_deactivation_event' ] );

		$initialized = true;
	}

	/**
	 * Records product activation event.
	 *
	 * @since 1.2.2
	 *
	 * @param string $product_path
	 *
	 * @return void
	 */
	public function record_product_activation_event( $product_path ) {
		$products = ProductManager::get_instance()->get_products_data();

		$product = Arr::first( $products, function ( $product ) use ( $product_path ) {
			return $product['path'] === $product_path;
		} );

		if ( ! $product ) {
			return;
		}

		$this->update_product_history( 'activate', $product );
	}

	/**
	 * Records product deactivation event.
	 *
	 * @since 1.2.2
	 *
	 * @param string $product_path
	 *
	 * @return void
	 */
	public function record_product_deactivation_event( $product_path ) {
		$products = ProductManager::get_instance()->get_products_data();

		$product = Arr::first( $products, function ( $product ) use ( $product_path ) {
			return $product['path'] === $product_path;
		} );

		if ( ! $product ) {
			return;
		}

		$this->update_product_history( 'deactivate', $product );
	}

	/**
	 * Records product deletion event.
	 *
	 * @since 1.2.2
	 *
	 * @param string $product_path
	 * @param bool   $deleted
	 *
	 * @return void
	 */
	public function record_product_deletion_event( $product_path, $deleted ) {
		if ( ! $deleted ) {
			return;
		}

		$products = ProductManager::get_instance()->get_products_data();

		$product = Arr::first( $products, function ( $product ) use ( $product_path ) {
			return $product['path'] === $product_path;
		} );

		if ( ! $product ) {
			return;
		}

		$this->update_product_history( 'delete', $product );
	}

	/**
	 * Records product installation event.
	 *
	 * @since 1.2.2
	 *
	 * @param WP_Upgrader $upgrader WP_Upgrader instance.
	 * @param array       $data     WP_Upgrader data. See {@see WP_Upgrader::run()} for more information.
	 *
	 * @return void
	 */
	public function record_product_installation_event( $upgrader, $data ) {
		if ( 'plugin' !== Arr::get( $data, 'type' ) || 'install' !== Arr::get( $data, 'action' ) ) {
			return;
		}

		$products = ProductManager::get_instance()->get_products_data( [
			'skip_request_cache' => true,
		] );

		$text_domain = $upgrader->new_plugin_data['TextDomain'] ?? null;

		if ( ! isset( $products[ $text_domain ] ) ) {
			return;
		};

		$this->update_product_history( 'install', $products[ $text_domain ] );
	}

	/**
	 * Records product update event.
	 *
	 * @since 1.2.2
	 *
	 * @param WP_Upgrader $upgrader WP_Upgrader instance.
	 * @param array       $data     WP_Upgrader data. See {@see WP_Upgrader::run()} for more information.
	 *
	 * @return void
	 */
	public function record_product_update_event( $upgrader, $data ) {
		if ( 'plugin' !== Arr::get( $data, 'type' ) || 'update' !== $data['action'] ) {
			return;
		}

		$cached_products = ProductManager::get_instance()->get_products_data();

		$products = ProductManager::get_instance()->get_products_data( [
			'skip_request_cache' => true,
		] );

		$product = Arr::first( $products, function ( $product ) use ( $data ) {
			return $product['path'] === $data['plugin'];
		} );

		if ( ! $product ) {
			return;
		}

		$this->update_product_history( 'update', $product, $cached_products[ $product['text_domain'] ]['installed_version'] ?? null );
	}

	/**
	 * Updates product history in the database.
	 *
	 * @since 1.2.2
	 *
	 * @param string      $action           Action performed on the product.
	 * @param array       $product          Product data.
	 * @param string|null $previous_version (optional) The previous version of the product. Only used for updates. Defaults to null.
	 *
	 * @return array
	 */
	public function update_product_history( $action, $product, $previous_version = null ) {
		$history = get_option( self::DB_OPTION_NAME, [] );

		if ( ! isset( $history[ $product['text_domain'] ] ) ) {
			$history[ $product['text_domain'] ] = [];
		}

		$history_entry = [
			'action'  => $action,
			'version' => $product['installed_version']
		];

		if ( 'update' === $action ) {
			$history_entry['previous_version'] = $previous_version ?? $product['installed_version'];
		}

		$history[ $product['text_domain'] ][ current_time( 'timestamp' ) ] = $history_entry;

		krsort( $history[ $product['text_domain'] ] );

		update_option( self::DB_OPTION_NAME, $history );

		$this->_products_history = $history;

		return $history;
	}

	/**
	 * Returns history for all products.
	 *
	 * @since 1.2.2
	 *
	 * @return array
	 */
	public function get_products_history() {
		if ( apply_filters( 'gk/foundation/products/disable-history', false ) ) {
			return [];
		}

		if ( ! isset( $this->_products_history ) ) {
			$this->_products_history = get_option( self::DB_OPTION_NAME, [] );
		}

		return $this->_products_history;
	}

	/**
	 * Returns history for a specific product.
	 *
	 * @since 1.2.2
	 *
	 * @param string $text_domain
	 *
	 * @return array
	 */
	public function get_product_history( $text_domain ) {
		$history = $this->get_products_history();

		return $history[ $text_domain ] ?? [];
	}
}
