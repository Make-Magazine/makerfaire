import { StateCreator } from 'zustand';
import { PropertiesSlice } from './properties';
import { FieldSettingsSyncingSlice } from '../../framework/field-settings-syncing';
import { CoreSlice } from './core';

interface GPPAFilter {
	property: string;
	operator: string;
	value: string;
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
	PropertiesSlice & FiltersSlice & FieldSettingsSyncingSlice & CoreSlice,
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

		updatedFilterGroups[groupIndex][filterIndex] = {
			...updatedFilterGroups[groupIndex][filterIndex],
			...filterDraft,
		};

		set({ filterGroups: updatedFilterGroups });
	},
});

export default createFiltersSlice;
