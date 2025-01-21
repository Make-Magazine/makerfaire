import { StateCreator } from 'zustand';
import { PropertiesSlice } from './properties';
import { FieldSettingsSyncingSlice } from '../../framework/field-settings-syncing';
import { CoreSlice } from './core';
import { ComputedSlice } from './computed';

interface GPPAFilter {
	property: string;
	operator: string;
	value: string | null;
	uuid: number;
}

export interface FiltersSlice {
	filterGroups: GPPAFilter[][];

	setFilterGroups: (filterGroups: GPPAFilter[][]) => void;

	addFilterGroup: () => void;

	addFilter: (filterIndex: number, filterGroupIndex: number) => void;

	removeFilter: (index: number, filterGroupIndex: number) => void;

	updateFilter: (
		groupIndex: number,
		filterIndex: number,
		filterDraft: Partial<GPPAFilter>
	) => void;

	shouldFilterUseValue: (groupIndex: number, filterIndex: number) => boolean;
}

const filterFactory = () => {
	const date = new Date();

	return {
		property: '',
		operator: 'is',
		value: '',
		uuid: date.getTime(),
	};
};

export const initialStateFilters: Pick<FiltersSlice, 'filterGroups'> = {
	filterGroups: [],
};

const createFiltersSlice: StateCreator<
	PropertiesSlice &
		FiltersSlice &
		FieldSettingsSyncingSlice &
		CoreSlice &
		ComputedSlice,
	[],
	[],
	FiltersSlice
> = (set, get) => ({
	...initialStateFilters,

	setFilterGroups(filterGroups: GPPAFilter[][]) {
		set({ filterGroups });
	},

	addFilterGroup() {
		get().setFilterGroups([...get().filterGroups, [filterFactory()]]);
	},

	addFilter(filterIndex: number, filterGroupIndex: number) {
		const updatedFilterGroups = [...get().filterGroups];

		if (!isNaN(filterIndex)) {
			updatedFilterGroups[filterGroupIndex].splice(
				filterIndex + 1,
				0,
				filterFactory()
			);

			set({ filterGroups: updatedFilterGroups });

			return;
		}

		updatedFilterGroups[filterGroupIndex].push(filterFactory());

		get().setFilterGroups(updatedFilterGroups);
	},

	removeFilter(index: number, filterGroupIndex: number) {
		const updatedFilterGroups = [...get().filterGroups];

		updatedFilterGroups[filterGroupIndex].splice(index, 1);

		if (updatedFilterGroups[filterGroupIndex].length === 0) {
			updatedFilterGroups.splice(filterGroupIndex, 1);
		}

		get().setFilterGroups(updatedFilterGroups);
	},

	updateFilter(
		groupIndex: number,
		filterIndex: number,
		filterDraft: Partial<GPPAFilter>
	) {
		const updatedFilterGroups = [...get().filterGroups];
		const currentFilter = updatedFilterGroups[groupIndex][filterIndex];

		if (!get().shouldFilterUseValue(groupIndex, filterIndex)) {
			// set to null to explicity show that this value is not used
			filterDraft.value = null;
		}

		const finalDraft = {
			...currentFilter,
			...filterDraft,
		};

		const nextProperty = get().getProperty(finalDraft.property);
		const operators = nextProperty?.operators ?? [];

		/**
		 * If the property has changed, the operator may need updated as well
		 * _if_ the new property doesn't support the currently selected operator
		 * E.g. if the newly selected property does not support the "is" operator
		 * and "is" is currently selected, change the operator to the first operator
		 * that the newly selected property supports.
		 */
		if (!operators.includes(currentFilter.operator)) {
			filterDraft.operator = nextProperty?.operators?.[0];
		}

		updatedFilterGroups[groupIndex][filterIndex] = finalDraft;

		set({ filterGroups: updatedFilterGroups });
	},

	shouldFilterUseValue(groupIndex: number, filterIndex: number) {
		const filter = get().filterGroups?.[groupIndex]?.[filterIndex];

		if (!filter) {
			return true;
		}

		/**
		 * Filter whether the value select should be used / shown in a filter.
		 *
		 * @param {string}   shouldShow       If the value select should be shown.
		 * @param {Object}   args             Arguments passed to the filter.
		 * @param {Object}   args.filter      The filter object.
		 * @param {Function} args.get         The Zustand store getter.
		 * @param {Function} args.set         The Zustand store setter.
		 * @param {number}   args.groupIndex  The index of the filter group.
		 * @param {number}   args.filterIndex The index of the filter.
		 *
		 * @since 2.0.16 TODO TODO TODO UPDATE RIGHT BEFORE MERGING
		 */
		return window.gform.applyFilters(
			'gppa_filter_group_show_value_select',
			true,
			{
				filter,
				groupIndex,
				filterIndex,
				get,
				set,
			}
		);
	},
});

export default createFiltersSlice;
