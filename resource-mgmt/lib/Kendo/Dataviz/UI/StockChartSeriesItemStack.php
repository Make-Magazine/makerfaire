<?php

namespace Kendo\Dataviz\UI;

class StockChartSeriesItemStack extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The type of stack to plot. The following types are supported: "normal" - the value of the stack is the sum of all points in the category (or group) or "100%" - the value of the stack is always 100% (1.00). Points within the category (or group) are represented as percentages..
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartSeriesItemStack
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * Indicates that the series should be stacked in a group with the specified name.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartSeriesItemStack
    */
    public function group($value) {
        return $this->setProperty('group', $value);
    }

//<< Properties
}

?>
