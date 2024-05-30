/*
 * String.format was deprecated in GF 2.7.1 and will be removed in GF 2.8 in favor of String.prototype.gformFormat.
 *
 * As we support older versions of GF, we need to add String.prototype.gformFormat if it doesn't exist.
 */
// @ts-ignore
if (!String.prototype.gformFormat) {
	// @ts-ignore
	String.prototype.gformFormat = function() {
		// eslint-disable-next-line prefer-rest-params
		const args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] !== 'undefined' ? args[number] : match;
		});
	};
}
