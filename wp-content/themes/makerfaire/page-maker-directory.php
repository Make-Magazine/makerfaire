<?php
/*
 * Template name: Maker Directory
 */
get_header();

//Pull ACF data
$faire_years = get_field('faire-years');
$faire_years_trimmed = preg_replace('/\s+/', '', $faire_years);

$faire_forms = get_field('faire-forms');
$faire_forms_trimmed = preg_replace('/\s+/', '', $faire_forms);

$noMakerText = get_field('no_makers_found_text');
if ($noMakerText == '')
    $noMakerText = 'No makers found';

?>

<div class="mtm" ng-app="makerdir">
    <div ng-controller="mdirMakers">        
        <input type="hidden" id="years2use"   value="<?php echo $faire_years_trimmed; ?>" />        
        <input type="hidden" id="noMakerText" value="<?php echo $noMakerText; ?>" />        

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

                </div>
                <div ng-show="makers.length !==0" class="col-sm-4 filter-section">
                    <!-- Faire Filter -->
                    <div class="dropdown" ng-if="faire_names.length > 0">
                        <button class="btn btn-link dropdown-toggle" type="button" id="faire_name-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span ng-if="makerSearch.faire_name != ''">{{makerSearch.faire_name}}</span>
                            <span ng-if="makerSearch.faire_name == ''">Faires</span>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="faire_name-dropdownMenu">
                            <li>
                                <a class="pointer-on-hover" ng-click="makerSearch.faire_name = ''"><?php _e("Any", 'makerfaire') ?></a>
                            </li>

                            <li ng-repeat="faire_name in faire_names| orderBy: 'faire_name'">
                                <a class="pointer-on-hover" ng-click="makerSearch.faire_name = faire_name">{{faire_name}}</a>
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

        <div class="mtm-results" makerdir-scroll="loadMore()">
            <!-- Default View Prior to data load-->
            <div ng-show="!makers.length" class="mtm-results-cont loading">                
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
                <div ng-repeat="maker in makers| filter : makerSearch | limitTo: limit" style="height:600px;">                
                    <a href="{{maker.link}}" target="_blank">
                        <article class="mtm-maker">
                            <h4>{{maker.name}}</h5>
                            <p>{{maker.faire_name}} - {{maker.faire_year}}</p>
                            <div class="mtm-image" style="background-image:url({{maker.large_img_url}});"></div>
                            <p>{{maker.description}}</p>
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
