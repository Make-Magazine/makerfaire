import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import type { LicensedProductType, LicenseData, LicenseResponse, BaseProduct, LicenseError } from '../../types';
import useStore from '../../store';

export const useAllLicenses = () => {
	const forceLicenseRefresh = useStore(state => state.forceLicenseRefresh);
	const setForceLicenseRefresh = useStore(state => state.setForceLicenseRefresh);

	return useQuery<Record<LicensedProductType, LicenseData>>({
		queryKey: ['licenses'],
		queryFn: async ({ signal }) => {
			const licenses = await apiFetch<Record<LicensedProductType, LicenseData>>({
				path: `/gwiz/v1/license${forceLicenseRefresh ? '?force=1' : ''}`,
				signal
			});

			if (forceLicenseRefresh) {
				setForceLicenseRefresh(false);
			}

			return licenses;
		},
		staleTime: Infinity // Only refetch when we explicitly invalidate
	});
};

export const useLicense = (type: LicensedProductType, useBundleIfAvailable = false) => {
	const allLicensesQuery = useAllLicenses();

	// Determine actual type (bundle-first for perk/connect if requested)
	const actualType = useBundleIfAvailable && (type === 'perk' || type === 'connect') && allLicensesQuery.data?.['wiz-bundle']?.valid
		? 'wiz-bundle'
		: type;

	return {
		data: allLicensesQuery.data?.[actualType],
		isLoading: allLicensesQuery.isLoading,
		error: allLicensesQuery.error,
		actualType
	};
};

export const useValidateLicenseWithUnknownProductType = () => {
	// Define validation order: bundles first, then individual licenses
	// This prevents showing "upgrade" notification when entering a bundle key directly
	const validationTypes: LicensedProductType[] = ['wiz-bundle', 'perk', 'connect', 'shop'];
	const mutations = {
		perk: useLicenseMutations('perk'),
		connect: useLicenseMutations('connect'),
		shop: useLicenseMutations('shop'),
		'wiz-bundle': useLicenseMutations('wiz-bundle'),
	};

	return {
		mutate: async (key: string, options?: { onSuccess?: () => void }) => {
			// Reset all mutations
			Object.values(mutations).forEach(mutation => mutation.validate.reset());

			// Try each validation type in order
			for (const type of validationTypes) {
				try {
					await mutations[type].validate.mutateAsync(key);
					options?.onSuccess?.();
					return;
				} catch (error) {
					// Only continue if it's a license mismatch, otherwise re-throw
					if ((error as LicenseError).code !== 'license_mismatch') {
						throw error;
					}
					// Continue to next type if it's just a mismatch
				}
			}
		},
		reset: () => {
			Object.values(mutations).forEach(mutation => mutation.validate.reset());
		},
		isPending: Object.values(mutations).some(mutation => mutation.validate.isPending),
		// Return the first non-mismatch error
		error: Object.values(mutations)
			.map(mutation => mutation.validate.error)
			.find(error => error && error.code !== 'license_mismatch') || undefined
	};
};

export const useLicenseMutations = (productType: LicensedProductType, useBundleIfAvailable = false) => {
	const queryClient = useQueryClient();

	// Determine actual type (bundle-first for perk/connect if requested)
	const type = useBundleIfAvailable && (productType === 'perk' || productType === 'connect')
		? (useAllLicenses().data?.['wiz-bundle']?.valid ? 'wiz-bundle' : productType)
		: productType;

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
			onSuccess: async (data) => {
				// Check if this was a migration
				if (data.migrated && data.from_type && data.to_type) {
					// Backend has already migrated - refetch to get the correct state
					await queryClient.refetchQueries({ queryKey: ['licenses'] });

					// Show migration success message
					useStore.getState().showNotification(
						data.message || 'Your license has been upgraded to the Wiz Bundle!',
						'success'
					);
				} else {
					// Normal validation, just update the license
					updateLicenseInCache(data.license_data);
				}

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
				// Update license data - the type should already be correct since we're using
				// usePerksMutations/useConnectMutations which auto-select bundle if available
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
