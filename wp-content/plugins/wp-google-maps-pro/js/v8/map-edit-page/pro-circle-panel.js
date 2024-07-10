/**
 * @namespace WPGMZA
 * @module ProCirclePanel
 * @requires WPGMZA.CirclePanel
 */
jQuery(function($) {
	
	WPGMZA.ProCirclePanel = function(element)
	{
		WPGMZA.CirclePanel.apply(this, arguments);

		if(!WPGMZA.InternalEngine.isLegacy()){
			this.descriptionElement = $(this.element).find('[data-ajax-name="description"]');
			this.initWritersBlock(this.descriptionElement.get(0));
		}
	}
	
	WPGMZA.extend(WPGMZA.ProCirclePanel, WPGMZA.CirclePanel);

	/* Not Dry at the moment, we need a Pro Feature Panel Module */
	WPGMZA.ProCirclePanel.prototype.populate = function(data)
	{
		WPGMZA.CirclePanel.prototype.populate.apply(this, arguments);
		
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