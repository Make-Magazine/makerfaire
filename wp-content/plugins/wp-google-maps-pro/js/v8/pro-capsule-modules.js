/**
 * @namespace WPGMZA
 * @module ProCapsuleModules
 * @requires WPGMZA.CapsuleModules
 */
jQuery(function($) {
	
	WPGMZA.ProCapsuleModules = function(){
		WPGMZA.CapsuleModules.call(this, arguments);
	}

	WPGMZA.extend(WPGMZA.ProCapsuleModules, WPGMZA.CapsuleModules);

	WPGMZA.ProCapsuleModules.prototype.prepareCapsules = function(){
		WPGMZA.CapsuleModules.prototype.prepareCapsules.call(this, arguments);

		this.registerMarkerListings();
	}

	WPGMZA.ProCapsuleModules.prototype.registerMarkerListings = function(){
		$('[data-wpgmza-table]').each((index, element) => {
			const mapId = $(element).data('map-id');
			if(mapId && !WPGMZA.getMapByID(mapId)){	
				/* No map data loaded, time to create the capsule */
				const settings = $(element).data('map-settings');
				const mapProxy = this.proxyMap(mapId, settings);

				const capsule = {
					type : 'marker_listing',
					element : element,
					instance : WPGMZA.MarkerListing.createInstance(mapProxy, element)
				};

				capsule.instance.onItemClick = function(){};
				mapProxy.markerListing = capsule.instance;

				capsule.instance.isCapsule = true;
				this.capsules.push(capsule);

				mapProxy.on('filteringcomplete', (event) => {
					if(mapProxy.markerListing){
						mapProxy.markerListing.onFilteringComplete(event);
					}
				});
			}
		});
	}
});