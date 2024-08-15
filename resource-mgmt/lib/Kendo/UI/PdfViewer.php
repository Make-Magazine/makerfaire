<?php

namespace Kendo\UI;

class PDFViewer extends \Kendo\UI\Widget {
    public function name() {
        return 'PDFViewer';
    }
//>> Properties

    /**
    * Specifies the PDF.JS configuration options. Including pdfjs is mandatory.
    * @param \Kendo\UI\PDFViewerPdfjsProcessing|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function pdfjsProcessing($value) {
        return $this->setProperty('pdfjsProcessing', $value);
    }

    /**
    * Specifies the DPL configuration options.
    * @param \Kendo\UI\PDFViewerDplProcessing|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function dplProcessing($value) {
        return $this->setProperty('dplProcessing', $value);
    }

    /**
    * The width of the PDFViewer.
    * @param float|string $value
    * @return \Kendo\UI\PDFViewer
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The height of the PDFViewer.
    * @param float|string $value
    * @return \Kendo\UI\PDFViewer
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * Specifies the default page size if no PDF is displayed in the PDFViewer. The page size will shrink to fit the viewer dimensions.
    * @param \Kendo\UI\PDFViewerDefaultPageSize|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function defaultPageSize($value) {
        return $this->setProperty('defaultPageSize', $value);
    }

    /**
    * The selected page number in the viewer.
    * @param float $value
    * @return \Kendo\UI\PDFViewer
    */
    public function page($value) {
        return $this->setProperty('page', $value);
    }

    /**
    * Specifies the default scale of the pages.
    * @param float $value
    * @return \Kendo\UI\PDFViewer
    */
    public function scale($value) {
        return $this->setProperty('scale', $value);
    }

    /**
    * Specifies the minimum zoom that could be applied to the pages.
    * @param float $value
    * @return \Kendo\UI\PDFViewer
    */
    public function zoomMin($value) {
        return $this->setProperty('zoomMin', $value);
    }

    /**
    * Specifies the maximum zoom that could be applied to the pages.
    * @param float $value
    * @return \Kendo\UI\PDFViewer
    */
    public function zoomMax($value) {
        return $this->setProperty('zoomMax', $value);
    }

    /**
    * Specifies the zoom rate that could be applied to the pages. Used when zooming on mousewheel and for the zoomIn and zoomOut tools.
    * @param float $value
    * @return \Kendo\UI\PDFViewer
    */
    public function zoomRate($value) {
        return $this->setProperty('zoomRate', $value);
    }

    /**
    * Defines the page surface options. This setting is available only for DPL Processing. The page render a drawing Surface and all of its configuration options could be defined.
    * @param \Kendo\UI\PDFViewerView|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function view($value) {
        return $this->setProperty('view', $value);
    }

    /**
    * Toolbar option accepts a Boolean value which indicates if the toolbar will be displayed or an Object with items. Inherits Kendo UI Toolbar.
    * @param boolean|\Kendo\UI\PDFViewerToolbar|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * Specifies the localization messages of the PDFViewer.
    * @param \Kendo\UI\PDFViewerMessages|array $value
    * @return \Kendo\UI\PDFViewer
    */
    public function messages($value) {
        return $this->setProperty('messages', $value);
    }

    /**
    * Sets the document option of the PDFViewer.
    * The client-side global variable or function to provide the JSON data for the PDF document.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewer
    */
    public function document($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('document', $value);
    }

    /**
    * Sets the render event of the PDFViewer.
    * Fires when a page is rendered
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewer
    */
    public function renderEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('render', $value);
    }

    /**
    * Sets the open event of the PDFViewer.
    * Fires when a PDF is opened in the viewer.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewer
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the error event of the PDFViewer.
    * Fires when an error is encountered. By default, a dialog is shown with error message. The dialog will not be shown if the event is prevented.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\PDFViewer
    */
    public function error($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('error', $value);
    }


//<< Properties
}

?>
