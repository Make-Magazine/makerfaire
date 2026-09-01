<?php
/**
 * Legacy shim for GravityView_Widget_Search.
 *
 * @package GravityView\Widget\Search
 * @since 3.0.0
 * @deprecated 3.0.0 Use GravityKit\GravityView\Widget\Types\SearchWidget instead.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Wrap in conditional to prevent compile-time redeclaration conflict with PSR-4 class aliases.
if ( ! class_exists( 'GravityView_Widget_Search', false ) ) {
	class GravityView_Widget_Search extends \GravityKit\GravityView\Widget\Types\SearchWidget {
	}
}
