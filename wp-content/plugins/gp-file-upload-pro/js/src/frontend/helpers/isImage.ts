export default function isImage(file: File) : boolean {
	const supportedImageTypes = [
		'image/gif',
		'image/png',
		'image/jpeg',
		'image/bmp',
		'image/webp',
		'image/svg+xml',
		'image/heic',
	];

	// Check if the browser supports WebP before treating it as an image.
	const supports = checkBrowserSupport();

	if (!supports.canvas) {
		return false;
	}

	const fileType = getFileType(file);

	if (fileType === 'image/webp' && !supports.webp) {
		return false;
	}

	return fileType.indexOf('image/') === 0 && supportedImageTypes.includes(fileType);
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

/**
 * Get the file type from the file object.
 */
export function getFileType(file: File): string {
	let fileType = file.type || '';
	// This is needed because HEIC mime type is an empty string on Windows, etc.
	if (!fileType && file.name) {
		const extension = file.name.split('.').pop()?.toLowerCase();
		if (extension === 'heic') {
			fileType = 'image/heic';
		}
	}
	return fileType;
}
