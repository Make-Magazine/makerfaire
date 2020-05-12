<?php

namespace Kendo\UI;

class FileManager extends \Kendo\UI\Widget {
    public function name() {
        return 'FileManager';
    }
//>> Properties

    /**
    * Configures the width of the FileManager.
    * @param float|string $value
    * @return \Kendo\UI\FileManager
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Configures the height of the FileManager.
    * @param float|string $value
    * @return \Kendo\UI\FileManager
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * Configures the initial view of the FileManager.
    * @param string $value
    * @return \Kendo\UI\FileManager
    */
    public function initialView($value) {
        return $this->setProperty('initialView', $value);
    }

    /**
    * Configures the resizable features of the FileManager.
    * @param boolean $value
    * @return \Kendo\UI\FileManager
    */
    public function resizable($value) {
        return $this->setProperty('resizable', $value);
    }

    /**
    * Enables or disables the drag and drop features of the FileManager.
    * @param boolean $value
    * @return \Kendo\UI\FileManager
    */
    public function draggable($value) {
        return $this->setProperty('draggable', $value);
    }

    /**
    * Sets the data source of the FileManager.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\FileManager
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * Sets the upload url for the Upload widget.
    * @param string $value
    * @return \Kendo\UI\FileManager
    */
    public function uploadUrl($value) {
        return $this->setProperty('uploadUrl', $value);
    }

    /**
    * Configures the Toolbar of the FileManager
    * @param boolean|\Kendo\UI\FileManagerToolbar|array $value
    * @return \Kendo\UI\FileManager
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * Specifies the composite Dialog widgets of the FileManager.
    * @param \Kendo\UI\FileManagerDialogs|array $value
    * @return \Kendo\UI\FileManager
    */
    public function dialogs($value) {
        return $this->setProperty('dialogs', $value);
    }

    /**
    * Configures the ContextMenu of the FileManager.
    * @param boolean|\Kendo\UI\FileManagerContextMenu|array $value
    * @return \Kendo\UI\FileManager
    */
    public function contextMenu($value) {
        return $this->setProperty('contextMenu', $value);
    }

    /**
    * Configures every view registered for the FileManager.
    * @param \Kendo\UI\FileManagerViews|array $value
    * @return \Kendo\UI\FileManager
    */
    public function views($value) {
        return $this->setProperty('views', $value);
    }

    /**
    * Configures the Preview Pane of the FileManager.
    * @param \Kendo\UI\FileManagerPreviewPane|array $value
    * @return \Kendo\UI\FileManager
    */
    public function previewPane($value) {
        return $this->setProperty('previewPane', $value);
    }

    /**
    * Configures or disables the Breadcrumb component.
    * @param boolean|\Kendo\UI\FileManagerBreadcrumb|array $value
    * @return \Kendo\UI\FileManager
    */
    public function breadcrumb($value) {
        return $this->setProperty('breadcrumb', $value);
    }

    /**
    * Defines the text of the localizable UI parts of the FileManager.
    * @param \Kendo\UI\FileManagerMessages|array $value
    * @return \Kendo\UI\FileManager
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Configures the composite Upload widget of the FileManager. Accepts the same options as the kendoUpload widget.
    * @param \Kendo\UI\FileManagerUpload|array $value
    * @return \Kendo\UI\FileManager
    */
    public function upload($value) {
        return $this->setProperty('upload', $value);
    }

    /**
    * Sets the navigate event of the FileManager.
    * Fired when navigation occurs.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function navigate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('navigate', $value);
    }

    /**
    * Sets the select event of the FileManager.
    * Fired when selection changes.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the open event of the FileManager.
    * Fired when a file is opened (with double click).
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the execute event of the FileManager.
    * Fired when a command is executed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function execute($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('execute', $value);
    }

    /**
    * Sets the error event of the FileManager.
    * Fired when a error in the DataSource happen.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function error($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('error', $value);
    }

    /**
    * Sets the dataBinding event of the FileManager.
    * Fired before the widget binds to its data source.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function dataBinding($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBinding', $value);
    }

    /**
    * Sets the dataBound event of the FileManager.
    * Fired when the widget is bound to data from its data source.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the drop event of the FileManager.
    * Fired when a file is dragged and dropped over a folder.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\FileManager
    */
    public function drop($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drop', $value);
    }


//<< Properties
}

?>
