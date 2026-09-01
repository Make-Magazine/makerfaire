import { __, _x } from '@wordpress/i18n';
import { useState, useEffect, useRef } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs, isURL } from '@wordpress/url';
import { Spinner } from '@wordpress/components';

import InnerHTML from 'dangerously-set-html-content';

const API_PATH = '/wp/v2/block-renderer';
const DEBOUNCE_FETCH = 500; // Used to debounce fetch request so that it only happens when the block's attributes haven't changed in 500ms.

/**
 * Check if a string is a URL (for stylesheets or scripts).
 *
 * Uses WordPress's isURL() utility from @wordpress/url for robust URL detection.
 *
 * @param {*} value The value to check.
 * @return {boolean} True if the value is a URL.
 */
const isUrl = ( value ) => {
	if ( ! value || typeof value !== 'string' ) {
		return false;
	}
	if ( isURL( value ) ) {
		return true;
	}
	// Relative paths (/, ./, ../) are URLs too.
	if ( value.charAt( 0 ) === '/' || value.indexOf( './' ) === 0 || value.indexOf( '../' ) === 0 ) {
		return true;
	}
	return false;
};

/**
 * Inject an inline style or script into a document, once per exact content.
 *
 * Deduping on a 32-bit hash of the content would silently drop assets that
 * collide (`window.Aa=1` and `window.BB=1` hash identically), so the elements
 * we own carry a marker attribute and are compared by their text.
 *
 * @since 3.3.0
 *
 * @param {Document} doc     The document to inject into.
 * @param {string}   tag     Either 'style' or 'script'.
 * @param {string}   content Raw CSS or JavaScript text.
 */
const injectInline = ( doc, tag, content ) => {
	if ( ! content || typeof content !== 'string' ) {
		return;
	}

	const marker = `data-gv-inline-${ tag }`;
	const owned = doc.querySelectorAll( `${ tag }[${ marker }]` );

	if ( Array.prototype.some.call( owned, ( el ) => el.textContent === content ) ) {
		return;
	}

	const el = doc.createElement( tag );
	el.setAttribute( marker, '' );
	el.textContent = content;
	doc.head.appendChild( el );
};

const injectInlineCss = ( css, doc = document ) => injectInline( doc, 'style', css );

const injectInlineScript = ( js, doc ) => injectInline( doc, 'script', js );

/**
 * Check if a stylesheet URL is already linked in a document.
 *
 * @since 3.3.0
 *
 * @param {Document} doc The document to check.
 * @param {string}   src The stylesheet URL.
 * @return {boolean} True if a matching link element exists.
 */
const isStyleLoaded = ( doc, src ) => {
	return Array.prototype.some.call(
		doc.querySelectorAll( 'link[rel="stylesheet"]' ),
		( el ) => el.getAttribute( 'href' ) === src
	);
};

/**
 * Check if a script URL is already present in a document.
 *
 * @since 3.3.0
 *
 * @param {Document} doc The document to check.
 * @param {string}   src The script URL.
 * @return {boolean} True if a matching script element exists.
 */
const isScriptLoaded = ( doc, src ) => {
	return Array.prototype.some.call(
		doc.querySelectorAll( 'script[src]' ),
		( el ) => el.getAttribute( 'src' ) === src
	);
};

/**
 * Append a stylesheet link to a document's head.
 *
 * Head, not body, so it shares a container with the inline styles injected around
 * it and their relative document order matches wp_add_inline_style's cascade.
 *
 * @since 3.3.0
 *
 * @param {Document} doc The document to append to.
 * @param {string}   src The stylesheet URL.
 */
const appendStylesheet = ( doc, src ) => {
	const el = doc.createElement( 'link' );
	el.setAttribute( 'rel', 'stylesheet' );
	el.setAttribute( 'type', 'text/css' );
	el.setAttribute( 'href', src );
	doc.head.appendChild( el );
};

export const loadAsset = ( { asset, type, onLoad, targetDocument } ) => {
	const doc = targetDocument || document;

	if ( type === 'js' ) {
		const el = doc.createElement( 'script' );
		el.setAttribute( 'type', 'text/javascript' );
		el.setAttribute( 'src', asset );
		// Dynamically inserted external scripts default to async, which executes them
		// in load-completion order; WordPress enqueues assume dependency order.
		el.async = false;
		el.onload = onLoad;
		doc.head.appendChild( el );
	} else {
		// CSS: asset can be a string (URL or raw CSS) or an object { src, before, after }.
		if ( typeof asset === 'object' && asset !== null ) {
			if ( asset.before ) {
				injectInlineCss( typeof asset.before === 'string' ? asset.before : asset.before.join( '\n' ), doc );
			}
			if ( asset.src && isUrl( asset.src ) ) {
				appendStylesheet( doc, asset.src );
			}
			if ( asset.after ) {
				injectInlineCss( typeof asset.after === 'string' ? asset.after : asset.after.join( '\n' ), doc );
			}
		} else if ( isUrl( asset ) ) {
			appendStylesheet( doc, asset );
		} else {
			injectInlineCss( asset, doc );
		}
	}
};

const ServerSideRender = ( props ) => {
	const {
		block,
		blockPreviewImage,
		dataType,
		attributes,
		loadScripts,
		loadStyles,
		onEmptyResponse,
		onError,
		onLoading,
		onResponse
	} = props;

	const [ response, setResponse ] = useState( null );
	const [ isFetching, setIsFetching ] = useState( true );
	const [ error, setError ] = useState( null );

	// Preview assets must land in the document containing the rendered preview,
	// which in the iframed block editor is the canvas iframe, not the top-level document.
	const containerRef = useRef( null );

	// A preview fetch can outlive the request that replaced it: the debounce only
	// spaces requests 500ms apart, while a View preview routinely takes longer. An
	// older response must not overwrite newer content or inject the previous View's assets.
	const requestRef = useRef( 0 );
	const settleTimerRef = useRef( null );
	const isMountedRef = useRef( true );

	const isCurrentRequest = ( requestId ) => isMountedRef.current && requestId === requestRef.current;

	useEffect( () => {
		// Retire any in-flight request the moment the attributes change, not when the
		// debounced fetch finally starts, or a response arriving inside the debounce
		// window still passes the guard and injects the previous View's assets.
		requestRef.current++;

		clearTimeout( settleTimerRef.current );

		const handler = setTimeout( () => fetch(), DEBOUNCE_FETCH );

		return () => clearTimeout( handler );
	}, [ attributes ] );

	useEffect( () => {
		// Reassigned on every run, not just initialized, so a StrictMode remount
		// (mount, unmount, mount) does not leave the component marked unmounted.
		isMountedRef.current = true;

		return () => {
			isMountedRef.current = false;

			clearTimeout( settleTimerRef.current );
		};
	}, [] );

	const fetch = () => {
		const path = addQueryArgs( `${ API_PATH }/${ block }`, {
			context: 'edit',
			attributes,
		} );

		const requestId = ++requestRef.current;

		// Without this a single failed request pins the error state forever, because
		// renderContent() short-circuits on `error` before it looks at the response.
		setError( null );
		setIsFetching( true );

		apiFetch( { path } )
			.then( ( res ) => {
				if ( ! isCurrentRequest( requestId ) ) {
					return;
				}

				if ( dataType !== 'json' ) {
					setResponse( res.rendered );

					setIsFetching( false );

					return;
				}

				const response = JSON.parse( res.rendered );

				// No mounted container means the block unmounted (or the canvas
				// remounted) while the request was in flight; injecting into the
				// top-level document here would leak assets into the editor chrome.
				const targetDocument = containerRef.current?.ownerDocument;

				if ( loadStyles && targetDocument ) {
					Object.values( response.styles ).forEach( ( asset ) => {
						if ( typeof asset === 'object' && asset !== null ) {
							// Always inject inline before/after CSS even if
							// the stylesheet URL was already loaded — each
							// block instance may have its own overrides.
							if ( asset.before ) {
								loadAsset( { asset: { before: asset.before }, type: 'css', targetDocument } );
							}

							// Dedupe by DOM presence, not a session-global set, so a
							// remounted canvas iframe receives the styles again.
							if ( asset.src && ! isStyleLoaded( targetDocument, asset.src ) ) {
								loadAsset( { asset: asset.src, type: 'css', targetDocument } );
							}

							// After the stylesheet, matching wp_add_inline_style: `after` CSS
							// must win an equal-specificity tie against the sheet it extends.
							if ( asset.after ) {
								loadAsset( { asset: { after: asset.after }, type: 'css', targetDocument } );
							}
						} else if ( isUrl( asset ) ) {
							if ( ! isStyleLoaded( targetDocument, asset ) ) {
								loadAsset( { asset, type: 'css', targetDocument } );
							}
						} else {
							// Inline CSS string; dedupes itself by exact content.
							loadAsset( { asset, type: 'css', targetDocument } );
						}
					} );
				}

				if ( loadScripts && targetDocument ) {
					Object.values( response.scripts ).forEach( ( asset ) => {
						const src = asset?.src ? asset.src : asset;

						// Inline data (localized variables) is injected first so it runs
						// before the script that reads it: an inline script executes on
						// insertion, while an external one has to load first.
						if ( asset?.data ) {
							injectInlineScript( asset.data, targetDocument );
						}

						if ( isUrl( src ) && ! isScriptLoaded( targetDocument, src ) ) {
							loadAsset( { asset: src, type: 'js', targetDocument } );
						}
					} );
				}

				settleTimerRef.current = setTimeout( () => {
					if ( ! isCurrentRequest( requestId ) ) {
						return;
					}

					setResponse( response.content );

					setIsFetching( false );
				}, 250 ); // Wait for scripts/styles to load.
			} )
			.catch( ( error ) => {
				if ( ! isCurrentRequest( requestId ) ) {
					return;
				}

				setError( error );

				setIsFetching( false );
			} );
	};

	const renderContent = () => {
		if ( error ) {
			return typeof onError === 'function'
				? onError( error )
				: (
					<div className="error-state">
						{
							_x( 'The block could not be rendered due to an error: [error]', '[error] placeholder will be replaced with an error message and is not to be translated.', 'gk-gravityview' )
								.replace( '[error]', error.message )
						}
					</div>
				);
		}

		// If the block was previously rendered, do not clear existing response and just display the spinner; this prevents the unsightly content shift.
		if ( isFetching && response ) {
			return typeof onLoading === 'function'
				? onLoading( response )
				: (
					<div className="loading-state">
						<div className="loader">
							<Spinner />
						</div>
						<InnerHTML html={ response } />
					</div>
				);
		}

		if ( isFetching ) {
			return typeof onLoading === 'function'
				? onLoading()
				: (
					<div className="loading-state initial">
						<div className="loader">
							<Spinner />
						</div>
						{ blockPreviewImage }
					</div>
				);
		}

		if ( !response ) {
			return typeof onEmptyResponse === 'function'
				? onEmptyResponse()
				: (
					<div className="empty-response">
						<p>
							{ __( 'The block did not render any content.', 'gk-gravityview' ) }
						</p>
					</div>
				);
		}

		return typeof onResponse === 'function'
			? onResponse( response )
			: <InnerHTML html={ response } />;
	};

	return (
		<div ref={ containerRef }>
			{ renderContent() }
		</div>
	);
};

export default ServerSideRender;
