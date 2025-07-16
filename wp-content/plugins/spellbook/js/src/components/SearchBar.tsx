import { useEffect, useRef } from 'react';
import Input from '@gravityforms/components/react/admin/elements/Input';
import MetaBox from '@gravityforms/components/react/admin/modules/MetaBox';
import FilterBar from './FilterBar';
import { ProductFilter } from '../types/filters';
import { BaseProduct, ProductType } from '../types';
import './SearchBar.css';

interface SearchBarProps {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    activeFilter?: ProductFilter;
    onFilterChange?: (filter: ProductFilter) => void;
    products?: Record<string, BaseProduct>;
    type?: ProductType | 'all';
}

const SearchBar = ({ value, placeholder, onChange, activeFilter, onFilterChange, products, type = 'all' }: SearchBarProps) => {
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            // Check for Cmd+K (Mac) or Ctrl+K
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                inputRef.current?.focus();
            }
            // Check for Escape
            if (e.key === 'Escape' && document.activeElement === inputRef.current) {
                e.preventDefault();
                inputRef.current?.blur();
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, []);

    return (
        <MetaBox HeaderContent={undefined} FooterContent={undefined} spacing={4} customClasses={['spellbook-app__search-bar']}>
            <div className="spellbook-app__search-filter-container">
                <div className="spellbook-app__search-input">
                    <Input
                        name="search"
                        placeholder={placeholder || "Search products..."}
                        value={value}
                        onChange={onChange}
                        customAttributes={{
                            'data-test-id': 'product-search',
                            'aria-label': 'Search Products',
                            ref: inputRef
                        }}
                    />
                </div>
                {activeFilter !== undefined && onFilterChange && products && (
                    <FilterBar
                        activeFilter={activeFilter}
                        onFilterChange={onFilterChange}
                        products={products}
                        type={type}
                    />
                )}
            </div>
        </MetaBox>
    );
};

export default SearchBar;
