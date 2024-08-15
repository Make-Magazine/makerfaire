<?php

namespace Kendo\UI;

class Upload extends \Kendo\UI\Widget {
    protected function name() {
        return 'Upload';
    }

    protected function createElement() {
        $element = new \Kendo\Html\Element('input', true);

        $element->attr('type', 'file');

        return $element;
    }

    /**
    * List of files to be initially rendered in the Upload widget files list.
    * @param array $value
    * @return \Kendo\UI\Upload
    */
    public function files($value) {
        return $this->setProperty('files', $value);
    }

//>> Properties

    /**
    * Configures the asynchronous upload of files. For more details, refer to the article on the async mode of the Upload.
    * @param \Kendo\UI\UploadAsync|array $value
    * @return \Kendo\UI\Upload
    */
    public function async($value) {
        return $this->setProperty('async', $value);
    }

    /**
    * Enables the selection of folders instead of files. When the user selects a directory, its entire content hierarchy of files is included in the set of selected items. The directory setting is available only in browsers which support webkitdirectory.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function directory($value) {
        return $this->setProperty('directory', $value);
    }

    /**
    * Enables the dropping of folders over the Upload and its drop zone. When a directory is dropped, its entire content hierarchy of files is included in the set of selected items. The directoryDrop setting is available only in browsers which support DataTransferItem and webkitGetAsEntry.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function directoryDrop($value) {
        return $this->setProperty('directoryDrop', $value);
    }

    /**
    * Initializes a drop-zone element based on a given selector, which provides the drag-and-drop file upload.
    * @param string $value
    * @return \Kendo\UI\Upload
    */
    public function dropZone($value) {
        return $this->setProperty('dropZone', $value);
    }

    /**
    * Enables (if set to true) or disables (if set to false) an Upload. To re-enable a disabled Upload, use enable().
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function enabled($value) {
        return $this->setProperty('enabled', $value);
    }

    /**
    * Adds UploadFile to the Upload.
    * @param \Kendo\UI\UploadFile|array,... $value one or more UploadFile to add.
    * @return \Kendo\UI\Upload
    */
    public function addFile($value) {
        return $this->add('files', func_get_args());
    }

    /**
    * Sets the strings rendered by the Upload.
    * @param \Kendo\UI\UploadLocalization|array $value
    * @return \Kendo\UI\Upload
    */
    public function localization($value) {
        return $this->setProperty('localization', $value);
    }

    /**
    * Enables (if set to true) or disables (if set to false) the selection of multiple files. If set to false, the user can select only one file at a time.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function multiple($value) {
        return $this->setProperty('multiple', $value);
    }

    /**
    * Enables (if set to true) or disables (if set to false) the display of a file listing for the file upload. The disabling of a file listing might be useful if you want to customize the UI. To build your own UI, use the client-side events.
    * @param boolean $value
    * @return \Kendo\UI\Upload
    */
    public function showFileList($value) {
        return $this->setProperty('showFileList', $value);
    }

    /**
    * Sets the template option of the Upload.
    * Sets a template for rendering the files in the file list.The template data Array consists of: name - The name of the file. If in batch upload mode, represents a string combination of all file names separated with comma.; size - The file size in bytes. If in batch upload mode, represents the total file size. If not available, the value is null. or files - An array which contains information about all selected files (name, size, and extension)..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\Upload
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Upload.
    * Sets a template for rendering the files in the file list.The template data Array consists of: name - The name of the file. If in batch upload mode, represents a string combination of all file names separated with comma.; size - The file size in bytes. If in batch upload mode, represents the total file size. If not available, the value is null. or files - An array which contains information about all selected files (name, size, and extension)..
    * @param string $value The template content.
    * @return \Kendo\UI\Upload
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Configures the validation options for uploaded files.
    * @param \Kendo\UI\UploadValidation|array $value
    * @return \Kendo\UI\Upload
    */
    public function validation($value) {
        return $this->setProperty('validation', $value);
    }

    /**
    * Sets the cancel event of the Upload.
    * Fires when the upload was cancelled while in progress.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function cancel($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the clear event of the Upload.
    * Fires when the files are cleared by clicking on the Clear button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function clear($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('clear', $value);
    }

    /**
    * Sets the complete event of the Upload.
    * Fires when all active uploads complete—either successfully or with errors.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function complete($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('complete', $value);
    }

    /**
    * Sets the error event of the Upload.
    * Fires when an upload or remove operation fails.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function error($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('error', $value);
    }

    /**
    * Sets the pause event of the Upload.
    * Fires when the files are cleared by clicking the Pause button. The button is visible if chunksize is set.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function pause($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pause', $value);
    }

    /**
    * Sets the progress event of the Upload.
    * Fires when the data about the progress of the upload is available.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function progress($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('progress', $value);
    }

    /**
    * Sets the resume event of the Upload.
    * Fires when the files are resumed through clicking the Resume button. The button is visible if chunksize is set and the file upload is paused.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function resume($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('resume', $value);
    }

    /**
    * Sets the remove event of the Upload.
    * Fires when an uploaded file is about to be removed. If the event is canceled, the remove operation is prevented.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the select event of the Upload.
    * Fires when a file is selected.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the success event of the Upload.
    * Fires when an upload or remove operation is completed successfully.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function success($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('success', $value);
    }

    /**
    * Sets the upload event of the Upload.
    * Fires when one or more files are about to be uploaded. The canceling of the event prevents the upload.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Upload
    */
    public function upload($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('upload', $value);
    }


//<< Properties
}

?>
