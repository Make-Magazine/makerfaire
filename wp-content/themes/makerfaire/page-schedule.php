<?php
/*
 * Template name: Schedule
 */
get_header();

//look for day of week or type filter variables
$sched_dow = (isset($wp_query->query_vars['sched_dow']) ? ucfirst(urldecode($wp_query->query_vars['sched_dow'])) : 'All Days');
$sched_type = (isset($wp_query->query_vars['sched_type']) ? ucfirst(urldecode($wp_query->query_vars['sched_type'])) : 'All Types');

$schedule_ids = get_field('schedule_ids');

$displayNav = get_field('display_left_nav');          
if($displayNav){
?>
<div class="page-leftnav">
	<div class="row">
		<div class="left-hand-nav col-md-3">
			<?php
					$template_to_display = get_field('template_to_display');               
					wp_nav_menu( array( 'theme_location' => $template_to_display ) );
			?>
		</div>
		<div class="content col-md-9">
<?php } ?>


<?php 
if (have_posts()) {
   ?>
   <div class="schedule-header container">
      
       <h1 class="page-title"><?php echo get_the_title(); ?></h1>
		
   </div><?php
}
if ($schedule_ids && $schedule_ids != '') { //display the new schedule page
   //create_calendar(get_field('schedule_ids'));
   ?>
   <input type="hidden" id="forms2use" value="<?php echo get_field('schedule_ids'); ?>" />
   <input type="hidden" id="schedType" value="<?php echo $sched_type; ?>" />
   <input type="hidden" id="schedDOW"  value="<?php echo $sched_dow; ?>" />

   <div id="page-schedule" class="schedule-table <?php if($displayNav){ ?>left-nav-active<?php } ?>" ng-controller="scheduleCtrl" ng-app="scheduleApp" ng-cloak="">
      <div class="schedule-wrapper">
         <!--<a href="/wp-content/themes/makerfaire/FaireSchedule.ics">Download iCal</a>-->
         <div ng-cloak>
				<div class="mtm-search">
					<div class="search-wrapper">
                  <form class="form-inline">
                     <label for="mtm-search-input"><?php _e("Search by topic, keyword, project, sponsor or presenter name", 'makerfaire') ?></label><br/>
                     <input ng-model="schedSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Enter your search", 'makerfaire') ?>" type="text">        
                  </form>
					</div>
					<div class="filter-wrapper">
						<div class="schedule-filters" ng-if="showSchedules">
							<div class="sched-col-4">
								<div class="dropdown">
									<button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										<span ng-show="schedSearch.category != ''">{{schedSearch.category}}</span>
										<span ng-show="schedSearch.category == ''">All Topics</span>
										<i class="fa fa-chevron-down" aria-hidden="true"></i>
									</button>

									<ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
										<li>
											<a class="pointer-on-hover" ng-click="schedSearch.category = ''"><?php _e("All Topics", 'makerfaire') ?></a>
										</li>
										<li ng-repeat="tag in tags| orderBy: tag">                     
											<a class="pointer-on-hover" ng-click="schedSearch.category = tag">{{ tag}}</a>
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

										<li ng-repeat="schedule in schedules | filter:schedSearch | dateFilter: filterdow |  orderBy: 'type' | unique: 'type'">
											<a class="pointer-on-hover" ng-click="schedSearch.type = schedule.type">{{ schedule.type}}</a>
										</li>
									</ul>
								</div>                  
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
											<a class="pointer-on-hover" ng-click="schedSearch.nicename = ''"><?php _e("All Stages", 'makerfaire') ?></a>
										</li>
										<li ng-repeat="stage in stages">                     
											<a class="pointer-on-hover" ng-click="schedSearch.nicename = stage">{{stage}}</a>
										</li>
										<li ng-repeat="schedule in schedules | filter:schedSearch | dateFilter: filterdow |  orderBy: 'stageOrder' | unique: 'nicename'">
											<a ng-click="schedSearch.nicename = schedule.nicename">{{schedule.nicename}}</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="sched-col-4">
								<div class="dropdown">
									<button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">                        
										<span ng-show="filterdow != ''">{{filterdow}}</span>
										<span ng-show="filterdow == ''">All Days</span>
										<i class="fa fa-chevron-down" aria-hidden="true"></i>
									</button>

									<ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
										<li>
											<a ng-click="setDateFilter('')" class="pointer-on-hover"><?php _e("All Days", 'makerfaire') ?></a>
										</li>

										<li ng-repeat="dayOfWeek in dates">
											<a class="pointer-on-hover" ng-click="setDateFilter(dayOfWeek)">{{dayOfWeek}}</a>
										</li>
									</ul>
								</div>                               
							</div>
							<div class="sched-col-4">Filter by:</div>
					   </div>
					</div>
            </div>
           
         </div>
			

			<div ng-show="!schedules.length" class="container loading">
				<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
				<span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
			</div>
         <div class="sched-table"  sched-scroll="loadMore()">     
				
            <div class="row sched-header">
               <div class="sched-col-1"></div>               
               <div class="sched-body">
                  <div ng-repeat="schedule in schedules | filter : schedSearch | dateFilter: filterdow | orderBy: ['time_start','time_end'] | limitTo: limit">                     
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
										
										<div class="sched-registration" ng-show="schedule.flags === 'Registration Required'">
											<hr />
											<div ng-mouseover="schedule.isCollapsed = false" ng-mouseleave="schedule.isCollapsed = true">Requires Add-On ticket</div>
										</div>
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
               </div><!-- .sched-body -->
            </div>
         </div>

      </div>
   </div>
<!--LeftNav Containers-->
<?php           
	if($displayNav){
?>
  </div>
 </div>
</div>
<?php } ?>

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