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

registerBlockType('essb/essb-socialprofiles', {
    title: 'ESSB Social Profiles',
    description: "Add social profile links",
    icon: "external",
    category: 'widgets',
    keywords: ["Social", "Profile", "Social Profiles", "Easy Social Share Buttons"],
    attributes: {
        'template': {
            type: 'string',
            default: ''
        },
        'animation': {
            type: 'string',
            default: ''
        },
        'align': {
            type: 'string',
            default: ''
        },
        'size': {
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
        'cta': {
            type: 'boolean',
        },
        'cta_vertical': {
            type: 'boolean',
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
                    block: 'essb/essb-socialprofiles',
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
                            options:  essb_merge_object_to_block_values(essb_block_profiles['template'].options),
                            onChange: (value) => {
                                props.setAttributes({template: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Animation',
                            value: props.attributes.animation,
                            options:  essb_merge_object_to_block_values(essb_block_profiles['animation'].options),
                            onChange: (value) => {
                                props.setAttributes({animation: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Alignment',
                            value: props.attributes.align,
                            options:  essb_merge_object_to_block_values(essb_block_profiles['align'].options),
                            onChange: (value) => {
                                props.setAttributes({align: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Size',
                            value: props.attributes.size,
                            options:  essb_merge_object_to_block_values(essb_block_profiles['size'].options),
                            onChange: (value) => {
                                props.setAttributes({size: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Columns',
                            value: props.attributes.columns,
                            options:  essb_merge_object_to_block_values(essb_block_profiles['columns'].options),
                            onChange: (value) => {
                                props.setAttributes({columns: value});
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
                            label: 'Show texts with the buttons',
                            checked: props.attributes.cta,
                            onChange: (value) => {
                                props.setAttributes({cta: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Vertical text layout',
                            checked: props.attributes.cta_vertical,
                            onChange: (value) => {
                                props.setAttributes({cta_vertical: value});
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