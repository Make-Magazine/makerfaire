/*
 * x-editable input type for a rich text (Paragraph) field.
 *
 * The editor itself is WordPress's TinyMCE (wp.editor), mounted and torn down on the field's
 * shown/hidden events in fields-inline-editable.js. This type only owns the textarea wp.editor
 * attaches to, pulls the latest content out of TinyMCE before a save, and renders the stored
 * value as HTML on redraw so it matches the front end.
 *
 * @class RichText
 * @extends abstractinput
 */
( function ( $ ) {
	"use strict";

	var RichText = function ( options ) {
		this.init( 'richtext', options, RichText.defaults );
	};

	$.fn.editableutils.inherit( RichText, $.fn.editabletypes.abstractinput );

	$.extend( RichText.prototype, {
		render: function () {
			// wp.editor.initialize() targets a textarea by id, so every instance needs a unique one.
			if ( ! this.$input.attr( 'id' ) ) {
				this.$input.attr( 'id', 'gv_richtext_' + $.now() + '_' + Math.floor( Math.random() * 100000 ) );
			}

			// The textarea holds the raw HTML and is hidden by default (see inline-editable.scss) so its
			// source never flashes before TinyMCE mounts or after it is torn down. When the editor
			// runtime is unavailable, this textarea IS the editor, so reveal it. getDefaultSettings is
			// required too: wp.editor.initialize silently no-ops without it (it is printed only by
			// wp_enqueue_editor()'s footer bootstrap), which would otherwise leave no editing surface.
			var wpEditor  = window.wp && window.wp.editor;
			var hasEditor = wpEditor && 'function' === typeof wpEditor.initialize && 'function' === typeof wpEditor.getDefaultSettings;

			if ( ! hasEditor ) {
				this.$input.addClass( 'gv-richtext-visible' );
			}

			this.setClass();
		},

		value2html: function ( value, element ) {
			var $el  = $( element );
			var html = null == value ? '' : value;

			// The Gravity Forms entries table shows a plain-text preview, matching the textarea type.
			if ( $el.parents( 'table.gf_entries' ).length ) {
				$el.text( html );
				return;
			}

			// TinyMCE saves paragraphs as blank-line-separated text (wpautop's inverse), so restore the
			// paragraph markup for the redraw; otherwise multi-paragraph content collapses onto one line
			// until the page reloads. Falls back to rendering the value as-is when autop is unavailable.
			if ( html && window.wp && window.wp.editor && 'function' === typeof window.wp.editor.autop ) {
				html = window.wp.editor.autop( html );
			}

			$el.html( html );
		},

		html2value: function ( html ) {
			return html;
		},

		value2input: function ( value ) {
			this.$input.val( null == value ? '' : value );
		},

		input2value: function () {
			// Flushes TinyMCE's current content into the textarea before x-editable reads it. Falls
			// back to the raw textarea value when the editor never mounted (e.g. wp.editor absent).
			var id     = this.$input.attr( 'id' );
			var editor = ( window.tinymce && id ) ? window.tinymce.get( id ) : null;

			if ( editor ) {
				editor.save();
			}

			var value = this.$input.val();

			// TinyMCE serializes cleared content as a stray <br> or empty <p>. Left as-is, x-editable
			// treats that as a non-empty value and hides its clickable "Empty" placeholder, leaving a
			// blank field with nothing to click. Normalize visually-empty content to a true empty string.
			if ( '' === value.replace( /<br[^>]*>|<\/?p[^>]*>|&nbsp;|\s+/gi, '' ) ) {
				return '';
			}

			return value;
		},

		activate: function () {
			// x-editable calls activate() during form render, before the field's `shown` handler mounts
			// TinyMCE, so there is no editor to focus yet. TinyMCE focuses itself via
			// init_instance_callback; here only the plain-textarea fallback needs focusing.
			$.fn.editabletypes.abstractinput.prototype.activate.call( this );
		}
	} );

	RichText.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults, {
		tpl: '<textarea class="gv-inline-richtext" rows="8"></textarea>',
		inputclass: ''
	} );

	$.fn.editabletypes.richtext = RichText;

}( window.jQuery ) );
