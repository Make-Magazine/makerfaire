<?php

namespace Kendo\Dataviz\UI;

class DiagramPdf extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The author of the PDF document that will be visible in the PDF file metadata.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function author($value) {
        return $this->setProperty('author', $value);
    }

    /**
    * The creator of the PDF document.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function creator($value) {
        return $this->setProperty('creator', $value);
    }

    /**
    * The date when the PDF document is created. Defaults to new Date().
    * @param date $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function date($value) {
        return $this->setProperty('date', $value);
    }

    /**
    * Specifies the file name of the exported PDF file.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function fileName($value) {
        return $this->setProperty('fileName', $value);
    }

    /**
    * If set to true, the exported content is forwarded to the endpoint specified using the proxyURL setting, even if the browser supports saving of files in the user's local file system.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function forceProxy($value) {
        return $this->setProperty('forceProxy', $value);
    }

    /**
    * Specifies the keywords that will appear in the metadata of the exported PDF file.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function keywords($value) {
        return $this->setProperty('keywords', $value);
    }

    /**
    * If set to true, changes the document orientation to landscape. This property takes effect when pdf.paperSize is set.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function landscape($value) {
        return $this->setProperty('landscape', $value);
    }

    /**
    * Sets the paper margins. The value has to be an object containing top, left, right, and bottom numbers which specify the paper margins. If numbers are passed, they are assumed to be in points (pt). By using strings, you can specify the units. The supported units are mm, cm, in, and pt (default).
    * @param \Kendo\Dataviz\UI\DiagramPdfMargin|array $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function margin($value) {
        return $this->setProperty('margin', $value);
    }

    /**
    * Specifies the paper size of the PDF document. The default setting is auto and determines the paper size by content.The supported values are: A predefined size:  A0-A10, B0-B10, C0-C10, Executive, Folio, Legal, Letter, Tabloid.; An array of two numbers specifying the width and height in points (1pt = 1/72in) or An array of two strings specifying the width and height in units. The supported units are mm, cm, in, and pt..
    * @param string|array $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function paperSize($value) {
        return $this->setProperty('paperSize', $value);
    }

    /**
    * The URL of the server side proxy which will stream the file to the end user.When the browser is not capable of saving files locally, a proxy will be used. Such browsers are Internet Explorer version 9 (and older) and Safari. You are responsible for implementing the server-side proxy. The proxy receives a POST request with the following parameters in the request body: contentType - The MIME type of the file; base64 - The base-64 encoded file content or fileName - The file name, as requested by the caller.. The proxy is expected to return the decoded file with a set Content-Disposition header.You can see sample server proxy implementations here: Server Proxy Implementations.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function proxyURL($value) {
        return $this->setProperty('proxyURL', $value);
    }

    /**
    * A name or keyword indicating where to display the document returned from the proxy.If you want to display the document in a new window or an iframe, the proxy method has to set the Content-Disposition header of the response to inline; filename="<fileName.pdf>".
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function proxyTarget($value) {
        return $this->setProperty('proxyTarget', $value);
    }

    /**
    * Sets the subject of document, which will be shown in the PDF file metadata.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function subject($value) {
        return $this->setProperty('subject', $value);
    }

    /**
    * Sets the title of the document, which will be shown in the PDF file metadata.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramPdf
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

//<< Properties
}

?>
