<?php

namespace Kendo\UI;

class GridGroupableSort extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the compare option of the GridGroupableSort.
    * A JavaScript function which is used to compare the groups (refer to sortable for sorting the items of the groups). It has the same signature as the compare function accepted by Array.sort.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\GridGroupableSort
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
    * @return \Kendo\UI\GridGroupableSort
    */
    public function dir($value) {
        return $this->setProperty('dir', $value);
    }

//<< Properties
}

?>
