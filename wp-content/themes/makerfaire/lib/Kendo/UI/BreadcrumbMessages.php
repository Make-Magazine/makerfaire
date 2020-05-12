<?php

namespace Kendo\UI;

class BreadcrumbMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The label for the root icon of the Breadcrumb.
    * @param string $value
    * @return \Kendo\UI\BreadcrumbMessages
    */
    public function rootTitle($value) {
        return $this->setProperty('rootTitle', $value);
    }

//<< Properties
}

?>
