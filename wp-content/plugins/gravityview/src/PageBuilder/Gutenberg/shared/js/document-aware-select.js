import { useEffect, useLayoutEffect, useRef, useState } from '@wordpress/element';
import Select, { components as selectComponents } from 'react-select';
import createCache from '@emotion/cache';
import { CacheProvider } from '@emotion/react';

// Window-scoped so the five independently compiled block bundles share one
// cache per document instead of each inserting duplicate style rules.
const caches = ( window.gkGravityViewSelectCaches = window.gkGravityViewSelectCaches || new WeakMap() );

// The canvas iframe's body carries the theme's front-end typography; pin the
// control and its portaled menu to the editor UI font so they match the admin.
const editorFont = {
	fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
	fontSize: '13px',
};

const getCache = ( doc ) => {
	if ( ! caches.has( doc ) ) {
		caches.set( doc, createCache( { key: 'gk-select', container: doc.head } ) );
	}

	return caches.get( doc );
};

/**
 * Menu that flips above the control when it would overflow its own viewport.
 *
 * react-select picks placement from `window.innerHeight` (the module's window,
 * always the top-level one) whenever `menuPosition` is fixed, so inside the
 * shorter editor canvas iframe it believes there is more room below the control
 * than there is and opens a menu that runs off the bottom edge. Measure against
 * the document the menu actually renders in and correct the portal's offset.
 *
 * The portal's own `top` comes from an emotion class, so the inline value set
 * here wins without competing for the same declaration.
 *
 * @since 3.3.0
 */
const ViewportAwareMenu = ( props ) => {
	const menuRef = useRef( null );

	useLayoutEffect( () => {
		const menu = menuRef.current;
		const portal = menu?.parentElement;
		const control = props.selectProps.gvContainerRef?.current?.querySelector(
			'[class$="-control"]'
		);

		if ( ! menu || ! portal || ! control ) {
			return;
		}

		// Measured from the control, never from the portal's own top, so a correction
		// applied on one pass is not read back as the control's position on the next.
		const view = menu.ownerDocument.defaultView;
		const style = view.getComputedStyle( menu );
		const controlRect = control.getBoundingClientRect();

		// The portal positions the menu's margin box, so the margins are part of the
		// space it needs; ignoring them lands the flipped menu on top of the control.
		const spaceNeeded =
			menu.getBoundingClientRect().height +
			( parseFloat( style.marginTop ) || 0 ) +
			( parseFloat( style.marginBottom ) || 0 );
		const flippedTop = controlRect.top - spaceNeeded;

		// Only flip when the menu genuinely overflows below and fully fits above.
		if ( controlRect.bottom + spaceNeeded <= view.innerHeight || flippedTop < 0 ) {
			portal.style.removeProperty( 'top' );

			return;
		}

		portal.style.top = `${ flippedTop }px`;
	} );

	return <selectComponents.Menu { ...props } innerRef={ menuRef } />;
};

const documentAwareComponents = { Menu: ViewportAwareMenu };

/**
 * react-select styled for the document it renders in.
 *
 * react-select injects its styles through emotion, which defaults to the
 * top-level document; inside the iframed editor canvas that leaves the control
 * unstyled and its screen-reader live region visible. Binding the emotion
 * cache (and the menu portal) to the component's own document styles it in
 * both the editor chrome and the canvas.
 *
 * @since 3.3.0
 */
export default function DocumentAwareSelect( { styles, components, ...props } ) {
	const anchorRef = useRef( null );
	const containerRef = useRef( null );
	const [ doc, setDoc ] = useState( null );

	useEffect( () => {
		setDoc( anchorRef.current?.ownerDocument || document );
	}, [] );

	if ( ! doc ) {
		return <span ref={ anchorRef } />;
	}

	return (
		<CacheProvider value={ getCache( doc ) }>
			<div ref={ containerRef }>
				<Select
					{ ...props }
					gvContainerRef={ containerRef }
					components={ { ...components, ...documentAwareComponents } }
					// Declared after the spread so a caller passing the top-level document.body
					// cannot portal a canvas menu back into the editor chrome.
					menuPortalTarget={ doc.body }
					// Fixed positioning keeps the portaled menu aligned when the canvas iframe
					// scrolls; absolute mode offsets by the top window's pageYOffset, which is
					// not the window the control scrolls in.
					menuPosition="fixed"
					// A higher z-index is needed to ensure other editor elements don't overlap the dropdown.
					// Caller slot functions are composed rather than replaced, so passing a
					// `container` or `menuPortal` style still reaches react-select.
					styles={ {
						...styles,
						container: ( base, state ) => ( {
							...( styles?.container?.( base, state ) ?? base ),
							...editorFont,
						} ),
						menuPortal: ( base, state ) => ( {
							...( styles?.menuPortal?.( base, state ) ?? base ),
							zIndex: 10,
							...editorFont,
						} ),
					} }
				/>
			</div>
		</CacheProvider>
	);
}
