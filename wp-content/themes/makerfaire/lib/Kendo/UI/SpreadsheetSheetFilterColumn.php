<?php

namespace Kendo\UI;

class SpreadsheetSheetFilterColumn extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds SpreadsheetSheetFilterColumnCriteriaItem to the SpreadsheetSheetFilterColumn.
    * @param \Kendo\UI\SpreadsheetSheetFilterColumnCriteriaItem|array,... $value one or more SpreadsheetSheetFilterColumnCriteriaItem to add.
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function addCriteriaItem($value) {
        return $this->add('criteria', func_get_args());
    }

    /**
    * The filter that will apply to this column.The supported filters are: value - Filters based on unique values.; custom - Applies custom filtering criteria.; top - Filters the top or bottom records. or dynamic - Filters based on dynamic criteria..
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function filter($value) {
        return $this->setProperty('filter', $value);
    }

    /**
    * The index of the column relative to the filter range.
    * @param float $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function index($value) {
        return $this->setProperty('index', $value);
    }

    /**
    * The logical operator that will apply to filter criteria.The supported values are: and or or.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function logic($value) {
        return $this->setProperty('logic', $value);
    }

    /**
    * The filter sub-type, if any.The applicable types according to the main filter are: toptopNumbertopPercentbottomNumberbottomPercent or dynamicaboveAveragebelowAveragetomorrowtodayyesterdaynextWeekthisWeeklastWeeknextMonththisMonthlastMonthnextQuarterthisQuarterlastQuarternextYearthisYearlastYearyearToDate.
    * @param string $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * The filter value for filters that require a single value, for example, top.
    * @param float|string|date $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * The filter values for filters that support multiple values.
    * @param array $value
    * @return \Kendo\UI\SpreadsheetSheetFilterColumn
    */
    public function values($value) {
        return $this->setProperty('values', $value);
    }

//<< Properties
}

?>
