const $ = jQuery;
const instances = [];

export default class Tooltip {
	constructor( settings ) {
		this.dom_node = $('<div></div>');
		this.dom_node.data('tooltip', this);
		this.settings = settings;
		this.class_prefix = 'route';

		this.node = function() {
			return this.dom_node;
		}

		this.init = function() {
			instances.push(this);

			this.node().append($('<div class="top"></div>'));
			this.node().append(
				$('<div class="center" style="background: rgba(' + this.settings.rgba[0] + ', ' + this.settings.rgba[1] + ', ' + this.settings.rgba[2] + ', ' + this.settings.rgba[3] + ');"></div>')
					.append(this.settings.content)
			);
			this.node().append($('<div class="bottom" style="border-top: 20px solid rgba(' + this.settings.rgba[0] + ', ' + this.settings.rgba[1] + ', ' + this.settings.rgba[2] + ', ' + this.settings.rgba[3] + ');"></div>'));

			if (this.settings.close_behavior == 'manual') {
				var close_button = $('<a href="#" class="' + this.class_prefix + '-close-tooltip ' + this.class_prefix + '-notext">&nbsp;</a>');
				close_button.data('tooltip', this);
				close_button.on('click', function(e) {
					var t = $(this).data('tooltip');
					t.node().trigger({
						'type': 'tooltip_close'
					});
					e.preventDefault();
				});
				this.node().find('.center:first').prepend(close_button);
			}

			$('body').append(this.node());

			// Handle events
			this.node()
				.on('tooltip_mouseover', function(e) {
					var t = $(this).data('tooltip');
					t.show(e.settings.left, e.settings.top);
				})
				.on('tooltip_mouseout', function(e) {
					var t = $(this).data('tooltip');
					if (t.settings.close_behavior == 'auto') {
						t.hide();
					}
				})
				.on('tooltip_close', function(e) {
					var t = $(this).data('tooltip');
					t.hide();
				})
		}

		this.show = function(l, t) {
			for (var i = 0; i < instances.length; i++) {
				var instance = instances[i];
				instance.hide();
			}

			var tooltip_width = this.node().width();
			var arrow_width = 30;
			var arrow_margin = '0 auto';

			if (l < 0) {
				arrow_margin = '0 0 0 ' + Math.floor(tooltip_width / 2 + l - arrow_width / 2).toString() + 'px';
				l = 0;
			} else if (l + tooltip_width > $(window).width()) {
				var excess = l + tooltip_width - $(window).width();
				arrow_margin = '0 0 0 ' + Math.floor(tooltip_width / 2 + excess - arrow_width / 2).toString() + 'px';
				l = $(window).width() - tooltip_width;
			}
			this.node().css({
				'left': l,
				'top': t
			}).show();
			this.node().find('.bottom:first').css({
				'margin': arrow_margin
			});
		}

		this.hide = function() {
			this.node().hide();
		}

		this.init();
	}

	setGoogleMap(map) {
		var me = this;
		google.maps.event.addListener(map, 'center_changed', function(){
			me.hide();
		});
		google.maps.event.addListener(map, 'zoom_changed', function(){
			me.hide();
		});
		google.maps.event.addListener(map, 'dragstart', function(){
			me.hide();
		});
	}

	setImageMap(map) {
		var me = this;
		google.maps.event.addListener(map, 'center_changed', function(){
			me.hide();
		});
		google.maps.event.addListener(map, 'zoom_changed', function(){
			me.hide();
		});
		google.maps.event.addListener(map, 'dragstart', function(){
			me.hide();
		});
	}

	setLeafletMap( map ) {
		map.on( 'movestart', () => this.hide() );
		map.on( 'moveend', () => this.hide() );
		map.on( 'zoomstart', () => this.hide() );
		map.on( 'zoomend', () => this.hide() );
		map.on( 'viewreset', () => this.hide() );
	}
}