<?php

namespace Kendo\UI;

class TreeListPageable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * (Available as of the Kendo UI 2017 R3 release) By default, the TreeList will render a pager even when total number of items in the DataSource is less than the pageSize value.If set to false the TreeList will toggle the pager visibility as follows: When the total amount of items initially set in the DataSource is less than the pageSize number, the pager will be hidden.; When the total amount of items initially set in the DataSource is greater than or equal to the pageSize number, the pager will be shown.; When the total amount of items in the DataSource becomes less than the pageSize number (after a delete or filter operation, or upon changing the pageSize), the pager will be hidden. or When the total amount of items in the DataSource becomes greater than or equal to the pageSize number (after an insert or filter operation, or upon changing the pageSize), the pager will be shown..
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function alwaysVisible($value) {
        return $this->setProperty('alwaysVisible', $value);
    }

    /**
    * The number of data items which will be displayed in the TreeList.
    * @param float $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function pageSize($value) {
        return $this->setProperty('pageSize', $value);
    }

    /**
    * If set to true, the pager will display buttons for going to the first, previous, next, and last pages. By default, these buttons are displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function previousNext($value) {
        return $this->setProperty('previousNext', $value);
    }

    /**
    * If set to true, the pager will display buttons for navigating to specific pages. By default, these buttons are displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function numeric($value) {
        return $this->setProperty('numeric', $value);
    }

    /**
    * The maximum number of buttons that are displayed in the numeric pager. If more pages than the specified number are rendered, the pager will display ellipsis (...).
    * @param float $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function buttonCount($value) {
        return $this->setProperty('buttonCount', $value);
    }

    /**
    * If set to true, the pager will display an input element which allows the user to type a specific page number. By default, the page input is not displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function input($value) {
        return $this->setProperty('input', $value);
    }

    /**
    * If set to true, the pager will display a drop-down which allows the user to pick a page size. By default, the drop-down for the page size is not displayed. Can be set to an array of predefined page sizes to override the default list. The TreeList supports a special all value which sets the page size to the total number of records. If you set a pageSize for the data source, then this value will be selected initially.
    * @param boolean|array $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function pageSizes($value) {
        return $this->setProperty('pageSizes', $value);
    }

    /**
    * If set to true, the pager will display the Refresh button. Clicking the Refresh button will refresh the TreeList. By default, the Refresh button is not displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function refresh($value) {
        return $this->setProperty('refresh', $value);
    }

    /**
    * If set to false, the pager will not be responsive. By default, the pager is responsive.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function responsive($value) {
        return $this->setProperty('responsive', $value);
    }

    /**
    * If set to true, the pager will display information about the current page and the total number of data items. By default, the paging information is displayed.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function info($value) {
        return $this->setProperty('info', $value);
    }

    /**
    * The text messages that are displayed in the pager. Use this option to customize or localize the pager messages.
    * @param \Kendo\UI\TreeListPageableMessages|array $value
    * @return \Kendo\UI\TreeListPageable
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

//<< Properties
}

?>
