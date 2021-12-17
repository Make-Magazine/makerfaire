<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class StickyPosts extends \DynamicContentForElementor\Widgets\DCE_Widget_DynamicPosts_Base
{
    public function get_name()
    {
        return 'dce-sticky-posts';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Sticky_Posts_Skin_Grid($this));
        $this->add_skin(new Skins\Sticky_Posts_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Sticky_Posts_Skin_Carousel($this));
        $this->add_skin(new Skins\Sticky_Posts_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Sticky_Posts_Skin_Timeline($this));
    }
    protected function _register_controls()
    {
        parent::_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'sticky_posts']);
    }
}
