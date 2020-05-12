<?php

namespace Kendo\UI;

class DateRangePickerMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Allows customization of the start label text.
    * @param string $value
    * @return \Kendo\UI\DateRangePickerMessages
    */
    public function startLabel($value) {
        return $this->setProperty('startLabel', $value);
    }

    /**
    * Allows customization of the end label text.
    * @param string $value
    * @return \Kendo\UI\DateRangePickerMessages
    */
    public function endLabel($value) {
        return $this->setProperty('endLabel', $value);
    }

//<< Properties
}

?>
