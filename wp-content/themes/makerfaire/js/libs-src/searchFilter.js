jQuery(document).ready(function(){
	jQuery(".search-filter-reset").on('click', function(){
		jQuery("form.searchandfilter").toggleClass("minimize");
	});
	jQuery(".sf-field-submit input").on('click', function(event){
		event.preventDefault();
		jQuery(".search-filter-results").toggleClass("listview");
		jQuery(this).toggleClass("gridview");
		if(jQuery(this).attr('value') == "List View") {
			jQuery(this).attr('value', "Grid View");
		} else {
			jQuery(this).attr('value', "List View");
		}
	});
});

