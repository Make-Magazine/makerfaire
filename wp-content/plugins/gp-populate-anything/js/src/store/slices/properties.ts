import { StateCreator } from 'zustand';
import $ from 'jquery';
import { CoreSlice } from './core';
import { FiltersSlice } from './filters';
import { ComputedSlice } from './computed';

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
	getPropertiesLock?: string;
	getPropertyValuesLock: string[];

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
	getPropertyValuesLock: [],

	getProperties() {
		if (!get().computed.objectTypeInstance) {
			return;
		}

		// Prevent multiple requests
		if (get().getPropertiesLock === get().objectType) {
			return;
		}

		set({
			getPropertiesLock: get().objectType,
		});

		get().resetPropertyValues(true);

		const ajaxArgs = {
			action: 'gppa_get_object_type_properties',
			'object-type': get().computed.objectTypeInstance!.id,
			populate: get().populate,
			security: window.GPPA_ADMIN.nonce,
			'primary-property-value':
				'primary-property' in get().computed.objectTypeInstance!
					? get().computed.primaryPropertyComputed
					: undefined,
		};

		$.post(window.ajaxurl, ajaxArgs, null, 'json').done((data) => {
			set({
				properties: data,
				getPropertiesLock: undefined,
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

		// Prevent multiple requests
		if (get().getPropertyValuesLock.includes(property)) {
			return;
		}

		const ajaxArgs = {
			action: 'gppa_get_property_values',
			'object-type': get().computed.objectTypeInstance!.id,
			property,
			security: window.GPPA_ADMIN.nonce,
			'primary-property-value':
				'primary-property' in get().computed.objectTypeInstance!
					? get().computed.primaryPropertyComputed
					: undefined,
		};

		set({
			getPropertyValuesLock: get().getPropertyValuesLock.concat(property),
		});

		$.post(window.ajaxurl, ajaxArgs, null, 'json').done((data) => {
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
				getPropertyValuesLock: get().getPropertyValuesLock.filter(
					(lock) => lock !== property
				),
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
