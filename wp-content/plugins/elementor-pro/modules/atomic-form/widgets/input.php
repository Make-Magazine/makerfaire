<?php
namespace ElementorPro\Modules\AtomicForm\Widgets;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Input extends Atomic_Widget_Base {
	use Has_Template;

	public static $widget_description = 'Display a text input with customizable type, placeholder, default value, required, readonly, and attributes.';

	public static function get_element_type(): string {
		return 'e-form-input';
	}

	public function get_title(): string {
		return esc_html__( 'Input', 'elementor-pro' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-input';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'input', 'text', 'email' ];
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'placeholder' => String_Prop_Type::make()
				->default( '' ),
			'type' => String_Prop_Type::make()
				->default( 'text' )
				->enum( [ 'text', 'email' ] ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'readonly' => Boolean_Prop_Type::make()
				->default( false ),
			'attributes' => Attributes_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor-pro' ) )
				->set_items( [
					Text_Control::bind_to( 'placeholder' )
					  ->set_placeholder( 'Enter placeholder text' )
						->set_label( __( 'Input placeholder', 'elementor-pro' ) ),
					Select_Control::bind_to( 'type' )
						->set_label( __( 'Type', 'elementor-pro' ) )
						->set_options( [
							[
								'label' => __( 'Text', 'elementor-pro' ),
								'value' => 'text',
							],
							[
								'label' => __( 'Email', 'elementor-pro' ),
								'value' => 'email',
							],
						] ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'readonly' )
						->set_label( __( 'Read only', 'elementor-pro' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor-pro' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
	}

	protected function get_settings_controls(): array {
		return [
			Text_Control::bind_to( '_cssid' )
				->set_label( __( 'ID', 'elementor-pro' ) )
				->set_meta( $this->get_css_id_control_meta() ),
		];
	}

	protected function get_templates(): array {
		return [
			'input' => __DIR__ . '/../templates/input.html.twig',
		];
	}

	protected function define_base_styles(): array {
		return [
			'base' => Style_Definition::make(),
		];
	}

	protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => false,
		];
	}
}
