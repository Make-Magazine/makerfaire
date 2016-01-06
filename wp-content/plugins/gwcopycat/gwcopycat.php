<?php
/**
* Plugin Name: GP Copy Cat
* Description: Allow users to copy the value of one field to another automatically or by clicking a checkbox. Is your shipping address the same as your billing? Copy cat!
* Plugin URI: http://gravitywiz/category/perks/
* Version: 1.3.3
* Author: David Smith
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

/**
* Saftey net for individual perks that are active when core Gravity Perks plugin is inactive.
*/
$gw_perk_file = __FILE__;
if(!require_once(dirname($gw_perk_file) . '/safetynet.php'))
    return;

class GWCopyCat extends GWPerk {
	
	public $version = '1.3.4';
	protected $min_perks_version = '1.0.6';
	protected $min_gravity_forms_version = '1.9.3';

    public function init() {

        $this->register_scripts();

        add_filter( 'gform_enqueue_scripts',       array( $this, 'enqueue_form_scripts' ) );
        add_action( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );
        add_filter( 'gform_pre_render',            array( $this, 'modify_frontend_form' ) );

    }

    public function register_scripts() {
        wp_register_script( 'gp-copy-cat', $this->get_base_url() . '/js/gp-copy-cat.js', array(), $this->version );
    }

    public function enqueue_form_scripts( $form ) {
        if( $this->has_copy_cat_field( $form ) ) {
            wp_enqueue_script( 'gp-copy-cat'  );
        }
    }

    public function register_init_scripts( $form ) {

        if( ! $this->has_copy_cat_field( $form ) ) {
            return;
        }

        $copy_fields      = $this->get_copy_cat_fields( $form );
        $enable_overwrite = gf_apply_filters( 'gpcc_overwrite_existing_values', $form['id'], false, $form );
        $script           = 'new gwCopyObj( ' . $form['id'] . ', ' . json_encode( $copy_fields ) . ', ' . ( $enable_overwrite ? 'true' : 'false' ) . ' );';

        GFFormDisplay::add_init_script( $form['id'], 'gp-copy-cat', GFFormDisplay::ON_PAGE_RENDER, $script );

    }

    public function modify_frontend_form( $form ) {

        if( ! $this->has_copy_cat_field( $form ) ) {
            return $form;
        }

        $copy_field_ids = array_keys( $this->get_copy_cat_fields( $form ) );

        foreach( $form['fields'] as &$field ) {
            if( in_array( $field['id'], $copy_field_ids ) ) {
                $field['cssClass'] .= ' gwcopy';
            }
        }

        return $form;
    }

    function has_copy_cat_field( $form ) {
        $copy_fields = $this->get_copy_cat_fields( $form );
        return ! empty( $copy_fields );
    }

    function get_copy_cat_fields( $form ) {

        $copy_fields = array();

        foreach( $form['fields'] as &$field ) {

            preg_match_all( '/copy-([0-9]+(?:.[0-9]+)?)-to-([0-9]+(?:.[0-9]+)?)/', $field['cssClass'], $matches, PREG_SET_ORDER );
            if( empty( $matches ) ) {
                continue;
            }

            foreach( $matches as $match ) {
                list( $class, $source_field, $target_field ) = $match;
                $copy_fields[ $field['id'] ][] = array( 'source' => $source_field, 'target' => $target_field );
            }

        }

        return $copy_fields;
    }

    function documentation() {
        return array( 'type' => 'url', 'value' => 'http://gravitywiz.com/documentation/gp-copy-cat/' );
    }

}