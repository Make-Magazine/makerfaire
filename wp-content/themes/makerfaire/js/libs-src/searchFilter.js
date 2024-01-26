jQuery(document).ready(function(){
	sf_yearbook();
	jQuery(document).on("sf:ajaxfinish", ".searchandfilter", function(){
		sf_yearbook();
	});
	jQuery(document).on("sf:ajaxstart", ".searchandfilter", function() {
		// when sort order is selected, if the sort order is random, refresh the search results
		jQuery('select[name="_sf_sort_order[]"]').on('change', function (e) {
			var valueSelected = this.value;
			if(valueSelected == "rand+desc") {
				e.preventDefault();
				jQuery('.search-filter-results').load(document.URL +  ' .search-filter-results');
			}
		});
	});
});

// this function does all the things we want to have happen to the search and filter pages on load and ajax refresh
function sf_yearbook() {
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
}
