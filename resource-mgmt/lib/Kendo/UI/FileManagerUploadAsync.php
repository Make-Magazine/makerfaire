<?php

namespace Kendo\UI;

class FileManagerUploadAsync extends \Kendo\SerializableObject {
//>> Properties

    /**
    * By default, the selected files are uploaded immediately. To change this behavior, set `autoUpload` to `false`.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function autoUpload($value) {
        return $this->setProperty('autoUpload', $value);
    }

    /**
    * All selected files are uploaded in one request.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function batch($value) {
        return $this->setProperty('batch', $value);
    }

    /**
    * When `async.chunkSize` is set, the selected files are uploaded chunk by chunk with the declared size.
    * @param float $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function chunkSize($value) {
        return $this->setProperty('chunkSize', $value);
    }

    /**
    * By default, the selected files are uploaded one after the other. When `async.concurrent` is set to `true`, all selected files start to upload simultaneously.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function concurrent($value) {
        return $this->setProperty('concurrent', $value);
    }

    /**
    * If `async.autoRetryAfter` is set, the failed upload request is repeated after the declared amount of time in miliseconds.
    * @param float $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function autoRetryAfter($value) {
        return $this->setProperty('autoRetryAfter', $value);
    }

    /**
    * Sets the maximum number of attempts that are performed if an upload fails.
    * @param float $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function maxAutoRetries($value) {
        return $this->setProperty('maxAutoRetries', $value);
    }

    /**
    * The name of the form field which is submitted to `saveUrl`. The default value is the input name.
    * @param string $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function saveField($value) {
        return $this->setProperty('saveField', $value);
    }

    /**
    * The URL of the handler that will receive the submitted files. The handler must accept `POST` requests which contain one or more fields with the same name as the original input name.
    * @param string $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function saveUrl($value) {
        return $this->setProperty('saveUrl', $value);
    }

    /**
    * By default, the files are uploaded as file data. When set to `true`, the files are read as a file buffer by using [`FileReader`](https://developer.mozilla.org/en-US/docs/Web/API/FileReader).
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function useArrayBuffer($value) {
        return $this->setProperty('useArrayBuffer', $value);
    }

    /**
    * Controls whether to send credentials (cookies, headers) for cross-site requests.
    * @param boolean $value
    * @return \Kendo\UI\FileManagerUploadAsync
    */
    public function withCredentials($value) {
        return $this->setProperty('withCredentials', $value);
    }

//<< Properties
}

?>
