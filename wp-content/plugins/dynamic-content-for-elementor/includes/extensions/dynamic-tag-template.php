<?php

namespace DynamicContentForElementor\Extensions;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Extension_Template extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    public function init($param = null)
    {
        parent::init();
        $this->add_dynamic_tags();
    }
}
