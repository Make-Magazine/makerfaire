/**
 *
 * The jquery required for the shortcode MCE Form
 *
 * @TODO Cleanup the code and turn it into an array of checkboxes and values
 *
 * @author Mat Lipe <mat@matlipe.com>
 */
var output = '[simple-links';

var myObj = {
	local_ed : 'ed', //A Var for setting a global object to use

	//The function with sends the new output back to the editor and closes the popup
	insert : function(){

		tinyMCEPopup.execCommand( 'mceReplaceContent', false, output );

		// Return
		tinyMCEPopup.close();

	}
};

//Initiate the object This is required
tinyMCEPopup.onInit.add( myObj.init, myObj );

//The Jquery which grabs the form data
jQuery( document ).ready( function( $ ){

	var fields = ['count', 'orderby', 'order', 'title'];

	//Generate the Code
	$( '#generate' ).click( function(){

		//Go through the standard fields
		for( var i = 0; i < fields.length; i++ ){
			//Add the standard fields to the output if they have a value
			if( $( '#' + fields[i] ).val() != '' ){
				output += ' ' + fields[i] + '="' + $( '#' + fields[i] ).val() + '"';
			}
		}

		//Add the checked categories
		var cats = '';
		$( '.cat:checked' ).each( function(){
			if( cats == '' ){
				cats = ' category="';
				cats += $( this ).val();
			} else {
				cats += ',' + $( this ).val();
			}
		} );

		//Close the attribute and add it ot the shortcode
		if( cats != '' ){
			cats += '"';
			output += cats;
		}

		//Add the additional fields
		var addFields = '';
		$( '.additional:checked' ).each( function(){
			if( addFields == '' ){
				addFields = ' fields="';
				addFields += $( this ).val();
			} else {
				addFields += ',' + $( this ).val();
			}
		} );
		//Close the fields
		if( addFields != '' ){
			addFields += '"';
			output += addFields;
		}

		//Add the separator
		if( $( '#separator' ).val() != '-' ){
			output += ' separator="' + $( '#separator' ).val() + '"';
		}

		//Add the image to the shortcode
		if( $( '#show_image' ).is( ':checked' ) ){
			output += ' show_image="true"';
			if( $( '#image-size' ).val() != '' ){
				output += ' image_size="' + $( '#image-size' ).val() + '"';
			}

			//Add the show Image only
			if( $( '#show_image_only' ).is( ':checked' ) ){
				output += ' show_image_only="true"';
			}

		}

		//Add the description to the shortcode
		if( $( '#description' ).is( ':checked' ) ){
			output += ' description="true"';
		}

		//Add the description to the shortcode
		if( $( '#description-formatting' ).is( ':checked' ) ){
			output += ' show_description_formatting="true"';
		}

		//Add the line break to the code
		if( $( '#line_break' ).is( ':checked' ) ){
			output += ' remove_line_break="true"';
		}

		//Add the child categories to the shortcode
		if( $( '#child-categories' ).is( ':checked' ) ){
			output += ' include_child_categories="true"';
		}

		//add custom values here by using a $(document).on('simple-links-js-form-output', function(o){});
		$( document ).trigger( 'simple-links-js-form-output', [output] );

		output += ']';

		//Finish out the shortcode
		if( Simple_Links_Config.is_visual_shortcodes_enabled ){
			output = "[embed]" + output + "[/embed]"
		}

		//Send the shortcode back to the editor
		myObj.insert();
	} );

} );
