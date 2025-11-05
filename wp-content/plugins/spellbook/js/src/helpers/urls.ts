interface LinkContext {
    component?: string;     // Which component (e.g., 'license-bar', 'product-card')
    text?: string;         // The actual button/link text
    meta?: string;         // Optional: Additional context (e.g., 'no-capacity')
}

/**
 * Add UTM parameters to a URL.
 *
 * @param url     The URL to add UTM parameters to
 * @param context The context of where this link appears
 * @return        The URL with UTM parameters added
 */
export function addUtmParams(url: string, context: LinkContext): string {
    const urlObj = new URL(url);

    // Get current page from hash
    const hash = window.location.hash.slice(2); // Remove #/
    const source = hash ? `ui-${hash}` : 'ui';

    // Set UTM params
    urlObj.searchParams.set('utm_campaign', 'spellbook-plugin');
    urlObj.searchParams.set('utm_source', source);

    if (context.component) {
        urlObj.searchParams.set('utm_medium', context.component);
    }
    if (context.text) {
        urlObj.searchParams.set('utm_content', context.text);
    }
    if (context.meta) {
        urlObj.searchParams.set('utm_term', context.meta);
    }

    return urlObj.toString();
}

/**
 * Get the pricing URL for a specific product type.
 *
 * @param productType The product type (perk, connect, shop, etc.)
 * @return            The pricing URL for the product type
 */
export function getPricingUrl(productType: string): string {
	const baseUrl = "https://gravitywiz.com";
	
	const productPaths: Record<string, string> = {
		perk: '/gravity-perks/pricing',
		connect: '/gravity-connect/pricing',
		shop: '/gravity-shop/pricing'
	};
	
	const path = productPaths[productType.toLowerCase()] ?? "/pricing";
	return `${baseUrl}${path}`;
}
