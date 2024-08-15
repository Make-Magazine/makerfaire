<?php

namespace Kendo\UI;

class DateInputMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The placeholder for the years part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function year($value) {
        return $this->setProperty('year', $value);
    }

    /**
    * The placeholder for the months part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function month($value) {
        return $this->setProperty('month', $value);
    }

    /**
    * The placeholder for the day of the month part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function day($value) {
        return $this->setProperty('day', $value);
    }

    /**
    * The placeholder for the day of the week part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function weekday($value) {
        return $this->setProperty('weekday', $value);
    }

    /**
    * The placeholder for the hours part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function hour($value) {
        return $this->setProperty('hour', $value);
    }

    /**
    * The placeholder for the minutes part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function minute($value) {
        return $this->setProperty('minute', $value);
    }

    /**
    * The placeholder for the seconds part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function second($value) {
        return $this->setProperty('second', $value);
    }

    /**
    * The placeholder for the AM/PM part.
    * @param string $value
    * @return \Kendo\UI\DateInputMessages
    */
    public function dayperiod($value) {
        return $this->setProperty('dayperiod', $value);
    }

//<< Properties
}

?>
