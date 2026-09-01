<?php

namespace GravityKit\GravityView\PageBuilder\Gutenberg\Blocks;

use GravityKit\GravityView\PageBuilder\Gutenberg\GutenbergBlockTrait;

class Entry {

	use GutenbergBlockTrait;

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'entry';
	}

	/**
	 * Modifies block meta.
	 *
	 * @since 2.17
	 *
	 * @param array $block_meta Block metadata.
	 *
	 * @return array Modified block metadata.
	 */
	public function modify_block_meta( $block_meta ) {
		return [
			'title'           => __( 'GravityView Entry', 'gk-gravityview' ),
			'render_callback' => [ self::class, 'render' ],
			'localization'    => [
				'previewImage' => untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/preview.svg',
			],
		];
	}

	/**
	 * Renders [gventry] shortcode.
	 *
	 * @since 2.17
	 *
	 * @param array $block_attributes Block attributes.
	 *
	 * @return string Rendered output.
	 */
	public static function render( $block_attributes = [] ) {
		return self::render_block( $block_attributes );
	}
}
