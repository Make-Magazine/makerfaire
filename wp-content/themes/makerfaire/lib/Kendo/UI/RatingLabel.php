<?php

namespace Kendo\UI;

class RatingLabel extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the template option of the RatingLabel.
    * 
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\RatingLabel
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the RatingLabel.
    * 
    * @param string $value The template content.
    * @return \Kendo\UI\RatingLabel
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

//<< Properties
}

?>
