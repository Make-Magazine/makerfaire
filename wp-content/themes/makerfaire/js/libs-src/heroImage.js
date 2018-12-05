function bgSize($el, cb){
    jQuery('<img />')
        .load( function(){ cb( this.width, this.height ); } )
        .attr( 'src', $el.css( 'background-image' ).match(/^url\("?(.+?)"?\)$/)[1] );
}

jQuery( document ).ready(function(){
   if( jQuery( '.hero-img' ).length ){
		bgSize( jQuery( '.hero-img' ), function( width, height ){
			if( height > 450 ) {
				jQuery('.hero-img').css( "height", "450px" );
			} else if( height > 320 ){
				jQuery( '.hero-img' ).css( "height", height + "px" );
			}
		});
   }
});