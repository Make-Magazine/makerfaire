<?php

namespace Kendo\UI;

class FilterOperators extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The texts of the filter operators displayed for string fields.
    * @param \Kendo\UI\FilterOperatorsString|array $value
    * @return \Kendo\UI\FilterOperators
    */
    public function string($value) {
        return $this->setProperty('string', $value);
    }

    /**
    * The texts of the filter operators displayed for number fields.
    * @param \Kendo\UI\FilterOperatorsNumber|array $value
    * @return \Kendo\UI\FilterOperators
    */
    public function number($value) {
        return $this->setProperty('number', $value);
    }

    /**
    * The texts of the filter operators displayed for date fields.
    * @param \Kendo\UI\FilterOperatorsDate|array $value
    * @return \Kendo\UI\FilterOperators
    */
    public function date($value) {
        return $this->setProperty('date', $value);
    }

    /**
    * The texts of the filter operators displayed for boolean fields
    * @param \Kendo\UI\FilterOperatorsBoolean|array $value
    * @return \Kendo\UI\FilterOperators
    */
    public function boolean($value) {
        return $this->setProperty('boolean', $value);
    }

//<< Properties
}

?>
