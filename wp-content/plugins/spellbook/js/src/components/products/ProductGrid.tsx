import Grid from '@gravityforms/components/react/admin/elements/Grid';
import Box from '@gravityforms/components/react/admin/elements/Box';
import ProductCard from './ProductCard';
import type { BaseProduct, ProductType, ProductTypeMap } from '../../types';
import './ProductGrid.css';

interface ProductGridProps {
    products: Record<string, ProductTypeMap[ProductType]>;
    type: ProductType;
}

const ProductGrid = ({ products, type }: ProductGridProps) => {
    return (
        <Box spacing={6}>
            <Grid
                container
				elementType="div"
				alignItems="stretch"
                customClasses={['spellbook-app__product-card-grid']}
                justifyContent="flex-start"
                columnSpacing={5}
                rowSpacing={6}
            >
                {Object.values(products).filter(product => !(product.is_deprecated && !product.is_installed)).map(product => (
                    <Grid
						elementType="div"
                        item
                        key={product.plugin_file}
                        customClasses={[
                            'spellbook-app__product-card-grid-item',
                            `spellbook-app__product-card-grid-item--${product.slug}`
                        ]}
                        customAttributes={{ 'data-test-id': `product-card-${product.slug}` }}
                    >
                        <ProductCard product={product} />
                    </Grid>
                ))}
            </Grid>
        </Box>
    );
};

export default ProductGrid;
