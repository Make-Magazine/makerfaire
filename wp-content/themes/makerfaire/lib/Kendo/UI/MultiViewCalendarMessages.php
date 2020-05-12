<?php

namespace Kendo\UI;

class MultiViewCalendarMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Allows customization of the week column header text. Set the value to make the widget compliant with web accessibility standards.
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendarMessages
    */
    public function weekColumnHeader($value) {
        return $this->setProperty('weekColumnHeader', $value);
    }

//<< Properties
}

?>
