<?php

namespace Kendo\UI;

class TreeList extends \Kendo\UI\Widget {
    public function name() {
        return 'TreeList';
    }
//>> Properties

    /**
    * If set to false, the TreeList will not bind to the specified DataSource during initialization. In this case, data binding will occur when the change event of the DataSource fires. By default, the TreeList will bind to the DataSource that is specified in the configuration.
    * @param boolean $value
    * @return \Kendo\UI\TreeList
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Adds TreeListColumn to the TreeList.
    * @param \Kendo\UI\TreeListColumn|array,... $value one or more TreeListColumn to add.
    * @return \Kendo\UI\TreeList
    */
    public function addColumn($value) {
        return $this->add('columns', func_get_args());
    }

    /**
    * If set to true, the user can resize columns by dragging their header borders. By default, resizing is disabled.
    * @param boolean $value
    * @return \Kendo\UI\TreeList
    */
    public function resizable($value) {
        return $this->setProperty('resizable', $value);
    }

    /**
    * If set to true, the user can reorder the columns by dragging their header cells. By default, reordering is disabled.
    * @param boolean $value
    * @return \Kendo\UI\TreeList
    */
    public function reorderable($value) {
        return $this->setProperty('reorderable', $value);
    }

    /**
    * If set to true, the TreeList displays the column menu when the user clicks the Chevron icon in the column headers. The column menu allows the user to show and hide columns, and, if filtering and sorting are enabled, filter and sort the data. By default, the column menu is disabled. Can be set to a JavaScript object which represents the column menu configuration.
    * @param boolean|\Kendo\UI\TreeListColumnMenu|array $value
    * @return \Kendo\UI\TreeList
    */
    public function columnMenu($value) {
        return $this->setProperty('columnMenu', $value);
    }

    /**
    * Sets the data source of the TreeList.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\TreeList
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * If set to true, the user will be able to edit the data to which the TreeList is bound. By default, editing is disabled. can be set to a JavaScript object (which represents the editing configuration) or to a string (which specifies the edit mode).The supported string values are: (Default) inline; popup or incell.
    * @param boolean|\Kendo\UI\TreeListEditable|array $value
    * @return \Kendo\UI\TreeList
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * Configures the Excel export settings of the TreeList.
    * @param \Kendo\UI\TreeListExcel|array $value
    * @return \Kendo\UI\TreeList
    */
    public function excel($value) {
        return $this->setProperty('excel', $value);
    }

    /**
    * If set to true, the user can filter the data source by using the TreeList filter menu. By default, filtering is disabled. Can be set to a JavaScript object which represents the filter menu configuration.
    * @param boolean|\Kendo\UI\TreeListFilterable|array $value
    * @return \Kendo\UI\TreeList
    */
    public function filterable($value) {
        return $this->setProperty('filterable', $value);
    }

    /**
    * The height of the TreeList. Numeric values are treated as pixels.
    * @param float|string $value
    * @return \Kendo\UI\TreeList
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * Defines the text of the command buttons that are shown within the TreeList. Used primarily for localization.
    * @param \Kendo\UI\TreeListMessages|array $value
    * @return \Kendo\UI\TreeList
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * If set to true, the user can navigate the widget with the keyboard. By default, keyboard navigation is disabled. For a runnable example, refer to the demo on keyboard navigation in the TreeList.
    * @param boolean $value
    * @return \Kendo\UI\TreeList
    */
    public function navigatable($value) {
        return $this->setProperty('navigatable', $value);
    }

    /**
    * If set to true, the TreeList displays a pager. By default, paging is disabled. Only client-side paging is supported which means that all data items are expected to be available when the TreeList is initialized. Can be set to a JavaScript object which represents the pager configuration.
    * @param boolean|\Kendo\UI\TreeListPageable|array $value
    * @return \Kendo\UI\TreeList
    */
    public function pageable($value) {
        return $this->setProperty('pageable', $value);
    }

    /**
    * Configures the PDF export settings of the TreeList.
    * @param \Kendo\UI\TreeListPdf|array $value
    * @return \Kendo\UI\TreeList
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * If set to true, the TreeList will display a scrollbar when the total row height or width exceeds the TreeList height or width. By default, scrolling is enabled. Scrolling renders separate tables for the header and data area. For accessibility-conscious applications, disable scrolling.
    * @param boolean| $value
    * @return \Kendo\UI\TreeList
    */
    public function scrollable($value) {
        return $this->setProperty('scrollable', $value);
    }

    /**
    * Configures the Kendo UI TreeList search bar settings.
    * @param \Kendo\UI\TreeListSearch|array $value
    * @return \Kendo\UI\TreeList
    */
    public function search($value) {
        return $this->setProperty('search', $value);
    }

    /**
    * If set to true, the user will be able to select TreeList rows. By default, selection is disabled.Can also be set to the following string values: row - The user can select a single row.; cell - The user can select a single cell.; multiple, row - The user can select multiple rows. or multiple, cell - The user can select multiple cells..
    * @param boolean|string $value
    * @return \Kendo\UI\TreeList
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * If set to true, the user is able to sort the TreeList by clicking the column header cells. By default, sorting is disabled. Can be set to a JavaScript object which represents the sorting configuration.
    * @param boolean|\Kendo\UI\TreeListSortable|array $value
    * @return \Kendo\UI\TreeList
    */
    public function sortable($value) {
        return $this->setProperty('sortable', $value);
    }

    /**
    * Adds TreeListToolbarItem to the TreeList.
    * @param \Kendo\UI\TreeListToolbarItem|array,... $value one or more TreeListToolbarItem to add.
    * @return \Kendo\UI\TreeList
    */
    public function addToolbarItem($value) {
        return $this->add('toolbar', func_get_args());
    }

    /**
    * Sets the beforeEdit event of the TreeList.
    * Fires when the user tries to edit or creates a data item before the editor is created. Can be used for preventing the editing depending on custom logic. The event handler function context (available through the this keyword) will be set to the widget instance. The event will be fired only when the TreeList is editable.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function beforeEdit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('beforeEdit', $value);
    }

    /**
    * Sets the cancel event of the TreeList.
    * Fires when the user clicks the Cancel button (in inline or popup edit mode) or closes the popup window. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function cancel($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the cellClose event of the TreeList.
    * Fires when the incell edit mode is used and the cell will be closed. The event is triggered after saving or canceling the changes but before the cell is closed. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function cellClose($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cellClose', $value);
    }

    /**
    * Sets the change event of the TreeList.
    * Fires when the user selects a table row or cell in the TreeList. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the collapse event of the TreeList.
    * Fires when an item is about to be collapsed. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function collapse($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('collapse', $value);
    }

    /**
    * Sets the dataBinding event of the TreeList.
    * Fires before the widget binds to its data source. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function dataBinding($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBinding', $value);
    }

    /**
    * Sets the dataBound event of the TreeList.
    * Fires when the widget is bound to data from its data source. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the dragstart event of the TreeList.
    * (Available as of the 2015.3.1014 release) Fires when the user attempts to drag an item. If prevented, the item is not allowed to move. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function dragstart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragstart', $value);
    }

    /**
    * Sets the drag event of the TreeList.
    * (Available as of the 2015.3.1014 release) Fires while the user is dragging and item. This event is triggered on every mouse move. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function drag($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drag', $value);
    }

    /**
    * Sets the dragend event of the TreeList.
    * (Available as of the 2015.3.1014 release) Fires when the user finishes dragging an item and the model was updated. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function dragend($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragend', $value);
    }

    /**
    * Sets the drop event of the TreeList.
    * (Available as of the 2015.3.1014 release) Fires when the user drops an item. If prevented, the source row will not be moved. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function drop($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drop', $value);
    }

    /**
    * Sets the edit event of the TreeList.
    * Fires when the user edits or creates a data item. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function edit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('edit', $value);
    }

    /**
    * Sets the excelExport event of the TreeList.
    * Fires when the user clicks the Export to Excel toolbar button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function excelExport($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('excelExport', $value);
    }

    /**
    * Sets the expand event of the TreeList.
    * Fires when an item is about to be expanded. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function expand($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('expand', $value);
    }

    /**
    * Sets the filterMenuInit event of the TreeList.
    * Fires when the TreeList filter menu is initialized. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function filterMenuInit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('filterMenuInit', $value);
    }

    /**
    * Sets the filterMenuOpen event of the TreeList.
    * Fires when the TreeList filter menu is opened. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function filterMenuOpen($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('filterMenuOpen', $value);
    }

    /**
    * Sets the pdfExport event of the TreeList.
    * Fires  when the user clicks the Export to PDF toolbar button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function pdfExport($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pdfExport', $value);
    }

    /**
    * Sets the remove event of the TreeList.
    * Fires when the user clicks the Destroy command button. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the save event of the TreeList.
    * Fires when a data item is saved. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function save($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('save', $value);
    }

    /**
    * Sets the saveChanges event of the TreeList.
    * Fires when the user clicks the Save command button. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function saveChanges($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('saveChanges', $value);
    }

    /**
    * Sets the columnShow event of the TreeList.
    * Fires when the user shows a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnShow($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnShow', $value);
    }

    /**
    * Sets the columnHide event of the TreeList.
    * Fires when the user hides a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnHide($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnHide', $value);
    }

    /**
    * Sets the columnReorder event of the TreeList.
    * Fires when the user changes the order of a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnReorder($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnReorder', $value);
    }

    /**
    * Sets the columnResize event of the TreeList.
    * Fires when the user resizes a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnResize($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnResize', $value);
    }

    /**
    * Sets the columnMenuInit event of the TreeList.
    * Fires when the column menu is initialized. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnMenuInit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnMenuInit', $value);
    }

    /**
    * Sets the columnMenuOpen event of the TreeList.
    * Fires when the column menu is opened. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnMenuOpen($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnMenuOpen', $value);
    }

    /**
    * Sets the columnLock event of the TreeList.
    * Fires when the user lock a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnLock($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnLock', $value);
    }

    /**
    * Sets the columnUnlock event of the TreeList.
    * Fires when the user unlock a column. The event handler function context (available through the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeList
    */
    public function columnUnlock($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('columnUnlock', $value);
    }


//<< Properties
}

?>
