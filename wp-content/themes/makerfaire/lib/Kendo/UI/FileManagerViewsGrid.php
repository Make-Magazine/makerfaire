<?php

namespace Kendo\UI;

class FileManagerViewsGrid extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds FileManagerViewsGridColumn to the FileManagerViewsGrid.
    * @param \Kendo\UI\FileManagerViewsGridColumn|array,... $value one or more FileManagerViewsGridColumn to add.
    * @return \Kendo\UI\FileManagerViewsGrid
    */
    public function addColumn($value) {
        return $this->add('columns', func_get_args());
    }

//<< Properties
}

?>
