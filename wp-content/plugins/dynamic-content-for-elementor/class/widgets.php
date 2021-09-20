<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Widgets Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class Widgets
{
    public static $widgets_info = [];
    public static $registered_widgets = [];
    public static $grouped_widgets = [];
    public static $groups;
    public static $namespace = '\\DynamicContentForElementor\\Widgets\\';
    public function __construct()
    {
        self::$widgets_info = self::init_widgets_info();
        self::$grouped_widgets = self::get_widgets_by_group();
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories'], 20);
    }
    public static function init_group()
    {
        self::$groups = ['ACF' => __('Advanced Custom Fields', 'dynamic-content-for-elementor'), 'CONTENT' => __('Content', 'dynamic-content-for-elementor'), 'CREATIVE' => __('Creative', 'dynamic-content-for-elementor'), 'DEV' => __('Developer', 'dynamic-content-for-elementor'), 'DYNAMIC' => __('Dynamic', 'dynamic-content-for-elementor'), 'INTERFACE' => __('Interface', 'dynamic-content-for-elementor'), 'LIST' => __('List', 'dynamic-content-for-elementor'), 'PODS' => 'Pods', 'POST' => __('Post', 'dynamic-content-for-elementor'), 'SVG' => 'SVG', 'TOOLSET' => 'Toolset', 'WEBGL' => 'WEBGL'];
    }
    public static function get_widget_info($widget = '')
    {
        return self::$widgets_info[$widget];
    }
    public static function get_widgets_info()
    {
        return self::$widgets_info;
    }
    public static function init_widgets_info()
    {
        $widgets_info = [];
        $widgets_info['DCE_Widget_Acf'] = ['category' => 'ACF', 'name' => 'dyncontel-acf', 'title' => __('ACF Fields', 'dynamic-content-for-elementor'), 'description' => __('Add a custom field created with Advanced Custom Fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-acffields', 'plugin_depends' => ['acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-fields/', 'keywords' => ['Advanced Custom Fields']];
        $widgets_info['DCE_Widget_Gallery'] = ['category' => 'ACF', 'name' => 'dyncontel-acfgallery', 'title' => __('ACF Gallery', 'dynamic-content-for-elementor'), 'description' => __('Use images from an ACF Gallery field and display them in a variety of formats', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-grid', 'plugin_depends' => ['advanced-custom-fields-pro' => 'acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-gallery/', 'keywords' => ['Advanced Custom Fields', 'gallery', 'fields', 'images', 'image']];
        $widgets_info['DCE_Widget_Relationship'] = ['category' => 'ACF', 'name' => 'dyncontel-acf-relation', 'title' => __('ACF Relationship', 'dynamic-content-for-elementor'), 'description' => __('Use the ACF Relationship field to easily display related content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-relation', 'plugin_depends' => ['acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-relationship/', 'keywords' => ['Advanced Custom Fields', 'fields']];
        $widgets_info['DCE_Widget_Repeater'] = ['category' => 'ACF', 'legacy' => \true, 'name' => 'dyncontel-acf-repeater', 'title' => __('ACF Repeater (old version)', 'dynamic-content-for-elementor'), 'description' => __('Take advantage of the power and flexibility of the ACF Repeater field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-repeater', 'plugin_depends' => ['advanced-custom-fields-pro' => 'acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-repeater-fields/', 'keywords' => ['Advanced Custom Fields']];
        $widgets_info['Acf_Repeater_V2'] = ['category' => 'ACF', 'name' => 'dce-acf-repeater-v2', 'title' => __('ACF Repeater', 'dynamic-content-for-elementor'), 'description' => __('Take advantage of the power and flexibility of the ACF Repeater field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-repeater', 'plugin_depends' => ['advanced-custom-fields-pro' => 'acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-repeater-fields/', 'keywords' => ['Advanced Custom Fields']];
        $widgets_info['Acf_Flexible_Content'] = ['category' => 'ACF', 'name' => 'dce-flexible-content', 'title' => __('ACF Flexible Content', 'dynamic-content-for-elementor'), 'description' => __('Insert a Flexible Content field using layouts and sub fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-flexiblecontent', 'plugin_depends' => ['advanced-custom-fields-pro' => 'acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-flexible-content/', 'keywords' => ['Advanced Custom Fields']];
        $widgets_info['DCE_Widget_Slider'] = ['category' => 'ACF', 'name' => 'dyncontel-acfslider', 'title' => __('ACF Slider', 'dynamic-content-for-elementor'), 'description' => __('Use images from an ACF Gallery field to create an image carousel', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-slider', 'plugin_depends' => ['advanced-custom-fields-pro' => 'acf'], 'doc_url' => 'https://www.dynamic.ooo/widget/acf-slider/', 'keywords' => ['Advanced Custom Fields', 'gallery', 'images', 'carousel']];
        $widgets_info['DCE_Widget_BarCode'] = ['category' => 'CONTENT', 'name' => 'dce_barcode', 'title' => __('QR & Barcodes', 'dynamic-content-for-elementor'), 'description' => __('Quick creation for 1D and 2D barcodes, like EAN and QR Codes', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-qrcode', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/qr-and-bars-code/', 'keywords' => ['barcode', 'qr']];
        $widgets_info['DCE_Widget_Calendar'] = ['category' => 'CONTENT', 'name' => 'dce_add_to_calendar', 'title' => __('Add to Calendar', 'dynamic-content-for-elementor'), 'description' => __('Add an event to Google Calendar, iOS Calendar, Outlook and others', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-buttoncalendar', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/button-calendar/', 'keywords' => ['date', 'calendar', 'ics', 'reminder']];
        $widgets_info['DCE_Widget_Clipboard'] = ['category' => 'CONTENT', 'name' => 'dce-copy-to-clipboard', 'title' => __('Copy to Clipboard', 'dynamic-content-for-elementor'), 'description' => __('Copy the specified text to the User Clipboard', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-buttoncopy', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/button-copy-to-clipboard/'];
        $widgets_info['DCE_Widget_DynamicCookie'] = ['category' => 'CONTENT', 'name' => 'dce-dynamiccookie', 'title' => __('Dynamic Cookie', 'dynamic-content-for-elementor'), 'description' => __('Create and manage cookies using dynamic content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-cookie', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-cookie'];
        $widgets_info['DCE_Widget_Favorites'] = ['category' => 'CONTENT', 'name' => 'dce-add-to-favorites', 'title' => __('Add to Favorites', 'dynamic-content-for-elementor'), 'description' => __('Create a favorite list for your users', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-like', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/add-to-favorites/', 'keywords' => ['favourites']];
        $widgets_info['DCE_Widget_ModalWindow'] = ['category' => 'CONTENT', 'name' => 'dyncontel-modalwindow', 'title' => __('Fire Modal Window', 'dynamic-content-for-elementor'), 'description' => __('Add a Modal Window button to your pages to create a popup window', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-modal', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/fire-modalwindow/', 'keywords' => ['popup', 'pop-up', 'modal']];
        $widgets_info['DCE_Widget_Pdf'] = ['category' => 'CONTENT', 'name' => 'dce_pdf_button', 'title' => __('PDF Button', 'dynamic-content-for-elementor'), 'description' => __('Dynamically generate a PDF document using your content and styles', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-buttonpdf', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/pdf-button/'];
        $widgets_info['DCE_Widget_PopUp'] = ['category' => 'CONTENT', 'name' => 'dyncontel-popup', 'title' => __('Modals', 'dynamic-content-for-elementor'), 'description' => __('Add a modal window to your page', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-popups', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/popups/', 'keywords' => ['popup', 'pop-up']];
        $widgets_info['DCE_Widget_Template'] = ['category' => 'CONTENT', 'name' => 'dyncontel-template', 'title' => __('Dynamic Template', 'dynamic-content-for-elementor'), 'description' => __('Insert an existing template and dynamically assign its content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-layout', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-template/'];
        $widgets_info['DCE_Widget_Tokens'] = ['category' => 'CONTENT', 'name' => 'dce-tokens', 'title' => __('Text Editor with Tokens', 'dynamic-content-for-elementor'), 'description' => __('Add Tokens to show values from posts, users, terms, custom fields, options and others', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-tokens', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/tokens/'];
        $widgets_info['DCE_Widget_AnimateText'] = ['category' => 'CREATIVE', 'name' => 'dyncontel-animateText', 'title' => __('Animated Text', 'dynamic-content-for-elementor'), 'description' => __('Advanced animation for your text', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-animate_text', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/animated-text/', 'keywords' => ['animations']];
        $widgets_info['DCE_Widget_Parallax'] = ['category' => 'CREATIVE', 'name' => 'dyncontel-parallax', 'title' => __('Parallax', 'dynamic-content-for-elementor'), 'description' => __('Manage parallax movements with your mouse on desktop or device orientation on mobile', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-parallax', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/parallax/'];
        $widgets_info['DCE_Widget_ThreesixtySlider'] = ['category' => 'CREATIVE', 'name' => 'dyncontel-threesixtyslider', 'title' => __('360 Slider', 'dynamic-content-for-elementor'), 'description' => __('Generate a rotation through a series of images', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-360', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/360-viewer/', 'keywords' => ['images', 'products']];
        $widgets_info['DCE_Widget_Tilt'] = ['category' => 'CREATIVE', 'name' => 'dyncontel-tilt', 'title' => __('Tilt', 'dynamic-content-for-elementor'), 'description' => __('Parallax hover tilt effect applied to a template', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-tilt', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/tilt/', 'keywords' => ['parallax']];
        $widgets_info['DCE_Widget_TwentyTwenty'] = ['category' => 'CREATIVE', 'name' => 'dyncontel-twentytwenty', 'title' => __('Before After', 'dynamic-content-for-elementor'), 'description' => __('Display an image with a before – after effect, ideal for comparing differences between two images', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-afterbefore', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/before-after/', 'keywords' => ['images', 'image']];
        $widgets_info['DCE_Widget_DoShortcode'] = ['category' => 'DEV', 'name' => 'dyncontel-doshortcode', 'title' => __('DoShortcode', 'dynamic-content-for-elementor'), 'description' => __('Apply a WordPress shortcode', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-doshortc', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/doshortcode/', 'keywords' => ['shortcode']];
        $widgets_info['DCE_Widget_IncludeFile'] = ['category' => 'DEV', 'name' => 'dyncontel-includefile', 'title' => __('File Include', 'dynamic-content-for-elementor'), 'description' => __('Directly include files from a path in root as if you were writing in a theme', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-incfile', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/file-include/'];
        $widgets_info['DCE_Widget_RawPhp'] = ['category' => 'DEV', 'name' => 'dce-rawphp', 'title' => __('PHP Raw', 'dynamic-content-for-elementor'), 'description' => __('Add PHP code directly in Elementor', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-phprow', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/php-raw/', 'keywords' => ['code']];
        $widgets_info['DCE_Widget_RemoteContent'] = ['category' => 'DEV', 'name' => 'dyncontel-remotecontent', 'title' => __('Remote Content', 'dynamic-content-for-elementor'), 'description' => __('Dynamically read any type of content from the web, incorporate text blocks, pictures and more from external sources. Compatible with REST APIs, including the native ones from WordPress. Option to format the resulting values in JSON', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-remotecontent', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/remote-content/', 'keywords' => ['iframe', 'json']];
        $widgets_info['DCE_Widget_DynamicPosts'] = ['category' => 'DYNAMIC', 'legacy' => \true, 'name' => 'dyncontel-acfposts', 'title' => __('Dynamic Posts (old version)', 'dynamic-content-for-elementor'), 'description' => __('Create archives from lists of articles with different query source options. You can display the list with various layouts and use templates to control item content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-dynamic-posts', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-posts/', 'keywords' => ['list', 'articles', 'carousel', 'slider', 'timeline', 'archive']];
        $widgets_info['DCE_Widget_DynamicPosts_v2'] = ['category' => 'DYNAMIC', 'name' => 'dce-dynamicposts-v2', 'title' => __('Dynamic Posts (was Dynamic Posts v2)', 'dynamic-content-for-elementor'), 'description' => __('Create archives from lists of articles with different query source options. You can display the list with various layouts and use templates to control item content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-dynamic-posts', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-posts-v2', 'keywords' => ['grid', '3d', 'skin', 'list', 'articles', 'carousel', 'slider', 'timeline', 'archive', 'custom', 'custom post type', 'cpt', 'item', 'loop', 'v2', 'pods', 'acf', 'fields']];
        $widgets_info['DCE_Widget_GoogleMaps'] = ['category' => 'DYNAMIC', 'name' => 'dyncontel-acf-google-maps', 'title' => __('Dynamic Google Maps', 'dynamic-content-for-elementor'), 'description' => __('Build a map using data from ACF Google Map fields, addresses or latitude and longitude', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-map', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-google-maps/', 'keywords' => ['Advanced Custom Fields', 'fields']];
        $widgets_info['DCE_Widget_DynamicUsers'] = ['category' => 'DYNAMIC', 'name' => 'dyncontel-dynamicusers', 'title' => __('Dynamic Users', 'dynamic-content-for-elementor'), 'description' => __('Create an archive based on User profiles', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-users', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-users/', 'keywords' => ['list', 'users', 'archive']];
        $widgets_info['DCE_Widget_AnimatedOffcanvasMenu'] = ['category' => 'INTERFACE', 'name' => 'dce-animatedoffcanvasmenu', 'title' => __('Animated Off-Canvas Menu', 'dynamic-content-for-elementor'), 'description' => __('An off-canvas menu is positioned outside of the viewport and slides in when activated', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-animatedoffcanvasmenu', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/animated-offcanvas-menu/', 'keywords' => ['menu', 'animations']];
        $widgets_info['DCE_Widget_CursorTracker'] = ['category' => 'INTERFACE', 'name' => 'dyncontel-cursorTracker', 'title' => __('Cursor Tracker', 'dynamic-content-for-elementor'), 'description' => __('An element that follows the cursor and indicates the level of scrolling', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-cursor-tracker', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/cursor-tracker/'];
        $widgets_info['DCE_Widget_FileBrowser'] = ['category' => 'LIST', 'name' => 'dce-filebrowser', 'title' => __('FileBrowser', 'dynamic-content-for-elementor'), 'description' => __('Display a list of files you uploaded in a specific “uploads” directory. This is particularly useful when you need to make pictures or documents available in a simple and intuitive way', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-filebrowser', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/file-browser/', 'keywords' => ['list', 'file', 'download', 'archive']];
        $widgets_info['DCE_Widget_ParentChildMenu'] = ['category' => 'LIST', 'name' => 'parent-child-menu', 'title' => __('Parent Child Menu', 'dynamic-content-for-elementor'), 'description' => __('Build a list of entries in horizontal or vertical mode where a parent element can be considered as a menu', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-parentchild', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/widget-parent-child-menu/'];
        $widgets_info['DCE_Widget_SearchFilter'] = ['category' => 'LIST', 'name' => 'dce-searchfilter', 'title' => __('Search & Filter Pro', 'dynamic-content-for-elementor'), 'description' => __('The Ultimate WordPress filter plugin that uses Ajax with Elementor', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-search-filter', 'plugin_depends' => ['search-filter-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/search-filter-pro-elementor', 'keywords' => ['Search and Filter Pro']];
        $widgets_info['DCE_Widget_SinglePostsMenu'] = ['category' => 'LIST', 'name' => 'single-posts-menu', 'title' => __('Single Posts List', 'dynamic-content-for-elementor'), 'description' => __('Create a custom menu from single pages', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-listsingle', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/single-posts-list/'];
        $widgets_info['DCE_Widget_TaxonomyTermsMenu'] = ['category' => 'LIST', 'name' => 'taxonomy-terms-menu', 'title' => __('Taxonomy Terms List', 'dynamic-content-for-elementor'), 'description' => __('Write a taxonomy for your article', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-parenttax', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/taxonomy-terms-list/', 'keywords' => ['list', 'menu']];
        $widgets_info['DCE_Widget_Views'] = ['category' => 'LIST', 'name' => 'dce-views', 'title' => __('Views', 'dynamic-content-for-elementor'), 'description' => __('Create a custom list from query results', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-views', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/views/', 'keywords' => ['list', 'articles', 'search', 'archive', 'cpt']];
        $widgets_info['DCE_Widget_Pods'] = ['category' => 'PODS', 'name' => 'dyncontel-pods', 'title' => __('Pods Fields', 'dynamic-content-for-elementor'), 'description' => __('Add a custom field created with Pods', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-acffields', 'plugin_depends' => ['pods' => 'pods'], 'doc_url' => 'https://www.dynamic.ooo/widget/pods-fields/'];
        $widgets_info['DCE_Widget_PodsGallery'] = ['category' => 'PODS', 'name' => 'dyncontel-podsgallery', 'title' => __('Pods Gallery', 'dynamic-content-for-elementor'), 'description' => __('Use a list of images from a Pods Image field with various display options', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-grid', 'plugin_depends' => ['pods' => 'pods'], 'doc_url' => 'https://www.dynamic.ooo/widget/pods-gallery/', 'keywords' => ['images', 'fields']];
        $widgets_info['DCE_Widget_PodsRelationship'] = ['category' => 'PODS', 'name' => 'dyncontel-pods-relation', 'title' => __('Pods Relationship', 'dynamic-content-for-elementor'), 'description' => __('Display related posts linked using the Pods Relationship field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-relation', 'plugin_depends' => ['pods' => 'pods'], 'doc_url' => 'https://www.dynamic.ooo/widget/pods-relationship/'];
        $widgets_info['DCE_Widget_Breadcrumbs'] = ['category' => 'POST', 'name' => 'dce-breadcrumbs', 'title' => __('Breadcrumbs', 'dynamic-content-for-elementor'), 'description' => __('Insert breadcrumbs and generate paths inside your page automatically', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-breadcrumbs', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/breadcrumbs/'];
        $widgets_info['DCE_Widget_Content'] = ['category' => 'POST', 'name' => 'dyncontel-content', 'title' => __('Content', 'dynamic-content-for-elementor'), 'description' => __('Retrieve the post content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-content', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/content/'];
        $widgets_info['DCE_Widget_Date'] = ['category' => 'POST', 'name' => 'dyncontel-date', 'title' => __('Date', 'dynamic-content-for-elementor'), 'description' => __('Display the published or modified date', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-date', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/date/'];
        $widgets_info['DCE_Widget_Excerpt'] = ['category' => 'POST', 'name' => 'dyncontel-excerpt', 'title' => __('Excerpt', 'dynamic-content-for-elementor'), 'description' => __('Display a short section from the beginning of the content', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-excerpt', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/excerpt/', 'keywords' => ['content']];
        $widgets_info['DCE_Widget_FeaturedImage'] = ['category' => 'POST', 'name' => 'dyncontel-featured-image', 'title' => __('Featured Image', 'dynamic-content-for-elementor'), 'description' => __('Retrieve the featured image', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-image', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/featured-image/'];
        $widgets_info['DCE_Widget_IconFormat'] = ['category' => 'POST', 'name' => 'dyncontel-iconformat', 'title' => __('Icon Format', 'dynamic-content-for-elementor'), 'description' => __('Add an icon for your post format and identify its type', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-formats', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/icon-format/'];
        $widgets_info['DCE_Widget_Meta'] = ['category' => 'POST', 'name' => 'dce-meta', 'title' => __('Post Meta', 'dynamic-content-for-elementor'), 'description' => __('Display any post meta saved in the database', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-customfields', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/meta-fields/'];
        $widgets_info['DCE_Widget_NextPrev'] = ['category' => 'POST', 'name' => 'dyncontel-post-nextprev', 'title' => __('Prev Next', 'dynamic-content-for-elementor'), 'description' => __('Display navigation links to adjacent posts based on a category/taxonomy or tag', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-prevnext', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/prevnext/', 'keywords' => ['previous', 'list']];
        $widgets_info['DCE_Widget_ReadMore'] = ['category' => 'POST', 'name' => 'dyncontel-readmore', 'title' => __('Read More', 'dynamic-content-for-elementor'), 'description' => __('Add a "Read More" button below your post or on the block in the archive, create a call-to-action.', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-readmore', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/read-more-button/'];
        $widgets_info['DCE_Widget_Terms'] = ['category' => 'POST', 'name' => 'dyncontel-terms', 'title' => __('Terms & Taxonomy', 'dynamic-content-for-elementor'), 'description' => __('Insert your post taxonomies', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-terms', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/terms-and-taxonomy/', 'keywords' => ['taxonomies']];
        $widgets_info['DCE_Widget_Title'] = ['category' => 'POST', 'name' => 'dyncontel-title', 'title' => __('Title', 'dynamic-content-for-elementor'), 'description' => __('Insert the post title', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-title', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/title/'];
        $widgets_info['DCE_Widget_TitleType'] = ['category' => 'POST', 'name' => 'dyncontel-titleType', 'title' => __('Title Type', 'dynamic-content-for-elementor'), 'description' => __('Retrieve the post type', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-title-type', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/title-type/', 'keywords' => ['cpt', 'Custom Post Type']];
        $widgets_info['DCE_Widget_User'] = ['category' => 'POST', 'name' => 'dce-user-fields', 'title' => __('User Fields', 'dynamic-content-for-elementor'), 'description' => __('Display any user field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-userfields', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/user-fields/'];
        $widgets_info['DCE_Widget_TitleTaxonomy'] = ['category' => 'POST', 'name' => 'dyncontel-titleTaxonomy', 'title' => __('Title Taxonomy', 'dynamic-content-for-elementor'), 'description' => __('Use the current Taxonomy term as the Archive title', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-title-taxonomy', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/title-taxonomy/'];
        $widgets_info['DCE_Widget_SvgBlob'] = ['category' => 'SVG', 'name' => 'dyncontel-svgblob', 'title' => __('SVG Blob', 'dynamic-content-for-elementor'), 'description' => __('Create a shape using an SVG path and make it move!', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-svgblob', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-blob/', 'keywords' => ['animations']];
        $widgets_info['DCE_Widget_SvgDistortion'] = ['category' => 'SVG', 'name' => 'dyncontel-svgdistortion', 'title' => __('SVG Distortion', 'dynamic-content-for-elementor'), 'description' => __('The SVG Distortion widget operates by calculating the displacement map based on a source image', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-distortion', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-distortion/', 'keywords' => ['animations']];
        $widgets_info['DCE_Widget_SvgFilterEffects'] = ['category' => 'SVG', 'name' => 'dyncontel-filtereffects', 'title' => __('SVG Filter Effects', 'dynamic-content-for-elementor'), 'description' => __('The widget operates using primitive SVG filters', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-svgfe', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-fe-filtereffects/'];
        $widgets_info['DCE_Widget_SvgImagemask'] = ['category' => 'SVG', 'name' => 'dyncontel-svgimagemask', 'title' => __('SVG Image Mask', 'dynamic-content-for-elementor'), 'description' => __('The SVG Image Mask widget operates through the mask attribute of the SVG', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-svgmask', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-mask/', 'keywords' => ['masking', 'images']];
        $widgets_info['DCE_Widget_SvgMorphing'] = ['category' => 'SVG', 'name' => 'dyncontel-svgmorphing', 'title' => __('SVG Morphing', 'dynamic-content-for-elementor'), 'description' => __('You can transpose SVG paths to morph from one shape to another ', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-svgmorph', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-morphing/', 'keywords' => ['animations']];
        $widgets_info['DCE_Widget_Svg_PathText'] = ['category' => 'SVG', 'name' => 'dyncontel-svgpathText', 'title' => __('SVG Text Path', 'dynamic-content-for-elementor'), 'description' => __('Write out text along a source based on an SVG path line', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-svgtextpath', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/svg-text-path/'];
        $widgets_info['DCE_Widget_Toolset'] = ['category' => 'TOOLSET', 'name' => 'dyncontel-toolset', 'title' => __('Toolset Fields', 'dynamic-content-for-elementor'), 'description' => __('Display a Toolset custom field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-acffields', 'plugin_depends' => ['types' => 'toolset'], 'doc_url' => 'https://www.dynamic.ooo/widget/toolset-fields/'];
        $widgets_info['DCE_Widget_ToolsetRelationship'] = ['category' => 'TOOLSET', 'name' => 'dyncontel-toolset-relation', 'title' => __('Toolset Relationship', 'dynamic-content-for-elementor'), 'description' => __('Display related posts defined by Toolset Relationships', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-relation', 'plugin_depends' => ['types' => 'toolset'], 'doc_url' => 'https://www.dynamic.ooo/widget/toolset-relationship/', 'keywords' => ['fields']];
        $widgets_info['DCE_Widget_BgCanvas'] = ['category' => 'WEBGL', 'name' => 'dyncontel-bgcanvas', 'title' => __('Background Canvas', 'dynamic-content-for-elementor'), 'description' => __('Easily integrate WebGL with Canvas for Shader effects', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-canvas', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/webgl-threejs-background-canvas/', 'keywords' => ['bg', 'images']];
        $widgets_info['DCE_Widget_DistortionImage'] = ['category' => 'WEBGL', 'name' => 'dyncontel-imagesDistortion', 'title' => __('Images Distortion Hover', 'dynamic-content-for-elementor'), 'description' => __('Special rollover effects based on WebGL, Three.js, and transformation shaders', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-distortion', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/webgl-threejs-images-distortions-hover/', 'keywords' => ['animations']];
        $widgets_info['DCE_Widget_Panorama'] = ['category' => 'WEBGL', 'name' => 'dyncontel-panorama', 'title' => __('Panorama', 'dynamic-content-for-elementor'), 'description' => __('Display a 360 degree spherical image through VR mode', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-panorama', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/panorama/', 'keywords' => ['images']];
        $widgets_info['OsmMap'] = ['category' => 'CONTENT', 'name' => 'dce-osm-map', 'title' => __('OpenStreetMap', 'dynamic-content-for-elementor'), 'description' => __('A map widget that does not need a key thanks to OpenStreetMap!', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-osm', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/openstreetmap-elementor/', 'keywords' => ['map', 'open street map', 'google maps', 'osm', 'address']];
        return $widgets_info;
    }
    public static function get_widgets_by_group()
    {
        $widgets_info = self::get_widgets_info();
        $grouped_widgets = [];
        foreach ($widgets_info as $widget_class => $widget_info) {
            $grouped_widgets[$widget_info['category']][$widget_class] = $widget_info;
        }
        return $grouped_widgets;
    }
    /**
     * On Widgets Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_widgets_registered()
    {
        $this->register_widgets();
    }
    /**
     * Register Widgets
     *
     * @since 0.5.0
     *
     * @access private
     */
    public function register_widgets()
    {
        if (empty(self::$registered_widgets)) {
            $excluded_widgets = self::get_excluded_widgets();
            $widgets_info = self::get_widgets_info();
            foreach ($widgets_info as $widget_class => $widget_info) {
                if (!isset(self::$registered_widgets[$widget_class])) {
                    if (!$excluded_widgets || !isset($excluded_widgets[$widget_class])) {
                        if (\DynamicContentForElementor\Helper::check_plugin_dependencies(\false, $widget_info['plugin_depends']) && (!isset($widget_info['minimum_php']) || isset($widget_info['minimum_php']) && \version_compare(\phpversion(), $widget_info['minimum_php'], '>='))) {
                            $widget_object_name = self::$namespace . $widget_class;
                            $widget_object = new $widget_object_name();
                            \Elementor\Plugin::instance()->widgets_manager->register_widget_type($widget_object);
                            self::$registered_widgets[$widget_class] = $widget_object;
                        }
                    }
                }
            }
        }
    }
    public static function get_legacy_widgets()
    {
        $legacy = \array_filter(self::get_widgets_info(), function ($info) {
            return isset($info['legacy']) && $info['legacy'];
        });
        return $legacy;
    }
    public static function get_excluded_widgets()
    {
        $option = \json_decode(get_option('dce_excluded_widgets', '[]'), \true);
        return \array_filter($option, function ($v) {
            return $v;
        });
    }
    /**
     * Add Elementor categories
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function add_elementor_widget_categories($elements)
    {
        // Default category for widgets without a category
        $elements->add_category('dynamic-content-for-elementor', array('title' => 'Dynamic.ooo'));
        self::init_group();
        // Add categories
        foreach (self::$groups as $group_key => $group_name) {
            $elements->add_category('dynamic-content-for-elementor-' . \strtolower($group_key), array('title' => 'Dynamic.ooo - ' . $group_name));
        }
    }
}
