<?php

namespace Kendo\UI;

class DropDownTree extends \Kendo\UI\Widget {
    public function name() {
        return 'DropDownTree';
    }

    protected function createElement() {
        $tag = $this->getProperty('checkboxes');

        if ($tag) {
           return new \Kendo\Html\Element('select');
        } else {
            return new \Kendo\Html\Element('input', true);
        }
    }
//>> Properties

    /**
    * Configures the opening and closing animations of the suggestion popup. Setting the animation option to false will disable the opening and closing animations. As a result, the suggestion popup will open and close instantly. is not a valid configuration.
    * @param boolean|\Kendo\UI\DropDownTreeAnimation|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function animation($value) {
        return $this->setProperty('animation', $value);
    }

    /**
    * Controls whether to bind the widget to the data source on initialization.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Controls whether to close the popup when item is selected or checked.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function autoClose($value) {
        return $this->setProperty('autoClose', $value);
    }

    /**
    * If set to true, the widget automatically adjusts the width of the popup element and does not wrap up the item label.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function autoWidth($value) {
        return $this->setProperty('autoWidth', $value);
    }

    /**
    * When this options is set to true and checkboxes are enabled, a tristate checkbox appears above the embedded treeview. Clicking that checkbox will check or uncheck all the loaded enabled items of the treeview.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function checkAll($value) {
        return $this->setProperty('checkAll', $value);
    }

    /**
    * Sets the checkAllTemplate option of the DropDownTree.
    * The template used to render the checkAll label. By default, the widget displays only a span element with text "Check all".
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function checkAllTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('checkAllTemplate', $value);
    }

    /**
    * Sets the checkAllTemplate option of the DropDownTree.
    * The template used to render the checkAll label. By default, the widget displays only a span element with text "Check all".
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function checkAllTemplate($value) {
        return $this->setProperty('checkAllTemplate', $value);
    }

    /**
    * If true or an object, renders checkboxes beside each node. In this case the widget value should be an array.
    * @param boolean|\Kendo\UI\DropDownTreeCheckboxes|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function checkboxes($value) {
        return $this->setProperty('checkboxes', $value);
    }

    /**
    * Unless this option is set to false, a button will appear when hovering the widget. Clicking that button will reset the widget's value and will trigger the change event.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function clearButton($value) {
        return $this->setProperty('clearButton', $value);
    }

    /**
    * Sets the field of the data item that provides the image URL of the DropDownTree nodes.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataImageUrlField($value) {
        return $this->setProperty('dataImageUrlField', $value);
    }

    /**
    * Sets the data source of the DropDownTree.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * Sets the field of the data item that provides the sprite CSS class of the nodes. If an array, each level uses the field that is at the same index in the array, or the last item in the array.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataSpriteCssClassField($value) {
        return $this->setProperty('dataSpriteCssClassField', $value);
    }

    /**
    * Sets the field of the data item that provides the text content of the nodes. If an array, each level uses the field that is at the same index in the array, or the last item in the array.
    * @param string|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataTextField($value) {
        return $this->setProperty('dataTextField', $value);
    }

    /**
    * Sets the field of the data item that provides the link URL of the nodes.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataUrlField($value) {
        return $this->setProperty('dataUrlField', $value);
    }

    /**
    * The field of the data item that provides the value of the widget. If an array, each level uses the field that is at the same index in the array, or the last item in the array.
    * @param string|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function dataValueField($value) {
        return $this->setProperty('dataValueField', $value);
    }

    /**
    * Specifies the delay in milliseconds after which the DropDownTree will start filtering dataSource.
    * @param float $value
    * @return \Kendo\UI\DropDownTree
    */
    public function delay($value) {
        return $this->setProperty('delay', $value);
    }

    /**
    * If set to false the widget will be disabled and will not allow user input. The widget is enabled by default and allows user input.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function enable($value) {
        return $this->setProperty('enable', $value);
    }

    /**
    * If set to true the widget will not show all items when the text of the search input cleared. By default, the widget shows all items when the text of the search input is cleared. Works in conjunction with minLength.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function enforceMinLength($value) {
        return $this->setProperty('enforceMinLength', $value);
    }

    /**
    * The filtering method used to determine the suggestions for the current value. Filtration is turned off by default, and can be performed over string values only (either the widget's data has to be an array of strings, or over the field, configured in the dataTextField option). The supported filter values are startswith, endswith and contains.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function filter($value) {
        return $this->setProperty('filter', $value);
    }

    /**
    * Sets the footerTemplate option of the DropDownTree.
    * The template used to render the footer template. The footer template receives the widget itself as a part of the data argument. Use the widget fields directly in the template.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function footerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * Sets the footerTemplate option of the DropDownTree.
    * The template used to render the footer template. The footer template receives the widget itself as a part of the data argument. Use the widget fields directly in the template.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function footerTemplate($value) {
        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * Sets max-height of the embedded treeview in pixels. The default value is 200 pixels. If set to "Auto" the height of the popup will depend on the height of the treeview.
    * @param string|float $value
    * @return \Kendo\UI\DropDownTree
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * If set to false case-sensitive search will be performed to find suggestions. The widget performs case-insensitive searching by default.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function ignoreCase($value) {
        return $this->setProperty('ignoreCase', $value);
    }

    /**
    * Indicates whether the child DataSources should be fetched lazily when parent groups get expanded. Setting this to true causes loading the child DataSources when expanding the parent node.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function loadOnDemand($value) {
        return $this->setProperty('loadOnDemand', $value);
    }

    /**
    * The text messages displayed in the widget. Use it to customize or localize the messages.
    * @param \Kendo\UI\DropDownTreeMessages|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * The minimum number of characters the user must type before a search is performed. Set to a higher value if the search could match a lot of items.
    * @param float $value
    * @return \Kendo\UI\DropDownTree
    */
    public function minLength($value) {
        return $this->setProperty('minLength', $value);
    }

    /**
    * Sets the noDataTemplate option of the DropDownTree.
    * The template used to render the "no data" template, which will be displayed if no results are found or the underlying data source is empty. The noData template receives the widget itself as a part of the data argument. The template will be evaluated on every widget data bound.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function noDataTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('noDataTemplate', $value);
    }

    /**
    * Sets the noDataTemplate option of the DropDownTree.
    * The template used to render the "no data" template, which will be displayed if no results are found or the underlying data source is empty. The noData template receives the widget itself as a part of the data argument. The template will be evaluated on every widget data bound.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function noDataTemplate($value) {
        return $this->setProperty('noDataTemplate', $value);
    }

    /**
    * The hint displayed by the widget when it is empty. Not set by default.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function placeholder($value) {
        return $this->setProperty('placeholder', $value);
    }

    /**
    * The options that will be used for the popup initialization. For more details about the available options refer to Popup documentation.
    * @param \Kendo\UI\DropDownTreePopup|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function popup($value) {
        return $this->setProperty('popup', $value);
    }

    /**
    * Sets the headerTemplate option of the DropDownTree.
    * Specifies a static HTML content, which will be rendered as a header of the popup element.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the DropDownTree.
    * Specifies a static HTML content, which will be rendered as a header of the popup element.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the valueTemplate option of the DropDownTree.
    * The template used to render the value and the or the selected tags.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function valueTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('valueTemplate', $value);
    }

    /**
    * Sets the valueTemplate option of the DropDownTree.
    * The template used to render the value and the or the selected tags.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function valueTemplate($value) {
        return $this->setProperty('valueTemplate', $value);
    }

    /**
    * The mode used to render the selected tags when checkboxes are enabled. The available modes are: - multiple - renders a tag for every selected value - single - renders only one tag that shows the number of the selected values
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function tagMode($value) {
        return $this->setProperty('tagMode', $value);
    }

    /**
    * Sets the template option of the DropDownTree.
    * Template for rendering each node.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownTree
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the DropDownTree.
    * Template for rendering each node.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownTree
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The text of the widget used when the autoBind is set to false.
    * @param string $value
    * @return \Kendo\UI\DropDownTree
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Define the value of the widget. It accepts 'String' when it is in single selection mode and 'Array' when multiple selection is enabled via checkboxes property.
    * @param string|array $value
    * @return \Kendo\UI\DropDownTree
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Specifies the value binding behavior for the widget. If set to true, the View-Model field will be updated with the selected item value field. If set to false, the View-Model field will be updated with the selected item.
    * @param boolean $value
    * @return \Kendo\UI\DropDownTree
    */
    public function valuePrimitive($value) {
        return $this->setProperty('valuePrimitive', $value);
    }

    /**
    * Sets the change event of the DropDownTree.
    * Fired when the value of the widget is changed by the user.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the close event of the DropDownTree.
    * Fired when the popup of the widget is closed.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the dataBound event of the DropDownTree.
    * Fired when the widget or sub levels of its items are bound to data from the dataSource.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the filtering event of the DropDownTree.
    * Fired when the widget is about to filter the data source.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function filtering($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('filtering', $value);
    }

    /**
    * Sets the open event of the DropDownTree.
    * Fired when the popup of the widget is opened by the user.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the select event of the DropDownTree.
    * Triggered when a node is being selected by the user. Cancellable. When checkboxes are enabled, it is also triggered when a node is being deselected.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownTree
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }


//<< Properties
}

?>
