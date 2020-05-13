<?php

namespace Kendo\UI;

class GridColumnGroupable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the sort configuration when grouping.
    * @param \Kendo\UI\GridColumnGroupableSort|array $value
    * @return \Kendo\UI\GridColumnGroupable
    */
    public function sort($value) {
        return $this->setProperty('sort', $value);
    }

//<< Properties
}

?>
