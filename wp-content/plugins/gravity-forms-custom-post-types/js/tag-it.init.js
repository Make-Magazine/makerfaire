
var init_tagit_script = function() {
	/* Temporary fix due to jQuery version issue */
	jQuery.curCSS = jQuery.css;

	jQuery.each( gfcpt_tag_inputs.tag_inputs, function() {
		jQuery( this.input ).tagit( {
			availableTags:      gfcpt_tag_taxonomies[ this.taxonomy ],
			removeConfirmation: true,
			allowSpaces:        true,
			animate:            false
		} );
	} );

};

