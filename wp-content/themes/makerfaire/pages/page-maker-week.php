<?php
/*
 * Template name: Maker Week Page
 */

get_header('version-2'); ?>

<div class="maker-week-page">
  <div class="maker-week-hero">
    <img class="hero-banner-images" src="http://makerfaire.com/wp-content/uploads/2016/04/week_photos.jpg"
      alt="Pictures of maker week makers">
    <img class="hero-badge" src="http://makerfaire.com/wp-content/uploads/2016/04/makerweek_logo.png"
      alt="Bay Area Maker Week">
    <div class="flags-divider"></div>
    <div class="container">
      <div class="mw-info-header row">
        <div class="description-text col-md-8">
          Calling all Makers! 
          Join us for Maker Week—the week leading up to Maker Faire Bay Area 2015. Here you’ll find a full run-down of all the activities in which Makers can participate during Maker Week. 
          Check back often to see what other exciting events and activities have been added to the schedule.
        </div>
        <div class="col-md-4">
          <div class="action-callout-box">
            Have an event, meetup, activity, party, launch, or other exciting offering for Maker Week? Let us know, we’d love to add it here!
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <?php
      function meet_the_makers_rows() {
        $months_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        // check for rows (parent repeater)
        if(have_rows('meet_the_makers_row_group')):
          ?>
          <?php // loop through rows (parent repeater)
          while(have_rows('meet_the_makers_row_group')): the_row(); ?>
            <div class="row meet-the-makers-row">
              <div class="col-md-4 dynamic-date-badge">
                <h4 class="date-day-name"><?php echo get_sub_field('date_day_name'); ?></h4>
                <div class="calendar-date">
                  <h3 class="date-month"><?php echo $months_array[get_sub_field('date_month') - 1] ?></h3>
                  <h2 class="date-day-number"><?php echo get_sub_field('date_day_number'); ?></h2>
                </div>
              </div>
              <div class="col-md-8">
                <div class="row">
                  <?php // check for rows (sub repeater)
                  if(have_rows('day_event_block')): ?>
                      <?php // loop through rows (sub repeater)
                      while(have_rows('day_event_block')): the_row();
                        ?>
                        <div class="col-md-6 day-event-block">
                          <h4 class="day-event-title"><?php the_sub_field('day_event_title'); ?></h4>
                          <h4 class="day-event-title-description"><?php the_sub_field('day_event_title_description'); ?></h4>
                          <div><?php the_sub_field('day_event_body'); ?></div>
                        </div>
                      <?php endwhile; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php endif;
      }
      meet_the_makers_rows();
    ?>
  </div>
</div>

<?php get_footer(); ?>
