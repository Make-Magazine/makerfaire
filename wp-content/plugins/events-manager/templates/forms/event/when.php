<?php
global $EM_Event, $post;
$required = apply_filters('em_required_html','<i>*</i>');
$id = rand();
?>
<div class="em event-form-when input" id="em-form-when">
	<?php if( get_option('dbem_dates_range_double_inputs', false) ): ?>
		<?php include( em_locate_template('forms/event/when/dates-separate.php') ); ?>
	<?php else: ?>
		<?php include( em_locate_template('forms/event/when/dates.php') ); ?>
	<?php endif; ?>
	<?php include( em_locate_template('forms/event/when/times.php') ); ?>
	<?php include( em_locate_template('forms/event/when/timezone.php') ); ?>
	<span id='event-date-explanation'>
	<?php esc_html_e( 'This event spans every day between the beginning and end date, with start/end times applying to each day.', 'events-manager'); ?>
	</span>
	<?php if( get_option('dbem_event_status_enabled') ) : ?>
		<?php include( em_locate_template('forms/event/when/active-status.php') ); ?>
	<?php endif; ?>
</div>  
<?php if( false && get_option('dbem_recurrence_enabled') && $EM_Event->is_recurrence() ) : //in future, we could enable this and then offer a detach option alongside, which resets the recurrence id and removes the attachment to the recurrence set ?>
<input type="hidden" name="recurrence_id" value="<?php echo $EM_Event->recurrence_id; ?>" />
<?php endif;