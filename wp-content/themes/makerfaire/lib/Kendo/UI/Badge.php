<?php

namespace Kendo\UI;

class Badge extends \Kendo\UI\Widget {
    public function name() {
        return 'Badge';
    }

    protected function createElement() {
        return new \Kendo\Html\Element('span', true);
    }
//>> Properties

    /**
    * Specifies the shape of the badge - rectangle or pill.
    * @param string $value
    * @return \Kendo\UI\Badge
    */
    public function appearance($value) {
        return $this->setProperty('appearance', $value);
    }

    /**
    * Specifies the look of the badge - flat or outline.
    * @param string $value
    * @return \Kendo\UI\Badge
    */
    public function look($value) {
        return $this->setProperty('look', $value);
    }

    /**
    * Sets the template option of the Badge.
    * The template which renders the content of the badge
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Badge
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Badge.
    * The template which renders the content of the badge
    * @param string $value The template content.
    * @return \Kendo\UI\Badge
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Specifies the type of the badge - primary, secondary, info, success, warning and error.
    * @param string $value
    * @return \Kendo\UI\Badge
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * The value of the badge
    * @param string|float $value
    * @return \Kendo\UI\Badge
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * If set to false the badge will not be displayed.
    * @param boolean $value
    * @return \Kendo\UI\Badge
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }


//<< Properties
}

?>
