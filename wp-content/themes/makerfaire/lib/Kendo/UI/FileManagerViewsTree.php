<?php

namespace Kendo\UI;

class FileManagerViewsTree extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the template option of the FileManagerViewsTree.
    * Template for rendering each node.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerViewsTree
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the FileManagerViewsTree.
    * Template for rendering each node.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerViewsTree
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

//<< Properties
}

?>
