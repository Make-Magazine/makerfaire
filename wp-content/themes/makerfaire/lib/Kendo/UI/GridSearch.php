<?php

namespace Kendo\UI;

class GridSearch extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines a list of fields which will be included in the search. If values for the property are not defined the grid will search in all column fields.
    * @param array $value
    * @return \Kendo\UI\GridSearch
    */
    public function fields($value) {
        return $this->setProperty('fields', $value);
    }

//<< Properties
}

?>
