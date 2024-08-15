<?php

namespace Kendo\Dataviz\UI;

class DiagramEditableResizeHandlesHoverStroke extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Specifies the stroke color on hovering over the resizing handles. See the editable.resize configuration for an example.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramEditableResizeHandlesHoverStroke
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * Specifies the stroke dash type on hovering over the resizing handles. See the editable.resize configuration for an example.The following dash types are supported: "dash" - A line that consists of dashes; "dashDot" - A line that consists of a repeating pattern of dash-dot; "dot" - A line that consists of dots; "longDash" - A line that consists of a repeating pattern of long-dash; "longDashDot" - A line that consists of a repeating pattern of long-dash-dot; "longDashDotDot" - A line that consists of a repeating pattern of long-dash-dot-dot or "solid" - A solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramEditableResizeHandlesHoverStroke
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * Specifies the stroke color on hovering over the resizing handles. See the editable.resize configuration for an example.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramEditableResizeHandlesHoverStroke
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
