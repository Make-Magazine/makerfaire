<?php

namespace Kendo\UI;

class GridScrollable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the grid virtualization settings. If set to true the grid will enable row virtualization and display a single page of data. Scrolling would just change the data which is currently displayed.Can also be set to the following string values: "rows" - enables virtualization of rows.; "columns" - enables virtualization of columns. or "rows, columns" - enables virtualization of both rows and columns..
    * @param boolean|string $value
    * @return \Kendo\UI\GridScrollable
    */
    public function virtual($value) {
        return $this->setProperty('virtual', $value);
    }

    /**
    * If set to true the grid will always display a single page of data. Scrolling to the end will load more items untill all items are displayed.
    * @param boolean $value
    * @return \Kendo\UI\GridScrollable
    */
    public function endless($value) {
        return $this->setProperty('endless', $value);
    }

//<< Properties
}

?>
