<?php

namespace Kendo\Dataviz\UI;

class StockChartNavigatorSeriesItemTooltip extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The background color of the tooltip. The default is determined from the series color.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The border configuration options.
    * @param \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltipBorder|array $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The text color of the tooltip. The default is the same as the series labels color.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The tooltip font.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function font($value) {
        return $this->setProperty('font', $value);
    }

    /**
    * The tooltip format. Format variables depend on the series type: Area, column, line and pie0 - value or Candlestick and OHLC0 - open value1 - high value2 - low value3 - close value4 - category name.
    * @param string $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The padding of the tooltip.
    * @param float| $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function padding($value) {
        return $this->setProperty('padding', $value);
    }

    /**
    * Sets the template option of the StockChartNavigatorSeriesItemTooltip.
    * The tooltip template. Template variables: value - the point value (either a number or an object); category - the category name; series - the data series or dataItem - the original data item used to construct the point.     Will be null if binding to array..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the StockChartNavigatorSeriesItemTooltip.
    * The tooltip template. Template variables: value - the point value (either a number or an object); category - the category name; series - the data series or dataItem - the original data item used to construct the point.     Will be null if binding to array..
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * A value indicating if the tooltip should be displayed.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\StockChartNavigatorSeriesItemTooltip
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

//<< Properties
}

?>
