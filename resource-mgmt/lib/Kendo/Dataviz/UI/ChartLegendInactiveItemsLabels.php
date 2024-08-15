<?php

namespace Kendo\Dataviz\UI;

class ChartLegendInactiveItemsLabels extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text color of the labels. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartLegendInactiveItemsLabels
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The font style of the labels. Accepts a valid CSS color string, for example "20px 'Courier New'".
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartLegendInactiveItemsLabels
    */
    public function font($value) {
        return $this->setProperty('font', $value);
    }

    /**
    * Sets the template option of the ChartLegendInactiveItemsLabels.
    * The template which renders the labels.The fields which can be used in the template are: text - the text the legend item.; series - the data series.; value - the point value. (only for donut and pie charts) or percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\ChartLegendInactiveItemsLabels
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ChartLegendInactiveItemsLabels.
    * The template which renders the labels.The fields which can be used in the template are: text - the text the legend item.; series - the data series.; value - the point value. (only for donut and pie charts) or percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts..
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\ChartLegendInactiveItemsLabels
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

//<< Properties
}

?>
