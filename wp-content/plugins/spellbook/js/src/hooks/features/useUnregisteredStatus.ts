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
