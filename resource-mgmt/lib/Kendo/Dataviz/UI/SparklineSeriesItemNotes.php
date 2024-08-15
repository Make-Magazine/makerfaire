<?php

namespace Kendo\Dataviz\UI;

class SparklineSeriesItemNotes extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The position of the series note. "top" - The note is positioned on the top.; "bottom" - The note is positioned on the bottom.; "left" - The note is positioned on the left. or "right" - The note is positioned on the right..
    * @param string $value
    * @return \Kendo\Dataviz\UI\SparklineSeriesItemNotes
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * The icon of the notes.
    * @param \Kendo\Dataviz\UI\SparklineSeriesItemNotesIcon|array $value
    * @return \Kendo\Dataviz\UI\SparklineSeriesItemNotes
    */
    public function icon($value) {
        return $this->setProperty('icon', $value);
    }

    /**
    * The label of the notes.
    * @param \Kendo\Dataviz\UI\SparklineSeriesItemNotesLabel|array $value
    * @return \Kendo\Dataviz\UI\SparklineSeriesItemNotes
    */
    public function label($value) {
        return $this->setProperty('label', $value);
    }

    /**
    * The line of the notes.
    * @param \Kendo\Dataviz\UI\SparklineSeriesItemNotesLine|array $value
    * @return \Kendo\Dataviz\UI\SparklineSeriesItemNotes
    */
    public function line($value) {
        return $this->setProperty('line', $value);
    }

//<< Properties
}

?>
