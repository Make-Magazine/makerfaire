<?php

namespace Kendo\UI;

class FileManagerMessagesViews extends \Kendo\SerializableObject {
//>> Properties

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function nameField($value) {
        return $this->setProperty('nameField', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function sizeField($value) {
        return $this->setProperty('sizeField', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function typeField($value) {
        return $this->setProperty('typeField', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function dateModifiedField($value) {
        return $this->setProperty('dateModifiedField', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function dateCreatedField($value) {
        return $this->setProperty('dateCreatedField', $value);
    }

    /**
    * 
    * @param string $value
    * @return \Kendo\UI\FileManagerMessagesViews
    */
    public function items($value) {
        return $this->setProperty('items', $value);
    }

//<< Properties
}

?>
