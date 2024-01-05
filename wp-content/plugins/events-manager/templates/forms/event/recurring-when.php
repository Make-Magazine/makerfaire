<?php
/* Used by the admin area to display recurring event time-related information - edit with caution */
global $EM_Event;
$days_names = em_get_days_names();
$hours_format = em_get_hour_format();
$classes = array();
$id = rand();
?>
<div id="em-form-recurrence" class="em event-form-recurrence event-form-when">
	<?php include( em_locate_template('forms/event/when/times.php') ); ?>
	<?php include( em_locate_template('forms/event/when/timezone.php') ); ?>
	
	<div class="<?php if( !empty($EM_Event->event_id) ) echo 'em-recurrence-reschedule'; ?>">
		<?php if( !empty($EM_Event->event_id) ): ?>
		<div class="recurrence-reschedule-warning">
		    <p><em><?php echo sprintf(esc_html__('Current Recurrence Pattern: %s', 'events-manager'), $EM_Event->get_recurrence_description()); ?></em></p>
		    <p><strong><?php esc_html_e( 'Modifications to event dates will cause all recurrences of this event to be deleted and recreated, previous bookings will be deleted.', 'events-manager'); ?></strong></p>
		    <p>
		       <a href="<?php echo esc_url( add_query_arg(array('scope'=>'all', 'recurrence_id'=>$EM_Event->event_id), em_get_events_admin_url()) ); ?>">
	                <strong><?php esc_html_e('You can edit individual recurrences and disassociate them with this recurring event.', 'events-manager'); ?></strong>
	           </a>
		    </p>
		</div>
		<?php endif; ?>
		<div class="event-form-when-wrap <?php if( !empty($EM_Event->event_id) && empty($_REQUEST['reschedule']) ) echo 'reschedule-hidden'; ?>">
			<div class="event-form-recurrence-when">
				<?php if( get_option('dbem_dates_range_double_inputs', false) ): ?>
					<?php include( em_locate_template('forms/event/when/dates-separate.php') ); ?>
				<?php else: ?>
					<?php include( em_locate_template('forms/event/when/dates.php') ); ?>
				<?php endif; ?>
				<?php include( em_locate_template('forms/event/when/recurrence-pattern.php') ); ?>
				<?php include( em_locate_template('forms/event/when/recurrence-duration.php') ); ?>
			</div>
		</div>
		<?php if( !empty($EM_Event->event_id) ): ?>
		<div class="recurrence-reschedule-buttons">
		    <a href="<?php echo esc_url(add_query_arg('reschedule', null)); ?>" class="button-secondary button em-reschedule-cancel<?php if( empty($_REQUEST['reschedule']) ) echo ' reschedule-hidden'; ?>" data-target=".event-form-when-wrap">
		        <?php esc_html_e('Cancel Reschedule', 'events-manager'); ?>
		    </a>
		    <a href="<?php echo esc_url(add_query_arg('reschedule', '1')); ?>" class="em-reschedule-trigger button button-secondary<?php if( !empty($_REQUEST['reschedule']) ) echo ' reschedule-hidden'; ?>" data-target=".event-form-when-wrap">
		        <?php esc_html_e('Reschedule Recurring Event', 'events-manager'); ?>
		    </a>
		    <input type="hidden" name="event_reschedule" class="em-reschedule-value" value="<?php echo empty($_REQUEST['reschedule']) ? 0:1 ?>" />
		</div>
		<?php endif; ?>
		<?php if( get_option('dbem_event_status_enabled') ) : ?>
			<?php include( em_locate_template('forms/event/when/active-status.php') ); ?>
		<?php endif; ?>
	</div>
</div>