import 'jquery';

const $ = window.jQuery;

interface GPMultiPageNavigationArgs {
	formId: number;
	lastPage: number; // Fixed from lagePage
	activationType: 'last_page' | 'progression' | 'first_page';
	labels: { [key: string]: string };
	enableSubmissionFromLastPageWithErrors: boolean;
	pageValidity: { [key: number]: boolean}
	pagesVisited: Array<number>;
}

class GPMultiPageNavigation {
	// Properties
	formId: number;
	$formElem: JQuery;
	lastPage: number;
	activationType: string;
	labels: { [key: string]: string };
	enableSubmissionFromLastPageWithErrors: boolean;
	pageValidity: { [key: number]: boolean } = {};
	pagesVisited: Set<number>;
	addedButtons: string[];
	$footer?: JQuery;
	$saveAndContinueButton?: JQuery;
	errorPagesCount?: string;
	currentPage?: string;
	obververInitialized: boolean = false;

	constructor(args: GPMultiPageNavigationArgs) {
		this.formId = args.formId;
		this.$formElem = $('form#gform_' + this.formId);

		this.lastPage = args.lastPage;
		this.activationType = args.activationType;
		this.labels = args.labels;
		this.enableSubmissionFromLastPageWithErrors = args.enableSubmissionFromLastPageWithErrors;
		this.pagesVisited = this.getPagesVisitedFromHiddenInput();
		this.pageValidity = this.getPageValidityFromHiddenInput();
		this.addedButtons = [];

		this.init();
	}

	init(): void {
		window.gform.addAction('gppt_before_transition', (curr, next, gppt) => {
			if (!gppt.validatePage) {
				/**
				 * Older version of GPPT do not have the validatePage method.
				 * Skipping this validation step will not cause issues, it just
				 * means that the step indicators will not be updated in the
				 * case that a page is not valid. In this case, that is okay
				 * as this check here was added as a new feature to improve the
				 * UX of the step indicators.
				 */
				return;
			}

			// currentPageRealIndex is 0 based and returns the index of the page being moved to.
			const currentPageIndex: number = gppt.getCurrentPageRealIndex() + 1;

			const pagesVisited = this.getPagesVisitedFromHiddenInput();
			pagesVisited.add(currentPageIndex);
			this.setPagesVisitedHiddenInput(pagesVisited);

			// Validate all pages in the range (including missing ones).
			const pagesToValidate = [];
			if (this.pagesVisited.size > 0) {
				const minPage = Math.min(...Array.from(this.pagesVisited));
				const maxPage = Math.max(...Array.from(this.pagesVisited));
				for (let pageId = minPage; pageId <= maxPage; pageId++) {
					pagesToValidate.push(pageId);
				}
			}

			pagesToValidate.forEach((pageId) => {
				this.pageValidity[pageId] = gppt.validatePage(pageId);
			});
		});

		window.gform.utils.addAsyncFilter('gform/submission/pre_submission', async (data: any) => {
			// If all pages are valid, reset page validity to prevent submission issues.
			const allValid = Object.values(this.pageValidity).every(Boolean);
			if (allValid) {
				this.pagesVisited = new Set<number>();
			}
			return data;
		});

		window.gform.addAction('gppt_after_transition', (gppt) => {
			this.updateUI();
			$('input#gw_page_progression').val(gppt.currentPage);
		});

		const pageLinksSelector = 'a.gpmpn-page-link, a.gwmpn-page-link, .gpmpn-page-link a';

		$(document).on('click', pageLinksSelector, function(event) {
			event.preventDefault();

			const hrefArray = $(this).attr('href')?.split('#') || [];

			if (hrefArray.length >= 2) {
				const $parentForm = $(this).parents('form');
				let $formElem = $parentForm.length > 0 ? $parentForm : $('.gform_wrapper form');
				// Get form element for WC GF Add-on.
				$formElem = $formElem.length > 0 ? $formElem : $('.gform_wrapper').parent('form');
				const formId = $formElem.attr('id')?.split('_')[1] || '';
				const pageNumber = hrefArray.pop() || '';

				GPMultiPageNavigation.postToPage(pageNumber, formId, true);
			}
		});

		this.updateUI();

		this.observeAndSyncSteps();

	}

	observeAndSyncSteps(): void {
		if (this.obververInitialized) {
			return;
		}

		this.obververInitialized = true;

		const observer = new MutationObserver((mutations) => {
			for (const mutation of mutations) {
				const pageId = parseInt(mutation.target?.id?.split?.('_')?.[3]);
				if (pageId) {
					mutation.target.classList.toggle(
						'gf_step_completed',
						this.shouldMarkStepComplete(pageId)
					);
				}
			}
		});

		for (const pageId of Object.keys(this.pageValidity)) {
			const step = document.getElementById(`gf_step_${this.formId}_${pageId}`);
			if (step) {
				observer.observe(step, { attributes: true });
			}
		}
	}

	getPagesVisitedInputId(): string {
		return `#gpmpn_pages_visited_${this.formId}`;
	}

	getPagesVisitedFromHiddenInput(): Set<number> {
		let pagesVisited = new Set<number>();

		try {
			const maybeJSON = $(this.getPagesVisitedInputId()).val() as string;
			const parsed = JSON.parse(maybeJSON);
			if (Array.isArray(parsed)) {
				pagesVisited = new Set<number>(parsed);
			}
		} catch (e) {
			// no-op
		}

		return pagesVisited;
	}

	setPagesVisitedHiddenInput(pagesVisited: Set<number>): void {
		this.pagesVisited = pagesVisited;
		const pagesVisitedString = JSON.stringify(Array.from(pagesVisited));
		$(this.getPagesVisitedInputId()).val(pagesVisitedString);
	}

	getPageValidityInputId(): string {
		return `#gpmpn_page_validity_${this.formId}`;
	}

	getPageValidityFromHiddenInput(): { [key: number]: boolean } {
		let pageValidity = {};

		try {
			const maybeJSON = $(this.getPageValidityInputId()).val() as string;
			const parsed = JSON.parse(maybeJSON);
			if (typeof parsed === 'object') {
				pageValidity = parsed;
			}
		} catch (e) {
			// no-op
		}

		return pageValidity;
	}


	isStepValid(stepId: number): boolean {
		return this.pageValidity[stepId] && this.pagesVisited.has(stepId);
	}

	shouldMarkStepComplete(stepId: number): boolean {
		return this.isStepValid(stepId) && stepId !== Number(this.getCurrentPage());
	}

	updateUI(): void {
		if (this.$formElem.length <= 0) {
			this.$formElem = $('#gform_wrapper_' + this.formId);
		}

		// set page specific elements
		this.$footer = $('#gform_page_' + this.formId + '_' + this.getCurrentPage() + ' .gform_page_footer');
		this.$saveAndContinueButton = this.$footer.find('a.gform_save_link');

		if (this.activationType == 'last_page' && !this.isLastPageReached()) {
			return;
		}

		const $steps = this.$formElem.find('.gf_step');

		$steps.each((index, el) => {
			// As of GF 2.5 we cannot rely on the text displayed to figure out the step number
			// as they can change dynamically with conditional logic. Use index in DOM instead.
			const stepNumber = index + 1;

			$(el).toggleClass('gf_step_completed', this.shouldMarkStepComplete(stepNumber));

			if (this.activationType == 'progression' && stepNumber > this.getPageProgression()) {
				return;
			}

			if (stepNumber != this.getCurrentPage()) {
				const existingLink = $('a[href="#' + stepNumber + '"]');
				if (!existingLink.length || !$(el).hasClass('gpmpn-step-linked')) {
					$(el).html(this.getPageLinkMarkup(stepNumber, $(el).html() || '')).addClass('gpmpn-step-linked');
				}
			} else {
				// If this step was changed to a link, remove the link.
				$(el)
					.find('a')
					.children()
					.unwrap();

				$(el)
					.find('a').remove();

				$(el).addClass('gpmpn-step-current');
				$(el).addClass('gf_step_active');
			}
		});

		if (this.activationType == 'last_page' && !this.isLastPage() && this.isLastPageReached()) {
			this.addBackToLastPageButton();
		} else if (this.activationType == 'progression' && this.getCurrentPage() < this.getPageProgression()) {
			this.addBackToLastPageButton(this.getPageProgression());
		} else if (!this.isLastPage() && this.wasFinalSubmissionAttempted()) {
			this.addNextPageWithErrorsButton();
		}

		this.$formElem.data('GPMultiPageNavigation', this);

		// Use type assertion to tell TypeScript this is a valid index
		(window as any)[`gpmpn_${this.formId}`] = this;
	}

	getPageLinkMarkup(stepNumber: number, content: string): string {
		return '<a href="#' + stepNumber + '" class="gwmpn-page-link gwmpn-default gpmpn-page-link gpmpn-default">' + content + '</a>';
	}

	addBackToLastPageButton(page?: number): void {
		const targetPage = typeof page == 'undefined' ? this.lastPage : page;
		const $button = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + targetPage + ', ' + this.formId + ' );" value="' + this.labels.backToLastPage + '" class="button gform_button gform_last_page_button">';

		this.insertButton($button);
	}

	addNextPageWithErrorsButton(): void {
		const page = 0;
		const label = this.getErrorPagesCount() > 1 ? this.labels.nextPageWithErrors : this.labels.submit;
		const cssClass = this.getErrorPagesCount() > 1 ? 'gform_next_page_errors_button' : 'gform_resubmit_button';
		const $button = '<input type="button" onclick="GPMultiPageNavigation.postToPage( ' + page + ', ' + this.formId + ' );" value="' + label + '" class="button gform_button ' + cssClass + '">';

		if (this.getErrorPagesCount() <= 1 && !this.enableSubmissionFromLastPageWithErrors) {
			this.addBackToLastPageButton();
		} else {
			this.insertButton($button);
		}
	}

	insertButton($button: string): void {
		// Prevent duplicate buttons from being added.
		if (this.addedButtons.indexOf($button) >= 0) {
			return;
		}

		if (this.$saveAndContinueButton && this.$saveAndContinueButton.length > 0) {
			this.$saveAndContinueButton.before($button);
		} else if (this.$footer) {
			this.$footer.append($button);
		}

		this.addedButtons.push($button);
	}

	getCurrentPage(): any {
		// Get the current page from the form
		return Number(this.$formElem.find('input#gform_source_page_number_' + this.formId).val());
	}

	getPageProgression(): number {
		return parseInt($('input#gw_page_progression').val() as string);
	}

	getErrorPagesCount(): any {
		if (!this.errorPagesCount) {
			this.errorPagesCount = this.$formElem.find('input#gw_error_pages_count').val() as string;
		}

		return this.errorPagesCount;
	}

	isLastPage(): boolean {
		return this.getCurrentPage() >= this.lastPage;
	}

	isLastPageReached(): boolean {
		return this.isLastPage() || this.$formElem.find('input#gw_last_page_reached').val() === '1';
	}

	wasFinalSubmissionAttempted(): boolean {
		return this.$formElem.find('input#gw_final_submission_attempted').val() === '1';
	}

	// Static method for posting to a page
	static postToPage(page: string | number, formId: string | number, bypassValidation?: boolean): void {
		const $form = $('form#gform_' + formId);
		const $targetPageInput = $form.find('input#gform_target_page_number_' + formId);
		
		// We need to get the instance to access its methods
		const instance = (window as any)[`gpmpn_${formId}`] as GPMultiPageNavigation | undefined;
		const currentPage = instance ? instance.getCurrentPage() : null;

		$targetPageInput.val(page);

		// Handle GPPT Soft Validation differently. Posting the form will do some weird stuff!
		const gppt = (window as any)[`GPPageTransitions_${formId}`];

		if (typeof gppt !== 'undefined' && gppt.enableSoftValidation) {
			if (!bypassValidation && !gppt.validate()) {
				return;
			}

			/*
			 * If the page that we're submitting to is 0 (end of the form), go straight to submit handler.
			 *
			 * GPPT will not do any handling for the resubmit/next page with errors buttons.
			 */
			if (page == 0) {
				$form.submit();
				return;
			}

			// Get the instance
			const self = (window as any)[`gpmpn_${formId}`] as GPMultiPageNavigation | undefined;
			
			if (!self) {
				return;
			}

			// Get the index for the next page.
			const $activeSlides = self.$formElem
				.find('.swiper-slide:not(.swiper-slide-disabled)');

			const $targetSlide = self.$formElem.find('#gform_page_' + self.formId + '_' + page);

			// Transition to the slide.
			gppt.swiper.slideTo($activeSlides.index($targetSlide));

			// Update page numbers, the naming is admittedly kind of confusing.
			gppt.currentPage = page;
			gppt.sourcePage = currentPage;

			gppt.updateProgressIndicator(currentPage);

			self.$formElem.trigger('softValidationPageLoad.gppt', [
				page,
				currentPage,
				formId,
			]);

			return;
		}

		if (bypassValidation) {
			const $bypassValidationInput = $('<input type="hidden" name="gw_bypass_validation" id="gw_bypass_validation" value="1" />');
			$form.append($bypassValidationInput);
		}

		/**
		 * If submit buttons are hidden via conditional logic (next/prev/submit), form will not be able to submit; this code finds
		 * all hidden submit inputs and hides them in a way that will still enable submission.
		 */
		$form.find('.gform_page_footer:visible').find('input[type="submit"], input[type="button"]').not(':visible').css({ display: 'block', visibility: 'hidden', position: 'absolute' });

		/**
		 * If attempting to submit the form (page = 0, happens w/ "Next Page with Errors" button), move the Submit
		 * button to the current page so Gravity Forms will not abort the submission.
		 */
		if (parseInt(page as string) === 0) {
			$('#gform_submit_button_' + formId).appendTo('.gform_page_footer:visible').css({ display: 'block', visibility: 'hidden', position: 'absolute' });
			/**
			 * GF adds spinners to all Submit and Next buttons when the form is submitted as only one of these button
			 * types is visible for each page. GPMPN moves the submit button to the current page when submitting the
			 * form early (i.e. Next Page with Errors button). This results in multiple spinners showing as the
			 * Submit and Next buttons are on the same page. To resolve this, we set our custom buttons as the spinner
			 * target so only a single spinner is displayed.
			 */
			window.gform.addFilter('gform_spinner_target_elem', function($target) {
				// GF doesn't provide a way to check if a function is already bound to a filter so let's remove it
				// as part of the function and it will be rebound when it is applicable again.
				window.gform.removeFilter('gform_spinner_target_elem', 10, 'gpmpn_set_spinner_target');
				const $nextPage = $('.gform_next_page_errors_button:visible, .gform_resubmit_button:visible');
				return $nextPage.length ? $nextPage : $target;
			}, 10, 'gpmpn_set_spinner_target');
		}

		$form.submit();
	}
}

// Assign the class to window.GPMultiPageNavigation
window.GPMultiPageNavigation = GPMultiPageNavigation;

/**
 * Take over Gravity Forms gformInitSpinner function which allows us to append the spinner after other custom buttons
 */
window.gformOrigInitSpinner = window.gformInitSpinner;
window.gformInitSpinner = function(formId: number, spinnerUrl?: string): void {
	if (typeof spinnerUrl == 'undefined' || !spinnerUrl) {
		spinnerUrl = window.gform.applyFilters('gform_spinner_url', window.gf_global.spinnerUrl, formId);
	}

	const $form = jQuery('#gform_' + formId);

	$form.submit(function() {
		if (jQuery('#gform_ajax_spinner_' + formId).length == 0) {
			$form.find('.gform_page_footer').append('<img id="gform_ajax_spinner_' + formId + '"  class="gform_ajax_spinner" src="' + spinnerUrl + '" alt="" />');
		}
	});
};
