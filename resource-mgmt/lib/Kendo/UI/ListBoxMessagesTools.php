<?php

namespace Kendo\UI;

class ListBoxMessagesTools extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Defines the text of the Move Down button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function moveDown($value) {
        return $this->setProperty('moveDown', $value);
    }

    /**
    * Defines the text of the Move Up button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function moveUp($value) {
        return $this->setProperty('moveUp', $value);
    }

    /**
    * Defines the text of the Delete button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function remove($value) {
        return $this->setProperty('remove', $value);
    }

    /**
    * Defines the text of the Transfer All From button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function transferAllFrom($value) {
        return $this->setProperty('transferAllFrom', $value);
    }

    /**
    * Defines the text of the Transfer All To button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function transferAllTo($value) {
        return $this->setProperty('transferAllTo', $value);
    }

    /**
    * Defines the text of the Transfer From button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function transferFrom($value) {
        return $this->setProperty('transferFrom', $value);
    }

    /**
    * Defines the text of the Transfer To button that is located in the toolbar of the ListBox.
    * @param string $value
    * @return \Kendo\UI\ListBoxMessagesTools
    */
    public function transferTo($value) {
        return $this->setProperty('transferTo', $value);
    }

//<< Properties
}

?>
