<?php
namespace GV;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

if ( ! class_exists( '\GV\Entry_Template' ) ) {
	return;
}

/**
 * The Entry DIY Template class .
 *
 * Renders a \GV\Entry using a \GV\Entry_Renderer.
 */
class Entry_DIY_Template extends Entry_Template {
	/**
	 * @var string The template slug to be loaded (like "table", "list")
	 */
	public static $slug = 'diy';

	/**
	 * Entry_DIY_Template constructor.
	 */
	public function __construct(  Entry $entry, View $view, Request $request ) {
		parent::__construct(  $entry, $view, $request );

		add_action( 'gravityview/template/diy/single/before', array( __CLASS__, 'back_link' ) );
	}

	/**
	 * Add the back link to the entry
	 *
	 * @see https://gist.github.com/zackkatz/97c6159782cc26344851803e31399acc to remove
	 *
	 * @since 2.1
	 *
	 * @return void
	 */
	static public function back_link( $context ) {
		echo '<p class="gv-back-link">' . gravityview_back_link( $context ) . '</p>';
	}

	/**
	 * Output the field in the diy view.
	 *
	 * @param \GV\Field $field The field to output.
	 * @param \GV\Entry $entry The entry.
	 * @param array $extras Extra stuff, like wpautop, etc.
	 *
	 * @return string
	 */
	public function the_field( \GV\Field $field, $extras = array() ) {
		$form = $this->view->form;
		$entry = method_exists( $this->entry, 'from_field' ) ? $this->entry->from_field( $field ) : $this->entry;

		$context = Template_Context::from_template( $this, compact( 'field' ) );

		$renderer = new Field_Renderer();
		$source = is_numeric( $field->ID ) ? ( GF_Form::by_id( $field->form_id ) ? : $this->view->form ) : new Internal_Source();
		
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
		 * @param boolean $hide_empty Should the row be hidden if the value is empty? Default: don't hide.
		 * @param \GV\Template_Context $context The context ;) Love it, cherish it. And don't you dare modify it!
		 */
		$hide_empty = apply_filters( 'gravityview/render/hide-empty-zone', Utils::get( $extras, 'hide_empty', $this->view->settings->get( 'hide_empty', false ) ), $context );

		$extras = array_merge( $extras, compact( 'hide_empty', 'value', 'label' ) );

		$extras['wrapper_class'] = false;
		$extras['label_markup'] = '';
		$extras['wpautop'] = false;
		$extras['markup'] = '{{ value }}';

		return \gravityview_field_output( $extras, $context );
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
		 * @param \GravityView_View $this Current GravityView_View object
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
	 * `gravityview_diy_single_before` and `gravityview/template/diy/single/before` actions.
	 *
	 * @param $context \GV\Template_Context The 2.0 context.
	 *
	 * @return void
	 */
	public static function single_before( $context ) {
		/**
		 * @action `gravityview/template/diy/single/before`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/single/before', $context );

		/**
		* @action `gravityview_diy_single_before`
		* @deprecated Use `gravityview/template/diy/single/before`
		* @since 1.0
		* @param \GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_diy_single_before', \GravityView_View::getInstance() /** ugh! */ );
	}

	/**
	 * `gravityview_diy_single_after` and `gravityview/template/diy/single/after` actions.
	 *
	 * @param $context \GV\Template_Context The 2.0 context.
	 *
	 * @return void
	 */
	public static function single_after( $context ) {
		/**
		 * @action `gravityview/template/diy/single/after`
		 * @since 2.0
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/single/after', $context );

		/**
		* @action `gravityview_diy_single_after`
		* @deprecated Use `gravityview/template/diy/single/after`
		* @since 1.0
		* @param \GravityView_View $gravityview_view Current GravityView_View object.
		*/
		do_action( 'gravityview_diy_single_after', \GravityView_View::getInstance() /** ugh! */ );
	}

	/**
	 * `gravityview_diy_entry_before` and `gravityview/template/diy/entry/before` actions.
	 *
	 * @since 2.1
	 *
	 * @param \GV\Entry $entry The current entry
	 * @param \GV\Template_Context $context The 2.0 context.
	 *
	 * @return void
	 */
	public static function entry_before( $entry, $context ) {
		/**
		 * @action `gravityview/template/diy/entry/before`
		 * @since 2.1
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/entry/before', $context );
	}

	/**
	 * `gravityview_diy_entry_after` and `gravityview/template/diy/entry/after` actions.
	 *
	 * @since 2.1
	 *
	 * @param \GV\Entry $entry The current entry
	 * @param \GV\Template_Context $context The 2.0 context.
	 *
	 * @return void
	 */
	public static function entry_after( $entry, $context ) {
		/**
		 * @action `gravityview/template/diy/entry/after`
		 * @since 2.1
		 * @param \GV\Template_Context $context The template context.
		 */
		do_action( 'gravityview/template/diy/entry/after', $context );
	}
}