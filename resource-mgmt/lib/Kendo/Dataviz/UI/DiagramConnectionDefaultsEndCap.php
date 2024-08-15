<?php

namespace Kendo\Dataviz\UI;

class DiagramConnectionDefaultsEndCap extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The connections end cap fill options or color.
    * @param string|\Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCapFill|array $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCap
    */
    public function fill($value) {
        return $this->setProperty('fill', $value);
    }

    /**
    * The connections end cap stroke options or color.
    * @param string|\Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCapStroke|array $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCap
    */
    public function stroke($value) {
        return $this->setProperty('stroke', $value);
    }

    /**
    * The end cap type used in connections.The supported values are: "none": no cap; "ArrowEnd": a filled arrow or "FilledCircle": a filled circle.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCap
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

//<< Properties
}

?>
