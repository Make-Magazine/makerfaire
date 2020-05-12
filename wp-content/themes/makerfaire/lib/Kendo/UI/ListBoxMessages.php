<?php

namespace Kendo\UI;

class ListBoxMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the localization texts for tools in the ListBox. Texts are used when you configure the tooltip and accessibility support.
    * @param \Kendo\UI\ListBoxMessagesTools|array $value
    * @return \Kendo\UI\ListBoxMessages
    */
    public function tools($value) {
        return $this->setProperty('tools', $value);
    }

//<< Properties
}

?>
