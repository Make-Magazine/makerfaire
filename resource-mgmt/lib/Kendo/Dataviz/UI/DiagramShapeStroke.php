<?php

namespace Kendo\Dataviz\UI;

class DiagramShapeStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the color of the shape stroke.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The dash type of the shape.The following dash types are supported: "dash" - A line that consists of dashes; "dashDot" - A line that consists of a repeating pattern of dash-dot; "dot" - A line that consists of dots; "longDash" - A line that consists of a repeating pattern of long-dash; "longDashDot" - A line that consists of a repeating pattern of long-dash-dot; "longDashDotDot" - A line that consists of a repeating pattern of long-dash-dot-dot or "solid" - A solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * Defines the thickness or width of the shape stroke.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
