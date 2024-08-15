<?php

namespace Kendo\Dataviz\UI;

class DiagramShape extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds DiagramShapeConnector to the DiagramShape.
    * @param \Kendo\Dataviz\UI\DiagramShapeConnector|array,... $value one or more DiagramShapeConnector to add.
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function addConnector($value) {
        return $this->add('connectors', func_get_args());
    }

    /**
    * Defines default options for all connectors belonging to a given shape.
    * @param \Kendo\Dataviz\UI\DiagramShapeConnectorDefaults|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function connectorDefaults($value) {
        return $this->setProperty('connectorDefaults', $value);
    }

    /**
    * Defines the shapes content settings.
    * @param \Kendo\Dataviz\UI\DiagramShapeContent|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

    /**
    * Defines the shape editable options.
    * @param boolean|\Kendo\Dataviz\UI\DiagramShapeEditable|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * Defines the background fill options of the shape.
    * @param string|\Kendo\Dataviz\UI\DiagramShapeFill|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function fill($value) {
        return $this->setProperty('fill', $value);
    }

    /**
    * Defines the height of the shape when added to the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * Defines the hover configuration.
    * @param \Kendo\Dataviz\UI\DiagramShapeHover|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function hover($value) {
        return $this->setProperty('hover', $value);
    }

    /**
    * The unique identifier for a Shape. The id value is used to identify shapes in connection configurations. The connection to and from properties usually point to shape id values.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function id($value) {
        return $this->setProperty('id', $value);
    }

    /**
    * Defines the minimum height the shape should have, that is, it cannot be resized to a value smaller than the given one.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function minHeight($value) {
        return $this->setProperty('minHeight', $value);
    }

    /**
    * Defines the minimum width the shape should have, that is, it cannot be resized to a value smaller than the given one. See example at shapes.minHeight.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function minWidth($value) {
        return $this->setProperty('minWidth', $value);
    }

    /**
    * The path option of a Shape is a description of a custom geometry. The format follows the standard SVG format (https://www.w3.org/TR/SVG/paths.html#PathData "SVG Path data.").
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function path($value) {
        return $this->setProperty('path', $value);
    }

    /**
    * Specifies a rotation angle for the shape.
    * @param \Kendo\Dataviz\UI\DiagramShapeRotation|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function rotation($value) {
        return $this->setProperty('rotation', $value);
    }

    /**
    * The source of the shape image. Applicable when the type is set to "image".
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function source($value) {
        return $this->setProperty('source', $value);
    }

    /**
    * Defines the shape border stroke configuration.
    * @param \Kendo\Dataviz\UI\DiagramShapeStroke|array $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function stroke($value) {
        return $this->setProperty('stroke', $value);
    }

    /**
    * Specifies the type of the Shape using any of the built-in shape type. "rectangle" - The default option; "circle" - A circle/ellipse or "image" - An image.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * Sets the visual option of the DiagramShape.
    * A function returning a visual element to render for this shape. For more information, refer to visual.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * Defines the width of the shape when added to the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Defines the x-coordinate of the shape when added to the Diagram. Does not take effect if the Diagram is using a pre-defined layout.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function x($value) {
        return $this->setProperty('x', $value);
    }

    /**
    * Defines the y-coordinate of the shape when added to the Diagram. Does not take effect if the Diagram is using a pre-defined layout. You can see an example at shapes.x
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShape
    */
    public function y($value) {
        return $this->setProperty('y', $value);
    }

//<< Properties
}

?>
