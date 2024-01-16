jQuery(document).ready(function(){
	jQuery('[data-sf-field-input-type="button"] .search-filter-reset').on('click', function(){
		jQuery("form.searchandfilter").toggleClass("minimized");
		if(jQuery(this).attr('value') == "+") {
			jQuery(this).attr('value', " ");
		} else {
			jQuery(this).attr('value', "+");
		}
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

	jQuery(document).on("sf:ajaxfinish", ".searchandfilter", function(){
		jQuery('.sf-field-sort_order select.sf-input-select').select2();
	});

	jQuery(document).ready(function(){
		jQuery('.sf-field-sort_order select.sf-input-select').select2();
	});
});

