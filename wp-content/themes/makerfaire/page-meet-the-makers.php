<?php
/*
 * Template name: Meet the Makers
 */
get_header();

//Pull ACF data
$faire_forms = get_field('faire-forms');
$faire_forms_trimmed = preg_replace('/\s+/', '', $faire_forms);

$noMakerText = get_field('no_makers_found_text');
if ($noMakerText == '')
    $noMakerText = 'No makers found';

//faire name
$faire     = get_field('faire');
$results   = $wpdb->get_results('SELECT * FROM wp_mf_faire where faire= "'.strtoupper($faire).'"');
$faireName = (isset($results[0]->faire_name)?$results[0]->faire_name:'');
?>

<div class="row">

    <?php    
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            $the_content = the_content();        
            if (!empty($the_content)) {
                ?>
                <div class="content-wrapper">
                    <?php echo $the_content; ?>
                </div>
                <?php
            } else {
                ?>  
                <div class="content col-sm-12 col-xs-12">
                    <h1 class="page-title text-center">Meet the Makers - <?php echo $faireName; ?></h1>
                </div>
                <?php
            }
        }
    } 
?>
</div>
  
<div class="mtm" ng-app="mtm">
    <div ng-controller="mtmMakers">
        <input type="hidden" id="forms2use" value="<?php echo $faire_forms_trimmed; ?>" />
        <input type="hidden" id="mtm-faire" value="<?php echo $faire; ?>" />
        <input type="hidden" id="noMakerText" value="<?php echo $noMakerText; ?>" />

        <div class="mtm-filter-wrap" ng-cloak>
            <div class="search-wrapper">
                <input ng-model="makerSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Search...", 'makerfaire') ?>" type="text">
            </div>

            <!-- Weekend Filter -->
            <div class="dropdown form-control" ng-if="weekends.length > 0">
                <button class="btn btn-link dropdown-toggle" type="button" id="weekend-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span ng-if="makerSearch.weekend != ''">{{makerSearch.weekend}}</span>
                    <span ng-if="makerSearch.weekend == ''">Weekends Exhibiting</span>
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
            <div class="dropdown form-control" ng-if="locations.length > 0">
                <button class="btn btn-link dropdown-toggle" type="button" id="location-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span ng-if="makerSearch.location != ''">{{makerSearch.location}}</span>
                    <span ng-if="makerSearch.location == ''">All <?php echo (get_field('faire') == 'VMF2020' ? 'Tracks' : 'Locations'); ?></span>
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
            <div class="dropdown form-control">
                <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span ng-if="makerSearch.categories != ''">{{makerSearch.categories}}</span>
                    <span ng-if="makerSearch.categories == ''">All Categories</span>
                </button>

                <ul>
                    <li ng-repeat="maker in makers| filter:{categories: {id}}">
                        {{maker.categories}}
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

            <!--Type filter-->
            <div class="dropdown form-control" ng-if="types.length > 0">
                <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span ng-if="makerSearch.types != ''">{{makerSearch.types}}</span>
                    <span ng-if="makerSearch.types == ''">All Types</span>
                </button>

                <ul class="dropdown-menu topic-menu" aria-labelledby="mtm-dropdownMenu">
                    <li>
                        <a class="pointer-on-hover" ng-click="makerSearch.types = ''"><?php _e("All", 'makerfaire') ?></a>
                    </li>
                    <li ng-repeat="type in types| orderBy: type">
                        <a class="pointer-on-hover" ng-click="makerSearch.types = type">{{type}}</a>
                    </li>
                </ul>
            </div>

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

            <div class="mtm-filter-view">
                <a ng-class="{active: layout == 'list'}" ng-click="layout = 'list'" class="mtm-filter-l pointer-on-hover box list"><i class="fas fa-bars" aria-hidden="true"></i></a>
                <a ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'" class="mtm-filter-g pointer-on-hover box gallery"><i class="far fa-grid-2" aria-hidden="true"></i></a>
            </div>

        </div>

        <div class="mtm-results container-fluid" mtm-scroll="loadMore()">
            <div ng-show="!makers.length" class="mtm-results-cont loading">
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="ng-scope mf-card"><a href="javascript:void();" style="pointer-events:none;">
                        <article class="mtm-maker">
                            <div class="mtm-image" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div>
                            <div class="mtm-text">
                                <h3>Loading...</h3>
                                <div class="mtm-detail-items">
                                    <div class="mtm-detail-item"><span><i class="fa fa-user-robot"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-rocket"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                    <div class="mtm-detail-item"><span><i class="fa fa-tent-double-peak"></i></span>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                                <div class="read-more-link">More</div>
                            </div>
                        </article>
                        </article>
                    </a></div>
                <div class="loading-container">
                    <img src="https://make.co/wp-content/universal-assets/v2/images/makey-spinner.gif" />
                    <span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
                </div>
            </div>
            <div class="no-results" ng-if="(makers|filter:makerSearch).length == 0">No Results for your search and filter terms</div>
            <!-- Grid View -->
            <div ng-if="layout == 'grid'" class="mtm-results-cont">
                <div class="mf-card" ng-repeat="maker in makers| filter : makerSearch | limitTo: limit">

                    <article class="mtm-maker">
                        <div class="mtm-image">
                            <a href="{{maker.link}}" target="_blank">
                                <img src="{{maker.large_img_url}}" alt="{{maker.name}} Photo" />
                            </a>
                        </div>
                        <div class="mtm-text">
                            <h3> <a href="{{maker.link}}" target="_blank">{{maker.name}}</a></h3>
                            <div class="mtm-detail-items">
                                <div class="mtm-detail-item" ng-show="maker.makerList.length">
                                    <span>
                                        <a href="{{maker.link}}" target="_blank">
                                            <i class="fa fa-user-robot"></i></a>
                                    </span>
                                    <p>
                                        <a href="{{maker.link}}" ng-bind-html="trust(maker.makerList)" target="_blank"></a>
                                    </p>

                                </div>
                                <div class="mtm-detail-item">
                                    <span ng-bind-html="maker.main_cat_icon"></span>
                                    <p>
                                        <a href="?category={{maker.category_id_refs[0]}}">{{maker.category_id_refs[0]}}</a>
                                    </p>
                                </div>
                                <div class="mtm-detail-item">
                                    <span>
                                        <a href="?type={{maker.typeString}}">
                                            <i class="fa {{maker.types[0].toLowerCase()}}"></i>
                                        </a>
                                    </span>
                                    <p>
                                        <a href="?type={{maker.typeString}}">{{maker.typeString}}</a>
                                    </p>
                                </div>
                            </div>
                            <div class="read-more-link"><a href="{{maker.link}}" target="_blank">More</a></div>
                        </div>
                    </article>

                </div>
                <div class="clearfix"></div>
            </div>

            <!-- List View -->
            <div ng-if="layout == 'list'" class="mtm-results-cont-list container">
                <div class="filter-alpha-wrapper">
                    <span class="filterAlpha" ng-repeat="searchLetter in alphabet.split('') track by $index">
                        <a href="" target="none" class="pointer-on-hover" ng-click="setLetter(searchLetter)">{{ searchLetter}}</a>
                    </span>
                    <span class="filterAlpha"><a href="" class="pointer-on-hover" ng-click="setLetter('')">Reset</a></span>
                </div>

                <div ng-repeat="maker in makers| filter : makerSearch | orderBy: 'name' | startsWithLetter:letter">

                    <article class="mtm-maker">
                        <div class="mtm-image">
                            <a href="{{maker.link}}" target="_blank">
                                <img src="{{maker.large_img_url}}" alt="{{maker.name}} Photo" />
                            </a>
                        </div>
                        <div class="mtm-text">
                            <a href="{{maker.link}}" target="_blank">
                                <h3>{{maker.name}}</h3>
                                <h4 ng-bind-html="trust(maker.makerList)"></h4>
                                <p class="description">{{maker.description}}</p>
                            </a>
                            <div class="mtm-detail-items">
                                <div class="mtm-detail-item">
                                    <span>
                                        <a href="{{maker.link}}" target="_blank">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </span>
                                    <p>
                                        <a href="{{maker.link}}" target="_blank">More</a>
                                    </p>
                                </div>
                                <div class="mtm-detail-item">
                                    <span ng-bind-html="maker.main_cat_icon"></span>
                                    <p>
                                        <a href="?category={{maker.category_id_refs[0]}}">
                                            {{maker.category_id_refs[0]}}
                                        </a>
                                    </p>
                                </div>
                                <div class="mtm-detail-item">
                                    <span>
                                        <a href="?type={{maker.typeString}}">
                                            <i class="fa {{maker.types[0].toLowerCase()}}"></i>
                                        </a>
                                    </span>
                                    <p>
                                        <a href="?type={{maker.typeString}}">
                                            {{maker.typeString}}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>

                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="load-trigger"></div>
</div>

<?php get_footer(); ?>