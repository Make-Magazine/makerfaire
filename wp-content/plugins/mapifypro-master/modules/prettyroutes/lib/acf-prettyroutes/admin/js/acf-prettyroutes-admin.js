(function( $ ) {
	'use strict';

	/**
	 * Move description label on ACF repeater, from below label to below fields
	 * as this functionality haven't been made by ACF developers
	 * 
	 * https://support.advancedcustomfields.com/forums/topic/repeater-instruction-placement/
	 */
	$( document ).ready(function() {
		const $acf_repeater_rows = $('.acf-field[data-name="acf_prettyroutes_waypoints"] .acf-repeater .acf-table .acf-row');

		$acf_repeater_rows.each(function( index, element ) {
			const $row_fields = $( element ).find('.acf-fields .acf-field');
			
			$row_fields.each(function( index, element ) {
				const $description = $( element ).find('.acf-label .description');
				const $input = $( element ).find('.acf-input');
								
				// add margin to description
				$description.css( 'padding-top', '5px' );

				// move label
				$description.appendTo( $input );
			});
		});		
	});

})( jQuery );
