export default function isImage(file: File) : boolean {
	const supportedImageTypes = [
		'image/gif',
		'image/png',
		'image/jpeg',
		'image/bmp',
		'image/webp',
		'image/svg+xml',
	];

	// Check if the browser supports WebP before treating it as an image.
	const supports = checkBrowserSupport();

	if (!supports.canvas) {
		return false;
	}

	if (file.type === 'image/webp' && !supports.webp) {
		return false;
	}

	return file.type.indexOf('image/') === 0 && supportedImageTypes.includes(file.type);
}

/**
 * Check if the browser supports Canvas and WebP.
 */
export function checkBrowserSupport() : { canvas: boolean, webp: boolean } {
	const canvas = document.createElement('canvas');

	if (!!(canvas.getContext && canvas.getContext('2d'))) {
		return {
			canvas: true,
			webp: canvas.toDataURL('image/webp').indexOf('data:image/webp') == 0,
		};
	} else {
		return {
			canvas: false,
			webp: false,
		}
	}
}
