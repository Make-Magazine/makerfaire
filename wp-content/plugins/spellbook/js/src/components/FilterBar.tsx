import { useMemo } from 'react';
import Button from '@gravityforms/components/react/admin/elements/Button';
import { type ProductFilter } from '../types/filters';
import { getBaseFilterOptions, STATUS_FILTER_OPTIONS, SPECIAL_FILTER_OPTIONS } from '../types/filterConstants';
import { BaseProduct, ProductType } from '../types';
import {
    useHasActive,
    useHasInactive,
    useHasUnregistered,
    useHasUpdates,
    useHasUninstalled
} from '../hooks/features/useFilterStatus';
import './FilterBar.css';
import { isProductUnregistered } from '../helpers/productStatus';
import { useAllLicenses } from '../hooks/api/useLicenses';
import { useSpellbookRegistration } from '../hooks/api/useSpellbook';

interface FilterBarProps {
    activeFilter: ProductFilter;
    onFilterChange: (filter: ProductFilter) => void;
    products: Record<string, BaseProduct>;
    type: ProductType | 'all';
}

const FilterBar = ({ activeFilter, onFilterChange, products, type }: FilterBarProps) => {
    const hasActive = useHasActive(products);
    const hasInactive = useHasInactive(products);
    const hasUnregisteredProducts = useHasUnregistered(products);
    const hasUpdates = useHasUpdates(products);
    const hasUninstalled = useHasUninstalled(products);
	const { data: licenses } = useAllLicenses();
	const { registrationStatus } = useSpellbookRegistration();

    const filterOptions = useMemo(() => {
        const statusOptions = STATUS_FILTER_OPTIONS.filter(option => {
            switch (option.id) {
                case 'active':
                    return hasActive;
                case 'inactive':
                    return hasInactive;
                case 'not-installed':
                    return hasUninstalled;
                default:
                    return true;
            }
        });

        const specialOptions = SPECIAL_FILTER_OPTIONS.filter(option => {
            switch (option.id) {
                case 'update-available':
                    return hasUpdates;
                case 'unregistered':
                    return hasUnregisteredProducts;
                default:
                    return true;
            }
        });

        return [...getBaseFilterOptions(type), ...specialOptions, ...statusOptions];
    }, [hasActive, hasInactive, hasUnregisteredProducts, hasUpdates, hasUninstalled]);

    return (
        <div className="gform-tabs">
            <div className="gform-tabs__tablist" role="tablist">
                {filterOptions.map((option) => (
                    <Button
                        key={option.id}
                        customClasses={{
                            'gform-tabs__tab': true,
                            'gform-tabs__tab--active': activeFilter === option.id
                        }}
                        onClick={() => onFilterChange(option.id)}
                        customAttributes={{
                            'data-test-id': `filter-${option.id}`,
                            'aria-selected': activeFilter === option.id ? 'true' : 'false',
                            role: 'tab'
                        }}
                        size="size-height-m"
                        type="simplified"
                    >
                        {option.label}
                        {option.id === 'unregistered' && hasUnregisteredProducts && (
                            <span className="gform-tabs__tab-badge gform-tabs__tab-badge--unregistered">
                                {Object.values(products).filter(p => isProductUnregistered(p, licenses, registrationStatus)).length}
                            </span>
                        )}
                        {option.id === 'update-available' && hasUpdates && (
                            <span className="gform-tabs__tab-badge gform-tabs__tab-badge--update">
                                {Object.values(products).filter(p => p.has_update && p.is_installed).length}
                            </span>
                        )}
                    </Button>
                ))}
            </div>
        </div>
    );
};

export default FilterBar;
