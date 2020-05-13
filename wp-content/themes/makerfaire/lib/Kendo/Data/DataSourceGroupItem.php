<?php

namespace Kendo\Data;

class DataSourceGroupItem extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds DataSourceGroupItemAggregate to the DataSourceGroupItem.
    * @param \Kendo\Data\DataSourceGroupItemAggregate|array,... $value one or more DataSourceGroupItemAggregate to add.
    * @return \Kendo\Data\DataSourceGroupItem
    */
    public function addAggregate($value) {
        return $this->add('aggregates', func_get_args());
    }

    /**
    * Sets the compare option of the DataSourceGroupItem.
    * A JavaScript function which is used to compare the groups (refer to sort.compare for comparing the items of the groups). It has the same signature as the compare function accepted by Array.sort.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Data\DataSourceGroupItem
    */
    public function compare($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('compare', $value);
    }

    /**
    * The sort order of the group.The supported values are: "asc" (ascending order) or "desc" (descending order). The default sort order is ascending.
    * @param string $value
    * @return \Kendo\Data\DataSourceGroupItem
    */
    public function dir($value) {
        return $this->setProperty('dir', $value);
    }

    /**
    * The data item field to group by.
    * @param string $value
    * @return \Kendo\Data\DataSourceGroupItem
    */
    public function field($value) {
        return $this->setProperty('field', $value);
    }

//<< Properties
}

?>
