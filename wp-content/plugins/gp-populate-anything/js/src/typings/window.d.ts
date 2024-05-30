import GPPALiveMergeTags from "../classes/GPPALiveMergeTags";
import GPPopulateAnything from "../classes/GPPopulateAnything";

declare global {
	type GPPAPopulate = 'choices' | 'values';

	type GPPAProperty = {
		value: string
		label: string
		orderby?: boolean
		group?: string
		operators?: string[]
	}

	type GPPAFilter = {
		property: string
		operator: string
		value: string
	}

	interface String {
		gformFormat: (...args: any[]) => string
	}

	interface GPPAObjectType {
		id: string
		label: string
		properties: any
		groups: {
			[key: string]: {
				label: string
				operators?: string[]
			}
		}
		templates: any
		restricted: boolean
		'primary-property'?: {
			label: string
		}
		supportsNullFilterValue?: boolean
	}

	interface Window {
		GFMergeTag: any;
		gppaLiveMergeTags: { [formId: string]: GPPALiveMergeTags };
		gppaForms: { [formId: string]: GPPopulateAnything };
		jQuery: JQueryStatic
		field: GravityFormsField
		fieldSettings: { [fieldType: string]: string };
		ajaxurl: string
		form: any
		gfMergeTagsObj: any
		GPPA_ADMIN: {
			isSuperAdmin: boolean
			interpretedMultiInputFieldTypes: string[]
			multiSelectableChoiceFieldTypes: string[]
			strings: {
				[key: string]: string
			}
			defaultOperators: string[]
			objectTypes: { [objectTypeId: string]: GPPAObjectType }
			nonce: string
		}
		gform: any
		gf_raw_input_change: any
		GPPA: {
			AJAXURL: string
			GF_BASEURL: string
			NONCE: string
			I18N: { [s: string]: string }
		},
		[gppaForm: `GPPA_FORM_${string}`]: {
			SHOW_ADMIN_FIELDS_IN_AJAX: boolean
		},
		gf_global: any
		gformInitChosenFields: any
		GetSelectedField: any
		ToggleCalculationOptions: any
		GetInputType: any
		SetFieldProperty: any
		gformInitDatepicker: any
		gformCalculateTotalPrice: (formId: string | number) => void
		GPLimitDates: {
			initDisabledDatepicker: ( $input: JQuery ) => void
		}
		[key: string]: any
	}
}
