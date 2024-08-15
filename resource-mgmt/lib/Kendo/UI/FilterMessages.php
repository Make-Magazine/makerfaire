<?php

namespace Kendo\UI;

class FilterMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the option which represents the "and" logical operation.
    * @param string $value
    * @return \Kendo\UI\FilterMessages
    */
    public function _and($value) {
        return $this->setProperty('and', $value);
    }

    /**
    * The text inside the apply button.
    * @param string $value
    * @return \Kendo\UI\FilterMessages
    */
    public function apply($value) {
        return $this->setProperty('apply', $value);
    }

    /**
    * The text of the option which represents the "or" logical operation.
    * @param string $value
    * @return \Kendo\UI\FilterMessages
    */
    public function _or($value) {
        return $this->setProperty('or', $value);
    }

//<< Properties
}

?>
