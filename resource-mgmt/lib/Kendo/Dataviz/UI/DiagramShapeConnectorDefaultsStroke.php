<?php

namespace Kendo\Dataviz\UI;

class DiagramShapeConnectorDefaultsStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the stroke color.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeConnectorDefaultsStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The dash type of the stroke.The following dash types are supported: "dash" - A line that consists of dashes; "dashDot" - A line that consists of a repeating pattern of dash-dot; "dot" - A line that consists of dots; "longDash" - A line that consists of a repeating pattern of long-dash; "longDashDot" - A line that consists of a repeating pattern of long-dash-dot; "longDashDotDot" - A line that consists of a repeating pattern of long-dash-dot-dot or "solid" - A solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeConnectorDefaultsStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * Defines the thickness or width of the shape connectors stroke.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeConnectorDefaultsStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
