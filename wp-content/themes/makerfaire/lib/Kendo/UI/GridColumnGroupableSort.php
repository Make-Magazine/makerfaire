<?php

namespace Kendo\UI;

class GridColumnGroupableSort extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the compare option of the GridColumnGroupableSort.
    * A JavaScript function which is used to compare the groups (refer to sortable.compare for comparing the items of the groups). It has the same signature as the compare function accepted by Array.sort.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridColumnGroupableSort
    */
    public function compare($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('compare', $value);
    }

    /**
    * The sort order of the groups according to the group field.The supported values are: "asc" (ascending order) or "desc" (descending order).
    * @param string $value
    * @return \Kendo\UI\GridColumnGroupableSort
    */
    public function dir($value) {
        return $this->setProperty('dir', $value);
    }

//<< Properties
}

?>
