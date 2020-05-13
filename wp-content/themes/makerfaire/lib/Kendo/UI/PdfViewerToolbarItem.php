<?php

namespace Kendo\UI;

class PDFViewerToolbarItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function overflow($value) {
        return $this->setProperty('overflow', $value);
    }

    /**
    * Default commands in the PDF Viewer are: OpenCommand; PageChangeCommand; DownloadCommand; EnableSelectionCommand; EnablePanCommand; ExportCommand; PrintCommand; OpenSearchCommand or ZoomCommand.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function command($value) {
        return $this->setProperty('command', $value);
    }

    /**
    * Specifies the tool's name. Tool definition will be taken from the default collection - kendo.pdfviewer.DefaultTools
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Sets the click option of the PDFViewerToolbarItem.
    * Specifies the click event handler of the button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * Sets the toggle option of the PDFViewerToolbarItem.
    * Specifies the toggle event handler of the button. Applicable only for commands of type button and togglable: true.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function toggle($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('toggle', $value);
    }

    /**
    * Specifies if the button is togglable, e.g. has a selected and unselected state.
    * @param boolean $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function togglable($value) {
        return $this->setProperty('togglable', $value);
    }

    /**
    * Sets the text of the button.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Sets the template option of the PDFViewerToolbarItem.
    * Specifies what element will be added in the ToolBar wrapper. Items with template does not have a type.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the PDFViewerToolbarItem.
    * Specifies what element will be added in the ToolBar wrapper. Items with template does not have a type.
    * @param string $value The template content.
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Specifies where the text will be displayed. Possible values are: "toolbar", "overflow" or "both" (default).
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function showText($value) {
        return $this->setProperty('showText', $value);
    }

    /**
    * Specifies whether the button is primary. Primary buttons receive different styling.
    * @param boolean $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function primary($value) {
        return $this->setProperty('primary', $value);
    }

    /**
    * Specifies the HTML attributes of a ToolBar button.
    * @param  $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function attributes($value) {
        return $this->setProperty('attributes', $value);
    }

    /**
    * Specifies whether the control is initially enabled or disabled. Default value is "true".
    * @param boolean $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function enable($value) {
        return $this->setProperty('enable', $value);
    }

    /**
    * Determines if a button is visible or hidden. By default buttons are visible.
    * @param boolean $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function hidden($value) {
        return $this->setProperty('hidden', $value);
    }

    /**
    * Defines a CSS class (or multiple classes separated by spaces) which will be used for button icon.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function spriteCssClass($value) {
        return $this->setProperty('spriteCssClass', $value);
    }

    /**
    * If set, the ToolBar will render an image with the specified URL in the button.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function imageUrl($value) {
        return $this->setProperty('imageUrl', $value);
    }

    /**
    * Specifies where the button icon will be displayed. Possible values are: "toolbar", "overflow" or "both" (default).
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function showIcon($value) {
        return $this->setProperty('showIcon', $value);
    }

    /**
    * Sets icon for the item. The icon should be one of the existing in the Kendo UI theme sprite.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function icon($value) {
        return $this->setProperty('icon', $value);
    }

    /**
    * Specifies the ID of the button.
    * @param string $value
    * @return \Kendo\UI\PDFViewerToolbarItem
    */
    public function id($value) {
        return $this->setProperty('id', $value);
    }

//<< Properties
}

?>
