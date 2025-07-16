export type ProductFilter =
    | 'all'
    | 'active'
    | 'inactive'
    | 'update-available'
    | 'not-installed'
    | 'registered'
    | 'unregistered';

export interface FilterOption {
    id: ProductFilter;
    label: string;
}
