import type { BaseProduct, LicenseData, LicensedProductType } from '../types';

interface RegistrationStatus {
    is_registered: boolean;
}

/**
 * Get the appropriate license for GCGS based on eligibility.
 * Returns Perk license if it's GCGS eligible and valid, otherwise Connect license.
 */
const getGcgsLicense = (
    licenses: Record<string, LicenseData | undefined> | null | undefined
): LicenseData | undefined => {
    const perkLicense = licenses?.perk;
    if (perkLicense?.gcgs_eligible && perkLicense.valid) {
        return perkLicense;
    }
    return licenses?.connect;
};

/**
 * Get the appropriate license for a product.
 * Handles special case for GCGS, otherwise returns license based on product type.
 */
export const getLicenseForProduct = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
): LicenseData | undefined => {
    if (
        product.plugin_file === 'gc-google-sheets/gc-google-sheets.php' ||
        product.plugin_file === 'gp-google-sheets/gp-google-sheets.php'
    ) {
        return getGcgsLicense(licenses);
    }
    return licenses?.[product.type];
};

/**
 * Check if a product supports registration
 */
export const supportsRegistration = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
) => {
    if (product.type === 'free') {
        return false;
    }

    const license = getLicenseForProduct(product, licenses);
    return license?.registered_products !== null;
};

/**
 * Check if a license has unlimited registrations
 */
export const hasUnlimitedRegistrations = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
) => {
    const license = getLicenseForProduct(product, licenses);
    if (!license?.registered_products) {
        return false;
    }

    return license.registered_products_limit === 0;
};

/**
 * Check if a product is registered
 */
export const isProductRegistered = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
) => {
    const license = getLicenseForProduct(product, licenses);

	if (product.plugin_file === 'gc-google-sheets/gc-google-sheets.php') {
        if (license?.gcgs_eligible) {
            return true;
        }
    }

    // GSPC is automatically registered when license is valid
    if (product.type === 'shop' && license?.valid) {
        return true;
    }

    if (!license?.registered_products) {
        return false;
    }

	// Convert registered_products to array if object.
	const registeredProducts = Array.isArray(license.registered_products)
		? license.registered_products
		: Object.values(license.registered_products);

    return registeredProducts.includes(product.ID.toString());
};

/**
 * Check if a product can be registered
 */
export const canRegisterProduct = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
) => {
    const license = getLicenseForProduct(product, licenses);
    if (!license?.valid) {
        return false;
    }

    // No registration system or unlimited registrations
    if (!supportsRegistration(product, licenses) || hasUnlimitedRegistrations(product, licenses)) {
        return true;
    }

    // Has available slots
    return license.registered_products.length < license.registered_products_limit;
};

/**
 * Check if a product can be updated
 */
export const canUpdateProduct = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined
) => {
    if (!product.has_update) {
        return false;
    }

    if (product.type === 'free') {
        return true;
    }

    const license = getLicenseForProduct(product, licenses);
    if (!license?.valid) {
        return false;
    }

    // No registration system or unlimited registrations
    if (!supportsRegistration(product, licenses) || hasUnlimitedRegistrations(product, licenses)) {
        return true;
    }

    // Already registered
    if (isProductRegistered(product, licenses)) {
        return true;
    }

    return false;
};

/**
 * Check if a product should be considered unregistered
 *
 * For free plugins:
 * - Shows as unregistered if the user has neither registered their email
 *   nor has any valid license (perk, connect, or shop)
 *
 * For licensed plugins (perk, connect, shop):
 * - Some product suites (e.g., Shop) don't use the concept of registered products,
 *   in which case registered_products will be null
 * - For suites that do use registered products:
 *   - registered_products_limit = 0 means unlimited registrations
 *   - Otherwise, shows as unregistered if the product isn't registered and
 *     the license has a registration limit
 *
 * Special case for GCGS:
 * - First checks if it's registered through a valid GP license with GCGS access
 * - If not, falls back to checking Connect license registration
 */
export const isProductUnregistered = (
    product: BaseProduct,
    licenses: Record<string, LicenseData | undefined> | null | undefined,
    registrationStatus: RegistrationStatus | null | undefined
) => {
    if (!product.is_installed) {
        return false;
    }

    if (product.type === 'free') {
        return !registrationStatus?.is_registered &&
               !licenses?.perk?.key &&
               !licenses?.connect?.key &&
               !licenses?.shop?.key;
    }

	 const license = getLicenseForProduct(product, licenses);

	if (product.plugin_file === 'gc-google-sheets/gc-google-sheets.php') {
        if (license?.gcgs_eligible) {
            return false;
        }
    }

    return supportsRegistration(product, licenses) &&
           !hasUnlimitedRegistrations(product, licenses) &&
           !isProductRegistered(product, licenses);
};
