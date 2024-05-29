import { StateCreator } from 'zustand';
import { FieldSettingsSyncingSlice } from '../../framework/field-settings-syncing';
import { PropertiesSlice } from './properties';
import { CoreSlice } from './core';

const { strings } = window.GPPA_ADMIN;

export interface TemplatesSlice {
	templates: any;

	setTemplates: (templates: any) => void;
	updateTemplate: (templateRowId: string, value: string) => void;
}

const createTemplatesSlice: StateCreator<
	CoreSlice & PropertiesSlice & FieldSettingsSyncingSlice & TemplatesSlice,
	[],
	[],
	TemplatesSlice
> = (set, get) => ({
	templates: {},

	setTemplates(templates: any) {
		set({ templates });
	},

	updateTemplate(templateRowId: string, value: string) {
		set({
			templates: {
				...get().templates,
				[templateRowId]: value,
			},
		});
	},
});

export default createTemplatesSlice;
