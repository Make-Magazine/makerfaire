<?php
// The following are included in the scope of this event date range picker
/* @var int $id */
/* @var EM_Event $EM_Event */
?>
<div class="em-recurrence-duration em-recurring-text">
	<?php ob_start(); ?>
	<input id="recurrence-days-<?php echo $id; ?>" type="text" size="2" maxlength="8" name="recurrence_days" class="inline" value="<?php echo esc_attr($EM_Event->recurrence_days); ?>">
	<?php $input = ob_get_clean(); ?>
	<label><?php echo sprintf(__('Each event spans %s day(s)','events-manager'), $input); ?></label>
</div>
