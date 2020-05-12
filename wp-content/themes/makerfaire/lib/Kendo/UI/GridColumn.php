<?php

namespace Kendo\UI;

class GridColumn extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The aggregate(s) which are calculated when the grid is grouped by the columns field. The supported aggregates are "average", "count", "max", "min" and "sum".
    * @param array $value
    * @return \Kendo\UI\GridColumn
    */
    public function aggregates($value) {
        return $this->setProperty('aggregates', $value);
    }

    /**
    * HTML attributes of the table cell (<td>) rendered for the column.
    * @param  $value
    * @return \Kendo\UI\GridColumn
    */
    public function attributes($value) {
        return $this->setProperty('attributes', $value);
    }

    /**
    * Adds GridColumnCommandItem to the GridColumn.
    * @param \Kendo\UI\GridColumnCommandItem|array,... $value one or more GridColumnCommandItem to add.
    * @return \Kendo\UI\GridColumn
    */
    public function addCommandItem($value) {
        return $this->add('command', func_get_args());
    }

    /**
    * Sets the editable option of the GridColumn.
    * The JavaScript function executed when the cell/row is about to be opened for edit. The result returned will determine whether an editor for the column will be created.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridColumn
    */
    public function editable($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('editable', $value);
    }

    /**
    * Sets the editor option of the GridColumn.
    * Provides a way to specify a custom editing UI for the column. Use the container parameter to create the editing UI.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridColumn
    */
    public function editor($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('editor', $value);
    }

    /**
    * If set to true the column value will be HTML-encoded before it is displayed. If set to false the column value will be displayed as is. By default the column value is HTML-encoded.
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function encoded($value) {
        return $this->setProperty('encoded', $value);
    }

    /**
    * The field to which the column is bound. The value of this field is displayed in the column's cells during data binding. Only columns that are bound to a field can be sortable or filterable.The field name should be a valid Javascript identifier and should contain only alphanumeric characters (or "$" or "_"), and may not start with a digit.
    * @param string $value
    * @return \Kendo\UI\GridColumn
    */
    public function field($value) {
        return $this->setProperty('field', $value);
    }

    /**
    * If set to true a filter menu will be displayed for this column when filtering is enabled. If set to false the filter menu will not be displayed. By default a filter menu is displayed for all columns when filtering is enabled via the filterable option.Can be set to a JavaScript object which represents the filter menu configuration.
    * @param boolean|\Kendo\UI\GridColumnFilterable|array $value
    * @return \Kendo\UI\GridColumn
    */
    public function filterable($value) {
        return $this->setProperty('filterable', $value);
    }

    /**
    * HTML attributes of the column footer. The footerAttributes option can be used to set the HTML attributes of that cell.
    * @param  $value
    * @return \Kendo\UI\GridColumn
    */
    public function footerAttributes($value) {
        return $this->setProperty('footerAttributes', $value);
    }

    /**
    * Sets the footerTemplate option of the GridColumn.
    * The template which renders the footer table cell for the column.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified) or data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function footerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * Sets the footerTemplate option of the GridColumn.
    * The template which renders the footer table cell for the column.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified) or data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average.
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function footerTemplate($value) {
        return $this->setProperty('footerTemplate', $value);
    }

    /**
    * The format that is applied to the value before it is displayed. Takes the form "{0:format}" where "format" is a standard number format,custom number format, standard date format or a custom date format.
    * @param string $value
    * @return \Kendo\UI\GridColumn
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * If set to false the column will not be groupable (requires Grid groupable property to be enabled). By default all columns are groupable.
    * @param boolean|\Kendo\UI\GridColumnGroupable|array $value
    * @return \Kendo\UI\GridColumn
    */
    public function groupable($value) {
        return $this->setProperty('groupable', $value);
    }

    /**
    * Sets the groupHeaderColumnTemplate option of the GridColumn.
    * Introduced in the Kendo UI 2018 R3 release.The template which renders the content for specific column in the group header when the grid is grouped by the column field.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average or group - provides information for the current group. An object with three fields - field, value and items. items field contains the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function groupHeaderColumnTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('groupHeaderColumnTemplate', $value);
    }

    /**
    * Sets the groupHeaderColumnTemplate option of the GridColumn.
    * Introduced in the Kendo UI 2018 R3 release.The template which renders the content for specific column in the group header when the grid is grouped by the column field.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average or group - provides information for the current group. An object with three fields - field, value and items. items field contains the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function groupHeaderColumnTemplate($value) {
        return $this->setProperty('groupHeaderColumnTemplate', $value);
    }

    /**
    * Sets the groupHeaderTemplate option of the GridColumn.
    * The template which renders the group header when the grid is grouped by the column field. By default the name of the field and the current group value is displayed.The fields which can be used in the template are: value - the current group value; field - the current group field; average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); aggregates - provides access to all available aggregates, e.g. aggregates.fieldName1.sum or aggregates.fieldName2.average or items - the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function groupHeaderTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('groupHeaderTemplate', $value);
    }

    /**
    * Sets the groupHeaderTemplate option of the GridColumn.
    * The template which renders the group header when the grid is grouped by the column field. By default the name of the field and the current group value is displayed.The fields which can be used in the template are: value - the current group value; field - the current group field; average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); aggregates - provides access to all available aggregates, e.g. aggregates.fieldName1.sum or aggregates.fieldName2.average or items - the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function groupHeaderTemplate($value) {
        return $this->setProperty('groupHeaderTemplate', $value);
    }

    /**
    * Sets the groupFooterTemplate option of the GridColumn.
    * The template which renders the group footer when the grid is grouped by the column field. By default the group footer is not displayed.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average or group - provides information for the current group. An object with three fields - field, value and items. items field contains the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function groupFooterTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('groupFooterTemplate', $value);
    }

    /**
    * Sets the groupFooterTemplate option of the GridColumn.
    * The template which renders the group footer when the grid is grouped by the column field. By default the group footer is not displayed.The fields which can be used in the template are: average - the value of the "average" aggregate (if specified); count - the value of the "count" aggregate (if specified); max - the value of the "max" aggregate (if specified); min - the value of the "min" aggregate (if specified); sum - the value of the "sum" aggregate (if specified); data - provides access to all available aggregates, e.g. data.fieldName1.sum or data.fieldName2.average or group - provides information for the current group. An object with three fields - field, value and items. items field contains the data items for current group. Returns groups if the data items are grouped (in case there are child groups).
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function groupFooterTemplate($value) {
        return $this->setProperty('groupFooterTemplate', $value);
    }

    /**
    * HTML attributes of the column header. The grid renders a table header cell (<th>) for every column. The headerAttributes option can be used to set the HTML attributes of that th.
    * @param  $value
    * @return \Kendo\UI\GridColumn
    */
    public function headerAttributes($value) {
        return $this->setProperty('headerAttributes', $value);
    }

    /**
    * Sets the headerTemplate option of the GridColumn.
    * The template which renders the column header content. By default the value of the title column option is displayed in the column header cell.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the GridColumn.
    * The template which renders the column header content. By default the value of the title column option is displayed in the column header cell.
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * If set to true the column will not be displayed in the grid. By default all columns are displayed.
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function hidden($value) {
        return $this->setProperty('hidden', $value);
    }

    /**
    * If set to true the column will be displayed as locked (frozen) in the grid. Also see the information about Locked Columns in the Grid Appearance article.
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function locked($value) {
        return $this->setProperty('locked', $value);
    }

    /**
    * If set to false the column will remain in the side of the grid into which its own locked configuration placed it.
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function lockable($value) {
        return $this->setProperty('lockable', $value);
    }

    /**
    * Sets the condition that needs to be satisfied for a column to remain visible. The property accepts valid strings for the matchMedia browser API (assuming it is supported by the browser) and toggles the visibility of the columns based on the media queries.The hidden option takes precedence over media. This option cannot be used with minScreenWidth at the same time.Also accepts the device identifiers that are available in Bootstrap 4: xs is equivalent to "(max-width: 576px)"; sm is equivalent to "(min-width: 576px)"; md is equivalent to "(min-width: 768px)"; lg is equivalent to "(min-width: 992px)" or xl is equivalent to "(min-width: 1200px)".
    * @param string $value
    * @return \Kendo\UI\GridColumn
    */
    public function media($value) {
        return $this->setProperty('media', $value);
    }

    /**
    * The pixel screen width below which the user will not be able to resize the column via the UI.
    * @param float $value
    * @return \Kendo\UI\GridColumn
    */
    public function minResizableWidth($value) {
        return $this->setProperty('minResizableWidth', $value);
    }

    /**
    * The pixel screen width below which the column will be hidden. The setting takes precedence over the hidden setting, so the two should not be used at the same time.
    * @param float $value
    * @return \Kendo\UI\GridColumn
    */
    public function minScreenWidth($value) {
        return $this->setProperty('minScreenWidth', $value);
    }

    /**
    * If set to true the grid will render a select column with checkboxes in each cell, thus enabling multi-row selection. The header checkbox allows users to select/deselect all the rows on the current page.
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * If set to true the user can click the column header and sort the grid by the column field when sorting is enabled. If set to false sorting will be disabled for this column. By default all columns are sortable if sorting is enabled via the sortable option.
    * @param boolean|\Kendo\UI\GridColumnSortable|array $value
    * @return \Kendo\UI\GridColumn
    */
    public function sortable($value) {
        return $this->setProperty('sortable', $value);
    }

    /**
    * Sets the template option of the GridColumn.
    * The template which renders the column content. The grid renders table rows (<tr>) which represent the data source items. Each table row consists of table cells (<td>) which represent the grid columns. By default the HTML-encoded value of the field is displayed in the column.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\GridColumn
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the GridColumn.
    * The template which renders the column content. The grid renders table rows (<tr>) which represent the data source items. Each table row consists of table cells (<td>) which represent the grid columns. By default the HTML-encoded value of the field is displayed in the column.
    * @param string $value The template content.
    * @return \Kendo\UI\GridColumn
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The text that is displayed in the column header cell. If not set the field is used.
    * @param string $value
    * @return \Kendo\UI\GridColumn
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * The width of the column. Numeric values are treated as pixels. The width option supports the fundamental measuring units. For instance: px sets the width in pixels; cm sets the width in centimeters; mm sets the width in millimeters; % sets the width relative to the grid's element width; em sets the width relative to the font-size of the grid's element width or rem sets the width relative to font-size of the root element. For more important information, please refer to Column Widths.
    * @param string|float $value
    * @return \Kendo\UI\GridColumn
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * An array of values that will be displayed instead of the bound value. Each item in the array must have a text and value fields.
    * @param array $value
    * @return \Kendo\UI\GridColumn
    */
    public function values($value) {
        return $this->setProperty('values', $value);
    }

    /**
    * If set to true the column will be visible in the grid column menu. By default the column menu includes all data-bound columns (ones that have their field set).
    * @param boolean $value
    * @return \Kendo\UI\GridColumn
    */
    public function menu($value) {
        return $this->setProperty('menu', $value);
    }

//<< Properties

    /**
    * The columns which should be rendered as child columns under this group column header.**Note that group column cannot be data bound and supports limited number of bound column settings - such as title, headerTemplate, locked
    * @param \Kendo\UI\GridColumn|array,... $value one or more GridColumn to add.
    */
    public function addColumns($value) {
        return $this->add('columns', func_get_args());
    }
}

?>
