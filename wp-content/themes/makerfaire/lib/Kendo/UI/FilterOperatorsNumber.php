<?php

namespace Kendo\UI;

class FilterOperatorsNumber extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the "equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function eq($value) {
        return $this->setProperty('eq', $value);
    }

    /**
    * The text of the "not equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function neq($value) {
        return $this->setProperty('neq', $value);
    }

    /**
    * The text of the "isnull" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function isnull($value) {
        return $this->setProperty('isnull', $value);
    }

    /**
    * The text of the "isnotnull" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function isnotnull($value) {
        return $this->setProperty('isnotnull', $value);
    }

    /**
    * The text of the "greater than or equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function gte($value) {
        return $this->setProperty('gte', $value);
    }

    /**
    * The text of the "greater than" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function gt($value) {
        return $this->setProperty('gt', $value);
    }

    /**
    * The text of the "less than or equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function lte($value) {
        return $this->setProperty('lte', $value);
    }

    /**
    * The text of the "less than" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function lt($value) {
        return $this->setProperty('lt', $value);
    }

//<< Properties

    /**
    * Sets a custom operator.
    * @param \Kendo\UI\FilterCustomOperator $value
    * @return \Kendo\UI\FilterOperatorsNumber
    */
    public function custom($value) {
        $name = $value->getProperty('name');
        $handler = $value->properties()['handler'];
        $text = $value->properties()['text'];

        return $this->setProperty($name, array('handler' => $handler, 'text' => $text ));
    }
}

?>
