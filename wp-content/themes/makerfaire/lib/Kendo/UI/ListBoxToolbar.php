<?php

namespace Kendo\UI;

class ListBoxToolbar extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The relative position of the ListBox element at which the toolbar will be displayed. The possible values are "left", "right", "top", and "bottom".
    * @param string $value
    * @return \Kendo\UI\ListBoxToolbar
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * A collection of tools that are used to interact with the ListBox.The built-in tools are: - "moveUp" - Moves up the selected ListBox items. - "moveDown" - Moves down the selected ListBox items. - "remove" - Removes the selected ListBox items. - "transferTo" - Moves the selected items from the current ListBox to the target that is defined in the connectWith option. - "transferFrom" - Moves the selected items from the ListBox that is defined in the connectWith option to the current ListBox. - "transferAllTo" - Moves all items from the current ListBox to the target that is defined in the connectWith option. - "transferAllFrom" - Moves all items from the ListBox that is defined in the connectWith option to the current ListBox.
    * @param array $value
    * @return \Kendo\UI\ListBoxToolbar
    */
    public function tools($value) {
        return $this->setProperty('tools', $value);
    }

//<< Properties
}

?>
