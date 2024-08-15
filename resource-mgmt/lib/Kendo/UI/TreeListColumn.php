<?php

namespace Kendo\UI;

class TreeListColumn extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The HTML attributes of the table cell (<td>) that is rendered for the column.
    * @param  $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function attributes($value) {
        return $this->setProperty('attributes', $value);
    }

    /**
    * Adds TreeListColumnCommandItem to the TreeListColumn.
    * @param \Kendo\UI\TreeListColumnCommandItem|array,... $value one or more TreeListColumnCommandItem to add.
    * @return \Kendo\UI\TreeListColumn
    */
    public function addCommandItem($value) {
        return $this->add('command', func_get_args());
    }

    /**
    * Sets the editable option of the TreeListColumn.
    * The JavaScript function that is executed when the cell or row is about to be opened for editing. The returned result will determine whether an editor for the column will be created.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeListColumn
    */
    public function editable($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('editable', $value);
    }

    /**
    * Sets the editor option of the TreeListColumn.
    * Provides a way to specify a custom editing UI for the column. To create the editing UI, use the container parameter.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeListColumn
    */
    public function editor($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('editor', $value);
    }

    /**
    * If set to true, the column value will be HTML-encoded before it is displayed. If set to false, the column value will be displayed as is. By default, the column value is HTML-encoded.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function encoded($value) {
        return $this->setProperty('encoded', $value);
    }

    /**
    * If set to true, the column will show the icons that are used for expanding and collapsing child rows. By default, the first column of the TreeList is expandable.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function expandable($value) {
        return $this->setProperty('expandable', $value);
    }

    /**
    * The field to which the column is bound. The value of this field is displayed by the column during data binding.
    * @param string $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function field($value) {
        return $this->setProperty('field', $value);
    }

    /**
    * If set to true and if filtering is enabled, a filter menu will be displayed for this column. If set to false, the filter menu will not be displayed. By default, a filter menu is displayed for all columns when filtering is enabled through the filterable option. Can be set to a JavaScript object which represents the filter menu configuration.
    * @param boolean|\Kendo\UI\TreeListColumnFilterable|array $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function filterable($value) {
        return $this->setProperty('filterable', $value);
    }

    /**
    * Sets the footerTemplate option of the TreeListColumn.
    * The template which renders the footer table cell for the column.The following fields can be used in the template: average - The value of the average aggregate (if specified).; count - The value of the count aggregate (if specified).; max - The value of the max aggregate (if specified).; min - The value of the min aggregate (if specified). or sum - The value of the sum aggregate (if specified)..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\TreeListColumn
    */
    public function footerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * Sets the footerTemplate option of the TreeListColumn.
    * The template which renders the footer table cell for the column.The following fields can be used in the template: average - The value of the average aggregate (if specified).; count - The value of the count aggregate (if specified).; max - The value of the max aggregate (if specified).; min - The value of the min aggregate (if specified). or sum - The value of the sum aggregate (if specified)..
    * @param string $value The template content.
    * @return \Kendo\UI\TreeListColumn
    */
    public function footerTemplate($value) {
        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * The format that is applied to the value before it is displayed. Takes the {0:format} form where format is a standard number format, custom number format, standard date format or a custom date format.
    * @param string $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The HTML attributes of the table header cell (<th>) that is rendered for the column.
    * @param  $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function headerAttributes($value) {
        return $this->setProperty('headerAttributes', $value);
    }

    /**
    * Sets the headerTemplate option of the TreeListColumn.
    * The template which renders the column header content. By default, the value of the title column option is displayed in the column header cell.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\TreeListColumn
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the TreeListColumn.
    * The template which renders the column header content. By default, the value of the title column option is displayed in the column header cell.
    * @param string $value The template content.
    * @return \Kendo\UI\TreeListColumn
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * The pixel screen width below which the column will be hidden. The setting takes precedence over the hidden setting and the two cannot not be used at the same time.
    * @param float $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function minScreenWidth($value) {
        return $this->setProperty('minScreenWidth', $value);
    }

    /**
    * If set to true and sorting is enabled, the user can click the column header and sort the TreeList by the column field. If set to false, sorting will be disabled for this column. By default, all columns are sortable if sorting is enabled though the sortable option.
    * @param boolean|\Kendo\UI\TreeListColumnSortable|array $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function sortable($value) {
        return $this->setProperty('sortable', $value);
    }

    /**
    * Sets the template option of the TreeListColumn.
    * The template which renders the column content. The TreeList renders table rows (<tr>) which represent the data source items. Each table row consists of table cells (<td>) which represent the TreeList columns. By default, the HTML-encoded value of the field is displayed in the column.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\TreeListColumn
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the TreeListColumn.
    * The template which renders the column content. The TreeList renders table rows (<tr>) which represent the data source items. Each table row consists of table cells (<td>) which represent the TreeList columns. By default, the HTML-encoded value of the field is displayed in the column.
    * @param string $value The template content.
    * @return \Kendo\UI\TreeListColumn
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The text that is displayed in the column header cell. If not set, the TreeList uses field.
    * @param string $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * The width of the column. Numeric values are treated as pixels.
    * @param string|float $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * If set to true, the TreeList will not display the column. By default, all columns are displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function hidden($value) {
        return $this->setProperty('hidden', $value);
    }

    /**
    * If set to true, the TreeList will display the column in the column menu. By default, the column menu includes all data-bound columns, that is, the ones with a set field option.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function menu($value) {
        return $this->setProperty('menu', $value);
    }

    /**
    * If set to true, the TreeList will display the column as locked (frozen).
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function locked($value) {
        return $this->setProperty('locked', $value);
    }

    /**
    * If set to false, the column will remain in that side of the TreeList where its own locked configuration placed it.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumn
    */
    public function lockable($value) {
        return $this->setProperty('lockable', $value);
    }

//<< Properties

    /**
    * The columns which should be rendered as child columns under this group column header.**Note that group column cannot be data bound and supports limited number of bound column settings - such as title, headerTemplate, locked
    * @param \Kendo\UI\TreeListColumn|array,... $value one or more TreeListColumn to add.
    */
    public function addColumns($value) {
        return $this->add('columns', func_get_args());
    }
}

?>
