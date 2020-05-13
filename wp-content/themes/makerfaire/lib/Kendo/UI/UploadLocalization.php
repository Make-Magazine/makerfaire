<?php

namespace Kendo\UI;

class UploadLocalization extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Sets the text of the Cancel button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function cancel($value) {
        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the text of the Clear button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function clearSelectedFiles($value) {
        return $this->setProperty('clearSelectedFiles', $value);
    }

    /**
    * Sets the hint of the drop-zone.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function dropFilesHere($value) {
        return $this->setProperty('dropFilesHere', $value);
    }

    /**
    * Sets the status message of the header for the uploaded files.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function headerStatusUploaded($value) {
        return $this->setProperty('headerStatusUploaded', $value);
    }

    /**
    * Sets the status message of the header for the files that are in the process of upload.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function headerStatusUploading($value) {
        return $this->setProperty('headerStatusUploading', $value);
    }

    /**
    * Sets the text of the validation message for an invalid file extension.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function invalidFileExtension($value) {
        return $this->setProperty('invalidFileExtension', $value);
    }

    /**
    * Sets the text of the validation messages for invalid files when the batch property is set to true and two or more files fail the validation.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function invalidFiles($value) {
        return $this->setProperty('invalidFiles', $value);
    }

    /**
    * Sets the text of the validation message for an invalid maximum file size.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function invalidMaxFileSize($value) {
        return $this->setProperty('invalidMaxFileSize', $value);
    }

    /**
    * Sets the text of the validation message for an invalid minimum file size.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function invalidMinFileSize($value) {
        return $this->setProperty('invalidMinFileSize', $value);
    }

    /**
    * Sets the text of the Remove button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function remove($value) {
        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the text of the Retry button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function retry($value) {
        return $this->setProperty('retry', $value);
    }

    /**
    * Sets the text of the Select... button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function select($value) {
        return $this->setProperty('select', $value);
    }

    /**
    * Sets the status message for failed uploads.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function statusFailed($value) {
        return $this->setProperty('statusFailed', $value);
    }

    /**
    * Sets the status message for successful uploads.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function statusUploaded($value) {
        return $this->setProperty('statusUploaded', $value);
    }

    /**
    * Sets the status message for files that are in the process of upload.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function statusUploading($value) {
        return $this->setProperty('statusUploading', $value);
    }

    /**
    * Sets the text of the Upload files button.
    * @param string $value
    * @return \Kendo\UI\UploadLocalization
    */
    public function uploadSelectedFiles($value) {
        return $this->setProperty('uploadSelectedFiles', $value);
    }

//<< Properties
}

?>
