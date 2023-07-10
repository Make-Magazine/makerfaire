/**
 * Twitter Block
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

registerBlockType('essb/essb-twitter', {
    title: 'ESSB Click to Tweet',
    description: "Add a tweet box.",
    icon: "twitter",
    category: 'widgets',
    keywords: ["Twitter", "Tweet", "Easy Social Share Buttons"],
    attributes: {
        'theme': {
            type: 'string',
            default: ''
        },
        'username': {
            type: 'string',
            default: ""
        },
        'hashtags': {
            type: 'string',
            default: ""
        },
        'tweet': {
            type: 'string',
            default: ""
        },
        'url': {
            type: 'string',
            default: ""
        },
        'hide_username': {
            type: 'boolean',
        },
        'hide_hashtags': {
            type: 'boolean',
        },
    },


    edit: (props) => {

        if (props.isSelected) {
            // console.debug(props.attributes);
        };
        
        var displayTweet = "The sharable quote for Tweet is not set yet. Select the block and use the settings to set Tweet and additional options.";
        if (props.attributes.tweet) displayTweet = props.attributes.tweet;
        if (props.attributes.username) displayTweet += ', @' + props.attributes.username;
        if (props.attributes.hashtags) displayTweet += ', #' + props.attributes.hashtags;

        return [
            /**
             * Server side render
             */
            el("div", {
                    className: "essb-block-editor-container",
                    style: {}
                },
                el("div", { className: "essb-block-editor-icon"}),
                el("div", { className: "essb-block-editor-command-tag essb-block-editor-command-tag-cct"}, "Click to Tweet"),
                el("div", { className: "essb-block-editor-ctt essb-block-editor-content"}, displayTweet)
            ),

            /**
             * Inspector
             */
            el(InspectorControls,
                {}, [

                    el(PanelBody, {title: "Settings", className: 'essb-block-settings', initialOpen: true},

                        el(TextareaControl, {
                            style: {height: 150},
                            label: 'Tweet',
                            value: props.attributes.tweet,
                            onChange: (value) => {
                                props.setAttributes({tweet: value});
                            }
                        }, props.attributes.tweet),

                        el(TextControl, {
                            label: 'Username',
                            value: props.attributes.username,
                            onChange: (value) => {
                                props.setAttributes({username: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Hashtags',
                            value: props.attributes.hashtags,
                            onChange: (value) => {
                                props.setAttributes({hashtags: value});
                            }
                        }),

                        el(TextControl, {
                            label: 'Share URL',
                            value: props.attributes.url,
                            onChange: (value) => {
                                props.setAttributes({url: value});
                            }
                        }),

                        el(SelectControl, {
                            label: 'Theme',
                            value: props.attributes.theme,
                            options: [
                                {label: 'Default', value: ''},
                                {label: 'Light', value: 'light'},
                                {label: 'Dark', value: 'dark'},
                                {label: 'Quote', value: 'qlite'},
                                {label: 'Modern', value: 'modern'},
                                {label: 'User', value: 'user'},
                            ],
                            onChange: (value) => {
                                props.setAttributes({theme: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Remove Username',
                            checked: props.attributes.hide_username,
                            onChange: (value) => {
                                props.setAttributes({hide_username: value});
                            }
                        }),

                        el(ToggleControl, {
                            label: 'Hide Hashtags',
                            checked: props.attributes.hide_hashtags,
                            onChange: (value) => {
                                props.setAttributes({hide_hashtags: value});
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