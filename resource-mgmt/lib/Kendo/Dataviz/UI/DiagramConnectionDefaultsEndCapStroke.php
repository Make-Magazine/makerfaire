<?php

namespace Kendo\Dataviz\UI;

class DiagramConnectionDefaultsEndCapStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The connections end cap stroke color.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCapStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The connections end cap stroke dash type.The following dash types are supported: "dash" - A line that consists of dashes; "dashDot" - A line that consists of a repeating pattern of dash-dot; "dot" - A line that consists of dots; "longDash" - A line that consists of a repeating pattern of long-dash; "longDashDot" - A line that consists of a repeating pattern of long-dash-dot; "longDashDotDot" - A line that consists of a repeating pattern of long-dash-dot-dot or "solid" - A solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCapStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * The connections end cap stroke width.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsEndCapStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
