import { StateCreator } from 'zustand';
import { PropertiesSlice } from './properties';

export interface OrderingSlice {
	orderingProperty: string;
	orderingMethod: string;

	setOrderingProperty: (orderingProperty: string) => void;
	setOrderingMethod: (orderingMethod: string) => void;
}

export const initialStateOrdering: Pick<
	OrderingSlice,
	'orderingProperty' | 'orderingMethod'
> = {
	orderingProperty: '',
	orderingMethod: 'asc',
};

const createOrderingSlice: StateCreator<
	PropertiesSlice & OrderingSlice,
	[],
	[],
	OrderingSlice
> = (set, get) => ({
	...initialStateOrdering,

	setOrderingProperty(orderingProperty: string) {
		set({ orderingProperty });
	},

	setOrderingMethod(orderingMethod: string) {
		set({ orderingMethod });
	},
});

export default createOrderingSlice;
