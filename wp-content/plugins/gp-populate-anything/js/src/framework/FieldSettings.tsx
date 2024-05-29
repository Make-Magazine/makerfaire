import { createRoot, render } from '@wordpress/element';

abstract class FieldSettings {
	public state!: {
		field: GravityFormsField;
	};

	constructor() {
		/*
		 * Register the field setting for all field types. We do this as we typically have more robust logic
		 * for determining when to show perk settings based on a variety of conditions including making it filterable.
		 */
		for (const i in window.fieldSettings) {
			for (const selector of this.fieldSettingsSelectors()) {
				window.fieldSettings[i] += `, ${selector}`;
			}
		}

		this.initReact();
	}

	public abstract get rootEl(): Element;

	public abstract rootComponent(): () => JSX.Element;

	public abstract fieldSettingsSelectors(): string[];

	public initReact() {
		const el = this.rootEl;
		const Root = this.rootComponent();

		if (typeof createRoot === 'function') {
			const root = createRoot(el!);
			root.render(<Root />);
		} else {
			render(<Root />, el);
		}
	}
}

export { FieldSettings };
