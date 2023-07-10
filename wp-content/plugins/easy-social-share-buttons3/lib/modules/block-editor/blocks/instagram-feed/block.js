/**
 * Instagram Feed
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
    r.push( { 'label': '', value: ''});
    for (var key in obj) {
        r.push({'label': obj[key], 'value': key});
    }
    return r;
}

registerBlockType('essb/essb-instagram', {
    title: 'ESSB Instagram Feed',
    description: "Add instagram feed",
    icon: "instagram",
    category: 'widgets',
    keywords: ["Social", "Feed", "Instagram", "Instagram Feed", "Easy Social Share Buttons"],
    attributes: {
        'username': {
            type: 'string',
            default: ''
        },
        'type': {
            type: 'string',
            default: ''
        },
        'show': {
            type: 'string',
            default: ''
        },
        'profile': {
            type: 'string',
            default: ''
        },
        'followbtn': {
            type: 'string',
            default: ''
        },
        'profile_size': {
            type: 'string',
            default: ''
        },
        'space': {
            type: 'string',
            default: ''
        },
        'masonry': {
            type: 'string',
            default: ''
        },
    },


    edit: (props) => {

        if (props.isSelected) {
            // console.debug(props.attributes);
        }
        ;
        
        var displayText = 'This block will show an Instagram feed on the front of the website.';
        if (props.attributes.username) displayText += ' @' + props.attributes.username;

        return [
            /**
             * Server side render
             */
            el("div", {
	            	className: "essb-block-editor-container",
	            	style: {}
	            },
	            el("div", { className: "essb-block-editor-icon"}),
	            el("div", { className: "essb-block-editor-command-tag essb-block-editor-command-tag-subscribe"}, "Instagram Feed"),
	            el("div", { className: "essb-block-editor-instagram essb-block-editor-content"}, displayText)
	        ),

            /**
             * Inspector
             */
            el(InspectorControls,
                {}, [

                    el(PanelBody, {title: "Settings", className: 'essb-block-settings', initialOpen: true},

                        el(SelectControl, {
                            label: 'Username',
                            value: props.attributes.username,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['username']),
                            onChange: (value) => {
                                props.setAttributes({username: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Display type',
                            value: props.attributes.type,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['type']),
                            onChange: (value) => {
                                props.setAttributes({type: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Images to show',
                            value: props.attributes.show,
                            help: 'Enter number between 1 and 15',
                            onChange: (value) => {
                                props.setAttributes({show: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Show profile information',
                            value: props.attributes.profile,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['profile']),
                            onChange: (value) => {
                                props.setAttributes({profile: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Show profile follow button',
                            value: props.attributes.followbtn,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['followbtn']),
                            onChange: (value) => {
                                props.setAttributes({followbtn: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Profile size',
                            value: props.attributes.profile_size,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['profile_size']),
                            onChange: (value) => {
                                props.setAttributes({profile_size: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Space between images',
                            value: props.attributes.space,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['space']),
                            onChange: (value) => {
                                props.setAttributes({space: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Masonry',
                            value: props.attributes.masonry,
                            options:  essb_merge_object_to_block_values(essb_block_instagram['masonry']),
                            onChange: (value) => {
                                props.setAttributes({masonry: value});
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