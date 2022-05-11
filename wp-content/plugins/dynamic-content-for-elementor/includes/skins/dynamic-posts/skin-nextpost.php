<?php

namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin_NextPost extends \DynamicContentForElementor\Includes\Skins\Skin_Base
{
    protected function _register_controls_actions()
    {
        add_action('elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [$this, 'register_additional_nextpost_controls']);
    }
    public $depended_scripts = ['dce-dynamicPosts-nextpost'];
    public $depended_styles = ['dce-dynamicPosts-grid', 'dce-dynamicPosts-nextpost'];
    public function get_id()
    {
        return 'nextpost';
    }
    public function get_title()
    {
        return __('Next Post', 'dynamic-content-for-elementor');
    }
    public function register_additional_nextpost_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_nextpost', ['label' => '<i class="dynicon eicon-parallax"></i> ' . __('Next Post', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('nextpost_use_date', ['label' => '<i class="far fa-calendar-alt"></i>&nbsp;&nbsp;' . __('Show Date', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('nextpost_date_format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'd/<b>m</b>/y', 'dynamic' => ['active' => \false], 'condition' => [$this->get_control_id('nextpost_use_date') => 'yes']]);
        $this->add_control('nextpost_use_author', ['label' => '<i class="far fa-user-circle"></i>&nbsp;&nbsp;' . __('Show Author', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'default' => 'yes']);
        $this->add_control('nextpost_use_authoravatar', ['label' => '&nbsp;&nbsp;&nbsp;' . __('Show Avatar', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [$this->get_control_id('nextpost_use_author') => 'yes']]);
        $this->add_control('nextpost_use_terms', ['label' => '<i class="far fa-folder-open"></i>&nbsp;&nbsp;' . __('Show Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'default' => 'yes']);
        $this->add_control('nextpost_taxonomy_filter', ['label' => __('Filter Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'label_block' => \true, 'multiple' => \true, 'options' => Helper::get_taxonomies(), 'placeholder' => __('Auto', 'dynamic-content-for-elementor'), 'description' => __('Use only terms in selected taxonomies. If empty all terms will be used.', 'dynamic-content-for-elementor'), 'condition' => [$this->get_control_id('nextpost_use_terms') => 'yes']]);
        $this->add_control('nextpost_use_next', ['label' => '<i class="far fa-caret-square-right"></i>&nbsp;&nbsp;' . __('Show Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'default' => 'yes']);
        $this->add_control('nextpost_heading_content', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => '<i class="fas fa-align-left"></i>&nbsp;&nbsp;' . __('Content', 'dynamic-content-for-elementor'), 'label_block' => \false, 'content_classes' => 'dce-icon-heading', 'separator' => 'before']);
        $this->add_control('nextpost_use_template', ['label' => '<i class="fas fa-cogs"></i>&nbsp;&nbsp;' . __('Dynamic Template', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('nextpost_content_template_id', ['label' => __('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'separator' => 'after', 'frontend_available' => \true, 'condition' => [$this->get_control_id('nextpost_use_template') => 'yes']]);
        $this->add_responsive_control('nextpost_content_width', ['label' => '<i class="fas fa-arrows-alt-h"></i>&nbsp;&nbsp;' . __('Content Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', '%', 'vw'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 1200, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-content' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('nextpost_content_padding', ['label' => '<i class="fas fa-arrows-alt"></i>&nbsp;&nbsp;' . __('Content Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_nextpost', ['label' => __('Next Post', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        // ------------------- TITLE
        $this->add_control('nextpost_heading_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        //title color
        $this->add_control('nextpost_title_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-big-image .dce-inner .dce-fader .dce-text .dce-title' => 'color: {{VALUE}};']]);
        //title typography
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'nextpost_title_typography', 'selector' => '{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-big-image .dce-inner .dce-fader .dce-text .dce-title']);
        // ------------------- BEFORE (terms)
        $this->add_control('nextpost_heading_terms', ['label' => __('Before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        //terms color
        $this->add_control('nextpost_terms_color', ['label' => __('Terms Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline' => 'color: {{VALUE}};']]);
        //terms typography. BEFORE
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'nextpost_before_typography', 'label' => __('Typography Before', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-before, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-before']);
        //terms space
        $this->add_responsive_control('nextpost_terms_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 80, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline .dce-termstaxonomy span' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('nextpost_before_gutter', ['label' => __('Gutter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 80, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-before, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-before' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        // ------------------- AFTER (date and author)
        $this->add_control('nextpost_heading_after', ['label' => __('After', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        //date color
        $this->add_control('nextpost_date_color', ['label' => __('Date Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline .dce-date' => 'color: {{VALUE}};']]);
        //author color
        $this->add_control('nextpost_author_color', ['label' => __('Author Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline .dce-author' => 'color: {{VALUE}};']]);
        //author typography.  AFTER
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'nextpost_after_typography', 'label' => __('Typography After', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-after, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-after']);
        //terms space
        $this->add_responsive_control('nextpost_after_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 80, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline .dce-date' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('nextpost_after_gutter', ['label' => __('Gutter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'default' => ['size' => ''], 'range' => ['px' => ['max' => 80, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-after, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader .dce-text .dce-byline-after' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        // ------------------- OVERLAY
        $this->add_control('nextpost_heading_overlay', ['label' => __('Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        //overlay color CURRENT
        $this->add_control('nextpost_overlay_bgcolor', ['label' => __('Overlay Color of current post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-current .dce-big-image .dce-inner .dce-fader, {{WRAPPER}} .dce-nextpost-wrapper .dce-page.easing-upward .dce-big-image .dce-inner .dce-fader' => 'background-color: {{VALUE}};']]);
        //overlay color NEXT
        $this->add_control('nextpost_overlay_bgcolor_next', ['label' => __('Overlay Color of next post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-next .dce-big-image .dce-inner .dce-fader' => 'background-color: {{VALUE}};']]);
        $this->add_responsive_control('nextpost_height_netx', ['label' => __('Next Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-nextpost-wrapper .dce-page.dce-next.dce-content-hidden .dce-big-image' => 'height: {{SIZE}}%;']]);
        $this->end_controls_section();
    }
    protected function render_posts_wrapper_before()
    {
        $_skin = $this->get_parent()->get_settings('_skin');
        $content_template_id = $this->get_instance_value('nextpost_content_template_id');
        if ($content_template_id) {
            echo '<div class="dce-hidden">' . do_shortcode('[dce-elementor-template id="' . $content_template_id . '" post_id="' . $this->current_id . '"]') . '</div>';
        }
        ?>

		<article class='dce-page dce-hidden'>
			<div class='dce-big-image'>

				<div class='dce-inner'>
				  <div class='dce-fader'>
					<div class='dce-text'>

					  <?php 
        if ($this->get_instance_value('nextpost_use_next')) {
            ?>
					  <div class='goto-next'>Next</div>
					  <?php 
        }
        ?>

					  <div class='dce-byline dce-byline-before'>

						<?php 
        if ($this->get_instance_value('nextpost_use_terms')) {
            ?>
						  <div class='dce-termstaxonomy dce-item_termstaxonomy'></div>
						<?php 
        }
        ?>

					  </div>
					  <div class="dce-item_title">
					  <h1 class='dce-title'></h1>
						</div>
						 <div class='dce-byline dce-byline-after'>

						   <?php 
        if ($this->get_instance_value('nextpost_use_date')) {
            ?>
								   <div class="dce-item_date dce-date"></div>
						   <?php 
        }
        ?>

						   <?php 
        if ($this->get_instance_value('nextpost_use_author')) {
            ?>
								<?php 
            if ($this->get_instance_value('nextpost_use_authoravatar')) {
                ?>
								   <span class='dce-author-avatar dce-author-avatar'><img src="" /></span>
							<?php 
            }
            ?>
								   <span class='dce-author dce-item_author'></span>
						   <?php 
        }
        ?>

						</div>
					</div>
				  </div>
				</div>
			</div>
			<div class='dce-content dce-item_content elementor-repeater-item-item_content'>
			  <div class='dce-text'></div>
			</div>
		</article>
		<?php 
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<div class="dce-nota-nextpost">Render <span class="elementor-control-spinner dce-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span></div>';
        }
        ?>
		<script>
			var dceAjaxPath = {"ajaxurl": "<?php 
        echo admin_url('admin-ajax.php');
        ?>"};
			var dce_listPosts_<?php 
        echo $this->get_parent()->get_id();
        ?> = [
		<?php 
    }
    protected function render_posts_wrapper_after()
    {
        ?>
		];
		</script>
		<?php 
    }
    protected function render_post()
    {
        global $post;
        $separatorArray = '';
        if ($this->counter) {
            $separatorArray = ',';
        }
        $p_id = $this->current_id;
        $p_title = wp_kses_post(get_the_title());
        $p_slug = $post->post_name;
        $p_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        $p_author = get_the_author_meta('display_name');
        $p_authorimage = get_avatar_url(get_the_author_meta('ID'));
        $p_date = get_the_date(wp_kses_post($this->get_instance_value('nextpost_date_format')), '');
        $p_type = get_post_type_object(get_post_type())->rest_base;
        if (empty($p_type)) {
            $p_type = get_post_type();
        }
        $p_terms = '';
        $taxonomy = get_post_taxonomies($this->current_id);
        $cont = 0;
        $taxonomy_filter = $this->get_instance_value('nextpost_taxonomy_filter');
        foreach ($taxonomy as $tax) {
            if (isset($taxonomy_filter) && !empty($taxonomy_filter)) {
                if (!\in_array($tax, $taxonomy_filter)) {
                    continue;
                }
            }
            if ($tax != 'post_format') {
                $term_list = Helper::get_the_terms_ordered($this->current_id, $tax);
                if ($term_list && \is_array($term_list) && \count($term_list) > 0) {
                    foreach ($term_list as $key => $term) {
                        $sep = $cont ? ',' : '';
                        $p_terms .= $sep . $term->name;
                        $cont++;
                    }
                }
            }
        }
        $p_link = $this->current_permalink;
        echo $separatorArray . '{"id":"' . $p_id . '",';
        echo '"index":"' . $this->counter . '",';
        echo '"slug":"' . $p_slug . '",';
        echo '"title":"' . $p_title . '",';
        echo '"link":"' . $p_link . '",';
        echo '"image":"' . $p_image[0] . '",';
        echo '"author":"' . $p_author . '",';
        echo '"authorimage":"' . $p_authorimage . '",';
        echo '"date":"' . $p_date . '",';
        echo '"type":"' . $p_type . '",';
        echo '"terms": "' . $p_terms . '"}';
        $this->counter++;
    }
    // Classes ----------
    public function get_container_class()
    {
        return 'dce-nextpost-container dce-skin-' . $this->get_id();
    }
    public function get_wrapper_class()
    {
        return 'dce-grid-nextpost dce-nextpost-wrapper dce-wrapper-' . $this->get_id();
    }
    public function get_item_class()
    {
        return 'dce-grid__item dce-item-' . $this->get_id();
    }
    public function get_image_class()
    {
        return 'dce-img-el';
    }
}
