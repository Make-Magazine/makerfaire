<?php

namespace Kendo\UI;

class TreeListColumnMenuMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text message that is displayed for the column selection menu item.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function columns($value) {
        return $this->setProperty('columns', $value);
    }

    /**
    * The text message that is displayed for the filter menu item.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function filter($value) {
        return $this->setProperty('filter', $value);
    }

    /**
    * The text message that is displayed for the menu item which performs the ascending sort mode.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function sortAscending($value) {
        return $this->setProperty('sortAscending', $value);
    }

    /**
    * The text message that is displayed for the menu item which performs the descending sort mode.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function sortDescending($value) {
        return $this->setProperty('sortDescending', $value);
    }

    /**
    * The text message that is displayed in the menu header.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function settings($value) {
        return $this->setProperty('settings', $value);
    }

    /**
    * The text message that is displayed in the column menu when locking a column.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function lock($value) {
        return $this->setProperty('lock', $value);
    }

    /**
    * The text message that is displayed in the column menu for unlocking a column.
    * @param string $value
    * @return \Kendo\UI\TreeListColumnMenuMessages
    */
    public function unlock($value) {
        return $this->setProperty('unlock', $value);
    }

//<< Properties
}

?>
