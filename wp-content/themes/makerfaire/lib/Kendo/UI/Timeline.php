<?php

namespace Kendo\UI;

class Timeline extends \Kendo\UI\Widget {
    public function name() {
        return 'Timeline';
    }
//>> Properties

    /**
    * If set to false, the Timeline will not bind to the data source during initialization, i.e. it will not call the fetch method of the dataSource instance. In such scenarios data binding will occur when the change event of the dataSource instance is fired. By default, autoBind is set to true and the widget will bind to the data source specified in the configuration.
    * @param boolean $value
    * @return \Kendo\UI\Timeline
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Indicates whether events should be positioned on both sides of the timeline axis. By default all events are displayed on the right side of the timeline axis.
    * @param boolean $value
    * @return \Kendo\UI\Timeline
    */
    public function alternatingMode($value) {
        return $this->setProperty('alternatingMode', $value);
    }

    /**
    * Enables the events in the Kendo UI Timeline to be expandable or collapsible. When enabled initially all events are collapsed.
    * @param boolean $value
    * @return \Kendo\UI\Timeline
    */
    public function collapsibleEvents($value) {
        return $this->setProperty('collapsibleEvents', $value);
    }

    /**
    * Sets the field of the data item that provides the actions information for the event.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataActionsField($value) {
        return $this->setProperty('dataActionsField', $value);
    }

    /**
    * Sets the field of the data item that provides the description information for the event.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataDescriptionField($value) {
        return $this->setProperty('dataDescriptionField', $value);
    }

    /**
    * Sets the field of the data item that provides information when the given event happened in time.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataDateField($value) {
        return $this->setProperty('dataDateField', $value);
    }

    /**
    * Sets the field of the data item that provides the images information for the event.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataImagesField($value) {
        return $this->setProperty('dataImagesField', $value);
    }

    /**
    * Sets the field of the data item that provides the subtitle information for the event.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataSubtitleField($value) {
        return $this->setProperty('dataSubtitleField', $value);
    }

    /**
    * Sets the field of the data item that provides the title information for the event.
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dataTitleField($value) {
        return $this->setProperty('dataTitleField', $value);
    }

    /**
    * Sets the data source of the Timeline.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\Timeline
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * Sets the eventTemplate option of the Timeline.
    * Specifies the template used to render the event details.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Timeline
    */
    public function eventTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('eventTemplate', $value);
    }

    /**
    * Sets the eventTemplate option of the Timeline.
    * Specifies the template used to render the event details.
    * @param string $value The template content.
    * @return \Kendo\UI\Timeline
    */
    public function eventTemplate($value) {
        return $this->setProperty('eventTemplate', $value);
    }

    /**
    * Sets the date format used to display the date in the event's label
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function dateFormat($value) {
        return $this->setProperty('dateFormat', $value);
    }

    /**
    * Sets specific height for the event in horizontal mode.
    * @param float $value
    * @return \Kendo\UI\Timeline
    */
    public function eventHeight($value) {
        return $this->setProperty('eventHeight', $value);
    }

    /**
    * Sets specific width for the event in vertical mode.
    * @param float $value
    * @return \Kendo\UI\Timeline
    */
    public function eventWidth($value) {
        return $this->setProperty('eventWidth', $value);
    }

    /**
    * If set to false, the event's date label will be hidden.
    * @param boolean $value
    * @return \Kendo\UI\Timeline
    */
    public function showDateLabels($value) {
        return $this->setProperty('showDateLabels', $value);
    }

    /**
    * Defines the orientation of the Timeline widget
    * @param string $value
    * @return \Kendo\UI\Timeline
    */
    public function orientation($value) {
        return $this->setProperty('orientation', $value);
    }

    /**
    * Sets the change event of the Timeline.
    * Fires when a new event is opened.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the dataBound event of the Timeline.
    * Fires when the Timeline is bound to data.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the expand event of the Timeline.
    * Fires when an event is going to be expanded.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function expand($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('expand', $value);
    }

    /**
    * Sets the collapse event of the Timeline.
    * Fires when an event is going to be collapsed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function collapse($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('collapse', $value);
    }

    /**
    * Sets the actionClick event of the Timeline.
    * Fires when an action for an event is clicked.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function actionClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('actionClick', $value);
    }

    /**
    * Sets the navigate event of the Timeline.
    * Fires when the left or right arrow is clicked.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Timeline
    */
    public function navigate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('navigate', $value);
    }


//<< Properties
}

?>
