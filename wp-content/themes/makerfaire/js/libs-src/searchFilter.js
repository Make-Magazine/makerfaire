jQuery(document).ready(function(){
	jQuery('.sf-field-sort_order select.sf-input-select').select2();
	// behold as I transform a reset button into a button of my own
	jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').removeClass("search-filter-reset");
	jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').attr("type", "button");
	jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').on('click', function(event){
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
		// behold as I transform a reset button into a button of my own
		jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').removeClass("search-filter-reset");
		jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').attr("type", "button");
		jQuery('[data-sf-field-input-type="button"] input[name="_sf_reset"]').on('click', function(event){
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
	});

});

