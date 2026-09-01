<?php
namespace GravityKit\GravityView\Utils;

use GravityView_frontend;
use GravityView_View;
use GravityView_View_Data;

use function gravityview;

/**
 * A manager of legacy global states and contexts.
 *
 * Handles mocking of:
 * - \GravityView_View_Data
 * - \GravityView_View
 * - \GravityView_frontend
 *
 * Allows us to set a specific state globally using the old
 *  containers, then reset it. Useful for legacy code that keeps
 *  on depending on these variables.
 *
 * Some examples right now include template files, utility functions,
 *  some actions and filters that expect the old contexts to be set.
 */
final class LegacyContext {
	private static $stack = [];

	/**
	 * Set the state depending on the provided configuration.
	 *
	 * Saves current global state and context.
	 *
	 * Configuration keys:
	 *
	 * - \GV\View   view:       sets GravityView_View::atts, GravityView_View::view_id,
	 *                               GravityView_View::back_link_label
	 *                               GravityView_frontend::context_view_id,
	 *                               GravityView_View::form, GravityView_View::form_id
	 * - \GV\Field  field:      sets GravityView_View::_current_field, GravityView_View::field_data,
	 * - \GV\Entry  entry:      sets GravityView_View::_current_entry, GravityView_frontend::single_entry,
	 *                               GravityView_frontend::entry. The page context ('single' vs
	 *                               'directory') comes from the `request` key, not from loading an
	 *                               entry, so a directory row loop keeps reporting the per-row entry.
	 * - \WP_Post   post:       sets GravityView_View::post_id, GravityView_frontend::post_id,
	 *                               GravityView_frontend::is_gravityview_post_type,
	 *                               GravityView_frontend::post_has_shortcode
	 * - array      paging:     sets GravityView_View::paging
	 * - array      sorting:    sets GravityView_View::sorting
	 * - array      template:   sets GravityView_View::template_part_slug, GravityView_View::template_part_name
	 *
	 * - boolean    in_the_loop sets $wp_actions['loop_start'] and $wp_query::in_the_loop
	 *
	 * also:
	 *
	 * - \GV\Request    request:    sets GravityView_frontend::is_search, GravityView_frontend::single_entry,
	 *                                   GravityView_View::context, GravityView_frontend::entry
	 *
	 * - \GV\View_Collection    views:      sets GravityView_View_Data::views
	 * - \GV\Field_Collection   fields:     sets GravityView_View::fields
	 * - \GV\Entry_Collection   entries:    sets GravityView_View::entries, GravityView_View::total_entries
	 *
	 * and automagically:
	 *
	 * - \GravityView_View      data:       sets GravityView_frontend::gv_output_data
	 *
	 * @param array $configuration The configuration.
	 *
	 * @return void
	 */
	public static function push( $configuration ) {
		array_push( self::$stack, self::freeze() );
		self::load( $configuration );
	}

	/**
	 * Restores last saved state and context.
	 *
	 * @return void
	 */
	public static function pop() {
		self::thaw( array_pop( self::$stack ) );
	}

	/**
	 * Serializes the current configuration as needed.
	 *
	 * @return array The configuration.
	 */
	public static function freeze() {
		global $wp_actions, $wp_query;

		return [
			'GravityView_View::atts'                   => GravityView_View::getInstance()->getAtts(),
			'GravityView_View::view_id'                => GravityView_View::getInstance()->getViewId(),
			'GravityView_View::back_link_label'        => GravityView_View::getInstance()->getBackLinkLabel( false ),
			'GravityView_View_Data::views'             => GravityView_View_Data::getInstance()->views,
			'GravityView_View::entries'                => GravityView_View::getInstance()->getEntries(),
			'GravityView_View::form'                   => GravityView_View::getInstance()->getForm(),
			'GravityView_View::form_id'                => GravityView_View::getInstance()->getFormId(),
			'GravityView_View::context'                => GravityView_View::getInstance()->getContext(),
			'GravityView_View::total_entries'          => GravityView_View::getInstance()->getTotalEntries(),
			'GravityView_View::post_id'                => GravityView_View::getInstance()->getPostId(),
			'GravityView_View::hide_until_searched'    => GravityView_View::getInstance()->isHideUntilSearched(),
			'GravityView_frontend::post_id'            => GravityView_frontend::getInstance()->getPostId(),
			'GravityView_frontend::context_view_id'    => GravityView_frontend::getInstance()->get_context_view_id(),
			'GravityView_frontend::is_gravityview_post_type' => GravityView_frontend::getInstance()->isGravityviewPostType(),
			'GravityView_frontend::post_has_shortcode' => GravityView_frontend::getInstance()->isPostHasShortcode(),
			'GravityView_frontend::gv_output_data'     => GravityView_frontend::getInstance()->getGvOutputData(),
			'GravityView_View::paging'                 => GravityView_View::getInstance()->getPaging(),
			'GravityView_View::sorting'                => GravityView_View::getInstance()->getSorting(),
			'GravityView_frontend::is_search'          => GravityView_frontend::getInstance()->isSearch(),
			'GravityView_frontend::single_entry'       => GravityView_frontend::getInstance()->getSingleEntry(),
			'GravityView_frontend::entry'              => GravityView_frontend::getInstance()->getEntry(),
			'GravityView_View::_current_entry'         => GravityView_View::getInstance()->getCurrentEntry(),
			'GravityView_View::fields'                 => GravityView_View::getInstance()->getFields(),
			'GravityView_View::_current_field'         => GravityView_View::getInstance()->getCurrentField(),
			'wp_actions[loop_start]'                    => empty( $wp_actions['loop_start'] ) ? 0 : $wp_actions['loop_start'],
			'wp_query::in_the_loop'                     => $wp_query->in_the_loop,
		];
	}

	/**
	 * Deserializes a saved configuration. Modifies the global state.
	 *
	 * @param array $data Saved configuration from self::freeze()
	 */
	public static function thaw( $data ) {
		foreach ( (array) $data as $key => $value ) {
			switch ( $key ) :
				case 'GravityView_View::atts':
					GravityView_View::getInstance()->setAtts( $value );
					break;
				case 'GravityView_View::view_id':
					GravityView_View::getInstance()->setViewId( $value );
					break;
				case 'GravityView_View::back_link_label':
					GravityView_View::getInstance()->setBackLinkLabel( $value );
					break;
				case 'GravityView_View_Data::views':
					GravityView_View_Data::getInstance()->views = $value;
					break;
				case 'GravityView_View::entries':
					GravityView_View::getInstance()->setEntries( $value );
					break;
				case 'GravityView_View::form':
					GravityView_View::getInstance()->setForm( $value );
					break;
				case 'GravityView_View::form_id':
					GravityView_View::getInstance()->setFormId( $value );
					break;
				case 'GravityView_View::context':
					GravityView_View::getInstance()->setContext( $value );
					break;
				case 'GravityView_View::total_entries':
					GravityView_View::getInstance()->setTotalEntries( $value );
					break;
				case 'GravityView_View::post_id':
					GravityView_View::getInstance()->setPostId( $value );
					break;
				case 'GravityView_View::is_hide_until_searched':
					GravityView_View::getInstance()->setHideUntilSearched( $value );
					break;
				case 'GravityView_frontend::post_id':
					GravityView_frontend::getInstance()->setPostId( $value );
					break;
				case 'GravityView_frontend::context_view_id':
					$frontend                  = GravityView_frontend::getInstance();
					$frontend->context_view_id = $value;
					break;
				case 'GravityView_frontend::is_gravityview_post_type':
					GravityView_frontend::getInstance()->setIsGravityviewPostType( $value );
					break;
				case 'GravityView_frontend::post_has_shortcode':
					GravityView_frontend::getInstance()->setPostHasShortcode( $value );
					break;
				case 'GravityView_frontend::gv_output_data':
					GravityView_frontend::getInstance()->setGvOutputData( $value );
					break;
				case 'GravityView_View::paging':
					GravityView_View::getInstance()->setPaging( $value );
					break;
				case 'GravityView_View::sorting':
					GravityView_View::getInstance()->setSorting( $value );
					break;
				case 'GravityView_frontend::is_search':
					GravityView_frontend::getInstance()->setIsSearch( $value );
					break;
				case 'GravityView_frontend::single_entry':
					GravityView_frontend::getInstance()->setSingleEntry( $value );
					break;
				case 'GravityView_frontend::entry':
					GravityView_frontend::getInstance()->setEntry( $value );
					break;
				case 'GravityView_View::_current_entry':
					GravityView_View::getInstance()->setCurrentEntry( $value );
					break;
				case 'GravityView_View::fields':
					GravityView_View::getInstance()->setFields( $value );
					break;
				case 'GravityView_View::_current_field':
					GravityView_View::getInstance()->setCurrentField( $value );
					break;
				case 'wp_actions[loop_start]':
					global $wp_actions;
					$wp_actions['loop_start'] = $value;
					break;
				case 'wp_query::in_the_loop':
					global $wp_query;
					$wp_query->in_the_loop = $value;
					break;
			endswitch;
		}
	}

	/**
	 * Hydrates the legacy context globals as needed.
	 *
	 * @see LegacyContext::push() for format.
	 *
	 * @return void
	 */
	public static function load( $configuration ) {
		foreach ( (array) $configuration as $key => $value ) {
			switch ( $key ) :
				case 'view':
					$views = new \GV\View_Collection();
					$views->add( $value );

					self::thaw(
						[
							'GravityView_View::atts'    => $value->settings->as_atts(),
							'GravityView_View::view_id' => $value->ID,
							'GravityView_View::back_link_label' => $value->settings->get( 'back_link_label', null ),
							'GravityView_View::form'    => $value->form ? $value->form->form : null,
							'GravityView_View::form_id' => $value->form ? $value->form->ID : null,
							'GravityView_View::is_hide_until_searched' => $value->settings->get( 'hide_until_searched', null ) && ! gravityview()->request->is_search( $value ),

							'GravityView_View_Data::views' => $views,
							'GravityView_frontend::gv_output_data' => GravityView_View_Data::getInstance(),
							'GravityView_frontend::context_view_id' => $value->ID,
						]
					);
					break;
				case 'post':
					$has_shortcode = false;
					foreach ( \GV\Shortcode::parse( $value->post_content ) as $shortcode ) {
						if ( 'gravityview' == $shortcode->name ) {
							$has_shortcode = true;
							break;
						}
					}
					self::thaw(
						[
							'GravityView_View::post_id' => $value->ID,
							'GravityView_frontend::post_id' => $value->ID,
							'GravityView_frontend::is_gravityview_post_type' => 'gravityview' == $value->post_type,
							'GravityView_frontend::post_has_shortcode' => $has_shortcode,
						]
					);
					break;
				case 'views':
					self::thaw(
						[
							'GravityView_View_Data::views' => $value,
							'GravityView_frontend::gv_output_data' => GravityView_View_Data::getInstance(),
						]
					);
					break;
				case 'entries':
					self::thaw(
						[
							'GravityView_View::entries' => array_map(
								function ( $e ) {
									return $e->as_entry(); },
								$value->all()
							),
							'GravityView_View::total_entries' => $value->total(),
						]
					);
					break;
				case 'entry':
					self::thaw(
						[
							'GravityView_frontend::single_entry' => $value->ID,
							'GravityView_frontend::entry' => $value->as_entry(),
							'GravityView_View::_current_entry' => $value->as_entry(),
						]
					);
					break;
				case 'fields':
					self::thaw(
						[
							'GravityView_View::fields' => $value->as_configuration(),
						]
					);
					break;
				case 'field':
					self::thaw(
						[
							'GravityView_View::_current_field' => [
								'field_id'       => $value->ID,
								'field'          => $value->field,
								'field_settings' => $value->as_configuration(),
								'form'           => GravityView_View::getInstance()->getForm(),
								'field_type'     => $value->type,
								/** {@since 1.6} */
																	'entry' => GravityView_View::getInstance()->getCurrentEntry(),
								'UID'            => $value->UID,

							// 'field_path' => $field_path, /** {@since 1.16} */
							// 'value' => $value,
							// 'display_value' => $display_value,
							// 'format' => $format,
							],
						]
					);
					break;
				case 'request':
					self::thaw(
						[
							'GravityView_View::context' => (
								$value->is_entry() ? 'single' :
								( $value->is_edit_entry() ? 'edit' :
										( $value->is_view( false ) ? 'directory' : null )
									)
							),
							'GravityView_frontend::is_search' => $value->is_search(),
						]
					);

					if ( ! $value->is_entry() ) {
						self::thaw(
							[
								'GravityView_frontend::single_entry' => 0,
								'GravityView_frontend::entry' => 0,
							]
						);
					}
					break;
				case 'paging':
					self::thaw(
						[
							'GravityView_View::paging' => $value,
						]
					);
					break;
				case 'sorting':
					self::thaw(
						[
							'GravityView_View::sorting' => $value,
						]
					);
					break;
				case 'in_the_loop':
					self::thaw(
						[
							'wp_query::in_the_loop'  => $value,
							'wp_actions[loop_start]' => $value ? 1 : 0,
						]
					);
					break;
			endswitch;
		}

		global $gravityview_view;
		$gravityview_view = GravityView_View::getInstance();
	}

	/**
	 * Resets the global state completely.
	 *
	 * Use with utmost care, as filter and action callbacks
	 *  may be added again.
	 *
	 * Does not touch the context stack.
	 *
	 * @return void
	 */
	public static function reset() {
		self::$stack = [];

		GravityView_View::$instance      = null;
		GravityView_frontend::$instance  = null;
		GravityView_View_Data::$instance = null;

		global $wp_query, $wp_actions;

		$wp_query->in_the_loop    = false;
		$wp_actions['loop_start'] = 0;
	}
}
