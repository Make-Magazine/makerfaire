<?php

namespace Kendo\UI;

class FilterOperatorsBoolean extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the "equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsBoolean
    */
    public function eq($value) {
        return $this->setProperty('eq', $value);
    }

    /**
    * The text of the "not equal" filter operator.
    * @param string $value
    * @return \Kendo\UI\FilterOperatorsBoolean
    */
    public function neq($value) {
        return $this->setProperty('neq', $value);
    }

//<< Properties

    /**
    * Sets a custom operator.
    * @param \Kendo\UI\FilterCustomOperator $value
    * @return \Kendo\UI\FilterOperatorsBoolean
    */
    public function custom($value) {
        $name = $value->getProperty('name');
        $handler = $value->properties()['handler'];
        $text = $value->properties()['text'];

        return $this->setProperty($name, array('handler' => $handler, 'text' => $text ));
    }
}

?>
