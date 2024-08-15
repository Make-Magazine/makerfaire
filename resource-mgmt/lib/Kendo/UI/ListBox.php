<?php

namespace Kendo\UI;

class ListBox extends \Kendo\UI\Widget {
    public function name() {
        return 'ListBox';
    }

    protected function createElement() {
        return new \Kendo\Html\Element('select');
    }
//>> Properties

    /**
    * If set to false, the widget will not bind to the data source during initialization. In this case, the data binding will occur when the change event of the data source is fired. By default, the ListBox will bind to the data source that is specified in the configuration.
    * @param boolean $value
    * @return \Kendo\UI\ListBox
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * The id of the target ListBox to which items from the source ListBox will be transferred and vice versa. If you have to transfer items from the target ListBox over its toolbar, then you also need to set its connectWith option.
    * @param string $value
    * @return \Kendo\UI\ListBox
    */
    public function connectWith($value) {
        return $this->setProperty('connectWith', $value);
    }

    /**
    * Sets the data source of the ListBox.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\ListBox
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * The field of the data item that provides the text content of the list items. Based on this field, the widget filters the data source.
    * @param string $value
    * @return \Kendo\UI\ListBox
    */
    public function dataTextField($value) {
        return $this->setProperty('dataTextField', $value);
    }

    /**
    * The field of the data item that provides the value of the widget.
    * @param string $value
    * @return \Kendo\UI\ListBox
    */
    public function dataValueField($value) {
        return $this->setProperty('dataValueField', $value);
    }

    /**
    * Indicates whether the ListBox items can be dragged and dropped.
    * @param boolean|\Kendo\UI\ListBoxDraggable|array $value
    * @return \Kendo\UI\ListBox
    */
    public function draggable($value) {
        return $this->setProperty('draggable', $value);
    }

    /**
    * Array of id strings which determines the ListBoxes that can drag and drop their items to the current ListBox. The dropSources option describes a one way relationship. If you want a two-way connection, then set the dropSources option on both widgets.
    * @param array $value
    * @return \Kendo\UI\ListBox
    */
    public function dropSources($value) {
        return $this->setProperty('dropSources', $value);
    }

    /**
    * Indicates whether the keyboard navigation is enabled or disabled.
    * @param boolean $value
    * @return \Kendo\UI\ListBox
    */
    public function navigatable($value) {
        return $this->setProperty('navigatable', $value);
    }

    /**
    * Defines the localization texts for the ListBox. Used primarily for localization.
    * @param \Kendo\UI\ListBoxMessages|array $value
    * @return \Kendo\UI\ListBox
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Indicates whether the selection is single or multiple. The possible values are: - "single" - A single-item selection. - "multiple" - A multiple-item selection.
    * @param string $value
    * @return \Kendo\UI\ListBox
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * Sets the template option of the ListBox.
    * Specifies the item template of the ListBox.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\ListBox
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ListBox.
    * Specifies the item template of the ListBox.
    * @param string $value The template content.
    * @return \Kendo\UI\ListBox
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Defines the settings for displaying the toolbar of the ListBox. The toolbar allows you to execute a set of predefined actions.By default, the toolbar is not displayed. If the tools array is populated, then the toolbar and the corresponding tools are displayed.
    * @param \Kendo\UI\ListBoxToolbar|array $value
    * @return \Kendo\UI\ListBox
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * Sets the add event of the ListBox.
    * Fires before an item is added to the ListBox.The function context of the event handler (available through the this keyword) that will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function addEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('add', $value);
    }

    /**
    * Sets the change event of the ListBox.
    * Fires when the ListBox selection has changed.The function context of the event handler (available through the this keyword) that will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the dataBound event of the ListBox.
    * Fires when the ListBox has received data from the data source and is already rendered.The function context of the event handler (available through the this keyword) that will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the dragstart event of the ListBox.
    * Fires when the dragging of the ListBox items starts.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function dragstart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragstart', $value);
    }

    /**
    * Sets the drag event of the ListBox.
    * Fires when the placeholder of the ListBox changes its position.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function drag($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drag', $value);
    }

    /**
    * Sets the drop event of the ListBox.
    * Fires when a ListBox item is dropped over one of the drop targets.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function drop($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drop', $value);
    }

    /**
    * Sets the dragend event of the ListBox.
    * Fires when the dragging of the item ends but before its position is changed in the DOM.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function dragend($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragend', $value);
    }

    /**
    * Sets the remove event of the ListBox.
    * Fires before an item is removed from the ListBox.The function context of the event handler (available through the this keyword) that will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the reorder event of the ListBox.
    * Fires when ListBox items are reordered.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\ListBox
    */
    public function reorder($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('reorder', $value);
    }


//<< Properties
}

?>
