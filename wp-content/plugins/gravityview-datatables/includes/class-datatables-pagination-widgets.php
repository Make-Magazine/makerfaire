<?php
/**
 * Core's Page Links, Pagination Info and Page Size widgets do not fit a DataTables View:
 * Page Links and Pagination Info depend on server-rendered pagination counts the DataTables
 * template never produces, so they render nothing. Page Size reloads the page to set
 * `?page_size=N`, which competes with DataTables' own length menu and, with "Save Table
 * State" on, is silently outranked by the persisted length on the next visit.
 *
 * @see reports/settings-interface-audit.md F-16
 *
 * @since 3.11
 */
class GV_DataTables_Pagination_Widgets {

	const SUPPRESSED_WIDGET_IDS = array( 'page_info', 'page_links', 'page_size' );

	function __construct() {
		add_filter( 'gravityview/view/widgets', array( $this, 'remove_from_rendered_view' ), 10, 2 );
		add_filter( 'gravityview/widgets/register', array( $this, 'restrict_to_other_templates' ), 20 );
	}

	/**
	 * Drops the suppressed widgets from a DataTables View's rendered widget collection, so a
	 * View that still carries one renders nothing for it rather than an inert or competing
	 * control. The editor no longer offers them, so only already-saved Views reach this.
	 *
	 * @param \GV\Widget_Collection $widgets The View's widgets.
	 * @param \GV\View              $view    The View.
	 *
	 * @return \GV\Widget_Collection
	 */
	function remove_from_rendered_view( $widgets, $view ) {
		if ( 'datatables_table' !== gravityview_get_directory_entries_template_id( $view->ID ) ) {
			return $widgets;
		}

		$filtered = new \GV\Widget_Collection();

		foreach ( $widgets->all() as $widget ) {
			if ( in_array( $widget->get_widget_id(), self::SUPPRESSED_WIDGET_IDS, true ) ) {
				continue;
			}

			$filtered->add( $widget );
		}

		return $filtered;
	}

	/**
	 * Stops the View editor from offering the suppressed widgets on a DataTables View.
	 *
	 * `show_in_template` on a registered widget is an allowlist, not a denylist, so
	 * excluding one template means listing every other one. Computed here, at call time
	 * rather than at construction, so a template registered later in the request (a
	 * third-party layout) is still included.
	 *
	 * @param array $registered_widgets Widgets registered on `gravityview/widgets/register`.
	 *
	 * @return array
	 */
	function restrict_to_other_templates( $registered_widgets ) {
		$other_templates = array_diff( array_keys( gravityview_get_registered_templates() ), array( 'datatables_table' ) );

		foreach ( self::SUPPRESSED_WIDGET_IDS as $widget_id ) {
			if ( empty( $registered_widgets[ $widget_id ] ) ) {
				continue;
			}

			// Don't clobber an allowlist another filter already narrowed.
			if ( empty( $registered_widgets[ $widget_id ]['show_in_template'] ) ) {
				$registered_widgets[ $widget_id ]['show_in_template'] = array_values( $other_templates );
			}
		}

		return $registered_widgets;
	}
}

new GV_DataTables_Pagination_Widgets();
