<?php
namespace GV;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

if ( ! class_exists( '\GV\View_Template' ) ) {
	return;
}

/**
 * The View DIY Template class .
 *
 * Renders a \GV\View and a \GV\Entry_Collection via a \GV\View_Renderer.
 */
class View_DIY_Template extends View_Template {
	/**
	 * @var string The template slug to be loaded (like "table", "list")
	 */
	public static $slug = 'diy';

	/**
	 * Output the field in the diy view.
	 *
	 * @param \GV\Field $field The field to output.
	 * @param \GV\Entry $entry The entry.
	 * @param array $extras Extra stuff, like wpautop, etc.
	 *
	 * @return string
	 */
	public function the_field( \GV\Field $field, \GV\Entry $entry, $extras = array() ) {
		$form = $this->view->form;

		\GV\Mocks\Legacy_Context::push( array( 'entry' => $entry ) );

		$context = Template_Context::from_template( $this, compact( 'field', 'entry' ) );

		$renderer = new Field_Renderer();
		$source = is_numeric( $field->ID ) ? $this->view->form : new Internal_Source();
		
		$value = $renderer->render( $field, $this->view, $source, $entry, $this->request );

		/**
		 * @deprecated Here for back-compatibility.
		 */
		$label = apply_filters( 'gravityview_render_after_label', $field->get_label( $this->view, $form ), $field->as_configuration() );
		$label = apply_filters( 'gravityview/template/field_label', $label, $field->as_configuration(), $form->form ? $form->form : null, null );

		/**
		 * @filter `gravityview/template/field/label` Override the field label.
		 * @since 2.0
		 * @param[in,out] string $label The label to override.
		 * @param \GV\Template_Context $context The context.
		 */
		$label = apply_filters( 'gravityview/template/field/label', $label, $context );

		/**
		 * @filter `gravityview/template/table/entry/hide_empty`
		 * @param boolean Should the row be hidden if the value is empty? Default: don't hide.
		 * @param \GV\Template_Context $context The context ;) Love it, cherish it. And don't you dare modify it!
		 */
		$hide_empty = apply_filters( 'gravityview/render/hide-empty-zone', Utils::get( $extras, 'hide_empty', $this->view->settings->get( 'hide_empty', false ) ), $context );

		$extras = array_merge( $extras, compact( 'hide_empty', 'value', 'label' ) );

		$extras['wrapper_class'] = false;
		$extras['label_markup'] = '';
		$extras['wpautop'] = false;
		$extras['markup'] = '{{ value }}';

		$output = \gravityview_field_output( $extras, $context );

		\GV\Mocks\Legacy_Context::pop();

		return $output;
	}

	/**
	 * `gravityview_entry_class` and `gravityview/template/diy/entry/class` filters.
	 *
	 * Modify of the class of a row.
	 *
	 * @param string $class The class.
	 * @param \GV\Entry $entry The entry.
	 * @param \GV\Template_Context The context.
	 *
	 * @return string The classes.
	 */
	public static function entry_class( $class, $entry, $context ) {
		/**
		 * @filter `gravityview_entry_class` Modify the class applied to the entry row.
		 * @param string $class Existing class.
		 * @param array $entry Current entry being displayed
		 * @param GravityView_View $this Current GravityView_View object
		 * @deprecated Use `gravityview/template/diy/entry/class`
		 * @return string The modified class.
		 */
		$class = apply_filters( 'gravityview_entry_class', $class, $entry->as_entry(), \GravityView_View::getInstance() );

		/**
		 * @filter `gravityview/template/diy/entry/class` Modify the class aplied to the entry row.
		 * @param string $class The existing class.
		 * @param \GV\Template_Context The context.
		 * @return string The modified class.
		 */
		return apply_filters( 'gravityview/template/diy/entry/class', $class, Template_Context::from_template( $context->template, compact( 'entry' ) ) );
	}

	/**
	 * `gravityview_diy_body_before` and `gravityview/template/diy/body/before` actions.
	 *
	 * @param $context \GV\Template_Context The 2.0 context.
	 *
	 * @return void
	 */
	public static function body_before( $context ) {
		/**
		 * @action `gravityview/template/diy/body/before`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/body/before', $context );

		/**
		* @action `gravityview_diy_body_before`
		* @deprecated Use `gravityview/template/diy/body/before`
		* @since 1.0
		* @param GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_diy_body_before', \GravityView_View::getInstance() /** ugh! */ );
	}

	/**
	 * `gravityview_diy_body_after` and `gravityview/template/diy/body/after` actions.
	 *
	 * @param $context \GV\Template_Context The 2.0 context.
	 *
	 * @return void
	 */
	public static function body_after( $context ) {
		/**
		 * @action `gravityview/template/diy/body/after`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/body/after', $context );

		/**
		* @action `gravityview_diy_body_after`
		* @deprecated Use `gravityview/template/diy/body/after`
		* @since 1.0
		* @param GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_diy_body_after', \GravityView_View::getInstance() /** ugh! */ );
	}

	/**
	 * `gravityview_entry_before` and `gravityview/template/dify/entry/before` actions.
	 *
	 * @param \GV\Entry $entry The entry.
	 * @param \GV\Template_Context $context The 2.0 context.
	 *
	 * @return void
	 */
	public static function entry_before( $entry, $context ) {
		/**
		 * @action `gravityview/template/diy/entry/before`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/entry/before', Template_Context::from_template( $context->template, compact( 'entry' ) ) );

		/**
		* @action `gravityview_entry_before`
		* @deprecated Use `gravityview/template/diy/entry/before`
		* @since 1.0
		* @param GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_entry_before', $entry->as_entry(), \GravityView_View::getInstance() /** ugh! */ );
	}

	/**
	 * `gravityview_entry_after` and `gravityview/template/dify/entry/after` actions.
	 *
	 * @param \GV\Entry $entry The entry.
	 * @param \GV\Template_Context $context The 2.0 context.
	 *
	 * @return void
	 */
	public static function entry_after( $entry, $context ) {
		/**
		 * @action `gravityview/template/diy/entry/after`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/entry/after', Template_Context::from_template( $context->template, compact( 'entry' ) ) );

		/**
		* @action `gravityview_entry_after`
		* @deprecated Use `gravityview/template/diy/entry/after`
		* @since 1.0
		* @param GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_entry_after', $entry->as_entry(), \GravityView_View::getInstance() /** ugh! */ );
	}
}
