<?php

namespace Kendo\UI;

class TreeListFilterableOperatorsString extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the eq (equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function eq($value) {
        return $this->setProperty('eq', $value);
    }

    /**
    * The text of the ne (not equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function neq($value) {
        return $this->setProperty('neq', $value);
    }

    /**
    * The text of the isnull filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function isnull($value) {
        return $this->setProperty('isnull', $value);
    }

    /**
    * The text of the isnotnull filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function isnotnull($value) {
        return $this->setProperty('isnotnull', $value);
    }

    /**
    * The text of the isempty filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function isempty($value) {
        return $this->setProperty('isempty', $value);
    }

    /**
    * The text of the isnotempty filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function isnotempty($value) {
        return $this->setProperty('isnotempty', $value);
    }

    /**
    * The text of the startswith filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function startswith($value) {
        return $this->setProperty('startswith', $value);
    }

    /**
    * The text of the contains filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function contains($value) {
        return $this->setProperty('contains', $value);
    }

    /**
    * The text of the doesnotcontain filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function doesnotcontain($value) {
        return $this->setProperty('doesnotcontain', $value);
    }

    /**
    * The text of the endswith filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsString
    */
    public function endswith($value) {
        return $this->setProperty('endswith', $value);
    }

//<< Properties
}

?>
