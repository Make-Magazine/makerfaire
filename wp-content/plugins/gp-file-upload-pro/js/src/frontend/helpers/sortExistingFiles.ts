import Sortable from 'sortablejs';

const $ = window.jQuery;

/**
 * Make existing file previews sortable and keep their order in sync with Gravity Forms'
 * hidden input, while preserving correct file deletion after reordering.
 */
export default function sortExistingFiles(fieldId: string) {
	const container = document.getElementById(`preview_existing_files_${fieldId}`);

	if (!container || (container as any).gpfupSortable) {
		return;
	}

	const input = document.querySelector(`input[name="input_${fieldId}"]`) as HTMLInputElement | null;

	if (!input) {
		return;
	}

	let urls: string[];

	try {
		urls = JSON.parse(input.value);
	} catch (e) {
		return;
	}

	const getPreviews = function() : HTMLElement[] {
		return Array.from(container.children).filter((el) => {
			return el.classList.contains('ginput_preview');
		}) as HTMLElement[];
	};

	let baseline = getPreviews();

	if (!Array.isArray(urls) || urls.length !== baseline.length) {
		return;
	}

	container.classList.add('gpfup__existing-files');

	baseline.forEach((preview, index) => {
		// Pair each preview with its file URL so we can rewrite the hidden input from the DOM order.
		preview.dataset.gpfupUrl = urls[index];
		preview.classList.add('gpfup__existing-file');

		const handle = document.createElement('div');
		handle.className = 'gpfup__drag-handle';
		preview.insertBefore(handle, preview.firstChild);
	});

	const syncInput = function() : void {
		input.value = JSON.stringify(getPreviews().map((preview) => preview.dataset.gpfupUrl));
	};

	const applyOrder = function(order: HTMLElement[]) : void {
		order.forEach((preview) => container.appendChild(preview));
		syncInput();
	};

	// Move a preview to a specific index within the container.
	const moveToIndex = function(preview: HTMLElement, index: number) : void {
		const others = getPreviews().filter((item) => item !== preview);

		container.insertBefore(preview, others[index] || null);
	};

	Sortable.create(container, {
		draggable: '.ginput_preview',
		handle: '.gpfup__drag-handle',
		animation: 200,
		forceFallback: true,
		ghostClass: 'ghost',
		onStart: () => document.documentElement.classList.add('gpfup--dragging'),
		onEnd: () => {
			document.documentElement.classList.remove('gpfup--dragging');
			syncInput();
		},
	});

	(container as any).gpfupSortable = true;

	/**
	 * Gravity Forms & Gravity Flow deletes existing files by their original preview index.
	 * Temporarily restore that order while the delete request is in progress.
	 */
	const originalDeleteFile = window.DeleteFile;
	const originalEndDeleteFile = window.EndDeleteFile;

	if (typeof originalDeleteFile !== 'function' || typeof originalEndDeleteFile !== 'function') {
		return;
	}

	// The order to restore once the file has been deleted.
	let pendingOrder: HTMLElement[] | null = null;
	let pendingDeletion: HTMLElement | null = null;

	window.DeleteFile = function(leadId: number, deleteFieldId: number, deleteButton: HTMLElement) {
		const preview = $(deleteButton).closest('.ginput_preview')[0];
		const baselineIndex = baseline.indexOf(preview);

		if (String(deleteFieldId) !== String(fieldId) || baselineIndex === -1) {
			return originalDeleteFile(leadId, deleteFieldId, deleteButton);
		}

		const currentOrder = getPreviews();

		moveToIndex(preview, baselineIndex);

		const confirmed = originalDeleteFile(leadId, deleteFieldId, deleteButton);

		if (!confirmed) {
			applyOrder(currentOrder);

			return confirmed;
		}

		pendingOrder = currentOrder.filter((item) => item !== preview);
		pendingDeletion = preview;

		return confirmed;
	};

	window.EndDeleteFile = function(endFieldId: number, fileIndex: number) {
		originalEndDeleteFile(endFieldId, fileIndex);

		if (String(endFieldId) !== String(fieldId) || !pendingOrder) {
			return;
		}

		baseline = baseline.filter((item) => item !== pendingDeletion);

		applyOrder(pendingOrder);

		pendingOrder = null;
		pendingDeletion = null;
	};
}
