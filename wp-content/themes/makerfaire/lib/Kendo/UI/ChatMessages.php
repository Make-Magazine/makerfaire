<?php

namespace Kendo\UI;

class ChatMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The hint that is displayed in the input textbox of the widget.
    * @param string $value
    * @return \Kendo\UI\ChatMessages
    */
    public function placeholder($value) {
        return $this->setProperty('placeholder', $value);
    }

    /**
    * The title and the aria-label attribute of the message box send button.
    * @param string $value
    * @return \Kendo\UI\ChatMessages
    */
    public function sendButton($value) {
        return $this->setProperty('sendButton', $value);
    }

    /**
    * The title and the aria-label attribute of the toggle toolbar button.
    * @param string $value
    * @return \Kendo\UI\ChatMessages
    */
    public function toggleButton($value) {
        return $this->setProperty('toggleButton', $value);
    }

//<< Properties
}

?>
