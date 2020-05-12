<?php

namespace Kendo\Dataviz\UI;

class ChartCategoryAxisItemLabels extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The background color of the labels. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The border of the labels.
    * @param \Kendo\Dataviz\UI\ChartCategoryAxisItemLabelsBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The text color of the labels. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The culture to use when formatting date values. See the globalization overview for more information.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function culture($value) {
        return $this->setProperty('culture', $value);
    }

    /**
    * The format used to display labels for date category axis. The {0} placeholder represents the category value.The chart will choose the appropriate format for the current categoryAxis.baseUnit. Setting the categoryAxis.labels.format option will override the date formats.See also: kendo.format.
    * @param \Kendo\Dataviz\UI\ChartCategoryAxisItemLabelsDateFormats|array $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function dateFormats($value) {
        return $this->setProperty('dateFormats', $value);
    }

    /**
    * The font style of the labels. Accepts a valid CSS color string, for example "20px 'Courier New'".
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function font($value) {
        return $this->setProperty('font', $value);
    }

    /**
    * The format used to display the labels. Uses kendo.format. Contains one placeholder ("{0}") which represents the category value.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The margin of the labels. A numeric value will set all margins.
    * @param float|\Kendo\Dataviz\UI\ChartCategoryAxisItemLabelsMargin|array $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function margin($value) {
        return $this->setProperty('margin', $value);
    }

    /**
    * If set to true the chart will mirror the axis labels and ticks. If the labels are normally on the left side of the axis, mirroring the axis will render them to the right.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function mirror($value) {
        return $this->setProperty('mirror', $value);
    }

    /**
    * The padding of the labels. A numeric value will set all paddings.
    * @param float|\Kendo\Dataviz\UI\ChartCategoryAxisItemLabelsPadding|array $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function padding($value) {
        return $this->setProperty('padding', $value);
    }

    /**
    * The position of the axis labels. By default, labels are positioned next to the axis. When position is set to end, the labels are placed at the end of the crossing axis— typically, at the top or right end of the Chart unless the crossing axis was reversed. or When position is set to start, the labels are placed at the start of the crossing axis— typically, at the left or bottom end of the Chart unless the crossing axis was reversed..
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * The rotation angle of the labels. By default the labels are not rotated. Can be set to "auto" if the axis is horizontal in which case the labels will be rotated only if the slot size is not sufficient for the entire labels.
    * @param float|string|\Kendo\Dataviz\UI\ChartCategoryAxisItemLabelsRotation|array $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function rotation($value) {
        return $this->setProperty('rotation', $value);
    }

    /**
    * The number of labels to skip. By default no labels are skipped.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function skip($value) {
        return $this->setProperty('skip', $value);
    }

    /**
    * The label rendering step - render every n-th label. By default every label is rendered.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function step($value) {
        return $this->setProperty('step', $value);
    }

    /**
    * Sets the template option of the ChartCategoryAxisItemLabels.
    * The template which renders the labels.The fields which can be used in the template are: value - the category value; dataItem - the data item, in case a field has been specified. If the category does not have a corresponding item in the data then an empty object will be passed.; format - the default format of the label or culture - the default culture (if set) on the label.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ChartCategoryAxisItemLabels.
    * The template which renders the labels.The fields which can be used in the template are: value - the category value; dataItem - the data item, in case a field has been specified. If the category does not have a corresponding item in the data then an empty object will be passed.; format - the default format of the label or culture - the default culture (if set) on the label.
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * If set to true the chart will display the category axis labels. By default the category axis labels are visible.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * Sets the visual option of the ChartCategoryAxisItemLabels.
    * A function that can be used to create a custom visual for the labels. The available argument fields are: createVisual - a function that can be used to get the default visual.; culture - the default culture (if set) on the label; dataItem - the data item, in case a field has been specified; format - the default format of the label; options - the label options.; rect - the kendo.geometry.Rect that defines where the visual should be rendered.; sender - the chart instance (may be undefined).; text - the label text. or value - the category value.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartCategoryAxisItemLabels
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

//<< Properties
}

?>
