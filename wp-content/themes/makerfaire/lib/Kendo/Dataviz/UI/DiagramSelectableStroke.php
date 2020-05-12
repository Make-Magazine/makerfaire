<?php

namespace Kendo\Dataviz\UI;

class DiagramSelectableStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the selection stroke color. Accepts valid CSS colors.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramSelectableStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * Defines the selection dash type. The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of long dashes; "longDashDot" - a line consisting of a repeating pattern of long dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long dash-dot-dot or "solid" - a solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramSelectableStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * Defines the selection stroke width.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramSelectableStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
