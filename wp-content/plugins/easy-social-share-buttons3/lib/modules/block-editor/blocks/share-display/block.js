/**
 * Share Display Block
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


var block_design_options = [];
block_design_options.push( { 'label': 'Select design ...', 'value': ''});
if (essb_block_share_display) {
    for (var key in essb_block_share_display) {
        block_design_options.push({'label': essb_block_share_display[key], 'value': key});
    }
}


registerBlockType('essb/essb-share-display', {
    title: 'ESSB Share Buttons',
    description: "Add share buttons in the content",
    icon: "share",
    category: 'widgets',
    keywords: ["Social", "Share", "Sharing", "Easy Social Share Buttons"],
    attributes: {
        'display': {
            type: 'string',
            default: ''
        },
        'force': {
            type: 'boolean'
        },
        'custom_share': {
            type: 'boolean'
        },
        'custom_share_url': {
            type: 'string',
            default: ''
        },
        'custom_share_message': {
            type: 'string',
            default: ''
        },
        'custom_share_image': {
            type: 'string',
            default: ''
        },
    },


    edit: (props) => {

        if (props.isSelected) {
            // console.debug(props.attributes);
        };
        
        var displayText = 'Choose the custom display position from the block settings. If you need to add additional displays you can do this from Where to Display -> Custom Position/Displays and create as many as you need.';
        if (props.attributes.display) displayText = 'Display: ' + (essb_block_share_display[props.attributes.display] || prop.attributes.display);

        return [
            /**
             * Server side render
             */
            el("div", {
	            	className: "essb-block-editor-container",
	            	style: {}
	            },
	            el("div", { className: "essb-block-editor-icon"}),
	            el("div", { className: "essb-block-editor-command-tag essb-block-editor-command-tag-subscribe"}, "Share Buttons"),
	            el("div", { className: "essb-block-editor-share essb-block-editor-content"}, displayText)
	        ),

            /**
             * Inspector
             */
            el(InspectorControls,
                {}, [

                    el(PanelBody, {title: "Settings", className: 'essb-block-settings', initialOpen: true},

                        el(SelectControl, {
                            label: 'Display',
                            help: 'If you need to add additional displays you can do this from Where to Display -> Custom Position/Displays and create as many as you need.',
                            value: props.attributes.display,
                            options:  block_design_options,
                            onChange: (value) => {
                                props.setAttributes({display: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Always show',
                            help: 'Ensure share buttons will be always visible no matter the selection in the position menu.',
                            checked: props.attributes.force,
                            onChange: (value) => {
                                props.setAttributes({force: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Custom share parameters',
                            help: 'Enable the ability to set custom share parameters for the button display. Most social networks accept modification only via social media optimization tags. As of this, you need to put the message in those tags of the shared URL. The "Message" and "Image" options are accepted by very networks.',
                            checked: props.attributes.custom_share,
                            onChange: (value) => {
                                props.setAttributes({custom_share: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Share URL',
                            help: '',
                            value: props.attributes.custom_share_url,
                            onChange: (value) => {
                                props.setAttributes({custom_share_url: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Share message',
                            help: '',
                            value: props.attributes.custom_share_message,
                            onChange: (value) => {
                                props.setAttributes({custom_share_message: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Share image',
                            help: 'Set the URL to the image if you pass custom.',
                            value: props.attributes.custom_share_image,
                            onChange: (value) => {
                                props.setAttributes({custom_share_image: value});
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