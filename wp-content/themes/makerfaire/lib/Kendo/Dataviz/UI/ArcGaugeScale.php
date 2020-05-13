<?php

namespace Kendo\Dataviz\UI;

class ArcGaugeScale extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The end angle of the gauge. The gauge is rendered clockwise(0 degrees are the 180 degrees in the polar coordinate system)
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function endAngle($value) {
        return $this->setProperty('endAngle', $value);
    }

    /**
    * Configures the scale labels.
    * @param \Kendo\Dataviz\UI\ArcGaugeScaleLabels|array $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function labels($value) {
        return $this->setProperty('labels', $value);
    }

    /**
    * Configures the scale major ticks.
    * @param \Kendo\Dataviz\UI\ArcGaugeScaleMajorTicks|array $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function majorTicks($value) {
        return $this->setProperty('majorTicks', $value);
    }

    /**
    * The interval between major divisions.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function majorUnit($value) {
        return $this->setProperty('majorUnit', $value);
    }

    /**
    * The maximum value of the scale.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function max($value) {
        return $this->setProperty('max', $value);
    }

    /**
    * The minimum value of the scale.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function min($value) {
        return $this->setProperty('min', $value);
    }

    /**
    * Configures the scale minor ticks.
    * @param \Kendo\Dataviz\UI\ArcGaugeScaleMinorTicks|array $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function minorTicks($value) {
        return $this->setProperty('minorTicks', $value);
    }

    /**
    * The interval between minor divisions.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function minorUnit($value) {
        return $this->setProperty('minorUnit', $value);
    }

    /**
    * The lineCap style of the ranges.The supported values are: "butt"; "round" or "square".
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function rangeLineCap($value) {
        return $this->setProperty('rangeLineCap', $value);
    }

    /**
    * The default color for the ranges.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function rangePlaceholderColor($value) {
        return $this->setProperty('rangePlaceholderColor', $value);
    }

    /**
    * The width of the range indicators.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function rangeSize($value) {
        return $this->setProperty('rangeSize', $value);
    }

    /**
    * The distance from the range indicators to the ticks.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function rangeDistance($value) {
        return $this->setProperty('rangeDistance', $value);
    }

    /**
    * Reverses the scale direction - values are increase anticlockwise.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function reverse($value) {
        return $this->setProperty('reverse', $value);
    }

    /**
    * The start angle of the gauge. The gauge is rendered clockwise(0 degrees are the 180 degrees in the polar coordinate system)
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScale
    */
    public function startAngle($value) {
        return $this->setProperty('startAngle', $value);
    }

//<< Properties
}

?>
