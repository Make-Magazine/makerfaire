jQuery(document).ready(function(){
	jQuery('.sf-field-sort_order select.sf-input-select').select2();
	// Listview button
	jQuery(".sf-field-submit input").on('click', function(event){
		event.preventDefault();
		jQuery("body").toggleClass("listview");
	});
	jQuery( ".sf-field-sort_order label .select2-selection" ).prop("title","Sort").tooltip();
	jQuery( ".sf-field-submit" ).prop("title","Change View").tooltip();
	// change the faireName id in the title to reflect the faire we are showing projects for
	if(jQuery(".sf-field-post-meta-faire_information_faire_post .chosen-single span").text() != "All Faires" ) {
		jQuery("#faireName").text(jQuery(".sf-field-post-meta-faire_information_faire_post .chosen-single span").text() + " Maker Faire ");
	} else {
		jQuery("#faireName").text("");
	}

	jQuery(document).on("sf:ajaxfinish", ".searchandfilter", function(){
		var urlParams = new URLSearchParams(window.location.search);
		if(urlParams.get('sort_order') == "rand desc") {
			jQuery('.search-filter-results').load(document.URL +  ' .search-filter-results');
		}
		jQuery('.sf-field-sort_order select.sf-input-select').select2();
		jQuery( ".sf-field-sort_order label .select2-selection" ).prop("title","Sort").tooltip();
		// Listview button
		jQuery(".sf-field-submit input").on('click', function(event){
			event.preventDefault();
			jQuery("body").toggleClass("listview");
		});
		// change the faireName id in the title to reflect the faire we are showing projects for
		if(jQuery(".sf-field-post-meta-faire_information_faire_post .chosen-single span").text() != "All Faires" ) {
		jQuery("#faireName").text(jQuery(".sf-field-post-meta-faire_information_faire_post .chosen-single span").text() + " Maker Faire ");
		} else {
			jQuery("#faireName").text("");
		}
	});
	
});

