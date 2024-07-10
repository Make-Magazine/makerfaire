/**
 * Registers the Pro only block for this module
 * 
 * @since 9.0.0
 * @for directions
*/

(function( blocks, element, components, i18n, wp) {
	var blockEditor = wp.blockEditor;
	var useBlockProps = blockEditor.useBlockProps;
	
	jQuery(($) => {
		/**
		 * Scalable module defined here
		 * 
		 * This allows Pro to improve on basic functionality, and helps stay within our architecture
		*/
		WPGMZA.Integration.Blocks.Directions = function(){
			wp.blocks.registerBlockType('gutenberg-wpgmza/directions', this.getDefinition());
		}

		WPGMZA.Integration.Blocks.Directions.createInstance = function() {
			return new WPGMZA.Integration.Blocks.Directions();
		}

		WPGMZA.Integration.Blocks.Directions.prototype.onEdit = function(props){
			const inspector = this.getInspector(props);
			const preview = this.getPreview(props);

			return [
				inspector,
				preview
			];
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getInspector = function(props){
			let inspector = [];
			if(!!props.isSelected){
				let panel = React.createElement(
					wp.blockEditor.InspectorControls,
					{ key: "inspector" },
					React.createElement(
						wp.components.PanelBody,
						{ title: wp.i18n.__('Map Options') },
						React.createElement(wp.components.SelectControl, {
							name: "id",
							label: wp.i18n.__("Map"),
							value: props.attributes.id || "",
							options: this.getMapOptions(),
							onChange: (value) => {
								props.setAttributes({id : value});
							}
						}),
					),
					React.createElement(
						wp.components.PanelBody,
						{ title: wp.i18n.__('Defaults') },
						React.createElement(wp.components.TextControl, {
							name: "default_from",
							label: wp.i18n.__("From Address"),
							value: props.attributes.default_from || "",
							onChange: (value) => {
								props.setAttributes({default_from : value});
							}
						}),
						React.createElement(wp.components.TextControl, {
							name: "default_to",
							label: wp.i18n.__("To Address"),
							value: props.attributes.default_to || "",
							onChange: (value) => {
								props.setAttributes({default_to : value});
							}
						}),
					)
				);

				inspector.push(panel);
			}
			return inspector;
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getPreview = function(props){
			let blockProps = useBlockProps({
				className: props.className + " wpgmza-gutenberg-block-module", key: 'directions-preview'
			});
			
			return React.createElement(
				"div",
				{ ...blockProps },
				React.createElement(wp.components.Dashicon, { icon: "location" }),
				React.createElement(
					"span",
					{ "className": "wpgmza-gutenberg-block-title" },
					wp.i18n.__("Your directions will appear here on your websites front end")
				),
				React.createElement(
					"div",
					{ "className": "wpgmza-gutenberg-block-hint"},
					wp.i18n.__("Must be placed on map page. Remember to disable the directions in your map settings (Maps > Edit > Settings > Directions)")
				)
			)
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getDefinition = function(){
			/* Now handled by the Block JSON */
			/*
			let keywords = this.getKeywords();

			keywords = keywords.map((phrase) => {
				return wp.i18n.__(phrase)
			});
			*/

			return {
				attributes : this.getAttributes(),
				edit : (props) => {
					return this.onEdit(props);
				},
				save : (props) => { 
					const blockProps = useBlockProps.save();
					return null; 
				}
			};
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getAttributes = function(){
			return {
				id : {type : 'string'},
				default_from : {type : 'string'},
				default_to : {type : 'string'}
			};
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getKeywords = function(){
			/* Deprecated */
			return [
				'Directions', 
				'Marker Directions', 
				'Map Directions', 
				'Get Directions', 
			];
		}

		WPGMZA.Integration.Blocks.Directions.prototype.getMapOptions = function () {
			let data = [];

			WPGMZA.gutenbergData.maps.forEach(function (el) {
				data.push({
					key: el.id,
					value: el.id,
					label: el.map_title + " (" + el.id + ")"
				});
			});

			return data;
		};

		/*
		* Register the block
		*/
		WPGMZA.Integration.Blocks.instances.directions = WPGMZA.Integration.Blocks.Directions.createInstance(); 
	});
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n, window.wp);