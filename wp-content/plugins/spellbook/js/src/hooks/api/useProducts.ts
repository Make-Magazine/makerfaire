import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { BaseProduct, DetailedProduct, LicensedProductType, LicenseData } from '../../types';
import useStore from '../../store';

export const useProducts = () => {
  const forceProductRefresh = useStore(state => state.forceProductRefresh);

  return useQuery<Record<string, Record<string, BaseProduct>>>({
    queryKey: ['products'],
    queryFn: async ({ signal }) => {
      const products = await apiFetch<BaseProduct[]>({
        path: `/gwiz/v1/products${forceProductRefresh ? '?force=1' : ''}`,
        signal
      });

      // Replace &amp; with & in product data
      const sanitizedProducts = products.map(product => ({
        ...product,
        name: product.name?.replace(/&amp;/g, '&'), // We do this since React will escape & in the name
      }));

      // Transform array into grouped object
      return sanitizedProducts.reduce((acc, product) => {
        if (!acc[product.type]) {
          acc[product.type] = {};
        }
        acc[product.type][product.plugin_file] = product;
        return acc;
      }, {} as Record<string, Record<string, BaseProduct>>);
    },
    staleTime: Infinity // Only refetch when we explicitly invalidate
  });
};

export const useProductDetails = (productId: number) => {
  return useQuery<DetailedProduct>({
    queryKey: ['product-details', productId],
    queryFn: async ({ signal }) => {
      return await apiFetch<DetailedProduct>({
        path: `/gwiz/v1/products/${productId}/details`,
        signal
      });
    },
    enabled: false, // Only fetch when explicitly called
    staleTime: Infinity
  });
};

export const useProductMutations = () => {
  const queryClient = useQueryClient();

  const updateProductInCache = (product: BaseProduct, updates: Partial<BaseProduct>) => {
    queryClient.setQueryData(['products'], (old: any) => ({
      ...old,
      [product.type]: {
        ...old[product.type],
        [product.plugin_file]: {
          ...product,
          ...updates
        }
      }
    }));
  };

  return {
    install: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}/install`,
          method: 'POST'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);

        // Optimistically update license data if it's a licensed product
        if (product.type !== 'free') {
          queryClient.setQueryData(['licenses'], (old: Record<LicensedProductType, LicenseData>) => {
            const type = product.type as LicensedProductType;
            if (!old || !old[type]) return old;
            return {
              ...old,
              [type]: {
                ...old[type],
                registered_products: [...old[type].registered_products, product.ID.toString()]
              }
            };
          });

		  // Still invalidate to ensure accuracy with server state
       	 queryClient.invalidateQueries({ queryKey: ['licenses'] });
        }

        useStore.getState().showNotification('Plugin installed successfully', 'success');
      }
    }),
    activate: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}/activate`,
          method: 'POST'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);
      }
    }),
    deactivate: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}/deactivate`,
          method: 'POST'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);
      }
    }),
    delete: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}`,
          method: 'DELETE'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);
        useStore.getState().showNotification('Plugin deleted successfully', 'success');
      }
    }),
    uninstall: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}/uninstall`,
          method: 'POST'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);
        useStore.getState().showNotification('Plugin uninstalled successfully', 'success');
      }
    }),
    update: useMutation<BaseProduct, Error, BaseProduct>({
      mutationFn: (product) =>
        apiFetch({
          path: `/gwiz/v1/products/${product.slug}/update`,
          method: 'POST'
        }),
      onSuccess: (data, product) => {
        updateProductInCache(product, data);
        useStore.getState().showNotification('Plugin updated successfully', 'success');
      }
    })
  };
};
