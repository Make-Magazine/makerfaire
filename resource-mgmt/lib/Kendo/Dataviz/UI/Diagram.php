<?php

namespace Kendo\Dataviz\UI;

class Diagram extends \Kendo\UI\Widget {
    public function name() {
        return 'Diagram';
    }
//>> Properties

    /**
    * If set to false the widget will not bind to the data source during initialization. In this case data binding will occur when the change event of the data source is fired. By default the widget will bind to the data source specified in the configuration.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Defines the defaults of the connections. Whenever a connection is created, the specified connectionDefaults will be used and merged with the (optional) configuration passed through the connection creation method.
    * @param \Kendo\Dataviz\UI\DiagramConnectionDefaults|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function connectionDefaults($value) {
        return $this->setProperty('connectionDefaults', $value);
    }

    /**
    * Adds DiagramConnection to the Diagram.
    * @param \Kendo\Dataviz\UI\DiagramConnection|array,... $value one or more DiagramConnection to add.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addConnection($value) {
        return $this->add('connections', func_get_args());
    }

    /**
    * Defines the data source of the connections.
    * @param |array| $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function connectionsDataSource($value) {
        return $this->setProperty('connectionsDataSource', $value);
    }

    /**
    * Sets the data source of the Diagram.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * A set of settings to configure the Diagram behavior when the user attempts to: edit, delete or create shapes and connections.; drag shapes.; resize shapes. or rotate shapes..
    * @param boolean|\Kendo\Dataviz\UI\DiagramEditable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * The layout of a diagram consists in arranging the shapes (sometimes also the connections) in some fashion in order to achieve an aesthetically pleasing experience to the user. It aims at giving a more direct insight in the information contained within the diagram and its relational structure.On a technical level, layout consists of a multitude of algorithms and optimizations: analysis of the relational structure (loops, multi-edge occurrence...); connectedness of the diagram and the splitting into disconnected components; crossings of connections or bends and length of links. and various ad-hoc calculations which depend on the type of layout. The criteria on which an algorithm is based vary but the common denominator is: a clean separation of connected components (subgraphs); an orderly organization of the shapes in such a way that siblings are close to another, i.e. a tight packing of shapes which belong together (parent of child relationship) or a minimum of connection crossings. Kendo diagram includes three of the most used layout algorithms which should cover most of your layout needs - tree layout, force-directed layout and layered layout. Please, check the type property for more details regarding each type.The generic way to apply a layout is by calling the layout() method on the diagram. The method has a single parameter options. It is an object, which can contain parameters which are specific to the layout as well as parameters customizing the global grid layout. Parameters which apply to other layout algorithms can be included but are overlooked if not applicable to the chose layout type. This means that you can define a set of parameters which cover all possible layout types and simply pass it in the method whatever the layout define in the first parameter.
    * @param \Kendo\Dataviz\UI\DiagramLayout|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function layout($value) {
        return $this->setProperty('layout', $value);
    }

    /**
    * Defines the pannable options. Use this setting to disable Diagram pan or change the key that activates the pan behavior.
    * @param boolean|\Kendo\Dataviz\UI\DiagramPannable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pannable($value) {
        return $this->setProperty('pannable', $value);
    }

    /**
    * Configures the export settings for the saveAsPDF method.
    * @param \Kendo\Dataviz\UI\DiagramPdf|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * Defines the Diagram selection options.By default, you can select shapes in the Diagram in one of two ways: Clicking a single shape to select it and deselect any previously selected shapes. or Holding the Ctrl key while clicking multiple shapes to select them and any other shapes between them.. Using the selectable configuration, you can enable single selection only, enable selection by drawing a rectangular area with the mouse around shapes in the canvas, or disable selection altogether.
    * @param boolean|\Kendo\Dataviz\UI\DiagramSelectable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * Defines the default options that will be applied to all shapes in the Diagram.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaults|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function shapeDefaults($value) {
        return $this->setProperty('shapeDefaults', $value);
    }

    /**
    * Adds DiagramShape to the Diagram.
    * @param \Kendo\Dataviz\UI\DiagramShape|array,... $value one or more DiagramShape to add.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addShape($value) {
        return $this->add('shapes', func_get_args());
    }

    /**
    * Sets the template option of the Diagram.
    * The template which renders the content of the shape when bound to a dataSource. The names you can use in the template correspond to the properties used in the dataSource. For an example, refer to the dataSource topic below.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Diagram.
    * The template which renders the content of the shape when bound to a dataSource. The names you can use in the template correspond to the properties used in the dataSource. For an example, refer to the dataSource topic below.
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The diagram theme. This can be either a built-in theme or "sass". When set to "sass" the diagram will read the variables from a Sass-based theme.The supported values are: * "sass" - works only when a custom Sass theme is loaded in the page * "black" * "blueopal" * "bootstrap" * "bootstrap-v4" - works only with the Bootstrap-v4 Sass theme loaded in the page * "default" * "default-v2" - works only with the Default-v2 Sass theme loaded in the page * "fiori" * "flat" * "highcontrast" * "material" * "materialBlack" * "metro" * "metroblack" * "moonlight" * "nova" * "office365" * "silver" * "uniform"
    * @param string $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function theme($value) {
        return $this->setProperty('theme', $value);
    }

    /**
    * The default zoom level of the Diagram in percentages.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoom($value) {
        return $this->setProperty('zoom', $value);
    }

    /**
    * The maximum zoom level in percentages. The user will not be allowed to zoom in past this level.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomMax($value) {
        return $this->setProperty('zoomMax', $value);
    }

    /**
    * The minimum zoom level in percentages. The user will not be allowed to zoom out past this level. You can see an example in zoomMax.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomMin($value) {
        return $this->setProperty('zoomMin', $value);
    }

    /**
    * The zoom step when using the mouse-wheel to zoom in or out.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomRate($value) {
        return $this->setProperty('zoomRate', $value);
    }

    /**
    * Sets the add event of the Diagram.
    * Fired when the user adds new shape or connection.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('add', $value);
    }

    /**
    * Sets the cancel event of the Diagram.
    * Fired when the user clicks the "cancel" button in the popup window in case the item was added via a toolbar.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function cancel($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the change event of the Diagram.
    * Fired when an item is added or removed to/from the diagram.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the click event of the Diagram.
    * Fired when the user clicks on a shape or a connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * Sets the dataBound event of the Diagram.
    * Fired when the widget is bound to data from dataDource and connectionsDataSource.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the drag event of the Diagram.
    * Fired when dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function drag($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drag', $value);
    }

    /**
    * Sets the dragEnd event of the Diagram.
    * Fired after finishing dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dragEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragEnd', $value);
    }

    /**
    * Sets the dragStart event of the Diagram.
    * Fired before starting dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dragStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragStart', $value);
    }

    /**
    * Sets the edit event of the Diagram.
    * Fired when the user edits a shape or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function edit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('edit', $value);
    }

    /**
    * Sets the itemBoundsChange event of the Diagram.
    * Fired when the location or size of a shape are changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function itemBoundsChange($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('itemBoundsChange', $value);
    }

    /**
    * Sets the itemRotate event of the Diagram.
    * Fired when a shape is rotated.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function itemRotate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('itemRotate', $value);
    }

    /**
    * Sets the mouseEnter event of the Diagram.
    * Fired when the mouse enters a shape or a connection.Will not fire for disabled items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function mouseEnter($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('mouseEnter', $value);
    }

    /**
    * Sets the mouseLeave event of the Diagram.
    * Fired when the mouse leaves a shape or a connection.Will not fire for disabled items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function mouseLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('mouseLeave', $value);
    }

    /**
    * Sets the pan event of the Diagram.
    * Fired when the user pans the diagram.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pan($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pan', $value);
    }

    /**
    * Sets the remove event of the Diagram.
    * Fired when the user removes a shape or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the save event of the Diagram.
    * Fired when the user saves a shape or a connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function save($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('save', $value);
    }

    /**
    * Sets the select event of the Diagram.
    * Fired when the user selects one or more items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the toolBarClick event of the Diagram.
    * Fired when the user clicks an item in the toolbar.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function toolBarClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('toolBarClick', $value);
    }

    /**
    * Sets the zoomEnd event of the Diagram.
    * Fired when the user changes the diagram zoom level.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomEnd', $value);
    }

    /**
    * Sets the zoomStart event of the Diagram.
    * Fired when the user starts changing the diagram zoom level.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomStart', $value);
    }


//<< Properties
}

?>
