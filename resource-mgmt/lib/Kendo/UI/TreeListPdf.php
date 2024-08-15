<?php

namespace Kendo\UI;

class TreeListPdf extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The author of the PDF document.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function author($value) {
        return $this->setProperty('author', $value);
    }

    /**
    * (Available as of the 2015.3.1020 release) A flag which indicates whether to produce actual hyperlinks in the exported PDF file. You can also pass a CSS selector as an argument. All matching links will be ignored.
    * @param boolean|string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function avoidLinks($value) {
        return $this->setProperty('avoidLinks', $value);
    }

    /**
    * The creator of the PDF document.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function creator($value) {
        return $this->setProperty('creator', $value);
    }

    /**
    * The date when the PDF document is created. Defaults to new Date().
    * @param date $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function date($value) {
        return $this->setProperty('date', $value);
    }

    /**
    * Specifies the file name of the exported PDF file.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function fileName($value) {
        return $this->setProperty('fileName', $value);
    }

    /**
    * If set to true, the content will be forwarded to proxyURL even if the browser supports the local saving of files.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function forceProxy($value) {
        return $this->setProperty('forceProxy', $value);
    }

    /**
    * Specifies the keywords of the exported PDF file.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function keywords($value) {
        return $this->setProperty('keywords', $value);
    }

    /**
    * If set to true, reverses the paper dimensions in such a way that the width becomes the larger edge.
    * @param boolean $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function landscape($value) {
        return $this->setProperty('landscape', $value);
    }

    /**
    * Specifies the margins of the page and accepts numbers or strings with units.The supported units are: mm; cm; in or (Default) pt.
    * @param \Kendo\UI\TreeListPdfMargin|array $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function margin($value) {
        return $this->setProperty('margin', $value);
    }

    /**
    * Specifies the paper size of the PDF document. The default auto setting means that the paper size is determined by the content.The supported values are: A predefined size such as A4, A3, and so on.; An array of two numbers which specify the width and height in points (1pt = 1/72in). or An array of two strings which specify the width and height in units. The supported units are:mmcminpt.
    * @param string|array $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function paperSize($value) {
        return $this->setProperty('paperSize', $value);
    }

    /**
    * The URL of the server side proxy which will stream the file to the end user. A proxy will be used when the browser is not capable of saving files locally. Such browsers are IE version 9 and earlier, and Safari. The developer is responsible for implementing the server-side proxy. The proxy will return the decoded file with the Content-Disposition header set to attachment; filename="<fileName.pdf>".The proxy will receive a POST request with the following parameters in the request body: contentType - The MIME type of the file.; base64 - The base-64 encoded file content. or fileName - The file name, as requested by the caller..
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function proxyURL($value) {
        return $this->setProperty('proxyURL', $value);
    }

    /**
    * A name or keyword whcih indicates where to display the document that was returned by the proxy. To display the document in a new window or iframe, the proxy will set the Content-Disposition header to inline; filename="<fileName.pdf>".
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function proxyTarget($value) {
        return $this->setProperty('proxyTarget', $value);
    }

    /**
    * Sets the subject of the PDF file.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function subject($value) {
        return $this->setProperty('subject', $value);
    }

    /**
    * Sets the title of the PDF file.
    * @param string $value
    * @return \Kendo\UI\TreeListPdf
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

//<< Properties
}

?>
