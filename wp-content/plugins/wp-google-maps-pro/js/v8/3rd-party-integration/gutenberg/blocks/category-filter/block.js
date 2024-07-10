/**
 * Registers the Pro only block for this module
 * 
 * @since 9.0.0
 * @for category-legends
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
		WPGMZA.Integration.Blocks.CategoryFilter = function(){
			wp.blocks.registerBlockType('gutenberg-wpgmza/category-filter', this.getDefinition());
		}

		WPGMZA.Integration.Blocks.CategoryFilter.createInstance = function() {
			return new WPGMZA.Integration.Blocks.CategoryFilter();
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.onEdit = function(props){
			const inspector = this.getInspector(props);
			const preview = this.getPreview(props);

			return [
				inspector,
				preview
			];
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getInspector = function(props){
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
					)
				);

				inspector.push(panel);
			}
			return inspector;
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getPreview = function(props){
			let blockProps = useBlockProps({
				className: props.className + " wpgmza-gutenberg-block-module", key: 'category-filter-preview'
			});

			return React.createElement(
				"div",
				{ ...blockProps },
				React.createElement(wp.components.Dashicon, { icon: "filter" }),
				React.createElement(
					"span",
					{ "className": "wpgmza-gutenberg-block-title" },
					wp.i18n.__("Your category filer will appear here on your websites front end")
				),
				React.createElement(
					"div",
					{ "className": "wpgmza-gutenberg-block-hint"},
					wp.i18n.__("Must be placed on map page. Remember to disable the category filter in your map settings (Maps > Edit > Settings > Marker Listing > Filtering)")
				)
			)
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getDefinition = function(){
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

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getAttributes = function(){
			return {
				id : {type : 'string'}
			};
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getKeywords = function(){
			/* Deprecated - Now handled by Block JSON*/
			return [
				'Category', 
				'Category Filter', 
				'Map Categories', 
				'Filter', 
				'Map Filter', 
			];
		}

		WPGMZA.Integration.Blocks.CategoryFilter.prototype.getMapOptions = function () {
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
		WPGMZA.Integration.Blocks.instances.categoryFilter = WPGMZA.Integration.Blocks.CategoryFilter.createInstance(); 
	});
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n, window.wp);