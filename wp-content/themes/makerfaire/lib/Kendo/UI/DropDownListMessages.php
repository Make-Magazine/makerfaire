<?php

namespace Kendo\UI;

class DropDownListMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text message shown in the noDataTemplate when no data is available in the widget drop-down.
    * @param string $value
    * @return \Kendo\UI\DropDownListMessages
    */
    public function noData($value) {
        return $this->setProperty('noData', $value);
    }

//<< Properties
}

?>
