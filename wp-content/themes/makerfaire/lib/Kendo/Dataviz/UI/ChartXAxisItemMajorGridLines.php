<?php

namespace Kendo\Dataviz\UI;

class ChartXAxisItemMajorGridLines extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The color of the lines. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The dash type of the line.The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of a repeating pattern of long-dash; "longDashDot" - a line consisting of a repeating pattern of long-dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long-dash-dot-dot or "solid" - a solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * If set to false the chart will not display the x major grid lines. By default the x major grid lines are visible.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * The width of the line in pixels. Also affects the major and minor ticks, but not the grid lines. #### Example - set the scatter chart x major grid lines width
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The step of the x axis major grid lines.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function step($value) {
        return $this->setProperty('step', $value);
    }

    /**
    * The skip of the x axis major grid lines.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartXAxisItemMajorGridLines
    */
    public function skip($value) {
        return $this->setProperty('skip', $value);
    }

//<< Properties
}

?>
