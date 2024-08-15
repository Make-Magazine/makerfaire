<?php

namespace Kendo\UI;

class Breadcrumb extends \Kendo\UI\Widget {
    public function name() {
        return 'Breadcrumb';
    }

    protected function createElement() {
        $tag = $this->getProperty('tag');

        if (!$tag) {
            $tag = 'nav';
        }

        $element = new \Kendo\Html\Element($tag);

        $this->addAttributes($element);

        return $element;
    }

//>> Properties

    /**
    * Indicates whether the Breadcrumb will enable/disable the binding to the location object of the browser on initialization.
    * @param boolean $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function bindToLocation($value) {
        return $this->setProperty('bindToLocation', $value);
    }

    /**
    * Defines a name of an existing icon in the Kendo UI Web Font Icons. The icon will be applied as separator between the segments of the Breadcrumb path.
    * @param string $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function delimiterIcon($value) {
        return $this->setProperty('delimiterIcon', $value);
    }

    /**
    * Indicates whether the editing functionality of the Breadcrumb will be enabled/disabled.If the option is enabled the path will be editable. Clicking in an empty area of the component will trigger editing mode. Editing mode shows an input showing the value of the component enabling the end user to type a new path.
    * @param boolean $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * Adds BreadcrumbItem to the Breadcrumb.
    * @param \Kendo\UI\BreadcrumbItem|array,... $value one or more BreadcrumbItem to add.
    * @return \Kendo\UI\Breadcrumb
    */
    public function addItem($value) {
        return $this->add('items', func_get_args());
    }

    /**
    * Defines the space in pixels after the last item to stay empty.The gap value is taken into account when items overflow and continues to remain empty.
    * @param float $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function gap($value) {
        return $this->setProperty('gap', $value);
    }

    /**
    * Defines the text of the root icon title that is displayed within the Breadcrumb.
    * @param \Kendo\UI\BreadcrumbMessages|array $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Indicates whether the navigation functionality of the Breadcrumb will be enabled/disabled.When navigational is set to false, automatic navigation (changing url location) is disabled by default. In this state, the click event will be prevented and navigation will occur only if programmatic navigation is implemented.When navigational is set to true, the url (path) will be automatically added to the href attribute of the rendered links. In this state, the click event will trigger navigation.
    * @param boolean $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function navigational($value) {
        return $this->setProperty('navigational', $value);
    }

    /**
    * Defines a name of an existing icon in the Kendo UI Web Font Icons. The icon will be applied as the first item(root) of Breadcrumb path.The root icon is clickable and resets the value of the component.
    * @param string $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function rootIcon($value) {
        return $this->setProperty('rootIcon', $value);
    }

    /**
    * Defines the value/path of the component. Each segments is separated by a slash.
    * @param string $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Sets the click event of the Breadcrumb.
    * Fires when an item or a rootitem is clicked.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Breadcrumb
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * Sets the change event of the Breadcrumb.
    * Fires when the value of the Breadcrumb is changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Breadcrumb
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }


//<< Properties

    /**
    * Defines the tag, which the Breadcrumb will render.
    * @param string $value
    * @return \Kendo\UI\Breadcrumb
    */
    public function tag($value) {
        return $this->setProperty('tag', $value);
    }
}

?>
