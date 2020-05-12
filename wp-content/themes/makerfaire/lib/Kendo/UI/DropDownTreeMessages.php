<?php

namespace Kendo\UI;

class DropDownTreeMessages extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The text message when hovering the clear button.
    * @param string $value
    * @return \Kendo\UI\DropDownTreeMessages
    */
    public function clear($value) {
        return $this->setProperty('clear', $value);
    }

    /**
    * The text message shown when hovering delete icon in a selected tag.
    * @param string $value
    * @return \Kendo\UI\DropDownTreeMessages
    */
    public function deleteTag($value) {
        return $this->setProperty('deleteTag', $value);
    }

    /**
    * The text message shown in the noDataTemplate when no data is available in the widget drop-down.
    * @param string $value
    * @return \Kendo\UI\DropDownTreeMessages
    */
    public function noData($value) {
        return $this->setProperty('noData', $value);
    }

    /**
    * The text message shown in the single TagMode tag.
    * @param string $value
    * @return \Kendo\UI\DropDownTreeMessages
    */
    public function singleTag($value) {
        return $this->setProperty('singleTag', $value);
    }

//<< Properties
}

?>
