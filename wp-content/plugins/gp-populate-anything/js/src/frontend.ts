/* eslint-disable no-shadow */
/* Polyfills */
import 'core-js/es/array/includes';
import 'core-js/es/array/find';
import 'core-js/es/object/assign';
import 'core-js/es/object/values';
import 'core-js/es/object/entries';

import GPPopulateAnything, {
	fieldMap,
	formID,
} from './classes/GPPopulateAnything';
import GPPALiveMergeTags from './classes/GPPALiveMergeTags';
import deepmerge from 'deepmerge';

const gppaMergedFieldMaps: { [formId: string]: fieldMap } = {};

window.gppaForms = {};
window.gppaLiveMergeTags = {};

for (const prop in window) {
	if (
		window.hasOwnProperty(prop) &&
		(prop.indexOf('GPPA_FILTER_FIELD_MAP') === 0 ||
			prop.indexOf('GPPA_FIELD_VALUE_OBJECT_MAP') === 0)
	) {
		const formId = prop.split('_').pop() as string;
		const map = (window as any)[prop];

		if (!(formId in gppaMergedFieldMaps)) {
			gppaMergedFieldMaps[formId] = {};
		}

		gppaMergedFieldMaps[formId] = deepmerge(
			gppaMergedFieldMaps[formId],
			map[formId]
		);
	}
}

const maybeRegisterForm = (formId: formID, fieldMap = {}) => {
	if (!(formId in window.gppaLiveMergeTags)) {
		if (!(formId in window.gppaForms)) {
			window.gppaForms[formId] = new GPPopulateAnything(formId, fieldMap);
		}

		window.gppaLiveMergeTags[formId] = new GPPALiveMergeTags(formId);
	}
};

for (const [formId, fieldMap] of Object.entries(gppaMergedFieldMaps)) {
	maybeRegisterForm(formId, fieldMap);
}

/**
 * WooCommerce Gravity Forms Product Add-Ons appears to add the ID to the form after page load so
 * div[id^="gform_wrapper_"] was added as a fallback.
 */
jQuery('form[id^="gform_"], div[id^="gform_wrapper_"]').each((index, el) => {
	const formId = jQuery(el)
		?.attr('id')
		?.replace(/^gform_(wrapper_)?/, '');

	if (!formId) {
		return;
	}

	maybeRegisterForm(formId);
});

window.gform.addAction('gpnf_init_nested_form', (formId: any) => {
	maybeRegisterForm(formId);
});

/**
 * Initialize GPPA JS for a specific form
 * This is not currently used internally by GPPA but allows external scripts to register GPPA on demand.
 * Currently used in GW Cache Buster. See HS#23661
 *
 * @since 1.0-beta-4.167
 *
 * @param number formId  Form ID to initialize
 */
window.gform.addAction('gppa_register_form', (formId: number) => {
	maybeRegisterForm(formId);
});

/**
 * This is a workaround for the issue where the conditional logic action
 * does not trigger the input event on the field.
 *
 * @since 2.1.32
 */
window.gform.addAction('gform_post_conditional_logic_field_action', function(
	_formId: any,
	action: string,
	targetId: any,
	defaultValues:
		| string
		| number
		| string[]
		| ((this: HTMLElement, index: number, value: string) => string),
	isInit: any
) {
	// Normalize conditional logic defaults to prevent malformed field values.
	patchConditionalLogicDefaults();

	const $targetField = jQuery(targetId).find('input, select, textarea');

	if (
		$targetField.length &&
		defaultValues &&
		typeof defaultValues === 'string' &&
		action === 'show' &&
		!$targetField.data('gppa-triggered') // Early exit if already triggered
	) {
		$targetField.data('gppa-triggered', true);

		// Trigger the input and change events.
		if (!$targetField.val() && !$targetField.is('[type="file"]')) {
			$targetField.val(defaultValues);
		}
		$targetField.trigger('input').trigger('change');
	}
});

/**
 * Fixes an issue where conditionally shown List fields in GravityView edit mode
 * could display raw serialized array data as default values when unhidden.
 * Cleans up and normalizes Gravity Forms conditional logic defaults to prevent
 * malformed field values.
 *
 * @since 2.1.43
 */
function patchConditionalLogicDefaults() {
	// Filter out empty items as GF stores these in an array for some reason.
	// The result is that if only a config for form id `5` is present, then
	// there will be four empty items in the array before `5`.
	const formConfigs = window.gf_form_conditional_logic.filter(Boolean);

	const regex = new RegExp(/^[a-z]:[1-9]:{[a-z]:[0-9];[a-z]:[0-9]:"";}$/);
	for (const formConfig of formConfigs) {
		let { defaults } = formConfig;

		defaults = Object.entries(defaults).reduce(
			(res: { [key: string]: any }, curr) => {
				const [key, defaultValue] = curr;

				if (
					typeof defaultValue === 'string' &&
					regex.test(defaultValue)
				) {
					res[key] = '';
				} else {
					res[key] = defaultValue;
				}

				return res;
			},
			{} as { [key: string]: any }
		);

		formConfig.defaults = defaults;
	}
}
