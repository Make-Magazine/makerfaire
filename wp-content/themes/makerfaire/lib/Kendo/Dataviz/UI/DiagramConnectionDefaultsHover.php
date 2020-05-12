<?php

namespace Kendo\Dataviz\UI;

class DiagramConnectionDefaultsHover extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the default hover configuration of the connection lines.
    * @param \Kendo\Dataviz\UI\DiagramConnectionDefaultsHoverStroke|array $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsHover
    */
    public function stroke($value) {
        return $this->setProperty('stroke', $value);
    }

//<< Properties
}

?>
