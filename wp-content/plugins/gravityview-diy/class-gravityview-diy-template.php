<?php

if ( ! class_exists( '\GravityView_Template' ) ) {
	return;
}

/**
 * GravityView_Default_Template_Code class.
 * Defines DIY template
 */
class GravityView_DIY_Template extends \GravityView_Template {

	public $template_id = 'diy';

	public $template_part_slug = 'diy';

	function __construct( $id = 'diy', $settings = array(), $field_options = array(), $areas = array() ) {

		$list_settings = array(
			'slug'        => 'diy',
			'type'        => 'custom',
			'label'       => _x( 'DIY', 'DIY means "Do It Yourself"', 'gravityview-diy' ),
			'description' => esc_html__( 'A flexible, powerful layout for designers & developers.', 'gravityview-diy' ),
			'logo'        => plugins_url( 'logo-diy.png', __FILE__ ),
			'css_source'  => null,
		);

		$settings = wp_parse_args( $settings, $list_settings );

		$field_options = array(
			'show_as_link' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Link to single entry', 'gravityview-diy' ),
				'value'   => false,
				'context' => 'directory'
			),
		    'wrapper_div' => array(
			    'type'    => 'checkbox',
			    'label'   => __( 'Wrapper', 'gravityview-diy' ),
			    'value'   => 'div',
		    ),
		);

		$areas = array(
			array(
				'1-1' => array(
					array(
						'areaid'   => 'diy-diy',
						'title'    => __( 'Fields to Display', 'gravityview-diy' ),
						'subtitle' => __( 'The field values will need to be styled using CSS.', 'gravityview-diy' ),
					),
				),
			),
		);

		$this->_add_hooks();

		parent::__construct( $id, $settings, $field_options, $areas );

	}

	function _add_hooks() {
		add_filter( 'gravityview/template/views_template_paths', array( $this, 'template_paths' ) );
		add_filter( 'gravityview/template/entries_template_paths', array( $this, 'template_paths_single' ) );

		add_filter( 'gravityview_field_entry_value', array( $this, 'field_entry_value' ), 10, 4 );
	}

	/**
	 * @param  array       $field_options Array of field options with `label`, `value`, `type`, `default` keys
	 * @param  string      $template_id Table slug
	 * @param  float       $field_id    GF Field ID - Example: `3`, `5.2`, `entry_link`, `created_by`
	 * @param  string      $context     What context are we in? Example: `single` or `directory`
	 * @param  string      $input_type  (textarea, list, select, etc.)
	 *
	 * @return array $field_options, with
	 */
	function assign_field_options( $field_options, $template_id, $field_id = null, $context = 'directory', $input_type = '' ) {

		$field_options = parent::assign_field_options( $field_options, $template_id, $field_id, $context, $input_type );

		if ( ! in_array( $template_id, array( $this->template_id, 'default_table_edit' ) ) ) {
			return $field_options;
		}

		unset( $field_options['custom_label'], $field_options['show_label'] );

		$diy_options = array();

		$html_desc = esc_html_x( 'Display HTML %s the field output. Supports Merge Tags.', 'Same description for before and after areas. %s replaced by "before" and "after"', 'gravityview-diy');

		$diy_options['show_label'] = array(
			'value'     => 1,
			'type'      => 'checkbox',
			'class'     => 'hidden hide-if-js',
		);

		$diy_options['custom_label'] = array(
			'type'       => 'text',
			'label'      => __( 'Custom Label', 'gravityview-diy' ),
			'desc'       => __('This label is to make it easier to organize your fields. It is not visible on the front-end.', 'gravityview-diy'),
			'value'      => '',
			'merge_tags' => false,
		);

		if ( 'custom' !== $field_id && 'edit' !== $context ) {
			$diy_options['before_output'] = array(
				'type'       => 'textarea',
				'label'      => __( 'Before Output', 'gravityview-diy' ),
				'desc'       => sprintf( $html_desc, __('before', 'gravityview-diy') ),
				'value'      => '',
				'class'      => 'widefat code',
				'merge_tags' => 'force',
				'rows'       => 8
			);
		}

		if ( ( 'custom' === $field_id && 'edit' === $context ) || 'edit' !== $context ) {
			$diy_options['container'] = array(
				'type'       => 'radio',
				'label'      => __( 'Container Tag', 'gravityview-diy' ),
				'desc'       => __( 'This HTML tag will be used to wrap the field value.', 'gravityview-diy' ),
				'value'      => ( ( 'custom' === $field_id ) ? '' : 'div' ),
				'class'      => 'widefat code',
				'merge_tags' => false,
				'tooltip'    => 'gv_container_tag',
				'options'    => $this->get_container_tags( $input_type, $context ),
				'requires'   => 'default_diy',
			);
		}

		if ( 'custom' !== $field_id && 'edit' !== $context ) {
			$diy_options['after_output'] = array(
				'type' => 'textarea',
				'label' => __( 'After Output', 'gravityview-diy' ),
				'desc'  => sprintf( $html_desc, __('after', 'gravityview-diy') ),
				'value' => '',
				'class' => 'widefat code',
				'merge_tags' => 'force',
				'rows'  => 8
			);
		}

		$diy_options['show_label'] = array(
			'type'     => 'checkbox',
			'label'    => __( 'Show Label', 'gravityview-diy' ),
			'value'    => ! empty ( $is_table_layout ),
			'priority' => 1000,
			'group'    => 'label',
		);

		$diy_options['custom_label'] = array(
			'type'       => 'text',
			'label'      => __( 'Custom Label:', 'gravityview-diy' ),
			'value'      => '',
			'merge_tags' => true,
			'class'      => 'widefat',
			'priority'   => 1100,
			'requires'   => 'show_label',
			'group'      => 'label',
		);

		return $diy_options + $field_options;
	}

	/**
	 * Get array of HTML tags available to be used as field value containers
	 *
	 * @param string $input_type Type of input being displayed
	 * @param string $context `directory`, `single`, `edit`
	 *
	 * @return array|mixed|void
	 */
	private function get_container_tags( $input_type = '', $context = 'directory' ) {

		$container_tags = array(
			'div' => 'DIV',
			'blockquote' => 'BLOCKQUOTE',
			'pre' => 'PRE',
			'h1' => 'H1',
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
			'p'  => 'P',
			'' => __('None', 'gravityview-diy'),
		);

		if ( 'address' === $input_type ) {
			$container_tags['address'] = 'ADDRESS';
		}

		/**
		 * @filter `gravityview-diy/container-tags`
		 * @param array $container_tags HTML tags in "tag name" => "tag label" array
		 * @param string $input_type Type of input being displayed ("address", "text", "list", "custom")
		 * @param string $context `directory`, `single`, `edit`
		 */
		$container_tags = apply_filters( 'gravityview-diy/container-tags', $container_tags, $input_type, $context );

		return $container_tags;
	}

	public function field_entry_value( $output, $entry, $field_settings, $current_field ) {

		$return = $output;

		$output_is_empty = ( '' === $output );

		/**
		 * @filter `gravityview-diy/field-value/show-settings-content-if-empty` Whether to show Before and After output values, even if the field output is empty
		 * @param bool $show_if_output_is_empty Default: false
		 */
		$show_if_output_is_empty = apply_filters( 'gravityview-diy/field-value/show-settings-content-if-empty', false );

		if ( ! empty( $field_settings['container'] ) &&  ! $output_is_empty ) {

			if ( in_array( $field_settings['container'], array_keys( self::get_container_tags() ) ) ) {

				$form = \GV\GF_Form::by_id( $entry['form_id'] );

				$css_class = gv_class( $field_settings, $form, $entry );

				$css_class = gravityview_sanitize_html_class( $css_class );

				$css_class = empty( $css_class ) ? $css_class : ' class="' . $css_class . '"';

				$return = sprintf( '<%1$s%2$s>%3$s</%1$s>', $field_settings['container'], $css_class, $return );
			}
		}

		if ( ! empty( $field_settings['before_output'] ) ) {

			$before_output = GFCommon::replace_variables( $field_settings['before_output'], $current_field['form'], $entry, false, true, false );

			$before_output = trim( $before_output );

			if( ! $output_is_empty || $show_if_output_is_empty ) {
				$return = $before_output . $return;
			}
		}

		if ( ! empty( $field_settings['after_output'] ) ) {

			$after_output = GFCommon::replace_variables( $field_settings['after_output'], $current_field['form'], $entry, false, true, false );

			$after_output = trim( $after_output );

			if( ! $output_is_empty || $show_if_output_is_empty ) {
				$return = $return . $after_output;
			}
		}

		return $return;
	}

	/**
	 * Tell GravityView to check for templates here, too
	 *
	 * @param array $paths Array of paths to check for template files
	 *
	 * @return mixed
	 */
	public function template_paths( $paths ) {
		$paths[5736] = plugin_dir_path( __FILE__ ) . 'templates/views/';
		return $paths;
	}

	/**
	 * Tell GravityView to check for templates here, too
	 *
	 * @param array $paths Array of paths to check for template files
	 *
	 * @return mixed
	 */
	public function template_paths_single( $paths ) {
		$paths[5736] = plugin_dir_path( __FILE__ ) . 'templates/entries/';
		return $paths;
	}
}

new GravityView_DIY_Template;
