import { StateCreator } from 'zustand';
import $ from 'jquery';
import { CoreSlice } from './core';
import { FiltersSlice } from './filters';
import { ComputedSlice } from './computed';
import { memoizedjQueryAjax } from '../../helpers/memoizedjQueryAjax';

const { strings } = window.GPPA_ADMIN;

export interface PropertiesSlice {
	primaryProperty: string;
	properties: {
		[group: string]: GPPAProperty[];
	};
	propertyValues: {
		[property: string]: {
			value: string;
			label: string;
			disabled?: boolean;
		}[];
	};

	/* Actions */
	getProperties: () => void;
	getPropertyValues: (property: string) => void;
	setPrimaryProperty: (primaryProperty: string) => void;
	resetPropertyValues: (keepPrimaryPropertyValues: boolean) => void;
}

export const initialStateProperties: Pick<
	PropertiesSlice,
	'primaryProperty' | 'properties' | 'propertyValues'
> = {
	primaryProperty: '',
	properties: {},
	propertyValues: {},
};

const createPropertiesSlice: StateCreator<
	PropertiesSlice & CoreSlice & FiltersSlice & ComputedSlice,
	[],
	[],
	PropertiesSlice
> = (set, get) => ({
	...initialStateProperties,

	getProperties() {
		if (!get().computed.objectTypeInstance) {
			return;
		}

		get().resetPropertyValues(true);

		memoizedjQueryAjax(window.ajaxurl, {
			data: {
				action: 'gppa_get_object_type_properties',
				'object-type': get().computed.objectTypeInstance!.id,
				populate: get().populate,
				security: window.GPPA_ADMIN.nonce,
				'primary-property-value':
					'primary-property' in get().computed.objectTypeInstance!
						? get().computed.primaryPropertyComputed
						: undefined,
			},
			dataType: 'json',
			method: 'POST',
		}).then((data) => {
			set({
				properties: data,
			});
		});
	},

	getPropertyValues(property: string) {
		if (
			property in get().propertyValues ||
			!get().computed.objectTypeInstance ||
			!property
		) {
			return;
		}

		memoizedjQueryAjax(window.ajaxurl, {
			data: {
				action: 'gppa_get_property_values',
				'object-type': get().computed.objectTypeInstance!.id,
				property,
				security: window.GPPA_ADMIN.nonce,
				'primary-property-value':
					'primary-property' in get().computed.objectTypeInstance!
						? get().computed.primaryPropertyComputed
						: undefined,
			},
			dataType: 'json',
			method: 'POST',
		}).then((data) => {
			if (data === 'gppa_over_max_values_in_editor') {
				/**
				 * If gppa_max_property_values_in_editor filter is met, do not output any properties to be selected.
				 *
				 * Instead, a custom value or special value should by used by the user.
				 *
				 * This is done for usability purposes but also to help browsers from locking up if there are a huge number of
				 * results.
				 */
				set({
					propertyValues: {
						...get().propertyValues,
						[property]: [
							{
								value: '',
								label: strings.tooManyPropertyValues,
								disabled: true,
							},
						],
					},
				});

				return;
			}

			set({
				propertyValues: {
					...get().propertyValues,
					[property]: $.map(data, function(option, index) {
						let value = option;
						let label = option;

						if (Array.isArray(option)) {
							value = option[0];
							label = option[1];
						}

						return {
							value,
							label,
						};
					}),
				},
			});
		});
	},

	setPrimaryProperty(primaryProperty: string) {
		set({
			primaryProperty,
			filterGroups: [],
		});

		if (
			get().computed.usingFieldObjectType &&
			!get().computed.objectTypeInstance?.['primary-property']
		) {
			return;
		}

		get().getProperties();
	},

	resetPropertyValues(keepPrimaryPropertyValues: boolean) {
		const primaryPropertyValues = [
			...(get().propertyValues?.['primary-property'] ?? []),
		];

		set({ propertyValues: {} });

		if (
			keepPrimaryPropertyValues &&
			Object.keys(primaryPropertyValues).length
		) {
			set({
				propertyValues: {
					'primary-property': primaryPropertyValues,
				},
			});
		}
	},
});

export default createPropertiesSlice;
