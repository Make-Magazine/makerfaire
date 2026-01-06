import { StateCreator, UseBoundStore } from 'zustand';
import $ from 'jquery';

export interface FieldSettingsSyncingSlice {
	disableSyncing?: boolean;
	field?: GravityFormsField;

	/* Actions */
	setField: (field: GravityFormsField) => void;
}

export interface FieldSettingsSyncingDependencies {
	importFieldSettings: () => void;
	postImportFieldSettings?: () => void;
}

const createFieldSettingsSyncingSlice: StateCreator<
	FieldSettingsSyncingDependencies & FieldSettingsSyncingSlice,
	[],
	[],
	FieldSettingsSyncingSlice
> = (set, get) => ({
	setField: (field: GravityFormsField) => {
		const existingFieldId = get()?.field?.id;

		// Prevent syncing back to the field settings as we're importing as it can result in an incomplete import.
		set({
			disableSyncing: true,
		});

		set({ field });

		if (field.id !== existingFieldId) {
			get().importFieldSettings();
		}

		set({
			disableSyncing: false,
		});

		if (typeof get().postImportFieldSettings === 'function') {
			// @ts-ignore
			get().postImportFieldSettings();
		}
	},
});

export const subscribeStoreToFieldSettings = (store: UseBoundStore<any>) => {
	$(document).on(
		'gform_load_field_settings',
		(event: JQuery.Event, field: GravityFormsField) => {
			store.getState().setField(field);
		}
	);

	window.gform.addAction(
		'gform_post_set_field_property',
		(
			name: string,
			field: GravityFormsField,
			value: any,
			previousValue: any
		) => {
			store.getState().setField(field);
		}
	);

	window.gform.addAction(
		'gform_after_refresh_field_preview',
		(fieldId: number) => {
			if (fieldId) {
				const field = window.GetFieldById(fieldId);
				if (field) {
					store.getState().setField(field);
				}
			}
		}
	);
};

export const subscribeFieldSettingsToStore = (
	store: UseBoundStore<any>,
	mappingFn: (state: any) => { [x: string]: unknown }
) => {
	store.subscribe(
		(state: FieldSettingsSyncingSlice & { [x: string]: unknown }) => {
			const field = window.GetSelectedField();

			if (
				!state.field?.id ||
				state.field.id !== field.id ||
				state.disableSyncing
			) {
				return;
			}

			Object.assign(field, {
				...field,
				...mappingFn(state),
			});
		}
	);
};

export default createFieldSettingsSyncingSlice;
