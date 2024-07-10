/**
 * Registers the Pro extended block for this module
 * 
 * @since 9.0.0
 * @for pro-store-locator
*/
(function( blocks, element, components, i18n, wp) {
	jQuery(($) => {
		/**
		 * Scalable module defined here
		 * 
		 * This allows Pro to improve on basic functionality, and helps stay within our architecture
		*/
		WPGMZA.Integration.Blocks.ProStoreLocator = function(){
			WPGMZA.Integration.Blocks.StoreLocator.apply(this, arguments);
		}

		WPGMZA.extend(WPGMZA.Integration.Blocks.ProStoreLocator, WPGMZA.Integration.Blocks.StoreLocator);

		WPGMZA.Integration.Blocks.ProStoreLocator.prototype.getAttributes = function(){
			let attributes = WPGMZA.Integration.Blocks.StoreLocator.prototype.getAttributes.apply(this, arguments);

			attributes.id = {type : "string"};
			return attributes;
		}

		WPGMZA.Integration.Blocks.ProStoreLocator.prototype.getInspector = function(props){
			let inspector = WPGMZA.Integration.Blocks.StoreLocator.prototype.getInspector.apply(this, arguments);

			if(inspector){
				if(!!props.isSelected){
					let panel = React.createElement(
						wp.blockEditor.InspectorControls,
						{ key: "proinspector" },
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
			}

			return inspector;
		}

		WPGMZA.Integration.Blocks.ProStoreLocator.prototype.getMapOptions = function () {
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

		WPGMZA.Integration.Blocks.instances.storeLocator = WPGMZA.Integration.Blocks.StoreLocator.createInstance(); 
	});
})( window.wp.blocks, window.wp.element, window.wp.components, window.wp.i18n, window.wp);