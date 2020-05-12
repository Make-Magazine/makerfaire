<?php

namespace Kendo\UI;

class TreeListSearch extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines a list of fields which will be included in the search. If values for the property are not defined the treelist will search in all column fields.
    * @param array $value
    * @return \Kendo\UI\TreeListSearch
    */
    public function fields($value) {
        return $this->setProperty('fields', $value);
    }

//<< Properties
}

?>
