<?php
/**
 * GravityView default field types.
 *
 * @package   GravityView
 * @license   GPL2+
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2020, Katz Web Services, Inc.
 */

/**
 * Register the default field types.
 *
 * A field type registers itself via `GravityView_Fields::register()` from its
 * constructor, so each must be instantiated here. Instantiate on
 * `after_setup_theme` priority 0 (not at autoload): the constructors translate,
 * and translating before `after_setup_theme` trips WordPress 6.7's
 * `_load_textdomain_just_in_time` notice, while `init` is too late because a
 * field with no explicit label defers its Gravity Forms label to `init`
 * priority 1. Priority 0 makes the registry available to any consumer hooked
 * at `after_setup_theme` default priority or later; code reading the registry
 * during `plugins_loaded` will see it empty. The base constructor try/catches
 * the registry's double-registration exception, so a single instantiation is
 * safe.
 *
 * @since 3.1.0
 *
 * @return void
 */
function gravityview_register_gravityview_fields() {
	new \GravityKit\GravityView\Field\Types\Address();
	new \GravityKit\GravityView\Field\Types\Calculation();
	new \GravityKit\GravityView\Field\Types\Captcha();
	new \GravityKit\GravityView\Field\Types\ChainedSelect();
	new \GravityKit\GravityView\Field\Types\Checkbox();
	new \GravityKit\GravityView\Field\Types\Consent();
	new \GravityKit\GravityView\Field\Types\CreatedBy();
	new \GravityKit\GravityView\Field\Types\CreditCard();
	new \GravityKit\GravityView\Field\Types\Currency();
	new \GravityKit\GravityView\Field\Types\CustomContent();
	new \GravityKit\GravityView\Field\Types\Date();
	new \GravityKit\GravityView\Field\Types\DateCreated();
	new \GravityKit\GravityView\Field\Types\DateUpdated();
	new \GravityKit\GravityView\Field\Types\DeleteLink();
	new \GravityKit\GravityView\Field\Types\EditLink();
	new \GravityKit\GravityView\Field\Types\Email();
	new \GravityKit\GravityView\Field\Types\EntryApproval();
	new \GravityKit\GravityView\Field\Types\EntryId();
	new \GravityKit\GravityView\Field\Types\EntryLink();
	new \GravityKit\GravityView\Field\Types\FileUpload();
	new \GravityKit\GravityView\Field\Types\Gravatar();
	new \GravityKit\GravityView\Field\Types\GravityForms();
	new \GravityKit\GravityView\Field\Types\GravityViewView();
	new \GravityKit\GravityView\Field\Types\Hidden();
	new \GravityKit\GravityView\Field\Types\HTML();
	new \GravityKit\GravityView\Field\Types\ImageChoice();
	new \GravityKit\GravityView\Field\Types\IP();
	new \GravityKit\GravityView\Field\Types\IsApproved();
	new \GravityKit\GravityView\Field\Types\IsFulfilled();
	new \GravityKit\GravityView\Field\Types\IsRead();
	new \GravityKit\GravityView\Field\Types\IsStarred();
	new \GravityKit\GravityView\Field\Types\ListField();
	new \GravityKit\GravityView\Field\Types\MultipleChoice();
	new \GravityKit\GravityView\Field\Types\MultiSelect();
	new \GravityKit\GravityView\Field\Types\Name();
	new \GravityKit\GravityView\Field\Types\Notes();
	new \GravityKit\GravityView\Field\Types\Number();
	new \GravityKit\GravityView\Field\Types\Option();
	new \GravityKit\GravityView\Field\Types\OtherEntries();
	new \GravityKit\GravityView\Field\Types\Page();
	new \GravityKit\GravityView\Field\Types\Password();
	new \GravityKit\GravityView\Field\Types\PaymentAmount();
	new \GravityKit\GravityView\Field\Types\PaymentDate();
	new \GravityKit\GravityView\Field\Types\PaymentMethod();
	new \GravityKit\GravityView\Field\Types\PaymentStatus();
	new \GravityKit\GravityView\Field\Types\Phone();
	new \GravityKit\GravityView\Field\Types\PipeRecorder();
	new \GravityKit\GravityView\Field\Types\PostCategory();
	new \GravityKit\GravityView\Field\Types\PostContent();
	new \GravityKit\GravityView\Field\Types\PostCustomField();
	new \GravityKit\GravityView\Field\Types\PostExcerpt();
	new \GravityKit\GravityView\Field\Types\PostId();
	new \GravityKit\GravityView\Field\Types\PostImage();
	new \GravityKit\GravityView\Field\Types\PostTags();
	new \GravityKit\GravityView\Field\Types\PostTitle();
	new \GravityKit\GravityView\Field\Types\Product();
	new \GravityKit\GravityView\Field\Types\Quantity();
	new \GravityKit\GravityView\Field\Types\Quiz();
	new \GravityKit\GravityView\Field\Types\QuizScore();
	new \GravityKit\GravityView\Field\Types\Radio();
	new \GravityKit\GravityView\Field\Types\Repeater();
	new \GravityKit\GravityView\Field\Types\Section();
	new \GravityKit\GravityView\Field\Types\Select();
	new \GravityKit\GravityView\Field\Types\Sequence();
	new \GravityKit\GravityView\Field\Types\Shipping();
	new \GravityKit\GravityView\Field\Types\SourceId();
	new \GravityKit\GravityView\Field\Types\SourceUrl();
	new \GravityKit\GravityView\Field\Types\Survey();
	new \GravityKit\GravityView\Field\Types\Text();
	new \GravityKit\GravityView\Field\Types\Textarea();
	new \GravityKit\GravityView\Field\Types\Time();
	new \GravityKit\GravityView\Field\Types\Total();
	new \GravityKit\GravityView\Field\Types\TransactionId();
	new \GravityKit\GravityView\Field\Types\TransactionType();
	new \GravityKit\GravityView\Field\Types\Unsubscribe();

	if ( class_exists( 'GF_User_Registration_Bootstrap' ) ) {
		new \GravityKit\GravityView\Field\Types\UserActivation();
	}

	new \GravityKit\GravityView\Field\Types\Username();
	new \GravityKit\GravityView\Field\Types\Website();
	new \GravityKit\GravityView\Field\Types\WorkflowCurrentStatusTimestamp();
	new \GravityKit\GravityView\Field\Types\WorkflowFinalStatus();
	new \GravityKit\GravityView\Field\Types\WorkflowStep();
}

// Load default field types.
add_action( 'after_setup_theme', 'gravityview_register_gravityview_fields', 0 );
