<?php

namespace Kendo\UI;

class FilterOperatorsString extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the "equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function eq($value) {
        return $this->setProperty('eq', $value);
    }

    /**
    * The text of the "not equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function neq($value) {
        return $this->setProperty('neq', $value);
    }

    /**
    * The text of the "isnull" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isnull($value) {
        return $this->setProperty('isnull', $value);
    }

    /**
    * The text of the "isnotnull" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isnotnull($value) {
        return $this->setProperty('isnotnull', $value);
    }

    /**
    * The text of the "isempty" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isempty($value) {
        return $this->setProperty('isempty', $value);
    }

    /**
    * The text of the "isnotempty" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isnotempty($value) {
        return $this->setProperty('isnotempty', $value);
    }

    /**
    * The text of the "starts with" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function startswith($value) {
        return $this->setProperty('startswith', $value);
    }

    /**
    * The text of the "contains" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function contains($value) {
        return $this->setProperty('contains', $value);
    }

    /**
    * The text of the "does not contain" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function doesnotcontain($value) {
        return $this->setProperty('doesnotcontain', $value);
    }

    /**
    * The text of the "ends with" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function endswith($value) {
        return $this->setProperty('endswith', $value);
    }

    /**
    * The text of the "isnullorempty" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isnullorempty($value) {
        return $this->setProperty('isnullorempty', $value);
    }

    /**
    * The text of the "isnotnullorempty" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function isnotnullorempty($value) {
        return $this->setProperty('isnotnullorempty', $value);
    }

//<< Properties

    /**
    * Sets a custom operator.
    * @param \Kendo\UI\FilterCustomOperator $value
    * @return \Kendo\UI\FilterOperatorsString
    */
    public function custom($value) {
        $name = $value->getProperty('name');
        $handler = $value->properties()['handler'];
        $text = $value->properties()['text'];

        return $this->setProperty($name, array('handler' => $handler, 'text' => $text ));
    }
}

?>
