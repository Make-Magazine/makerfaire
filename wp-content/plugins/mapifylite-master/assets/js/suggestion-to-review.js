jQuery(function($) {
	// mpfy-do-the-review
	$('.notice-info-mapifylite').on('click', '#mpfy-do-the-review', function(){
		mpfy_send_selected_user_review_action('do-the-review');
	});

	// mpfy-did-the-review
	$('.notice-info-mapifylite').on('click', '#mpfy-did-the-review', function(e){
		e.preventDefault();
		mpfy_send_selected_user_review_action('did-the-review');
	});

	// mpfy-wont-review
	$('.notice-info-mapifylite').on('click', '#mpfy-wont-review', function(e){
		e.preventDefault();
		mpfy_send_selected_user_review_action('wont-review');
	});

	function mpfy_send_selected_user_review_action( selected_action ) {
		var data = {
			action: 'mpfy_review_save_selected_user_action',
			selected_action: selected_action,
			nonce: $('input[name=mpfy_review_nonce]').val(),
		}

		$.post(ajaxurl, data, function(response){
			$('#mpfy-suggestion-to-review').slideUp();
			$('#mpfy-response-to-the-review').slideDown();
		})
	}
});