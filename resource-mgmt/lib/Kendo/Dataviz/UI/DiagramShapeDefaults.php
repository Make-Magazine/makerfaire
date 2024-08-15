<?php

namespace Kendo\Dataviz\UI;

class DiagramShapeDefaults extends \Kendo\SerializableObject {
//>> Properties

    /**
    * Adds DiagramShapeDefaultsConnector to the DiagramShapeDefaults.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsConnector|array,... $value one or more DiagramShapeDefaultsConnector to add.
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function addConnector($value) {
        return $this->add('connectors', func_get_args());
    }

    /**
    * Defines the default options for the shape connectors.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsConnectorDefaults|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function connectorDefaults($value) {
        return $this->setProperty('connectorDefaults', $value);
    }

    /**
    * Defines the default shapes content settings.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsContent|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function content($value) {
        return $this->setProperty('content', $value);
    }

    /**
    * Defines the shape editable options.
    * @param boolean|\Kendo\Dataviz\UI\DiagramShapeDefaultsEditable|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * Defines the fill options of the shape. Use these settings to apply a single-color or a gradient background to all shapes in the Diagram.
    * @param string|\Kendo\Dataviz\UI\DiagramShapeDefaultsFill|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function fill($value) {
        return $this->setProperty('fill', $value);
    }

    /**
    * Defines the default height of shapes in the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * Defines the hover configuration.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsHover|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function hover($value) {
        return $this->setProperty('hover', $value);
    }

    /**
    * Defines the minimum height the shape can have. Use this setting to apply a lower limit to the height of shapes when users resize them.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function minHeight($value) {
        return $this->setProperty('minHeight', $value);
    }

    /**
    * Defines the minimum width the shape can have. Use this setting to apply a lower limit to the width of shapes when users resize them.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function minWidth($value) {
        return $this->setProperty('minWidth', $value);
    }

    /**
    * The path option of a Shape is a description of a custom geometry. The format follows the standard SVG format (https://www.w3.org/TR/SVG/paths.html#PathData "SVG Path data.").
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function path($value) {
        return $this->setProperty('path', $value);
    }

    /**
    * Defines the rotation of the shape.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsRotation|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function rotation($value) {
        return $this->setProperty('rotation', $value);
    }

    /**
    * Specifies if Diagram shapes can be selected.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * The path/URL to the shape image. Applicable when the type is set to "image".
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function source($value) {
        return $this->setProperty('source', $value);
    }

    /**
    * Defines the configuration of the border around Diagram shapes.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaultsStroke|array $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function stroke($value) {
        return $this->setProperty('stroke', $value);
    }

    /**
    * Specifies the type of the Shape using any of the built-in shape type. "rectangle": this is the default option; "circle": a circle/ellipse; "image": an image or "text": some text.
    * @param string $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * Sets the visual option of the DiagramShapeDefaults.
    * A function that returns a visual element to render for a given shape. The following primitives can be used to construct a composite visual: Circle; Rectangle; Path; Line; Polyline; TextBlock; Image or Layout.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * Defines the default width of shapes in the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * Defines the x-coordinate of shapes added to the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function x($value) {
        return $this->setProperty('x', $value);
    }

    /**
    * Defines the y-coordinate of shapes added to the Diagram.
    * @param float $value
    * @return \Kendo\Dataviz\UI\DiagramShapeDefaults
    */
    public function y($value) {
        return $this->setProperty('y', $value);
    }

//<< Properties
}

?>
