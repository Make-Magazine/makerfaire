<?php

namespace Kendo\UI;

class ContextMenuScrollable extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the scroll amount (in pixels) that the ContextMenu scrolls when the scroll buttons are hovered. Each such distance is animated and then another animation starts with the same distance. If clicking a scroll button, the ContextMenu scrolls by doubling the distance.
    * @param float $value
    * @return \Kendo\UI\ContextMenuScrollable
    */
    public function distance($value) {
        return $this->setProperty('distance', $value);
    }

//<< Properties
}

?>
