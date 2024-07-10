/**
 * @namespace WPGMZA
 * @module ProPolygonPanel
 * @requires WPGMZA.PolygonPanel
 */
jQuery(function($) {
	
	WPGMZA.ProPolygonPanel = function(element)
	{
		WPGMZA.PolygonPanel.apply(this, arguments);

		if(!WPGMZA.InternalEngine.isLegacy()){
			this.descriptionElement = $(this.element).find('[data-ajax-name="description"]');
			this.initWritersBlock(this.descriptionElement.get(0));
		}
	}
	
	WPGMZA.extend(WPGMZA.ProPolygonPanel, WPGMZA.PolygonPanel);

	/* Not Dry at the moment, we need a Pro Feature Panel Module */
	WPGMZA.ProPolygonPanel.prototype.populate = function(data)
	{
		WPGMZA.PolygonPanel.prototype.populate.apply(this, arguments);
		
		for(var name in data){
			switch(name){
				case "description":
					if(!WPGMZA.InternalEngine.isLegacy()){
						if(this.writersblock){
							if(this.writersblock.ready){
								this.writersblock.setContent(data.description);								
							}
						} 
					}
			}
		}
	}
});