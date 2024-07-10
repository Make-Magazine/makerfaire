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
		WPGMZA.Integration.Blocks.CategoryLegends = function(){
			wp.blocks.registerBlockType('gutenberg-wpgmza/category-legends', this.getDefinition());
		}

		WPGMZA.Integration.Blocks.CategoryLegends.createInstance = function() {
			return new WPGMZA.Integration.Blocks.CategoryLegends();
		}

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.onEdit = function(props){
			const inspector = this.getInspector(props);
			const preview = this.getPreview(props);

			return [
				inspector,
				preview
			];
		}

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getInspector = function(props){
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

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getPreview = function(props){
			let blockProps = useBlockProps({
				className: props.className + " wpgmza-gutenberg-block-module", key: 'category-legends-preview'
			});

			return React.createElement(
				"div",
				{ ...blockProps },
				React.createElement(wp.components.Dashicon, { icon: "category" }),
				React.createElement(
					"span",
					{ "className": "wpgmza-gutenberg-block-title" },
					wp.i18n.__("Your category legends will appear here on your websites front end")
				)
			)
		}

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getDefinition = function(){
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

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getAttributes = function(){
			return {
				id : {type : 'string'}
			};
		}

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getKeywords = function(){
			/* DEPRECATED */
			return [
				'Category', 
				'Category Legends', 
				'Map Categories', 
				'Legend', 
			];
		}

		WPGMZA.Integration.Blocks.CategoryLegends.prototype.getMapOptions = function () {
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
		WPGMZA.Integration.Blocks.instances.categoryLegends = WPGMZA.Integration.Blocks.CategoryLegends.createInstance(); 
	});
})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n, window.wp);