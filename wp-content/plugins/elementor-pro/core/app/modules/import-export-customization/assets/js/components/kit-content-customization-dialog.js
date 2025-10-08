import { useState, useEffect } from 'react';
import * as PropTypes from 'prop-types';
import { Stack, CircularProgress, Box } from '@elementor/ui';
import { __ } from '@wordpress/i18n';
import { KitCustomizationDialog } from './kit-customization-dialog';
import { ListSettingSection } from './customization-list-setting-section';
import { SettingSection } from './customization-setting-section';
import { SubSetting } from './customization-sub-setting';
import { UpgradeNoticeBanner } from './upgrade-notice-banner';
import { useKitCustomizationPages } from '../hooks/use-kit-customization-pages';
import { useKitCustomizationTaxonomies } from '../hooks/use-kit-customization-taxonomies';
import { useKitCustomizationCustomPostTypes } from '../hooks/use-kit-customization-custom-post-types';
import { isHighTier } from '../hooks/use-tier';
import { UpgradeVersionBanner } from './upgrade-version-banner';
import { transformValueForAnalytics } from '../utils/analytics-transformer';

const transformAnalyticsData = ( payload, pageOptions, taxonomyOptions, customPostTypes ) => {
	const optionsArray = [
		{ key: 'pages', options: pageOptions },
		{ key: 'taxonomies', options: taxonomyOptions },
		{ key: 'customPostTypes', options: customPostTypes },
	];

	const transformed = {};

	for ( const [ key, value ] of Object.entries( payload ) ) {
		transformed[ key ] = transformValueForAnalytics( key, value, optionsArray );
	}

	return transformed;
};

export function KitContentCustomizationDialog( {
	open,
	handleClose,
	handleSaveChanges,
	data,
	isImport,
	isOldExport,
	isOldElementorVersion,
} ) {
	const initialState = data.includes.includes( 'content' );
	const { isLoading: isPagesLoading, pageOptions, isLoaded: isPagesLoaded } = useKitCustomizationPages( { open, data } );
	const { isLoading: isTaxonomiesLoading, taxonomyOptions, isLoaded: isTaxonomiesLoaded } = useKitCustomizationTaxonomies( { open, data } );
	const { customPostTypes } = useKitCustomizationCustomPostTypes( { data } );

	const [ settings, setSettings ] = useState( () => {
		if ( data.customization.content ) {
			return data.customization.content;
		}

		return {
			pages: [],
			menus: initialState,
			taxonomies: [],
			customPostTypes: [],
		};
	} );

	useEffect( () => {
		if ( ! open || data.includes.includes( 'content' ) ) {
			return;
		}

		setSettings( {
			pages: [],
			menus: false,
			taxonomies: [],
			customPostTypes: [],
		} );
	}, [ open, data.includes ] );

	useEffect( () => {
		if ( ! open || ! data.includes.includes( 'content' ) ) {
			return;
		}

		setSettings( ( prevSettings ) => ( {
			...prevSettings,
			pages: isPagesLoaded || isImport
				? ( data.customization.content?.pages || pageOptions.map( ( { value } ) => value ) )
				: prevSettings.pages,
		} ) );
	}, [
		open,
		data.includes,
		data.customization.content?.pages,
		isPagesLoaded,
		isImport,
		pageOptions,
	] );

	useEffect( () => {
		if ( ! open || ! data.includes.includes( 'content' ) ) {
			return;
		}

		setSettings( ( prevSettings ) => ( {
			...prevSettings,
			taxonomies: isTaxonomiesLoaded || isImport
				? ( data.customization.content?.taxonomies || taxonomyOptions.map( ( { value } ) => value ) )
				: prevSettings.taxonomies,
		} ) );
	}, [
		open,
		data.includes,
		data.customization.content?.taxonomies,
		isTaxonomiesLoaded,
		isImport,
		taxonomyOptions,
	] );

	useEffect( () => {
		if ( ! open || ! data.includes.includes( 'content' ) ) {
			return;
		}

		setSettings( ( prevSettings ) => ( {
			...prevSettings,
			customPostTypes: customPostTypes
				? ( data.customization.content?.customPostTypes || customPostTypes.map( ( { value } ) => value ) )
				: prevSettings.customPostTypes,
		} ) );
	}, [
		open,
		data.includes,
		data.customization.content?.customPostTypes,
		customPostTypes,
	] );

	useEffect( () => {
		if ( ! open || ! data.includes.includes( 'content' ) ) {
			return;
		}

		setSettings( ( prevSettings ) => ( {
			...prevSettings,
			menus: isImport
				? ( data.customization.content?.menus || Object.keys( data?.uploadedData?.manifest[ 'wp-content' ]?.nav_menu_item || {} ).length > 0 )
				: ( data.customization.content?.menus ?? initialState ),
		} ) );
	}, [
		open,
		data.includes,
		data.customization.content?.menus,
		data.uploadedData?.manifest,
		isImport,
	] );

	useEffect( () => {
		if ( open ) {
			window.elementorModules?.appsEventTracking?.AppsEventTracking?.sendPageViewsWebsiteTemplates( elementorCommon.eventsManager.config.secondaryLocations.kitLibrary.kitExportCustomizationEdit );
		}
	}, [ open ] );

	const handleSettingsChange = ( settingKey, payload ) => {
		setSettings( ( prev ) => ( {
			...prev,
			[ settingKey ]: payload,
		} ) );
	};

	const isTaxonomiesExported = () => {
		return isImport && taxonomyOptions?.length > 0;
	};

	const isPagesExported = () => {
		const content = data?.uploadedData?.manifest?.content;
		const wpContent = data?.uploadedData?.manifest?.[ 'wp-content' ];

		const isSomeContentExported = Object.keys( content?.page || {} )?.length;
		const isSomeWPContentExported = Object.keys( wpContent?.page || {} )?.length;

		return Boolean( isSomeContentExported || isSomeWPContentExported );
	};
	const isMenusExported = () => {
		return Object.keys( data?.uploadedData?.manifest?.[ 'wp-content' ]?.nav_menu_item || {} ).length > 0 ||
			customPostTypes?.find( ( cpt ) => cpt.value.includes( 'nav_menu' ) );
	};

	const isCustomPostTypesExported = () => {
		return isImport && customPostTypes?.length > 0;
	};

	const renderPagesSection = () => {
		if ( isImport && isOldExport ) {
			return null;
		}

		return isImport && ! isPagesExported() ? (
			<SettingSection
				title={ __( 'Site pages', 'elementor' ) }
				settingKey="pages"
				notExported
			/>
		) : (
			<ListSettingSection
				settingKey="pages"
				title={ __( 'Site pages', 'elementor' ) }
				onSettingChange={ ( selectedPages ) => {
					handleSettingsChange( 'pages', selectedPages );
				} }
				settings={ settings.pages }
				items={ pageOptions }
				loading={ isPagesLoading }
				disabled={ ! isHighTier() }
				tooltip={ ! isHighTier() }
			/>
		);
	};

	const renderMenusSection = () => {
		if ( isImport && isOldExport ) {
			return null;
		}

		return (
			<SettingSection
				checked={ settings.menus }
				disabled={ ( isImport && ! isMenusExported() ) || ! isHighTier() }
				title={ __( 'Menus', 'elementor' ) }
				settingKey="menus"
				tooltip={ ! isHighTier() }
				onSettingChange={ ( key, isChecked ) => {
					handleSettingsChange( key, isChecked );
				} }
			/>
		);
	};
	const renderTaxonomiesSection = () => {
		if ( isImport && isOldExport ) {
			return null;
		}

		return (
			<SettingSection
				description={ __( 'Group your content by type, topic, or any structure you choose.', 'elementor' ) }
				title={ __( 'Taxonomies', 'elementor' ) }
				settingKey="taxonomies"
				notExported={ isImport && ! isTaxonomiesExported() }
				hasToggle={ false }
			>
				{ isTaxonomiesLoading
					? <Box sx={ { p: 1, alignItems: 'center', textAlign: 'center' } } >
						<CircularProgress size={ 30 } />
					</Box>
					: ( taxonomyOptions.map( ( taxonomy ) => {
						return (
							<SubSetting
								key={ taxonomy.value }
								label={ taxonomy.label }
								settingKey="taxonomies"
								checked={ settings.taxonomies.includes( taxonomy.value ) }
								disabled={ ! isHighTier() }
								tooltip={ ! isHighTier() }
								onSettingChange={ ( key, isChecked ) => {
									setSettings( ( prevState ) => {
										const selectedTaxonomies = isChecked
											? [ ...prevState.taxonomies, taxonomy.value ]
											: prevState.taxonomies.filter( ( value ) => value !== taxonomy.value );

										return {
											...prevState,
											taxonomies: selectedTaxonomies,
										};
									} );
								} }
							/>
						);
					} )
					) }
			</SettingSection>
		);
	};

	return (
		<KitCustomizationDialog
			open={ open }
			title={ __( 'Edit content', 'elementor' ) }
			handleClose={ handleClose }
			handleSaveChanges={ () => {
				const hasEnabledCustomization = settings.pages.length > 0 || settings.menus || settings.customPostTypes.length > 0 || settings.taxonomies.length > 0;
				const transformedAnalytics = transformAnalyticsData( settings, pageOptions, taxonomyOptions, customPostTypes );
				handleSaveChanges( 'content', settings, hasEnabledCustomization, transformedAnalytics );
			} }
		>
			<Stack sx={ { position: 'relative' } } gap={ 2 }>
				{ isOldElementorVersion && (
					<UpgradeVersionBanner />
				) }
				<Stack>
					{ renderPagesSection() }
					{ renderMenusSection() }
					{
						isImport && ! isCustomPostTypesExported() ? (
							<SettingSection
								title={ __( 'Custom post types', 'elementor' ) }
								settingKey="customPostTypes"
								notExported
							/>
						) : (
							<ListSettingSection
								settingKey="customPostTypes"
								title={ __( 'Custom post types', 'elementor' ) }
								onSettingChange={ ( selectedCustomPostTypes ) => {
									handleSettingsChange( 'customPostTypes', selectedCustomPostTypes );
								} }
								settings={ settings.customPostTypes }
								items={ customPostTypes }
								disabled={ ( isImport && undefined === data?.uploadedData?.manifest[ 'custom-post-type-title' ] ) || ! isHighTier() }
								tooltip={ ! isHighTier() }
							/>
						)
					}
					{ renderTaxonomiesSection() }
				</Stack>
				<UpgradeNoticeBanner />
			</Stack>
		</KitCustomizationDialog>
	);
}

KitContentCustomizationDialog.propTypes = {
	open: PropTypes.bool.isRequired,
	isImport: PropTypes.bool,
	isOldExport: PropTypes.bool,
	isOldElementorVersion: PropTypes.bool,
	handleClose: PropTypes.func.isRequired,
	handleSaveChanges: PropTypes.func.isRequired,
	data: PropTypes.object.isRequired,
};
