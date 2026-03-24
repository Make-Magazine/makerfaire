<?php
namespace ElementorPro\Modules\AtomicForm\Widgets;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Number_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Base\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Textarea extends Atomic_Widget_Base {
	use Has_Template;

	public static $widget_description = 'Display a text area with customizable type, placeholder, default value, required, readonly, and attributes.';

	public static function get_element_type(): string {
		return 'e-form-textarea';
	}

	public function get_title(): string {
		return esc_html__( 'Text area', 'elementor-pro' );
	}

	public function get_icon(): string {
		return 'eicon-atomic-text-area';
	}

	public function get_categories(): array {
		return [ 'atomic-form' ];
	}

	public function get_keywords() {
		return [ 'atomic', 'form', 'textarea', 'text', 'email' ];
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'placeholder' => String_Prop_Type::make()
				->default( '' ),
			'rows' => Number_Prop_Type::make()
				->default( 4 ),
			'required' => Boolean_Prop_Type::make()
				->default( false ),
			'readonly' => Boolean_Prop_Type::make()
				->default( false ),
			'resizable' => Boolean_Prop_Type::make()
				->default( true ),
			'minlength' => Number_Prop_Type::make(),
			'maxlength' => Number_Prop_Type::make(),
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
						->set_label( __( 'Text area placeholder', 'elementor-pro' ) ),
					Number_Control::bind_to( 'rows' )
						->set_label( __( 'Rows', 'elementor-pro' ) )
						->set_min( 1 )
						->set_step( 1 ),
					Switch_Control::bind_to( 'required' )
						->set_label( __( 'Required', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'readonly' )
						->set_label( __( 'Read only', 'elementor-pro' ) ),
					Switch_Control::bind_to( 'resizable' )
						->set_label( __( 'Resizable', 'elementor-pro' ) ),
					Number_Control::bind_to( 'minlength' )
						->set_label( __( 'Min length', 'elementor-pro' ) )
						->set_min( 0 )
						->set_step( 1 ),
					Number_Control::bind_to( 'maxlength' )
						->set_label( __( 'Max length', 'elementor-pro' ) )
						->set_min( 0 )
						->set_step( 1 ),
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
			'textarea' => __DIR__ . '/../templates/textarea.html.twig',
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
