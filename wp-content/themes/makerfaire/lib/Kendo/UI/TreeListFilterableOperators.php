<?php

namespace Kendo\UI;

class TreeListFilterableOperators extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The texts of the filter operators that are displayed for columns whcih are bound to string fields.In the following example, only the Contains... and Starts with... operators will be displayed in the DropDownList.
    * @param \Kendo\UI\TreeListFilterableOperatorsString|array $value
    * @return \Kendo\UI\TreeListFilterableOperators
    */
    public function string($value) {
        return $this->setProperty('string', $value);
    }

    /**
    * The texts of the filter operators that are displayed for columns which are bound to number fields.In the following example, only the Equal to... and Not equal to... operators will be displayed in the operators DropDownList.
    * @param \Kendo\UI\TreeListFilterableOperatorsNumber|array $value
    * @return \Kendo\UI\TreeListFilterableOperators
    */
    public function number($value) {
        return $this->setProperty('number', $value);
    }

    /**
    * The texts of the filter operators that are displayed for columns which are bound to date fields.In the following example, only the Is before... and Is after... operators will be displayed in the operators DropDownList.
    * @param \Kendo\UI\TreeListFilterableOperatorsDate|array $value
    * @return \Kendo\UI\TreeListFilterableOperators
    */
    public function date($value) {
        return $this->setProperty('date', $value);
    }

//<< Properties
}

?>
