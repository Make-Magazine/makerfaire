function bgSize($el, cb){ // is this function cool enough to become universal.js?
    jQuery('<img />')
        .load( function(){ cb( this.width, this.height ); } )
        .attr( 'src', $el.css( 'background-image' ).match(/^url\("?(.+?)"?\)$/)[1] );
}
