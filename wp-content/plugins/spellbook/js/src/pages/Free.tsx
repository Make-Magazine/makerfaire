import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import ProductGrid from '../components/products/ProductGrid';
import PageHeader from '../components/PageHeader';
import { useProducts } from '../hooks/api/useProducts';
import LicenseBarAll from '../components/license/LicenseBarAll';
import SearchBar from '../components/SearchBar';
import { useState } from 'react';
import { useProductFilterSearch } from '../hooks/useProductFilterSearch';
import { ProductFilter } from '../types/filters';

const Free = () => {
    const { data: products, isLoading: loading, error } = useProducts();
    const [searchTerm, setSearchTerm] = useState('');
    const [activeFilter, setActiveFilter] = useState<ProductFilter>('all');

    const { results: filteredProducts } = useProductFilterSearch({
        products: products?.free ?? {},
        searchTerm,
        activeFilter
    });

    if (loading) return <RingLoader />;
    if (error) return <div>Error: {error.message}</div>;

    return (
        <div className="gravityperks-settings-app__free">
			<LicenseBarAll />
			{
				Object.entries(products?.free ?? {}).length > 2 && (
					<SearchBar
                        value={searchTerm}
                        onChange={setSearchTerm}
                        placeholder="Search free plugins"
                        activeFilter={activeFilter}
                        onFilterChange={setActiveFilter}
                        products={products?.free ?? {}}
                        type="free"
                    />
				)
			}
            <PageHeader
                title="Free Plugins"
                description="Handy plugins that extend Gravity Forms with simple, focused functionality—no license required."
                type="free"
            />
            {Object.keys(filteredProducts).length > 0 && (
                <ProductGrid products={filteredProducts} type="free" />
            )}
        </div>
    );
};

export default Free;
