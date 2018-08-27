<?php
/*
 * Template name: Schedule
 */
get_header();

//look for day of week or type filter variables
$sched_dow = (isset($wp_query->query_vars['sched_dow']) ? ucfirst(urldecode($wp_query->query_vars['sched_dow'])) : 'All Days');
$sched_type = (isset($wp_query->query_vars['sched_type']) ? ucfirst(urldecode($wp_query->query_vars['sched_type'])) : 'All Types');

$schedule_ids = get_field('schedule_ids');
if (have_posts()) {
   ?>
   <div class="container">
      <div class="row schedule-wrapper">
         <h1><?php echo get_the_title(); ?></h1>
         <hr>
      </div>
   </div><?php
}
if ($schedule_ids && $schedule_ids != '') { //display the new schedule page
   //create_calendar(get_field('schedule_ids'));
   ?>
   <input type="hidden" id="forms2use" value="<?php echo get_field('schedule_ids'); ?>" />
   <input type="hidden" id="schedType" value="<?php echo $sched_type; ?>" />
   <input type="hidden" id="schedDOW"  value="<?php echo $sched_dow; ?>" />

   <div id="page-schedule" class="container schedule-table" ng-controller="scheduleCtrl" ng-app="scheduleApp" ng-cloak="">
      <div class="schedule-wrapper">
         <!--<a href="/wp-content/themes/makerfaire/FaireSchedule.ics">Download iCal</a>-->
         <div ng-cloak>
            <div class="schedule-filters container" ng-if="showSchedules">
               <div class="mtm-search">
                  <form class="form-inline">
                     <label for="mtm-search-input"><?php _e("Search by category, keyword, project, sponsor or maker name", 'makerfaire') ?></label><br/>
                     <input ng-model="schedSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Enter your search", 'makerfaire') ?>" type="text">        
                  </form>
               </div>
               
               <div class="sched-col-4">
                  <div class="dropdown">
                     <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span ng-show="schedSearch.nicename != ''">{{schedSearch.nicename}}</span>
                        <span ng-show="schedSearch.nicename == ''">All Stages</span>
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                     </button>

                     <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                        <li>
                           <a class="pointer-on-hover" ng-click="schedSearch.nicename = ''"><?php _e("All", 'makerfaire') ?></a>
                        </li>
                        <li ng-repeat="stage in stages">                     
                           <a class="pointer-on-hover" ng-click="schedSearch.nicename = stage">{{ stage}}</a>
                        </li>
                        <li ng-repeat="schedule in schedules | filter:schedSearch |  orderBy: 'stageOrder' | unique: 'nicename'">
                           <a ng-click="schedSearch.nicename = schedule.nicename)">{{schedule.nicename}}</a>
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="sched-col-4">
                  <div class="dropdown">
                     <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span ng-show="schedSearch.type != ''">{{schedSearch.type}}</span>
                        <span ng-show="schedSearch.type == ''">All Types</span>
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                     </button>

                     <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                        <li>
                           <a class="pointer-on-hover" ng-click="schedSearch.type = ''"><?php _e("All Types", 'makerfaire') ?></a>
                        </li>
                        
                        <li ng-repeat="schedule in schedules | filter:schedSearch |  orderBy: 'type' | unique: 'type'">
                           <a class="pointer-on-hover" ng-click="schedSearch.type = schedule.type">{{ schedule.type}}</a>
                        </li>
                     </ul>
                  </div>                  
               </div>

<!--
               <div class="sched-col-4">
                  <span class="dropdown day-filter">
                     <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                       <!-- <span ng-show="dateFilter != 'All Days'">{{schedDay | date: 'EEEE'}}</span>
                       <span ng-show="dateFilter == ''">All Days</span> -->
                       <!-- <span><?php _e('All Days', 'MiniMakerFaire'); ?></span>
                        <i class="fa fa-angle-down fa-lg" aria-hidden="true"></i>
                     </button>
                     <ul ng-class="{'active':'{{schedDay| date: 'EEEE'}}' == dateFilter}" class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                        <li>
                           <a ng-click="setDateFilter('')"><?php _e('All Days', 'MiniMakerFaire'); ?></a>
                        </li>
                        <li ng-repeat="(schedDay,schedule) in schedules"> 
                           <a ng-click="setDateFilter(schedDay)">{{schedDay| date: "EEEE"}}</a>
                        </li>
                     </ul>
                  </span>
               </div>-->
            </div>
         </div>
        
         <div class="sched-table">
            <div class="row sched-header">
               <div class="sched-col-1"></div>               
               <div class="sched-body">
                  <div ng-repeat="schedule in schedules | filter : schedSearch | orderBy: ['time_start','time_end']">                     
                        <div class="row sched-row">
                           <div class="sched-col-1">
                              <a href="/maker/entry/{{schedule.id}}">
                                 <div class="sched-img" style="background-image: url({{schedule.thumb_img_url}});"></div>
                              </a>
                           </div>

                           <div class="sched-flex-row">
                              <div class="sched-col-2">
                                 <h3>
                                    <a href="/maker/entry/{{schedule.id}}">{{schedule.name}}</a>
                                 </h3>
                                 <p class="sched-description">{{schedule.maker_list}}</p>
                              </div>

                              <div class="sched-col-3">
                                 <div class="row">
                                    <div class="col-xs-3 col-sm-12">
                                       {{schedule.time_start| date: "EEEE"}}
                                    </div>
                                    <div class="col-xs-9 col-sm-12">
                                       {{schedule.time_start| date: "shortTime"}} -
                                       <span class="lineBr"><br/></span>
                                       {{schedule.time_end| date: "shortTime"}}
                                    </div>
                                 </div>
                              </div>

                              <div class="sched-col-4">{{schedule.nicename}}</div>

                              <?php /*
                                <div class="sched-col-5 sched-type">
                                <img ng-if="schedule.type == 'Demo'" src="<?php echo get_bloginfo('template_directory'); ?>/img/Demo-icon.svg" alt="Maker Exhibit Demo Topic Icon" class="img-responsive" />
                                <img ng-if="schedule.type == 'Talk'" src="<?php echo get_bloginfo('template_directory'); ?>/img/Talk-icon.svg" alt="Maker Exhibit Talk Topic Icon" class="img-responsive" />
                                <img ng-if="schedule.type == 'Workshop'" src="<?php echo get_bloginfo('template_directory'); ?>/img/Workshop-icon.svg" alt="Maker Exhibit Workshop Topic Icon" class="img-responsive" />
                                <img ng-if="schedule.type == 'Performance'" src="<?php echo get_bloginfo('template_directory'); ?>/img/Performance-icon.svg" alt="Maker Exhibit Performance Topic Icon" class="img-responsive" />
                                </div>

                                <div class="sched-col-6">
                                <div class="overflow-ellipsis-text">
                                <span data-ng-repeat="catName in schedule.category">{{catName}}<font ng-show="!$last">, </font></span>
                                </div>
                                </div> */ ?>

                              <div class="col-xs-10 col-xs-offset-2 sched-more-info">
                                 <div class="panel-heading">
                                    <span ng-click="schedule.isCollapsed = !schedule.isCollapsed" ng-init="schedule.isCollapsed = true"><?php _e('quick view', 'MiniMakerFaire'); ?>
                                       <i class="fa fa-lg" ng-class="{'fa-angle-down': schedule.isCollapsed, 'fa-angle-up': !schedule.isCollapsed}"></i>
                                    </span>
                                 </div>
                                 <div collapse="schedule.isCollapsed">
                                    <div ng-show="!schedule.isCollapsed" class="panel-body">
                                       <p ng-bind-html="schedule.desc"></p>
                                       <a href="/maker/entry/{{schedule.id}}" target="none"><?php _e('full details', 'MiniMakerFaire'); ?></a>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php
} else { //display what is in content
   ?>
   <div class="clear"></div>
   <div class="post-thumbnail">
      <?php the_post_thumbnail(); ?>
   </div><!-- .post-thumbnail -->
   <div class="container">
      <div class="row">
         <div class="content col-md-12">
            <?php
            if (have_posts()) {
               while (have_posts()) {
                  the_post();
                  ?>
                  <article <?php post_class(); ?>>
                  <?php the_content(); ?>
                  </article>
                  <?php }
               ?>
               <ul class="pager">
                  <li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
                  <li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>
               </ul>

               <?php } else {
               ?>
               <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
               <?php }
            ?>
         </div><!--Content-->
      </div>
   </div><!--Container-->
   <?php
}
?>
<?php get_footer(); ?>