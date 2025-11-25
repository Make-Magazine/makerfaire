export interface BaseProduct {
    ID: number;
    name: string;
    version: string;
    new_version?: string;
    has_update: boolean;
    slug: string;
    plugin_file: string;
    plugin: string;
    homepage: string;
    documentation: string;
    sections: {
        description: string;
    };
    banners: {
        high?: string;
        low?: string;
    };
    icons: {
        '1x'?: string;
        '2x'?: string;
    };
    categories: string[];
    type: ProductType;
    last_updated: string;
    download_link: string;
	can_uninstall: boolean;
	is_legacy_free_plugin: boolean;
    is_installed: boolean;
    is_active: boolean;
    has_settings: boolean;
    is_deprecated?: boolean;
}

export interface DetailedProduct extends BaseProduct {
    sections: {
        description: string;
        changelog: string;
    };
}
export type ProductType = 'perk' | 'connect' | 'shop' | 'free';
export type LicensedProductType = 'perk' | 'connect' | 'shop' | 'wiz-bundle';

export interface PerkProduct extends BaseProduct {
    type: 'perk';
    form_settings?: boolean;
    field_settings?: boolean;
}

export interface ConnectProduct extends BaseProduct {
    type: 'connect';
    connection_status: 'connected' | 'disconnected';
    last_sync?: string;
}

export interface ShopProduct extends BaseProduct {
    type: 'shop';
    purchase_status?: 'purchased' | 'not_purchased';
}

export type ProductTypeMap = {
    perk: PerkProduct;
    connect: ConnectProduct;
    shop: ShopProduct;
	free: BaseProduct;
}

// License Management Types
export type LicenseAction =
    | 'validate'
    | 'register'
    | 'deactivate'
    | 'register_product'
    | 'deregister_product';

export interface LicenseData {
    key: string | false;
    status: 'valid' | 'invalid' | 'expired' | 'item_name_mismatch' | 'site_inactive';
    type: string;
    expiration: string | 'lifetime' | null;
    site_count: number;
    site_limit: number;
    registered_products: string[];
	registered_products_limit: number;
    product_type: LicensedProductType;
	valid: boolean;
	is_bundle?: boolean;
	manage_url: string;
	extend_url: string;
	upgrade_url: string;
	gcgs_eligible: string | null;
}

export interface LicenseResponse {
    success: boolean;
    message: string;
    license_data: LicenseData | null;
	migrated?: boolean;
	from_type?: LicensedProductType;
	to_type?: LicensedProductType;
    product?: {
        id: string;
        is_registered: boolean;
        registered_at: string;
    };
}

export type AllLicensesResponse = Record<LicensedProductType, LicenseData | undefined>;

export interface LicenseError {
    code: 'invalid_license' | 'expired_license' | 'site_limit_reached' |
          'product_limit_reached' | 'already_registered' | 'not_registered' |
          'invalid_product_type' | 'invalid_product' | 'license_mismatch';
    message: string;
    data?: {
        status: number;
        [key: string]: any;
    };
}

export interface LicenseStateData {
    data: LicenseData | null;
    loading: Record<LicenseAction, boolean>;
    error: LicenseError | null;
}

export type LicenseState = Record<LicensedProductType, LicenseStateData>;

export interface LicenseActions {
    // Fetch operations
    getLicenses: (force?: boolean) => Promise<AllLicensesResponse>;
    getLicense: (productType: LicensedProductType) => Promise<LicenseData>;

    // License operations
    validateLicense: (productType: LicensedProductType, key: string) => Promise<LicenseResponse>;
    deactivateLicense: (productType: LicensedProductType) => Promise<LicenseResponse>;

    // Product registration
    registerProduct: (productType: LicensedProductType, id: string) => Promise<LicenseResponse>;
    deregisterProduct: (productType: LicensedProductType, id: string) => Promise<LicenseResponse>;

    // State management
    clearLicenseError: (productType: LicensedProductType) => void;
    getLicenseError: (productType: LicensedProductType) => LicenseError | null;
    isLicenseActionLoading: (productType: LicensedProductType, action: LicenseAction) => boolean;
    getLicenseState: (productType: LicensedProductType) => LicenseStateData | null;
}
