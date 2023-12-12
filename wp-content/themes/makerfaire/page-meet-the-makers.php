<?php
/*
 * Template name: Meet the Makers New
 */
get_header();

//Pull ACF data
$faire_forms = get_field('faire-forms');
$faire_forms_trimmed = preg_replace('/\s+/', '', $faire_forms);

$noMakerText = get_field('no_makers_found_text');
if ($noMakerText == '')
    $noMakerText = 'No makers found';

//Pull from Make: Projects?
$showMakeProjects = get_field('show_make_projects');
$MPCategory = get_field('make:_projects_category_to_pull_from');

if (($showMakeProjects === 'mponly' || $showMakeProjects === 'mfandmp') && $MPCategory == '') {
    echo 'Category cannot be blank';
}
?>

<div class="mtm" ng-app="mtm">
    <div ng-controller="mtmMakers">
        <input type="hidden" id="forms2use" value="<?php echo $faire_forms_trimmed; ?>" />
        <input type="hidden" id="mtm-faire" value="<?php echo get_field('faire'); ?>" />
        <input type="hidden" id="noMakerText" value="<?php echo $noMakerText; ?>" />
        <input type="hidden" id="showMakeProjects" value="<?php echo $showMakeProjects; ?>" />
        <input type="hidden" id="MPCategory" value="<?php echo $MPCategory; ?>" />

        <div class="container">
            <div class="col-md-3 col-sm-12 col-xs-12">
                <?php
                echo get_faire_backlink();
                ?>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
                <h1 class="page-title text-center"><?php echo get_the_title(); ?></h1>
            </div>
            <div class="col-md-3 col-sm-12">
            </div>
        </div>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="schedule-description">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <div class="flag-banner"></div>

        <div class="mtm-search">
            <form class="form-inline">
                <label for="mtm-search-input"><?php _e("Search by topic, keyword, project, sponsor or maker name", 'makerfaire') ?></label><br/>
                <input ng-model="makerSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Enter your search", 'makerfaire') ?>" type="text">
            </form>
        </div>


        <div class="mtm-filter container">
            <div class="row" ng-cloak>
                <div class="col-sm-4">
                    <div class="mtm-filter-view">
                        <span class="mtm-view-by"><?php _e("View by:", 'makerfaire') ?></span>
                        <a ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'" class="mtm-filter-g pointer-on-hover box gallery"><i class="fa fa-picture-o" aria-hidden="true"></i> <?php _e("GALLERY", 'makerfaire') ?></a>
                        <span class="mtm-pipe">|</span>
                        <a ng-class="{active: layout == 'list'}" ng-click="layout = 'list'" class="mtm-filter-l pointer-on-hover box list" ><i class="fa fa-th-list" aria-hidden="true"></i> <?php _e("LIST", 'makerfaire') ?></a>
                    </div>
                </div>
                <div class="col-sm-4 mid-section">
                    <div class="faux-checkbox">
                        <label>Featured Makers</label>
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button ng-class="{'ng-hide':showFeatured == 'Featured Maker'}" type="button" ng-click="makerSearch.flag = 'Featured Maker';showFeatured = 'Featured Maker';" class="btn btn-default" ng-hide="showFeatured">&nbsp;</button>
                            </li>
                            <li class="nav-item">
                                <button ng-init="showFeatured = makerSearch.flag" ng-class="{'ng-hide':showFeatured == ''}" type="button" ng-click="makerSearch.flag = '';showFeatured = '';" class="btn btn-default"><i class="fa fa-check"></i></button>
                            </li>
                        </ul>
                    </div>
                    <div class="faux-checkbox" style='display:none'>
                        <label>Hands-On Activities</label>
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <button ng-class="{'ng-hide':showHandsOn == 'Featured HandsOn'}" type="button" ng-click="makerSearch.handson = 'Featured HandsOn';showHandsOn = 'Featured HandsOn';" class="btn btn-default">&nbsp;</button>
                            </li>
                            <li class="nav-item">
                                <button ng-init="showHandsOn = makerSearch.handson" ng-class="{'ng-hide':showHandsOn == ''}" type="button" ng-click="makerSearch.handson = '';showHandsOn = '';" class="btn btn-default"><i class="fa fa-check"></i></button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4 filter-section">
                    <!-- Weekend Filter -->
                    <div class="dropdown" ng-if="weekends.length > 0">
                        <button class="btn btn-link dropdown-toggle" type="button" id="weekend-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span ng-if="makerSearch.weekend != ''">{{makerSearch.weekend}}</span>
                            <span ng-if="makerSearch.weekend == ''">Weekends Exhibiting</span>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="weekend-dropdownMenu">
                            <li>
                                <a class="pointer-on-hover" ng-click="makerSearch.weekend = ''"><?php _e("Any", 'makerfaire') ?></a>
                            </li>

                            <li ng-repeat="weekend in weekends| orderBy: 'weekend'">
                                <a class="pointer-on-hover" ng-click="makerSearch.weekend = weekend">{{weekend}}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Area Filter -->
                    <div class="dropdown" ng-if="locations.length > 0">
                        <button class="btn btn-link dropdown-toggle" type="button" id="location-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span ng-if="makerSearch.location != ''">{{makerSearch.location}}</span>
                            <span ng-if="makerSearch.location == ''">All <?php echo (get_field('faire') == 'VMF2020' ? 'Tracks' : 'Locations'); ?></span>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="location-dropdownMenu">
                            <li>
                                <a class="pointer-on-hover" ng-click="makerSearch.location = ''"><?php _e("All", 'makerfaire') ?></a>
                            </li>

                            <li ng-repeat="location in locations| orderBy: 'location'">
                                <a class="pointer-on-hover" ng-click="makerSearch.location = location">{{location}}</a>
                            </li>
                        </ul>
                    </div>

                    <!--Category filter-->
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span ng-if="makerSearch.categories != ''">{{makerSearch.categories}}</span>
                            <span ng-if="makerSearch.categories == ''">All Topics</span>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>

                        <ul>
                            <li ng-repeat="maker in makers| filter:{categories: {id}}">
                                {{ maker.categories}}
                            </li>

                        </ul>
                        <ul class="dropdown-menu topic-menu" aria-labelledby="mtm-dropdownMenu">
                            <li>
                                <a class="pointer-on-hover" ng-click="makerSearch.categories = ''"><?php _e("All", 'makerfaire') ?></a>
                            </li>
                            <li ng-repeat="tag in tags| orderBy: tag">
                                <a class="pointer-on-hover" ng-click="makerSearch.categories = tag">{{tag}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mtm-results" mtm-scroll="loadMore()">
            <div ng-show="!makers.length" class="mtm-results-cont loading">
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="ng-scope"><a href="javascript:void();" style="pointer-events:none;"><article class="mtm-maker"><h3></h3><div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div><div class="bottom-line">&nbsp;</div><div class="read-more-btn universal-btn"></div></article></a></div>
                <div class="loading-container">
                    <img src="https://make.co/wp-content/universal-assets/v2/images/makey-spinner.gif" />
                    <span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
                </div>
            </div>
            <!-- Grid View -->
            <div ng-if="layout == 'grid'" class="mtm-results-cont">
                <div ng-repeat="maker in makers| filter : makerSearch | limitTo: limit">
                    <a href="{{maker.link}}" target="_blank">
                        <article class="mtm-maker">
                            <h3>{{maker.name}}</h3>
                            <div class="mtm-image" style="background-image:url({{maker.large_img_url}});"></div>
                            <div class="bottom-line">
                                <div class="mtm-cat">{{maker.category_id_refs[0]}}</div>
                                <span ng-bind-html="trust(maker.makerList)"></span>
                            </div>
                            <div class="read-more-btn universal-btn">Read More</div>
                        </article>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <!-- List View -->
            <div ng-if="layout == 'list'" class="mtm-results-cont-list container">
                <div class="filter-alpha-wrapper">
                    <span class="filterAlpha" ng-repeat="searchLetter in alphabet.split('') track by $index">
                        <a href="" target="none" class="pointer-on-hover" ng-click="setLetter(searchLetter)">{{ searchLetter}}</a>
                    </span>
                    <span class="filterAlpha"><a href=""  class="pointer-on-hover" ng-click="setLetter('')">Reset</a></span>
                </div>

                <div ng-repeat="maker in makers| filter : makerSearch | orderBy: 'name' | startsWithLetter:letter">
                    <a href="{{maker.link}}" target="_blank">
                        <article class="mtm-maker">
                            <h3>{{maker.name}}</h3>
                            <h4 ng-bind-html="trust(maker.makerList)"></h4>
                            <span>
                                {{maker.category_id_refs.join(', ')}}
                            </span>
                        </article>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="load-trigger"></div>
</div>

<?php get_footer(); ?>
