export default class Module extends elementorModules.Module {
	#actionLinks = [
		{
			href: 'elementor_pro_renew_license_menu_link',
			external_url: 'https://go.elementor.com/wp-menu-renew/',
		},
		{
			href: 'elementor_pro_upgrade_license_menu_link',
			external_url: 'https://go.elementor.com/go-pro-advanced-elementor-menu/',
		},
	];

	onInit() {
		this.assignMenuItemActions();
		this.assignProLicenseActivateEvent();
	}

	assignMenuItemActions() {
		window.addEventListener( 'DOMContentLoaded', () => {
			this.#actionLinks.forEach( ( item ) => {
				const link = document.querySelector( `a[href="${ item.href }"]` );

				if ( ! link ) {
					return;
				}

				link.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					window.open( item.external_url, '_blank' );
				} );
			} );
		} );
	}

	assignProLicenseActivateEvent() {
		window.addEventListener( 'DOMContentLoaded', () => {
			const activateButton = document.querySelector( '.button-primary[href*="elementor-connect"]' );

			if ( activateButton ) {
				activateButton.addEventListener( 'click', () => {
					if ( ! window.elementorCommon?.config?.experimentalFeatures?.editor_events ) {
						return;
					}

					const eventsManager = window.elementorCommon?.eventsManager || {};
					const dispatchEvent = eventsManager.dispatchEvent?.bind( eventsManager );

					const eventName = 'pro_license_activate';
					const eventData = {
						app_type: 'editor',
						location: 'Elementor WP-admin pages',
						secondaryLocation: 'license page',
						trigger: 'click',
					};

					dispatchEvent?.( eventName, eventData );
				} );
			}
		} );
	}
}
