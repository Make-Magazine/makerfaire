jQuery(document).ready(function($){
	var toggleEnableMapLocationFields = acf.getField( 'mapify_acf_field_619f08c91a57a' );
	var isEnableMapLocationFields     = toggleEnableMapLocationFields.val();

	// Hide map location field on default. 
	// Give a time to all map-location fields to load first before hide them.
	if ( ! isEnableMapLocationFields ) {
		setTimeout(function(){
			hideMapLocationFields();
		}, 100 );	
	}

	// On change field `Use this blog post as a MapifyPro map location`
	toggleEnableMapLocationFields.on( 'change', function( e ){
		isEnableMapLocationFields = toggleEnableMapLocationFields.val();

		if ( isEnableMapLocationFields ) {
			showMapLocationFields();
		} else {
			hideMapLocationFields();
		}
	} );

	function showMapLocationFields() {
		$( '#acf-mapify_acf_group_62c79b2459423' ).show();
		$( '#acf-mapify_acf_group_604cde8eca652' ).show();
		$( '#acf-mapify_acf_group_604ce142884e6' ).show();
		$( '#acf-mapify_acf_group_604ce1e87a9ec' ).show();
	}

	function hideMapLocationFields() {
		$( '#acf-mapify_acf_group_62c79b2459423' ).hide();
		$( '#acf-mapify_acf_group_604cde8eca652' ).hide();
		$( '#acf-mapify_acf_group_604ce142884e6' ).hide();
		$( '#acf-mapify_acf_group_604ce1e87a9ec' ).hide();
	}
} );