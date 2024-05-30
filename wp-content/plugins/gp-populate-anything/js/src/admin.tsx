/**
 * Refactor changes:
 *
 * • Removed core-js polyfills in form editor.
 */
import './polyfills/gformFormat';

import { FieldSettings } from './framework/FieldSettings';
import GPPAFieldSettings from './components/GPPAFieldSettings';
import $ from 'jquery';

class GPPopulateAnythingAdmin extends FieldSettings {
	constructor() {
		super();

		window.gform.addFilter(
			'gppa_is_supported_field',
			this.isSupportedField
		);
	}

	public fieldSettingsSelectors() {
		return ['#gppa', '#gppa-choices', '#gppa-values'];
	}

	public rootComponent() {
		return GPPAFieldSettings;
	}

	public get rootEl() {
		return document.querySelector('#gppa')!;
	}

	public getFieldSettings(field: any) {
		let currentFieldSettings = window.fieldSettings[field.type];

		if (field.type !== field.inputType) {
			currentFieldSettings += ',' + window.fieldSettings[field.inputType];
		}

		return $.map(currentFieldSettings.split(','), function(value) {
			return value.trim();
		});
	}

	public fieldHasChoices(field: any) {
		return (
			field?.choices && field?.choices !== '' && field?.choices !== null
		);
	}

	isSupportedField = (
		isSupportedField: boolean,
		field: GravityFormsField,
		populate: GPPAPopulate
	): boolean => {
		if (!field?.id) {
			return false;
		}

		/* Exclude specific field types */
		if (['consent', 'tos'].indexOf(field.type) !== -1) {
			return false;
		}

		switch (populate) {
			case 'choices':
				if (field.type === 'list') {
					return false;
				}

				if (this.fieldHasChoices(field)) {
					/* Exclude chained selects */
					if (field.choices[0] && 'choices' in field.choices[0]) {
						return false;
					}

					return true;
				}

				if (
					['workflow_user', 'workflow_multi_user'].indexOf(
						field.type
					) !== -1
				) {
					return true;
				}

				break;

			case 'values':
				if (this.fieldHasChoices(field)) {
					/* Exclude chained selects */
					if (field.choices[0] && 'choices' in field.choices[0]) {
						return false;
					}

					return true;
				}

				if (field?.enableCalculation) {
					return false;
				}

				/* Single input */
				if (
					this.getFieldSettings(field).indexOf(
						'.default_value_setting'
					) !== -1
				) {
					return true;
				}

				/* Textarea */
				if (
					this.getFieldSettings(field).indexOf(
						'.default_value_textarea_setting'
					) !== -1
				) {
					return true;
				}

				/* Input with multiple fields */
				if (
					this.getFieldSettings(field).indexOf(
						'.default_input_values_setting'
					) !== -1
				) {
					return true;
				}

				if (field.inputType === 'singleproduct') {
					return true;
				}

				if (field.type === 'list') {
					return true;
				}

				if (field.type === 'workflow_multi_user') {
					return true;
				}

				break;
		}

		return false;
	};
}

(window as any).GPPA = new GPPopulateAnythingAdmin();
