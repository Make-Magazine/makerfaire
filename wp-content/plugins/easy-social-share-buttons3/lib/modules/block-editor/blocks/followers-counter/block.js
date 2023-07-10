/**
 * Followers Counter
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

/**
 * Convert a setup object to block selection
 * @param obj
 * @returns {[]}
 */
function essb_merge_object_to_block_values(obj) {
    var r = [];
    for (var key in obj) {
        r.push({'label': obj[key], 'value': key});
    }
    return r;
}

registerBlockType('essb/essb-socialfollowers', {
    title: 'ESSB Social Followers',
    description: "Add social followers block",
    icon: "external",
    category: 'widgets',
    keywords: ["Social", "Profile", "Social Profiles", "Followers", "Social Followers", "Easy Social Share Buttons"],
    attributes: {
        'template': {
            type: 'string',
            default: ''
        },
        'animation': {
            type: 'string',
            default: ''
        },
        'bgcolor': {
            type: 'string',
            default: ''
        },
        'total_type': {
            type: 'string',
            default: ''
        },
        'columns': {
            type: 'string',
            default: ''
        },
        'nospace': {
            type: 'boolean',
        },
        'hide_value': {
            type: 'boolean',
        },
        'hide_text': {
            type: 'boolean',
        },
        'show_total': {
            type: 'boolean',
        },
    },


    edit: (props) => {

        if (props.isSelected) {
            // console.debug(props.attributes);
        }
        ;
        
        var displayText = "This block will generate social profile links (with followers' counter) on the frontend of the website. You can customize the design settings using the block options. The networks that will show are configured in the Easy Social Share Buttons for WordPress plugin options.";


        return [
            /**
             * Server side render
             */
            el("div", {
	            	className: "essb-block-editor-container",
	            	style: {}
	            },
	            el("div", { className: "essb-block-editor-icon"}),
	            el("div", { className: "essb-block-editor-command-tag essb-block-editor-command-tag-subscribe"}, "Social Followers"),
	            el("div", { className: "essb-block-editor-followers essb-block-editor-content"}, displayText)
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
                            options:  essb_merge_object_to_block_values(essb_block_followers['template'].values),
                            onChange: (value) => {
                                props.setAttributes({template: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Columns',
                            value: props.attributes.columns,
                            options:  essb_merge_object_to_block_values(essb_block_followers['columns'].values),
                            onChange: (value) => {
                                props.setAttributes({columns: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Animation',
                            value: props.attributes.animation,
                            options:  essb_merge_object_to_block_values(essb_block_followers['animation'].values),
                            onChange: (value) => {
                                props.setAttributes({animation: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Without space between buttons',
                            checked: props.attributes.nospace,
                            onChange: (value) => {
                                props.setAttributes({nospace: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Hide number of followers',
                            checked: props.attributes.hide_value,
                            onChange: (value) => {
                                props.setAttributes({hide_value: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Hide followers\' text',
                            checked: props.attributes.hide_text,
                            onChange: (value) => {
                                props.setAttributes({hide_text: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Display total followers',
                            checked: props.attributes.show_total,
                            onChange: (value) => {
                                props.setAttributes({show_total: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Total type',
                            value: props.attributes.total_type,
                            options:  essb_merge_object_to_block_values(essb_block_followers['total_type'].values),
                            onChange: (value) => {
                                props.setAttributes({total_type: value});
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