<?php

namespace Kendo\UI;

class SpreadsheetSheetRowCellValidation extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the validation type.The supported options are: reject or warning (default).
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * Defines the comparer type that is used to validate the cell value.The supported values are: greaterThan; lessThan; between; equalTo; notEqualTo; greaterThanOrEqualTo; lessThanOrEqualTo; notBetween or custom.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function comparerType($value) {
        return $this->setProperty('comparerType', $value);
    }

    /**
    * Defines the data type of the cell value.The supported values are: date; text; number; list or custom.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function dataType($value) {
        return $this->setProperty('dataType', $value);
    }

    /**
    * Defines a formula or a value that is used for the comparison process. Used as the only compare value if the comparer type does not require a second argument. Mandatory for validation to work.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function from($value) {
        return $this->setProperty('from', $value);
    }

    /**
    * A Boolean value which indicates if a button for selecting list items will be displayed (dataType set to list).
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function showButton($value) {
        return $this->setProperty('showButton', $value);
    }

    /**
    * Defines a formula or a value that is used for the comparison process. Will be used if the comparer type requires a second argument.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function to($value) {
        return $this->setProperty('to', $value);
    }

    /**
    * Specifies whether to allow null values.
    * @param boolean $value
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function allowNulls($value) {
        return $this->setProperty('allowNulls', $value);
    }

    /**
    * Sets the messageTemplate option of the SpreadsheetSheetRowCellValidation.
    * Defines the hint message that will be displayed if the value is invalid.The template provides access to the following variables: from{0}; to{1}; fromFormula{2}; toFormula{3}; dataType{4}; type{5} or comparerType{6}.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function messageTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('messageTemplate', $value);
    }

    /**
    * Sets the messageTemplate option of the SpreadsheetSheetRowCellValidation.
    * Defines the hint message that will be displayed if the value is invalid.The template provides access to the following variables: from{0}; to{1}; fromFormula{2}; toFormula{3}; dataType{4}; type{5} or comparerType{6}.
    * @param string $value The template content.
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function messageTemplate($value) {
        return $this->setProperty('messageTemplate', $value);
    }

    /**
    * Sets the titleTemplate option of the SpreadsheetSheetRowCellValidation.
    * Defines the hint title that will be displayed if the value is invalid.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function titleTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('titleTemplate', $value);
    }

    /**
    * Sets the titleTemplate option of the SpreadsheetSheetRowCellValidation.
    * Defines the hint title that will be displayed if the value is invalid.
    * @param string $value The template content.
    * @return \Kendo\UI\SpreadsheetSheetRowCellValidation
    */
    public function titleTemplate($value) {
        return $this->setProperty('titleTemplate', $value);
    }

//<< Properties
}

?>
