<?php

namespace Kendo\UI;

class FileManagerUpload extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Configures the asynchronous upload of files. For more details, refer to the article on the [async mode](/web/upload/modes#asynchronous-mode) of the Upload.
    * @param \Kendo\UI\FileManagerUploadAsync|array $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function async($value) {
        return $this->setProperty('async', $value);
    }

    /**
    * Enables the selection of folders instead of files.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function directory($value) {
        return $this->setProperty('directory', $value);
    }

    /**
    * Sets the strings rendered by the Upload.
    * @param \Kendo\UI\FileManagerUploadLocalization|array $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function localization($value) {
        return $this->setProperty('localization', $value);
    }

    /**
    * Enables (if set to `true`) or disables (if set to `false`) the selection of multiple files. If set to `false`, the user can select only one file at a time.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function multiple($value) {
        return $this->setProperty('multiple', $value);
    }

    /**
    * Enables (if set to `true`) or disables (if set to `false`) the display of a file listing for the file upload. The disabling of a file listing might be useful if you want to customize the UI. To build your own UI, use the client-side events.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function showFileList($value) {
        return $this->setProperty('showFileList', $value);
    }

    /**
    * Configures the validation options for uploaded files.
    * @param \Kendo\UI\FileManagerUploadValidation|array $value
    * @return \Kendo\UI\FileManagerUpload
    */
    public function validation($value) {
        return $this->setProperty('validation', $value);
    }

//<< Properties
}

?>
