<?php

namespace Kendo\UI;

class DateRangePicker extends \Kendo\UI\Widget {
    public function name() {
        return 'DateRangePicker';
    }
//>> Properties

    /**
    * Sets the ARIATemplate option of the DateRangePicker.
    * Specifies a template used to populate value of the aria-label attribute.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DateRangePicker
    */
    public function ARIATemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('ARIATemplate', $value);
    }

    /**
    * Sets the ARIATemplate option of the DateRangePicker.
    * Specifies a template used to populate value of the aria-label attribute.
    * @param string $value The template content.
    * @return \Kendo\UI\DateRangePicker
    */
    public function ARIATemplate($value) {
        return $this->setProperty('ARIATemplate', $value);
    }

    /**
    * Specifies the culture info used by the widget.
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function culture($value) {
        return $this->setProperty('culture', $value);
    }

    /**
    * Specifies a list of dates, which will be passed to the month template.
    * @param array $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function dates($value) {
        return $this->setProperty('dates', $value);
    }

    /**
    * Specifies the navigation depth. The following settings are available for the depth value: "month" - Shows the days of the month. (default value); "year" - Shows the months of the year.; "decade" - Shows the years of the decade. or "century" - Shows the decades from the century..
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function depth($value) {
        return $this->setProperty('depth', $value);
    }

    /**
    * An array or function that will be used to determine which dates to be disabled for selection by the widget.
    * @param array|\Kendo\JavaScriptFunction $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function disableDates($value) {
        return $this->setProperty('disableDates', $value);
    }

    /**
    * Specifies the end field name for model binding.
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function endField($value) {
        return $this->setProperty('endField', $value);
    }

    /**
    * The template which renders the footer of the calendar. If false, the footer will not be rendered.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function footer($value) {
        return $this->setProperty('footer', $value);
    }

    /**
    * Specifies the format, which is used to format the value of the DateRangePicker displayed in the input. The format also will be used to parse the input.For more information on date and time formats please refer to Date Formatting.
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * Specifies the maximum date, which the calendar can show.
    * @param date $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function max($value) {
        return $this->setProperty('max', $value);
    }

    /**
    * Allows localization of the strings that are used in the widget.
    * @param \Kendo\UI\DateRangePickerMessages|array $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Specifies the minimum date that the calendar can show.
    * @param date $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function min($value) {
        return $this->setProperty('min', $value);
    }

    /**
    * Templates for the cells rendered in the calendar "month" view.
    * @param \Kendo\UI\DateRangePickerMonth|array $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function month($value) {
        return $this->setProperty('month', $value);
    }

    /**
    * Determines if the labels for the inputs will be visible.
    * @param boolean $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function labels($value) {
        return $this->setProperty('labels', $value);
    }

    /**
    * If set to true a week of the year will be shown on the left side of the calendar. It is possible to define a template in order to customize what will be displayed.
    * @param boolean $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function weekNumber($value) {
        return $this->setProperty('weekNumber', $value);
    }

    /**
    * Configures the Kendo UI DateRangePicker range settings.
    * @param \Kendo\UI\DateRangePickerRange|array $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function range($value) {
        return $this->setProperty('range', $value);
    }

    /**
    * Specifies the start view. The following settings are available for the start value: "month" - Shows the days of the month.; "year" - Shows the months of the year.; "decade" - Shows the years of the decade. or "century" - Shows the decades from the century..
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function start($value) {
        return $this->setProperty('start', $value);
    }

    /**
    * Specifies the start field name for model binding.
    * @param string $value
    * @return \Kendo\UI\DateRangePicker
    */
    public function startField($value) {
        return $this->setProperty('startField', $value);
    }

    /**
    * Sets the change event of the DateRangePicker.
    * Fires when the selected date is changed
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DateRangePicker
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the close event of the DateRangePicker.
    * Fires when the calendar is closed
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DateRangePicker
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the open event of the DateRangePicker.
    * Fires when the calendar is opened
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DateRangePicker
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }


//<< Properties
}

?>
