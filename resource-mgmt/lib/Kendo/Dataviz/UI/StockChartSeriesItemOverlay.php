<?php

namespace Kendo\Dataviz\UI;

class StockChartSeriesItemOverlay extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The gradient name.Available options: glass (column and candlestick series) or none.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartSeriesItemOverlay
    */
    public function gradient($value) {
        return $this->setProperty('gradient', $value);
    }

//<< Properties
}

?>
