<?php

namespace Kendo\Dataviz\UI;

class ArcGauge extends \Kendo\UI\Widget {
    public function name() {
        return 'ArcGauge';
    }
//>> Properties

    /**
    * Sets the centerTemplate option of the ArcGauge.
    * The label template. Template variables: *   value - the value
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function centerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('centerTemplate', $value);
    }

    /**
    * Sets the centerTemplate option of the ArcGauge.
    * The label template. Template variables: *   value - the value
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function centerTemplate($value) {
        return $this->setProperty('centerTemplate', $value);
    }

    /**
    * The color of the value pointer. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * Adds ArcGaugeColor to the ArcGauge.
    * @param \Kendo\Dataviz\UI\ArcGaugeColor|array,... $value one or more ArcGaugeColor to add.
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function addColor($value) {
        return $this->add('colors', func_get_args());
    }

    /**
    * The gauge area configuration options. This is the entire visible area of the gauge.
    * @param \Kendo\Dataviz\UI\ArcGaugeGaugeArea|array $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function gaugeArea($value) {
        return $this->setProperty('gaugeArea', $value);
    }

    /**
    * The opacity of the value pointer.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function opacity($value) {
        return $this->setProperty('opacity', $value);
    }

    /**
    * Configures the scale.
    * @param \Kendo\Dataviz\UI\ArcGaugeScale|array $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function scale($value) {
        return $this->setProperty('scale', $value);
    }

    /**
    * The gauge theme. This can be either a built-in theme or "sass". When set to "sass" the gauge will read the variables from the Sass-based themes.The supported values are: "sass" - special value, see notes; "black"; "blueopal"; "bootstrap"; "default"; "highcontrast"; "metro"; "metroblack"; "moonlight"; "silver" or "uniform".
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function theme($value) {
        return $this->setProperty('theme', $value);
    }

    /**
    * A value indicating if transition animations should be played.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function transitions($value) {
        return $this->setProperty('transitions', $value);
    }

    /**
    * The gauge value.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Specifies the preferred widget rendering mode.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ArcGauge
    */
    public function renderAs($value) {
        return $this->setProperty('renderAs', $value);
    }


//<< Properties
}

?>
