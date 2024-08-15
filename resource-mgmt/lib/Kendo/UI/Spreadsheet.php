<?php

namespace Kendo\UI;

class Spreadsheet extends \Kendo\UI\Widget {
    public function name() {
        return 'Spreadsheet';
    }

    /**
    * An object containing any images used in the Spreadsheet.  The keys should be image ID-s (they are referenced by this ID in (sheets.drawings)[#configuration-sheets.drawings]) and the values should be image URLs.The image URLs can be eitherdata URLs, in which case the images are fully contained by the JSON, or can be external URLs.Note that when external URLs are used, they should reside on the same domain, or the server must be configured with the properCORS headers, for the Spreadsheet to be able to fetch binary image data using a XMLHttpRequest.  If it cannot fetch the image, export to Excel or PDF might not work.
    * @param  $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function images($value) {
        return $this->setProperty('images', $value);
    }

//>> Properties

    /**
    * The name of the currently active sheet. Must exactly match one of the (sheet names)[#configuration-sheets.name].
    * @param string $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function activeSheet($value) {
        return $this->setProperty('activeSheet', $value);
    }

    /**
    * The default column width in pixels.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function columnWidth($value) {
        return $this->setProperty('columnWidth', $value);
    }

    /**
    * The number of columns in the document.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function columns($value) {
        return $this->setProperty('columns', $value);
    }

    /**
    * The default cell styles that will be applied to the sheet cells.
    * @param \Kendo\UI\SpreadsheetDefaultCellStyle|array $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function defaultCellStyle($value) {
        return $this->setProperty('defaultCellStyle', $value);
    }

    /**
    * The height of the header row in pixels.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function headerHeight($value) {
        return $this->setProperty('headerHeight', $value);
    }

    /**
    * The width of the header column in pixels.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function headerWidth($value) {
        return $this->setProperty('headerWidth', $value);
    }

    /**
    * Configures the Excel export settings of the Spreadsheet.
    * @param \Kendo\UI\SpreadsheetExcel|array $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function excel($value) {
        return $this->setProperty('excel', $value);
    }

    /**
    * Configures the PDF export settings of the Spreadsheet.
    * @param \Kendo\UI\SpreadsheetPdf|array $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * The default row height in pixels.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function rowHeight($value) {
        return $this->setProperty('rowHeight', $value);
    }

    /**
    * The number of rows in the document.
    * @param float $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function rows($value) {
        return $this->setProperty('rows', $value);
    }

    /**
    * Adds SpreadsheetSheet to the Spreadsheet.
    * @param \Kendo\UI\SpreadsheetSheet|array,... $value one or more SpreadsheetSheet to add.
    * @return \Kendo\UI\Spreadsheet
    */
    public function addSheet($value) {
        return $this->add('sheets', func_get_args());
    }

    /**
    * A Boolean value which indicates if the sheets-bar will be displayed.
    * @param boolean $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function sheetsbar($value) {
        return $this->setProperty('sheetsbar', $value);
    }

    /**
    * A Boolean value which indicates if the toolbar will be displayed.
    * @param boolean|\Kendo\UI\SpreadsheetToolbar|array $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function toolbar($value) {
        return $this->setProperty('toolbar', $value);
    }

    /**
    * If set to true, the Spreadsheet formula parser will obey the decimal separator of the current culture.  If set to false (default), the decimal separator in formulas will always be the dot.This flag has implications on how formulas are entered. When it is set to true, in cultures where the decimal separator is the comma (,), similar to Excel, the following additional changes upon entering a formula will occur: The semicolon will become a function argument separator. For example, =SUM(A1;A2) instead of =SUM(A1,A2). or The backslash will become an element separator in an array formula. For example, ={1;3} instead of ={1,2;3,4}.. This flag only affects the presentation - the way formulas are entered by the end user or displayed on screen. Serialization to JSON or XLSX as well as the public API functions will continue to use the dot as decimal separator and the comma as an argument separator (canonical form). For example, to apply a formula by using the API, even if useCultureDecimals is in effect, you still need to use the canonical form.To make the API functions obey useCultureDecimals, wrap your code in a call to sheet.withCultureDecimals. Assuming a culture where the comma is used for decimals, compare the previous example with the following one.
    * @param boolean $value
    * @return \Kendo\UI\Spreadsheet
    */
    public function useCultureDecimals($value) {
        return $this->setProperty('useCultureDecimals', $value);
    }

    /**
    * Sets the insertSheet event of the Spreadsheet.
    * Triggered when a sheet is inserted. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function insertSheet($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('insertSheet', $value);
    }

    /**
    * Sets the removeSheet event of the Spreadsheet.
    * Triggered when a sheet will be removed. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function removeSheet($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('removeSheet', $value);
    }

    /**
    * Sets the renameSheet event of the Spreadsheet.
    * Triggered when a sheet will be renamed. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function renameSheet($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('renameSheet', $value);
    }

    /**
    * Sets the selectSheet event of the Spreadsheet.
    * Triggered when a sheet will be activated. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function selectSheet($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('selectSheet', $value);
    }

    /**
    * Sets the unhideColumn event of the Spreadsheet.
    * Triggered when a column will be shown. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function unhideColumn($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('unhideColumn', $value);
    }

    /**
    * Sets the unhideRow event of the Spreadsheet.
    * Triggered when a row will be shown. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function unhideRow($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('unhideRow', $value);
    }

    /**
    * Sets the hideColumn event of the Spreadsheet.
    * Triggered when a column will be hidden. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function hideColumn($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('hideColumn', $value);
    }

    /**
    * Sets the hideRow event of the Spreadsheet.
    * Triggered when a row will be hidden. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function hideRow($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('hideRow', $value);
    }

    /**
    * Sets the deleteColumn event of the Spreadsheet.
    * Triggered when a column will be deleted. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function deleteColumn($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('deleteColumn', $value);
    }

    /**
    * Sets the deleteRow event of the Spreadsheet.
    * Triggered when a row will be deleted. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function deleteRow($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('deleteRow', $value);
    }

    /**
    * Sets the insertColumn event of the Spreadsheet.
    * Triggered when a column will be inserted. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function insertColumn($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('insertColumn', $value);
    }

    /**
    * Sets the insertRow event of the Spreadsheet.
    * Triggered when a row will be inserted. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function insertRow($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('insertRow', $value);
    }

    /**
    * Sets the select event of the Spreadsheet.
    * Triggered when the Spreadsheet selection is changed. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the changeFormat event of the Spreadsheet.
    * Triggered when the range format is changed from the UI. Introduced in the 2017 Q1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function changeFormat($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('changeFormat', $value);
    }

    /**
    * Sets the changing event of the Spreadsheet.
    * Triggered when a value or validation in the Spreadsheet is about to be changed upon user interaction.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function changing($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('changing', $value);
    }

    /**
    * Sets the change event of the Spreadsheet.
    * Triggered when a value in the Spreadsheet has been changed. Introduced in the 2016.Q1.SP1 release.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the render event of the Spreadsheet.
    * Triggered after the widget has completed rendering. The event will also fire when a cell is selected or when the Spreadsheet's tools (bold, italic) are used, as the target element is re-generated with new styles (e.g background-color, box-shadow, font-weight, etc.).
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function renderEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('render', $value);
    }

    /**
    * Sets the excelExport event of the Spreadsheet.
    * Fires when the user clicks the Export to Excel toolbar button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function excelExport($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('excelExport', $value);
    }

    /**
    * Sets the excelImport event of the Spreadsheet.
    * Fired when the user clicks the Open toolbar button.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function excelImport($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('excelImport', $value);
    }

    /**
    * Sets the pdfExport event of the Spreadsheet.
    * Fired when the user initiates the export to PDF.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function pdfExport($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pdfExport', $value);
    }

    /**
    * Sets the copy event of the Spreadsheet.
    * Fired when a range of a sheet is about to be copied.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function copy($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('copy', $value);
    }

    /**
    * Sets the cut event of the Spreadsheet.
    * Fired when a range of a sheet is about to be cut.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function cut($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cut', $value);
    }

    /**
    * Sets the paste event of the Spreadsheet.
    * Fired when a data is about to be pasted in a sheet.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function paste($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('paste', $value);
    }

    /**
    * Sets the dataBinding event of the Spreadsheet.
    * Fired when the data retrieved from a DataSource is about to be bound to a sheet. Available only if DataSource has been defined for at least one sheet.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function dataBinding($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBinding', $value);
    }

    /**
    * Sets the dataBound event of the Spreadsheet.
    * Fired when the data from a DataSource is already populated in a sheet. Available only if DataSource has been defined for at least one sheet.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\Spreadsheet
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }


//<< Properties
}

?>
