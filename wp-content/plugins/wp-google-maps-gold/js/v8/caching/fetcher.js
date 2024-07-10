/* 
 * Note, this does not auto-load, because it does not contain identifiable namespaces or module dependencies
 * 
 * For the beta, this is intentional. It will likely change with time
*/
jQuery(function($) {
    WPGMZA.Map.prototype.__fetchFeaturesViaREST = WPGMZA.Map.prototype.fetchFeaturesViaREST;

    WPGMZA.Map.prototype.fetchFeaturesViaREST = function(){
        if(typeof WPGMZA_CACHE === 'undefined' || typeof WPGMZA_CACHE.markers === 'undefined' || typeof WPGMZA_CACHE.markers.data === 'undefined' || WPGMZA_CACHE.markers.data.length <= 0){
            /* Run the original function */
           this.__fetchFeaturesViaREST();
        } else {
            /* Load from the cache */
            const mapIds = [parseInt(this.id)];
            if(this.mashupIDs && this.mashupIDs.length){
                for(let id of this.mashupIDs){
                    mapIds.push(parseInt(id));
                }
            }

            let cachedData = [];
            for(let mapId of mapIds){
                if(typeof WPGMZA_CACHE.markers.data[mapId] !== 'undefined'){
                    cachedData = cachedData.concat(WPGMZA_CACHE.markers.data[mapId]);
                }
            }

            this.onMarkersFetched(cachedData, false);

            var self = this;
            var data;
            var filter = this.markerFilter.getFilteringParameters();
            
            data = this.getRESTParameters({
                filter: JSON.stringify(filter)
            });

            data.exclude = "markers";
            WPGMZA.restAPI.call("/features/", {
                useCompressedPathVariable: true,
                data: data,
                success: function(result, status, xhr) {
                    self.onFeaturesFetched(result);
                }
            });
        }
	}
})