<?php

namespace Kendo\Dataviz\UI;

class DiagramConnectionDefaultsStartCapStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The connection start cap stroke color.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsStartCapStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The connection start cap stroke dash type.The following dash types are supported: "dash" - A line that consists of dashes; "dashDot" - A line that consists of a repeating pattern of dash-dot; "dot" - A line that consists of dots; "longDash" - A line that consists of a repeating pattern of long-dash; "longDashDot" - A line that consists of a repeating pattern of long-dash-dot; "longDashDotDot" - A line that consists of a repeating pattern of long-dash-dot-dot or "solid" - A solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsStartCapStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * The connection start cap stroke width.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramConnectionDefaultsStartCapStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
