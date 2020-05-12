<?php

namespace Kendo\UI;

class GridColumnCommandItemIconClass extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The class for the web font icon of the cancel command button.
    * @param string $value
    * @return \Kendo\UI\GridColumnCommandItemIconClass
    */
    public function cancel($value) {
        return $this->setProperty('cancel', $value);
    }

    /**
    * The class for the web font icon of the edit command button.
    * @param string $value
    * @return \Kendo\UI\GridColumnCommandItemIconClass
    */
    public function edit($value) {
        return $this->setProperty('edit', $value);
    }

    /**
    * The class for the web font icon of the update command button.
    * @param string $value
    * @return \Kendo\UI\GridColumnCommandItemIconClass
    */
    public function update($value) {
        return $this->setProperty('update', $value);
    }

//<< Properties
}

?>
