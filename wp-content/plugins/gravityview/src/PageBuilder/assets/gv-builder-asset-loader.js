/**
 * Page-builder asset loader.
 *
 * Builder previews (Beaver Builder, Divi, Elementor) and the Gutenberg
 * block-renderer REST endpoint render Views via AJAX/REST *after* `wp_head`
 * and `wp_footer` have fired, so styles/scripts a View enqueues during render
 * never reach the page the normal way. To bridge that,
 * `ShortcodeRenderer::render_asset_loader()` (PHP) diffs the enqueue queues
 * around the render and serializes the newly-enqueued assets — stylesheet and
 * script URLs plus any `wp_add_inline_style()` / `wp_add_inline_script()`
 * payloads — into a JSON `data-gv-assets` attribute on the rendered markup.
 *
 * This script is the client half: it scans for those payload nodes on
 * DOMContentLoaded and — because builders inject markup dynamically — via a
 * MutationObserver, then injects each asset once. Dedupe state lives on
 * `window.gkGravityViewAssetCache` so it survives re-execution across partial
 * refreshes and across multiple modules on one page.
 *
 * Payload contract: `styles` and `scripts` are arrays whose entries are either
 * an asset-URL string, or an object — `{ src, before, after }` for styles and
 * `{ src, data }` for scripts, where `before` / `after` / `data` are inline
 * CSS / JS bodies that load alongside the handle.
 */
(function () {
	// Shared across every payload on the page and persisted on `window` so it
	// survives this IIFE re-running on builder partial refreshes.
	var cache = window.gkGravityViewAssetCache || { scripts: {}, styles: {}, inline: {} };
	window.gkGravityViewAssetCache = cache;

	var isScriptUrl = function (value) {
		if (!value || typeof value !== 'string') {
			return false;
		}
		if (/^(https?:)?\/\//.test(value)) {
			return true;
		}
		if (/\.js(\?|#|$)/.test(value)) {
			return true;
		}
		if (value.charAt(0) === '/' || value.indexOf('./') === 0 || value.indexOf('../') === 0) {
			return true;
		}
		return false;
	};

	var getSafeSelectorValue = function (value) {
		if (window.CSS && CSS.escape) {
			return CSS.escape(value);
		}
		// Escape characters that could break the selector
		return value.replace(/[\\"'\n\r\f\0]/g, function(c) {
			return '\\' + c.charCodeAt(0).toString(16) + ' ';
		});
	};

	var isStyleUrl = function (value) {
		if (!value || typeof value !== 'string') {
			return false;
		}
		if (/^(https?:)?\/\//.test(value)) {
			return true;
		}
		if (/\.css(\?|#|$)/.test(value)) {
			return true;
		}
		if (value.charAt(0) === '/' || value.indexOf('./') === 0 || value.indexOf('../') === 0) {
			return true;
		}
		return false;
	};

	var hashCode = function (str) {
		var hash = 0;
		for (var i = 0; i < str.length; i++) {
			var char = str.charCodeAt(i);
			hash = ((hash << 5) - hash) + char;
			hash = hash & hash; // Convert to 32-bit integer
		}
		return Math.abs(hash);
	};

	var loadStyle = function (style) {
		var safeHref, link;

		// Handle objects with src/before/after properties
		if (typeof style === 'object' && style !== null && !Array.isArray(style)) {
			var href = style.src;
			var beforeStyle = style.before;
			var afterStyle = style.after;

			if (href && isStyleUrl(href)) {
				if (cache.styles[href]) {
					// Still inject inline styles if they weren't loaded before
					if (beforeStyle || afterStyle) {
						loadInlineStyle(href, beforeStyle, afterStyle);
					}
					return;
				}

				try {
					safeHref = getSafeSelectorValue(href);
					if (document.querySelector('link[href="' + safeHref + '"]')) {
						cache.styles[href] = true;
						loadInlineStyle(href, beforeStyle, afterStyle);
						return;
					}
				} catch (e) {
					// Ignore selector errors
				}

				link = document.createElement('link');
				link.rel = 'stylesheet';
				link.type = 'text/css';
				link.href = href;
				document.head.appendChild(link);
				cache.styles[href] = true;
			}

			loadInlineStyle(href || 'inline', beforeStyle, afterStyle);
			return;
		}

		// Handle string assets (URL or raw CSS)
		if (typeof style === 'string') {
			if (isStyleUrl(style)) {
				// It's a stylesheet URL
				if (cache.styles[style]) {
					return;
				}

				try {
					safeHref = getSafeSelectorValue(style);
					if (document.querySelector('link[href="' + safeHref + '"]')) {
						cache.styles[style] = true;
						return;
					}
				} catch (e) {
					// Ignore selector errors
				}

				link = document.createElement('link');
				link.rel = 'stylesheet';
				link.type = 'text/css';
				link.href = style;
				document.head.appendChild(link);
				cache.styles[style] = true;
			} else {
				// It's raw CSS - inject it as an inline style
				var cssHash = hashCode(style);
				var styleId = 'gv-inline-css-' + cssHash;

				if (!cache.inline[styleId] && !document.getElementById(styleId)) {
					var styleElement = document.createElement('style');
					styleElement.id = styleId;
					styleElement.textContent = style;
					document.head.appendChild(styleElement);
					cache.inline[styleId] = true;
				}
			}
		}
	};

	var loadInlineStyle = function (baseId, beforeStyle, afterStyle) {
		var styleEl;

		if (beforeStyle) {
			var beforeId = baseId + '-inline-before';
			if (!cache.inline[beforeId] && !document.getElementById(beforeId)) {
				styleEl = document.createElement('style');
				styleEl.id = beforeId;
				styleEl.textContent = beforeStyle;
				document.head.appendChild(styleEl);
				cache.inline[beforeId] = true;
			}
		}

		if (afterStyle) {
			var afterId = baseId + '-inline-after';
			if (!cache.inline[afterId] && !document.getElementById(afterId)) {
				styleEl = document.createElement('style');
				styleEl.id = afterId;
				styleEl.textContent = afterStyle;
				document.head.appendChild(styleEl);
				cache.inline[afterId] = true;
			}
		}
	};

	var loadScript = function (script) {
		if (!script) {
			return;
		}

		if (typeof script === 'string' && !isScriptUrl(script)) {
			if (!cache.inline[script]) {
				try {
					var inlineEl = document.createElement('script');
					inlineEl.textContent = script;
					document.head.appendChild(inlineEl);
				} catch (e) {}
				cache.inline[script] = true;
			}
			return;
		}

		var src = (typeof script === 'string') ? script : script.src;
		var data = (typeof script === 'object' && script) ? script.data : null;

		if (data && !cache.inline[data]) {
			try {
				var dataEl = document.createElement('script');
				dataEl.textContent = data;
				document.head.appendChild(dataEl);
			} catch (e) {}
			cache.inline[data] = true;
		}

		if (!src || !isScriptUrl(src) || cache.scripts[src]) {
			return;
		}

		try {
			var safeSrc = getSafeSelectorValue(src);
			if (document.querySelector('script[src="' + safeSrc + '"]')) {
				cache.scripts[src] = true;
				return;
			}
		} catch (e) {
			// Ignore selector errors and append the asset.
		}

		var tag = document.createElement('script');
		tag.type = 'text/javascript';
		// Dynamically inserted scripts are async by default; force ordered
		// execution so the dependency-ordered payload runs in sequence.
		tag.async = false;
		tag.src = src;
		document.body.appendChild(tag);
		cache.scripts[src] = true;
	};

	var loadAssets = function (payload) {
		if (!payload) {
			return;
		}

		if (Array.isArray(payload.styles)) {
			payload.styles.forEach(loadStyle);
		}

		if (Array.isArray(payload.scripts)) {
			payload.scripts.forEach(loadScript);
		}
	};

	var parseAssetPayload = function (element) {
		if (!element || element.getAttribute('data-gv-assets-loaded')) {
			return;
		}

		var raw = element.getAttribute('data-gv-assets');
		if (!raw) {
			return;
		}

		try {
			loadAssets(JSON.parse(raw));
			element.setAttribute('data-gv-assets-loaded', '1');
		} catch (e) {
			// Ignore malformed JSON payloads.
		}
	};

	var scan = function (root) {
		var scope = root || document;
		var nodes = scope.querySelectorAll('[data-gv-assets]:not([data-gv-assets-loaded])');

		if (!nodes.length) {
			return;
		}

		Array.prototype.forEach.call(nodes, parseAssetPayload);
	};

	var onReady = function () {
		scan();
		if ('MutationObserver' in window) {
			var observer = new MutationObserver(function (mutations) {
				mutations.forEach(function (mutation) {
					Array.prototype.forEach.call(mutation.addedNodes, function (node) {
						if (node.nodeType !== 1) {
							return;
						}
						if (node.hasAttribute && node.hasAttribute('data-gv-assets')) {
							parseAssetPayload(node);
						}
						if (node.querySelectorAll) {
							scan(node);
						}
					});
				});
			});

			observer.observe(document.documentElement, { childList: true, subtree: true });
		}
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', onReady);
	} else {
		onReady();
	}

	window.gkGravityViewLoadAssets = loadAssets;
})();
