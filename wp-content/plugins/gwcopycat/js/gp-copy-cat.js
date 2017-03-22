/**
 * GP Copy Cat JS
 */
( function( $ ) {

    window.gwCopyObj = function( formId, fields, overwrite ) {

        var self = this;

        self.formId   = formId;
        self.fields   = fields;
        self.override = overwrite; // do not overwrite existing values when a checkbox field is the copy trigger

        self.init = function() {

            var $formWrapper = $( '#gform_wrapper_{0}'.format( self.formId ) );

            $formWrapper.on( 'click.gpcopycat', '.gwcopy input[type="checkbox"]', function() {
                if( $( this ).is( ':checked' ) ) {
                    self.copyValues( this );
                } else {
                    self.clearValues( this );
                }
            } );

            $formWrapper.on( 'change.gpcopycat', '.gwcopy input:not(:checkbox), .gwcopy textarea, .gwcopy select', function() {
                self.copyValues( this );
            } );

            $formWrapper.find( '.gwcopy' ).find( 'input, textarea, select' ).each( function() {
                if( ! $( this ).is( ':checkbox, :radio' ) ) {
                    self.copyValues( this );
                } else if( $( this ).is( ':checked' ) ) {
                    self.copyValues( this );
                }
            } );

            $formWrapper.data( 'GPCopyCat', self );

        };

        self.copyValues = function( elem, isOverride, forceEmptyCopy ) {

            var fieldId = gf_get_input_id_by_html_id( $( elem ).parents( 'li.gwcopy' ).attr( 'id' ) ),
                fields  = self.fields[ fieldId ];

            isOverride = self.override || isOverride;
            if( typeof forceEmptyCopy == 'undefined' ) {
				forceEmptyCopy = isOverride;
            }

            for( var i = 0; i < fields.length; i++ ) {

                var field             = fields[i],
                    sourceFieldId     = field['source'],
	                targetFieldId     = field['target'],
                    sourceGroup       = self.getFieldGroup( field, 'source' ),
                    targetGroup       = self.getFieldGroup( field, 'target' ),
	                isListToList      = self.isListField( sourceGroup ) && self.isListField( targetGroup ),
	                sourceValues      = self.getGroupValues( sourceGroup, 'source', {
	                	sort:          ! isListToList && self.isListField( targetGroup ) ? self.getGroupValues( targetGroup, 'target', { isListToList: isListToList, sourceInputId: targetFieldId } ) : false,
		                isListToList:  isListToList,
		                sourceInputId: sourceFieldId,
		                targetInputId: targetFieldId
	                } );

                // Add new rows for List field - if - we have more than one value to populate - and - our target is a List field.
                if( self.isListField( targetGroup ) ) {

	                var targetInputIndex = self.getListInputIndex( targetFieldId, true ),
		                targetRowCount   = parseInt( targetFieldId ) == targetFieldId ? targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group' ).length : targetGroup.length,
		                sourceRowCount   = parseInt( sourceFieldId ) == sourceFieldId ? sourceGroup.parents( '.ginput_list' ).find( '.gfield_list_group' ).length : sourceGroup.length,
		                perRow           = targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group:first-child .gfield_list_cell' ).length,
		                rowsRequired     = ( sourceRowCount - targetRowCount ) / ( targetInputIndex.column ? 1 : perRow );

	                if( rowsRequired < 0 && targetGroup.length > 1 ) {
		                // Remove rows from target List field that do not have corresponding source values.
		                targetGroup.each( function() {
			                if( $.inArray( $( this ).val(), sourceValues ) === -1 ) {
			                	$( this ).parents( '.gfield_list_group' ).find( '.delete_list_item' ).click();
			                }
		                } );
	                } else {
		                // Get the last row of the target List field and click the Add button to create additional rows.
		                for( var j = 0; j < rowsRequired; j++ ) {
			                targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group:last-child .add_list_item' ).click();
		                }
	                }

	                // Re-fetch the target group so we'll loop through any newly added inputs.
	                targetGroup = self.getFieldGroup( field, 'target' );

                }

                targetGroup.each( function( i ) {

                    var $targetElem     = $( this ),
                        isCheckable    = $targetElem.is( ':checkbox, :radio' ),
                        hasValue       = isCheckable ? $targetElem.is( ':checked' ) : $targetElem.val(),
                        index          = isListToList ? self.getListInputIndex( $targetElem ) : i,
                        hasSourceValue = isCheckable || sourceValues[ index ] || sourceValues.join( ' ' );

                    // if overwrite is false and a value exists, skip
                    if( ! isOverride && hasValue ) {
                        return true;
                    }

                    // if there is no source value for this element, skip
                    if( ! hasSourceValue && ! forceEmptyCopy ) {
                        return true;
                    }

                    if( isCheckable ) {
                        if( $.inArray( $targetElem.val(), sourceValues ) != -1 ) {
                            $targetElem.prop( 'checked', true );
                        }
                    } else if( targetGroup.length > 1 ) {
                        $targetElem.val( sourceValues[ index ] );
                    }
                    // if there is only one input, join the source values
                    else {
                    	// filter out empty values
                    	sourceValues = sourceValues.filter( function( item, pos ) {
		                    return item != '';
	                    } );
                        $targetElem.val( self.cleanValueByInputType( sourceValues.join( ' ' ), $targetElem.attr( 'type' ) ) );
                    }

                } );

                // force user events to trigger
                if( targetGroup.is( ':checkbox, :radio' ) ) {
                	if( ! isOverride ) {
		                // trigger 'keypress' on all checked checkboxes to trigger applicable conditional logic
		                targetGroup.filter( ':checked' )
	                }
	                targetGroup.keypress();
                } else {
                    targetGroup
                        .change()
                        // @hack trigger chosen:updated on every change since it doesn't "hurt" anything to do so; alternative is checking if chosen is activated
                        .trigger( 'chosen:updated' );
                }

                targetGroup.trigger( 'copy.gpcopycat' );

            }

        };

        /**
         * Clear values when checkbox has been unselected. Only used by checkbox-triggered copies.
         *
         * @param elem
         */
        self.clearValues = function(elem) {

            var fieldId = $(elem).parents('li.gwcopy').attr('id').replace('field_' + self.formId + '_', '');
            var fields = self.fields[fieldId];

            for( var i = 0; i < fields.length; i++ ) {

                var field        = fields[i],
                    sourceValues = [],
                    targetGroup  = self.getFieldGroup( field, 'target' ),
                    sourceGroup  = self.getFieldGroup( field, 'source' );

                if( field.source == fieldId && $( elem ).is( ':checkbox' ) ) {
                	if( self.override ) {
		                targetGroup.prop( 'checked', false );
	                }
                    self.copyValues( elem, true, true );
                    return;
                }

                sourceGroup.each( function( i ) {
                    sourceValues[i] = $(this).val();
                } );

                targetGroup.each( function( i ) {

                    var $targetElem = $( this ),
                        fieldValue  = $targetElem.val(),
                        isCheckable = $targetElem.is( ':checkbox, :radio' ),
                        isCheckbox  = $targetElem.is( ':checkbox' );

                    if( isCheckbox ) {
                        $targetElem.prop( 'checked', $.inArray( fieldValue, sourceValues ) !== -1 );
                    } else if( isCheckable ) {
                        $targetElem.prop( 'checked', false );
                    } else if( fieldValue == sourceValues[i] ) {
                        $targetElem.val( '' );
                    }

                } ).change();

                // remove empty rows from List fields
                if( self.isListField( targetGroup ) ) {
                    targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group:not(:first)' ).each( function() {
                        if( $( this ).find( '.gfield_list_cell input[value!=""]' ).length == 0 ) {
                            $( this ).find( '.delete_list_item' ).click();
                        }
                    } );
                }

            }

        };

        self.cleanValueByInputType = function( value, inputType ) {

            if( inputType == 'number' ) {
                value = gformToNumber( value );
            }

            return value;
        };

        self.getFieldGroup = function( field, groupType ) {

            var rawFieldId      = field[ groupType ],
                fieldId         = parseInt( rawFieldId ),
	            isInputSpecific = fieldId != rawFieldId,
                formId          = field[ groupType + 'FormId' ], // i.e. 'sourceFormId' or 'targetFormId',
                $field          = $( '#field_' + formId + '_' + fieldId ),
                group           = $field.find( 'input[name^="input"]:not( :button ), select[name^="input"], textarea[name^="input"]' ),
	            isListField     = self.isListField( group );

            // Many 3rd parties add additional non-capturable inputs to the List field. Let's filter those out.
            if( isListField ) {
                group = group.filter( '[name="input_{0}[]"]'.format( fieldId ) );
            }

            // Handle input-specific fields (excluding List fields).
            if( isInputSpecific && ! isListField ) {

                var inputId       = rawFieldId.split( '.' )[1],
                    filteredGroup = group.filter( '#input_' + formId + '_' + fieldId + '_' + inputId + ', input[name="input_' + rawFieldId + '"]' );

                // some fields (like email with confirmation enabled) have multiple inputs but the first input has no HTML ID (input_1_1 vs input_1_1_1)
                if( filteredGroup.length <= 0 ) {
                    group = group.filter( '#input_' + formId + '_' + rawFieldId );
                } else {
                    group = filteredGroup;
                }

            }
            // Handle input-specific List fields.
            else if( isInputSpecific && isListField ) {

				group = group.filter( function() {

					var currentListInputIndex = self.getListInputIndex( $( this ) ),
						targetListInputIndex  = self.getListInputIndex( rawFieldId, currentListInputIndex );

					return currentListInputIndex == targetListInputIndex;
				} );

            }

            if( groupType == 'source' && group.is( 'input:radio, input:checkbox' ) ) {
                group = group.filter( ':checked' );
            }

            return group;
        };

        self.getGroupValues = function( group, type, args ) {

        	if( typeof args == 'undefined' ) {
        		args = {};
	        }

        	args = parseArgs( args, {
        		sort:          false,
		        isListToList:  false,
		        sourceInputId: false,
		        targetInputId: false
	        } );

        	var values = [];

	        group.each( function( i ) {

	        	var index = args.isListToList ? self.getListInputIndex( $( this ) ) : i;

	        	// if we're copying - to or from - a list field and we've specified a specific column/row, we only want to get
		        // values from the specified column/row
		        if( self.isListField( group ) && parseInt( args.sourceInputId ) != args.sourceInputId ) {
		            var current = self.getListInputIndex( $( this ) ),
			            source  = self.getListInputIndex( args.sourceInputId, current );
		            if( current != source ) {
		                return true;
		            }
		        }

		        values[ index ] = $( this ).val();

	        } );

	        if( args.sort !== false ) {

		        var sort = args.sort.filter( function( item, pos ) {
			        return args.sort.indexOf( item ) == pos && item != '';
		        } );

	        	var sorted = [];

	        	for( var i = 0; i < sort.length; i++ ) {
					var index = values.indexOf( sort[ i ] );
					if( index !== -1 ) {
						sorted.push( values[ index ] );
						values.splice( index, 1 );
					}
		        }

		        values = sorted.concat( values );

	        }

	        return values;
        };

        self.isListField = function( group ) {
            return group.parents( '.ginput_list' ).length > 0;
        };

        self.getListInputIndex = function( $input, currentInputIndex, returnObject ) {

        	if( typeof currentInputIndex == 'undefined' ) {
        		returnObject = false;
	        } else if( typeof currentInputIndex == 'boolean' ) {
		        returnObject = currentInputIndex;
		        currentInputIndex = false;
	        } else if( typeof returnObject == 'undefined' ) {
        		returnObject = false;
	        }

        	if( typeof $input == 'object' ) {
		        var fieldId = $input.attr( 'name' ).match( /(\d+)/ )[0], // returns '34' from 'input_34[]'
			        $group  = $input.parents( '.gfield_list_group' ),
			        $inputs = $group.find( '[name="input_{0}[]"]'.format( fieldId ) ),
			        $groups = $input.parents( '.gfield_list_container' ).find( '.gfield_list_group' ),
			        column  = $inputs.index( $input ) + 1,
			        row     = $groups.index( $group ) + 1;
	        } else {
				var inputId = $input,
					bits    = inputId.split( '.' ),
					byts    = currentInputIndex ? currentInputIndex.split( '.' ) : [ 1, 1 ],
					column  = bits[1],
					row     = bits[2] ? bits[2] : byts[1];
	        }

	        var inputIndex = column + '.' + row;

            return returnObject ? { index: inputIndex, column: column, row: row } : inputIndex;
        };

	    function parseArgs( args, defaults ) {

		    for( key in defaults ) {
			    if( defaults.hasOwnProperty( key ) && typeof args[ key ] == 'undefined' ) {
				    args[ key ] = defaults[ key ];
			    }
		    }

		    return args;
	    }

        self.init();

    };

} )( jQuery );