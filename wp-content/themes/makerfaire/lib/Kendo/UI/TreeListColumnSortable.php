<?php

namespace Kendo\UI;

class TreeListColumnSortable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the compare option of the TreeListColumnSortable.
    * A JavaScript function for comparing the values. If the first argument is less than the second one, returns -1.; If both arguments are the same, returns 0. or If the first argument is greater than the second one, returns +1..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\TreeListColumnSortable
    */
    public function compare($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('compare', $value);
    }

//<< Properties
}

?>
