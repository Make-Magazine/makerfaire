<?php

namespace Kendo\UI;

class DateInput extends \Kendo\UI\Widget {
    public function name() {
        return 'DateInput';
    }

    protected function createElement() {
        return new \Kendo\Html\Element('input', true);
    }
//>> Properties

    /**
    * Specifies the format, which is used to format the value of the DateInput displayed in the input. The format also will be used to parse the input.
    * @param string $value
    * @return \Kendo\UI\DateInput
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * Specifies the maximum date which can be entered in the input.
    * @param date $value
    * @return \Kendo\UI\DateInput
    */
    public function max($value) {
        return $this->setProperty('max', $value);
    }

    /**
    * Specifies the minimum date that which be entered in the input.
    * @param date $value
    * @return \Kendo\UI\DateInput
    */
    public function min($value) {
        return $this->setProperty('min', $value);
    }

    /**
    * Specifies the selected date.
    * @param date $value
    * @return \Kendo\UI\DateInput
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * The messages that DateInput uses.  Use it to customize or localize the placeholders of each date/time part.
    * @param \Kendo\UI\DateInputMessages|array $value
    * @return \Kendo\UI\DateInput
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Sets the change event of the DateInput.
    * Fires when the selected date is changed
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DateInput
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }


//<< Properties
}

?>
