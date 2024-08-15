<?php

namespace Kendo\UI;

class Window extends \Kendo\UI\Widget {
    protected $ignore = array('content');

    protected function name() {
        return 'Window';
    }

    protected function createElement() {
        $element = new \Kendo\Html\Element('div');

        $content = $this->getProperty('content');

        if (gettype($content) == "string") {
            $element->html($content);
        } else {
            // serialize content by removing it from the ignore list
            $this->ignore = array_diff(array('content'), $this->ignore);
        }

        return $element;
    }
//>> Properties

    /**
    * The buttons for interacting with the Window.The predefined array values are: Close; Refresh; Minimize; Maximize or Pin.
    * @param array $value
    * @return \Kendo\UI\Window
    */
    public function actions($value) {
        return $this->setProperty('actions', $value);
    }

    /**
    * A collection of {Animation} objects that is used to change the default animations. When set to false, all animations will be disabled.
    * @param boolean|\Kendo\UI\WindowAnimation|array $value
    * @return \Kendo\UI\Window
    */
    public function animation($value) {
        return $this->setProperty('animation', $value);
    }

    /**
    * The element to which the Window will be appended. It is beneficial to use the Window together with a form which does not constrain the dragging of the Window within the specific element. For such scenarios, use the draggable.containment setting.
    * @param |string $value
    * @return \Kendo\UI\Window
    */
    public function appendTo($value) {
        return $this->setProperty('appendTo', $value);
    }

    /**
    * Determines whether the Window will be focused automatically when opened. The property also influences the focus behavior when an already opened Window is clicked.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function autoFocus($value) {
        return $this->setProperty('autoFocus', $value);
    }

    /**
    * Enables (true) or disables (false) the dragging of the widget.
    * @param boolean|\Kendo\UI\WindowDraggable|array $value
    * @return \Kendo\UI\Window
    */
    public function draggable($value) {
        return $this->setProperty('draggable', $value);
    }

    /**
    * Explicitly states whether a content iframe will be created. For more information, refer to the documentation on using iframes.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function iframe($value) {
        return $this->setProperty('iframe', $value);
    }

    /**
    * Specifies the height of the Window.
    * @param float|string $value
    * @return \Kendo\UI\Window
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * The maximum height (in pixels) that may be achieved by resizing the Window.
    * @param float $value
    * @return \Kendo\UI\Window
    */
    public function maxHeight($value) {
        return $this->setProperty('maxHeight', $value);
    }

    /**
    * The maximum width (in pixels) that may be achieved by resizing the Window.
    * @param float $value
    * @return \Kendo\UI\Window
    */
    public function maxWidth($value) {
        return $this->setProperty('maxWidth', $value);
    }

    /**
    * The minimum height (in pixels) that may be achieved by resizing the Window.
    * @param float $value
    * @return \Kendo\UI\Window
    */
    public function minHeight($value) {
        return $this->setProperty('minHeight', $value);
    }

    /**
    * The minimum width (in pixels) that may be achieved by resizing the Window.
    * @param float $value
    * @return \Kendo\UI\Window
    */
    public function minWidth($value) {
        return $this->setProperty('minWidth', $value);
    }

    /**
    * Specifies whether the Window will display a modal overlay over the page.
    * @param boolean|\Kendo\UI\WindowModal|array $value
    * @return \Kendo\UI\Window
    */
    public function modal($value) {
        return $this->setProperty('modal', $value);
    }

    /**
    * Specifies whether the Window will be pinned, that is, that it will not move together with the page content during scrolling.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function pinned($value) {
        return $this->setProperty('pinned', $value);
    }

    /**
    * A collection of one or two members which define the initial top and/or left position of the Window or the position of the containment element on the page.
    * @param \Kendo\UI\WindowPosition|array $value
    * @return \Kendo\UI\Window
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * Enables (true) or disables (false) the resizing of the Window.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function resizable($value) {
        return $this->setProperty('resizable', $value);
    }

    /**
    * Enables (true) or disables (false) the scrolling of the Window contents.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function scrollable($value) {
        return $this->setProperty('scrollable', $value);
    }

    /**
    * The text in the title bar of the Window. If set to false, the Window will be displayed without a title bar.
    * @param string|boolean|\Kendo\UI\WindowTitle|array $value
    * @return \Kendo\UI\Window
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * Specifies whether the Window will be initially visible.
    * @param boolean $value
    * @return \Kendo\UI\Window
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * Specifies the width of the Window.
    * @param float|string $value
    * @return \Kendo\UI\Window
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Sets a predefined size to the Window. The width and height configuration options override the predefined size.The supported values are: auto; small; medium or large.
    * @param string $value
    * @return \Kendo\UI\Window
    */
    public function size($value) {
        return $this->setProperty('size', $value);
    }

    /**
    * Sets the activate event of the Window.
    * Triggered when a Window has finished its opening animation.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function activate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('activate', $value);
    }

    /**
    * Sets the close event of the Window.
    * Triggered when a Window is closed either by the user or through the close() method.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the deactivate event of the Window.
    * Triggered when a Window has finished its closing animation.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function deactivate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('deactivate', $value);
    }

    /**
    * Sets the dragend event of the Window.
    * Triggered when a Window has been moved by the user.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function dragend($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragend', $value);
    }

    /**
    * Sets the dragstart event of the Window.
    * Triggered when the user starts to move the Window.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function dragstart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragstart', $value);
    }

    /**
    * Sets the error event of the Window.
    * Triggered when an Ajax request for content fails.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function error($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('error', $value);
    }

    /**
    * Sets the maximize event of the Window.
    * Triggered when the user maximizes the Window. Introduced in 2016.Q1.SP1.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function maximize($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('maximize', $value);
    }

    /**
    * Sets the minimize event of the Window.
    * Triggered when the user minimizes the Window. Introduced in 2016.Q1.SP1.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function minimize($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('minimize', $value);
    }

    /**
    * Sets the open event of the Window.
    * Triggered when a Window is opened, that is, when the open() method is called.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the refresh event of the Window.
    * Triggered when the content of a Window has finished loading via Ajax, when the Window iframe has finished loading, or when the Refresh button has been clicked on a Window with static content.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function refresh($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('refresh', $value);
    }

    /**
    * Sets the resize event of the Window.
    * Triggered when the user resizes the Window.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Window
    */
    public function resize($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('resize', $value);
    }


    /**
    * Sets the HTML content of the Window.
    * @param string $value
    * @return \Kendo\UI\Window
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

    /**
    * Starts output bufferring. Any following markup will be set as the content of the Window.
    */
    public function startContent() {
        ob_start();
    }

    /**
    * Stops output bufferring and sets the preceding markup as the content of the Window.
    */
    public function endContent() {
        $this->content(ob_get_clean());
    }

//<< Properties
}

?>
