<?php

namespace Kendo\UI;

class FileManagerContextMenuItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the name of the item.
    * @param string $value
    * @return \Kendo\UI\FileManagerContextMenuItem
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Specifies the text of the item.
    * @param string $value
    * @return \Kendo\UI\FileManagerContextMenuItem
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Specifies the spriteCssClass of the item.
    * @param string $value
    * @return \Kendo\UI\FileManagerContextMenuItem
    */
    public function spriteCssClass($value) {
        return $this->setProperty('spriteCssClass', $value);
    }

    /**
    * Specifies the command of the item.
    * @param string $value
    * @return \Kendo\UI\FileManagerContextMenuItem
    */
    public function command($value) {
        return $this->setProperty('command', $value);
    }

//<< Properties
}

?>
