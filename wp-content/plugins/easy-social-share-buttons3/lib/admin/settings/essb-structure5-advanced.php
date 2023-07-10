<?php
/**
 * Advanced Settings Menu Screen
 *
 * @package EasySocialShareButtons
 * @since 3.0
 */

if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_sidebar_section_menu('advanced', 'optimization', esc_html__('Optimizations', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('advanced', 'deactivate', esc_html__('Deactivate', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('advanced', 'advanced', esc_html__('Advanced Options', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('advanced', 'integrate', esc_html__('Integrations', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('advanced', 'administrative', esc_html__('Administrative', 'essb'));
	
	if (!essb_options_bool_value('deactivate_module_translate')) {
		ESSBControlCenter::register_sidebar_section_menu('advanced', 'localization', esc_html__('Translate', 'essb'));
	}
}


ESSBOptionsStructureHelper::menu_item('advanced', 'optimization', esc_html__('Optimizations', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'advanced', esc_html__('Advanced Options', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'integrate', esc_html__('Integrations', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'administrative', esc_html__('Administrative Options', 'essb'), 'default');

if (!essb_options_bool_value('deactivate_module_translate')) {
	ESSBOptionsStructureHelper::menu_item('advanced', 'localization', esc_html__('Translate Options', 'essb'), 'default');
}

ESSBOptionsStructureHelper::help('advanced', 'optimization', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/optimizations-how-to-select-the-working-optimization-options-for-your-site/'));

$modes = array(
    '' => 'Custom setup',
    'level0' => 'No optimizations',
    'level1' => 'Cache/optimization plugin is running on the website',
    'level2' => 'No cache or optimization plugin is installed'
);

ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'optimization_level', esc_html__('Optimization mode', 'essb'), esc_html__('Select a level based on your website if you don\'t wish to configure manually all the options.', 'essb'), $modes);


ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'optimizations-css', 'optimizations-css');

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading7', esc_html__('CSS Styles Optimization', 'essb'), '', 'pb0', '<i class="ti-bolt-alt"></i>');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'essb-related-heading7 mb40', '');

ESSBOptionsStructureHelper::field_section_start_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'use_minified_css', esc_html__('Use minified CSS files', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'load_css_footer', esc_html__('Generate dynamic CSS in the footer instead of the header', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'load_css_active', esc_html__('Generate dynamic user styles for active sharing positions only', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'css_mode', esc_html__('Share buttons stylesheet type', 'essb'), esc_html__('Choose from a different type of stylesheet for the share buttons. If you choose a different than full, the plugin will automatically deactivate features excluded from the stylesheet. ', 'essb'), array('' => esc_html__('Full', 'essb'), 'slim' => esc_html__('Slim', 'essb'), 'mini' => esc_html__('Mini', 'essb')));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'dont_load_css', esc_html__('Don\'t load the plugin CSS styles', 'essb') .essb_generate_expert_badge(), esc_html__('If you enable this option plugin won\'t load any styles. You need to write your own. The plugin won\'t load also the icon font.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::holder_end('advanced', 'optimization');

ESSBOptionsStructureHelper::holder_end('advanced', 'optimization'); // optimizations-css

ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'optimizations-css-builder', 'optimizations-css-builder');

ESSBOptionsStructureHelper::holder_end('advanced', 'optimization');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'optimizations-other', 'optimizations-other');

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading7', esc_html__('Script Optimization', 'essb'), '', 'pb0', '<i class="ti-bolt-alt"></i>');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'essb-related-heading7 mb40', '');
ESSBOptionsStructureHelper::field_section_start_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'use_minified_js', esc_html__('Use minified javascript files', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'scripts_in_head', esc_html__('Load javascript files in the header of site', 'essb'), esc_html__('Use if you have scripts generating errors on your site, preventing Easy Social Share Buttons functions from work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'load_js_async', esc_html__('Load plugin javascript files asynchronous', 'essb'), esc_html__('This will load scripts during page load in non render blocking way', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'load_js_defer', esc_html__('Load plugin javascript files deferred', 'essb'), esc_html__('This will load scripts after page load in non render blocking way', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::holder_end('advanced', 'optimization'); // heading7

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading7', esc_html__('Combine CSS and Javascript files (pre-compiled mode)', 'essb'), '', 'pb0', '<i class="ti-zip"></i>');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'essb-related-heading7 mb40', '');
ESSBOptionsStructureHelper::hint('advanced', 'optimization', '', esc_html__('Combine CSS merges all your files into 1, reducing HTTP requests.', 'essb'), '', 'glowhint');
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'precompiled_resources', esc_html__('Enable combine CSS and Javascript files', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'precompiled_mode', esc_html__('Optimize', 'essb'), '', array('' => 'CSS and Javascript', 'css' => 'CSS only', 'js' => 'Javascript only'));
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'precompiled_folder', esc_html__('Cache data storage', 'essb'), '', array('' => 'WordPress Content Folder', 'uploads' => 'WordPress Uploads Folder', 'plugin' => 'Plugin Folder'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'precompiled_post', esc_html__('Generate a separate cache file for each post or page', 'essb'), esc_html__('Use this option to create a separate cache file for each post or page of the website. Usage may generate a large number of stored files in the cache folder.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'precompiled_unique', esc_html__('Generate unique filename', 'essb'), esc_html__('Generate a unique filename each time the cache is cleared. Recommended if you are using a CDN.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'precompiled_footer', esc_html__('Move loading of the CSS styles to the footer', 'essb'), esc_html__('This improves page load time by making the styles non-render-blocking. Depending on the connection speed, a flash of unstyled content (FOUC) might occur.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
/**
 * @since 7.3.2
 */
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'precompiled_preload_css', esc_html__('Load CSS asynchronously (rel=preload)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::holder_end('advanced', 'optimization'); // heading7

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading7', esc_html__('Global Optimization', 'essb'), '', 'pb0', '<i class="ti-bolt-alt"></i>');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'essb-related-heading7 mb40', '');
ESSBOptionsStructureHelper::field_section_start_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'remove_ver_resource', esc_html__('Remove plugin version from static resources', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'optimize_load', esc_html__('Load static resources on', 'essb'), esc_html__('Select where static resources or plugin will load (CSS and Javascript) - everywhere (default), on activated post types in Where to display section or on selected post/page IDs only.', 'essb'), array('' => 'Everywhere (default)', 'selected' => 'On activated post types in Where to display', 'post' => 'On selected post/page IDs'));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'optimization', 'optimize_load_id', esc_html__('Select post/page IDs', 'essb'), esc_html__('Post/page numeric IDs (separated with comma). Example: 1,2,103', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::holder_end('advanced', 'optimization'); // heading7

/**
 * @deprecated 
 * @since 8.0
 * 
$cache_plugin_detected = "";
if (ESSBCacheDetector::is_cache_plugin_detected()) {
	ESSBOptionsStructureHelper::hint('advanced', 'optimization', esc_html__('Cache plugin detected: ', 'essb').ESSBCacheDetector::cache_plugin_name(), esc_html__('Easy Social Share Buttons for WordPress detect that you are using cache plugin on your site. Activation of any of options inside build in cache may lead to visual issues or missing share buttons. Please use them with caution', 'essb'), 'fa32 ti-info-alt', 'orange');
}*/

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading7', esc_html__('Internal plugin cache', 'essb') . essb_generate_expert_badge(), '', 'pb0', '<i class="fa21 fa fa-rocket"></i>');
ESSBOptionsStructureHelper::holder_start('advanced', 'optimization', 'essb-related-heading7 mb40', '');
ESSBOptionsStructureHelper::hint('advanced', 'optimization', '', esc_html__('Expert only settings - use with caution. Don\'t use it if you have the "Combine CSS And Javascript Files" enabled or if your website uses a cache/optimization plugin. The internal plugin cache stores a separate version of CSS, Javascript and buttons render for each button instance on a post or page. The internal cache will require a lot of space on the host and it will generate a large number of inodes (depends on the number of posts and button instances).', 'essb'), '', 'glowhint');

ESSBOptionsStructureHelper::panel_start('advanced', 'optimization', esc_html__('Internal plugin cache components', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));

ESSBOptionsStructureHelper::field_section_start_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_runtime', esc_html__('Activate WordPress persistant cache', 'essb'), esc_html__('Activating WordPress cache function usage will cache button generation via default WordPress cache or via the persistant cache plugin if you use such (like W3 Total Cache)', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache', esc_html__('Activate plugin cache', 'essb'), esc_html__('To clear the cache you can press the link in the top section of settings. The cache is also automatically cleared each time you save plugin settings.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
$cache_mode = array ("" => "Cache button render and dynamic resources", "resource" => "Cache only dynamic resources", "buttons" => "Cache only buttons render" );
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'essb_cache_mode', esc_html__('Cache mode', 'essb'), '', $cache_mode);
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_static', esc_html__('Combine into single file all plugin static CSS files', 'essb'), esc_html__('This option will combine all plugin static CSS files into single file.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_static_js', esc_html__('Combine into single file all plugin static javascript files', 'essb'), esc_html__('This option will combine all plugin static javacsript files into single file. This option will not work if scripts are set to load asynchronous or deferred.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('advanced', 'optimization');
ESSBOptionsStructureHelper::panel_end('advanced', 'optimization');
ESSBOptionsStructureHelper::holder_end('advanced', 'optimization'); // heading7

ESSBOptionsStructureHelper::holder_end('advanced', 'optimization');

ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'display_deactivate_on', esc_html__('Deactivate plugin on', 'essb'), esc_html__('Deactivate buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts. Plugin also allows to deactivate only specific functions on selected page/post ids.', 'essb'));
ESSBOptionsStructureHelper::field_textarea('advanced', 'deactivate', 'url_deactivate_full', esc_html__('Specify URLs of pages or posts where the plugin will not appear ', 'essb'), esc_html__('The option will deactivate all plugin functions. One per line without the domain name. Use (.*) wildcards to address multiple URLs under a given path. Example: /profile/(.*)', 'essb'), '10');
ESSBOptionsStructureHelper::field_switch('advanced', 'deactivate', 'deactivate_mobile', esc_html__('Deactivate plugin on mobile', 'essb') . essb_generate_server_side_mobile_badge(), esc_html__('Deactivate completely plugin work on mobile. The option requires server-side mobile detection to work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_component('advanced', 'deactivate', 'essb5_additional_deactivate_options', 'false');


ESSBOptionsStructureHelper::help('advanced', 'advanced', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/advanced-plugin-settings-integrations/#Advanced_Plugin_Settings'));
ESSBOptionsStructureHelper::field_textbox('advanced', 'advanced', 'priority_of_buttons', esc_html__('Change default priority of buttons', 'essb'), esc_html__('Provide custom value of priority when buttons will be included in content (default is 10). This will make code of plugin to execute before or after another plugin. Attention! Providing incorrect value may cause buttons not to display.', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_avoid_nonmain', esc_html__('Prevent buttons from appearing on non associated parts of content', 'essb'), esc_html__('Very rare you may see buttons appearing on not associated parts of content. Activate this option to prevent it.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

ESSBOptionsStructureHelper::panel_start('advanced', 'advanced', esc_html__('Use previously stored data from old versions', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle", 'state' => 'closed', 'css_class' => 'essb-auto-open'));
ESSBOptionsStructureHelper::hint('advanced', 'advanced', '', esc_html__('The options below include support for data stored in past formats by the plugin. They will enable automatically in the process of the plugin update. And go again to No as soon as you migrate to the new format. In case of need, you can enable them back again at any time.', 'essb'), '', 'glowhint');
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'legacy_shorturl_cache', esc_html__('Short URLs stored in the post meta', 'essb'), esc_html__('Activate this option if you use multi-page posts and wish to share only main page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'advanced');


ESSBOptionsStructureHelper::panel_start('advanced', 'advanced', esc_html__('Plugin does not share correct data', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle", 'state' => 'closed', 'css_class' => 'essb-auto-open'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'avoid_nextpage', esc_html__('Avoid &lt;!--nextpage--&gt; and always share main post address', 'essb'), esc_html__('Activate this option if you use multi-page posts and wish to share only main page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_wp_query_postid', esc_html__('Force get of current post/page', 'essb'), esc_html__('Activate this option if share doest not get correct page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'reset_postdata', esc_html__('Reset WordPress loops', 'essb'), esc_html__('Activate this option if plugin does not detect properly post permalink.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_wp_fullurl', esc_html__('Allow usage of query string parameters in share address', 'essb'), esc_html__('Activate this option to allow usage of query string parameters in url (there are plugins that use this).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_archive_pages', esc_html__('Correct shared data when sidebar, top bar, bottom bar, pop up or fly in are used in archive pages', 'essb'), esc_html__('Enable if you see wrong shared information on the archive pages.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_archive_pages_content', esc_html__('Correct shared data when static content buttons are used in archive pages', 'essb'), esc_html__('Enable if you see wrong shared information on the archive pages.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'always_use_http', esc_html__('Make plugin share always http version of page', 'essb') . essb_generate_expert_badge(), esc_html__('When you migrate from http to https all social share counters will go down to zero (0) because social networks count shares by the unique address of post/page. Making this will allow plugin always to use post/page http version of address.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'advanced');

ESSBOptionsStructureHelper::panel_start('advanced', 'advanced', esc_html__('Advanced Modifications of Core Features', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle", 'state' => 'closed', 'css_class' => 'essb-auto-open'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'float_onsingle_only', esc_html__('Float display methods on single posts/pages only', 'essb'), esc_html__('Plugin will check and display float from top and post vertical float only when a single post/page is being displayed. In all other case method will be replaced with display method top.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'hide_preview_share', esc_html__('Don\'t show on preview mode', 'essb'), esc_html__('Enable option if you need to hide share buttons on the preview mode of pages. The option will affect only automated share button locations - it will not hide those buttons generated with shortcode, widget, etc.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'reset_posttype', esc_html__('Duplicate check to avoid buttons appear on not associated post types', 'essb'), esc_html__('Activate this option if buttons appear on post types that are not marked as active.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'counter_curl_fix', esc_html__('Fix counter problem with limited cURL configuration', 'essb'), esc_html__('Activate this option if have troubles displaying counters for networks that do not have native access to counter API (ex: Google). To make it work you also need to activate in Display Settings -> Counters to load with WordPress admin ajax function..', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'deactivate_fa', esc_html__('Do not load FontAwsome', 'essb'), esc_html__('Activate this option if your site already uses Font Awesome font.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'use_rel_me', esc_html__('Add rel="me" instead of rel="nofollow" to social share buttons', 'essb'), esc_html__('Activate this option if your SEO strategy requires this. Default is nofollow which is suggested value.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'use_rel_noopener', esc_html__('Add rel="noopener" instead of rel="nofollow" to social share buttons', 'essb'), esc_html__('This will wrap the rel with "noreferrer noopener".', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'deactivate_bottom_mark', esc_html__('Deactivate generation of bottom content mark', 'essb'), esc_html__('This option will stop generation of hidden element which allows plugin to find the exact content end used in display methods that needs. Set to Yes if you see a visual problem with white space areas appearing on site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'user_link_notitle', esc_html__('Don\'t generate sharing links title', 'essb'), esc_html__('This option will remove the appearing on the hover title "Share on Network".', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::panel_end('advanced', 'advanced');


ESSBOptionsStructureHelper::panel_start('advanced', 'advanced', esc_html__('Clean buttons from excerpts', 'essb') .essb_generate_expert_badge(), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'apply_clean_buttons', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
$methods = array ("default" => "Clean network texts", "actionremove" => "Remove entire action", "clean2" => "Smart clean network texts", "remove2" => "Show buttons only on mail query" );
ESSBOptionsStructureHelper::field_select('advanced', 'advanced', 'apply_clean_buttons_method', esc_html__('Clean method', 'essb'), esc_html__('Choose method of buttons clean.', 'essb'), $methods);
ESSBOptionsStructureHelper::panel_end('advanced', 'advanced');

ESSBOptionsStructureHelper::panel_start('advanced', 'advanced', esc_html__('Additional URL and Message encoding', 'essb') . essb_generate_expert_badge(), '', 'fa21 fa fa-cogs', array("mode" => "toggle", 'state' => 'closed'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_url', esc_html__('Use encoded version of url for sharing', 'essb'), esc_html__('Activate this option to encode url used for sharing. This is option is recommended when you notice that parts of shared url are missing - usually when additional options are used like campaign tracking.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_text', esc_html__('Use encoded version of texts for sharing', 'essb'), esc_html__('Activate this option to encode texts used for sharing. You need to use this option when you have special characters which does not appear in share.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_text_plus', esc_html__('Fix problem with appearing + in text when shared via mobile', 'essb'), esc_html__('Activate this option to fix the problem with + sign that appears in share description (usually in Tweet when Twitter App is used).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'advanced');

ESSBOptionsStructureHelper::help('advanced', 'integrate', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/advanced-plugin-settings-integrations/#Integrations'));

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('WordPress', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'classic_editor_disable_buttons', esc_html__('Remove shortcode buttons from the Classic Editor', 'essb'), esc_html__('Remove the plugin shortcode generator button from the editing post screen.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'classic_editor_disable_plugins', esc_html__('Remove editor plugins from the Classic Editor', 'essb'), esc_html__('The option will disable loading the additional editor plugins created by the plugin for functions like Click to Tweet, Pinterest Pro, etc. When plugins are disabled you can generate the shortcode using the plugin shortcode generator and paste it.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'deactivate_column_shares', esc_html__('Remove shares column from posts list', 'essb'), esc_html__('Remove the shares column from the administrative list of posts. The value of the column is generated from the update of shares when the counter is used.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'deactivate_column_shareinfo', esc_html__('Remove the setup share options column from the post list', 'essb'), esc_html__('Remove the column showing the customized sharing options done on each post inside the plugin fields.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Block Editor', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'gutenberg_disable_pinterenst', esc_html__('Deactivate image block integration with Pinterest', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'gutenberg_disable_blocks', esc_html__('Deactivate plugin blocks', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Elementor', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'using_elementor', esc_html__('I am using Elementor page builder', 'essb'), esc_html__('Enable the option if your post share customizations disappear when you build or edit content with the Elementor page builder.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'using_elementor_events', esc_html__('Use Elementor page builder content events', 'essb'), esc_html__('Enable the option to attach content only share buttons to the Elementor content events. The option may be used if the share buttons don\'t appear on generated with Elementor posts or pages.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'remove_elementor_widgets', esc_html__('Don\'t load plugin Elementor widgets', 'essb'), esc_html__('Enable the option to stop the plugin from loading Elementor widgets.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'hide_buttons_elementor_edit', esc_html__('Don\'t show share buttons in Elementor design mode', 'essb'), esc_html__('Enable the option to hide site-wide social share buttons in Elementor edit mode.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Yoast SEO', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'deactivate_pair_yoast_sso', esc_html__('Deactivate Yoast Social Tags Integration', 'essb'), esc_html__('By default when Yoast SEO plugin is detected, Easy Social Share Buttons loads all customizations in the Social Media settings you have made in Yoast SEO in the share message and share optimization. Enable this option to stop the automatic integration.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'deactivate_pair_yoast_seo', esc_html__('Deactivate Yoast SEO Data Integration ', 'essb'), esc_html__('By default when Yoast SEO plugin is detected, Easy Social Share Buttons loads all customizations in the SEO settings you have made in Yoast SEO in the share message and share optimization. Enable this option to stop the automatic integration.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Rank Math', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'rankmath_og_deactivate', esc_html__('Deactivate Rank Math social share optimization tags', 'essb'), esc_html__('If you are using Rank Math plugin and need to show social share optimization tags from Easy Social Share Buttons for WordPress, activate this option. Rank Math does not have option to deactivate the tags from Rank Math settings.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('CDN Support', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'integrate', 'cdn_domain', esc_html__('CDN URL', 'essb'), esc_html__('Example: https://cdn.example.com', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_cdn_sso', esc_html__('Enable CDN support for social media optimization images', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_cdn_pinterest', esc_html__('Enable CDN support for custom Pinterest images', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('WPML & Polylang', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'deactivate_multilang', esc_html__('Deactivate automated WPML & Polylang bridge', 'essb'), esc_html__('When WPML or Polylang is found in the current WordPress setup plugin will setup a multilangual setup fields. This with version change may cause a problem in settings work. If such appear please activate this option temporary', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');


ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Social Warfare', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_sw_bridge', esc_html__('Use previous data set in Social Warfare', 'essb'), esc_html__('If you use in past Social Warfare and you have a customizations made in social sharing than you can activate this option and allow plugin read all that stored values.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('Social Snap', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_ss_bridge', esc_html__('Use previous data set in Social Snap', 'essb'), esc_html__('You migrate from this plugin, enable the option to automatically detect and used previous customizations on posts (social media image, custom tweets, Pin image).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('MashShare', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_ms_bridge', esc_html__('Use previous data set in MashShare', 'essb'), esc_html__('You migrate from this plugin, enable the option to automatically detect and used previous customizations on posts (social media image, custom tweets, Pin image).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'activate_ms_fbcount_bridge', esc_html__('Use Facebook share counter values', 'essb'), esc_html__('Read and check the previously-stored Facebook counters in MashShare. If they are greater than the current value plugin will show those.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');

ESSBOptionsStructureHelper::panel_start('advanced', 'integrate', esc_html__('AddThis', 'essb'), '', 'fa21 fa fa-plug', array("mode" => "toggle", 'state' => 'closed', "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('advanced', 'integrate', 'cache_counter_addthis', esc_html__('Load AddThis internal share counters', 'essb'), esc_html__('Set this option to Yes if you have a used AddThis. The option will call the AddThis API to display the total number internal shares. As there is no network based split the value will be added only to the total counter. Due to AddThis restrictions the import will not work if you set to Yes the option "Speed Up Process Of Counters Update"', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'integrate');


ESSBOptionsStructureHelper::help('advanced', 'administrative', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/administrative-options-disable-specific-features-and-limit-access/'));
ESSBOptionsStructureHelper::panel_start('advanced', 'administrative', esc_html__('Administrative Tools', 'essb'), '', 'fa21 fa fa-times', array("mode" => "toggle", 'state' => 'opened'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'deactivate_ajaxsubmit', esc_html__('Deactivate AJAX save of settings', 'essb'), esc_html__('Enable this option if you have a problem with dynamic settings save.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'disable_settings_rollback', esc_html__('Don\'t save history of settings change', 'essb'), esc_html__('The plugin stores up to 10 previous versions of settings (history). Enable this option to deactivate this feature.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'deactivate_appscreo', esc_html__('Remove the admin dashboard widget added by the plugin', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'live_customizer_disabled', esc_html__('Turn off front end quick plugin setup', 'essb'), esc_html__('The front end quick setup is limited for usage by administrators only. Activate this option if you wish to remove it completely.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'disable_translation', esc_html__('Do not load translations of interface', 'essb'), esc_html__('All plugin translations are made with love from our customers. If you do not wish to use it activate this option and plugin will load with default English language.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'deactivate_updates', esc_html__('Stop automatic updates', 'essb'), esc_html__('Registered versions of plugin do an automated check for updates using the WordPress update. The update happens from external server and in case your host does not allow that you can set this option to Yes and do a manual plugin updates.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'deactivate_helphints', esc_html__('Deactivate internal help hints', 'essb'), esc_html__('Inside plugin you have a help hint sections that provide useful links to the knowledge base. If you already know the features and that panel bothers you just hit Yes to hide them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'uninstall_data', esc_html__('Clean uninstall', 'essb') . essb_generate_expert_badge(), esc_html__('Permanently remove all plugin stored data upon uninstall. This also includes the custom networks, positions, analytics, internal counters, or any other information used or cached by the plugin.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

ESSBOptionsStructureHelper::panel_end('advanced', 'administrative');


ESSBOptionsStructureHelper::panel_start('advanced', 'administrative', esc_html__('Plugin Settings Access', 'essb'), '', 'fa21 fa fa-key', array("mode" => "toggle", 'state' => 'opened'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'disable_adminbar_menu', esc_html__('Remove plugin menu from top bar', 'essb'), esc_html__('Enable this option to remove the plugin menu from the WordPress admin top bar.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
$listOfOptions = array("manage_options" => "Administrator", "delete_pages" => "Editor", "publish_posts" => "Author", "edit_posts" => "Contributor");
ESSBOptionsStructureHelper::field_select('advanced', 'administrative', 'essb_access', esc_html__('Plugin settings access', 'essb'), esc_html__('Make settings available for the following user roles (if you use multiple user roles on your site we recommend to select Administrator to disallow other users change settings of the plugin).', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'limit_editor_fields', esc_html__('Limit post/page settings access', 'essb'), esc_html__('Set to Yes if you need to limit the default editing components visibility on posts/pages.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('advanced', 'administrative', 'limit_editor_fields_access', esc_html__('User access role', 'essb'), esc_html__('Select the role that user should have to access the setup of fields. But only when the previous option is active.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'disable_meta_editor_fields', esc_html__('Fully deactivate plugin editing metaboxes', 'essb'), esc_html__('Set to Yes if you need to completely remove the plugin metaboxes inlcuding those that are required for sharing personalization.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'always_load_meta_editor_fields', esc_html__('Always load share customization editing no matter of selected post types', 'essb'), esc_html__('The share customization fields appear automatically on assigned posts. If you use shortcodes on post types not associated you need this option to have access to share customization fields.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'administrative');

ESSBOptionsStructureHelper::panel_start('advanced', 'administrative', esc_html__('Metabox visibiltiy', 'essb'), '', 'fa21 fa fa-eye', array("mode" => "toggle", 'state' => 'opened'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_advanced_box', esc_html__('Remove post advanced visual settings metabox', 'essb'), esc_html__('Activation of this option will remove the advanced meta box on each post that allow customizations of visual styles for post.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_optimize_box', esc_html__('Remove post share customization metabox', 'essb'), esc_html__('Activation of this option will remove the share customization meta box on each post (allows changing social share optimization tags, customize share and etc.).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_stats_box', esc_html__('Remove post detailed stats metabox', 'essb'), esc_html__('Activation of this option will remove the detailed stats meta box from each post/page when social share analytics option is activated.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_main_box', esc_html__('Remove post plugin deactivation box', 'essb'), esc_html__('Set this to Yes if you wish to remove the post metabox fields for plugin deactivation.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('advanced', 'administrative');

ESSBOptionsStructureHelper::field_func('advanced', 'administrative', 'essb3_reset_postdata', esc_html__('Reset Plugin Settings & Clear Data', 'essb'), esc_html__('Warning! Pressing any of buttons will reset/clear data stored by plugin. Once action is completed the data can be restored only if you have made a backup before.', 'essb'));

if (!essb_option_bool_value('deactivate_module_translate')) {
	ESSBOptionsStructureHelper::help('advanced', 'localization', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/translating-in-your-language-texts-generated-from-plugin/'));	

	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Copy Link Button Messages', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_copy_message1', esc_html__('Press to copy the link', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_copy_message2', esc_html__('Copied to clipboard.', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_copy_message3', esc_html__('Please use Ctrl/Cmd+C to copy the URL.', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');
	
	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Mail form texts', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_title', esc_html__('Share this with a friend', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_email', esc_html__('Your Email', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_name', esc_html__('Your Name', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_recipient', esc_html__('Recipient Email', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_custom', esc_html__('Custom user message', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_cancel', esc_html__('Cancel', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_send', esc_html__('Send', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_captcha', esc_html__('Fill in captcha code text', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_sent', esc_html__('Message sent!', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_invalid_captcha', esc_html__('Invalid Captcha code!', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_error_send', esc_html__('Error sending message!', 'essb'), '');

	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_error_mail', esc_html__('Invalid recepient email', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_error_fill', esc_html__('Please fill all fields in form!', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_sending', esc_html__('Sending to', 'essb'), '');
	//

	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');

	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Love this button messages', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_love_thanks', esc_html__('Thank you for loving this.', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_love_loved', esc_html__('You already love this today.', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');

	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Subscribe forms', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_subscribe_invalidemail', esc_html__('Invalid email address', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');


	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Custom texts that will appear on button hover', 'essb'), '', 'fa ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_share_on_prefix', esc_html__('Share on', 'essb'), '');
	essb3_prepare_texts_on_button_hover('advanced', 'localization');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');

	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('Click to Tweet', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_clicktotweet', esc_html__('Translate Click To Tweet text', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');

	ESSBOptionsStructureHelper::panel_start('advanced', 'localization', esc_html__('After Share Events', 'essb'), '', 'ti-world fa21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_as_popular_title', esc_html__('Popular Posts Title', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_as_popular_shares', esc_html__('Popular Posts shares text', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('advanced', 'localization');
}

add_action('admin_init', 'essb3_register_settings_by_posttypes');
function essb3_register_settings_by_posttypes() {
	global $wp_post_types;

	if (essb_option_value('functions_mode') != 'light' && !essb_option_bool_value('deactivate_settings_post_type')) {
		ESSBControlCenter::register_sidebar_section_menu('advanced', 'advancedpost', esc_html__('Settings by Post Type', 'essb'));
		
		$all_posttypes = array();
		
		$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
		$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
		$first_post_type = "";
		$key = 1;
		foreach ( $pts as $pt ) {
			$all_posttypes[$pt] = $wp_post_types [$pt]->label;
		}

		foreach ( $cpts as $cpt ) {
			$all_posttypes[$pt] = $wp_post_types [$cpt]->label;
		}
		
		ESSBControlCenter::register_sidebar_section_menu_sub('advanced', 'advancedpost', 'advancedpost_menu', array('type' => 'menu', 'value' => $all_posttypes));				
		foreach ($all_posttypes as $pt => $label) {
			essb_prepare_location_advanced_customization ( 'advanced', 'advancedpost|'.$pt, 'post-type-'.$pt, true, $label );
		}
	}

	if (!essb_options_bool_value('deactivate_method_integrations')) {
		ESSBControlCenter::register_sidebar_section_menu('advanced', 'advancedmodule', esc_html__('Settings for Plugin Integration', 'essb'));
		$all_posttypes = array();
		$all_posttypes['woocommerce'] = 'WooCommerce';
		$all_posttypes['wpecommerce'] = 'WP e-Commerce';
		$all_posttypes['jigoshop'] = 'JigoShop';
		$all_posttypes['ithemes'] = 'iThemes Exchange';
		$all_posttypes['bbpress'] = 'bbPress';
		$all_posttypes['buddypress'] = 'BuddyPress';
		ESSBOptionsStructureHelper::menu_item ( 'advanced', 'advancedmodule', esc_html__ ( 'Settings for Plugin Integration', 'essb' ), 'default', 'activate_first', 'advancedmodule-1' );
		ESSBControlCenter::register_sidebar_section_menu_sub('advanced', 'advancedmodule', 'advancedmodule_menu', array('type' => 'menu', 'value' => $all_posttypes));
		foreach ($all_posttypes as $pt => $label) {
			essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule|'.$pt, 'post-type-'.$pt, true, $label );
		}
	}
}

function essb3_prepare_texts_on_button_hover($tab_id, $menu_id) {
	global $essb_networks;

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}

	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'hovertext'.'_'.$key, $text, '');
	}
}

function essb3_reset_postdata() {
	echo '<div class="mb15">';
	echo '<a href="#" class="essb-btn essb-btn-red essb-reset-settings" data-clear="resetsettings" data-title="Plugin Settings">'.esc_html__('I want to reset plugin settings to default', 'essb').'</a>';
	echo '</div>';

	echo '<div class="mb15">';
	echo '<a href="#" class="essb-btn essb-btn-red essb-reset-settings" data-clear="all" data-title="All Plugin Stored Data">'.esc_html__('I want to remove all stored plugin data', 'essb').'</a>';
	echo '</div>';
}

function essb5_stylebuilder_select() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('User CSS style builder', 'essb'),
			esc_html__('The CSS style builder will let you join and use only selected styles that plugin has. In case you need to fully load custom styles than you can activate the style builder but not select an option. The style builder option should not be used in combination with Pre-compiled Mode or Built-in Cache.', 'essb'),
			'style-builder', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', esc_html__('CSS Style Builder', 'essb'));
	
}

function essb_generate_optimization_modes($options) {
    
}