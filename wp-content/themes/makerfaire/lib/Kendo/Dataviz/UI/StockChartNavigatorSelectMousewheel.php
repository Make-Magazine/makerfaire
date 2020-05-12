<?php

namespace Kendo\Dataviz\UI;

class StockChartNavigatorSelectMousewheel extends \Kendo\SerializableObject {
//>> Properties

    /**
    * If set to true will reverse the mouse wheel direction. The normal direction is down for "zoom out", up for "zoom in".
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSelectMousewheel
    */
    public function reverse($value) {
        return $this->setProperty('reverse', $value);
    }

    /**
    * The zoom direction.The supported values are: "both" - zooming expands and contracts the selection both sides; "left" - zooming expands and contracts the selection left side only or "right" - zooming expands and contracts the selection right side only.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSelectMousewheel
    */
    public function zoom($value) {
        return $this->setProperty('zoom', $value);
    }

//<< Properties
}

?>
