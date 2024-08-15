<?php

namespace Kendo\UI;

class FileManagerBreadcrumb extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines a new root icon for the breadcrumb.
    * @param string $value
    * @return \Kendo\UI\FileManagerBreadcrumb
    */
    public function rootIcon($value) {
        return $this->setProperty('rootIcon', $value);
    }

    /**
    * Defines a new delimiter icon for the breadcrumb.
    * @param string $value
    * @return \Kendo\UI\FileManagerBreadcrumb
    */
    public function delimiterIcon($value) {
        return $this->setProperty('delimiterIcon', $value);
    }

//<< Properties
}

?>
