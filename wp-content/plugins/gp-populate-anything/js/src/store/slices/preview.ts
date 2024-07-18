import { StateCreator } from 'zustand';
import { FiltersSlice } from './filters';
import { TemplatesSlice } from './templates';
import { CoreSlice } from './core';
import { FieldSettingsSyncingSlice } from '../../framework/field-settings-syncing';
import { ComputedSlice } from './computed';
import { memoizedjQueryAjax } from '../../helpers/memoizedjQueryAjax';

export interface PreviewSlice {
	previewResults:
		| {
				results: { [template: string]: string }[];
				limit: number;
		  }
		| undefined;
	previewError: string | undefined;
	previewResultsLoading: boolean | undefined;
	previewResultsPromise: JQuery.jqXHR | undefined;

	getPreviewResults: () => void;
}

const createPreviewSlice: StateCreator<
	FiltersSlice &
		ComputedSlice &
		PreviewSlice &
		TemplatesSlice &
		CoreSlice &
		FieldSettingsSyncingSlice,
	[],
	[],
	PreviewSlice
> = (set, get) => ({
	previewResults: undefined,
	previewError: undefined,
	previewResultsLoading: undefined,
	previewResultsPromise: undefined,

	getPreviewResults() {
		if (
			get().previewResultsPromise &&
			get().previewResultsPromise!.state() !== 'resolved'
		) {
			get().previewResultsPromise!.abort();
		}

		if (
			get().computed.missingTemplates.length ||
			get().computed.hasFilterFieldValue
		) {
			return;
		}

		set({ previewResultsLoading: true });

		const previewResultsPromise = memoizedjQueryAjax(window.ajaxurl, {
			data: {
				action: 'gppa_get_query_results',
				templateRows: get().computed.templateRows,
				gppaPopulate: get().populate,
				fieldSettings: JSON.stringify(get().field),
				security: window.GPPA_ADMIN.nonce,
			},
			dataType: 'json',
			method: 'POST',
		});

		previewResultsPromise
			.done((data) => {
				set({
					previewResults: {
						results: data.results,
						limit: data.limit,
					},
					previewResultsLoading: false,
				});
			})
			.fail((jqXHR) => {
				set({
					previewResults: undefined,
					previewError: jqXHR.responseText,
					previewResultsLoading: false,
				});
			});

		set({ previewResultsPromise });
	},
});

export default createPreviewSlice;
