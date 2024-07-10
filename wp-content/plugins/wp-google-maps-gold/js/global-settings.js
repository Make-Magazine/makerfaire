jQuery(function($) {
	
	function onNearVicinityModuleChanged(event)
	{
		var useLegacy = $("input[name='marker_separator_use_legacy_module']").prop("checked");
		
		if(useLegacy)
		{
			$(".wpgmza-marker-separator-legacy-setting").show();
			$(".wpgmza-marker-separator-modern-setting").hide();
		}
		else
		{
			$(".wpgmza-marker-separator-legacy-setting").hide();
			$(".wpgmza-marker-separator-modern-setting").show();
		}
	}
	
	var widget;
	
	widget = new WPGMZA.RadiosRatingWidget();
	$("input[name='marker_rating_widget_style'][value='radios']").after(widget.element);
	
	widget = new WPGMZA.GradientRatingWidget();
	$("input[name='marker_rating_widget_style'][value='gradient']").after(widget.element);
	
	widget = new WPGMZA.StarsRatingWidget();
	$("input[name='marker_rating_widget_style'][value='stars']").after(widget.element);
	
	widget = new WPGMZA.ThumbsRatingWidget();
	$("input[name='marker_rating_widget_style'][value='thumbs']").after(widget.element);
	
	onNearVicinityModuleChanged();
	
	$("input[name='marker_separator_use_legacy_module']").on("change", onNearVicinityModuleChanged);
	
});