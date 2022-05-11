/**
 * Subscribe Block
 */
var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    ServerSideRender = wp.components.ServerSideRender,
    TextControl = wp.components.TextControl,
    SelectControl = wp.components.SelectControl,
    ToggleControl = wp.components.ToggleControl,
    TextareaControl = wp.components.TextareaControl,
    PanelBody = wp.components.PanelBody,
    InspectorControls = wp.editor.InspectorControls;


var block_subscribe_design_options = [];
if (essb_block_subscribe_designs) {
    for (var key in essb_block_subscribe_designs) {
    	block_subscribe_design_options.push({'label': essb_block_subscribe_designs[key], 'value': key});
    }
}


registerBlockType('essb/essb-subscribe', {
    title: 'ESSB Subscribe Form',
    description: "Add a subscribe to mailing list form.",
    icon: "email",
    category: 'widgets',
    keywords: ["Subscribe", "Form", "Easy Social Share Buttons"],
    attributes: {
        'template': {
            type: 'string',
            default: ''
        },
    },


    edit: (props) => {

        if (props.isSelected) {
            // console.debug(props.attributes);
        }
        ;

        return [
            /**
             * Server side render
             */
            el("div", {
                    className: "essb-editor-container",
                    style: {textAlign: "left"}
                },
                el(ServerSideRender, {
                    block: 'essb/essb-subscribe',
                    attributes: props.attributes
                })
            ),

            /**
             * Inspector
             */
            el(InspectorControls,
                {}, [

                    el(PanelBody, {title: "Settings", className: 'essb-block-settings', initialOpen: true},

                        el(SelectControl, {
                            label: 'Template',
                            value: props.attributes.template,
                            options:  block_subscribe_design_options,
                            onChange: (value) => {
                                props.setAttributes({template: value});
                            }
                        }),

                        // end elements
                    ), // panel body
                ]
            )
        ]
    },

    save: () => {
        /** this is resolved server side */
        return null
    }
});