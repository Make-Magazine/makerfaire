<?php

namespace Kendo\UI;

class MultiViewCalendar extends \Kendo\UI\Widget {
    public function name() {
        return 'MultiViewCalendar';
    }
//>> Properties

    /**
    * Specifies the culture info used by the widget.
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function culture($value) {
        return $this->setProperty('culture', $value);
    }

    /**
    * Specifies a list of dates, which will be passed to the month template.
    * @param array $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function dates($value) {
        return $this->setProperty('dates', $value);
    }

    /**
    * Specifies the navigation depth. The following settings are available for the depth value: "month" - Shows the days of the month.; "year" - Shows the months of the year.; "decade" - Shows the years of the decade. or "century" - Shows the decades from the century..
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function depth($value) {
        return $this->setProperty('depth', $value);
    }

    /**
    * The template which renders the footer. If false, the footer will not be rendered.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function footer($value) {
        return $this->setProperty('footer', $value);
    }

    /**
    * Specifies the format, which is used to parse value set with value() method.
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * Specifies the maximum date, which the multiViewCalendar can show.
    * @param date $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function max($value) {
        return $this->setProperty('max', $value);
    }

    /**
    * Allows localization of the strings that are used in the widget.
    * @param \Kendo\UI\MultiViewCalendarMessages|array $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Specifies the minimum date, which the multiViewCalendar can show.
    * @param date $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function min($value) {
        return $this->setProperty('min', $value);
    }

    /**
    * Templates for the cells rendered in "month" view.
    * @param \Kendo\UI\MultiViewCalendarMonth|array $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function month($value) {
        return $this->setProperty('month', $value);
    }

    /**
    * This property controls how many months to be shown at same time. By default it shows two months.
    * @param float $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function views($value) {
        return $this->setProperty('views', $value);
    }

    /**
    * Configures the Kendo UI MultiViewCalendar range settings.
    * @param \Kendo\UI\MultiViewCalendarRange|array $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function range($value) {
        return $this->setProperty('range', $value);
    }

    /**
    * By default user is able to select a single date. The property can also be set to "multiple" or "range". More information about multiple selection can be found in the Selection article.
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * Specifies which dates to be selected when the multiViewCalendar is initialized.
    * @param array $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function selectDates($value) {
        return $this->setProperty('selectDates', $value);
    }

    /**
    * If set to true will render a header for every view.
    * @param boolean $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function showViewHeader($value) {
        return $this->setProperty('showViewHeader', $value);
    }

    /**
    * If set to true a week of the year will be shown on the left side of the multiViewCalendar.
    * @param boolean $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function weekNumber($value) {
        return $this->setProperty('weekNumber', $value);
    }

    /**
    * Specifies the start view. The following settings are available for the start value: "month" - Shows the days of the month.; "year" - Shows the months of the year.; "decade" - Shows the years of the decade. or "century" - Shows the decades from the century..
    * @param string $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function start($value) {
        return $this->setProperty('start', $value);
    }

    /**
    * Specifies the selected date.
    * @param date $value
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Sets the change event of the MultiViewCalendar.
    * Fires when selection is changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the navigate event of the MultiViewCalendar.
    * Fires when multiViewCalendar navigates.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\MultiViewCalendar
    */
    public function navigate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('navigate', $value);
    }


//<< Properties

    /**
    * An array or function that will be used to determine which dates to be disabled in the calendar.
    * @param array|\Kendo\JavaScriptFunction $value
    * @return \Kendo\UI\Calendar
    */
    public function disableDates($value) {
		if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }
        return $this->setProperty('disableDates', $value);
	}
}

?>
