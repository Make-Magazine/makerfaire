<?php

namespace Kendo\UI;

class ListBoxDraggable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Indicates whether dragging is enabled.
    * @param boolean $value
    * @return \Kendo\UI\ListBoxDraggable
    */
    public function enabled($value) {
        return $this->setProperty('enabled', $value);
    }

    /**
    * Provides an option to customize the draggable item hint. If a function is supplied, it receives a single argument - the jQuery object of the draggable element. If a hint is not provided, the ListBox clones the dragged item and uses it as a hint.
    * @param \Kendo\JavaScriptFunction|string| $value
    * @return \Kendo\UI\ListBoxDraggable
    */
    public function hint($value) {
        return $this->setProperty('hint', $value);
    }

    /**
    * Provides an option to customize the item placeholder of the ListBox. If a function is supplied, it receives a single argument - the jQuery object of the draggable element. If a placeholder is not provided, the ListBox clones the dragged item, removes its id attribute, sets its visibility to hidden, and uses it as a placeholder.
    * @param \Kendo\JavaScriptFunction|string| $value
    * @return \Kendo\UI\ListBoxDraggable
    */
    public function placeholder($value) {
        return $this->setProperty('placeholder', $value);
    }

//<< Properties
}

?>
