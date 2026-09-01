/**
 * Minimal typings for the subset of SortableJS that we use. SortableJS 1.10.2 does not ship its own typings.
 */
declare module 'sortablejs' {
	interface SortableOptions {
		draggable?: string
		handle?: string
		animation?: number
		forceFallback?: boolean
		ghostClass?: string
		group?: string
		disabled?: boolean
		onStart?: (event: any) => void
		onEnd?: (event: any) => void
	}

	export default class Sortable {
		static create(element: HTMLElement, options?: SortableOptions): Sortable;
		destroy(): void;
	}
}
