<?php

namespace Kendo\UI;

class TreeListColumnMenu extends \Kendo\SerializableObject {
//>> Properties

    /**
    * If set to true, the column menu allows the user to select (show and hide) TreeList columns. By default, the column menu allows column selection.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumnMenu
    */
    public function columns($value) {
        return $this->setProperty('columns', $value);
    }

    /**
    * If set to true, the column menu will allow the user to filter the TreeList. By default, if filtering is enabled through filterable, the column menu allows the user to filter.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumnMenu
    */
    public function filterable($value) {
        return $this->setProperty('filterable', $value);
    }

    /**
    * If set to true, the column menu will allow the user to sort the TreeList by the column field. By default, if sorting is enabled through sortable, the column menu allows the user to sort the data.
    * @param boolean $value
    * @return \Kendo\UI\TreeListColumnMenu
    */
    public function sortable($value) {
        return $this->setProperty('sortable', $value);
    }

    /**
    * The text messages that is played in the column menu. Use it to customize or localize the column menu messages.
    * @param \Kendo\UI\TreeListColumnMenuMessages|array $value
    * @return \Kendo\UI\TreeListColumnMenu
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

//<< Properties
}

?>
