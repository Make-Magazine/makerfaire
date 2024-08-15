<?php

namespace Kendo\UI;

class SpreadsheetSheetRowCell extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The background color of the cell. Many standard CSS formats are supported. However, the canonical form is #ccff00.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The style information for the bottom border of the cell.
    * @param \Kendo\UI\SpreadsheetSheetRowCellBorderBottom|array $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function borderBottom($value) {
        return $this->setProperty('borderBottom', $value);
    }

    /**
    * The style information for the left border of the cell.
    * @param \Kendo\UI\SpreadsheetSheetRowCellBorderLeft|array $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function borderLeft($value) {
        return $this->setProperty('borderLeft', $value);
    }

    /**
    * The style information for the top border of the cell.
    * @param \Kendo\UI\SpreadsheetSheetRowCellBorderTop|array $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function borderTop($value) {
        return $this->setProperty('borderTop', $value);
    }

    /**
    * The style information for the right border of the cell.
    * @param \Kendo\UI\SpreadsheetSheetRowCellBorderRight|array $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function borderRight($value) {
        return $this->setProperty('borderRight', $value);
    }

    /**
    * The font color of the cell. Many standard CSS formats are supported. However, the canonical form is #ccff00.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The comment of the cell - a tooltip that appears when the cell is hovered.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function comment($value) {
        return $this->setProperty('comment', $value);
    }

    /**
    * The font family of the cell.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function fontFamily($value) {
        return $this->setProperty('fontFamily', $value);
    }

    /**
    * The font size of the cell in pixels.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function fontSize($value) {
        return $this->setProperty('fontSize', $value);
    }

    /**
    * If set to true, sets the cell font to italic.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function italic($value) {
        return $this->setProperty('italic', $value);
    }

    /**
    * If set to true, sets the cell font to bold.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function bold($value) {
        return $this->setProperty('bold', $value);
    }

    /**
    * If set to false, disables the cell.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function enable($value) {
        return $this->setProperty('enable', $value);
    }

    /**
    * The format of the cell text. For more information, refer to the article on creating or deleting a custom number format on MS Office.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The cell formula without the leading equals sign, for example, A1 * 10.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function formula($value) {
        return $this->setProperty('formula', $value);
    }

    /**
    * The zero-based index of the cell. Required to ensure correct positioning.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function index($value) {
        return $this->setProperty('index', $value);
    }

    /**
    * The hyperlink (URL) of the cell.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function link($value) {
        return $this->setProperty('link', $value);
    }

    /**
    * If set to true, sets the cell font to underline.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function underline($value) {
        return $this->setProperty('underline', $value);
    }

    /**
    * The cell value.
    * @param float|string|boolean|date $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * The validation rule that is applied to the cell.
    * @param \Kendo\UI\SpreadsheetSheetRowCellValidation|array $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function validation($value) {
        return $this->setProperty('validation', $value);
    }

    /**
    * If set to true, wraps the cell content.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function wrap($value) {
        return $this->setProperty('wrap', $value);
    }

    /**
    * Specifies the text alignment in the cell
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function textAlign($value) {
        return $this->setProperty('textAlign', $value);
    }

    /**
    * Specifies the text vertical alignment in the cell
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCell
    */
    public function verticalAlign($value) {
        return $this->setProperty('verticalAlign', $value);
    }

//<< Properties
}

?>
