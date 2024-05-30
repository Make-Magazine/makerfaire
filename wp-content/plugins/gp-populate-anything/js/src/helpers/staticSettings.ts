import $ from 'jquery';

export const hideStaticSettings = (
	isSupportedField: boolean,
	populate: GPPAPopulate
) => {
	if (!isSupportedField) {
		return;
	}

	switch (populate) {
		case 'choices':
			if (window.field.choices) {
				$('.choices_setting, .choices-ui__trigger-section').hide();
				window?.gform?.instances?.choicesUi?.flyout?.closeFlyout();
			}
			break;
		case 'values':
			if (
				window.fieldSettings[window.field.type] &&
				window.fieldSettings[window.field.type].indexOf(
					'calculation_setting'
				) !== -1
			) {
				window.ToggleCalculationOptions(
					false,
					window.GetSelectedField()
				);
				$('.calculation_setting').hide();
			}
			break;
	}
};

export const showStaticSettings = (
	isSupportedField: boolean,
	populate: GPPAPopulate
) => {
	if (!isSupportedField) {
		return;
	}

	switch (populate) {
		case 'choices':
			if (
				!window?.field?.choices?.length ||
				window.field.type === 'post_category'
			) {
				break;
			}

			/**
			 * Quiz fields have custom settings added to the ".choices_section"
			 * If we explicity show that div here, then two sets of settings will be shown in the
			 * flyover after clicking "Edit Choices".
			 *
			 * ".choices-ui_trigger-section" is the container that holds the "Edit Choices" button.
			 * ".choices_setting" is the container that holds the settings field in the Choices flyover
			 */
			if (window.field.type === 'quiz') {
				$('.choices-ui__trigger-section').show();
				break;
			}

			$('.choices_setting, .choices-ui__trigger-section').show();
			break;
		case 'values':
			if (
				$.inArray(window.GetInputType(window.GetSelectedField()), [
					'number',
					'calculation',
				]) !== -1 &&
				window.GetSelectedField().type !== 'quantity'
			) {
				$('.calculation_setting').show();
			}
			break;
	}
};
