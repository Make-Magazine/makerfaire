function em_setup_selectize( container ){
	container = jQuery(container); // in case we were given a dom object
	// Selectize General
	container.find('select:not([multiple]).em-selectize, .em-selectize select:not([multiple])').selectize();
	container.find('select[multiple].em-selectize, .em-selectize select[multiple]').selectize({
		hideSelected : false,
		plugins: ["remove_button", 'click2deselect'],
		diacritics : true,
		render: {
			item: function (item, escape) {
				return '<div class="item"><span>' + item.text.replace(/^\s+/i, '') + '</span></div>';
			},
			option : function (item, escape) {
				let html = '<div class="option"';
				if( 'data' in item ){
					// any key/value object pairs wrapped in a 'data' key within JSON object in the data-data attribute is added automatically as a data-key="value" attribute
					Object.entries(item.data).forEach( function( item_data ){
						html += ' data-'+ escape(item_data[0]) + '="'+ escape(item_data[1]) +'"';
					});
				}
				html +=	'>';
				if( this.$input.hasClass('checkboxes') ){
					html += item.text.replace(/^(\s+)?/i, '$1<span></span> ');
				}else{
					html += item.text;
				}
				html += '</div>';
				return html;
			},
			optgroup : function (item, escape) {
				let html = '<div class="optgroup" data-group="' + escape(item.label) + '"';
				if( 'data' in item ){
					// any key/value object pairs wrapped in a 'data' key within JSON object in the data-data attribute is added automatically as a data-key="value" attribute
					Object.entries(item.data).forEach( function( item_data ){
						html += ' data-'+ escape(item_data[0]) + '="'+ escape(item_data[1]) +'"';
					});
				}
				html +=	'>';
				return html + item.html + '</div>';
			}

		},
	});
	container.find('.em-selectize.always-open').each( function(){
		//extra behaviour for selectize "always open mode"
		if( 'selectize' in this ){
			let s = this.selectize;
			s.open();
			s.advanceSelection = function(){}; // remove odd item shuffling
			s.setActiveItem = function(){}; // remove odd item shuffling
			// add event listener to fix remove button issues due to above hacks
			this.selectize.$control.on('click', '.remove', function(e) {
				e.preventDefault();
				if (s.isLocked) return;
				var $item = jQuery(e.currentTarget).parent();
				s.removeItem($item.attr('data-value'));
				s.refreshOptions();
				return false;
			});

		}
	});
	jQuery(document).triggerHandler('em_selectize_loaded', [container]);
}