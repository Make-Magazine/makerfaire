import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import ProductGrid from '../components/products/ProductGrid';
import PageHeader from '../components/PageHeader';
import { useProducts } from '../hooks/api/useProducts';
import LicenseBarAll from '../components/license/LicenseBarAll';
import SearchBar from '../components/SearchBar';
import { useState } from 'react';
import { useProductFilterSearch } from '../hooks/useProductFilterSearch';
import { ProductFilter } from '../types/filters';

const AllPage = () => {
    const { data: products, isLoading: loading, error } = useProducts();
    const [searchTerm, setSearchTerm] = useState('');
    const [activeFilter, setActiveFilter] = useState<ProductFilter>('all');

    const { results: filteredPerks } = useProductFilterSearch({
        products: products?.perk ?? {},
        searchTerm,
        activeFilter
    });

    const { results: filteredConnect } = useProductFilterSearch({
        products: products?.connect ?? {},
        searchTerm,
        activeFilter
    });

    const { results: filteredShop } = useProductFilterSearch({
        products: products?.shop ?? {},
        searchTerm,
        activeFilter
    });

    const { results: filteredFree } = useProductFilterSearch({
        products: products?.free ?? {},
        searchTerm,
        activeFilter
    });

	if (loading) return <RingLoader />;
    if (error) return <div>Error: {error.message}</div>;

    return (
        <div className="gravityperks-settings-app__perks">
			<LicenseBarAll />
			<SearchBar
                value={searchTerm}
                onChange={setSearchTerm}
                placeholder={`Search ${Object.values(products ?? {}).reduce((count, typeProducts) => count + Object.keys(typeProducts).length, 0)} plugins`}
                activeFilter={activeFilter}
                onFilterChange={setActiveFilter}
                products={Object.entries(products ?? {}).reduce((acc, [type, typeProducts]) => ({
                    ...acc,
                    ...typeProducts
                }), {})}
                type="all"
            />

			{
				Object.keys(filteredPerks).length > 0 &&
				(
					<>
						<PageHeader
							title="Perks"
							description="Install and manage your Gravity Perks. Each perk adds new functionality to Gravity Forms."
							type="perk"
						/>
						<ProductGrid products={filteredPerks} type="perk" />
					</>
				)
			}

			{
				Object.keys(filteredConnect).length > 0 &&
				(
					<>
						<PageHeader
							title="Connect"
							description="Integrate your forms with third-party services and applications."
							type="connect"
						/>
						<ProductGrid products={filteredConnect} type="connect" />
					</>
				)
			}

			{
				Object.keys(filteredShop).length > 0 &&
				(
					<>
						<PageHeader
							title="Shop"
							description="Plugins that bring the flexibility of Gravity Forms into WooCommerce."
							type="shop"
						/>
						<ProductGrid products={filteredShop} type="shop" />
					</>
				)
			}

            {
				Object.keys(filteredFree).length > 0 &&
				(
					<>
						<PageHeader
							title="Free Plugins"
							description="Handy plugins that extend Gravity Forms with simple, focused functionality—no license required."
							type="free"
						/>
						<ProductGrid products={filteredFree} type="free" />
					</>
				)
			}
        </div>
    );
};

export default AllPage;
