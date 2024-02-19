;(function($){

window.MapifyTooltip = function(settings) {
	this.dom_node = $('<div></div>');
	this.dom_node.data('tooltip', this);
	this.settings = settings;
	this.class_prefix = 'mpfy';
	this.creation_id = 0;
	this.visible = false;

	this.dom_node.addClass( this.settings.classes );


	this.node = function() {
		return this.dom_node;
	}

	this.init = function() {
		MapifyTooltip._instances.push(this);
		this.creation_id = MapifyTooltip._instances.length;

		var inner_wrap = $('<div class="inner-wrap"></div>');
		this.node().append(inner_wrap);
		inner_wrap.append($('<div class="top"></div>'));
		inner_wrap.append(
			$('<div class="center"></div>')
				.append('<div class="tltpcnt">' + this.settings.content + '</div>')
		);
		inner_wrap.append($('<div class="bottom" style="border-top: 20px solid rgba(' + this.settings.rgba[0] + ', ' + this.settings.rgba[1] + ', ' + this.settings.rgba[2] + ', ' + this.settings.rgba[3] + ');"></div>'));

		inner_wrap
			.find('.mpfy-tooltip-content')
			.css( 'backgroundColor', 'rgba(' + this.settings.rgba[0] + ', ' + this.settings.rgba[1] + ', ' + this.settings.rgba[2] + ', ' + this.settings.rgba[3] + ')' );

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
			});

	}

	this.show = function(l, t) {
		l = Math.round(l);
		t = Math.round(t);


		for (var i = 0; i < MapifyTooltip._instances.length; i++) {
			var instance = MapifyTooltip._instances[i];


			if (instance.creation_id != this.creation_id) {
				instance.hide();
			}
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

		if (this.settings.animate && !this.visible) {
			this.node().find('.inner-wrap:first')
				.stop()
				.css({
					'top': 7,
					'opacity': 0
				})
				.animate({
					'top': 0,
					'opacity': 1
				}, 300);
		}
		this.node()
			.stop()
			.css({
				'left': l,
				'top': t,
				'opacity': 1
			})
			.show();
		this.node().find('.bottom:first').css({
			'margin': arrow_margin
		});

		this.visible = true;
	}

	this.showCentered = function(l, t) {
		var l = l - Math.ceil(this.node().width() / 2);
		var t = t - this.node().height();
		this.show(l, t);
	}

	this.hide = function() {
		if (!this.visible) {
			return false;
		}
		if (this.settings.animate) {
			this.node().stop().fadeTo(300, 0, function() {
				$(this).hide();
			})
		} else {
			this.node().hide();
		}

		this.visible = false;
		this.node().trigger({
    		'type': 'tooltip_closed'
    	});
	}

	this.setContent = function(content) {
		this.node().find('.tltpcnt:first').html(content);
	}

	this.setGoogleMap = function(map) {
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

	this.setImageMap = function(map) {
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

	this.init();
}
MapifyTooltip._instances = [];

})(jQuery);