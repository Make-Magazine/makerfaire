import { useMemo } from 'react';
import { useProductSearch } from './useProductSearch';
import type { BaseProduct } from '../types';
import type { ProductFilter } from '../types/filters';
import { useHasUnregistered, useHasUpdates } from './features/useFilterStatus';
import { useAllLicenses } from './api/useLicenses';
import { useSpellbookRegistration } from './api/useSpellbook';
import { isProductUnregistered } from '../helpers/productStatus';

interface UseProductFilterSearchProps<T extends BaseProduct> {
    products: Record<string, T>;
    searchTerm: string;
    activeFilter: ProductFilter;
}

export const useProductFilterSearch = <T extends BaseProduct>({
    products,
    searchTerm,
    activeFilter
}: UseProductFilterSearchProps<T>) => {
    // First apply search
    const { results: searchResults } = useProductSearch({ products, searchTerm });

    // Check if filters are available
    const hasUnregisteredProducts = useHasUnregistered(products);
    const hasUpdates = useHasUpdates(products);
    const { data: licenses } = useAllLicenses();
    const { registrationStatus } = useSpellbookRegistration();

    // Then apply filters
    const filteredResults = useMemo(() => {
        // Only return all results for 'all' filter
        if (activeFilter === 'all') {
            return searchResults;
        }

        // For other filters, always apply the filter even if the tab is hidden

        return Object.fromEntries(
            Object.entries(searchResults).filter(([_, product]) => {
                switch (activeFilter) {
                    case 'active':
                        return product.is_active;
                    case 'inactive':
                        return !product.is_active && product.is_installed;
                    case 'update-available':
                        return product.has_update;
                    case 'not-installed':
                        return !product.is_installed;
                    case 'unregistered':
                        return isProductUnregistered(product, licenses, registrationStatus);
                    default:
                        return true;
                }
            })
        );
    }, [searchResults, activeFilter, hasUnregisteredProducts, hasUpdates, licenses, registrationStatus]);

    return { results: filteredResults };
};
