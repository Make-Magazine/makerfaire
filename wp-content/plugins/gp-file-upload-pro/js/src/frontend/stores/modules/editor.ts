import getImageSize from '../../helpers/getImageSize';
import {blobToDataURL} from 'blob-util';
import Vue from 'vue';

export default function () {
	return {
		state: {
			currentFile: null,
			originals: {},
			cropperResults: {},
			open: false,
		},
		getters: {
			currentImageIndex: (state: any, getters: any, rootState: any) => {
				if (!state.currentFile?.id) {
					return undefined;
				}

				return rootState.currentAddedFiles.findIndex((file: MOxieFile) => {
					return file.id === state.currentFile?.id;
				}) + 1;
			},
			imgSrc: (state: any) => {
				return state.originals[state.currentFile?.id]?.src;
			},
			imgSize: (state: any) => {
				const cropped = state?.cropperResults[state.currentFile?.id]?.coords;

				if (cropped) {
					return {
						width: cropped.width,
						height: cropped.height,
					}
				}

				return state.originals[state.currentFile?.id]?.size;
			},
			imgOriginalSize: (state: any) => {
				return state.originals[state.currentFile?.id]?.size;
			},
			imgPos: (state: any) => {
				const cropped = state?.cropperResults[state.currentFile?.id]?.coords;

				if (cropped) {
					return {
						left: cropped.left,
						top: cropped.top,
					}
				}

				return {
					top: 0,
					left: 0,
				};
			},
			imgTransforms: (state: any) => {
				const imageTransforms = state?.cropperResults[state.currentFile?.id]?.imageTransforms;

				return imageTransforms;
			}
		},
		actions: {
			async setCurrentFile(context: any, { file, clearPrevious = true } : { file: MOxieFile, clearPrevious: boolean }) {
				/**
				 * In some cases we don't want to clear the current file before setting it to a new file.
				 *
				 * A specific example of this is in required cropping mode when cropping a set of images.
				 */
				if (clearPrevious) {
					context.commit('SET_CURRENT_FILE', null);
				}

				if (!context.state.originals[file.id]) {
					await context.dispatch('storeOriginal', file);
				}

				context.commit('SET_CURRENT_FILE', file);
			},
			async storeOriginal(context: any, file: MOxieFile) {
				let dataUrl: string;

				/**
				 * If file.getNative() is not available, then this is a subsequent pageload and an original still does not
				 * exist so we need to use the preview from localforge as the original.
				 */
				if (typeof file.getNative === 'function') {
					dataUrl = await blobToDataURL(file.getNative());
				} else {
					dataUrl = await context.rootState.storage.getPreview(file.id);
				}

				context.dispatch('storeImagePreview', {
					fileId: file.id,
					dataUrl,
				});

				context.commit('STORE_ORIGINAL', {
					fileId: file.id,
					src: dataUrl,
					size: await getImageSize(dataUrl),
				});

				context.rootState.storage.storeOriginal(file.id, {
					src: dataUrl,
					size: await getImageSize(dataUrl),
				});
			},
			async transferOriginal(context: any, {
				currentFileId,
				newFileId
			}: { currentFileId: string, newFileId: string }) {
				context.commit('TRANSFER_ORIGINAL', {
					currentFileId,
					newFileId,
				});

				context.rootState.storage.transferOriginal(newFileId, currentFileId);
			},
			async transferResults(context: any, {
				currentFileId,
				newFileId
			}: { currentFileId: string, newFileId: string }) {
				context.commit('TRANSFER_RESULTS', {
					currentFileId,
					newFileId,
				});

				context.rootState.storage.transferResults(newFileId, currentFileId);
			},
			async storeCroppedResults(context: any, {fileId, coords, imageTransforms}: { fileId: string, coords: Coords, imageTransforms: ImageTransforms }) {
				const results = {
					coords,
					imageTransforms,
				};

				context.commit('STORE_CROPPER_RESULTS', {
					fileId,
					results,
				});

				context.rootState.storage.storeCroppedResults(fileId, {
					coords,
					results,
				});
			}
		},
		mutations: {
			SET_CURRENT_FILE(state: any, file: MOxieFile) {
				state.currentFile = file;
			},
			OPEN_EDITOR(state: any) {
				state.open = true;
			},
			CLOSE_EDITOR(state: any) {
				state.open = false;
			},
			STORE_ORIGINAL(
				state: any,
				{fileId, src, size}: { fileId: string, src: string, size: number }
			) {
				Vue.set(state.originals, fileId, {
					src,
					size,
				});
			},
			/**
			 * Originally, we set the originals using the filename instead of ID. Unfortunately, I found that Plupload
			 * would transform the name and upon refresh, the filename in the Gravity Forms hidden input did not match
			 * what Plupload would sanitize it to be. Specifically, hyphens were doubling up.
			 */
			TRANSFER_ORIGINAL(
				state: any,
				{newFileId, currentFileId}: { newFileId: string, currentFileId: string }
			) {
				Vue.set(state.originals, newFileId, state.originals?.[currentFileId]);
				Vue.delete(state.originals, currentFileId);
			},
			TRANSFER_RESULTS(
				state: any,
				{newFileId, currentFileId}: { newFileId: string, currentFileId: string }
			) {
				Vue.set(state.cropperResults, newFileId, state.cropperResults?.[currentFileId]);
				Vue.delete(state.cropperResults, currentFileId);
			},
			STORE_CROPPER_RESULTS(
				state: any,
				{fileId, results}: { filename: string, fileId: string, results: CropperResults },
			) {
				Vue.set(state.cropperResults, fileId, results);
			},
		},
	}
}
