import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import ProductGrid from '../components/products/ProductGrid';
import PageHeader from '../components/PageHeader';
import { useProducts } from '../hooks/api/useProducts';
import LicenseBarSuite from '../components/license/LicenseBarSuite';
import SearchBar from '../components/SearchBar';
import { useState } from 'react';
import { useProductFilterSearch } from '../hooks/useProductFilterSearch';
import { ProductFilter } from '../types/filters';

const ShopPage = () => {
    const { data: products, isLoading: loading, error } = useProducts();
    const [searchTerm, setSearchTerm] = useState('');
    const [activeFilter, setActiveFilter] = useState<ProductFilter>('all');

    const { results: filteredProducts } = useProductFilterSearch({
        products: products?.shop ?? {},
        searchTerm,
        activeFilter
    });

    if (loading) return <RingLoader />;
    if (error) return <div>Error: {error.message}</div>;

    return (
        <div className="gravityperks-settings-app__shop">
            <LicenseBarSuite type="shop" />
			{
				Object.entries(products?.shop ?? {}).length > 3 && (
					<SearchBar
                        value={searchTerm}
                        onChange={setSearchTerm}
                        placeholder="Search plugins"
                        activeFilter={activeFilter}
                        onFilterChange={setActiveFilter}
                        products={products?.shop ?? {}}
                        type="shop"
                    />
				)
			}
            <PageHeader
                title="Shop"
                description="Plugins that bring the flexibility of Gravity Forms into WooCommerce."
                type="shop"
            />
            {Object.keys(filteredProducts).length > 0 && (
                <ProductGrid products={filteredProducts} type="shop" />
            )}
        </div>
    );
};

export default ShopPage;
