const $ = window.jQuery;

/**
 * Based on jQuery.serializeArray() but contains the following changes:
 * 	* Includes disabled inputs
 * 	* Includes the elements in the output
 *
 * @see https://github.com/jquery/jquery/blob/a684e6ba836f7c553968d7d026ed7941e1a612d8/src/serialize.js#L98
 * @param $form
 */
function serializeAll(
	$form: JQuery<HTMLFormElement>
): { name: string; value: string; el: HTMLInputElement }[] {
	const rcheckableType = /^(?:checkbox|radio)$/i;
	const rCRLF = /\r?\n/g;
	const rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i;
	const rsubmittable = /^(?:input|select|textarea|keygen)/i;

	const formElements: HTMLInputElement[] = $.makeArray(
		$form.prop('elements')
	);

	const inputElements = formElements.filter((el: HTMLInputElement) => {
		const type = el.type;

		return (
			el.name &&
			rsubmittable.test(el.nodeName) &&
			!rsubmitterTypes.test(type) &&
			(el.checked || !rcheckableType.test(type))
		);
	});

	/**
	 * This spread/concat is a cheap way to flatMap without a polyfill.
	 */
	return ([] as (
		| { name: string; value: string; el: HTMLInputElement }
		| undefined
	)[])
		.concat(
			...inputElements.map((el: HTMLInputElement) => {
				// eslint-disable-next-line eqeqeq
				if (el == null) {
					return undefined;
				}

				const val = $(el).val();

				if (Array.isArray(val)) {
					// Handle empty multi-selects otherwise no value will be sent, and it could get re-populated.
					if (val.length === 0) {
						return {
							name: el.name,
							value: '',
							el,
						};
					}

					return $.map(val, function(individualVal) {
						return {
							name: el.name,
							value: individualVal?.replace(rCRLF, '\n'),
							el,
						};
					});
				}

				/**
				 * Filter the AJAX form field values used when dynamically refreshing fields and Live Merge Tags.
				 *
				 * @param string elVal  The form field value.
				 * @param string elName The form field name.
				 *
				 * @since 2.1.11
				 */
				const elVal = window.gform.applyFilters(
					'gppa_ajax_form_field_values',
					(val as string)?.replace(rCRLF, '\n'),
					el.name
				);

				return {
					name: el.name,
					value: elVal,
					el,
				};
			})
		)
		.filter(Boolean) as {
		name: string;
		value: string;
		el: HTMLInputElement;
	}[];
}

export default function getFormFieldValues(
	$form: JQuery<HTMLFormElement>,
	inputPrefix: string = 'input_'
) {
	const inputsArray = serializeAll($form).filter(
		(value?: { name: string; value: string }) => {
			if (!value || value.name?.indexOf(inputPrefix) !== 0) {
				return false;
			}

			return true;
		}
	);

	const inputsObject: { [input: string]: string[] | string } = {};

	for (const input of inputsArray) {
		const { value, el } = input;
		let inputName = input.name.replace(inputPrefix, '');

		/**
		 * Do not send input value if it is not checked otherwise when hydrating values, it will be treated as if it
		 * was checked.
		 */
		if ((el?.type === 'radio' || el?.type === 'checkbox') && !el?.checked) {
			continue;
		}

		/* Handle array-based inputs such as the Time field */
		if (inputName.indexOf('[]') !== -1) {
			inputName = inputName.replace('[]', '');

			if (!(inputName in inputsObject)) {
				inputsObject[inputName] = [];
			}

			(inputsObject[inputName] as string[]).push(value);
			/* Standard inputs */
		} else {
			inputsObject[inputName] = value;
		}
	}

	return inputsObject;
}
