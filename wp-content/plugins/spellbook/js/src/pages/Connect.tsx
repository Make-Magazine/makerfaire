import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import ProductGrid from '../components/products/ProductGrid';
import EmptyProductsNotice from '../components/products/EmptyProductsNotice';
import PageHeader from '../components/PageHeader';
import { useProducts } from '../hooks/api/useProducts';
import LicenseBarSuite from '../components/license/LicenseBarSuite';
import SearchBar from '../components/SearchBar';
import { useState } from 'react';
import { useProductFilterSearch } from '../hooks/useProductFilterSearch';
import { ProductFilter } from '../types/filters';

const ConnectPage = () => {
    const { data: products, isLoading: loading, error } = useProducts();
    const [searchTerm, setSearchTerm] = useState('');
    const [activeFilter, setActiveFilter] = useState<ProductFilter>('all');

    const { results: filteredProducts } = useProductFilterSearch({
        products: products?.connect ?? {},
        searchTerm,
        activeFilter
    });

    if (loading) return <RingLoader />;
    if (error) return <div>Error: {error.message}</div>;

    const hasProducts = Object.keys(products?.connect ?? {}).length > 0;

    return (
        <div className="gravityperks-settings-app__connect">
            <LicenseBarSuite type="connect" />
			{hasProducts && (
				<SearchBar
					value={searchTerm}
					onChange={setSearchTerm}
					placeholder="Search connections"
					activeFilter={activeFilter}
					onFilterChange={setActiveFilter}
					products={products?.connect ?? {}}
					type="connect"
				/>
			)}
            <PageHeader
                title="Connect"
                description="Integrate your forms with third-party services and applications."
                type="connect"
            />
            {!hasProducts && <EmptyProductsNotice />}
            {Object.keys(filteredProducts).length > 0 && (
                <ProductGrid products={filteredProducts} type="connect" />
            )}
        </div>
    );
};

export default ConnectPage;
