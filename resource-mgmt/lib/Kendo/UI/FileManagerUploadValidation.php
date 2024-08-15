<?php

namespace Kendo\UI;

class FileManagerUploadValidation extends \Kendo\SerializableObject {
//>> Properties

    /**
    * A list of the file extensions which are allowed for upload. Recognizes entries of both `.type` and `type` values.
    * @param array $value
    * @return \Kendo\UI\FileManagerUploadValidation
    */
    public function allowedExtensions($value) {
        return $this->setProperty('allowedExtensions', $value);
    }

    /**
    * Defines the maximum file size in bytes allowed for upload.
    * @param float $value
    * @return \Kendo\UI\FileManagerUploadValidation
    */
    public function maxFileSize($value) {
        return $this->setProperty('maxFileSize', $value);
    }

    /**
    * Defines the minimum file size in bytes allowed for upload.
    * @param float $value
    * @return \Kendo\UI\FileManagerUploadValidation
    */
    public function minFileSize($value) {
        return $this->setProperty('minFileSize', $value);
    }

//<< Properties
}

?>
