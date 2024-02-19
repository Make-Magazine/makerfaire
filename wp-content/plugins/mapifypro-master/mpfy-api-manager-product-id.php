<?php

/**
 * Used by the included WooCommerce Api Manager library for license activation.
 * The ID should be the same as the product ID on the store.
 * The ID will be replaced with a new auto-generated one on Gitlab's pipeline build process.
 * 
 * @var int WooCommerce product id
 */
if ( ! defined( 'MAPIFY_AM_PRODUCT_ID' ) ) {
	define('MAPIFY_AM_PRODUCT_ID', 8672 );
}