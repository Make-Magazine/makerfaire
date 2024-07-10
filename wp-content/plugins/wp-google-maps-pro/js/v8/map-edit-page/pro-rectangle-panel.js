/**
 * @namespace WPGMZA
 * @module ProRectanglePanel
 * @requires WPGMZA.RectanglePanel
 */
jQuery(function($) {
	
	WPGMZA.ProRectanglePanel = function(element)
	{
		WPGMZA.RectanglePanel.apply(this, arguments);

		if(!WPGMZA.InternalEngine.isLegacy()){
			this.descriptionElement = $(this.element).find('[data-ajax-name="description"]');
			this.initWritersBlock(this.descriptionElement.get(0));
		}
	}
	
	WPGMZA.extend(WPGMZA.ProRectanglePanel, WPGMZA.RectanglePanel);
	
	/* Not Dry at the moment, we need a Pro Feature Panel Module */
	WPGMZA.ProRectanglePanel.prototype.populate = function(data)
	{
		WPGMZA.RectanglePanel.prototype.populate.apply(this, arguments);
		
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