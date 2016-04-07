/**
 * GP Copy Cat JS
 */
( function( $ ) {

    window.gwCopyObj = function(formId, fields, overwrite ) {

        this._formId = formId;
        this._fields = fields;

        // do not overwrite existing values when a checkbox field is the copy trigger
        this._overwrite = overwrite;

        this.init = function() {

            var copyObj = this;

            $( '#gform_wrapper_' + this._formId + ' .gwcopy input[type="checkbox"]').bind( 'click.gpcopycat', function(){

                if($(this).is(':checked')) {
                    copyObj.copyValues(this);
                } else {
                    copyObj.clearValues(this);
                }

            } );

            $( '#gform_wrapper_' + this._formId + ' .gwcopy' ).find( 'input, textarea, select' ).each( function() {
                var isCheckbox = $( this ).is( 'input[type="checkbox"]' );
                if( ! isCheckbox ) {
                    copyObj.copyValues( this );
                } else if( $( this ).is( ':checked' ) ) {
                    copyObj.copyValues( this );
                }
            } ).not( 'input[type="checkbox"]' ).bind( 'change.gpcopycat', function() {
                copyObj.copyValues( this );
            } );

            $( '#gform_wrapper_' + this._formId ).data( 'GPCopyCat', this );

        };

        this.copyValues = function( elem, isOverride ) {

            var copyObj    = this,
                fieldId    = $(elem).parents('li.gwcopy').attr('id').replace('field_' + this._formId + '_', '' ),
                fields     = this._fields[fieldId],
                isOverride = copyObj._overwrite || isOverride;

            for( var i = 0; i < fields.length; i++ ) {

                var field        = fields[i],
                    sourceValues = [],
                    sourceGroup  = this.getFieldGroup( field, 'source' ),
                    targetGroup  = this.getFieldGroup( field, 'target' );

                sourceGroup.each( function( i ) {
                    sourceValues[i] = $( this ).val();
                } );

                targetGroup.each(function(i){

                    var targetElem     = $( this ),
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
                        if( $.inArray( targetElem.val(), sourceValues ) != -1 ) {
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

            var fieldId = $(elem).parents('li.gwcopy').attr('id').replace('field_' + this._formId + '_', '');
            var fields = this._fields[fieldId];

            for( var i = 0; i < fields.length; i++ ) {

                var field        = fields[i],
                    sourceValues = [],
                    targetGroup  = this.getFieldGroup( field, 'target' ),
                    sourceGroup  = this.getFieldGroup( field, 'source' );

                sourceGroup.each( function( i ) {
                    sourceValues[i] = $(this).val();
                } );

                targetGroup.each( function( i ) {

                    var $targetElem = $( this ),
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

        this.getFieldGroup = function( field, groupType ) {

            var rawFieldId = field[ groupType ],
                fieldId    = parseInt( rawFieldId ),
                formId     = field[ groupType + 'FormId' ], // i.e. 'sourceFormId' or 'targetFormId',
                group      = $( '#field_' + formId + '_' + fieldId ).find( 'input, select, textarea' );

            // if input-specific
            if( fieldId != rawFieldId ) {

                var inputId       = rawFieldId.split( '.' )[1],
                    filteredGroup = group.filter( '#input_' + formId + '_' + fieldId + '_' + inputId + ', input[name="input_' + rawFieldId + '"]' );

                // some fields (like email with confirmation enabled) have multiple inputs but the first input has no HTML ID (input_1_1 vs input_1_1_1)
                if( filteredGroup.length <= 0 ) {
                    group = group.filter( '#input_' + formId + '_' + rawFieldId );
                } else {
                    group = filteredGroup;
                }
            }

            if( group.is( 'input:radio, input:checkbox' ) ) {
                group = group.filter( ':checked' );
            }

            return group;
        };

        this.init();

    };

} )( jQuery );