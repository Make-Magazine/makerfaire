import { create } from 'zustand';
import createTemplatesSlice, { TemplatesSlice } from './slices/templates';
import createOrderingSlice, { OrderingSlice } from './slices/ordering';
import createPreviewSlice, { PreviewSlice } from './slices/preview';
import createFiltersSlice, { FiltersSlice } from './slices/filters';
import createPropertiesSlice, { PropertiesSlice } from './slices/properties';
import createFieldSettingsSyncingSlice, {
	FieldSettingsSyncingDependencies,
	FieldSettingsSyncingSlice,
	subscribeFieldSettingsToStore,
	subscribeStoreToFieldSettings,
} from '../framework/field-settings-syncing';
import { CoreSlice, createCoreSlice } from './slices/core';
import { ComputedSlice, createComputedSlice } from './slices/computed';

type GPPAState = CoreSlice &
	PropertiesSlice &
	TemplatesSlice &
	OrderingSlice &
	PreviewSlice &
	FiltersSlice &
	FieldSettingsSyncingDependencies &
	FieldSettingsSyncingSlice &
	ComputedSlice;

const createGPPAStore = (populate: GPPAPopulate) => {
	return create<GPPAState>()((...a) => {
		return {
			...createCoreSlice(...a),
			populate,

			...createPropertiesSlice(...a),
			...createTemplatesSlice(...a),
			...createOrderingSlice(...a),
			...createPreviewSlice(...a),
			...createFiltersSlice(...a),
			...createFieldSettingsSyncingSlice(...a),

			/*
			 * Unfortunately, all the computed properties need to be in its own slice due to issues
			 * with spread operator and getters.
			 */
			computed: createComputedSlice(...a).computed,
		};
	});
};

const useChoicesStore = createGPPAStore('choices');
const useValuesStore = createGPPAStore('values');

const useGPPAStore = (populate: GPPAPopulate) => {
	if (populate === 'choices') {
		return useChoicesStore;
	}

	return useValuesStore;
};

const storeToFieldSettingsMapping = (state: GPPAState) => {
	const fieldSettingsPrefix = 'gppa-' + state.populate + '-';

	return {
		[fieldSettingsPrefix + 'enabled']: state.enabled,
		[fieldSettingsPrefix + 'object-type']: state.objectType,
		[fieldSettingsPrefix + 'primary-property']: state.primaryProperty,
		[fieldSettingsPrefix + 'ordering-property']: state.orderingProperty,
		[fieldSettingsPrefix + 'ordering-method']: state.orderingMethod,
		[fieldSettingsPrefix + 'filter-groups']: state.filterGroups,
		[fieldSettingsPrefix + 'templates']: state.templates,
		[fieldSettingsPrefix + 'unique-results']: Boolean(state.uniqueResults),
	};
};

subscribeStoreToFieldSettings(useChoicesStore);
subscribeStoreToFieldSettings(useValuesStore);

subscribeFieldSettingsToStore(useChoicesStore, storeToFieldSettingsMapping);
subscribeFieldSettingsToStore(useValuesStore, storeToFieldSettingsMapping);

export default useGPPAStore;
