<?php

namespace Kendo\UI;

class TreeListFilterableOperatorsNumber extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the eq (equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function eq($value) {
        return $this->setProperty('eq', $value);
    }

    /**
    * The text of the ne (not equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function neq($value) {
        return $this->setProperty('neq', $value);
    }

    /**
    * The text of the isnull filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function isnull($value) {
        return $this->setProperty('isnull', $value);
    }

    /**
    * The text of the isnotnull filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function isnotnull($value) {
        return $this->setProperty('isnotnull', $value);
    }

    /**
    * The text of the gte (greater than or equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function gte($value) {
        return $this->setProperty('gte', $value);
    }

    /**
    * The text of the gt (greater than) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function gt($value) {
        return $this->setProperty('gt', $value);
    }

    /**
    * The text of the lte (less than or equal to) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function lte($value) {
        return $this->setProperty('lte', $value);
    }

    /**
    * The text of the lt (less than) filter operator.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableOperatorsNumber
    */
    public function lt($value) {
        return $this->setProperty('lt', $value);
    }

//<< Properties
}

?>
