<?php

namespace Kendo\Dataviz\UI;

class DiagramEditableDrag extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the shapes drag snap options. By default, during dragging, the shapes move by a given number of pixels at once. You can disable this behavior to make shapes movement smooth or you can specify a different number for the drag snap size to simulate a snap-to-grid functionality.
    * @param boolean|\Kendo\Dataviz\UI\DiagramEditableDragSnap|array $value
    * @return \Kendo\Dataviz\UI\DiagramEditableDrag
    */
    public function snap($value) {
        return $this->setProperty('snap', $value);
    }

//<< Properties
}

?>
