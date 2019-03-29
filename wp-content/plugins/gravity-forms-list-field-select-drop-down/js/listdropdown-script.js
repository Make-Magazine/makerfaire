var text_no_results = itsg_gf_listdropdown_js_settings.text_no_results;
var text_placeholder = itsg_gf_listdropdown_js_settings.text_placeholder;
var text_no_results_other = itsg_gf_listdropdown_js_settings.text_no_results_other;
var text_placeholder_other = itsg_gf_listdropdown_js_settings.text_placeholder_other;

function itsg_gf_listdropdown_make_chosen( select, placeholder_text, no_results_text ) {
	var options = gform.applyFilters( 'gform_chosen_options', {
		width: '100%',
		placeholder_text: placeholder_text,
		no_results_text: no_results_text
		}, jQuery( '.ginput_list' ).find( 'select.chosen' ) );

	// set first value as selected if none are
	if ( ! select.find( 'option:selected' ).length ) {
		select.val( select.find( 'option:first' ).val() );
	}

	select.chosen( options );

	itsg_gf_listdropdown_set_width_function( select );
	/*var column =  select.closest( 'td' );
	var chosen_container = select.next( '.chosen-container' );
	var chosen_container_width = chosen_container.width();console.log( column.siblings().length );
	if ( chosen_container_width > 0 && column.siblings().length > 1 ) {
		column.css( {
			'width' : chosen_container_width + 'px',
		});
		chosen_container.css( {
			'width' : chosen_container_width + 'px',
			'display' : 'block',
		});
	}*/
}

function itsg_gf_listdropdown_newrow_function( self ) {
	var new_row = jQuery( self ).parents( 'tr.gfield_list_group' ).next( 'tr.gfield_list_group' );
	new_row.find( '.gfield_list_cell > select option:first-of-type' ).prop( 'selected', true ); // select the first item in the select list
	new_row.find( '.chosen-container' ).remove(); // remove existing chosen container - ready for it to be recreated
	new_row.find( 'select.chosen' ).show(); // make sure the select is displayed - ready for it to be hidden again when chosen runs

	new_row.find( 'select.chosen:not(.other)' ).each( function(){
		var select = jQuery( this );
		itsg_gf_listdropdown_make_chosen( select, text_placeholder, text_no_results );
	});

	new_row.find( 'select.chosen.other' ).each( function(){

		var select = jQuery( this );
		itsg_gf_listdropdown_make_chosen( select, text_placeholder_other, text_no_results_other );


	});

	new_row.find( 'select' ).on( 'change', function() {
	  jQuery(this).keyup();
	})

	itsg_gf_listdropdown_set_height_function();
	//itsg_gf_listdropdown_set_width_function();
}

function itsg_gf_listdropdown_set_height_function() {
	var height = jQuery( 'select.chosen' ).parents( 'tr' ).find( 'input' ).first().innerHeight();
	if ( height < 20 ) {
		var height = jQuery( '.gfield input' ).first().innerHeight();
		if ( height < 20 ) {
			setTimeout(function(){
				var height = jQuery( '.gfield input' ).first().innerHeight();
				if ( height < 20 ) {
					var height = 34;
				}
				jQuery ( '.ginput_list .chosen-container-single .chosen-single' ).height( height + 'px');
			}, 500);
		}
	}
	jQuery ( '.ginput_list .chosen-container-single .chosen-single' ).height( height + 'px');
}

function itsg_gf_listdropdown_set_width_function( select ) {

	/*if ( '1' != itsg_gf_listdropdown_js_settings.is_entry_detail ) {
		jQuery( '.ginput_list' ).each( function(){
			var chosen_container = jQuery( this ).find( '.gfield_list_cell .chosen-container' );
			chosen_container.each( function() {
				var column =  jQuery( this ).closest( 'td' );
				var chosen_container_width = jQuery( this ).width();
				if ( chosen_container_width > 0 ) { console.log( chosen_container_width );
					column.css( {
						'width' : chosen_container_width + 'px',
					});
					jQuery( this ).css( {
						'width' : chosen_container_width + 'px',
						'display' : 'block',
					});
				}
			});
		});
	}*/

	var column =  select.closest( 'td' );
	var chosen_container = select.next('.chosen-container');
	var chosen_container_width = chosen_container.width();
	if ( chosen_container_width > 0 ) {
		column.css( {
			'width' : chosen_container_width + 'px',
		});
		chosen_container.css( {
			'width' : chosen_container_width + 'px',
			'display' : 'block',
		});
	}

/*	if ( '1' != itsg_gf_listdropdown_js_settings.is_entry_detail ) {
		jQuery( '.ginput_list' ).each( function(){
			var chosen_container = jQuery( this ).find('.gfield_list_cell .chosen-container');
			chosen_container.each( function() {
				var column =  jQuery( this ).closest( 'td' );console.log(column);
				var chosen_container_width = jQuery( this ).width();
				if ( chosen_container_width > 0 ) {console.log(chosen_container_width);
					column.css( {
						'width' : chosen_container_width + 'px',
					});
					chosen_container.css( {
						'width' : chosen_container_width + 'px',
						'display' : 'block',
					});
				}
			});
		});
	}*/

/*	if ( '1' != itsg_gf_listdropdown_js_settings.is_entry_detail ) {
		jQuery('.ginput_list').each( function(){
			var width_total = 0;
			//var width_max = 0;
			var count = 0;
			var chosen_container = jQuery( this ).find('.gfield_list_cell .chosen-container');
			var chosen_container_count = chosen_container.length;

			chosen_container.each( function() {
				var column = jQuery( this ).closest( 'td' );
				if ( column.siblings().length > 1 ) {
					//jQuery( this ).width( 'auto' );
					var width = jQuery( this ).width();
					width_total += jQuery( this ).width();
					//width_max = width > width_max ? width : width_max;
					count++;
					if ( !--chosen_container_count ) {
						var avg_width = width_total / count;
						//var column_width = column.width();console.log(column_width);
						//var width = width_max > avg_width ? column_width : avg_width;
						if ( avg_width > 0  ) {
							chosen_container.width( avg_width + 'px' );
							column.width( avg_width + 'px' );
						}
					}
				}
			});
		});
	}*/
	/*
	jQuery('.ginput_list').find('.gfield_list_cell .chosen-container').each( function( index, value ){
		num += jQuery(this).width();
		count++;
	});
	var avg_width = num/count;
	jQuery('.ginput_list').find('.gfield_list_cell .chosen-container').each( function( index, value ){
		jQuery(this).width(avg_width + 'px');
		var column =  jQuery(this).closest( 'td' );
		column.width(avg_width + 'px');
	});*/
}

if ( '1' == itsg_gf_listdropdown_js_settings.is_entry_detail ) {
	// runs the main function when the page loads -- entry editor -- configures any existing upload fields
	jQuery(document).ready( function($) {

		jQuery( '.ginput_list' ).find( 'select.chosen:not(.other)' ).each( function( index, value ){
			var select = jQuery( this );

			itsg_gf_listdropdown_make_chosen( select, text_placeholder, text_no_results );
		});

		jQuery( '.ginput_list' ).find( 'select.chosen.other' ).each( function( index, value ){
			var select = jQuery( this );

			itsg_gf_listdropdown_make_chosen( select, text_placeholder_other, text_no_results_other );
		});

		jQuery( '.ginput_list' ).find( 'select').on('change', function() {
		  jQuery( this ).keyup();
		})

		itsg_gf_listdropdown_set_height_function();
		//itsg_gf_listdropdown_set_width_function();

		// when field is added to repeater, runs the main function passing the current row
		jQuery( '.gfield_list' ).on( 'click', '.add_list_item', function(){
			itsg_gf_listdropdown_newrow_function( jQuery(this) );
		});
	});
} else {
	// runs the main function when the page loads -- front end forms -- configures any existing upload fields
	jQuery( document ).bind( 'gform_post_render', function( $ ) {

		jQuery( '.ginput_list' ).find( 'select.chosen:not(.other)' ).each( function( index, value ){
			var select = jQuery( this );

			itsg_gf_listdropdown_make_chosen( select, text_placeholder, text_no_results );
		});

		jQuery( '.ginput_list' ).find( 'select.chosen.other' ).each( function( index, value ){
			var select = jQuery( this );

			itsg_gf_listdropdown_make_chosen( select, text_placeholder_other, text_no_results_other );
			itsg_gf_listdropdown_set_height_function( select );

		});

		jQuery( '.ginput_list' ).find( 'select').on('change', function() {
		  jQuery(this).keyup();
		})



		// when field is added to repeater, runs the main function passing the current row
		jQuery( '.gfield_list' ).on( 'click', '.add_list_item', function(){
			itsg_gf_listdropdown_newrow_function( jQuery( this ) );
		});
	});
}