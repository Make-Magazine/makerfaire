<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesDefaultsNotes extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The icon of the notes.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesIcon|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotes
    */
    public function icon($value) {
        return $this->setProperty('icon', $value);
    }

    /**
    * The label of the notes.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLabel|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotes
    */
    public function label($value) {
        return $this->setProperty('label', $value);
    }

    /**
    * The line of the notes.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsNotesLine|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotes
    */
    public function line($value) {
        return $this->setProperty('line', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesDefaultsNotes.
    * A function that can be used to create a custom visual for the notes. The available argument fields are: rect - the kendo.geometry.Rect that defines the note target rect.; options - the note options.; createVisual - a function that can be used to get the default visual.; category - the category of the note point.; dataItem - the dataItem of the note point.; value - the value of the note point.; sender - the chart instance.; series - the series of the note point. or text - the note text..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsNotes
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

//<< Properties
}

?>
