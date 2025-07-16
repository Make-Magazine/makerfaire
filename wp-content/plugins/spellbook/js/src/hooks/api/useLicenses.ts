import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { LicensedProductType, LicenseData, LicenseResponse, BaseProduct, LicenseError } from '../../types';
import useStore from '../../store';

export const useAllLicenses = () => {
	const forceLicenseRefresh = useStore(state => state.forceLicenseRefresh);

	return useQuery<Record<LicensedProductType, LicenseData>>({
		queryKey: ['licenses'],
		queryFn: async ({ signal }) => {
			return apiFetch({
				path: `/gwiz/v1/license${forceLicenseRefresh ? '?force=1' : ''}`,
				signal
			});
		},
		staleTime: Infinity // Only refetch when we explicitly invalidate
	});
};

export const useLicense = (type: LicensedProductType) => {
	const allLicensesQuery = useAllLicenses();

	return {
		data: allLicensesQuery.data?.[type],
		isLoading: allLicensesQuery.isLoading,
		error: allLicensesQuery.error
	};
};

export const useValidateLicenseWithUnknownProductType = () => {
	const { validate: validatePerk } = useLicenseMutations('perk');
	const { validate: validateConnect } = useLicenseMutations('connect');
	const { validate: validateShop } = useLicenseMutations('shop');

	return {
		mutate: async (key: string, options?: { onSuccess?: () => void }) => {
			// Reset errors from previous attempts
			validatePerk.reset();
			validateConnect.reset();
			validateShop.reset();

			// Try Perks first (most likely)
			try {
				await validatePerk.mutateAsync(key);
				options?.onSuccess?.();
				return;
			} catch (error) {
				// Only continue if it's a mismatch
				if ((error as LicenseError).code === 'license_mismatch') {
					// Try Connect second
					try {
						await validateConnect.mutateAsync(key);
						options?.onSuccess?.();
						return;
					} catch (error) {
						// Only continue if it's a mismatch
						if ((error as LicenseError).code === 'license_mismatch') {
							// Finally try Shop
							await validateShop.mutateAsync(key);
							options?.onSuccess?.();
						}
					}
				}
			}
		},
		reset: () => {
			validatePerk.reset();
			validateConnect.reset();
			validateShop.reset();
		},
		isPending: validatePerk.isPending || validateConnect.isPending || validateShop.isPending,
		// Only show current error if it's not a mismatch
		error: validatePerk.error?.code !== 'license_mismatch' ? validatePerk.error :
			validateConnect.error?.code !== 'license_mismatch' ? validateConnect.error :
			validateShop.error?.code !== 'license_mismatch' ? validateShop.error :
			undefined
	};
};

export const useLicenseMutations = (type: LicensedProductType) => {
	const queryClient = useQueryClient();

	const updateLicenseInCache = (updates: Partial<LicenseData> | null, replace = false) => {
		queryClient.setQueryData(['licenses'], (old: Record<LicensedProductType, LicenseData> | undefined) => {
			if (!old) return old;
			if (updates === null) {
				// Remove the license from the cache
				const { [type]: _, ...rest } = old;
				return rest;
			}

			return {
				...old,
				[type]: replace ? updates : {
					...old[type],
					...updates
				}
			};
		});
	};

	return {
		validate: useMutation<LicenseResponse, LicenseError, string>({
			mutationFn: (key) =>
				apiFetch({
					path: `/gwiz/v1/license/${type}/validate`,
					method: 'POST',
					data: { license_key: key }
				}),
			onSuccess: (data) => {
				updateLicenseInCache(data.license_data);

				// Reset products query to ensure we have the latest data
				queryClient.invalidateQueries({ queryKey: ['products'] });
			},
			// Empty handler to prevent global error handling
			onError: () => {}
		}),
		register: useMutation<LicenseResponse, LicenseError, string>({
			mutationFn: (id) =>
				apiFetch({
					path: `/gwiz/v1/license/${type}/products/${id}/register`,
					method: 'POST'
				}),
			onSuccess: (data) => {
				// Update license data with new registered_products array
				updateLicenseInCache(data.license_data);

				// Update product's is_registered flag
				if (data.product) {
					queryClient.setQueryData(
						['products'],
						(products: Record<string, Record<string, BaseProduct>>) => {
							if (!products) {
								return products;
							}

							// Find product in nested structure
							for (const [productType, typeProducts] of Object.entries(products)) {
								for (const [pluginFile, product] of Object.entries(typeProducts)) {
									if (product.ID.toString() === data.product!.id) {
										return {
											...products,
											[productType]: {
												...products[productType],
												[pluginFile]: {
													...product,
													is_registered: true
												}
											}
										};
									}
								}
							}
							return products;
						}
					);
				}
			}
		}),
		deactivate: useMutation<LicenseResponse, LicenseError, void>({
			mutationFn: () =>
				apiFetch({
					path: `/gwiz/v1/license/${type}/deactivate`,
					method: 'POST'
				}),
			onSuccess: (data) => {
				console.log('Deactivated license:', data);

				// For deactivate, replace the entire license data since it's a complete reset
				updateLicenseInCache(data.license_data, true);

				// Reset products query to ensure we have the latest data
				queryClient.invalidateQueries({ queryKey: ['products'] });
			}
		})
	};
};
