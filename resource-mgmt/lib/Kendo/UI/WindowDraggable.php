<?php

namespace Kendo\UI;

class WindowDraggable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the element in which the window will be able to move. The window is appended to this element and in this way overrides the appendTo option. Accepts either a selector or an element.
    * @param string| $value
    * @return \Kendo\UI\WindowDraggable
    */
    public function containment($value) {
        return $this->setProperty('containment', $value);
    }

    /**
    * Constrains dragging to the horizontal (x) or to the vertical (y) axis.The supported values are: x or y.
    * @param string $value
    * @return \Kendo\UI\WindowDraggable
    */
    public function axis($value) {
        return $this->setProperty('axis', $value);
    }

    /**
    * Restricts the dragging of the window through the specified element which will be part of the window content. Accepts either a selector or an element.
    * @param string $value
    * @return \Kendo\UI\WindowDraggable
    */
    public function dragHandle($value) {
        return $this->setProperty('dragHandle', $value);
    }

//<< Properties
}

?>
