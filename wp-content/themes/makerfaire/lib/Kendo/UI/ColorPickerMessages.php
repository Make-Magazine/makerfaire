<?php

namespace Kendo\UI;

class ColorPickerMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Allows customization of the "Apply" button text.
    * @param string $value
    * @return \Kendo\UI\ColorPickerMessages
    */
    public function apply($value) {
        return $this->setProperty('apply', $value);
    }

    /**
    * Allows customization of the "Cancel" button text.
    * @param string $value
    * @return \Kendo\UI\ColorPickerMessages
    */
    public function cancel($value) {
        return $this->setProperty('cancel', $value);
    }

    /**
    * Allows customization of the "Color Hexadecimal Code" preview input title.
    * @param string $value
    * @return \Kendo\UI\ColorPickerMessages
    */
    public function previewInput($value) {
        return $this->setProperty('previewInput', $value);
    }

//<< Properties
}

?>
