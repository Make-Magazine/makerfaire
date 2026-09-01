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
 * Prevent partial/incomplete masked phone inputs from triggering GPPA updates.
 *
 * @since 2.1.64
 */
window.gform.addFilter(
	'gppa_should_trigger_change',
	(
		triggerChange: boolean,
		_formId: any,
		_inputId: any,
		$el: JQuery,
		_event: JQuery.Event
	) => {
		if (!triggerChange) {
			return triggerChange;
		}

		if (
			$el.is('input[type="tel"]') &&
			$el.closest('.gfield--type-phone').length
		) {
			const value = String($el.val() || '');

			if (value && !isPhoneMaskComplete($el, value)) {
				return false;
			}
		}

		return triggerChange;
	}
);

/**
 * Whether a masked phone input has been filled out completely.
 *
 * @since 2.1.75
 *
 * @param $el   The phone input.
 * @param value The current value of the phone input.
 *
 * @return Whether the mask has been filled out completely.
 */
function isPhoneMaskComplete($el: JQuery, value: string) {
	/**
	 * Up to GF 2.9, phone masks were applied by jquery.maskedinput, which pads the value with the
	 * unfilled portion of the mask (e.g. "(123) 45_-____").
	 */
	if (value.indexOf('_') !== -1) {
		return false;
	}

	/**
	 * As of GF 3.0, masks are applied by IMask in lazy mode, which only keeps the portion of the mask
	 * the user has actually typed (e.g. "(123) 45") and exposes the mask via `data-mask`. Without a
	 * placeholder to look for, completeness is measured against the mask's required portion, which is
	 * everything preceding the optional marker ("?").
	 */
	const mask = $el.attr('data-mask');

	if (mask) {
		const optionalIndex = mask.indexOf('?');
		const requiredMask =
			optionalIndex === -1 ? mask : mask.substring(0, optionalIndex);

		if (value.length < requiredMask.length) {
			return false;
		}
	}

	return true;
}

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

	if (!$targetField.length) {
		return;
	}

	if (action === 'hide') {
		$targetField.removeData('gppa-triggered');
		return;
	}

	if (action !== 'show' || $targetField.data('gppa-triggered')) {
		return;
	}

	$targetField.data('gppa-triggered', true);

	if (shouldApplyConditionalDefault($targetField, defaultValues, targetId)) {
		$targetField.val(defaultValues);
	}

	const isPhoneField = jQuery(targetId).hasClass('gfield--type-phone');

	// Skip phone fields. The mask placeholder (e.g. "(___) ___-____") shows as unwanted
	// formatting on initial show, and dispatching `input` on a masked field crashes
	// jquery.maskedinput on mobile, aborting GF's multi-page render.
	if (isPhoneField) {
		return;
	}

	// GF does not always emit these events when a field becomes visible.
	//
	// Masked inputs are excluded from `input`: jquery.maskedinput (GF < 3.0) binds an
	// Android + Chromium only `input` handler that reads a caret position only available
	// while the field is focused, so a synthetic event throws and aborts GF's conditional
	// logic loop. `change` is what conditional logic and GPPA listen for anyway.
	$targetField.filter((_index, el) => !hasInputMask(el)).trigger('input');
	$targetField.trigger('change');
});

/**
 * Whether an input currently has a jquery.maskedinput mask attached. The library sets this
 * data key on mask and removes it on unmask.
 *
 * @param {HTMLElement} el The input to check.
 *
 * @since 2.1.74
 */
function hasInputMask(el: HTMLElement) {
	const dataName = (jQuery as any).mask?.dataName ?? 'rawMaskFn';

	return !!jQuery(el).data(dataName);
}

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

function shouldApplyConditionalDefault(
	$targetField: JQuery,
	defaultValues:
		| string
		| number
		| string[]
		| ((this: HTMLElement, index: number, value: string) => string),
	targetId: any
) {
	if (
		typeof defaultValues !== 'string' ||
		!defaultValues ||
		$targetField.val() ||
		$targetField.is('[type="file"]') ||
		$targetField.hasClass('gf_coupon_code') ||
		jQuery(targetId).hasClass('gfield--type-phone')
	) {
		return false;
	}

	// Do not inject unresolved merge tag placeholders as literal values.
	return !/@\{:[^}]+\}/.test(defaultValues);
}
