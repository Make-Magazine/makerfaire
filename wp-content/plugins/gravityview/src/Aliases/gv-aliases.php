<?php
/**
 * GV namespace aliases for the PSR-4 migration.
 *
 * Maps \GV\* classes to their new \GravityKit\GravityView\* equivalents
 * via a lazy SPL autoloader. Aliases are created on demand when a class
 * is first referenced, avoiding circular dependency issues.
 *
 * @package GravityKit\GravityView\Aliases
 * @since 3.0.0
 */

/**
 * Only register the autoloader when the plugin constants are available.
 *
 * This file may be loaded multiple times:
 * 1. By Composer's `files` autoload (may run before constants are defined)
 * 2. Explicitly by future/loader.php (after constants are defined)
 */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	return;
}

/**
 * Lazy alias autoloader for GV\* namespace classes.
 *
 * When PHP encounters a GV\* class reference, this autoloader:
 * 1. Checks the static map for a PSR-4 equivalent
 * 2. Ensures the PSR-4 class is loaded (triggers Composer PSR-4 autoloader)
 * 3. Creates a class_alias() so the GV\* name resolves
 *
 * @since 3.0.0
 */
if ( ! defined( 'GRAVITYVIEW_GV_ALIASES_REGISTERED' ) ) {
	define( 'GRAVITYVIEW_GV_ALIASES_REGISTERED', true );

	spl_autoload_register( function ( $class ) {
		static $map = null;

		if ( null === $map ) {
			$map = [
				// Core infrastructure classes.
				'GV\\Core'                                => 'GravityKit\\GravityView\\Core\\Core',
				'GV\\Plugin'                              => 'GravityKit\\GravityView\\Core\\Plugin',
				'GV\\Permalinks'                          => 'GravityKit\\GravityView\\Core\\Permalinks',

				// Logging infrastructure.
				'GV\\LogLevel'                            => 'GravityKit\\GravityView\\Logging\\LogLevel',
				'GV\\Logger'                              => 'GravityKit\\GravityView\\Logging\\Logger',
				'GV\\WP_Action_Logger'                    => 'GravityKit\\GravityView\\Logging\\LoggerWpAction',

				// Extension infrastructure.
				'GV\\Extension'                           => 'GravityKit\\GravityView\\Extension\\Extension',

				// Settings infrastructure.
				'GV\\Settings'                            => 'GravityKit\\GravityView\\Settings\\Settings',
				'GV\\Plugin_Settings'                     => 'GravityKit\\GravityView\\Settings\\PluginSettings',
				'GV\\View_Settings'                       => 'GravityKit\\GravityView\\Settings\\ViewSettings',

				// Utility classes.
				'GV\\oEmbed'                              => 'GravityKit\\GravityView\\Media\\oEmbed',
				'GV\\Utils'                               => 'GravityKit\\GravityView\\Utils\\Utils',
				'GV\\Background_Scheduler'                => 'GravityKit\\GravityView\\BackgroundJobs\\Scheduler',

				// Collection and Source base classes.
				'GV\\Collection'                          => 'GravityKit\\GravityView\\Data\\Collection',
				'GV\\Source'                               => 'GravityKit\\GravityView\\Form\\Source',

				// Context classes.
				'GV\\Context'                             => 'GravityKit\\GravityView\\Template\\Context',
				'GV\\Template_Context'                    => 'GravityKit\\GravityView\\Template\\TemplateContext',

				// Contracts (interfaces).
				'GV\\Collection_Position_Aware'           => 'GravityKit\\GravityView\\Contracts\\CollectionPositionAwareInterface',

				// Traits.
				'GV\\Field_Renderer_Trait'                => 'GravityKit\\GravityView\\Renderer\\FieldRendererTrait',

				// Wrappers.
				'GV\\Wrappers\\views'                     => 'GravityKit\\GravityView\\Wrappers\\Views',

				// Form domain classes.
				'GV\\Form'                                => 'GravityKit\\GravityView\\Form\\Form',
				'GV\\GF_Form'                             => 'GravityKit\\GravityView\\Form\\FormGravityForms',
				'GV\\Form_Collection'                     => 'GravityKit\\GravityView\\Form\\FormCollection',

				// Field domain classes.
				'GV\\Field'                               => 'GravityKit\\GravityView\\Field\\Field',
				'GV\\GF_Field'                            => 'GravityKit\\GravityView\\Field\\FieldGravityForms',
				'GV\\Internal_Field'                      => 'GravityKit\\GravityView\\Field\\InternalField',
				'GV\\Internal_Source'                      => 'GravityKit\\GravityView\\Field\\InternalSource',
				'GV\\Field_Collection'                    => 'GravityKit\\GravityView\\Field\\FieldCollection',

				// Entry domain classes.
				'GV\\Entry'                               => 'GravityKit\\GravityView\\Entry\\Entry',
				'GV\\GF_Entry'                            => 'GravityKit\\GravityView\\Entry\\EntryGravityForms',
				'GV\\Entry_Collection'                    => 'GravityKit\\GravityView\\Entry\\EntryCollection',

				// Entry collection helper classes.
				'GV\\Entry_Filter'                        => 'GravityKit\\GravityView\\Entry\\EntryFilter',
				'GV\\GF_Entry_Filter'                     => 'GravityKit\\GravityView\\Entry\\EntryFilterGravityForms',
				'GV\\Entry_Sort'                          => 'GravityKit\\GravityView\\Entry\\EntrySort',
				'GV\\Entry_Offset'                        => 'GravityKit\\GravityView\\Entry\\EntryOffset',
				'GV\\Entry_Batches'                       => 'GravityKit\\GravityView\\Entry\\BackgroundJobs\\EntryBatches',
				'GV\\Entry_Selection'                     => 'GravityKit\\GravityView\\Entry\\BackgroundJobs\\EntrySelection',

				// Multi-entry class.
				'GV\\Multi_Entry'                         => 'GravityKit\\GravityView\\Entry\\MultiEntry',

				// Grid class.
				'GV\\Grid'                                => 'GravityKit\\GravityView\\Renderer\\Grid',

				// View domain classes.
				'GV\\View'                                => 'GravityKit\\GravityView\\View\\View',
				'GV\\View_Collection'                     => 'GravityKit\\GravityView\\View\\ViewCollection',
				'GV\\Join'                                => 'GravityKit\\GravityView\\View\\Join',

				// Widget domain classes.
				'GV\\Widget'                              => 'GravityKit\\GravityView\\Widget\\Widget',
				'GV\\Widget_Collection'                   => 'GravityKit\\GravityView\\Widget\\WidgetCollection',
				'GV\\Widgets\\Page_Size'                  => 'GravityKit\\GravityView\\Widget\\Types\\PageSize',

				// Template loader (third-party base class).
				'GV\\Gamajo_Template_Loader'              => 'GravityKit\\GravityView\\Template\\GamajoTemplateLoader',

				// Template base classes.
				'GV\\Template'                            => 'GravityKit\\GravityView\\Template\\Template',
				'GV\\View_Template'                       => 'GravityKit\\GravityView\\Template\\TemplateView',
				'GV\\Entry_Template'                      => 'GravityKit\\GravityView\\Template\\TemplateEntry',
				'GV\\Field_Template'                      => 'GravityKit\\GravityView\\Template\\TemplateField',

				// Field template implementation classes.
				'GV\\Field_CSV_Template'                  => 'GravityKit\\GravityView\\Template\\Field\\CSV',
				'GV\\Field_HTML_Template'                 => 'GravityKit\\GravityView\\Template\\Field\\HTML',

				// Entry template implementation classes.
				'GV\\Entry_Table_Template'                => 'GravityKit\\GravityView\\Template\\Entry\\Table',
				'GV\\Entry_List_Template'                 => 'GravityKit\\GravityView\\Template\\Entry\\ListTemplate',
				'GV\\Entry_Legacy_Template'               => 'GravityKit\\GravityView\\Template\\Entry\\Legacy',
				'GV\\Entry_Layout_Builder_Template'       => 'GravityKit\\GravityView\\Template\\Entry\\LayoutBuilder',

				// View template implementation classes.
				'GV\\View_Table_Template'                 => 'GravityKit\\GravityView\\Template\\View\\Table',
				'GV\\View_List_Template'                  => 'GravityKit\\GravityView\\Template\\View\\ListTemplate',
				'GV\\View_Legacy_Template'                => 'GravityKit\\GravityView\\Template\\View\\Legacy',
				'GV\\View_Layout_Builder_Template'        => 'GravityKit\\GravityView\\Template\\View\\LayoutBuilder',

				// Legacy override template class.
				'GV\\Legacy_Override_Template'            => 'GravityKit\\GravityView\\Template\\LegacyOverride',

				// Renderer classes.
				'GV\\Renderer'                            => 'GravityKit\\GravityView\\Renderer\\Renderer',
				'GV\\View_Renderer'                       => 'GravityKit\\GravityView\\Renderer\\ViewRenderer',
				'GV\\Entry_Renderer'                      => 'GravityKit\\GravityView\\Renderer\\EntryRenderer',
				'GV\\Edit_Entry_Renderer'                 => 'GravityKit\\GravityView\\Renderer\\EntryEditRenderer',
				'GV\\Field_Renderer'                      => 'GravityKit\\GravityView\\Renderer\\FieldRenderer',

				// Request domain classes.
				'GV\\Request'                             => 'GravityKit\\GravityView\\Request\\Request',
				'GV\\Frontend_Request'                    => 'GravityKit\\GravityView\\Request\\FrontendRequest',
				'GV\\Admin_Request'                       => 'GravityKit\\GravityView\\Request\\AdminRequest',
				'GV\\Mock_Request'                        => 'GravityKit\\GravityView\\Request\\MockRequest',
				'GV\\REST\\Request'                       => 'GravityKit\\GravityView\\Request\\RestRequest',
				'GV\\CLI_Request'                         => 'GravityKit\\GravityView\\Request\\CLIRequest',

				// Shortcode classes.
				'GV\\Shortcode'                           => 'GravityKit\\GravityView\\Shortcode\\Shortcode',
				'GV\\Shortcodes\\gravityview'             => 'GravityKit\\GravityView\\Shortcode\\GravityViewShortcode',
				'GV\\Shortcodes\\gventry'                 => 'GravityKit\\GravityView\\Shortcode\\GVEntryShortcode',
				'GV\\Shortcodes\\gvfield'                 => 'GravityKit\\GravityView\\Shortcode\\GVFieldShortcode',
				'GV\\Shortcodes\\gv_entry_link'           => 'GravityKit\\GravityView\\Shortcode\\GVEntryLinkShortcode',
				'GV\\Shortcodes\\gvlogic'                 => 'GravityKit\\GravityView\\Shortcode\\GVLogicShortcode',

				// REST API classes.
				'GV\\REST\\Core'                          => 'GravityKit\\GravityView\\REST\\Core',
				'GV\\REST\\Route'                         => 'GravityKit\\GravityView\\REST\\Route',
				'GV\\REST\\Views_Route'                   => 'GravityKit\\GravityView\\REST\\ViewsRoute',

				// Mock classes.
				'GV\\Mocks\\Legacy_Context'               => 'GravityKit\\GravityView\\Utils\\LegacyContext',
				'GV\\Mocks\\GF_Query_Call_TIMESORT'       => 'GravityKit\\GravityView\\GravityForms\\QueryExtensions\\GFQueryCallTimesort',
				'GV\\Mocks\\GF_Query_Condition_IS_NULL'   => 'GravityKit\\GravityView\\GravityForms\\QueryExtensions\\GFQueryConditionIsNull',

				// Search querying.
				'GV\\Search\\Search_Policy'                              => 'GravityKit\\GravityView\\Search\\SearchPolicy',
				'GV\\Search\\Policies\\Search_Fields_Policy'             => 'GravityKit\\GravityView\\Search\\Policies\\SearchFieldsPolicy',
				'GV\\Search\\Querying\\Search_Filter'                    => 'GravityKit\\GravityView\\Search\\Querying\\SearchFilter',
				'GV\\Search\\Querying\\Search_Filter_Builder'            => 'GravityKit\\GravityView\\Search\\Querying\\SearchFilterBuilder',
				'GV\\Search\\Querying\\Search_Request'                   => 'GravityKit\\GravityView\\Search\\Querying\\SearchRequest',
				'GV\\Search\\Querying\\Search_Filter_Visitor'            => 'GravityKit\\GravityView\\Search\\Querying\\SearchFilterVisitor',
				'GV\\Search\\Querying\\Visitors\\Abstract_Search_Filter_Visitor' => 'GravityKit\\GravityView\\Search\\Querying\\Visitors\\AbstractSearchFilterVisitor',
				'GV\\Search\\Querying\\Visitors\\Search_Criteria_Visitor' => 'GravityKit\\GravityView\\Search\\Querying\\Visitors\\SearchCriteriaVisitor',
				'GV\\Search\\Querying\\Visitors\\Query_Filter_Visitor' => 'GravityKit\\GravityView\\Search\\Querying\\Visitors\\QueryFilterVisitor',
			];
		}

		if ( ! isset( $map[ $class ] ) ) {
			return;
		}

		$target = $map[ $class ];

		// The PSR-4 class will be autoloaded by Composer when class_exists() / interface_exists() / trait_exists() references it.
		if ( ! class_exists( $target, true ) && ! interface_exists( $target, true ) && ! trait_exists( $target, true ) ) {
			return;
		}

		class_alias( $target, $class );
	} );

	/**
	 * Eagerly resolve all GV\* aliases.
	 *
	 * The SPL autoloader above handles dependency ordering automatically:
	 * when a child class extends a parent, PHP triggers the autoloader for
	 * the parent first. By triggering class_exists() for each alias, we
	 * ensure all aliases are available before any code running on
	 * plugins_loaded (priority 1+) references the legacy GV\* class names.
	 *
	 * @since 3.0.0
	 */
	$gv_alias_names = [
		// Core infrastructure (load first as many classes depend on these).
		'GV\\Collection',
		'GV\\Source',
		'GV\\Context',
		'GV\\Template_Context',
		'GV\\Core',
		'GV\\Plugin',
		'GV\\Permalinks',

		// Logging.
		'GV\\LogLevel',
		'GV\\Logger',
		'GV\\WP_Action_Logger',

		// Extension.
		'GV\\Extension',

		// Settings.
		'GV\\Settings',
		'GV\\Plugin_Settings',
		'GV\\View_Settings',

		// Contracts (interfaces and traits).
		'GV\\Collection_Position_Aware',
		'GV\\Field_Renderer_Trait',

		// Utility.
		'GV\\oEmbed',
		'GV\\Utils',
		'GV\\Background_Scheduler',
		'GV\\Grid',
		'GV\\Wrappers\\views',

		// Form domain.
		'GV\\Form',
		'GV\\GF_Form',
		'GV\\Form_Collection',

		// Field domain.
		'GV\\Field',
		'GV\\GF_Field',
		'GV\\Internal_Field',
		'GV\\Internal_Source',
		'GV\\Field_Collection',

		// Entry domain.
		'GV\\Entry',
		'GV\\GF_Entry',
		'GV\\Entry_Collection',
		'GV\\Entry_Filter',
		'GV\\GF_Entry_Filter',
		'GV\\Entry_Sort',
		'GV\\Entry_Offset',
		'GV\\Entry_Batches',
		'GV\\Entry_Selection',
		'GV\\Multi_Entry',

		// View domain.
		'GV\\View',
		'GV\\View_Collection',
		'GV\\Join',

		// Widget domain.
		'GV\\Widget',
		'GV\\Widget_Collection',
		'GV\\Widgets\\Page_Size',

		// Template loader (third-party base class — must load before Template classes).
		'GV\\Gamajo_Template_Loader',

		// Template base classes (depend on Gamajo_Template_Loader).
		'GV\\Template',
		'GV\\View_Template',
		'GV\\Entry_Template',
		'GV\\Field_Template',

		// Field template implementations.
		'GV\\Field_CSV_Template',
		'GV\\Field_HTML_Template',

		// Entry template implementations.
		'GV\\Entry_Table_Template',
		'GV\\Entry_List_Template',
		'GV\\Entry_Legacy_Template',
		'GV\\Entry_Layout_Builder_Template',

		// View template implementations.
		'GV\\View_Table_Template',
		'GV\\View_List_Template',
		'GV\\View_Legacy_Template',
		'GV\\View_Layout_Builder_Template',

		// Legacy override template.
		'GV\\Legacy_Override_Template',

		// Renderer classes (depend on Gamajo_Template_Loader via LegacyOverride).
		'GV\\Renderer',
		'GV\\View_Renderer',
		'GV\\Entry_Renderer',
		'GV\\Edit_Entry_Renderer',
		'GV\\Field_Renderer',

		// Request classes.
		'GV\\Request',
		'GV\\Frontend_Request',
		'GV\\Admin_Request',
		'GV\\Mock_Request',
		'GV\\REST\\Request',
		'GV\\CLI_Request',

		// Shortcode classes.
		'GV\\Shortcode',
		'GV\\Shortcodes\\gravityview',
		'GV\\Shortcodes\\gventry',
		'GV\\Shortcodes\\gvfield',
		'GV\\Shortcodes\\gv_entry_link',
		'GV\\Shortcodes\\gvlogic',

		// REST API classes.
		'GV\\REST\\Core',
		'GV\\REST\\Route',
		'GV\\REST\\Views_Route',

		// Mock classes (excluding GF_Query extensions that depend on Gravity Forms classes).
		'GV\\Mocks\\Legacy_Context',

		// Search querying.
		'GV\\Search\\Search_Policy',
		'GV\\Search\\Policies\\Search_Fields_Policy',
		'GV\\Search\\Querying\\Search_Filter_Visitor',
		'GV\\Search\\Querying\\Search_Filter',
		'GV\\Search\\Querying\\Search_Request',
		'GV\\Search\\Querying\\Search_Filter_Builder',
		'GV\\Search\\Querying\\Visitors\\Abstract_Search_Filter_Visitor',
		'GV\\Search\\Querying\\Visitors\\Search_Criteria_Visitor',
		'GV\\Search\\Querying\\Visitors\\Query_Filter_Visitor',
	];

	$resolve_gv_aliases = static function () use ( $gv_alias_names ) {
		foreach ( $gv_alias_names as $alias ) {
			class_exists( $alias );
		}
	};

	if ( function_exists( 'add_action' ) ) {
		add_action( 'plugins_loaded', $resolve_gv_aliases, 0 );
	} else {
		$resolve_gv_aliases();
	}

	unset( $gv_alias_names, $resolve_gv_aliases );
}


// Search field aliases are registered in legacy-aliases.php to ensure
// GravityView_Admin_View_Item (which SearchField extends) is aliased first.
