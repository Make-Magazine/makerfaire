;(function($) {
	var $doc = $( document );
	var $window = $( window )

	$doc.ready(function() {
		/**
		 * Make sure we're on multimaps mode
		 */
		if ( ! $( ".mpfy-multi-map" ).length ) {
			return;
		}

		/**
		 * Run each maps
		 */
		$( ".mpfy-multi-map" ).each(function( index ) {
			var MultiMap = new MpfyMultiMap( $( this ) );
			MultiMap.on_ready();			
		});

		/**
		 * Dropdown toggle
		 */
		$('.mpfy-multi-map-dropdown-list-wrapper').on('click', function(){
			$(this).parents('.mpfy-multi-map-dropdown').toggleClass('show-list');
		});

		/**
		 * Hide all dropdown on click elsewhere
		 */
		$doc.on('click touchstart', function (e) {
			if ( ! $(e.target).parents().addBack().is('.mpfy-multi-map-dropdown-list-wrapper') ) {
				$(".mpfy-multi-map-dropdown").removeClass("show-list");
			}
		});

		/**
		 * On select dropdown map item
		 */
		$doc.on('click', '.mpfy-multi-map-dropdown ul li a', function(e) {
			e.preventDefault();
			
			var $container = $(this).parents('.mpfy-multi-map');
			var MultiMap   = new MpfyMultiMap( $container );
			var text       = $(this).text();
			var map_id     = $(this).data('target');

			$(this).closest('.mpfy-multi-map-dropdown').find('li').removeClass('current');
			$(this).closest('li').addClass('current');
			
			// set selected map title
			MultiMap.set_selected_dropdown_item( text );

			// show selected map
			MultiMap.show_selected_map( map_id );

			// reset current displayed map location
			$doc.trigger('mapify.action.setMapTag', {
				mapId: map_id,
				value: '0'
			});

			// trigger window resize to reset some elements sizes and positions
			$window.trigger('resize');
		});		
	});

	/**
	 * Reset the mpfy-multi-map-dropdown position on window resize and orientationchange
	 */
	$window.on( 'resize orientationchange', function() {
		$( ".mpfy-multi-map" ).each(function( index ) {
			var MultiMap = new MpfyMultiMap( $( this ) );
			MultiMap.on_resize();			
		});		
	});

})(jQuery);

/**
 * Class MpfyMultiMap
 * Responsible for managing each Multi Map element.
 * 
 * @since 4.0.0
 */
 class MpfyMultiMap {	

	/**
	 * The constructor
	 * 
	 * @param object el The current multi map element.
	 * 
	 * @return object This object.
	 */
	constructor ( el ) {
		this.el       = el;
		this.dropdown = this.el.find('.mpfy-multi-map-dropdown');

		return this;
	}

	/**
	 * Initial action after all elements ready
	 * 
	 * @return void
	 */
	on_ready() {
		var $selected_list = this.el.find('.mpfy-multi-map-dropdown ul li.current a');
		var text           = $selected_list.text();
		var map_id         = $selected_list.data('target');
		var this_class     = this;

		/**
		 * Set mpfy-multi-map-dropdown position
		 */
		setTimeout(function(){
			this_class.set_multi_map_dropdown_position( map_id );
		}, 100);		

		/**
		 * Set initial selected dropdown item
		 */
		this.set_selected_dropdown_item( text );			
	}

	/**
	 * On window resize
	 * 
	 * @return void
	 */
	on_resize() {
		var $selected_list = this.el.find('.mpfy-multi-map-dropdown ul li.current a');
		var map_id         = $selected_list.data('target');
		var this_class     = this;

		setTimeout(function(){
			this_class.set_multi_map_dropdown_position( map_id );
		}, 100);
	}

	/**
	 * Set selected dropdown item
	 * 
	 * @param string text
	 */
	set_selected_dropdown_item( text ) {
		this.el.find('.mpfy-multi-map-dropdown-list-current').text( text );
	}

	/**
	 * Show selected map
	 * 
	 * @param int map_id 
	 */
	show_selected_map( map_id ) {
		this.el.find('.mpfy-multi-map-item').not('.mpfy-' + map_id).addClass('d-none');
		this.el.find('.mpfy-multi-map-item.mpfy-' + map_id).removeClass('d-none');
	}

	/**
	 * Get mpfy-controls width
	 * 
	 * @param  string map_id Mapify map id.
	 * @return int
	 */
	get_mpfy_controls_width( map_id ) {
		var width   = 20; // margin right
		var $search = this.el.find('.mpfy-map-id-' + map_id + ' .mpfy-controls .mpfy-search-form');
		var $filter = this.el.find('.mpfy-map-id-' + map_id + ' .mpfy-controls .mpfy-filter');

		// mpfy-search-form
		if ( $search.is(":visible") ) {
			width+= $search.width();
		}

		// mpfy-filter
		if ( $filter.is(":visible") ) {
			width+= $filter.width();
		}

		return width;
	}

	/**
	 * Get mpfy-multi-map-dropdown width
	 * 
	 * @return int
	 */
	get_mpfy_multi_map_dropdown_width() {
		var width = 20; // margin left

		if ( this.dropdown.is(":visible") ) {
			width+= this.dropdown.width();
		}

		return width;
	}

	/**
	 * Set mpfy-multi-map-dropdown position
	 * 
	 * @param string map_id Mapify map id.
	 */
	set_multi_map_dropdown_position( map_id ) {
		var controls_width   = this.get_mpfy_controls_width( map_id );
		var dropdown_width   = this.get_mpfy_multi_map_dropdown_width();
		var container_width  = this.el.width();
		var container_height = this.el.data('height');
		var minimum_range    = 10;
		var dropdown_height  = 70;
		
		if ( controls_width + dropdown_width + minimum_range > container_width ) {
			this.dropdown.css('top', (container_height - dropdown_height) + 'px');
		} else {
			this.dropdown.css('top', '0');
		}
	}
}