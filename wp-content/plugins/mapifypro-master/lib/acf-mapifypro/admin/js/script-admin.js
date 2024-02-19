;(function($) {
	$( document ).ready(function($) {
		// inject note below location-tag meta box
		$('#tagsdiv-location-tag .inside:first').append(
			'<p>You may assign filters to this map, which will allow your users to filter location results via a dropdown. ' + 
			'When you create specific locations within this map, you can then assign these filters to the location.</p>'
		);
		
		// search the map locations results on key up
		$('.acf-field[data-type="mapify_map_locations_relationship"]').on('keyup', '.filter.-search input', function(){
			let query = $(this).val();
			let filteredList = $(".acf-field[data-type='mapify_map_locations_relationship'] .acf-bl.list.values-list li span:mapifyContains('" + query + "')");

			$(".acf-field[data-type='mapify_map_locations_relationship'] .acf-bl.list.values-list li").hide();
			filteredList.parent('li').show();	
		});

		// search the right side of ACF relationship field 
		$('[id^=acf-mapify_acf_group_] .acf-field-relationship, [id^=acf-prettyroutes_acf_group_] .acf-field-relationship')
			.on('keyup', '.filter.-search input', function(){
				let query = $(this).val();
				let $container = $(this).closest('.acf-field-relationship');
				let $filteredList = $container.find(".acf-bl.list.values-list li span:mapifyContains('" + query + "')");

				$container.find(".acf-bl.list.values-list li").hide();
				$filteredList.parent('li').show();	
			});
	});	
})(jQuery);

// custom jquery function for insensitive :contains
jQuery.expr[':'].mapifyContains = function(a, i, m) {
	return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};