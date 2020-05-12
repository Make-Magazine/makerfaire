<?php

namespace Kendo\UI;

class SpreadsheetSheetFilterColumnCriteriaItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The operator type of the criterion.The supported types vary based on the inferred column data type (inferred): Textcontains - The text contains the value.doesnotcontain - The text does not contain the value.startswith - The text starts with the value.endswith - The text ends with the value.; Dateeq - The date is the same as the value.neq - The date is not the same as the value.lt -  The date is before the value.gt -  The date is after the value. or Numbereq - Is equal to the value.neq - Is not equal to the value.gte - Is greater than or equal to the value.gt - Is greater than the value.lte - Is less than or equal to the value.lt - Is less than the value..
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumnCriteriaItem
    */
    public function operator($value) {
        return $this->setProperty('operator', $value);
    }

    /**
    * The value for the criteria operator.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumnCriteriaItem
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

//<< Properties
}

?>
