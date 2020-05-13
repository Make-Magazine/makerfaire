<?php

namespace Kendo\UI;

class UploadAsync extends \Kendo\SerializableObject {
//>> Properties

    /**
    * By default, the selected files are uploaded immediately. To change this behavior, set autoUpload to false.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function autoUpload($value) {
        return $this->setProperty('autoUpload', $value);
    }

    /**
    * By default and if supported by the browser, the selected files are uploaded in separate requests. To change this behavior, set batch to true. As a result, all selected files are uploaded in one request.The batch mode applies to multiple files which are selected simultaneously. Files that are selected one after the other are uploaded in separate requests.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function batch($value) {
        return $this->setProperty('batch', $value);
    }

    /**
    * When async.chunkSize is set, the selected files are uploaded chunk by chunk with the declared size. Each request sends a separate file blob and additional string metadata to the server. This metadata is in a stringified JSON format and contains the fileName, relativePath, chunkIndex, contentType, totalFileSize, totalChunks, and uploadUid properties. These properties enable the validation and combination of the file on the server side. The response also returns a JSON object with the uploaded and fileUid properties, which notifies the client what the next chunk is.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function chunkSize($value) {
        return $this->setProperty('chunkSize', $value);
    }

    /**
    * By default, the selected files are uploaded one after the other. When async.concurrent is set to true, all selected files start to upload simultaneously.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function concurrent($value) {
        return $this->setProperty('concurrent', $value);
    }

    /**
    * If async.autoRetryAfter is set, the failed upload request is repeated after the declared amount of time in miliseconds.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function autoRetryAfter($value) {
        return $this->setProperty('autoRetryAfter', $value);
    }

    /**
    * Sets the maximum number of attempts that are performed if an upload fails.
    * @param float $value
    * @return \Kendo\UI\UploadAsync
    */
    public function maxAutoRetries($value) {
        return $this->setProperty('maxAutoRetries', $value);
    }

    /**
    * The name of the form field that is submitted to removeUrl.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeField($value) {
        return $this->setProperty('removeField', $value);
    }

    /**
    * The URL of the handler which is responsible for the removal of the uploaded files (if any). The handler must accept POST requests with one or more "fileNames" fields which specify the files that will be deleted.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeUrl($value) {
        return $this->setProperty('removeUrl', $value);
    }

    /**
    * The HTTP verb that will be used by the remove action.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function removeVerb($value) {
        return $this->setProperty('removeVerb', $value);
    }

    /**
    * The name of the form field which is submitted to saveUrl. The default value is the input name.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function saveField($value) {
        return $this->setProperty('saveField', $value);
    }

    /**
    * The URL of the handler that will receive the submitted files. The handler must accept POST requests which contain one or more fields with the same name as the original input name.
    * @param string $value
    * @return \Kendo\UI\UploadAsync
    */
    public function saveUrl($value) {
        return $this->setProperty('saveUrl', $value);
    }

    /**
    * By default, the files are uploaded as file data. When set to true, the files are read as a file buffer by using FileReader. This buffer is sent in the request body.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function useArrayBuffer($value) {
        return $this->setProperty('useArrayBuffer', $value);
    }

    /**
    * Controls whether to send credentials (cookies, headers) for cross-site requests.
    * @param boolean $value
    * @return \Kendo\UI\UploadAsync
    */
    public function withCredentials($value) {
        return $this->setProperty('withCredentials', $value);
    }

//<< Properties
}

?>
