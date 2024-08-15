<?php

namespace Kendo\UI;

class TreeListFilterableMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text of the option which represents the AND logical operation.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function _and($value) {
        return $this->setProperty('and', $value);
    }

    /**
    * The text of the button which clears the filter.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function clear($value) {
        return $this->setProperty('clear', $value);
    }

    /**
    * The text of the button which applies the filter.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function filter($value) {
        return $this->setProperty('filter', $value);
    }

    /**
    * The text of the information message on top of the filter menu.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function info($value) {
        return $this->setProperty('info', $value);
    }

    /**
    * The text that is rendered for the title attribute of the filter menu form.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * The text of the radio button for false values. Displayed when the user filters Boolean fields.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function isFalse($value) {
        return $this->setProperty('isFalse', $value);
    }

    /**
    * The text of the radio button for true values. Displayed when the user filters Boolean fields.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function isTrue($value) {
        return $this->setProperty('isTrue', $value);
    }

    /**
    * The text of the option which represents the OR logical operation.
    * @param string $value
    * @return \Kendo\UI\TreeListFilterableMessages
    */
    public function _or($value) {
        return $this->setProperty('or', $value);
    }

//<< Properties
}

?>
