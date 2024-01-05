<?php
/* @var EM_Event    $EM_Event       The current EM_Event object being displayed.                 */
/* @var int         $id             A unique ID use to display this calendar instance            */
/* @var EM_DAteTime $EM_DateTime    The current date/time in an EM_DateTime object               */
/* @var array       $args           The $args passed onto the calendar template via EM_Calendar  */
/* @var array       $calendar       The $calendar array of data passed on by EM_Calendar         */
?>
<section class="em-cal-nav ">
	<?php if( $args['has_advanced_trigger'] ): ?>
		<button class="em-search-advanced-trigger em-clickable" data-search-advanced-id="em-search-advanced-<?php echo $id; ?>"  data-parent-trigger="em-search-advanced-trigger-<?php echo $id; ?>"></button>
	<?php endif; ?>
	<div class="month input">
		<?php if( empty($args['calendar_month_nav']) ): ?>
			<form action="" method="get">
				<input type="month" class="em-month-picker" value="<?php echo $EM_DateTime->i18n('Y-m') ?>" data-month-value="<?php echo $EM_DateTime->i18n('F Y') ?>">
				<span class="toggle"></span>
			</form>
		<?php else: ?>
			<?php echo esc_html($EM_DateTime->i18n(get_option('dbem_full_calendar_month_format'))); ?>
		<?php endif; ?>
	</div>
	<div class="month-nav input">
		<a class="em-calnav em-calnav-prev" href="<?php echo esc_url($calendar['links']['previous_url']); ?>" data-disabled="<?php echo empty($calendar['links']['previous_url']) ? 1 : 0; ?>">
			<svg viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><path d="M10 14L3 7.5L10 1" stroke="#555" stroke-linecap="square"></path></svg>

		</a>
		<a  href="<?php echo esc_url($calendar['links']['today_url']); ?>" class="em-calnav-today button button-secondary size-large size-medium <?php if( date('Y-m') === $EM_DateTime->format('Y-m') ) echo 'is-today'; ?>">
			<?php esc_html_e('Today', 'events-manager'); ?>
		</a>
		<a class="em-calnav em-calnav-next" href="<?php echo esc_url($calendar['links']['next_url']); ?>" data-disabled="<?php echo empty($calendar['links']['next_url']) ? 1 : 0; ?>">
			<svg viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><path d="M5 14L12 7.5L5 1" stroke="#555" stroke-linecap="square"></path></svg>
		</a>
	</div>
</section>