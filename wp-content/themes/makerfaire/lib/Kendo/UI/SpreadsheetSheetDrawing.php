<?php

namespace Kendo\UI;

class SpreadsheetSheetDrawing extends \Kendo\SerializableObject {
//>> Properties

    /**
    * A cell to which the drawing's top-left corner is anchored.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function topLeftCell($value) {
        return $this->setProperty('topLeftCell', $value);
    }

    /**
    * The horizontal offset from the anchor cell's top-left corner, in pixels.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function offsetX($value) {
        return $this->setProperty('offsetX', $value);
    }

    /**
    * The vertical offset from the anchor cell's top-left corner, in pixels.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function offsetY($value) {
        return $this->setProperty('offsetY', $value);
    }

    /**
    * The drawing's width in pixels.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The drawing's height in pixels.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * The ID of the image to display.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetDrawing
    */
    public function image($value) {
        return $this->setProperty('image', $value);
    }

//<< Properties
}

?>
