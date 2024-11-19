<?php
/*
 * Template name: Ribbons
 */
get_header();
$yearSql = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 and post_id=0 order by year desc");
$firstYear = $yearSql[0]->year;

foreach ($yearSql as $year) {
  if ($year->year == $firstYear) {
    $yearJSON = '{"id" : "' . $year->year . '", "name": "' . $year->year . '"}';
  } else {
    $yearJSON .= ',{"id" : "' . $year->year . '", "name": "' . $year->year . '"}';
  }
}

?>

<div class="clear"></div>
<div id="ribbonPage">
  <?php
  // Output the featured image.
  if (has_post_thumbnail()) :
  ?>
    <div id="brHeaderImg"
      style="background-image: url('<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full'); ?>');">
      <div>Maker Faire Ribbon Winners</div>
    </div>
  <?php endif; ?>

  <div class="mtm ng-cloak" ng-app="ribbonApp">
    <div class="row">
      <div class="content col-xs-12">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
              <?php the_content(); ?>
            </article>
          <?php endwhile; ?>
          
        <?php else: ?>
          <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
        <?php endif; ?>
        <!-- start blue ribbon data -->
        <div>
          <div ng-controller="ribbonController" class="my-controller mtm-results container-fluid" ng-init='loadData(<?php echo $firstYear; ?>, [<?php echo $yearJSON; ?>])'>
            <!-- Filters-->
            <div class="mtm-filter-wrap" ng-cloak>
              <!-- Text Search -->
              <div class="search-wrapper">
                <input ng-model="query.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Search...", 'makerfaire') ?>" type="text">
              </div>

              <!-- Faire Year Filter -->
              <div class="dropdown form-control" ng-init="faireYear = '<?php echo $firstYear; ?>'">
                <button class="btn btn-link dropdown-toggle" type="button" id="year-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  <span ng-if="query.faireYear != ''">{{query.faireYear}}</span>
                  <span ng-if="query.faireYear == ''">Faire Year</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="year-dropdownMenu">
                  <li>
                    <a class="pointer-on-hover" ng-click="query.faireYear = ''"><?php _e("All", 'makerfaire') ?></a>
                  </li>

                  <li ng-repeat="year in years | orderBy: 'year'">
                    <a class="pointer-on-hover" ng-click="query.faireYear = year.id;loadData(year.id)">{{year.name}}</a>
                  </li>
                </ul>
              </div>

              <!-- Faire Location Filter -->
              <div class="dropdown form-control" ng-if="faires.length >   1">
                <button class="btn btn-link dropdown-toggle" type="button" id="location-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  <span ng-if="query.location != ''">{{query.location}}</span>
                  <span ng-if="query.location == ''">All Faires</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="location-dropdownMenu">
                  <li>
                    <a class="pointer-on-hover" ng-click="query.location = ''"><?php _e("All", 'makerfaire') ?></a>
                  </li>

                  <li ng-repeat="faire in faires | orderBy: 'faire'">
                    <a class="pointer-on-hover" ng-click="query.location = faire">{{faire}}</a>
                  </li>
                </ul>
              </div>

              <!-- Ribbon Type Filter -->
              <div class="dropdown form-control" ng-if="hasBlue && hasRed">
                <button class="btn btn-link dropdown-toggle" type="button" id="ribbonType-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  <span ng-if="query.ribbonType != ''">{{query.ribbonType}}</span>
                  <span ng-if="query.ribbonType == ''">All Ribbons</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="ribbonType-dropdownMenu">
                  <li>
                    <a class="pointer-on-hover" ng-click="query.ribbonType = ''"><?php _e("All", 'makerfaire') ?></a>
                  </li>

                  <li><a class="pointer-on-hover" ng-click="query.ribbonType = blue">Blue</a></li>
                  <li><a class="pointer-on-hover" ng-click="query.ribbonType = red">Red</a></li>
                </ul>
              </div>

              <div class="mtm-filter-view">
                <a ng-class="{active: layout == 'list'}" ng-click="changeView('list')" class="mtm-filter-l pointer-on-hover box list" title="List View"><i class="fas fa-bars" aria-hidden="true"></i></a>
                <a ng-class="{active: layout == 'grid'}" ng-click="changeView('grid')" class="mtm-filter-g pointer-on-hover box gallery" title="Grid View"><i class="far fa-grid-2" aria-hidden="true"></i></a>
              </div>
            </div>

            <!-- Default view and no ribbon data found-->
            <div ng-show="!ribbons.length" class="card-deck loading">
                <div class="ng-scope card">
                    <div class="card-header" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div> 
                    <div class="card-body"> <div class="card-text"> <h3 class="card-title text-center"> <a href="#" class="no-link">Loading Project...</a></h3> <h4> <a href="#" class="no-link">Loading Faire</a></h4> <div class="card-detail-items"> <div class="card-detail-item"> <span> <a href="#" class="no-link"> <i class="fa fa-circle-user"></i> </a> </span> <p> <a href="#" class="no-link">Loading Maker</a> </p> </div> </div> </div> </div>
                    <div class="card-footer"> <a href="#" class="read-more-link no-link">More</a> </div>
                </div>
                <div class="ng-scope card">
                    <div class="card-header" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div> 
                    <div class="card-body"> <div class="card-text"> <h3 class="card-title text-center"> <a href="#" class="no-link">Loading Project...</a></h3> <h4> <a href="#" class="no-link">Loading Faire</a></h4> <div class="card-detail-items"> <div class="card-detail-item"> <span> <a href="#" class="no-link"> <i class="fa fa-circle-user"></i> </a> </span> <p> <a href="#" class="no-link">Loading Maker</a> </p> </div> </div> </div> </div>
                    <div class="card-footer"> <a href="#" class="read-more-link no-link">More</a> </div>
                </div>
                <div class="ng-scope card">
                    <div class="card-header" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div> 
                    <div class="card-body"> <div class="card-text"> <h3 class="card-title text-center"> <a href="#" class="no-link">Loading Project...</a></h3> <h4> <a href="#" class="no-link">Loading Faire</a></h4> <div class="card-detail-items"> <div class="card-detail-item"> <span> <a href="#" class="no-link"> <i class="fa fa-circle-user"></i> </a> </span> <p> <a href="#" class="no-link">Loading Maker</a> </p> </div> </div> </div> </div>
                    <div class="card-footer"> <a href="#" class="read-more-link no-link">More</a> </div>
                </div>
                <div class="ng-scope card">
                    <div class="card-header" style="background-image:url(https://makerfaire.com/wp-content/themes/makerfaire/images/stripe_bg1.gif);"></div> 
                    <div class="card-body"> <div class="card-text"> <h3 class="card-title text-center"> <a href="#" class="no-link">Loading Project...</a></h3> <h4> <a href="#" class="no-link">Loading Faire</a></h4> <div class="card-detail-items"> <div class="card-detail-item"> <span> <a href="#" class="no-link"> <i class="fa fa-circle-user"></i> </a> </span> <p> <a href="#" class="no-link">Loading Maker</a> </p> </div> </div> </div> </div>
                    <div class="card-footer"> <a href="#" class="read-more-link no-link">More</a> </div>
                </div>              
                <div class="loading-container">
                    <img src="https://make.co/wp-content/universal-assets/v2/images/makey-spinner.gif" />
                    <span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
                </div>
            </div>
            <div class="no-results" ng-if="ribbons.length && (ribbons|filter:query).length == 0">I'm sorry. There are no winners found.</div>

            <!-- Grid View -->
            <div ng-if="layout == 'grid'" class="mtm-results-cont card-deck">
              <div class="card" dir-paginate="ribbon in ribbons| filter:query | itemsPerPage: 100" current-page="currentPage">
                <div class="card-header">
                    <a href="{{ribbon.link}}">
                      <img src="{{ribbon.project_photo}}" on-error="/wp-content/themes/makerfaire/images/default-mtm-image.jpg" alt="{{maker.name}} Photo" class="card-image" />
                    </a>
                </div>
                <div class="ribbons">
                  <div class="blueRibbon" ng-if="ribbon.blueCount > 0">
                    {{ribbon.blueCount}}
                  </div>
                  <div class="redRibbon" ng-if="ribbon.redCount > 0">
                    {{ribbon.redCount}}
                  </div>
                </div>
                <div class="card-body">
                  <div class="card-text">
                    <h3 class="card-title text-center"> <a href="{{ribbon.link}}">{{ribbon.project_name}}</a></h3>
                    <h4> <a href="{{ribbon.link}}">{{ribbon.location}} {{ribbon.faireYear}}</a></h4>
                    <div class="card-detail-items">
                      <div class="card-detail-item" ng-show="ribbon.maker_name.length">
                        <span>
                          <a href="{{ribbon.link}}">
                            <i class="fa fa-circle-user"></i>
                          </a>
                        </span>
                        <p>
                          <a href="{{ribbon.link}}">{{ribbon.maker_name}}</a>
                        </p>
                      </div>                     
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <a href="{{ribbon.link}}" class="read-more-link">More</a>
                </div>
              </div>
              <div class="clearfix"></div>
            </div>

            <!-- List View -->
            <div ng-if="layout == 'list'" class="mtm-results-cont-list card-deck">
              <div class="card" dir-paginate="ribbon in ribbons| filter:query |orderBy: 'project_name' |itemsPerPage: 100" current-page="currentPage">              
                  <div class="card-header">
                    <a href="{{ribbon.link}}">
                      <img src="{{ribbon.project_photo}}" on-error="/wp-content/themes/makerfaire/images/default-mtm-image.jpg" alt="{{ribbon.project_name}} Photo" class="card-image" />
                    </a>
                    <div class="ribbons">
                      <div class="blueRibbon" ng-if="ribbon.blueCount > 0">
                        {{ribbon.blueCount}}
                      </div>
                      <div class="redRibbon" ng-if="ribbon.redCount > 0">
                        {{ribbon.redCount}}
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="card-text">
                      <a href="{{ribbon.link}}">
                        <h3 class="card-title">{{ribbon.project_name}}</h3>
                        <h4>{{ribbon.maker_name}}</h4>
                        <!--<p class="description">{{maker.description}}</p>-->
                      </a>
                      <div class="card-detail-items">
                        <div class="card-detail-item">
                          <span>
                            <a href="{{ribbon.link}}">
                              <i class="fa fa-plus"></i>
                            </a>
                          </span>
                          <p>
                            <a href="{{ribbon.link}}">More</a>
                          </p>
                        </div>                      
                      </div>
                    </div>
                  </div>
              </div>
              <div class="clearfix"></div>
            </div>

            <div class="text-center">
              <dir-pagination-controls boundary-links="true" template-url="<?php echo get_stylesheet_directory_uri(); ?>/partials/dirPagination.tpl.html">
              </dir-pagination-controls>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="container-fluid">
    </div>
    <!--Content-->
  </div>
</div>
</div>
<!--Container-->

<?php get_footer(); ?>