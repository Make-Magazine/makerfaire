import { PropertiesSlice } from './properties';
import { StateCreator } from 'zustand';
import { OrderingSlice } from './ordering';
import { FiltersSlice } from './filters';
import { TemplatesSlice } from './templates';
import { FieldSettingsSyncingSlice } from '../../framework/field-settings-syncing';
import { CoreSlice } from './core';
import { PreviewSlice } from './preview';

const { strings } = window.GPPA_ADMIN;

export interface ComputedSlice {
	computed: {
		// Core
		isSupportedField: boolean;
		isSuperAdmin: boolean;
		isRestrictedObjectTypeActive: boolean;
		objectTypeInstance: null | GPPAObjectType;
		usingFieldObjectType: boolean;
		fieldObjectTypeTargetFieldSettings: GravityFormsField;
		fieldValueObjects: GravityFormsField[];

		// Filters
		filterPropertiesUngrouped: GPPAProperty[];
		filterPropertiesGrouped: { [group: string]: GPPAProperty[] };
		filterSpecialValues: {
			label: string;
			value: string;
		}[];
		filterFormFieldValues: {
			label: string;
			value: string;
		}[];
		hasFilterFieldValue: boolean;

		// Ordering
		orderingPropertiesUngrouped: GPPAProperty[];
		orderingPropertiesGrouped: { [group: string]: GPPAProperty[] };

		// Preview
		missingTemplates: string[];
		resultColumns: string[];

		// Properties
		primaryPropertySelected: boolean;
		primaryPropertyComputed: string;
		CustomPrimaryPropertySelect: ({
			populate,
			propertyValues,
			primaryProperty,
			setPrimaryProperty,
		}: {
			populate: GPPAPopulate;
			propertyValues: PropertiesSlice['propertyValues'];
			primaryProperty: PropertiesSlice['primaryProperty'];
			setPrimaryProperty: PropertiesSlice['setPrimaryProperty'];
		}) => JSX.Element | null;
		groupedProperties: { [group: string]: GPPAProperty[] };
		ungroupedProperties: GPPAProperty[];
		flattenedProperties: { [property: string]: GPPAProperty };
		propertiesLoaded: boolean;

		// Templates
		templateRows: {
			id: string;
			label: string;
			required?: boolean;
			shouldShow?: (
				field: GravityFormsField,
				populate: string
			) => boolean;
		}[];
		templatePropertiesUngrouped: GPPAProperty[];
		templatePropertiesGrouped: { [group: string]: GPPAProperty[] };
	};
}

export const createComputedSlice: StateCreator<
	CoreSlice &
		ComputedSlice &
		PropertiesSlice &
		PreviewSlice &
		OrderingSlice &
		FiltersSlice &
		TemplatesSlice &
		FieldSettingsSyncingSlice,
	[],
	[],
	ComputedSlice
> = (set, get) => ({
	computed: {
		get isSupportedField() {
			if (!get()?.field) {
				return false;
			}

			/**
			 * Specify what fields can be populated by Populate Anything. This filter runs in the Form Editor and determines
			 * which fields the Populate Anything field settings will show for.
			 *
			 * @param {boolean}            isSupportedField Whether or not the current field is supported for population. Defaults to `false`.
			 * @param {GravityFormsField}  field            The current field selected in the Form Editor.
			 * @param {'choices'|'values'} populate         What is being populated. It will be either `choices` or `values`.
			 *
			 * @since 2.1.0 Removed `component` parameter.
			 */
			return window.gform.applyFilters(
				'gppa_is_supported_field',
				false,
				get().field,
				get().populate
			);
		},

		get isSuperAdmin() {
			return window.GPPA_ADMIN.isSuperAdmin;
		},

		get isRestrictedObjectTypeActive() {
			return !!get().computed.objectTypeInstance?.restricted;
		},

		get objectTypeInstance() {
			if (!get().objectType) {
				return null;
			}

			if (get().computed.usingFieldObjectType) {
				const targetFieldSettings = get().computed
					.fieldObjectTypeTargetFieldSettings;

				if (
					!targetFieldSettings ||
					!targetFieldSettings['gppa-choices-object-type'] ||
					!window.GPPA_ADMIN.objectTypes[
						targetFieldSettings['gppa-choices-object-type']
					]
				) {
					set({ objectType: '' });

					return null;
				}

				const fieldObjectType =
					window.GPPA_ADMIN.objectTypes[
						targetFieldSettings['gppa-choices-object-type']
					];

				return Object.assign({}, fieldObjectType);
			}

			return Object.assign(
				{},
				window.GPPA_ADMIN.objectTypes[get().objectType]
			);
		},

		get usingFieldObjectType() {
			return get().objectType?.indexOf('field_value_object') === 0;
		},

		get fieldObjectTypeTargetFieldSettings() {
			if (!get().computed.usingFieldObjectType) {
				return null;
			}

			const targetFieldID = get().objectType.split(':')[1];

			return window.form.fields.filter((field: GravityFormsField) => {
				// eslint-disable-next-line eqeqeq
				return field.id == targetFieldID;
			})[0];
		},

		get fieldValueObjects() {
			return window.form.fields.filter((field: GravityFormsField) => {
				if (!('choices' in field)) {
					return false;
				}

				if (field.id === get().field?.id) {
					return false;
				}

				return (
					field?.['gppa-choices-enabled'] &&
					field?.['gppa-choices-object-type']
				);
			});
		},

		// Filters
		get filterPropertiesUngrouped() {
			return get().computed.ungroupedProperties?.filter(
				(property: any) => property?.supports_filters !== false
			);
		},

		get filterPropertiesGrouped() {
			const groupedProperties: { [groupId: string]: any[] } = {
				...get().computed.groupedProperties,
			};

			for (const [groupId, properties] of Object.entries(
				groupedProperties
			)) {
				groupedProperties[groupId] = properties.filter(
					(property) => property?.supports_filters !== false
				);

				if (groupedProperties[groupId].length === 0) {
					delete groupedProperties[groupId];
				}
			}

			return groupedProperties;
		},

		get filterSpecialValues() {
			const specialValues = [
				{
					label: 'Current User ID',
					value: 'special_value:current_user:ID',
				},
				{
					label: 'Current Post ID',
					value: 'special_value:current_post:ID',
				},
			];

			if (get().computed.objectTypeInstance?.supportsNullFilterValue) {
				specialValues.push({
					label: 'NULL',
					value: 'special_value:null',
				});
			}

			return window.gform.applyFilters(
				'gppa_filter_special_values',
				specialValues,
				{
					field: get().field,
					populate: get().populate,
				}
			);
		},

		get filterFormFieldValues() {
			const formFieldValues = [];

			const excludedFormFieldValueInputTypes = ['chainedselect'];

			for (let i = 0; i < window.form.fields.length; i++) {
				const field = window.form.fields[i];
				const inputType = window.GetInputType(field);

				if (excludedFormFieldValueInputTypes.includes(inputType)) {
					continue;
				}

				/*
				 * Our intention behind this check is any fields supported by conditional logic have a value that
				 * can be used in a filter.
				 *
				 * However, we also want to support dates as dates are not supported by conditional logic unless
				 * using GP Conditional Logic Dates.
				 */
				if (
					window.IsConditionalLogicField(field) ||
					['date'].includes(inputType)
				) {
					if (
						field.inputs &&
						!['checkbox', 'radio', 'email'].includes(inputType)
					) {
						/*
						 * We want to show the entire field as an option if it's a Date Drop Down.
						 *
						 * We'll still offer the inputs as options.
						 */
						if (inputType === 'date') {
							formFieldValues.push({
								label: window.GetLabel(field),
								value: 'gf_field:' + field.id,
							});
						}

						for (let j = 0; j < field.inputs.length; j++) {
							const input = field.inputs[j];
							if (!input.isHidden) {
								formFieldValues.push({
									label: window.GetLabel(field, input.id),
									value: 'gf_field:' + input.id,
								});
							}
						}
					} else {
						formFieldValues.push({
							label: window.GetLabel(field),
							value: 'gf_field:' + field.id,
						});
					}
				}
			}

			return formFieldValues;
		},

		get hasFilterFieldValue() {
			let hasFilterFieldValue = false;

			get().filterGroups.forEach(function(filterGroup) {
				filterGroup.forEach(function(filter) {
					if (
						typeof filter.value === 'string' &&
						filter.value.indexOf('gf_field') === 0
					) {
						hasFilterFieldValue = true;
					}
				});
			});

			return hasFilterFieldValue;
		},

		// Ordering
		get orderingPropertiesUngrouped() {
			return get().computed.ungroupedProperties?.filter(
				(property: GPPAProperty) => property?.orderby
			);
		},

		get orderingPropertiesGrouped() {
			const groupedProperties: {
				[groupId: string]: GPPAProperty[];
			} = {
				...get().computed.groupedProperties,
			};

			for (const [groupId, properties] of Object.entries(
				groupedProperties
			)) {
				groupedProperties[groupId] = properties.filter(
					(property) => property?.orderby
				);

				if (groupedProperties[groupId].length === 0) {
					delete groupedProperties[groupId];
				}
			}

			return groupedProperties;
		},

		// Preview
		get missingTemplates() {
			const missingTemplates: string[] = [];

			get().computed.templateRows.forEach(function(templateRow) {
				if (
					!get().templates?.[templateRow.id] &&
					templateRow.required
				) {
					missingTemplates.push(templateRow.label);
				}
			});

			return missingTemplates;
		},

		get resultColumns() {
			if (!get()?.previewResults?.results?.length) {
				return [];
			}

			return Object.keys(get().previewResults!.results[0]);
		},

		// Properties
		get primaryPropertySelected() {
			// If the object type does not support a primary property, return true.
			if (!get().computed.objectTypeInstance?.['primary-property']) {
				return true;
			}

			// If the object type supports a primary property, return true if the primary property is selected.
			return !!get().computed.primaryPropertyComputed;
		},

		get primaryPropertyComputed() {
			if (get().computed.usingFieldObjectType) {
				return window.gform.applyFilters(
					'gppa_primary_property_computed',
					get().computed.fieldObjectTypeTargetFieldSettings[
						'gppa-choices-primary-property'
					],
					get()
				);
			}

			/**
			 * Filter the computed primary property in the form editor.
			 *
			 * Mostly used by other perks to determine if the primary property is fully ready to be used.
			 *
			 * @param {string} primaryProperty The primary property.
			 * @param {Object} store           The GPPA Zustand store.
			 *
			 * @since 2.0.14
			 * @since 2.1 Updated the second parameter to be the Zustand store instead of the Vue instance.
			 */
			return window.gform.applyFilters(
				'gppa_primary_property_computed',
				get().primaryProperty,
				get()
			);
		},

		get CustomPrimaryPropertySelect() {
			/**
			 * Filter the React Component used for primary properties.
			 *
			 * Mostly used by other perks to add additional UI around the primary property selection such as splitting
			 * it up into multiple selects.
			 *
			 * @param {() => JSX.Element|null} component The React component to use for the Primary Property. Defaults to `null`.
			 * @param {Object}                 store     The current GPPA store
			 *
			 * @since 2..0
			 */
			return window.gform.applyFilters(
				'gppa_custom_primary_property_component',
				null,
				get()
			);
		},

		get groupedProperties() {
			const groupedProperties = { ...get().properties };
			delete groupedProperties.ungrouped;

			return groupedProperties;
		},

		get ungroupedProperties() {
			return get().properties.ungrouped;
		},

		get flattenedProperties() {
			const propertiesFlat = {};

			Object.keys(get().properties).forEach((group) => {
				const groupProperties = get().properties[group];

				groupProperties.forEach((property: GPPAProperty) => {
					// @ts-ignore
					propertiesFlat[property.value] = property;
				});
			});

			return propertiesFlat;
		},

		get propertiesLoaded() {
			return !!(
				get().computed.flattenedProperties &&
				Object.keys(get().computed.flattenedProperties).length
			);
		},

		// Templates
		get templateRows() {
			let templateRows: {
				id: string;
				label: string;
				required?: boolean;
				shouldShow?: (
					field: GravityFormsField,
					populate: string
				) => boolean;
			}[] = [];

			if (!get().field) {
				return templateRows;
			}

			switch (get().populate) {
				case 'choices':
					templateRows.push({
						id: 'value',
						label: strings.value,
						required: true,
					});
					templateRows.push({
						id: 'label',
						label: strings.label,
						required: true,
					});

					if (
						get().field?.basePrice ||
						get().field?.type === 'option'
					) {
						templateRows.push({
							id: 'price',
							label: strings.price,
							required: true,
						});
					}

					if (get().field?.type === 'image_choice') {
						templateRows.push({
							id: 'image',
							label: strings.image,
							required: true,
						});
					}

					break;

				case 'values':
					if (
						get().field &&
						get().field!.inputs &&
						!window.GPPA_ADMIN.interpretedMultiInputFieldTypes.includes(
							get().field!.type
						) &&
						!window.GPPA_ADMIN.multiSelectableChoiceFieldTypes.includes(
							get().field!.type
						) &&
						!window.GPPA_ADMIN.multiSelectableChoiceFieldTypes.includes(
							get().field!.inputType
						)
					) {
						for (const input of get().field!.inputs) {
							if (input.isHidden) {
								continue;
							}

							templateRows.push({
								id: input.id,
								label: input.label,
							});
						}
					} else {
						templateRows.push({
							id: 'value',
							label: strings.value,
							required: true,
						});
					}
					break;
			}

			/**
			 * Modify the templates that will be shown under Choice Templates and/or Value Templates.
			 *
			 * @since 1.0-beta-4.116
			 *
			 * @param {Object[]} templateRows The available template rows. Each template should have an "id" string, "label" string, and "required" boolean.
			 * @param {Object}   field        The current field shown in the Form Editor.
			 * @param {string}   populate     What's being populated. Either "choices" or "values"
			 */
			templateRows = window.gform.applyFilters(
				'gppa_template_rows',
				templateRows,
				get().field,
				get().populate
			);

			// Loops through all template rows, if they have a shouldShow function property set, evaluate it. If it returns false, remove the row.
			return templateRows.filter((templateRow) => {
				if (typeof templateRow?.shouldShow === 'function') {
					return templateRow.shouldShow(get().field!, get().populate);
				}

				return true;
			});
		},

		get templatePropertiesUngrouped() {
			if (!get().computed.ungroupedProperties) {
				return [];
			}

			return get().computed.ungroupedProperties?.filter(
				(property: any) => property?.supports_templates !== false
			);
		},

		get templatePropertiesGrouped() {
			const groupedProperties: { [groupId: string]: any[] } = {
				...get().computed.groupedProperties,
			};

			for (const [groupId, properties] of Object.entries(
				groupedProperties
			)) {
				groupedProperties[groupId] = properties.filter(
					(property) => property?.supports_templates !== false
				);

				if (groupedProperties[groupId].length === 0) {
					delete groupedProperties[groupId];
				}
			}

			return groupedProperties;
		},
	},
});
