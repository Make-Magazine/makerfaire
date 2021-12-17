"use strict";

function initializeConfirmDialog($scope, $) {
	let settings = dceGetElementSettings($scope);
	if ( settings.dce_confirm_dialog_enabled !== 'yes' ) {
		return;
	}
	let title = settings.dce_confirm_dialog_title;
	let content = settings.dce_confirm_dialog_content;
	let $form = $scope.find('form').first();
	// The event is on the click event of the button. This is very useful
	// because it is fired before the submit event and it ignores
	// programmaticaly triggered submit events, for example from Stripe.
	$form.find('button[type="submit"]').on('click', (event) => {
		event.preventDefault();
		event.stopImmediatePropagation();
		$.confirm({
			title: renderLiveHTML($form[0], title),
			content: renderLiveHTML($form[0], content),
			theme: settings.dce_confirm_dialog_theme,
			boxWidth: '30%',
			useBootstrap: false,
			buttons: {
				confirm: function () {
					$form.trigger('submit');
				},
				cancel: function () {
					// nothing for now.
				},
			}
		});
	});
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeConfirmDialog);
});
