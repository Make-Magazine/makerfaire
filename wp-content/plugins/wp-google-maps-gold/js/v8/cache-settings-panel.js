/**
 * @namespace WPGMZA
 * @module CachingSettingsPanel
 */
jQuery(function($) {
	
	WPGMZA.CachingSettingsPanel = function(){
		var self = this;
		this.updateCachingContols();

        $('input[name="enable_caching"]').on('change', function(event) {
			self.updateCachingContols();
		});

	}

    /**
	 * Update caching controls visibility based on the root option
     * 
	 * @method
	 * @memberof WPGMZA.CachingSettingsPanel
	*/
	WPGMZA.CachingSettingsPanel.prototype.updateCachingContols = function(){
		const showControls =  $("input[name='enable_caching']").prop("checked");
		if(showControls){
			$('.caching-conditional-row').show();
		} else {
			$('.caching-conditional-row').hide();
		}
	}

    $(document).ready(function() {
		if(WPGMZA.currentPage == "map-settings"){
			WPGMZA.cachingSettingsPanel = new WPGMZA.CachingSettingsPanel();
        }
	});
});