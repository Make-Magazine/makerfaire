<?php

namespace Kendo\UI;

class FileManagerViewsList extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the editTemplate option of the FileManagerViewsList.
    * Specifies the template for items during edit mode.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function editTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('editTemplate', $value);
    }

    /**
    * Sets the editTemplate option of the FileManagerViewsList.
    * Specifies the template for items during edit mode.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function editTemplate($value) {
        return $this->setProperty('editTemplate', $value);
    }

    /**
    * Sets the template option of the FileManagerViewsList.
    * Specifies item template.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the FileManagerViewsList.
    * Specifies item template.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Sets the altTemplate option of the FileManagerViewsList.
    * Template to be used for rendering the alternate items.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function altTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('altTemplate', $value);
    }

    /**
    * Sets the altTemplate option of the FileManagerViewsList.
    * Template to be used for rendering the alternate items.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsList
    */
    public function altTemplate($value) {
        return $this->setProperty('altTemplate', $value);
    }

//<< Properties
}

?>
