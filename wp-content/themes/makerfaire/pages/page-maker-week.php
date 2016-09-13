<?php
/*
 * Template name: Maker Week Page
 */
$ribbon_banner = get_field('ribbon_image');
get_header('version-2'); ?>

<div class="maker-week-page">
  <div class="maker-week-hero">
    <img class="hero-banner-images" src="http://makerfaire.com/wp-content/uploads/2016/04/week_photos.jpg" alt="Pictures of maker week makers">
    <?php
    if (!empty($ribbon_banner)) {
      echo '<img class="hero-badge" src="' . $ribbon_banner . '" alt="Maker Week Ribbon">';
    }
    ?>
    <div class="flags-divider"></div>
    <div class="container">
      <div class="mw-info-header row">
        <div class="description-text col-md-8">
          <?php the_field('meet_the_makers_header'); ?>
        </div>
        <div class="col-md-4">
          <div class="action-callout-box">
            <img src="<?php echo get_template_directory_uri(); ?>/images/robot.png" alt="makey robot">
            <hr>
            <?php the_field('meet_the_makers_call_to_action'); ?>
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
                  <h2 class="date-day-number <?php if( get_sub_field('multi_day')){echo "date-day-number-small";} ?>"><?php echo get_sub_field('date_day_number'); ?></h2>
                </div>
              </div>
              <?php $columns = (int) get_sub_field('column_count');?>
              <div class="col-md-8 event-day-columns" style=" -webkit-column-count: <?php echo $columns;?>;-moz-column-count: <?php echo $columns;?>;column-count: <?php echo $columns;?>;">
                <?php // check for rows (sub repeater)
                if(have_rows('day_event_block')): ?>
                    <?php // loop through rows (sub repeater)
                    while(have_rows('day_event_block')): the_row();

                      ?>
                      <div class="day-event-block">
                        <h4 class="day-event-title"><?php the_sub_field('day_event_title'); ?></h4>
                        <h4 class="day-event-title-description"><?php the_sub_field('day_event_title_description'); ?></h4>
                        <div class="day-event-content"><?php the_sub_field('day_event_body'); ?>
                          <?php if( get_sub_field('invite_only')) {
                              echo "<p><span class='label label-warning'>INVITE ONLY</span></p>";
                            } ?>
                        </div>
                      </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <div class="column-end-no-dots"></div>
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
