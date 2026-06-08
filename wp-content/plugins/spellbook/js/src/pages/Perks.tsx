import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import ProductGrid from '../components/products/ProductGrid';
import EmptyProductsNotice from '../components/products/EmptyProductsNotice';
import PageHeader from '../components/PageHeader';
import LicenseBarSuite from '../components/license/LicenseBarSuite';
import SearchBar from '../components/SearchBar';
import { useState } from 'react';
import { useProductFilterSearch } from '../hooks/useProductFilterSearch';
import { ProductFilter } from '../types/filters';
import { useProducts } from '../hooks/api/useProducts';

const PerksPage = () => {
    const { data: products, isLoading: loading, error } = useProducts();
    const [searchTerm, setSearchTerm] = useState('');
    const [activeFilter, setActiveFilter] = useState<ProductFilter>('all');

    const { results: filteredProducts } = useProductFilterSearch({
        products: products?.perk ?? {},
        searchTerm,
        activeFilter
    });

    if (loading) return <RingLoader />;
    if (error) return <div>Error: {error.message}</div>;

    const hasProducts = Object.keys(products?.perk ?? {}).length > 0;

    return (
        <div className="gravityperks-settings-app__perks">
            <LicenseBarSuite type="perk" />
			{hasProducts && (
				<SearchBar
					value={searchTerm}
					onChange={setSearchTerm}
					placeholder="Search perks"
					activeFilter={activeFilter}
					onFilterChange={setActiveFilter}
					products={products?.perk ?? {}}
					type="perk"
				/>
			)}
            <PageHeader
                title="Perks"
                description="Install and manage your Gravity Perks. Each perk adds new functionality to Gravity Forms."
                type="perk"
            />
            {!hasProducts && <EmptyProductsNotice />}
            {Object.keys(filteredProducts).length > 0 && (
                <ProductGrid products={filteredProducts} type="perk" />
            )}
        </div>
    );
};

export default PerksPage;
