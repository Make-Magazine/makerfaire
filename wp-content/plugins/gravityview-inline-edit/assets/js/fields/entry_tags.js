/**
 Tag editable input.
 Internally value stored as `select one,select two`

 @class tag
 @extends abstractinput
 @final
 **/
 (function ( $ ) {
	"use strict";

	var Entry_Tags = function ( options ) {
		this.init( 'entry_tags', options, Entry_Tags.defaults );
		this.sourceData = null;
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit( Entry_Tags, $.fn.editabletypes.abstractinput );
	$.extend( Entry_Tags.prototype, {

		/**
		 * Renders input from tpl
		 */
		render: function () {
			this.$input = this.$tpl.find( 'input' );
			this.sourceData = this.options.source;
		},


		/**
		 * Default method to show value in element. Can be overwritten by display option.
		 *
		 **/
		value2html: function ( value, element ) {
			var $el = $( element );
			if ( !value ) {
				$el.empty();
				return;
			}


			if ( 'string' === typeof value ) {
				value = JSON.parse(value);
			}

			var value_html = '';
			$.map( value, function ( val, index ) {
				var entry_link = $($el).data('entry-link');
				entry_link = entry_link.replace('{replace_value}', val.label);
				value_html+= '<a class="gf_tag_field" href="'+entry_link+'" style="background-color: '+val.color+'; color: '+val.text_color+';">'+val.label+'</a> ';
			} );

			$el.html(value_html);
			
			
		},


        str2value: function ( str ) {
			if(typeof str === 'string'){
				return str;
			}
			return JSON.stringify(str);
        },


		/**
		 * set the inputs
		 */
		value2input: function ( value ) {
			var $tagField = this.$input;
			$tagField.val(value);
			
			// Needed for tagify To take time to identify the correct tags for each field in inline edit mode.
			setTimeout(() => {
				$(document).trigger('gk_tag_gravityedit',[JSON.parse(value)]);
			}, 10);
		}
	} );

	Entry_Tags.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults, {
		inputclass: ''
	} );

	$.fn.editabletypes.entry_tags = Entry_Tags;

}( window.jQuery ));
