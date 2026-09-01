<?php
/**
 * Legacy aliases for the PSR-4 migration.
 *
 * Maps GravityView_* classes to their new \GravityKit\GravityView\* equivalents
 * using a lazy SPL autoloader. Legacy class names are resolved on first use
 * via class_alias(), avoiding eager loading and circular dependency issues.
 *
 * @package GravityKit\GravityView\Aliases
 * @since 3.0.0
 */

/**
 * Only register aliases when the plugin constants are available.
 *
 * This file may be loaded multiple times:
 * 1. By Composer's `files` autoload (may run before constants are defined)
 * 2. Explicitly by future/loader.php (after constants are defined)
 *
 * The autoloader is registered once and lazily resolves legacy class names.
 */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	return;
}

/**
 * Register a lazy SPL autoloader for legacy class names.
 *
 * Instead of eagerly calling class_alias() for every mapping at load time,
 * this autoloader intercepts class lookups for legacy names and creates
 * the alias on demand. This prevents circular dependency issues when
 * parent classes haven't been aliased yet.
 *
 * @since 3.0.0
 */
if ( ! defined( 'GRAVITYVIEW_LEGACY_ALIASES_REGISTERED' ) ) {
	define( 'GRAVITYVIEW_LEGACY_ALIASES_REGISTERED', true );

	spl_autoload_register( function ( $class_name ) {
		static $map = [
			// API.
			'GravityView_API' => 'GravityKit\\GravityView\\API\\API',

			// View.
			'GravityView_View' => 'GravityKit\\GravityView\\View\\ViewLegacy',

			// Admin.
			'GravityView_Admin' => 'GravityKit\\GravityView\\Admin\\Admin',
			'GravityView_Admin_Views' => 'GravityKit\\GravityView\\Admin\\AdminViews',

			// Frontend.
			'GravityView_frontend' => 'GravityKit\\GravityView\\Frontend\\Frontend',

			// Gutenberg blocks (moved into the PageBuilder namespace).
			'GravityKit\\GravityView\\Gutenberg\\Blocks' => 'GravityKit\\GravityView\\PageBuilder\\Gutenberg\\Blocks',

			// Entry / Approval.
			'GravityView_Entry_Approval' => 'GravityKit\\GravityView\\Entry\\Approval\\Approval',
			'GravityView_Entry_Approval_Status' => 'GravityKit\\GravityView\\Entry\\ApprovalStatus',
			'GravityView_Entry_Approval_Merge_Tags' => 'GravityKit\\GravityView\\Entry\\ApprovalMergeTags',
			'GravityView_Entry_Batches' => 'GravityKit\\GravityView\\Entry\\BackgroundJobs\\EntryBatches',
			'GravityViewEntryBatches' => 'GravityKit\\GravityView\\Entry\\BackgroundJobs\\EntryBatches',

			// Support.
			'GravityView_Cache' => 'GravityKit\\GravityView\\Data\\Cache',
			'GVCommon' => 'GravityKit\\GravityView\\Legacy\\Utility\\Common',
			'GravityView_GFFormsModel' => 'GravityKit\\GravityView\\Data\\Data',
			'GravityView_Image' => 'GravityKit\\GravityView\\Frontend\\Image',
			'GravityView_Merge_Tags' => 'GravityKit\\GravityView\\Renderer\\MergeTags',
			'GravityView_Compatibility' => 'GravityKit\\GravityView\\Utils\\Compatibility',
			'GravityView_GF_Compat' => 'GravityKit\\GravityView\\GravityForms\\Compat',
			'GravityView_Background_Scheduler' => 'GravityKit\\GravityView\\BackgroundJobs\\Scheduler',
			'GravityView_Logging' => 'GravityKit\\GravityView\\Logging\\Logging',
			'GravityView_Notifications' => 'GravityKit\\GravityView\\Core\\Notifications',
			'GravityView_Powered_By' => 'GravityKit\\GravityView\\Frontend\\PoweredBy',
			'GravityView_Template' => 'GravityKit\\GravityView\\Template\\LegacyTemplate',
			'GravityView_oEmbed' => 'GravityKit\\GravityView\\Media\\LegacyOEmbed',
			'GravityView_Error_Messages' => 'GravityKit\\GravityView\\Utils\\ErrorMessages',
			'GravityView_Post_Types' => 'GravityKit\\GravityView\\Core\\PostTypes',
			'GravityView_View_Data' => 'GravityKit\\GravityView\\Data\\ViewData',
			'GravityView_HTML_Elements' => 'GravityKit\\GravityView\\Utils\\HtmlElements',
			'GravityView_Uninstall' => 'GravityKit\\GravityView\\Core\\Uninstall',

			// Deprecation.
			'GravityView_Deprecated_Hook_Notices' => 'GravityKit\\GravityView\\Deprecation\\DeprecatedHookNotices',

			// AJAX.
			'GravityView_Ajax' => 'GravityKit\\GravityView\\AJAX\\AJAX',

			// Settings.
			'GravityView_Settings' => 'GravityKit\\GravityView\\Settings\\GravityViewSettings',

			// Permissions.
			'GravityView_Roles_Capabilities' => 'GravityKit\\GravityView\\Permissions\\RolesCapabilities',

			// Admin Metaboxes.
			'GravityView_Admin_Metaboxes' => 'GravityKit\\GravityView\\Admin\\Metaboxes\\AdminMetaboxes',
			'GravityView_Metabox_Tab' => 'GravityKit\\GravityView\\Admin\\Metaboxes\\MetaboxTab',
			'GravityView_Metabox_Tabs' => 'GravityKit\\GravityView\\Admin\\Metaboxes\\MetaboxTabs',

			// Admin View Items.
			'GravityView_Admin_View_Item' => 'GravityKit\\GravityView\\Admin\\ViewItem',
			'GravityView_Admin_View_Field' => 'GravityKit\\GravityView\\Admin\\ViewField',
			'GravityView_Admin_View_Widget' => 'GravityKit\\GravityView\\Admin\\ViewWidget',

			// Admin Support.
			'GravityView_Support_Port' => 'GravityKit\\GravityView\\Admin\\SupportPort',
			'GravityView_Admin_No_Conflict' => 'GravityKit\\GravityView\\Admin\\NoConflict',
			'GravityView_Admin_Duplicate_View' => 'GravityKit\\GravityView\\Admin\\DuplicateView',

			// Admin Rendering.
			'GravityView_Render_Settings' => 'GravityKit\\GravityView\\Admin\\Rendering\\RenderSettings',
			'GravityView_FieldType' => 'GravityKit\\GravityView\\Admin\\Rendering\\FieldType',

			// Admin Field Types.
			'GravityView_FieldType_text' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Text',
			'GravityView_FieldType_number' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Number',
			'GravityView_FieldType_textarea' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Textarea',
			'GravityView_FieldType_select' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Select',
			'GravityView_FieldType_multiselect' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Multiselect',
			'GravityView_FieldType_checkbox' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Checkbox',
			'GravityView_FieldType_checkboxes' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Checkboxes',
			'GravityView_FieldType_radio' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Radio',
			'GravityView_FieldType_hidden' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Hidden',
			'GravityView_FieldType_html' => 'GravityKit\\GravityView\\Admin\\FieldTypes\\Html',

			// Admin Other.
			'GravityView_Admin_Notices' => 'GravityKit\\GravityView\\Admin\\Notices',
			'GravityView_Admin_Add_Shortcode' => 'GravityKit\\GravityView\\Admin\\AddShortcode',
			'GravityView_Admin_ApproveEntries' => 'GravityKit\\GravityView\\Entry\\Approval\\EntriesList',
			'GravityView_Welcome' => 'GravityKit\\GravityView\\Admin\\Welcome',
			'GravityView_Admin_Bar' => 'GravityKit\\GravityView\\Admin\\AdminBar',
			'GravityView_GF_Entries_List' => 'GravityKit\\GravityView\\Admin\\GFEntriesList',
			'GravityView_Migrate' => 'GravityKit\\GravityView\\Admin\\Migrate',

			// Field Types — Registry and Base.
			'GravityView_Fields' => 'GravityKit\\GravityView\\Field\\Types\\FieldRegistry',
			'GravityView_Field' => 'GravityKit\\GravityView\\Field\\Types\\GravityViewField',

			// Field Types — Basic.
			'GravityView_Field_Text' => 'GravityKit\\GravityView\\Field\\Types\\Text',
			'GravityView_Field_Textarea' => 'GravityKit\\GravityView\\Field\\Types\\Textarea',
			'GravityView_Field_Number' => 'GravityKit\\GravityView\\Field\\Types\\Number',
			'GravityView_Field_Email' => 'GravityKit\\GravityView\\Field\\Types\\Email',
			'GravityView_Field_Phone' => 'GravityKit\\GravityView\\Field\\Types\\Phone',
			'GravityView_Field_Website' => 'GravityKit\\GravityView\\Field\\Types\\Website',
			'GravityView_Field_Select' => 'GravityKit\\GravityView\\Field\\Types\\Select',
			'GravityView_Field_Radio' => 'GravityKit\\GravityView\\Field\\Types\\Radio',
			'GravityView_Field_Checkbox' => 'GravityKit\\GravityView\\Field\\Types\\Checkbox',
			'GravityView_Field_MultiSelect' => 'GravityKit\\GravityView\\Field\\Types\\MultiSelect',
			'GravityView_Field_Image_Choice' => 'GravityKit\\GravityView\\Field\\Types\\ImageChoice',

			// Field Types — Complex / File / Media.
			'GravityView_Field_Name' => 'GravityKit\\GravityView\\Field\\Types\\Name',
			'GravityView_Field_Address' => 'GravityKit\\GravityView\\Field\\Types\\Address',
			'GravityView_Field_Date' => 'GravityKit\\GravityView\\Field\\Types\\Date',
			'GravityView_Field_Time' => 'GravityKit\\GravityView\\Field\\Types\\Time',
			'GravityView_Field_List' => 'GravityKit\\GravityView\\Field\\Types\\ListField',
			'GravityView_Field_Consent' => 'GravityKit\\GravityView\\Field\\Types\\Consent',
			'GravityView_Field_FileUpload' => 'GravityKit\\GravityView\\Field\\Types\\FileUpload',
			'GravityView_Field_Post_Image' => 'GravityKit\\GravityView\\Field\\Types\\PostImage',

			// Field Types — Entry Metadata.
			'GravityView_Field_ID' => 'GravityKit\\GravityView\\Field\\Types\\EntryId',
			'GravityView_Field_Date_Created' => 'GravityKit\\GravityView\\Field\\Types\\DateCreated',
			'GravityView_Field_Date_Updated' => 'GravityKit\\GravityView\\Field\\Types\\DateUpdated',
			'GravityView_Field_Created_By' => 'GravityKit\\GravityView\\Field\\Types\\CreatedBy',
			'GravityView_Field_Source_URL' => 'GravityKit\\GravityView\\Field\\Types\\SourceUrl',
			'GravityView_Field_IP' => 'GravityKit\\GravityView\\Field\\Types\\IP',

			// Field Types — GV-specific.
			'GravityView_Field_Custom' => 'GravityKit\\GravityView\\Field\\Types\\CustomContent',
			'GravityView_Field_Entry_Link' => 'GravityKit\\GravityView\\Field\\Types\\EntryLink',
			'GravityView_Field_Edit_Link' => 'GravityKit\\GravityView\\Field\\Types\\EditLink',
			'GravityView_Field_Delete_Link' => 'GravityKit\\GravityView\\Field\\Types\\DeleteLink',
			'GravityView_Field_Other_Entries' => 'GravityKit\\GravityView\\Field\\Types\\OtherEntries',
			'GravityView_Field_Entry_Approval' => 'GravityKit\\GravityView\\Field\\Types\\EntryApproval',
			'GravityView_Field_Is_Approved' => 'GravityKit\\GravityView\\Field\\Types\\IsApproved',
			'GravityView_Field_Is_Read' => 'GravityKit\\GravityView\\Field\\Types\\IsRead',
			'GravityView_Field_Is_Starred' => 'GravityKit\\GravityView\\Field\\Types\\IsStarred',

			// Field Types — Product / Payment.
			'GravityView_Field_Product' => 'GravityKit\\GravityView\\Field\\Types\\Product',
			'GravityView_Field_Quantity' => 'GravityKit\\GravityView\\Field\\Types\\Quantity',
			'GravityView_Field_Option' => 'GravityKit\\GravityView\\Field\\Types\\Option',
			'GravityView_Field_Shipping' => 'GravityKit\\GravityView\\Field\\Types\\Shipping',
			'GravityView_Field_Total' => 'GravityKit\\GravityView\\Field\\Types\\Total',
			'GravityView_Field_Payment_Amount' => 'GravityKit\\GravityView\\Field\\Types\\PaymentAmount',
			'GravityView_Field_Payment_Date' => 'GravityKit\\GravityView\\Field\\Types\\PaymentDate',
			'GravityView_Field_Payment_Method' => 'GravityKit\\GravityView\\Field\\Types\\PaymentMethod',
			'GravityView_Field_Payment_Status' => 'GravityKit\\GravityView\\Field\\Types\\PaymentStatus',
			'GravityView_Field_Transaction_ID' => 'GravityKit\\GravityView\\Field\\Types\\TransactionId',
			'GravityView_Field_Transaction_Type' => 'GravityKit\\GravityView\\Field\\Types\\TransactionType',
			'GravityView_Field_CreditCard' => 'GravityKit\\GravityView\\Field\\Types\\CreditCard',
			'GravityView_Field_Is_Fulfilled' => 'GravityKit\\GravityView\\Field\\Types\\IsFulfilled',
			'GravityView_Field_Currency' => 'GravityKit\\GravityView\\Field\\Types\\Currency',
			'GravityView_Field_Unsubscribe' => 'GravityKit\\GravityView\\Field\\Types\\Unsubscribe',

			// Field Types — Add-on / Survey / Quiz.
			'GravityView_Field_Survey' => 'GravityKit\\GravityView\\Field\\Types\\Survey',
			'GravityView_Field_Quiz' => 'GravityKit\\GravityView\\Field\\Types\\Quiz',
			'GravityView_Field_Quiz_Score' => 'GravityKit\\GravityView\\Field\\Types\\QuizScore',
			'GravityView_Field_Chained_Select' => 'GravityKit\\GravityView\\Field\\Types\\ChainedSelect',

			// Field Types — Post.
			'GravityView_Field_Post_Title' => 'GravityKit\\GravityView\\Field\\Types\\PostTitle',
			'GravityView_Field_Post_Content' => 'GravityKit\\GravityView\\Field\\Types\\PostContent',
			'GravityView_Post_Excerpt' => 'GravityKit\\GravityView\\Field\\Types\\PostExcerpt',
			'GravityView_Field_Post_ID' => 'GravityKit\\GravityView\\Field\\Types\\PostId',
			'GravityView_Field_Post_Category' => 'GravityKit\\GravityView\\Field\\Types\\PostCategory',
			'GravityView_Field_Post_Tags' => 'GravityKit\\GravityView\\Field\\Types\\PostTags',
			'GravityView_Field_Post_Custom_Field' => 'GravityKit\\GravityView\\Field\\Types\\PostCustomField',

			// Field Types — GravityView / GF / Source.
			'GravityView_Field_GravityView_View' => 'GravityKit\\GravityView\\Field\\Types\\GravityViewView',
			'GravityView_Field_Gravity_Forms' => 'GravityKit\\GravityView\\Field\\Types\\GravityForms',
			'GravityView_Field_Source_ID' => 'GravityKit\\GravityView\\Field\\Types\\SourceId',
			'GravityView_Field_Gravatar' => 'GravityKit\\GravityView\\Field\\Types\\Gravatar',

			// Field Types — Form / User / Advanced.
			'GravityView_Field_Hidden' => 'GravityKit\\GravityView\\Field\\Types\\Hidden',
			'GravityView_Field_Page' => 'GravityKit\\GravityView\\Field\\Types\\Page',
			'GravityView_Field_Section' => 'GravityKit\\GravityView\\Field\\Types\\Section',
			'GravityView_Field_HTML' => 'GravityKit\\GravityView\\Field\\Types\\HTML',
			'GravityView_Field_Username' => 'GravityKit\\GravityView\\Field\\Types\\Username',
			'GravityView_Field_Password' => 'GravityKit\\GravityView\\Field\\Types\\Password',
			'GravityView_Field_User_Activation' => 'GravityKit\\GravityView\\Field\\Types\\UserActivation',
			'GravityView_Field_Calculation' => 'GravityKit\\GravityView\\Field\\Types\\Calculation',
			'GravityView_Field_Multiple_Choice' => 'GravityKit\\GravityView\\Field\\Types\\MultipleChoice',
			'GravityView_Field_Captcha' => 'GravityKit\\GravityView\\Field\\Types\\Captcha',
			'GravityView_Field_Repeater' => 'GravityKit\\GravityView\\Field\\Types\\Repeater',
			'GravityView_Repeater_Field_HTML_Template' => 'GravityKit\\GravityView\\Field\\Types\\RepeaterRendererHtml',
			'GravityView_Field_Sequence' => 'GravityKit\\GravityView\\Field\\Types\\Sequence',
			'GravityView_Field_Pipe_Recorder' => 'GravityKit\\GravityView\\Field\\Types\\PipeRecorder',

			// Field Types — Workflow.
			'GravityView_Field_Workflow_Current_Status_Timestamp' => 'GravityKit\\GravityView\\Field\\Types\\WorkflowCurrentStatusTimestamp',
			'GravityView_Field_Workflow_Final_Status' => 'GravityKit\\GravityView\\Field\\Types\\WorkflowFinalStatus',
			'GravityView_Field_Workflow_Step' => 'GravityKit\\GravityView\\Field\\Types\\WorkflowStep',

			// Field Types — Notes.
			'GravityView_Field_Notes' => 'GravityKit\\GravityView\\Field\\Types\\Notes',

			// Extensions.
			'GravityView_Extension' => 'GravityKit\\GravityView\\Extension\\GravityViewExtension',

			// Extension — Edit Entry.
			'GravityView_Edit_Entry' => 'GravityKit\\GravityView\\Extension\\EditEntry\\EditEntry',
			'GravityView_Edit_Entry_Admin' => 'GravityKit\\GravityView\\Extension\\EditEntry\\EditEntryAdmin',
			'GravityView_Edit_Entry_Render' => 'GravityKit\\GravityView\\Extension\\EditEntry\\EditEntryRender',
			'GravityView_Edit_Entry_User_Registration' => 'GravityKit\\GravityView\\Extension\\EditEntry\\EditEntryUserRegistration',
			'GravityView_Edit_Entry_Locking' => 'GravityKit\\GravityView\\Extension\\EditEntry\\EditEntryLocking',

			// Extension — Delete Entry.
			'GravityView_Delete_Entry' => 'GravityKit\\GravityView\\Extension\\DeleteEntry\\DeleteEntry',
			'GravityView_Delete_Entry_Admin' => 'GravityKit\\GravityView\\Extension\\DeleteEntry\\DeleteEntryAdmin',

			// Extension — Duplicate Entry.
			'GravityView_Duplicate_Entry' => 'GravityKit\\GravityView\\Extension\\DuplicateEntry\\DuplicateEntry',

			// Extension — Lightbox.
			'GravityView_Lightbox' => 'GravityKit\\GravityView\\Extension\\Lightbox\\Lightbox',
			'GravityView_Lightbox_Provider' => 'GravityKit\\GravityView\\Extension\\Lightbox\\LightboxProvider',
			'GravityView_Lightbox_Provider_FancyBox' => 'GravityKit\\GravityView\\Extension\\Lightbox\\FancyBox',

			// Extension — Lightbox Entry.
			'GravityView_Lightbox_Entry' => 'GravityKit\\GravityView\\Extension\\LightboxEntry\\LightboxEntry',
			'GravityView_Lightbox_Entry_Request' => 'GravityKit\\GravityView\\Extension\\LightboxEntry\\LightboxEntryRequest',

			// Widgets.
			'GravityView_Widget_Page_Links' => 'GravityKit\\GravityView\\Widget\\Types\\PageLinks',
			'GravityView_Widget_Pagination_Info' => 'GravityKit\\GravityView\\Widget\\Types\\PaginationInfo',
			'GravityView_Widget_Poll' => 'GravityKit\\GravityView\\Widget\\Types\\Poll',
			'GravityView_Widget' => 'GravityKit\\GravityView\\Widget\\Types\\LegacyWidget',
			'GravityView_Widget_Custom_Content' => 'GravityKit\\GravityView\\Widget\\Types\\CustomContent',
			'GravityView_Widget_Export_Link' => 'GravityKit\\GravityView\\Widget\\Types\\ExportLink',
			'GravityView_Widget_Gravity_Forms' => 'GravityKit\\GravityView\\Widget\\Types\\GravityFormsWidget',
			'GravityView_Widget_GravityBoard' => 'GravityKit\\GravityView\\Widget\\Types\\GravityBoardWidget',

			// WordPress Widgets.
			'GravityView_Recent_Entries_Widget' => 'GravityKit\\GravityView\\Widget\\WordPress\\RecentEntriesWidget',
			'GravityView_Search_WP_Widget' => 'GravityKit\\GravityView\\Widget\\WordPress\\SearchWPWidget',

			// Search.
			'GravityView_Widget_Search' => 'GravityKit\\GravityView\\Widget\\Types\\SearchWidget',
			'GravityView_Widget_Search_Author_GF_Query_Condition' => 'GravityKit\\GravityView\\Widget\\Types\\SearchWidgetAuthorCondition',
			'GravityView_Search_Widget_Settings_Visible_Fields_Only' => 'GravityKit\\GravityView\\Widget\\Types\\SearchWidgetVisibleFieldsOnly',
			'GV\\Search\\Fields\\Search_Field' => 'GravityKit\\GravityView\\Search\\Fields\\SearchField',
			'GV\\Search\\Fields\\Search_Field_Choices' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldChoices',
			'GV\\Search\\Search_Field_Collection' => 'GravityKit\\GravityView\\Search\\SearchFieldCollection',
			'GV\\Search\\Fields\\Search_Field_All' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldAll',
			'GV\\Search\\Fields\\Search_Field_Submit' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldSubmit',
			'GV\\Search\\Fields\\Search_Field_Created_By' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldCreatedBy',
			'GV\\Search\\Fields\\Search_Field_Entry_Date' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldEntryDate',
			'GV\\Search\\Fields\\Search_Field_Entry_ID' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldEntryId',
			'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Final_Status' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldGravityFlowFinalStatus',
			'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Status_Step' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldGravityFlowStatusStep',
			'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Step' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldGravityFlowStep',
			'GV\\Search\\Fields\\Search_Field_Gravity_Forms' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldGravityForms',
			'GV\\Search\\Fields\\Search_Field_Is_Approved' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldIsApproved',
			'GV\\Search\\Fields\\Search_Field_Is_Read' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldIsRead',
			'GV\\Search\\Fields\\Search_Field_Is_Starred' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldIsStarred',
			'GV\\Search\\Fields\\Search_Field_Search_Mode' => 'GravityKit\\GravityView\\Search\\Fields\\SearchFieldSearchMode',

			// Integration Hooks — Trait.
			'GravityView_Permalink_Override_Trait' => 'GravityKit\\GravityView\\Integration\\PermalinkOverrideTrait',

			// Integration Hooks — Base.
			'GravityView_Plugin_and_Theme_Hooks' => 'GravityKit\\GravityView\\Integration\\AbstractPluginHooks',

			// Integration Hooks — Plugin.
			'GravityView_Plugin_Hooks_Gravity_Flow' => 'GravityKit\\GravityView\\Integration\\GravityFlow',
			'GravityView_Plugin_Hooks_Gravity_Forms_Chained_Selects' => 'GravityKit\\GravityView\\Integration\\GravityFormsChainedSelects',
			'GravityView_Plugin_Hooks_Gravity_Forms_Coupon' => 'GravityKit\\GravityView\\Integration\\GravityFormsCoupon',
			'GravityView_Feature_Upgrade' => 'GravityKit\\GravityView\\Integration\\FeatureUpgrade',
			'GravityView_Object_Placeholder' => 'GravityKit\\GravityView\\Integration\\ObjectPlaceholder',
			'GravityView_Plugin_Hooks_ACF' => 'GravityKit\\GravityView\\Integration\\ACF',
			'GravityView_Plugin_Hooks_All_In_One_SEO' => 'GravityKit\\GravityView\\Integration\\AllInOneSEO',
			'GravityView_Plugin_Hooks_BuddyPress' => 'GravityKit\\GravityView\\Integration\\BuddyPress',
			'GravityView_Plugin_Hooks_Code_Snippets' => 'GravityKit\\GravityView\\Integration\\CodeSnippets',
			'GravityView_Plugin_Hooks_Debug_Bar' => 'GravityKit\\GravityView\\Integration\\DebugBar',
			'GravityView_Plugin_Hooks_Give' => 'GravityKit\\GravityView\\Integration\\Give',
			'GravityView_Plugin_Hooks_Gravity_Forms' => 'GravityKit\\GravityView\\Integration\\GravityForms',
			'GravityView_Plugin_Hooks_Gravity_Forms_Advanced_Post_Creation' => 'GravityKit\\GravityView\\Integration\\GravityFormsAdvancedPostCreation',
			'GravityView_Plugin_Hooks_Gravity_Forms_Directory' => 'GravityKit\\GravityView\\Integration\\GravityFormsDirectory',
			'GravityView_Plugin_Hooks_Gravity_Forms_Dropbox' => 'GravityKit\\GravityView\\Integration\\GravityFormsDropbox',
			'GravityView_Plugin_Hooks_Gravity_Forms_Partial_Entries' => 'GravityKit\\GravityView\\Integration\\GravityFormsPartialEntries',
			'GravityView_Plugin_Hooks_Gravity_Forms_Polls' => 'GravityKit\\GravityView\\Integration\\GravityFormsPolls',
			'GravityView_Plugin_Hooks_Gravity_Forms_Quiz' => 'GravityKit\\GravityView\\Integration\\GravityFormsQuiz',
			'GravityView_Plugin_Hooks_Gravity_Forms_Signature' => 'GravityKit\\GravityView\\Integration\\GravityFormsSignature',
			'GravityView_Plugin_Hooks_Gravity_Forms_Survey' => 'GravityKit\\GravityView\\Integration\\GravityFormsSurvey',
			'GravityView_Plugin_Hooks_Gravity_PDF' => 'GravityKit\\GravityView\\Integration\\GravityPDF',
			'GravityView_Plugin_Hooks_Gravity_Perks' => 'GravityKit\\GravityView\\Integration\\GravityPerks',
			'GravityView_Plugin_Hooks_Gravity_Perks_Nested_Forms' => 'GravityKit\\GravityView\\Integration\\GravityPerksNestedForms',
			'GravityView_Plugin_Hooks_Gravity_Perks_Populate_Anything' => 'GravityKit\\GravityView\\Integration\\GravityPerksPopulateAnything',
			'GravityView_Plugin_Hooks_GravityBoard' => 'GravityKit\\GravityView\\Integration\\GravityBoard',
			'GravityView_Plugin_Hooks_GravityMaps' => 'GravityKit\\GravityView\\Integration\\GravityMaps',
			'GravityView_Plugin_Hooks_GravityView_Advanced_Filtering' => 'GravityKit\\GravityView\\Integration\\GravityViewAdvancedFiltering',
			'GravityView_Plugin_Hooks_GravityView_DataTables' => 'GravityKit\\GravityView\\Integration\\GravityViewDataTables',
			'GravityView_Plugin_Hooks_GravityView_Ratings_Reviews' => 'GravityKit\\GravityView\\Integration\\GravityViewRatingsReviews',
			'GravityView_Plugin_Hooks_Iconic_WAP' => 'GravityKit\\GravityView\\Integration\\IconicWAP',
			'GravityView_Plugin_Hooks_Image_Hopper' => 'GravityKit\\GravityView\\Integration\\ImageHopper',
			'GravityView_Plugin_Hooks_LearnDash' => 'GravityKit\\GravityView\\Integration\\LearnDash',
			'GravityView_Plugin_Hooks_Leco_Client_Portal' => 'GravityKit\\GravityView\\Integration\\LecoClientPortal',
			'GravityView_Plugin_Hooks_Members' => 'GravityKit\\GravityView\\Integration\\Members',
			'GravityView_Plugin_Hooks_Pageviews' => 'GravityKit\\GravityView\\Integration\\Pageviews',
			'GravityView_Plugin_Hooks_Query_Monitor' => 'GravityKit\\GravityView\\Integration\\QueryMonitor',
			'GravityView_Plugin_Hooks_WooCommerce' => 'GravityKit\\GravityView\\Integration\\WooCommerce',
			'GravityView_Plugin_Hooks_Wicked_Folders' => 'GravityKit\\GravityView\\Integration\\WickedFolders',
			'GravityView_Plugin_Hooks_Yoast_SEO' => 'GravityKit\\GravityView\\Integration\\YoastSEO',

			// Integration Hooks — Theme.
			'GravityView_Theme_Hooks_Avada' => 'GravityKit\\GravityView\\Integration\\Avada',
			'GravityView_Theme_Hooks_Avia' => 'GravityKit\\GravityView\\Integration\\Avia',
			'GravityView_Theme_Hooks_Church_Themes' => 'GravityKit\\GravityView\\Integration\\ChurchThemes',
			'GravityView_Theme_Hooks_Elegant_Themes' => 'GravityKit\\GravityView\\Integration\\ElegantThemes',
			'GravityView_Theme_Hooks_Elementor' => 'GravityKit\\GravityView\\Integration\\Elementor',
			'GravityView_Theme_Hooks_Formidable_Views' => 'GravityKit\\GravityView\\Integration\\FormidableViews',
			'GravityView_Theme_Hooks_GeneratePress' => 'GravityKit\\GravityView\\Integration\\GeneratePress',
			'GravityView_Theme_Hooks_Genesis' => 'GravityKit\\GravityView\\Integration\\Genesis',
			'GravityView_Theme_Hooks_Jetpack_CRM_Client_Portal_Pro' => 'GravityKit\\GravityView\\Integration\\JetpackCRMClientPortalPro',
			'GravityView_Theme_Hooks_RCP' => 'GravityKit\\GravityView\\Integration\\RCP',
			'GravityView_Theme_Hooks_SiteOrigin' => 'GravityKit\\GravityView\\Integration\\SiteOrigin',
			'GravityView_Theme_Hooks_Ultimate_Member' => 'GravityKit\\GravityView\\Integration\\UltimateMember',
			'GravityView_Theme_Hooks_WooThemes' => 'GravityKit\\GravityView\\Integration\\WooThemes',
			'GravityView_Theme_Hooks_WPML' => 'GravityKit\\GravityView\\Integration\\WPML',

			// Presets.
			'GravityView_Default_Template_Table' => 'GravityKit\\GravityView\\Preset\\DefaultTable',
			'GravityView_Default_Template_List' => 'GravityKit\\GravityView\\Preset\\DefaultList',
			'GravityView_Default_Template_Edit' => 'GravityKit\\GravityView\\Preset\\DefaultEdit',
			'GravityView_Layout_Builder' => 'GravityKit\\GravityView\\Preset\\LayoutBuilder',
			'GravityView_Preset_Business_Data' => 'GravityKit\\GravityView\\Preset\\BusinessData',
			'GravityView_Preset_Business_Listings' => 'GravityKit\\GravityView\\Preset\\BusinessListings',
			'GravityView_Preset_Event_Listings' => 'GravityKit\\GravityView\\Preset\\EventListings',
			'GravityView_Preset_Issue_Tracker' => 'GravityKit\\GravityView\\Preset\\IssueTracker',
			'GravityView_Preset_Job_Board' => 'GravityKit\\GravityView\\Preset\\JobBoard',
			'GravityView_Preset_Profiles' => 'GravityKit\\GravityView\\Preset\\Profiles',
			'GravityView_Preset_Resume_Board' => 'GravityKit\\GravityView\\Preset\\ResumeBoard',
			'GravityView_Preset_Staff_Profiles' => 'GravityKit\\GravityView\\Preset\\StaffProfiles',
			'GravityView_Preset_Website_Showcase' => 'GravityKit\\GravityView\\Preset\\WebsiteShowcase',

			// Template.
			'GravityView_Placeholder_Template' => 'GravityKit\\GravityView\\Template\\PlaceholderTemplate',

			// Shortcodes.
			'GravityView_Shortcode' => 'GravityKit\\GravityView\\Shortcode\\LegacyShortcode',
			'GravityView_Entry_Link_Shortcode' => 'GravityKit\\GravityView\\Shortcode\\LegacyEntryLinkShortcode',
			'GVLogic_Shortcode' => 'GravityKit\\GravityView\\Shortcode\\LegacyGVLogicShortcode',

			// Entry.
			'GravityView_Entry_Notes' => 'GravityKit\\GravityView\\Entry\\EntryNotes',
			'GravityView_Entry_List' => 'GravityKit\\GravityView\\Entry\\EntryList',
			'GravityView_Change_Entry_Creator' => 'GravityKit\\GravityView\\Entry\\ChangeEntryCreator',
			'GravityView_Bulk_Actions' => 'GravityKit\\GravityView\\Entry\\Approval\\GFEntriesPageBulkActions',

			// Lib — XML Parser.
			'WXR_Parser' => 'GravityKit\\GravityView\\Lib\\XmlParser\\WXRParser',
			'WXR_Parser_SimpleXML' => 'GravityKit\\GravityView\\Lib\\XmlParser\\WXRParserSimpleXML',
			'WXR_Parser_XML' => 'GravityKit\\GravityView\\Lib\\XmlParser\\WXRParserXML',
			'WXR_Parser_Regex' => 'GravityKit\\GravityView\\Lib\\XmlParser\\WXRParserRegex',

			// Lib — Enkoder.
			'StandalonePHPEnkoder' => 'GravityKit\\GravityView\\Lib\\Enkoder',
		];

		if ( ! isset( $map[ $class_name ] ) ) {
			return;
		}

		$psr4_class = $map[ $class_name ];

		// GravityView_Permalink_Override_Trait is a trait, not a class.
		if ( 'GravityView_Permalink_Override_Trait' === $class_name ) {
			if ( ! trait_exists( $psr4_class, true ) ) {
				return;
			}
		} else {
			if ( ! class_exists( $psr4_class, true ) ) {
				return;
			}
		}

		class_alias( $psr4_class, $class_name );
	} );

	/**
	 * Eagerly resolve legacy aliases that have side effects.
	 *
	 * Many legacy classes (field types, widgets, integration hooks) self-register
	 * in static registries via constructor side effects. For example, each
	 * GravityView_Field_* class file ends with `new FieldType()`, which calls
	 * `GravityView_Fields::register($this)`. Without eager loading, these files
	 * are never loaded and the registries stay empty.
	 *
	 * The GV\* namespace aliases in gv-aliases.php are purely structural and have
	 * no side effects — those are resolved lazily via SPL autoloader only.
	 *
	 * @since 3.0.0
	 */
	$legacy_alias_names = [
		// API.
		'GravityView_API',

		// View.
		'GravityView_View',

		// Admin (load before children).
		'GravityView_Admin',
		'GravityView_Admin_Views',

		// Frontend.
		'GravityView_frontend',

		// Entry / Approval.
		'GravityView_Entry_Approval',
		'GravityView_Entry_Approval_Status',
		'GravityView_Entry_Approval_Merge_Tags',

		// Support.
		'GravityView_Cache',
		'GVCommon',
		// GravityView_GFFormsModel excluded: extends \GFFormsModel (Gravity Forms), unavailable during Composer autoload.
		// The lazy SPL autoloader registered above handles it on demand.
		'GravityView_Image',
		'GravityView_Merge_Tags',
		'GravityView_Compatibility',
		'GravityView_Logging',
		'GravityView_Notifications',
		'GravityView_Powered_By',
		'GravityView_Template',
		'GravityView_oEmbed',
		'GravityView_Error_Messages',
		'GravityView_Post_Types',
		'GravityView_View_Data',
		'GravityView_HTML_Elements',
		'GravityView_Uninstall',

		// AJAX.
		'GravityView_Ajax',

		// Settings.
		'GravityView_Settings',

		// Permissions.
		'GravityView_Roles_Capabilities',

		// Admin Metaboxes.
		'GravityView_Admin_Metaboxes',
		'GravityView_Metabox_Tab',
		'GravityView_Metabox_Tabs',

		// Admin View Items (load base before children).
		'GravityView_Admin_View_Item',
		'GravityView_Admin_View_Field',
		'GravityView_Admin_View_Widget',

		// Admin Support.
		'GravityView_Support_Port',
		'GravityView_Admin_No_Conflict',
		'GravityView_Admin_Duplicate_View',

		// Admin Rendering (load base before children).
		'GravityView_Render_Settings',
		'GravityView_FieldType',
		'GravityView_FieldType_text',
		'GravityView_FieldType_number',
		'GravityView_FieldType_textarea',
		'GravityView_FieldType_select',
		'GravityView_FieldType_multiselect',
		'GravityView_FieldType_checkbox',
		'GravityView_FieldType_checkboxes',
		'GravityView_FieldType_radio',
		'GravityView_FieldType_hidden',
		'GravityView_FieldType_html',

		// Admin Other.
		'GravityView_Admin_Notices',
		'GravityView_Admin_Add_Shortcode',
		'GravityView_Admin_ApproveEntries',
		'GravityView_Welcome',
		'GravityView_Admin_Bar',
		'GravityView_GF_Entries_List',
		'GravityView_Migrate',

		// Field Types — Registry and Base (must load before field implementations).
		'GravityView_Fields',
		'GravityView_Field',

		// Field Types — Basic.
		'GravityView_Field_Text',
		'GravityView_Field_Textarea',
		'GravityView_Field_Number',
		'GravityView_Field_Email',
		'GravityView_Field_Phone',
		'GravityView_Field_Website',
		'GravityView_Field_Select',
		'GravityView_Field_Radio',
		'GravityView_Field_Checkbox',
		'GravityView_Field_MultiSelect',
		'GravityView_Field_Image_Choice',

		// Field Types — Complex / File / Media.
		'GravityView_Field_Name',
		'GravityView_Field_Address',
		'GravityView_Field_Date',
		'GravityView_Field_Time',
		'GravityView_Field_List',
		'GravityView_Field_Consent',
		'GravityView_Field_FileUpload',
		'GravityView_Field_Post_Image',

		// Field Types — Entry Metadata.
		'GravityView_Field_ID',
		'GravityView_Field_Date_Created',
		'GravityView_Field_Date_Updated',
		'GravityView_Field_Created_By',
		'GravityView_Field_Source_URL',
		'GravityView_Field_IP',

		// Field Types — GV-specific.
		'GravityView_Field_Custom',
		'GravityView_Field_Entry_Link',
		'GravityView_Field_Edit_Link',
		'GravityView_Field_Delete_Link',
		'GravityView_Field_Other_Entries',
		'GravityView_Field_Entry_Approval',
		'GravityView_Field_Is_Approved',
		'GravityView_Field_Is_Read',
		'GravityView_Field_Is_Starred',

		// Field Types — Product / Payment.
		'GravityView_Field_Product',
		'GravityView_Field_Quantity',
		'GravityView_Field_Option',
		'GravityView_Field_Shipping',
		'GravityView_Field_Total',
		'GravityView_Field_Payment_Amount',
		'GravityView_Field_Payment_Date',
		'GravityView_Field_Payment_Method',
		'GravityView_Field_Payment_Status',
		'GravityView_Field_Transaction_ID',
		'GravityView_Field_Transaction_Type',
		'GravityView_Field_CreditCard',
		'GravityView_Field_Is_Fulfilled',
		'GravityView_Field_Currency',
		'GravityView_Field_Unsubscribe',

		// Field Types — Add-on / Survey / Quiz.
		'GravityView_Field_Survey',
		'GravityView_Field_Quiz',
		'GravityView_Field_Quiz_Score',
		'GravityView_Field_Chained_Select',

		// Field Types — Post.
		'GravityView_Field_Post_Title',
		'GravityView_Field_Post_Content',
		'GravityView_Post_Excerpt',
		'GravityView_Field_Post_ID',
		'GravityView_Field_Post_Category',
		'GravityView_Field_Post_Tags',
		'GravityView_Field_Post_Custom_Field',

		// Field Types — GravityView / GF / Source.
		'GravityView_Field_GravityView_View',
		'GravityView_Field_Gravatar',

		// Field Types — Form / User / Advanced.
		'GravityView_Field_Hidden',
		'GravityView_Field_Page',
		'GravityView_Field_Section',
		'GravityView_Field_HTML',
		'GravityView_Field_Username',
		'GravityView_Field_Password',
		'GravityView_Field_User_Activation',
		'GravityView_Field_Calculation',
		'GravityView_Field_Multiple_Choice',
		'GravityView_Field_Captcha',
		'GravityView_Field_Repeater',
		'GravityView_Repeater_Field_HTML_Template',
		'GravityView_Field_Sequence',
		'GravityView_Field_Pipe_Recorder',

		// Field Types — Workflow.
		'GravityView_Field_Workflow_Current_Status_Timestamp',
		'GravityView_Field_Workflow_Final_Status',
		'GravityView_Field_Workflow_Step',

		// Field Types — Notes.
		'GravityView_Field_Notes',

		// Deprecation.
		'GravityView_Deprecated_Hook_Notices',

		// Extensions.
		'GravityView_Extension',
		'GravityView_Edit_Entry',
		'GravityView_Edit_Entry_Admin',
		'GravityView_Edit_Entry_Render',
		'GravityView_Edit_Entry_User_Registration',
		'GravityView_Edit_Entry_Locking',
		'GravityView_Delete_Entry',
		'GravityView_Delete_Entry_Admin',
		'GravityView_Duplicate_Entry',
		'GravityView_Lightbox',
		'GravityView_Lightbox_Provider',
		'GravityView_Lightbox_Provider_FancyBox',
		'GravityView_Lightbox_Entry',
		'GravityView_Lightbox_Entry_Request',

		// Widgets (load base before implementations).
		'GravityView_Widget',
		'GravityView_Widget_Page_Links',
		'GravityView_Widget_Pagination_Info',
		'GravityView_Widget_Poll',
		'GravityView_Widget_Custom_Content',
		'GravityView_Widget_Export_Link',
		'GravityView_Widget_Gravity_Forms',
		'GravityView_Widget_GravityBoard',

		// WordPress Widgets.
		'GravityView_Recent_Entries_Widget',
		'GravityView_Search_WP_Widget',

		// Search.
		'GravityView_Widget_Search',
		// GravityView_Widget_Search_Author_GF_Query_Condition excluded: extends \GF_Query_Condition (Gravity Forms).
		'GravityView_Search_Widget_Settings_Visible_Fields_Only',
		'GV\\Search\\Fields\\Search_Field',
		'GV\\Search\\Fields\\Search_Field_Choices',
		'GV\\Search\\Search_Field_Collection',
		'GV\\Search\\Fields\\Search_Field_All',
		'GV\\Search\\Fields\\Search_Field_Submit',
		'GV\\Search\\Fields\\Search_Field_Created_By',
		'GV\\Search\\Fields\\Search_Field_Entry_Date',
		'GV\\Search\\Fields\\Search_Field_Entry_ID',
		'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Final_Status',
		'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Status_Step',
		'GV\\Search\\Fields\\Search_Field_Gravity_Flow_Step',
		'GV\\Search\\Fields\\Search_Field_Gravity_Forms',
		'GV\\Search\\Fields\\Search_Field_Is_Approved',
		'GV\\Search\\Fields\\Search_Field_Is_Read',
		'GV\\Search\\Fields\\Search_Field_Is_Starred',
		'GV\\Search\\Fields\\Search_Field_Search_Mode',

		// Integration Hooks — Base (must load before implementations).
		'GravityView_Plugin_and_Theme_Hooks',

		// Integration Hooks — Plugin.
		'GravityView_Plugin_Hooks_Gravity_Flow',
		'GravityView_Plugin_Hooks_Gravity_Forms_Chained_Selects',
		'GravityView_Plugin_Hooks_Gravity_Forms_Coupon',
		'GravityView_Feature_Upgrade',
		'GravityView_Object_Placeholder',
		'GravityView_Plugin_Hooks_ACF',
		'GravityView_Plugin_Hooks_All_In_One_SEO',
		'GravityView_Plugin_Hooks_BuddyPress',
		'GravityView_Plugin_Hooks_Code_Snippets',
		'GravityView_Plugin_Hooks_Debug_Bar',
		'GravityView_Plugin_Hooks_Give',
		'GravityView_Plugin_Hooks_Gravity_Forms',
		'GravityView_Plugin_Hooks_Gravity_Forms_Advanced_Post_Creation',
		'GravityView_Plugin_Hooks_Gravity_Forms_Directory',
		'GravityView_Plugin_Hooks_Gravity_Forms_Dropbox',
		'GravityView_Plugin_Hooks_Gravity_Forms_Partial_Entries',
		'GravityView_Plugin_Hooks_Gravity_Forms_Polls',
		'GravityView_Plugin_Hooks_Gravity_Forms_Quiz',
		'GravityView_Plugin_Hooks_Gravity_Forms_Signature',
		'GravityView_Plugin_Hooks_Gravity_Forms_Survey',
		'GravityView_Plugin_Hooks_Gravity_PDF',
		'GravityView_Plugin_Hooks_Gravity_Perks',
		'GravityView_Plugin_Hooks_Gravity_Perks_Nested_Forms',
		'GravityView_Plugin_Hooks_Gravity_Perks_Populate_Anything',
		'GravityView_Plugin_Hooks_GravityBoard',
		'GravityView_Plugin_Hooks_GravityMaps',
		'GravityView_Plugin_Hooks_GravityView_Advanced_Filtering',
		'GravityView_Plugin_Hooks_GravityView_DataTables',
		'GravityView_Plugin_Hooks_GravityView_Ratings_Reviews',
		'GravityView_Plugin_Hooks_Iconic_WAP',
		'GravityView_Plugin_Hooks_Image_Hopper',
		'GravityView_Plugin_Hooks_LearnDash',
		'GravityView_Plugin_Hooks_Leco_Client_Portal',
		'GravityView_Plugin_Hooks_Members',
		'GravityView_Plugin_Hooks_Pageviews',
		'GravityView_Plugin_Hooks_Query_Monitor',
		'GravityView_Plugin_Hooks_WooCommerce',
		'GravityView_Plugin_Hooks_Wicked_Folders',
		'GravityView_Plugin_Hooks_Yoast_SEO',

		// Integration Hooks — Theme.
		'GravityView_Theme_Hooks_Avada',
		'GravityView_Theme_Hooks_Avia',
		'GravityView_Theme_Hooks_Church_Themes',
		'GravityView_Theme_Hooks_Elegant_Themes',
		'GravityView_Theme_Hooks_Elementor',
		'GravityView_Theme_Hooks_Formidable_Views',
		'GravityView_Theme_Hooks_GeneratePress',
		'GravityView_Theme_Hooks_Genesis',
		'GravityView_Theme_Hooks_Jetpack_CRM_Client_Portal_Pro',
		'GravityView_Theme_Hooks_RCP',
		'GravityView_Theme_Hooks_SiteOrigin',
		'GravityView_Theme_Hooks_Ultimate_Member',
		'GravityView_Theme_Hooks_WooThemes',
		'GravityView_Theme_Hooks_WPML',

		// Presets.
		'GravityView_Default_Template_Table',
		'GravityView_Default_Template_List',
		'GravityView_Default_Template_Edit',
		'GravityView_Layout_Builder',
		'GravityView_Preset_Business_Data',
		'GravityView_Preset_Business_Listings',
		'GravityView_Preset_Event_Listings',
		'GravityView_Preset_Issue_Tracker',
		'GravityView_Preset_Job_Board',
		'GravityView_Preset_Profiles',
		'GravityView_Preset_Resume_Board',
		'GravityView_Preset_Staff_Profiles',
		'GravityView_Preset_Website_Showcase',

		// Template.
		'GravityView_Placeholder_Template',

		// Shortcodes.
		'GravityView_Shortcode',
		'GravityView_Entry_Link_Shortcode',
		'GVLogic_Shortcode',

		// Entry.
		'GravityView_Entry_Notes',
		'GravityView_Entry_List',
		'GravityView_Change_Entry_Creator',
		'GravityView_Bulk_Actions',
		'GravityView_Entry_Batches',
		'GravityViewEntryBatches',
		'GravityView_Background_Scheduler',

		// WXR_Parser/WXR_Parser_* and StandalonePHPEnkoder are intentionally excluded: those unprefixed
		// global names are also declared by WordPress core's WXR importer and by themes/plugins bundling the
		// standalone PHPEnkoder library, so eagerly aliasing them here fatals with a class-redeclaration
		// collision. They stay in the lazy autoloader map and resolve on demand instead.
	];

	$resolve_legacy_aliases = static function () use ( $legacy_alias_names ) {
		// Resolve the trait separately (class_exists returns false for traits).
		trait_exists( 'GravityView_Permalink_Override_Trait' );

		// These field constructors depend on Gravity Forms classes (GFCommon::get_base_path() and
		// GFForms::$version respectively), so they can only be eagerly resolved when GF is already loaded.
		// The lazy SPL autoloader above still handles them on demand if triggered later.
		$conditional_field_aliases = [];

		if ( class_exists( 'GFCommon', false ) ) {
			$conditional_field_aliases[] = 'GravityView_Field_Gravity_Forms';
		}

		if ( class_exists( 'GFForms', false ) ) {
			$conditional_field_aliases[] = 'GravityView_Field_Source_ID';
		}

		if ( $conditional_field_aliases ) {
			$insert_after = array_search( 'GravityView_Field_GravityView_View', $legacy_alias_names, true );

			if ( false !== $insert_after ) {
				array_splice( $legacy_alias_names, $insert_after + 1, 0, $conditional_field_aliases );
			} else {
				$legacy_alias_names = array_merge( $legacy_alias_names, $conditional_field_aliases );
			}
		}

		foreach ( $legacy_alias_names as $alias ) {
			class_exists( $alias );
		}
	};

	if ( function_exists( 'add_action' ) ) {
		add_action( 'plugins_loaded', $resolve_legacy_aliases, 0 );
	} else {
		$resolve_legacy_aliases();
	}

	unset( $legacy_alias_names, $resolve_legacy_aliases );

}
