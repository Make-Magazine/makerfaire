<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesDefaultsNotesLine extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The dash type of the note line.The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of a repeating pattern of long-dash; "longDashDot" - a line consisting of a repeating pattern of long-dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long-dash-dot-dot or "solid" - a solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLine
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * The line width of the notes.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLine
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The line color of the notes.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLine
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The length of the connecting lines in pixels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLine
    */
    public function length($value) {
        return $this->setProperty('length', $value);
    }

//<< Properties
}

?>
