<?php

namespace Kendo\UI;

class SpreadsheetToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * A Boolean value which indicates if the Home tab or a collection of tools that will be shown in the Home tab will be displayed.The following list indicates the available tools. The tools which are part of a tool group are defined as an array. For example ["bold", "italic", "underline"]. open; exportAs; [cut, copy, paste]; [bold, italic, underline]; backgroundColor, textColor; borders; fontSize, fontFamily; alignment; textWrap; [formatDecreaseDecimal, formatIncreaseDecimal]; format; merge; freeze or filter.
    * @param boolean|array $value
    * @return \Kendo\UI\SpreadsheetToolbar
    */
    public function home($value) {
        return $this->setProperty('home', $value);
    }

    /**
    * A Boolean value which indicates if the Insert tab or a collection of tools that will be shown in the Insert tab will be displayed.The following list indicates the available tools. The tools which are part of a tool group are defined as an array. For example ["deleteColumn", "deleteRow"]. [ addColumnLeft, addColumnRight, addRowBelow, addRowAbove ] or [ deleteColumn, deleteRow ].
    * @param boolean|array $value
    * @return \Kendo\UI\SpreadsheetToolbar
    */
    public function insert($value) {
        return $this->setProperty('insert', $value);
    }

    /**
    * A Boolean value which indicates if the Data tab or a collection of tools that will be shown in the Data tab will be displayed.The available tools are: sort; filter or validation.
    * @param boolean|array $value
    * @return \Kendo\UI\SpreadsheetToolbar
    */
    public function data($value) {
        return $this->setProperty('data', $value);
    }

//<< Properties
}

?>
