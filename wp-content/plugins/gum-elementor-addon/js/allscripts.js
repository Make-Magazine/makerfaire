jQuery(document).ready(function($){
	"use strict";

	function prepare_gummenu(menus){

		$('.arrow', menus).each(function(f, index){

			$(index).on('click', function(e){
				e.preventDefault();
				var curmenu = $(this).closest('li');
				curmenu.siblings().removeClass('menu-open');
				curmenu.toggleClass('menu-open');
			});
		});

	}


	if($('.gum-menu').length){
		$('.gum-menu').each(function(){

			var themenu = $(this), menuwrapper = themenu.closest('.nav-wrapper'), btn = menuwrapper.find('.toggle-gum-menu');

			prepare_gummenu(themenu);

			btn.unbind('click').on('click', function(e){
			e.preventDefault();
			themenu.toggleClass('toggle-collapse');

			});

		});
	}

});