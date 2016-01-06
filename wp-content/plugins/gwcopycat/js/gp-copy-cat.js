/**
 * GP Copy Cat JS
 */
window.gwCopyObj = function(formId, fields, overwrite ) {

    this._formId = formId;
    this._fields = fields;

    // do not overwrite existing values when a checkbox field is the copy trigger
    this._overwrite = overwrite;

    this.init = function() {

        var copyObj = this;

        jQuery( '#gform_wrapper_' + this._formId + ' .gwcopy input[type="checkbox"]').bind( 'click.gpcopycat', function(){

            if(jQuery(this).is(':checked')) {
                copyObj.copyValues(this);
            } else {
                copyObj.clearValues(this);
            }

        } );

        jQuery( '#gform_wrapper_' + this._formId + ' .gwcopy' ).find( 'input, textarea, select' ).each( function() {
            var isCheckbox = jQuery( this ).is( 'input[type="checkbox"]' );
            if( ! isCheckbox ) {
                copyObj.copyValues( this, true );
            } else if( jQuery( this ).is( ':checked' ) ) {
                copyObj.copyValues( this, true );
            }
        } ).not( 'input[type="checkbox"]' ).bind( 'change.gpcopycat', function() {
            copyObj.copyValues( this, true );
        } );

        jQuery( '#gform_wrapper_' + this._formId ).data( 'GPCopyCat', this );

    };

    this.copyValues = function( elem, isOverride ) {

        var copyObj    = this,
            fieldId    = jQuery(elem).parents('li.gwcopy').attr('id').replace('field_' + this._formId + '_', '' ),
            fields     = this._fields[fieldId],
            isOverride = copyObj._overwrite || isOverride;

        for( var i = 0; i < fields.length; i++ ) {

            var field        = fields[i],
                source       = parseInt( field.source ),
                target       = parseInt( field.target ),
                sourceValues = [],
                sourceGroup  = jQuery( '#field_' + this._formId + '_' + source ).find( 'input, select, textarea' ),
                targetGroup  = jQuery( '#field_' + this._formId + '_' + target ).find( 'input, select, textarea' );

            if( target != field.target ) {
                var targetInputId = field.target.split( '.' )[1];
                // search for field by ID - or - by name attribute
                targetGroup = targetGroup.filter( '#input_' + this._formId + '_' + target + '_' + targetInputId + ', input[name="input_' + field.target + '"]' );
            }

            if( source != field.source ) {

                var sourceInputId       = field.source.split( '.' )[1],
                    filteredSourceGroup = sourceGroup.filter( '#input_' + this._formId + '_' + source + '_' + sourceInputId + ', input[name="input_' + field.source + '"]' );

                // some fields (like email with confirmation enabled) have multiple inputs but the first input has no HTML ID (input_1_1 vs input_1_1_1)
                if( filteredSourceGroup.length <= 0 ) {
                    sourceGroup = sourceGroup.filter( '#input_' + this._formId + '_' + source );
                } else {
                    sourceGroup = filteredSourceGroup;
                }
            }

            if( sourceGroup.is( 'input:radio, input:checkbox' ) ) {
                sourceGroup = sourceGroup.filter( ':checked' );
            }

            sourceGroup.each( function( i ) {
                sourceValues[i] = jQuery(this).val();
            } );

            targetGroup.each(function(i){

                var targetElem     = jQuery( this ),
                    isCheckbox     = targetElem.is( ':checkbox' ),
                    hasValue       = isCheckbox ? targetElem.is( ':checked' ) : targetElem.val(),
                    hasSourceValue = isCheckbox || sourceValues[i] || sourceValues.join( ' ' );

                // if overwrite is false and a value exists, skip
                if( ! isOverride && hasValue ) {
                    return true;
                }

                // if there is no source value for this element, skip
                if( ! hasSourceValue ) {
                    return true;
                }

                if( isCheckbox ) {
                    if( jQuery.inArray( targetElem.val(), sourceValues ) != -1 ) {
                        targetElem.prop( 'checked', true );
                    }
                } else if( targetGroup.length > 1 ) {
                    targetElem.val( sourceValues[i] );
                }
                // if there is only one input, join the source values
                else {
                    targetElem.val( copyObj.cleanValueByInputType( sourceValues.join( ' ' ), targetElem.attr( 'type' ) ) );
                }

            } ).change();

        }

    };

    this.clearValues = function(elem) {

        var fieldId = jQuery(elem).parents('li.gwcopy').attr('id').replace('field_' + this._formId + '_', '');
        var fields = this._fields[fieldId];

        for( var i = 0; i < fields.length; i++ ) {

            var field        = fields[i],
                source       = parseInt( field.source ),
                target       = parseInt( field.target ),
                sourceValues = [],
                targetGroup  = jQuery( '#field_' + this._formId + '_' + target ).find( 'input, select, textarea' ),
                sourceGroup  = jQuery( '#field_' + this._formId + '_' + source ).find( 'input, select, textarea' );

            if( target != field.target ) {
                var targetInputId = field.target.split( '.' )[1];
                targetGroup = targetGroup.filter( '#input_' + this._formId + '_' + target + '_' + targetInputId + ', input[name="input_' + field.target + '"]' );
            }

            if( source != field.source ) {

                var sourceInputId       = field.source.split( '.' )[1],
                    filteredSourceGroup = sourceGroup.filter( '#input_' + this._formId + '_' + source + '_' + sourceInputId + ', input[name="input_' + field.source + '"]' );

                if( filteredSourceGroup.length <= 0 ) {
                    sourceGroup = sourceGroup.filter( '#input_' + this._formId + '_' + source );
                } else {
                    sourceGroup = filteredSourceGroup;
                }
            }

            if( sourceGroup.is( 'input:radio, input:checkbox' ) ) {
                sourceGroup = sourceGroup.filter( ':checked' );
            }

            sourceGroup.each( function( i ) {
                sourceValues[i] = jQuery(this).val();
            } );

            targetGroup.each( function( i ) {

                var $targetElem = jQuery( this ),
                    fieldValue  = $targetElem.val(),
                    isCheckbox  = $targetElem.is( ':checkbox' );

                if( isCheckbox ) {
                    $targetElem.prop( 'checked', false );
                } else if( fieldValue == sourceValues[i] ) {
                    $targetElem.val( '' );
                }

            } ).change();

        }

    };

    this.cleanValueByInputType = function( value, inputType ) {

        if( inputType == 'number' ) {
            value = gformToNumber( value );
        }

        return value;
    };

    this.init();

};