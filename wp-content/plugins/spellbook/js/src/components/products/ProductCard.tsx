import { memo, useMemo, useState } from 'react';
import type { FuseResultMatch } from 'fuse.js';
import { __, sprintf } from '@wordpress/i18n';
import { useProductSearch } from '../../hooks/useProductSearch';
import { useAllLicenses } from '../../hooks/api/useLicenses';
import { useUnregisteredStatus } from '../../hooks/features/useUnregisteredStatus';
import Button from '@gravityforms/components/react/admin/elements/Button';
import DotIndicator from '@gravityforms/components/react/admin/modules/Indicators/DotIndicator';
import Grid from '@gravityforms/components/react/admin/elements/Grid';
import Heading from '@gravityforms/components/react/admin/elements/Heading';
import IntegrationCard from '@gravityforms/components/react/admin/modules/Cards/IntegrationCard';
import Tag from '@gravityforms/components/react/admin/elements/Tag';
import Text from '@gravityforms/components/react/admin/elements/Text';
import Toggle from '@gravityforms/components/react/admin/elements/Toggle';
import PluginPlaceholder from '../../svgs/plugin-placeholder.svg'
import { Modal } from '@wordpress/components';
import PluginSettingsFrame from './PluginSettingsFrame';
import type { BaseProduct, LicensedProductType, LicenseData, LicenseResponse } from '../../types';
import './ProductCard.css';
import { useProductMutations, useProductDetails } from '../../hooks/api/useProducts';
import { useLicenseMutations } from '../../hooks/api/useLicenses';
import { canRegisterProduct, canUpdateProduct, isProductRegistered, getLicenseForProduct } from '../../helpers/productStatus';
import { addUtmParams } from '../../helpers/urls';

interface ProductCardButtonsProps<T extends BaseProduct> {
	product: T;
	licenses: Record<string, LicenseData | undefined> | null | undefined;
	mutations: ReturnType<typeof useProductMutations>;
}

const ProductCardButtons = <T extends BaseProduct>({ product, licenses, mutations }: ProductCardButtonsProps<T>) => {
    const { register } = useLicenseMutations(product.type as LicensedProductType);
    const isUnregistered = useUnregisteredStatus(product);
    const buttonConfig = useMemo(() => {
        // Free plugins can update if they're registered
        if (product.type === 'free') {
            return !isUnregistered && product.has_update ? {
                text: mutations.update.isPending ? __('Updating...', 'spellbook') : __('Update', 'spellbook'),
                onClick: () => mutations.update.mutate(product)
            } : null;
        }

        const license = getLicenseForProduct(product, licenses);

		if (product.is_legacy_free_plugin) {
			return null;
		}

        // No license present
        if (!license?.valid) {
            return {
                text: (product.has_update ? __('Enter License to Update', 'spellbook') : __('Enter License to Register', 'spellbook')),
                onClick: () => window.location.hash = `/licenses#${product.type}`,
            };
        }

        // Can update directly
        if (canUpdateProduct(product, licenses)) {
            return {
                text: mutations.update.isPending ? __('Updating...', 'spellbook') : __('Update', 'spellbook'),
                onClick: () => mutations.update.mutate(product)
            };
        }

		if (isProductRegistered(product, licenses)) {
			return null;
		}

        // Can register
        if (canRegisterProduct(product, licenses)) {
            return {
                text: register.isPending ? __('Registering...', 'spellbook') : (product.has_update ? __('Register to Update', 'spellbook') : __('Register', 'spellbook')),
                onClick: () => register.mutate(product.ID.toString()),
            };
        }

        // Has license but no capacity
        return {
            text: (product.has_update ? __('Upgrade License to Update', 'spellbook') : __('Upgrade License to Register', 'spellbook')),
            onClick: () => window.open(addUtmParams(license.upgrade_url, {
                component: 'product-card',
                text: 'upgrade-license',
                meta: 'no-capacity'
            }), '_blank'),
        };
    }, [licenses, isUnregistered, product, register.isPending, mutations.update.isPending]);

    return buttonConfig ? (
        <div className="product-card__buttons">
            <Button
                onClick={buttonConfig.onClick}
                type="white"
                size="size-sm"
                disabled={register.isPending || mutations.update.isPending}
            >
                {buttonConfig.text}
            </Button>
        </div>
    ) : null;
};

interface ProductCardProps<T extends BaseProduct> {
	product: T & { matches?: FuseResultMatch[] };
}

const ProductCard = memo(<T extends BaseProduct>({ product }: ProductCardProps<T>) => {
	const [isSettingsModalOpen, setIsSettingsModalOpen] = useState(false);
	const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
	const [isChangelogModalOpen, setIsChangelogModalOpen] = useState(false);

	const { is_installed, is_active } = product;
	const isUnregistered = useUnregisteredStatus(product);
	const { data: licenses } = product.type === 'free' ? { data: null } : useAllLicenses();
	const mutations = useProductMutations();
	const { data: productDetails, isLoading: isLoadingDetails, refetch: fetchProductDetails } = useProductDetails(product.ID);

	const handleChangelogClick = () => {
		// Fetch product details if we don't have changelog data
		if (!productDetails?.sections?.changelog) {
			fetchProductDetails();
		}
		setIsChangelogModalOpen(true);
	};

	const handleToggle = () => {
		if (is_active) {
			mutations.deactivate.mutate(product);
		} else {
			mutations.activate.mutate(product);
		}
	};

	const handleDelete = () => {
		mutations.delete.mutate(product, {
			onSuccess: () => setIsDeleteModalOpen(false)
		});
	};

	const handleUninstall = () => {
		mutations.uninstall.mutate(product, {
			onSuccess: () => setIsDeleteModalOpen(false)
		});
	};

	const { highlight } = useProductSearch({
		products: { [product.plugin_file]: product },
		searchTerm: ''
	});

	const title = useMemo(() => (
		<Heading
			customClasses={['gform-card__top-container-heading']}
			tagName="h3"
			size="text-md"
			weight="medium"
		>
			<span dangerouslySetInnerHTML={{ __html: highlight(product.name, product.matches, 'name') }} />
		</Heading>
	), [product.name, product.matches]);

	const description = useMemo(() => (
		<>
			<div className="product-card__tags">
				{product.version && (
					<button onClick={handleChangelogClick}>
						<Tag
							content={`v${product.version}`}
							size="text-xxs"
							type="blue"
							customClasses={product.has_update && product.is_installed ? 'version-tag--outdated' : undefined}
							customAttributes={{ style: { textTransform: 'none' } }}
						/>
					</button>
				)}
				{product.has_update && product.is_installed && (
					<button onClick={handleChangelogClick}>
						<Tag
							content={`v${product.new_version} AVAILABLE`}
							size="text-xxs"
							type="blue"
							customClasses="version-tag--available"
							customAttributes={{ style: { textTransform: 'none' } }}
						/>
					</button>
				)}
				{isUnregistered && (
					<Tag
						content={__('Unregistered', 'spellbook')}
						size="text-xxs"
						type="upgrade"
						customClasses="registration-required-tag"
					/>
				)}
			</div>
			<div className="product-card__content">
				<Text
					customClasses={['gform-card__top-container-description']}
					color="comet"
					size="text-sm"
					asHtml
					content={highlight(product.sections.description, product.matches, 'description')}
				/>
			{is_installed && (
				<ProductCardButtons
					product={product}
					licenses={licenses}
					mutations={mutations}
				/>
			)}
			</div>
		</>
	), [product.version, product.sections.description, product.matches, product.new_version, product.type, product.homepage, licenses, setIsChangelogModalOpen, mutations]);

	const LogoComponent = useMemo(() => {
		return product.icons?.['1x'] ? (
			<img
				src={product.icons['1x']}
				alt=""
				className="product-icon"
				loading="lazy"
				width={64}
				height={64}
			/>
		) : <PluginPlaceholder width={64} height={64} />;
	}, [product.icons]);

	const headerContent = [LogoComponent];
	const footerContent = [];

	// Add settings, documentation, and delete buttons
	const buttons = (
		<div className="product-card__header-buttons">
			{product.has_settings && (
				<Button
					customClasses={['gform-card__top-container-settings-button']}
					icon="cog"
					iconPrefix="gform-icon"
					label={__('Settings', 'spellbook')}
					onClick={() => setIsSettingsModalOpen(true)}
					size="size-height-s"
					type="icon-white"
				/>
			)}
			{product.documentation && (
				<Button
					customClasses={['gform-card__top-container-docs-button']}
					icon="book"
					iconPrefix="dashicons dashicons-book"
					label={__('Documentation', 'spellbook')}
					onClick={() => window.open(addUtmParams(product.documentation, {
						component: 'product-card',
						text: 'documentation'
					}), '_blank')}
					size="size-height-s"
					type="icon-white"
				/>
			)}
			{is_installed && !product.is_legacy_free_plugin && (
				<Button
					customClasses={['gform-card__top-container-delete-button']}
					icon="trash"
					iconPrefix="dashicons dashicons-trash"
					label={__('Delete', 'spellbook')}
					onClick={() => setIsDeleteModalOpen(true)}
					size="size-height-s"
					type="icon-white"
				/>
			)}
		</div>
	);

	headerContent.push(buttons);

	// Add status indicator and install/activate toggle
	const statusIndicator = (
		<Grid container elementType="div" columnSpacing={2}>
			<Grid item elementType="div">
				{
					product.is_legacy_free_plugin ? (
						<Text size="text-xs" spacing="0 2 0 0" weight="medium" color="dark-red">
							{__('Legacy free plugin—delete to manage in Spellbook.', 'spellbook')}
						</Text>
					) : (
						<Text size="text-xs" spacing="0 2 0 0" weight="medium">
							{!is_installed ? __('Not Installed', 'spellbook') : is_active ? __('Active', 'spellbook') : __('Inactive', 'spellbook')}
						</Text>
					)
				}

			</Grid>
			{
				product.is_legacy_free_plugin ? null : (
					<Grid item elementType="div">
						<DotIndicator type={!is_installed ? 'error' : is_active ? 'success' : 'warning'} />
					</Grid>
				)
			}
		</Grid>
	);

	const handleInstall = () => {
		mutations.install.mutate(product);
	};

	footerContent.push(statusIndicator);

	if (!product.is_legacy_free_plugin) {
		if (!is_installed) {
			const license = getLicenseForProduct(product, licenses);

			if (product.type !== 'free' && !license?.valid) {
				footerContent.push(
					<a
						href={addUtmParams("https://gravitywiz.com/pricing/", {
							component: "product-card",
							text: "buy-license"
						})}
						target="_blank"
						style={{ padding: '0.2rem 0', display: 'block' }}
					>
						{__('Buy License', 'spellbook')}
					</a>
				);
			} else {
				footerContent.push(
					<Button
						onClick={handleInstall}
						type="primary"
						disabled={mutations.install.isPending}
					>
						{mutations.install.isPending ? __('Installing...', 'spellbook') : __('Install', 'spellbook')}
					</Button>
				);
			}
		} else {
			footerContent.push(
				<Toggle
					externalChecked={is_active}
					externalControl={true}
					labelAttributes={{ label: __('Toggle Perk', 'spellbook'), isVisible: false }}
					onChange={handleToggle}
					disabled={mutations.activate.isPending || mutations.deactivate.isPending}
				/>
			);
		}
	}

	return (
		<>
			<IntegrationCard
				title={title}
				description={description}
				headerContent={headerContent}
				footerContent={footerContent}
			/>
			{isSettingsModalOpen && (
				<Modal
					title={__('Gravity Perks Settings', 'spellbook')}
					onRequestClose={() => setIsSettingsModalOpen(false)}
					className="spellbook-modal spellbook-plugin-settings-modal"
				>
					<PluginSettingsFrame
						src={`/wp-admin/admin.php?page=gwp_perks&view=perk_settings&slug=${product.plugin_file}`}
					/>
				</Modal>
			)}
			{isChangelogModalOpen && (
				<Modal
					title={__('Changelog', 'spellbook')}
					onRequestClose={() => setIsChangelogModalOpen(false)}
					className="changelog-modal"
				>
					<div>
						{isLoadingDetails ? (
							<div className="changelog-modal__loading">
								{__('Loading changelog...', 'spellbook')}
							</div>
						) : productDetails?.sections?.changelog ? (
							<div dangerouslySetInnerHTML={{ __html: productDetails.sections.changelog }} />
						) : (
							<div className="changelog-modal__error">
								{__('Changelog not available', 'spellbook')}
							</div>
						)}
					</div>
				</Modal>
			)}
			{isDeleteModalOpen && (
				<Modal
					title={__('Delete Plugin', 'spellbook')}
					onRequestClose={() => setIsDeleteModalOpen(false)}
					className="spellbook-modal spellbook-delete-modal"
				>
					<p style={{ margin: '0 0 1rem', fontSize: '14px' }}>
						{product.can_uninstall
							? sprintf(__('How would you like to delete %s?', 'spellbook'), product.name)
							: sprintf(__('Are you sure you want to delete %s?', 'spellbook'), product.name)}
					</p>
					<div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
						{product.can_uninstall ? (
							<>
								<Button
									onClick={handleDelete}
									type="white"
									disabled={mutations.delete.isPending || mutations.uninstall.isPending}
								>
									{mutations.delete.isPending ? __('Deleting...', 'spellbook') : __('Delete (Keep Data)', 'spellbook')}
								</Button>
								<Button
									onClick={handleUninstall}
									type="amaranth-red"
									disabled={mutations.delete.isPending || mutations.uninstall.isPending}
								>
									{mutations.uninstall.isPending ? __('Uninstalling...', 'spellbook') : __('Uninstall (Delete Data)', 'spellbook')}
								</Button>
							</>
						) : (
							<>
								<Button
									onClick={handleDelete}
									type="amaranth-red"
									disabled={mutations.delete.isPending}
								>
									{mutations.delete.isPending ? __('Deleting...', 'spellbook') : __('Yes', 'spellbook')}
								</Button>
								<Button
									onClick={() => setIsDeleteModalOpen(false)}
									type="white"
									disabled={mutations.delete.isPending}
								>
									{__('No', 'spellbook')}
								</Button>
							</>
						)}
					</div>
				</Modal>
			)}
		</>
	);
});

export default ProductCard;
