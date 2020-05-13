<?php

namespace Kendo\UI;

class ChatToolbarButton extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the name of the button.
    * @param string $value
    * @return \Kendo\UI\ChatToolbarButton
    */
    public function name($value) {
        return $this->setProperty('name', $value);
    }

    /**
    * Defines the text that will be rendered in the button.
    * @param string $value
    * @return \Kendo\UI\ChatToolbarButton
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * Defines the icon classes of the span that is rendered in the button.
    * @param string $value
    * @return \Kendo\UI\ChatToolbarButton
    */
    public function iconClass($value) {
        return $this->setProperty('iconClass', $value);
    }

    /**
    * Defines an object that is applied to the button element as attributes.
    * @param  $value
    * @return \Kendo\UI\ChatToolbarButton
    */
    public function attr($value) {
        return $this->setProperty('attr', $value);
    }

//<< Properties
}

?>
