<?php

namespace Kendo\UI;

class SpreadsheetPdf extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The area that will be exported.The supported values are: * workbook - Exports the full workbook, including all sheets. * sheet - Exports the active sheet. * selection - Exports the selected area on the active sheet.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function area($value) {
        return $this->setProperty('area', $value);
    }

    /**
    * The author of the PDF document.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function author($value) {
        return $this->setProperty('author', $value);
    }

    /**
    * The creator of the PDF document.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function creator($value) {
        return $this->setProperty('creator', $value);
    }

    /**
    * The date when the PDF document is created. Defaults to new Date().
    * @param date $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function date($value) {
        return $this->setProperty('date', $value);
    }

    /**
    * Specifies the file name of the exported PDF file.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function fileName($value) {
        return $this->setProperty('fileName', $value);
    }

    /**
    * Indicates whether to fit the content of the Spreadsheet to the width of the page.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function fitWidth($value) {
        return $this->setProperty('fitWidth', $value);
    }

    /**
    * If set to true, the content will be forwarded to proxyURL even if the browser supports the saving of files locally.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function forceProxy($value) {
        return $this->setProperty('forceProxy', $value);
    }

    /**
    * Indicates whether to export the cell guidelines.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function guidelines($value) {
        return $this->setProperty('guidelines', $value);
    }

    /**
    * Indicates whether to center the content horizontally. For more information, refer to vCenter.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function hCenter($value) {
        return $this->setProperty('hCenter', $value);
    }

    /**
    * Specifies the keywords of the exported PDF file.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function keywords($value) {
        return $this->setProperty('keywords', $value);
    }

    /**
    * If set to true, reverses the paper dimensions if that width is needed as the larger edge.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function landscape($value) {
        return $this->setProperty('landscape', $value);
    }

    /**
    * Specifies the margins of the page (numbers or strings with units).The supported values are: mm; cm; in or pt (default).
    * @param \Kendo\UI\SpreadsheetPdfMargin|array $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function margin($value) {
        return $this->setProperty('margin', $value);
    }

    /**
    * Specifies the paper size of the PDF document. The default auto setting means that the paper size is determined by the content.The supported values are: * A predefined size - A4, A3, and so on. * An array of two numbers which specify the width and height in points (1pt = 1/72in). * An array of two strings which specify the width and height in units. The supported values are mm, cm, in, and pt.
    * @param string|array $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function paperSize($value) {
        return $this->setProperty('paperSize', $value);
    }

    /**
    * The URL of the server side proxy which will stream the file to the end user. A proxy will be used when the browser is not capable of saving files locally, for example, Internet Explorer 9 and Safari. The developer is responsible for implementing the server-side proxy. The proxy will return the decoded file with the Content-Disposition header set to attachment; filename="<fileName.pdf>".The proxy will receive a POST request with the following parameters in the request body: contentType - The MIME type of the file.; base64 - The base-64 encoded file content. or fileName - The file name as requested by the caller..
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function proxyURL($value) {
        return $this->setProperty('proxyURL', $value);
    }

    /**
    * A name or keyword which indicates where to display the document that is returned from the proxy. To display the document in a new window or iframe, set the proxy Content-Disposition header to inline; filename="<fileName.pdf>".
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function proxyTarget($value) {
        return $this->setProperty('proxyTarget', $value);
    }

    /**
    * Sets the subject of the PDF file.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function subject($value) {
        return $this->setProperty('subject', $value);
    }

    /**
    * Sets the title of the PDF file.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * Indicates whether to center the content vertically. For more information, refer to hCenter.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetPdf
    */
    public function vCenter($value) {
        return $this->setProperty('vCenter', $value);
    }

//<< Properties
}

?>
