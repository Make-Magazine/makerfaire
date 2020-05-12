<?php

namespace Kendo\Dataviz\UI;

class ChartValueAxisItemCrosshair extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The color of the crosshair. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The dash type of the crosshair.The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of a repeating pattern of long-dash; "longDashDot" - a line consisting of a repeating pattern of long-dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long-dash-dot-dot or "solid" - a solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * The opacity of the crosshair. By default the crosshair is opaque.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function opacity($value) {
        return $this->setProperty('opacity', $value);
    }

    /**
    * The crosshair tooltip options.
    * @param \Kendo\Dataviz\UI\ChartValueAxisItemCrosshairTooltip|array $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function tooltip($value) {
        return $this->setProperty('tooltip', $value);
    }

    /**
    * If set to true the chart will display the value axis crosshair. By default the value axis crosshair is not visible.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * The width of the crosshair in pixels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartValueAxisItemCrosshair
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
