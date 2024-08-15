<?php

namespace Kendo\UI;

class MenuOpenOnClick extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies that the root menus will be opened only on item click.
    * @param boolean $value
    * @return \Kendo\UI\MenuOpenOnClick
    */
    public function rootMenuItems($value) {
        return $this->setProperty('rootMenuItems', $value);
    }

    /**
    * Specifies that the sub menus will be opened only on item click.
    * @param boolean $value
    * @return \Kendo\UI\MenuOpenOnClick
    */
    public function subMenuItems($value) {
        return $this->setProperty('subMenuItems', $value);
    }

//<< Properties
}

?>
