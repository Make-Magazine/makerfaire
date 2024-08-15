<?php

namespace Kendo\UI;

class FileManagerViews extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the built-in grid view - accepts kendoGrid options
    * @param \Kendo\UI\FileManagerViewsGrid|array $value
    * @return \Kendo\UI\FileManagerViews
    */
    public function grid($value) {
        return $this->setProperty('grid', $value);
    }

    /**
    * Configures the built-in tree view - accepts kendoTreeView options
    * @param \Kendo\UI\FileManagerViewsTree|array $value
    * @return \Kendo\UI\FileManagerViews
    */
    public function tree($value) {
        return $this->setProperty('tree', $value);
    }

    /**
    * Configures the built-in list view (thumbnails) - accepts kendoListView options
    * @param \Kendo\UI\FileManagerViewsList|array $value
    * @return \Kendo\UI\FileManagerViews
    */
    public function _list($value) {
        return $this->setProperty('list', $value);
    }

//<< Properties
}

?>
