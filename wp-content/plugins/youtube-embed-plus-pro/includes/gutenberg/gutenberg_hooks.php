<?php

// Hook: Frontend assets.
//add_action('enqueue_block_assets', array(self::class, 'gb_block_assets'));

// Hook: Editor assets.
add_action('enqueue_block_editor_assets', array(self::class, 'gb_editor_assets'));

add_action('admin_footer', array(self::class, 'gb_svg_defs'));

add_action('init', array(self::class, 'gb_register_block_types'));

add_action('admin_init', array(self::class, 'gb_classic_block_setup'));