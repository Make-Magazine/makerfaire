<?php

namespace Kendo\UI;

class FileManagerPreviewPane extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the meta fields to be showed in the preview pane.
    * @param array $value
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function metaFields($value) {
        return $this->setProperty('metaFields', $value);
    }

    /**
    * Sets the noFileTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when no file/folder is selected.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function noFileTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('noFileTemplate', $value);
    }

    /**
    * Sets the noFileTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when no file/folder is selected.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function noFileTemplate($value) {
        return $this->setProperty('noFileTemplate', $value);
    }

    /**
    * Sets the singleFileTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when a single file/folder is selected.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function singleFileTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('singleFileTemplate', $value);
    }

    /**
    * Sets the singleFileTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when a single file/folder is selected.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function singleFileTemplate($value) {
        return $this->setProperty('singleFileTemplate', $value);
    }

    /**
    * Sets the multipleFilesTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when multiple files/folders are selected.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function multipleFilesTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('multipleFilesTemplate', $value);
    }

    /**
    * Sets the multipleFilesTemplate option of the FileManagerPreviewPane.
    * Defines a new template for the preview pane when multiple files/folders are selected.
    * @param string $value The template content.
    * @return \Kendo\UI\FileManagerPreviewPane
    */
    public function multipleFilesTemplate($value) {
        return $this->setProperty('multipleFilesTemplate', $value);
    }

//<< Properties
}

?>
