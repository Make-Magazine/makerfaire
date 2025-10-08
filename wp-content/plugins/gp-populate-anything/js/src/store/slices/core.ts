import { StateCreator } from 'zustand';
import { initialStateProperties, PropertiesSlice } from './properties';
import { initialStateOrdering, OrderingSlice } from './ordering';
import { FiltersSlice, initialStateFilters } from './filters';
import { TemplatesSlice } from './templates';
import {
	FieldSettingsSyncingDependencies,
	FieldSettingsSyncingSlice,
} from '../../framework/field-settings-syncing';
import { ComputedSlice } from './computed';

/**
 * It felt like splitting hairs at this point to separate this into further slices.
 */
export interface CoreSlice {
	populate: string;
	enabled: boolean;
	uniqueResults: boolean;
	objectType: string;

	importFieldSettings: () => void;
	setEnabled: (enabled: boolean) => void;
	setObjectType: (objectType: string) => void;
	setUniqueResults: (uniqueResults: boolean) => void;
}

export const initialStateCore: Pick<
	CoreSlice,
	'populate' | 'enabled' | 'uniqueResults' | 'objectType'
> = {
	populate: 'choices', // Gets immediately overwritten when all slices are combined.
	enabled: false,
	uniqueResults: true,
	objectType: '',
};

export const createCoreSlice: StateCreator<
	CoreSlice &
		ComputedSlice &
		PropertiesSlice &
		OrderingSlice &
		FiltersSlice &
		TemplatesSlice &
		FieldSettingsSyncingSlice,
	[],
	[],
	CoreSlice & FieldSettingsSyncingDependencies
> = (set, get) => ({
	...initialStateCore,

	importFieldSettings() {
		const prefix = 'gppa-' + get().populate + '-';

		// @ts-ignore
		const f = (key: string) => get().field?.[prefix + key];

		get().setEnabled(f('enabled'));
		get().setObjectType(f('object-type'));
		get().setPrimaryProperty(f('primary-property'));
		get().setOrderingProperty(f('ordering-property'));
		get().setOrderingMethod(f('ordering-method'));
		get().setFilterGroups(f('filter-groups'));
		get().setTemplates(f('templates'));
		get().setUniqueResults(
			typeof f('unique-results') === 'undefined'
				? true
				: f('unique-results')
		);
	},

	postImportFieldSettings() {
		/* Disable GPPA Value population if 'enableCalculation' is enabled on the current field */
		if (get().field?.enableCalculation && get().populate === 'values') {
			get().setEnabled(false);
		}
	},

	setEnabled: (enabled: boolean) => {
		set({ enabled });
	},

	setObjectType: (objectType: string) => {
		set({
			...initialStateProperties,
			...initialStateOrdering,
			...initialStateFilters,
			objectType,
		});

		if (
			get().computed.objectTypeInstance?.['primary-property'] &&
			!get().computed.usingFieldObjectType
		) {
			get().getPropertyValues('primary-property');
		} else {
			get().getProperties();
		}

		if (
			get().computed?.objectTypeInstance &&
			Object.keys(get().computed?.objectTypeInstance?.templates).length
		) {
			set({
				templates: {
					...get().computed.objectTypeInstance?.templates,
				},
			});
		} else {
			set({
				templates: {},
			});
		}
	},

	setUniqueResults(uniqueResults: boolean) {
		set({ uniqueResults });
	},
});
