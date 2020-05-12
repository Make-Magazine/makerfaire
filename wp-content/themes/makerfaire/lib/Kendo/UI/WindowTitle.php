<?php

namespace Kendo\UI;

class WindowTitle extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text in the title bar of the Window.
    * @param string $value
    * @return \Kendo\UI\WindowTitle
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Specifies whether the title text will be encoded.
    * @param boolean $value
    * @return \Kendo\UI\WindowTitle
    */
    public function encoded($value) {
        return $this->setProperty('encoded', $value);
    }

//<< Properties
}

?>
