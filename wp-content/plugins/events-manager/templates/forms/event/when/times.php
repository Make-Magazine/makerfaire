<?php
// The following are included in the scope of this event date range picker
/* @var int $id */
/* @var EM_Event $EM_Event */
$hours_format = em_get_hour_format();
?>
<div class="em-time-range">
	<fieldset class="inline">
		<legend><?php _e('Event Times','events-manager'); ?></legend>
		<label for="em-event-start-time-<?php echo $id ?>" class="screen-reader-text"><?php esc_html_e('Start Time', 'events-manager'); ?></label>
		<input class="em-time-input em-time-start inline" id="em-event-start-time-<?php echo $id ?>" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo $EM_Event->start()->format($hours_format); ?>" placeholder="<?php esc_html_e('Start Time', 'events-manager'); ?>" >
		<?php _e('to','events-manager'); ?>
		<label for="em-event-end-time-<?php echo $id ?>" class="screen-reader-text"><?php esc_html_e('End Time', 'events-manager'); ?></label>
		<input class="em-time-input em-time-end inline" id="em-event-end-time-<?php echo $id ?>" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo $EM_Event->end()->format($hours_format); ?>" placeholder="<?php esc_html_e('End Time', 'events-manager'); ?>" >
	</fieldset>
	<label class="inline-left" for="em-time-all-day-<?php echo $id; ?>"><?php _e('All day','events-manager'); ?></label>
	<input type="checkbox" class="em-time-all-day" name="event_all_day" id="em-time-all-day-<?php echo $id; ?>" value="1" <?php if(!empty($EM_Event->event_all_day)) echo 'checked="checked"'; ?> >
</div>