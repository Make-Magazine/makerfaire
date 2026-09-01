import loadImage from 'blueimp-load-image';
import { getEncodableImageType, renameToImageType } from './isImage';

export default async function loadWithBlueimp({image, jpegQuality, loadImageOptions, stripMetadata}: {
	image: MOxieFile,
	loadImageOptions: loadImage.LoadImageOptions,
	jpegQuality: number,
	stripMetadata: boolean,
}): Promise<MOxieFile> {
	const img = await loadImage(image.getNative(), {
		canvas: true,
		orientation: true,
		meta: true,
		...loadImageOptions,
	});

	// Falls back to PNG for WebP when the browser (e.g. Safari) can't encode WebP via canvas.
	const blobImageType = getEncodableImageType(image?.type);
	const convertedFromWebp = image?.type === 'image/webp' && blobImageType !== 'image/webp';

	let processedBlob = await new Promise<Blob | null>((resolve) => {
		(img.image as unknown as HTMLCanvasElement).toBlob(function (blob) {
			resolve(blob);
		}, blobImageType, jpegQuality);
	});

	if (!stripMetadata) {
		processedBlob = await loadImage.replaceHead(processedBlob, img.imageHead);
	}

	/* Create new file object for Plupload using blob and update file name */
	const newFile = new window.mOxie.File(null, processedBlob);
	// Correct the extension only when the format changed (WebP -> PNG fallback).
	newFile.name = convertedFromWebp ? renameToImageType(image.name, blobImageType) : image.name;

	return newFile;
}
