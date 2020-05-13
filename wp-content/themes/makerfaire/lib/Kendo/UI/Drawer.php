<?php

namespace Kendo\UI;

class Drawer extends \Kendo\UI\Widget {
    protected $ignore = array("content");

    public function name() {
        return 'Drawer';
    }

    protected function createElement() {
        $element = new \Kendo\Html\Element('div');

        $content = $this->getProperty('content');

        if($content) {
            $element->html($content);
        }

        return $element;
    }

    public function html() {
        $element = $this->createElement();

        $this->addAttributes($element);

        return $element->outerHtml();
    }
//>> Properties

    /**
    * The position of the drawer. Can be left (default) or right.
    * @param string $value
    * @return \Kendo\UI\Drawer
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * Determines how the Kendo UI Drawer will interact with the associated content. The default one (overlay) will simply overlap the associated content with overlay effect. On the other hand "push" mode will show the drawer next to associated cotent. The associated content will shrink its content.
    * @param string $value
    * @return \Kendo\UI\Drawer
    */
    public function mode($value) {
        return $this->setProperty('mode', $value);
    }

    /**
    * Sets the template option of the Drawer.
    * Specifies the drawer's content.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Drawer
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Drawer.
    * Specifies the drawer's content.
    * @param string $value The template content.
    * @return \Kendo\UI\Drawer
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Specifies the minimum height for the drawer in push mode. The overlay mode takes 100% of the page height.
    * @param float $value
    * @return \Kendo\UI\Drawer
    */
    public function minHeight($value) {
        return $this->setProperty('minHeight', $value);
    }

    /**
    * Enables or configures the mini mode for the Kendo UI Drawer. This is a compact view that is displayed when the Kendo UI Drawer is collapsed. Usually it used to show only the icons when the drawer content contains icon and text for an item. When set to true it uses the main template.
    * @param boolean|\Kendo\UI\DrawerMini|array $value
    * @return \Kendo\UI\Drawer
    */
    public function mini($value) {
        return $this->setProperty('mini', $value);
    }

    /**
    * If set to false, swiping the associated content will not activate the drawer. In this case, the drawer will only be open by calling the show method. should be disabled for browsers, which use side swiping gestures for back/forward navigation, such as iOS Safari. Otherwise, users should swipe from an inner part of the view, and not from the view edge.
    * @param boolean $value
    * @return \Kendo\UI\Drawer
    */
    public function swipeToOpen($value) {
        return $this->setProperty('swipeToOpen', $value);
    }

    /**
    * Defines a specific width for the Kendo UI Drawer when expanded.
    * @param float $value
    * @return \Kendo\UI\Drawer
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Sets the hide event of the Drawer.
    * Fired when the Kendo UI Drawer is about to be hidden. The event can be prevented by calling the preventDefault method of the event parameter.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Drawer
    */
    public function hide($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('hide', $value);
    }

    /**
    * Sets the show event of the Drawer.
    * Fires before the Kendo UI Drawer is revealed. The event can be prevented by calling the preventDefault method of the event parameter.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Drawer
    */
    public function show($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('show', $value);
    }

    /**
    * Sets the itemClick event of the Drawer.
    * Fires when user clicks on item from the Kendo UI Drawer.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Drawer
    */
    public function itemClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('itemClick', $value);
    }


//<< Properties

    /**
    * Specifies the associated content for the drawer
    * @param string $value
    * @return \Kendo\UI\Drawer
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

/**
    * Starts output bufferring. Any following markup will be set as the content of the ScrollViewItem.
    */
    public function startContent() {
        ob_start();
    }

    /**
    * Stops output bufferring and sets the preceding markup as the content of the ScrollViewItem.
    */
    public function endContent() {
        $this->content(ob_get_clean());
    }
}

?>
