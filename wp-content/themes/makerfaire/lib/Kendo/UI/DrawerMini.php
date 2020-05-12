<?php

namespace Kendo\UI;

class DrawerMini extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines a specific width for the Kendo UI Drawer when in mini mode.
    * @param float $value
    * @return \Kendo\UI\DrawerMini
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Sets the template option of the DrawerMini.
    * Defines a specific template for the Kendo UI Drawer when in mini mode.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DrawerMini
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the DrawerMini.
    * Defines a specific template for the Kendo UI Drawer when in mini mode.
    * @param string $value The template content.
    * @return \Kendo\UI\DrawerMini
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

//<< Properties
}

?>
