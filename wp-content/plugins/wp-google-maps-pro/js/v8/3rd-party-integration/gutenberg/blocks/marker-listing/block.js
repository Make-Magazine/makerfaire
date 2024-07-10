/**
 * Registers the Pro only block for this module
 * 
 * @since 9.0.0
 * @for marker-listing
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
		WPGMZA.Integration.Blocks.MarkerListing = function(){
			wp.blocks.registerBlockType('gutenberg-wpgmza/marker-listing', this.getDefinition());
		}

		WPGMZA.Integration.Blocks.MarkerListing.createInstance = function() {
			return new WPGMZA.Integration.Blocks.MarkerListing();
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.onEdit = function(props){
			const inspector = this.getInspector(props);
			const preview = this.getPreview(props);

			return [
				inspector,
				preview
			];
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getInspector = function(props){
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
						{ title: wp.i18n.__('Options') },
						React.createElement(wp.components.SelectControl, {
							name: "id",
							label: wp.i18n.__("Style"),
							value: props.attributes.style_slug || "",
							options: this.getStyleOptions(),
							onChange: (value) => {
								props.setAttributes({style_slug : value});
							}
						}),
					)
				);

				inspector.push(panel);
			}
			return inspector;
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getPreview = function(props){
			let blockProps = useBlockProps({
				className: props.className + " wpgmza-gutenberg-block-module", key: 'marker-listing-preview'
			});

			return React.createElement(
				"div",
				{ ...blockProps },
				React.createElement(wp.components.Dashicon, { icon: "list-view" }),
				React.createElement(
					"span",
					{ "className": "wpgmza-gutenberg-block-title" },
					wp.i18n.__("Your marker listing will appear here on your websites front end")
				),
				React.createElement(
					"div",
					{ "className": "wpgmza-gutenberg-block-hint"},
					wp.i18n.__("Must be placed on map page. Remember to disable marker listings in your map settings (Maps > Edit > Settings > Marker Listing)")
				)
			)
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getDefinition = function(){
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

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getAttributes = function(){
			return {
				id : {type : 'string'},
				style_slug : {type : 'string'},
			};
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getKeywords = function(){
			/* Deprecated - See Block JSON */
			return [
				'Marker Listing', 
				'Marker List', 
				'Markers', 
			];
		}

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getMapOptions = function () {
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

		WPGMZA.Integration.Blocks.MarkerListing.prototype.getStyleOptions = function(){
			let data = [];

			data.push({
				key : "basic-list",
				value : "basic-list",
				label : "Basic List"
			});
			
			data.push({
				key : "basic-table",
				value : "basic-table",
				label : "Basic Table"
			});

			data.push({
				key : "advanced-table",
				value : "advanced-table",
				label : "Advanced Table"
			});

			data.push({
				key : "carousel",
				value : "carousel",
				label : "Carousel"
			});

			data.push({
				key : "grid",
				value : "grid",
				label : "Grid"
			});

			data.push({
				key : "panel",
				value : "panel",
				label : "Panel"
			});

			return data;
		}

		/*
		* Register the block
		*/
		WPGMZA.Integration.Blocks.instances.markerListing = WPGMZA.Integration.Blocks.MarkerListing.createInstance(); 
	});
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n, window.wp);