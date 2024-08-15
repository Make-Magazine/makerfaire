<?php

namespace Kendo\UI;

class DialogMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The title of the close button.
    * @param string $value
    * @return \Kendo\UI\DialogMessages
    */
    public function close($value) {
        return $this->setProperty('close', $value);
    }

    /**
    * The title of the prompt input.
    * @param string $value
    * @return \Kendo\UI\DialogMessages
    */
    public function promptInput($value) {
        return $this->setProperty('promptInput', $value);
    }

//<< Properties
}

?>
