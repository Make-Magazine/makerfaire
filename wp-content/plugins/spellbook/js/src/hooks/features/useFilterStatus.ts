import { useAllLicenses } from '../api/useLicenses';
import { useSpellbookRegistration } from '../api/useSpellbook';
import type { BaseProduct } from '../../types';

import { isProductUnregistered } from '../../helpers/productStatus';

/**
 * Hook to determine if a product should show as unregistered.
 */
export const useUnregisteredStatus = (product: BaseProduct) => {
    const { data: licenses } = useAllLicenses();
    const { registrationStatus } = useSpellbookRegistration();

    return isProductUnregistered(product, licenses, registrationStatus);
};

/**
 * Hook to determine if any products are active
 */
export const useHasActive = (products: Record<string, BaseProduct>) => {
    return Object.values(products).some(product => product.is_active);
};

/**
 * Hook to determine if any products are inactive
 */
export const useHasInactive = (products: Record<string, BaseProduct>) => {
    return Object.values(products).some(product => product.is_installed && !product.is_active);
};

/**
 * Hook to determine if any products have updates available
 */
export const useHasUpdates = (products: Record<string, BaseProduct>) => {
    return Object.values(products).some(product => product.has_update && product.is_installed);
};

/**
 * Hook to determine if any products are not installed
 */
export const useHasUninstalled = (products: Record<string, BaseProduct>) => {
    return Object.values(products).some(product => !product.is_installed);
};

/**
 * Hook to determine if any products in a collection are unregistered
 */
export const useHasUnregistered = (products: Record<string, BaseProduct>) => {
    const { data: licenses } = useAllLicenses();
    const { registrationStatus } = useSpellbookRegistration();

    return Object.values(products).some(product =>
        isProductUnregistered(product, licenses, registrationStatus)
    );
};
