<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
use DynamicContentForElementor\Controls\DCE_Group_Control_Transform_Element;
use DynamicContentForElementor\Controls\DCE_Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Widget_DynamicPosts_v2 extends \DynamicContentForElementor\Widgets\DCE_Widget_DynamicPosts_Base
{
    public function get_name()
    {
        return 'dce-dynamicposts-v2';
    }
    protected $depended_scripts = ['dce-dynamicPosts-base'];
    protected $depended_styles = ['dce-dynamicPosts'];
    public function add_script_depends($handler)
    {
        if (!empty($handler) && \is_array($handler)) {
            $this->depended_scripts[] = \array_merge($this->depended_scripts, $handler);
        } elseif (!empty($handler) && \is_string($handler)) {
            $this->depended_scripts[] = $handler;
        }
    }
    public function add_style_depends($handler)
    {
        if (!empty($handler) && \is_array($handler)) {
            $this->depended_styles[] = \array_merge($this->depended_styles, $handler);
        } elseif (!empty($handler) && \is_string($handler)) {
            $this->depended_styles[] = $handler;
        }
    }
    public function get_script_depends()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            $all_scripts = [];
            foreach ($this->get_skins() as $skin => $value) {
                $all_scripts = \array_merge($all_scripts, $this->get_skin($skin)->get_script_depends());
            }
            return \array_merge($this->depended_scripts, $all_scripts);
        }
        return \array_merge($this->depended_scripts, $this->get_current_skin()->get_script_depends());
    }
    public function get_style_depends()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            $all_styles = [];
            foreach ($this->get_skins() as $skin => $value) {
                $all_styles = \array_merge($all_styles, $this->get_skin($skin)->get_style_depends());
            }
            return \array_merge($this->depended_styles, $all_styles);
        }
        return \array_merge($this->depended_styles, $this->get_current_skin()->get_style_depends());
    }
    protected function _enqueue_scripts()
    {
        $scripts = $this->get_script_depends();
        if (isset($scripts) && !empty($scripts)) {
            foreach ($scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }
    protected function _enqueue_styles()
    {
        $styles = $this->get_style_depends();
        if (isset($styles) && !empty($styles)) {
            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Skin_Grid($this));
        $this->add_skin(new Skins\Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Skin_Carousel($this));
        $this->add_skin(new Skins\Skin_DualCarousel($this));
        $this->add_skin(new Skins\Skin_Timeline($this));
        $this->add_skin(new Skins\Skin_3D($this));
        $this->add_skin(new Skins\Skin_Gridtofullscreen3d($this));
        $this->add_skin(new Skins\Skin_CrossroadsSlideshow($this));
    }
    protected function _register_controls()
    {
        parent::_register_controls();
        $taxonomies = Helper::get_taxonomies();
        $types = Helper::get_post_types();
        $searchfilterpro_activated = \defined('SEARCH_FILTER_PRO_BASE_PATH');
        $sql_operators = Helper::get_sql_operators();
        $this->start_controls_section('section_query', ['label' => __('Query', 'dynamic-content-for-elementor')]);
        $this->add_control('query_type', ['label' => __('Query Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'separator' => 'before', 'options' => ['get_cpt' => ['title' => __('From Post Type', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'eicon-post-content'], 'dynamic_mode' => ['title' => __('Dynamic - Current Query', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-cogs'], 'relationship' => ['title' => __('ACF Relationship', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-american-sign-language-interpreting'], 'pods_relationship' => ['title' => __('Pods Relationship', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'icon-dyn-relation'], 'search_filter' => ['title' => 'Search & Filter Pro', 'return_val' => 'val', 'icon' => 'icon-dyn-search-filter'], 'post_parent' => ['title' => __('From Post Parent', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-sitemap'], 'search_page' => ['title' => __('Search Page', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-search'], 'specific_posts' => ['title' => __('From Specific Posts', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-list-ul'], 'id_list' => ['title' => __('ID List', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-clipboard-list']], 'default' => 'get_cpt']);
        // --------------------------------- [ Post Parent ]
        $this->add_control('specific_page_parent', ['label' => __('Show children from this parent-page', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Page Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['query_type' => 'post_parent', 'parent_source' => '', 'child_source' => '']]);
        $this->add_control('dynamic_parent_heading', ['label' => __('Dynamic', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['query_type' => 'post_parent']]);
        $this->add_control('parent_source', ['label' => __('My Siblings', 'dynamic-content-for-elementor'), 'description' => __('I take the post parent and I get my siblings out of myself.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'post_parent']]);
        $this->add_control('child_source', ['label' => __('My Children', 'dynamic-content-for-elementor'), 'description' => __('Compared to myself, I\'ll retrieve my children.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'post_parent', 'parent_source' => '']]);
        // --------------------------------- [ Specific Posts-Pages ]
        $repeater = new Repeater();
        $repeater->add_control('repeater_specific_posts', ['label' => __('Select Post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'show_label' => \false, 'placeholder' => __('Select post', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts']);
        $this->add_control('specific_posts', ['label' => __('Specific Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'prevent_empty' => \false, 'default' => [], 'separator' => 'after', 'fields' => $repeater->get_controls(), 'title_field' => 'ID: {{{ repeater_specific_posts }}}', 'condition' => ['query_type' => 'specific_posts']]);
        if ($searchfilterpro_activated) {
            $this->add_control('search_filter_id', ['label' => __('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => __('Select the filter', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'object_type' => 'search-filter-widget', 'condition' => ['query_type' => 'search_filter']]);
        } else {
            $this->add_control('search_filter_notice', ['type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'raw' => __('Combine the power of Search & Filter Pro front end filters with Dynamic Posts v2! Create front end search forms and filter Dynamic Posts v2 layouts using the advanced query and filter builder of Search & Filter Pro. Note: In order to use this feature you need install Search & Filter Pro. Search & Filter Pro is a premium product - you can <a href="https://searchandfilter.com">get it here</a>.', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'search_filter']]);
        }
        $this->add_control('id_list', ['label' => __('ID List', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'description' => __('Type a comma-separated list of ids (e.g. 1, 100, 250)', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['query_type' => 'id_list']]);
        // --------------------------------- [ META relationship ]
        $this->add_control('relationship_meta', ['label' => __('ACF Relationship field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false], 'object_type' => 'post_object,relationship', 'condition' => ['query_type' => 'relationship']]);
        $this->add_control('relationship_invert', ['label' => __('Invert direction', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('For bidirectional relationships, retrieve all posts that are associated with the current post', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'relationship']]);
        if (Helper::is_pods_active()) {
            $this->add_control('pods_relationship_field', ['label' => __('PODS Relationship field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_pods_fields('pick'), 'default' => '0', 'condition' => ['query_type' => 'pods_relationship']]);
        } else {
            $this->add_control('pods_notice', ['label' => __('Important Note', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'raw' => __('In order to use this feature you need install PODS. You can <a href="https://pods.io">download it free here</a>.', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'pods_relationship']]);
        }
        // --------------------------------- [ Custom Post Type ]
        $this->add_control('post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $types, 'multiple' => \true, 'label_block' => \true, 'default' => [], 'condition' => ['query_type' => ['get_cpt', 'search_page']]]);
        $this->add_control('post_status', ['label' => __('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['publish' => __('Publish', 'dynamic-content-for-elementor'), 'draft' => __('Draft', 'dynamic-content-for-elementor'), 'private' => __('Private', 'dynamic-content-for-elementor'), 'password' => __('Password', 'dynamic-content-for-elementor')], 'multiple' => \true, 'label_block' => \true, 'default' => [], 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('ignore_sticky_posts', ['label' => __('Ignore Sticky Posts', 'dynamic-content-for-elementor'), 'description' => __('Ignores that a post is sticky and shows the posts in the normal order. Your sticky posts will appear in the loop, however they will not be placed on the top', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'remove_sticky_posts' => '']]);
        $this->add_control('remove_sticky_posts', ['label' => __('Remove Sticky Posts from the loop', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('hr_query', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->add_control('num_posts', ['label' => __('Results per page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'relationship', 'pods_relationship', 'search_page', 'post_parent']]]);
        $this->add_control('post_offset', ['label' => __('Posts Offset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('Warning: posts offset doesn\'t support pagination', 'dynamic-content-for-elementor'), 'default' => 0, 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode'], 'num_posts!' => '-1']]);
        $this->add_control('orderby', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date', 'condition' => ['query_type!' => 'search_filter']]);
        $this->add_control('metakey', ['label' => __('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'separator' => 'after', 'dynamic' => ['active' => \false], 'condition' => ['orderby' => ['meta_value_date', 'meta_value_num', 'meta_value']]]);
        $this->add_control('order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => __('Ascending', 'dynamic-content-for-elementor'), 'DESC' => __('Descending', 'dynamic-content-for-elementor')], 'default' => 'DESC', 'condition' => ['query_type!' => 'search_filter', 'orderby!' => 'random']]);
        // --------------------------------- [ Posts Exclusion ]
        $this->add_control('heading_query_options', ['label' => __('Exclude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'search_page']]]);
        $this->add_control('exclude_io', ['label' => __('Current Post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('exclude_page_parent', ['label' => __('Page parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode']]]);
        $this->add_control('exclude_posts', ['label' => __('Specific Posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'multiple' => \true, 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'search_page']]]);
        $this->end_controls_section();
        // ------------------------------------------------------------------ [SECTION QUERY-FILTER]
        $this->start_controls_section('section_query_filter', ['label' => __('Query Filter', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => ['get_cpt', 'dynamic_mode', 'search_page', 'relationship', 'id_list']]]);
        $this->add_control('query_filter', ['label' => __('By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['date' => __('Date', 'dynamic-content-for-elementor'), 'term' => __('Terms & Taxonomy', 'dynamic-content-for-elementor'), 'author' => __('Author', 'dynamic-content-for-elementor'), 'metakey' => __('Metakey', 'dynamic-content-for-elementor')], 'multiple' => \true, 'label_block' => \true, 'default' => []]);
        // +********************* Date
        $this->add_control('heading_query_filter_date', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __(' Date Filters', 'dynamic-content-for-elementor'), 'label_block' => \false, 'separator' => 'before', 'content_classes' => 'dce-icon-heading', 'condition' => ['query_filter' => 'date']]);
        $this->add_control('querydate_mode', ['label' => __('Date Filter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'label_block' => \true, 'options' => ['' => __('No Filter', 'dynamic-content-for-elementor'), 'past' => __('Past', 'dynamic-content-for-elementor'), 'future' => __('Future', 'dynamic-content-for-elementor'), 'today' => __('Today', 'dynamic-content-for-elementor'), 'yesterday' => __('Yesterday', 'dynamic-content-for-elementor'), 'days' => __('Past Days', 'dynamic-content-for-elementor'), 'weeks' => __('Past Weeks', 'dynamic-content-for-elementor'), 'months' => __('Past Months', 'dynamic-content-for-elementor'), 'years' => __('Past Years', 'dynamic-content-for-elementor'), 'period' => __('Period', 'dynamic-content-for-elementor')], 'condition' => ['query_filter' => 'date']]);
        $this->add_control('querydate_field', ['label' => __('Date Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \false, 'options' => ['post_date' => ['title' => __('Publish Date', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-calendar'], 'post_modified' => ['title' => __('Modified Date', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-edit'], 'post_meta' => ['title' => __('Post Meta', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square']], 'default' => 'post_date', 'toggle' => \false, 'condition' => ['query_filter' => 'date', 'querydate_mode!' => ['', 'future']]]);
        $this->add_control('querydate_field_meta', ['label' => __('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'description' => __('Selected Post Meta value must be stored if format "Ymd", like ACF Date', 'dynamic-content-for-elementor'), 'separator' => 'before', 'dynamic' => ['active' => \false], 'condition' => ['query_filter' => 'date', 'querydate_mode!' => 'future', 'querydate_field' => 'post_meta']]);
        $this->add_control('querydate_field_meta_format', ['label' => __('Meta Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('Ymd', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => __('Ymd', 'dynamic-content-for-elementor'), 'condition' => ['query_filter' => 'date', 'querydate_mode!' => 'future', 'querydate_field' => 'post_meta']]);
        $this->add_control('querydate_field_meta_future', ['label' => __('Meta Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'description' => __('Selected Post Meta value must be stored if format "Ymd", like ACF Date', 'dynamic-content-for-elementor'), 'separator' => 'before', 'dynamic' => ['active' => \false], 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'future']]);
        $this->add_control('querydate_field_meta_future_format', ['label' => __('Meta Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => __('Y-m-d', 'dynamic-content-for-elementor'), 'label_block' => \false, 'default' => __('Ymd', 'dynamic-content-for-elementor'), 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'future']]);
        $this->add_control('querydate_field_meta_future_contains_today', ['label' => __('Future dates contains today\'s date', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'future']]);
        // number of days / months / years elapsed
        $this->add_control('querydate_range', ['label' => __('Number of (days/months/years) elapsed', 'dynamic-content-for-elementor'), 'label_block' => \false, 'type' => Controls_Manager::NUMBER, 'default' => 1, 'condition' => ['query_filter' => 'date', 'querydate_mode' => ['days', 'weeks', 'months', 'years']]]);
        $this->add_control('querydate_date_type', ['label' => __('Date Input Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \true, 'options' => ['static' => ['title' => __('Static', 'dynamic-content-for-elementor'), 'icon' => 'eicon-pencil'], 'dynamicstring' => ['title' => __('Dynamic', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs']], 'default' => '_dynamic', 'toggle' => \false, 'separator' => 'before', 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'period']]);
        $this->add_control('querydate_date_from', ['label' => __('Date FROM', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \false, 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'period', 'querydate_date_type' => 'static']]);
        $this->add_control('querydate_date_to', ['label' => __('Date TO', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \false, 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'period', 'querydate_date_type' => 'static']]);
        $this->add_control('querydate_date_from_dynamic', ['label' => __('Date FROM', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'period', 'querydate_date_type' => 'dynamicstring']]);
        $this->add_control('querydate_date_to_dynamic', ['label' => __('Date TO', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['query_filter' => 'date', 'querydate_mode' => 'period', 'querydate_date_type' => 'dynamicstring']]);
        // +********************* Term Taxonomy
        $this->add_control('heading_query_filter_term', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __(' Terms & Taxonomy Filters', 'dynamic-content-for-elementor'), 'separator' => 'before', 'content_classes' => 'dce-icon-heading', 'condition' => ['query_filter' => 'term']]);
        // From Post or Meta
        $this->add_control('term_from', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \false, 'options' => ['post_term' => ['title' => __('Select Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tag'], 'post_meta' => ['title' => __('Post Meta Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square'], 'dynamicstring' => ['title' => __('Dynamic String', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs']], 'default' => 'post_term', 'toggle' => \false, 'condition' => ['query_filter' => 'term']]);
        $this->add_control('taxonomy', ['label' => __('Select Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('All', 'dynamic-content-for-elementor')] + $taxonomies, 'default' => '', 'description' => __('Filter results by selected taxonomy', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['query_filter' => 'term']]);
        // [Post Meta]
        $this->add_control('term_field_meta', ['label' => __('Post Term custom meta field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'dynamic' => ['active' => \false], 'description' => __('Selected post meta value. The meta must return an element of type array or comma separated string that contains the term type IDs. (e.g.: array [5,27,88] or 5,27,88)', 'dynamic-content-for-elementor'), 'condition' => ['term_from' => 'post_meta', 'query_filter' => 'term']]);
        // [Post Meta String]
        $this->add_control('term_field_meta_string', ['label' => __('Post Term string field', 'dynamic-content-for-elementor'), 'description' => __('Type the Post Meta value. Type a sequence of comma-separated term IDs. (e.g.: "5,27,88")', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'render_type' => 'template', 'default' => '', 'condition' => ['term_from' => 'dynamicstring', 'query_filter' => 'term']]);
        // [Post Term]
        foreach ($taxonomies as $tax_key => $a_tax) {
            if ($tax_key) {
                $this->add_control('include_term_' . $tax_key, ['label' => __('Include Terms of', 'dynamic-content-for-elementor') . ' ' . $a_tax, 'type' => 'ooo_query', 'placeholder' => __('All terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tax_key, 'description' => __('Filter results by selected taxonomy term', 'dynamic-content-for-elementor'), 'render_type' => 'template', 'multiple' => \true, 'condition' => ['taxonomy' => $tax_key, 'query_filter' => 'term', 'term_from' => 'post_term', 'terms_current_post' => '']]);
            }
        }
        $this->add_control('include_term_operator', ['label' => __('Include Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['AND' => ['title' => __('AND', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'IN' => ['title' => __('IN', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle-o']], 'toggle' => \false, 'default' => 'IN', 'conditions' => ['terms' => [['name' => 'taxonomy', 'operator' => '!=', 'value' => ''], ['name' => 'term_from', 'operator' => '==', 'value' => 'post_term']]]]);
        foreach ($taxonomies as $tax_key => $a_tax) {
            if ($tax_key) {
                $this->add_control('exclude_term_' . $tax_key, ['label' => __('Exclude Term for', 'dynamic-content-for-elementor') . ' ' . $a_tax, 'type' => 'ooo_query', 'placeholder' => __('All terms', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'terms', 'object_type' => $tax_key, 'description' => __('Filter results by selected taxonomy term', 'dynamic-content-for-elementor'), 'render_type' => 'template', 'multiple' => \true, 'condition' => ['taxonomy' => $tax_key, 'query_filter' => 'term', 'term_from' => 'post_term', 'terms_current_post' => '']]);
            }
        }
        $this->add_control('terms_current_post', ['label' => __('Dynamic Current Post Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Filter results by taxonomy terms associated to the current post', 'dynamic-content-for-elementor'), 'separator' => 'before', 'condition' => ['taxonomy!' => '', 'query_filter' => 'term', 'term_from' => 'post_term']]);
        // Author
        $this->add_control('heading_query_filter_author', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __(' Author Filters', 'dynamic-content-for-elementor'), 'separator' => 'before', 'content_classes' => 'dce-icon-heading', 'condition' => ['query_filter' => 'author']]);
        // From, Post, Meta or Current
        $this->add_control('author_from', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \false, 'options' => [
            'post_author' => ['title' => __('Select Author', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-users'],
            // 'post_meta' => [
            // 	'title' => __( 'Post Meta Author', 'dynamic-content-for-elementor' ),
            // 	'icon' => 'fa fa-square',
            // ],
            // 'dynamicstring' => [
            // 	'title' => __( 'Dynamic String', 'dynamic-content-for-elementor' ),
            // 	'icon' => 'fa fa-cogs',
            // ],
            'current_author' => ['title' => __('Current author', 'dynamic-content-for-elementor'), 'icon' => 'eicon-edit'],
            'current_user' => ['title' => __('Current user', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user'],
        ], 'default' => 'post_author', 'toggle' => \false, 'condition' => ['query_filter' => 'author']]);
        // Post Meta
        $this->add_control('author_field_meta', ['label' => __('Post author custom meta field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'default' => 'nickname', 'dynamic' => ['active' => \false], 'description' => __('Selected Post Meta value. The meta must return an element of type array or comma separated string containing author IDs. (eg: array [5,27,88] or 5,27,88)', 'dynamic-content-for-elementor'), 'condition' => ['author_from' => 'post_meta', 'query_filter' => 'author']]);
        // Post Meta String
        $this->add_control('author_field_meta_string', ['label' => __('Post Author string field', 'dynamic-content-for-elementor'), 'description' => __('Type the Post Meta value. Type a sequence of author IDs separated by commas. (eg: 5,27,88)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'render_type' => 'template', 'default' => '', 'condition' => ['author_from' => 'dynamicstring', 'query_filter' => 'author']]);
        // Select Authors
        $this->add_control('include_author', ['label' => __('Include Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select author', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'description' => __('Filter posts by selected Authors', 'dynamic-content-for-elementor'), 'condition' => ['query_filter' => 'author', 'author_from' => 'post_author']]);
        $this->add_control('exclude_author', ['label' => __('Exclude Author', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('No', 'dynamic-content-for-elementor'), 'label_block' => \true, 'multiple' => \true, 'query_type' => 'users', 'description' => __('Filter posts by selected Authors', 'dynamic-content-for-elementor'), 'separator' => 'after', 'condition' => ['query_filter' => 'author', 'author_from' => 'post_author']]);
        // Meta key
        $this->add_control('heading_query_filter_metakey', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => __('Metakey Filters', 'dynamic-content-for-elementor'), 'separator' => 'before', 'content_classes' => 'dce-icon-heading', 'condition' => ['query_filter' => 'metakey']]);
        // From Post or Meta
        $this->add_control('metakey_from', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'label_block' => \false, 'options' => ['post_metakey' => ['title' => __('Select Metakey', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-key'], 'post_meta' => ['title' => __('Post Meta Key', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-square']], 'default' => 'post_metakey', 'toggle' => \false, 'condition' => ['query_filter' => 'metakey']]);
        // Post Meta
        $this->add_control('metakey_field_meta', ['label' => __('Post Metakey custom meta field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metas', 'object_type' => 'post', 'dynamic' => ['active' => \false], 'condition' => ['metakey_from' => 'post_meta', 'query_filter' => 'metakey']]);
        $this->add_control('metakey_field_meta_operator', ['label' => __('Operator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $sql_operators, 'default' => '=', 'condition' => ['metakey_from' => 'post_meta', 'query_filter' => 'metakey']]);
        $this->add_control('metakey_field_meta_value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['metakey_from' => 'post_meta', 'query_filter' => 'metakey']]);
        // [Post Meta String]
        $this->add_control('metakey_field_meta_string', ['label' => __('Post Metakey string field', 'dynamic-content-for-elementor'), 'description' => __('Type the Post Meta value. Type a sequence of metakey-type IDs separated by commas. (e.g.:"5,27,88")', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'render_type' => 'template', 'default' => '', 'condition' => ['metakey_from' => 'dynamicstring', 'query_filter' => 'metakey']]);
        // [Post Term]
        $this->add_control('include_metakey', ['label' => __('Include Metakey', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('All metakeys', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metakeys', 'description' => __('Filter results by selected metakey', 'dynamic-content-for-elementor'), 'render_type' => 'template', 'dynamic' => ['active' => \false], 'multiple' => \true, 'condition' => ['query_filter' => 'metakey', 'metakey_from' => 'post_metakey']]);
        $this->add_control('include_metakey_combination', ['label' => __('Include Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['AND' => ['title' => __('AND', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'OR' => ['icon' => 'fa fa-circle-o']], 'toggle' => \false, 'default' => 'OR', 'conditions' => ['terms' => [['name' => 'query_filter', 'operator' => 'contains', 'value' => 'metakey'], ['name' => 'query_filter', 'operator' => '!=', 'value' => []], ['name' => 'include_metakey', 'operator' => '!=', 'value' => ''], ['name' => 'include_metakey', 'operator' => '!=', 'value' => []], ['name' => 'metakey_from', 'value' => 'post_metakey']]]]);
        $this->add_control('exclude_metakey', ['label' => __('Exclude Metakey', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('All metakeys', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metakeys', 'description' => __('Filter results by selected metakey', 'dynamic-content-for-elementor'), 'render_type' => 'template', 'dynamic' => ['active' => \false], 'multiple' => \true, 'condition' => ['query_filter' => 'metakey', 'metakey_from' => 'post_metakey']]);
        $this->add_control('exclude_metakey_combination', ['label' => __('Exclude Combination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['AND' => ['title' => __('AND', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'OR' => ['title' => __('OR', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle-o']], 'toggle' => \false, 'default' => 'OR', 'conditions' => ['terms' => [['name' => 'query_filter', 'operator' => 'contains', 'value' => 'metakey'], ['name' => 'query_filter', 'operator' => '!=', 'value' => []], ['name' => 'exclude_metakey', 'operator' => '!=', 'value' => ''], ['name' => 'exclude_metakey', 'operator' => '!=', 'value' => []], ['name' => 'metakey_from', 'value' => 'post_metakey']]]]);
        $this->end_controls_section();
        // FALLBACK for NO RESULTS
        $this->start_controls_section('section_fallback', ['label' => __('No Results Behaviour', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback', ['label' => __('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('If you want to show something when no elements are found.', 'dynamic-content-for-elementor')]);
        $this->add_control('fallback_type', ['label' => __('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['fallback!' => '']]);
        $this->add_control('fallback_template', ['label' => __('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['fallback!' => '', 'fallback_type' => 'template']]);
        $this->add_control('fallback_text', ['label' => __('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => __('No results found.', 'dynamic-content-for-elementor'), 'description' => __('Type here your content, you can use HTML and Tokens.', 'dynamic-content-for-elementor'), 'condition' => ['fallback!' => '', 'fallback_type' => 'text']]);
        $this->end_controls_section();
    }
    public function render()
    {
        $is_imagemask = $this->get_settings('imagemask_popover');
        if ($is_imagemask) {
            $mask_shape_type = $this->get_settings('mask_shape_type');
            $this->render_svg_mask($mask_shape_type);
        }
    }
    public function query_posts()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $post_type = $settings['post_type'];
        $id_page = Helper::get_the_id();
        $type_page = get_post_type();
        $args = [];
        $taxquery = [];
        $exclude_current_post = [];
        $sticky_posts_to_remove = [];
        $posts_excluded = [];
        $use_parent_page = [];
        $terms_query = 'all';
        $terms_query_excluded = [];
        $post_status = '';
        if (empty($settings['post_status'])) {
            $post_status = 'publish';
        } else {
            $post_status = $settings['post_status'];
        }
        if (is_singular()) {
            if ($settings['exclude_io']) {
                $exclude_current_post = [$id_page];
            }
        } elseif (is_home() || is_archive()) {
            $exclude_current_post = [];
        }
        if ($settings['exclude_posts']) {
            $posts_excluded = $settings['exclude_posts'];
        }
        if ($settings['exclude_page_parent']) {
            $use_parent_page = [0];
        } else {
            $use_parent_page = [];
        }
        // Ignore Sticky Posts
        if ($settings['ignore_sticky_posts']) {
            $args['ignore_sticky_posts'] = '1';
        }
        // Remove Sticky Posts
        if ($settings['remove_sticky_posts']) {
            $sticky_posts_to_remove = get_option('sticky_posts');
        }
        // Query Type - Search Page
        if ($settings['query_type'] == 'search_page') {
            if (empty($post_type)) {
                $post_type[] = 'any';
            }
            if (is_search()) {
                $args = \array_merge($args, ['s' => sanitize_text_field($_GET['s']), 'post_type' => $post_type, 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['metakey'], 'posts_per_page' => $settings['num_posts'], 'post__not_in' => $posts_excluded, 'post_parent__not_in' => $use_parent_page]);
            }
        } elseif ($settings['query_type'] == 'dynamic_mode') {
            $array_taxquery = [];
            $taxonomy_list = [];
            if (is_archive()) {
                $queried_object = get_queried_object();
                if (is_tax() || is_category() || is_tag()) {
                    $taxonomy_list[0] = $queried_object->taxonomy;
                }
                if (is_date()) {
                }
            } elseif (is_home()) {
            } elseif (is_single()) {
                $taxonomy_list = get_post_taxonomies($id_page);
            }
            if (!empty($taxonomy_list)) {
                foreach ($taxonomy_list as $tax) {
                    $terms_list = [];
                    $lista_dei_termini = [];
                    if (is_single()) {
                        if ($settings['taxonomy'] == $tax) {
                            $terms_list = wp_get_post_terms($id_page, $tax, ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                        }
                        foreach ($terms_list as $term) {
                            $lista_dei_termini[] = $term->term_id;
                        }
                    } elseif (is_archive()) {
                        $lista_dei_termini[0] = $queried_object->term_id;
                    }
                    if (\count($lista_dei_termini) > 0) {
                        $array_taxquery = [];
                        if (\count($lista_dei_termini) > 1) {
                            $array_taxquery['relation'] = $settings['combination_taxonomy'];
                        }
                        foreach ($lista_dei_termini as $termine) {
                            $array_taxquery[] = ['taxonomy' => $tax, 'field' => 'id', 'terms' => $termine];
                        }
                    }
                    /* EXCLUDED */
                    if (isset($settings['terms_' . $tax . '_excluse'])) {
                        $terms_query_excluded = $settings['terms_' . $tax . '_excluse'];
                    }
                    if (!empty($terms_query_excluded)) {
                        $array_taxquery_excluded = [];
                        if (\count($terms_query_excluded) > 1) {
                            $array_taxquery_excluded['relation'] = $settings['combination_taxonomy_excluse'];
                        }
                        foreach ($terms_query_excluded as $term_query) {
                            $array_taxquery_excluded[] = ['taxonomy' => $tax, 'field' => 'term_id', 'terms' => $term_query, 'operator' => 'NOT IN'];
                        }
                        if (empty($array_taxquery)) {
                            $array_taxquery = $array_taxquery_excluded;
                        } else {
                            $array_taxquery = ['relation' => 'AND', $array_taxquery, $array_taxquery_excluded];
                        }
                    }
                }
            }
            // Se la taxQuery dinamica non da risultati uso quella statica.
            if (!$array_taxquery) {
                $array_taxquery = $taxquery;
            }
            if (\is_array($type_page)) {
                if ($cptkey = \array_search('elementor_library', $type_page)) {
                    $type_page[$cptkey] = 'post';
                }
            } else {
                if ('elementor_library' == $type_page) {
                    $type_page = 'post';
                }
            }
            if (!is_search()) {
                $args = \array_merge($args, ['post_type' => $type_page, 'posts_per_page' => $settings['num_posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['metakey'], 'post__not_in' => \array_merge($posts_excluded, $exclude_current_post, $sticky_posts_to_remove), 'post_parent__not_in' => $use_parent_page, 'tax_query' => $array_taxquery, 'post_status' => $post_status]);
            }
            if (is_date()) {
                global $wp_query;
                $args['year'] = $wp_query->query_vars['year'];
                $args['monthnum'] = $wp_query->query_vars['monthnum'];
                $args['day'] = $wp_query->query_vars['day'];
            }
            if (!empty($settings['post_offset'])) {
                $args['offset'] = $settings['post_offset'];
            }
        } elseif ($settings['query_type'] == 'get_cpt') {
            $args = \array_merge($args, ['post_type' => $settings['post_type'], 'posts_per_page' => $settings['num_posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'post_status' => $post_status]);
            if ($taxquery) {
                $args['tax_query'] = $taxquery;
            }
            if ($settings['metakey']) {
                $args['meta_key'] = $settings['metakey'];
            }
            $post__not_in = \array_merge($posts_excluded, $exclude_current_post, $sticky_posts_to_remove);
            if (!empty($post__not_in)) {
                $args['post__not_in'] = $post__not_in;
            }
            if (!empty($use_parent_page)) {
                $args['post_parent__not_in'] = $use_parent_page;
            }
            if (!empty($settings['post_offset'])) {
                $args['offset'] = $settings['post_offset'];
            }
        } elseif ($settings['query_type'] == 'post_parent') {
            if (!empty($settings['specific_page_parent'])) {
                $args = \array_merge($args, ['post_type' => get_post_type($settings['specific_page_parent']), 'post_parent' => $settings['specific_page_parent']]);
            }
            if ($settings['parent_source']) {
                $args = \array_merge($args, ['post_type' => $type_page, 'post_parent' => wp_get_post_parent_id($id_page)]);
            }
            if ($settings['child_source']) {
                $args = \array_merge($args, ['post_type' => $type_page, 'post_parent' => $id_page]);
            }
            $args = \array_merge($args, ['posts_per_page' => $settings['num_posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby']]);
        } elseif ($settings['query_type'] == 'relationship') {
            if ($settings['relationship_invert']) {
                $relations_ids = Helper::get_acf_field_value_relationship_invert($settings['relationship_meta'], $id_page);
            } else {
                $relations_ids = get_post_meta($id_page, $settings['relationship_meta'], \false);
            }
            if (!empty($relations_ids) && !\is_array($relations_ids)) {
                $relations_ids = [$relations_ids];
            } elseif (!empty($relations_ids) && \is_array($relations_ids[0])) {
                $relations_ids = $relations_ids[0];
            }
            if (empty($relations_ids)) {
                $relations_ids = ['0'];
            }
            if ($settings['metakey']) {
                $args['meta_key'] = $settings['metakey'];
            }
            $args = \array_merge($args, ['post_type' => 'any', 'posts_per_page' => $settings['num_posts'], 'post_status' => 'publish', 'post__in' => $relations_ids, 'orderby' => $settings['orderby'], 'order' => $settings['order']]);
        } elseif ($settings['query_type'] == 'pods_relationship' && Helper::is_pods_active()) {
            if (pods(get_post_type(), get_the_ID())) {
                $related_posts = pods_field_raw($settings['pods_relationship_field']);
            }
            $related_posts_id = \false;
            if (\is_numeric($related_posts)) {
                $related_posts_id = array($related_posts);
            } elseif (isset($related_posts['ID'])) {
                $related_posts_id = array($related_posts['ID']);
            } elseif (\is_array($related_posts)) {
                $related_posts_id = wp_list_pluck($related_posts, 'ID');
            }
            if ($settings['metakey']) {
                $args['meta_key'] = $settings['metakey'];
            }
            $args = \array_merge($args, ['post_type' => 'any', 'posts_per_page' => $settings['num_posts'], 'post_status' => 'publish', 'post__in' => $related_posts_id, 'orderby' => $settings['orderby'], 'order' => $settings['order']]);
        } elseif ($settings['query_type'] == 'specific_posts') {
            $post__in = [];
            $specific_posts = $settings['specific_posts'];
            if (\is_array($specific_posts) && !empty($specific_posts)) {
                foreach ($specific_posts as $post) {
                    if (!empty($post['repeater_specific_posts'])) {
                        $post__in[] = $post['repeater_specific_posts'];
                    }
                }
            } else {
                $post__in = [0];
            }
            $args = \array_merge($args, ['post_type' => 'any', 'post__in' => $post__in, 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['metakey'], 'post_status' => 'publish', 'posts_per_page' => -1]);
        } elseif ($settings['query_type'] == 'id_list') {
            $args = \array_merge($args, ['post_type' => 'any', 'post__in' => \explode(',', sanitize_text_field($settings['id_list'])), 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'meta_key' => $settings['metakey'], 'post_status' => 'publish', 'posts_per_page' => -1]);
        } elseif ($settings['query_type'] == 'search_filter') {
            $searchfilterpro_activated = \defined('SEARCH_FILTER_PRO_BASE_PATH');
            if ($searchfilterpro_activated) {
                $sfid = \intval($settings['search_filter_id']);
                $args = ['search_filter_id' => $sfid];
            }
        }
        // Pagination
        if ($settings['query_type'] != 'search_filter') {
            global $paged;
            $paged = $this->get_current_page();
            $args['paged'] = $paged;
        } else {
            if (isset($_GET['sf_paged'])) {
                $args['paged'] = \intval($_GET['sf_paged']);
            } else {
                $args['paged'] = 1;
            }
        }
        /*
         * ----- Query Filter
         */
        if (\is_array($settings['query_filter'])) {
            // Date query filter
            if (\in_array('date', $settings['query_filter'])) {
                $querydate_field_meta_format = 'Ymd';
                $date_field = $settings['querydate_field'];
                if ($settings['querydate_mode'] != 'future' && $settings['querydate_field'] == 'post_meta') {
                    $date_field_meta = sanitize_key($settings['querydate_field_meta']);
                    $querydate_field_meta_format = sanitize_text_field($settings['querydate_field_meta_format']);
                }
                if ($settings['querydate_mode'] == 'future') {
                    $date_field_meta = sanitize_key($settings['querydate_field_meta_future']);
                    $querydate_field_meta_format = sanitize_text_field($settings['querydate_field_meta_future_format']);
                    if ($settings['querydate_field_meta_future_contains_today']) {
                        $future_compare = '>=';
                    } else {
                        $future_compare = '>';
                    }
                    $args['meta_query'] = [['key' => $date_field_meta, 'value' => \date($querydate_field_meta_format, \time()), 'meta_type' => 'DATETIME', 'compare' => $future_compare]];
                }
                if ($date_field) {
                    $date_after = $date_before = \false;
                    switch ($settings['querydate_mode']) {
                        case 'past':
                            $date_before = \date('Y-m-d H:i:s');
                            break;
                        case 'today':
                            $date_after = \date('Y-m-d 00:00:00');
                            $date_before = \date('Y-m-d 23:59:59');
                            break;
                        case 'yesterday':
                            $date_after = \date('Y-m-d 00:00:00', \strtotime('-1 day'));
                            $date_before = \date('Y-m-d 23:59:59', \strtotime('-1 day'));
                            break;
                        case 'days':
                        case 'weeks':
                        case 'months':
                        case 'years':
                            $date_after = '-' . $settings['querydate_range'] . ' ' . $settings['querydate_mode'];
                            $date_before = 'now';
                            break;
                        case 'period':
                            $date_after = $settings['querydate_date_type'] === 'static' ? $settings['querydate_date_from'] : $settings['querydate_date_from_dynamic'];
                            $date_before = $settings['querydate_date_type'] === 'static' ? $settings['querydate_date_to'] : $settings['querydate_date_to_dynamic'];
                            break;
                    }
                    // compare by post publish date
                    if ($settings['querydate_field'] == 'post_date') {
                        $args['date_query'] = [['after' => $date_after, 'before' => $date_before, 'inclusive' => \true]];
                    } elseif ($settings['querydate_field'] == 'post_modified') {
                        $args['date_query'] = [['column' => 'post_modified', 'after' => $date_after, 'before' => $date_before, 'inclusive' => \true]];
                    } elseif ($settings['querydate_field'] == 'post_meta') {
                        if ($date_after) {
                            $date_after = \date($querydate_field_meta_format, \strtotime($date_after));
                        }
                        if ($date_before) {
                            $date_before = \date($querydate_field_meta_format, \strtotime($date_before));
                        }
                        if ($date_before && $date_after) {
                            $args['meta_query'] = [['key' => $date_field_meta, 'value' => [$date_after, $date_before], 'meta_type' => 'DATETIME', 'compare' => 'BETWEEN']];
                        } elseif ($date_after) {
                            $args['meta_query'] = [['key' => $date_field_meta, 'value' => $date_after, 'meta_type' => 'DATETIME', 'compare' => '>=']];
                        } else {
                            $args['meta_query'] = [['key' => $date_field_meta, 'value' => $date_before, 'meta_type' => 'DATETIME', 'compare' => '<=']];
                        }
                    }
                }
            }
            // Term query filter
            if (\in_array('term', $settings['query_filter'])) {
                if ($settings['term_from'] == 'post_term') {
                    if ($settings['taxonomy'] && ($settings['include_term_' . $settings['taxonomy']] || $settings['exclude_term_' . $settings['taxonomy']])) {
                        if ($settings['include_term_' . $settings['taxonomy']]) {
                            $args['tax_query'][] = ['operator' => $settings['include_term_operator'], 'taxonomy' => $settings['taxonomy'], 'terms' => $settings['include_term_' . $settings['taxonomy']]];
                        }
                        if ($settings['exclude_term_' . $settings['taxonomy']]) {
                            $args['tax_query'][] = ['operator' => 'NOT IN', 'taxonomy' => $settings['taxonomy'], 'terms' => $settings['exclude_term_' . $settings['taxonomy']]];
                        }
                        if ($settings['include_term_' . $settings['taxonomy']] && $settings['exclude_term_' . $settings['taxonomy']]) {
                            $args['tax_query']['relation'] = 'AND';
                        }
                    }
                    if ($settings['terms_current_post']) {
                        $terms_query = $this->get_terms_query($settings, $id_page);
                        if (\is_array($terms_query) && !empty($terms_query)) {
                            foreach ($terms_query as $term_query) {
                                $args['tax_query'][] = ['taxonomy' => $settings['taxonomy'], 'terms' => $term_query];
                            }
                        }
                        if (\is_array($terms_query) && \count($terms_query) > 1) {
                            $args['tax_query']['relation'] = 'OR';
                        }
                    }
                } elseif ($settings['term_from'] == 'post_meta') {
                    if ($settings['term_field_meta']) {
                        $args['tax_query'][] = ['operator' => 'IN', 'taxonomy' => $settings['taxonomy'], 'terms' => 'all'];
                        $args['meta_query'][] = ['key' => $settings['term_field_meta']];
                    }
                } elseif ($settings['term_from'] == 'dynamicstring') {
                    if ($settings['term_field_meta_string']) {
                        $args['tax_query'][] = ['operator' => 'IN', 'taxonomy' => $settings['taxonomy'], 'field' => 'slug', 'terms' => sanitize_text_field($settings['term_field_meta_string'])];
                    }
                }
            }
            // Author query filter
            if (\in_array('author', $settings['query_filter'])) {
                $author_id = get_the_author_meta('ID');
                if (!is_singular()) {
                    $queried_object = get_queried_object();
                    if ($queried_object) {
                        if (\get_class($queried_object) == 'WP_User') {
                            $author_id = get_queried_object_id();
                            $args['author__in'] = $author_id;
                        }
                    }
                }
                if ($settings['author_from'] == 'post_author') {
                    if ($settings['include_author']) {
                        $args['author__in'] = $settings['include_author'];
                    }
                    if ($settings['exclude_author']) {
                        $args['author__not_in'] = $settings['exclude_author'];
                    }
                } elseif ($settings['author_from'] == 'post_meta') {
                    if ($settings['author_field_meta']) {
                    }
                } elseif ($settings['author_from'] == 'dynamicstring') {
                    if ($settings['author_field_meta_string']) {
                    }
                } elseif ($settings['author_from'] == 'current_author') {
                    $args['author__in'] = $author_id;
                } elseif ($settings['author_from'] == 'current_user') {
                    $args['author__in'] = get_current_user_id();
                }
            }
            // Meta Key query filter
            if (\in_array('metakey', $settings['query_filter'])) {
                if ($settings['metakey_from'] == 'post_metakey') {
                    if ($settings['include_metakey'] && $settings('include_metakey_combination')) {
                        $args['meta_query'][] = [
                            'key' => $settings['include_metakey'],
                            // 'value'   => '',
                            'compare' => $settings['include_metakey_combination'],
                        ];
                    }
                    if ($settings['exclude_metakey'] && $settings('exclude_metakey_combination')) {
                        $args['meta_query'][] = [
                            'key' => $settings['exclude_metakey'],
                            // 'value'   => '',
                            'compare' => $settings['exclude_metakey_combination'],
                        ];
                    }
                } elseif ($settings['metakey_from'] == 'post_meta') {
                    if ($settings['metakey_field_meta']) {
                        $args['meta_query'][] = ['key' => $settings['metakey_field_meta'], 'value' => sanitize_text_field($settings['metakey_field_meta_value']), 'compare' => $settings['metakey_field_meta_operator']];
                    }
                } elseif ($settings['metakey_from'] == 'dynamicstring') {
                    if ($settings['metakey_field_meta_string']) {
                        $args['meta_query'][] = ['value' => sanitize_text_field($settings['metakey_field_meta_string'])];
                    }
                }
            }
        }
        $query_p = new \WP_Query($args);
        $this->query = $query_p;
        $this->query_args = $args;
    }
    public function get_terms_query($settings = null, $id_page = null)
    {
        $terms_query = 'all';
        if (!$settings) {
            $settings = $this->get_settings_for_display();
        }
        if (!$id_page) {
            $id_page = get_the_ID();
        }
        if ($settings['taxonomy']) {
            if ($settings['terms_current_post']) {
                // Da implementare oR & AND tems ...
                if (is_singular()) {
                    $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                    if (!empty($terms_list)) {
                        $terms_query = [];
                        foreach ($terms_list as $akey => $aterm) {
                            if (\is_object($aterm) && \get_class($aterm) == 'WP_Term') {
                                if (!\in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                }
                if (is_archive()) {
                    if (is_tax() || is_category() || is_tag()) {
                        $queried_object = get_queried_object();
                        $terms_query = [$queried_object->term_id];
                    }
                }
            }
            if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                $terms_query = $settings['terms_' . $settings['taxonomy']];
                // add current post terms id
                $dce_key = \array_search('dce_current_post_terms', $terms_query);
                if ($dce_key !== \false) {
                    unset($terms_query[$dce_key]);
                    $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], ['orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true]);
                    if (!empty($terms_list)) {
                        $terms_query = [];
                        foreach ($terms_list as $akey => $aterm) {
                            if (!\in_array($aterm->term_id, $terms_query)) {
                                $terms_query[] = $aterm->term_id;
                            }
                        }
                    }
                }
            }
        }
        return $terms_query;
    }
}
