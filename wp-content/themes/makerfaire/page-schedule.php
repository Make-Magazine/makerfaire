<?php
/*
 * Template name: Schedule
 */
get_header();

//look for day of week or type filter variables
$sched_type = (isset($wp_query->query_vars['sched_type']) ? ucfirst(urldecode($wp_query->query_vars['sched_type'])) : 'All Types');

$schedule_ids = get_field('schedule_ids');
$schedule_ids_trimmed = preg_replace('/\s+/', '', $schedule_ids);

$faireType = get_field('faire_type');

$displayNav = get_field('display_left_nav');

if ($displayNav) {
    ?>
    <div class="page-leftnav">
        <div class="row">
            <div class="left-hand-nav col-md-3">
                <?php
                $template_to_display = get_field('template_to_display');
                wp_nav_menu(array('theme_location' => $template_to_display));
                ?>
            </div>
            <div class="content col-md-9">
                <?php
            }


//set faire
            $post_data = get_post($post->post_parent);
            $parent_slug = $post_data->post_name;

            $sql = 'select * from wp_mf_faire where url_path="' . $parent_slug . '";';
            $faireData = $wpdb->get_row($sql);
            $faire = (isset($faireData->faire)?$faireData->faire:'');
            $timeZone = (isset($faireData->time_zone)?$faireData->time_zone:'');
            ?>
            <script type="text/javascript">
                printScheduleEvent = function () {
                    var dataObject = {
                        'event': 'printSchedule',
                    };
                    dataLayer.push(dataObject);
                };

            </script>

            <?php
            if ($schedule_ids_trimmed && $schedule_ids_trimmed != '') { //display the new schedule page
                ?>
                <div id="page-schedule" class="page-content schedule-table  ng-cloak <?php if ($displayNav) { ?>left-nav-active<?php } ?>" ng-controller="scheduleCtrl" ng-app="scheduleApp" ng-cloak="">
                    <input type="hidden" id="schedType" value="<?php echo $sched_type; ?>" />
                    <input type="hidden" id="faire"     value="<?php echo $faire; ?>" />
                    <input type="hidden" id="faire_st"  value="<?php echo $faireData->start_dt; ?>" />
                    <input type="hidden" id="faire_end" value="<?php echo $faireData->end_dt; ?>" />
                    <input type="hidden" id="faire_tz"  value="<?php echo $timeZone; ?>" />

                    <div class="schedule-wrapper">
                        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                                <div class="schedule-header container-fluid">
                                    <h1 class="page-title"><span ng-show="schedSearch.type != ''">{{schedSearch.type}} </span><?php echo get_the_title(); ?><span ng-show="schedSearch.category != ''"> for {{schedSearch.category}}</span><span ng-show="schedSearch.nicename != ''"> on &lsquo;{{schedSearch.nicename}}&rsquo;</span></h1>
                                </div>
                                <div class="schedule-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <div>
                            <div class="mtm-search">
                                <div class="search-wrapper">
                                    <form class="form-inline">
                                        <label for="mtm-search-input"><?php _e("Search by topic, keyword, project, sponsor or presenter name", 'makerfaire') ?></label>
                                        <input ng-model="schedSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Enter your search", 'makerfaire') ?>" type="text">
                                    </form>
                                </div>
                                <div class="filter-wrapper">
                                    <div class="schedule-filters" ng-show="showSchedules">
                                        <div class="sched-col-4">Filter by:</div>
                                        <div class="sched-col-4">
                                            <div class="dropdown">
                                                <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <span ng-show="schedSearch.category != ''">{{schedSearch.category}}</span>
                                                    <span ng-show="schedSearch.category == ''">All Topics</span>
                                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
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
                                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                                </button>

                                                <ul class="dropdown-menu type" aria-labelledby="mtm-dropdownMenu">
                                                    <li>
                                                        <a class="pointer-on-hover" ng-click="schedSearch.type = ''"><?php _e("All Types", 'makerfaire') ?></a>
                                                    </li>

                                                    <li ng-repeat="schedule in schedules| filter:schedSearch | dateFilter: filterdow |  orderBy: 'type' | unique: 'type'">
                                                        <a class="pointer-on-hover" ng-click="schedSearch.type = schedule.type;">{{schedule.type}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="sched-col-4">
                                            <div class="dropdown">
                                                <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <span ng-show="schedSearch.nicename != ''">{{schedSearch.nicename}}</span>
                                                    <span ng-show="schedSearch.nicename == ''">All <?php echo (get_field('faire_type') == 'VMF' ? 'Tracks' : 'Stages'); ?></span>

                                                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                                </button>

                                                <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                                                    <li>
                                                        <a class="pointer-on-hover" ng-click="schedSearch.nicename = ''">All <?php echo (get_field('faire_type') == 'VMF' ? 'Tracks' : 'Stages'); ?></a>
                                                    </li>
                                                    <li ng-repeat="stage in stages">
                                                        <a class="pointer-on-hover" ng-click="schedSearch.nicename = stage">{{stage}}</a>
                                                    </li>
                                                    <li ng-repeat="schedule in schedules| filter:schedSearch | dateFilter: filterdow |  orderBy: 'stageOrder' | unique: 'nicename'">
                                                        <a ng-click="schedSearch.nicename = schedule.nicename">{{schedule.nicename}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php if ($faire == "VMF2020") { ?>
                                            <div class="sched-col-4">
                                                <div class="dropdown">

                                                    <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                        <span ng-show="schedSearch.region != ''">{{schedSearch.region}}</span>
                                                        <span ng-show="schedSearch.region == null || schedSearch.region == ''">All Regions</span>
                                                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                                    </button>

                                                    <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                                                        <li>
                                                            <a class="pointer-on-hover" ng-click="schedSearch.region = ''">All Regions</a>
                                                        </li>
                                                        <li ng-repeat="stage in stages">
                                                            <a class="pointer-on-hover" ng-click="schedSearch.region = ''"><?php _e("All Regions", 'makerfaire') ?></a>
                                                        </li>
                                                        <li ng-repeat="schedule in schedules| filter:schedSearch | dateFilter: filterdow |  orderBy: 'region' | unique: 'region'">
                                                            <a ng-click="schedSearch.region = schedule.region">{{schedule.region}}</a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        <?php } if ($faire != "VMF2020") {
                                            ?>

                                            <div class="sched-col-4">
                                                <div class="dropdown">
                                                    <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                        <span ng-show="filterdow != ''">{{filterdow}}</span>
                                                        <span ng-show="filterdow == null || filterdow == ''">All Days</span>
                                                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
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
                                        <?php } ?>
                                        <div class="sched-col-4">
                                            <div class="faux-checkbox">
                                                <label>Featured</label>
                                                <ul class="nav nav-pills">
                                                    <li class="nav-item">
                                                        <button ng-class="{'ng-hide':showFeatured == 'Featured'}" type="button" ng-click="schedSearch.featured = 'Featured';showFeatured = 'Featured';" class="btn btn-default">&nbsp;</button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button ng-init="showFeatured = schedSearch.featured" ng-class="{'ng-hide':showFeatured == ''}" type="button" ng-click="schedSearch.featured = '';showFeatured = '';" class="btn btn-default"><i class="fas fa-check"></i></button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php if ($faireType == "VMF") { ?>
                                            <div class="sched-col-4">
                                                <div class='timezone-wrapper'>
                                                    <span class="timezone-label">Select Timezone:</span> <?php echo select_Timezone($timeZone); ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="calendar-wrapper">
                                    <form class="calendar" method="post" action="/wp-content/themes/makerfaire/download-ics.php">
                                        <input type="hidden" name="forms2use" id="forms2use" value="<?php echo $schedule_ids_trimmed; ?>" />
                                        <input type="hidden" name="filter_type"     value="{{schedSearch.type}}">
                                        <input type="hidden" name="filter_topic"    value="{{schedSearch.category}}">
                                        <input type="hidden" name="filter_stage"    value="{{schedSearch.nicename}}">
                                        <input type="hidden" name="filter_text"     value="{{schedSearch.$}}">
                                        <input type="hidden" name="parent_slug"     value="<?php echo $parent_slug; ?>">

                                        <span class="fa-stack fa-sm">
                                            <i class="fas fa-circle fa-stack-2x"></i>
                                            <i class="far fa-calendar-alt fa-stack-1x fa-inverse"></i>
                                        </span>
                                        <input type="submit" value="Download Filtered Calendar">
                                    </form>

                                    <a class="calendar" style="cursor:pointer;" onclick="window.frames['printSchedule'].focus();window.frames['printSchedule'].print();printScheduleEvent();event.preventDefault();">
                                        <span class="fa-stack fa-sm">
                                            <i class="fas fa-circle fa-stack-2x"></i>
                                            <i class="fas fa-print fa-stack-1x fa-inverse"></i>
                                        </span>
                                        Print filtered schedule
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div ng-show="!schedules.length" class="container loading">
                            <i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i>
                            <br/><b style="font-size: 25px;padding-top: 15px;display: block;">Please Wait While We Load Your Virtually Maker Faire Experience</b>
                            <span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
                        </div>

                        <div class="sched-table" sched-scroll="loadMore()">
                            <div class="row sched-header">
                                <div class="sched-col-1"></div>
                                <div class="sched-body">
                                    <!-- if we are in the faire time, only display events that haven't occurred yet inFaire = {{inFaire}} {{todaysDate | date:'yyyy-MM-ddTHH:mm:ss'}} -->
                                    <div ng-repeat="schedule in schedules| filter : schedSearch | dateFilter: filterdow | orderBy: ['time_start', 'time_end'] | limitTo: limit">

                                        <div class="row sched-row">
                                            <div class="sched-col-1">
                                                <a href="/maker/entry/{{schedule.id}}">
                                                    <div class="sched-img" style="background:url({{schedule.thumb_img_url}});"></div>
                                                </a>
                                            </div>

                                            <div class="sched-flex-row">
                                                <div class="sched-col-2">
                                                    <h3>
                                                        <a href="/maker/entry/{{schedule.id}}">{{schedule.name}}</a>
                                                    </h3>
                                                    <p class="sched-description" ng-bind-html="trust(schedule.maker_list)"></p>
                                                    <p class="stage-track stage-{{schedule.stageOrder}}">{{schedule.nicename}}</p>
                                                </div>

                                                <div class="sched-col-3">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-sm-12 dispDay">
                                                            {{schedule.time_start| date: "EEEE"}}
                                                        </div>
                                                        <div class="col-xs-9 col-sm-12">
                                                            <span class="dispStartTime">{{schedule.time_start| date: "shortTime"}}</span> -
                                                            <span class="lineBr"><br/></span>
                                                            <span class="dispEndTime">{{schedule.time_end| date: "shortTime"}}</span>
                                                            <div class="start_dt hidden">{{schedule.time_start| date: 'MMM dd, yyyy HH:mm:ss'}}</div>
                                                            <div class="end_dt hidden">{{schedule.time_end| date: 'MMM dd, yyyy HH:mm:ss'}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sched-col-4">
                                                    <h4 class="featured" ng-show="schedule.featured != NULL && schedule.featured == 'Featured'">Featured</h4>
                                                    <div class="sched-registration" ng-show="schedule.registration != NULL && schedule.registration != ''">
                                                        <a class="btn universal-btn" href="{{schedule.registration}}" target="_blank">Register Here</a>
                                                    </div>
                                                    <div class="sched-viewNow" ng-show="schedule.view_now != NULL && schedule.view_now != ''">
                                                        <a class="btn universal-btn" href="{{schedule.view_now}}" target="_blank">Watch Live</a>
                                                    </div>
                                                </div>

                                                <div class="col-xs-10 col-xs-offset-2 sched-more-info">
                                                    <div class="panel-heading">
                                                        <span ng-click="schedule.isCollapsed = !schedule.isCollapsed" ng-init="schedule.isCollapsed = true"><?php _e('quick view', 'MiniMakerFaire'); ?>
                                                            <i class="fas fa-lg" ng-class="{'fa-angle-down': schedule.isCollapsed, 'fa-angle-up': !schedule.isCollapsed}"></i>
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
                                    <div class="no-results" ng-show="schedules.length">
                                        There is nothing else scheduled for today. Be sure to check back tomorrow!
                                    </div>
                                </div><!-- .sched-body -->
                                <div class="load-trigger"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--LeftNav Containers-->
                <?php
                if ($displayNav) {
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

<iframe src="/stage-schedule/?faire=<?php echo $faire; ?>&orderBy=time&qr=true" style="display:none;" id="printSchedule" name="printSchedule"></iframe>

<?php get_footer(); ?>
