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
        };
        
        var designKey = props.attributes.template || '',
        	displayText = 'Select from design from the block settings';
        
        if (designKey != '') displayText = 'Form design # ' + (essb_block_subscribe_designs[designKey] || designKey);

        return [
            /**
             * Server side render
             */
            el("div", {
                	className: "essb-block-editor-container",
                	style: {}
	            },
	            el("div", { className: "essb-block-editor-icon"}),
	            el("div", { className: "essb-block-editor-command-tag essb-block-editor-command-tag-subscribe"}, "Subscribe Form"),
	            el("div", { className: "essb-block-editor-subscribe essb-block-editor-content"}, displayText)
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