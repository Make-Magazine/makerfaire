<?php

namespace Kendo\UI;

class TreeListPageableMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text with the pager information. Uses kendo.format.Contains the following placeholders: - {0} - The first data item index. - {1} - The last data item index. - {2} - The total number of data items.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function display($value) {
        return $this->setProperty('display', $value);
    }

    /**
    * The text that is displayed when the TreeList is empty.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function _empty($value) {
        return $this->setProperty('empty', $value);
    }

    /**
    * The label that is displayed before the pager input.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function page($value) {
        return $this->setProperty('page', $value);
    }

    /**
    * The label that is displayed before the pager input. Uses kendo.format. Contains one optional {0} placeholder which represents the total number of pages.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function of($value) {
        return $this->setProperty('of', $value);
    }

    /**
    * The label that is displayed after the drop-down list for the page size.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function itemsPerPage($value) {
        return $this->setProperty('itemsPerPage', $value);
    }

    /**
    * The tooltip of the button which goes to the first page.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function first($value) {
        return $this->setProperty('first', $value);
    }

    /**
    * The tooltip of the button which goes to the last page.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function last($value) {
        return $this->setProperty('last', $value);
    }

    /**
    * The tooltip of the button which goes to the next page.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function next($value) {
        return $this->setProperty('next', $value);
    }

    /**
    * The tooltip of the button which goes to the previous page.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function previous($value) {
        return $this->setProperty('previous', $value);
    }

    /**
    * The tooltip of the Refresh button.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function refresh($value) {
        return $this->setProperty('refresh', $value);
    }

    /**
    * The tooltip of the ... (ellipsis) button which appears when the number of pages is greater than the buttonCount.
    * @param string $value
    * @return \Kendo\UI\TreeListPageableMessages
    */
    public function morePages($value) {
        return $this->setProperty('morePages', $value);
    }

//<< Properties
}

?>
