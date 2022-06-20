/* Provide a class for Safari, the new IE */
if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Mac') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
	// console.log('Safari on Mac detected, applying class...');
	jQuery('html').addClass('safari-mac'); // provide a class for the safari-mac specific css to filter with
}
