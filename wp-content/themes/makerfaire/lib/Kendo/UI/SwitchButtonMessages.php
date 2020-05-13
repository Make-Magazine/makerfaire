<?php

namespace Kendo\UI;

class SwitchButtonMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The label for the checked state of the Switch.
    * @param string $value
    * @return \Kendo\UI\SwitchButtonMessages
    */
    public function checked($value) {
        return $this->setProperty('checked', $value);
    }

    /**
    * The label for the unchecked state of the Switch.
    * @param string $value
    * @return \Kendo\UI\SwitchButtonMessages
    */
    public function unchecked($value) {
        return $this->setProperty('unchecked', $value);
    }

//<< Properties
}

?>
