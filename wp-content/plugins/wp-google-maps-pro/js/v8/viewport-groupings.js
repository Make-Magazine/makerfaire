/**
 * @namespace WPGMZA
 * @module ViewportGroupings
 * @requires WPGMZA.EventDispatcher
 */
jQuery(function($) {
	
	WPGMZA.ViewportGroupings = function(map){
        WPGMZA.EventDispatcher.apply(this);

        this.map = map;
        
        this.initGroups();
    }

    WPGMZA.extend(WPGMZA.ViewportGroupings, WPGMZA.EventDispatcher);
    
    WPGMZA.ViewportGroupings.createInstance = function(map) {
        return new WPGMZA.ViewportGroupings(map);
    }

    WPGMZA.ViewportGroupings.prototype.initGroups = function(){
        var self = this;

        this.groups = [];

        $(this.map.element).find('.wpgmza-inner-stack .grouping').each(function(){
            var group = WPGMZA.ViewportGroupingPanel.createInstance(this);
            self.groups.push(group);
        });
    }

    WPGMZA.ViewportGroupings.prototype.update = function(tag){
        for(var i in this.groups){
            this.groups[i].showComponent(tag);
        }
    }
});