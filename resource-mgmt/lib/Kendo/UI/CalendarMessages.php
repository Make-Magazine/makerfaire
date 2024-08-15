<?php

namespace Kendo\UI;

class CalendarMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Allows customization of the week column header text. Set the value to make the widget compliant with web accessibility standards.
    * @param string $value
    * @return \Kendo\UI\CalendarMessages
    */
    public function weekColumnHeader($value) {
        return $this->setProperty('weekColumnHeader', $value);
    }

//<< Properties
}

?>
