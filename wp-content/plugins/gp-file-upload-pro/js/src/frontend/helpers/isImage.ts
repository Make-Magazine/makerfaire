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

	const supports = checkBrowserSupport();

	// Canvas support is required to preview/crop/resize.
	if (!supports.canvas) {
		return false;
	}

	const fileType = getFileType(file);

	// Don't gate WebP on `supports.webp` (encode support): Safari can decode WebP but not encode it,
	// and gating made it skip the thumbnail/cropper. Encode fallback is handled at the encode sites.
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
 * MIME type to encode a processed image with via canvas. Safari can't encode WebP (it silently
 * emits PNG), so fall back to PNG explicitly here so the caller can also fix the filename.
 */
export function getEncodableImageType(fileType: string): string {
	if (['image/jpg', 'image/jpeg'].includes(fileType)) {
		return 'image/jpeg';
	}

	if (fileType === 'image/webp' && !checkBrowserSupport().webp) {
		return 'image/png';
	}

	return fileType;
}

/**
 * Rewrite a filename's extension to match the given MIME type (e.g. WebP -> PNG fallback).
 */
export function renameToImageType(name: string, fileType: string): string {
	if (!name) {
		return name;
	}

	const extByType: { [type: string]: string } = {
		'image/png': 'png',
		'image/jpeg': 'jpg',
		'image/webp': 'webp',
	};

	const ext = extByType[fileType];

	if (!ext) {
		return name;
	}

	const imageExtensionPattern = /\.(gif|png|jpe?g|bmp|webp|svg|heic)$/i;

	// Append the output extension if the filename does not end in a recognized image extension.
	if (!imageExtensionPattern.test(name)) {
		return `${name}.${ext}`;
	}

	return name.replace(imageExtensionPattern, `.${ext}`);
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
