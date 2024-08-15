<?php

namespace Kendo\Dataviz\UI;

class ArcGaugeScaleLabelsBorder extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The color of the border. Any valid CSS color string will work here, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScaleLabelsBorder
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The opacity of the border. By default the border is opaque.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScaleLabelsBorder
    */
    public function opacity($value) {
        return $this->setProperty('opacity', $value);
    }

    /**
    * The width of the border.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScaleLabelsBorder
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Specifies the line dash type.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGaugeScaleLabelsBorder
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

//<< Properties
}

?>
