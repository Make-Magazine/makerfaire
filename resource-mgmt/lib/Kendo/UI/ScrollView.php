<?php

namespace Kendo\UI;

class ScrollView extends \Kendo\UI\Widget {
    protected $ignore = array('items');

    public function name() {
        return 'ScrollView';
    }
    protected function createElement() {
        $element = new \Kendo\Html\Element('div');

        $items = $this->getProperty('items');

        if ($items) {
            foreach($items as $item) {

                $contentElement = $item->createContentElement();

                if ($contentElement) {
                    $element->append($contentElement);
                }
            }
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
    * If set to false the widget will not bind to the DataSource during initialization.
    * @param boolean $value
    * @return \Kendo\UI\ScrollView
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * The intensity of the swipe after which the swipe will result in a bounce effect when the user scrolls to the next page. Higher values require more accelerative swipe to notice the bounce effect when a page is changed.
    * @param float $value
    * @return \Kendo\UI\ScrollView
    */
    public function bounceVelocityThreshold($value) {
        return $this->setProperty('bounceVelocityThreshold', $value);
    }

    /**
    * Sets the data source of the ScrollView.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\ScrollView
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * The duration (in milliseconds) for the ScrollView to snap to the current page after the user releases it.
    * @param float $value
    * @return \Kendo\UI\ScrollView
    */
    public function duration($value) {
        return $this->setProperty('duration', $value);
    }

    /**
    * Sets the emptyTemplate option of the ScrollView.
    * The template for rendering the pages without content. By default, the ScrollView renders a blank page.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\ScrollView
    */
    public function emptyTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('emptyTemplate', $value);
    }

    /**
    * Sets the emptyTemplate option of the ScrollView.
    * The template for rendering the pages without content. By default, the ScrollView renders a blank page.
    * @param string $value The template content.
    * @return \Kendo\UI\ScrollView
    */
    public function emptyTemplate($value) {
        return $this->setProperty('emptyTemplate', $value);
    }

    /**
    * If set to true, the ScrollView will display a pager. By default, the pager is enabled.
    * @param boolean $value
    * @return \Kendo\UI\ScrollView
    */
    public function enablePager($value) {
        return $this->setProperty('enablePager', $value);
    }

    /**
    * The initial page that will be displayed.
    * @param float $value
    * @return \Kendo\UI\ScrollView
    */
    public function page($value) {
        return $this->setProperty('page', $value);
    }

    /**
    * Sets the template option of the ScrollView.
    * The template for rendering the content of the pages. By default, the ScrollView renders a div element for every page.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\ScrollView
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ScrollView.
    * The template for rendering the content of the pages. By default, the ScrollView renders a div element for every page.
    * @param string $value The template content.
    * @return \Kendo\UI\ScrollView
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The length of the horizontal swipe after which a swipe will navigate to the next page - as opposed to snapping back to the current page. Higher values require long area swipes to navigate to the next page.
    * @param float $value
    * @return \Kendo\UI\ScrollView
    */
    public function velocityThreshold($value) {
        return $this->setProperty('velocityThreshold', $value);
    }

    /**
    * The height of the ScrollView content.
    * @param string $value
    * @return \Kendo\UI\ScrollView
    */
    public function contentHeight($value) {
        return $this->setProperty('contentHeight', $value);
    }

    /**
    * Adds ScrollViewItem to the ScrollView.
    * @param \Kendo\UI\ScrollViewItem|array,... $value one or more ScrollViewItem to add.
    * @return \Kendo\UI\ScrollView
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

    /**
    * Sets the change event of the ScrollView.
    * Fires when the ScrollView page is changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ScrollView
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the refresh event of the ScrollView.
    * Fires when the ScrollView refreshes.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ScrollView
    */
    public function refresh($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('refresh', $value);
    }


//<< Properties
}

?>
