<?php
get_header();
$yearSql = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");
$firstYear = $yearSql[0]->year;

foreach($yearSql as $year) {
  if($year->year == $firstYear) {
      $yearJSON = '{"id" : "'.$year->year.'", "name": "'.$year->year.'"}';
  } else {
      $yearJSON .= ',{"id" : "'.$year->year.'", "name": "'.$year->year.'"}';
  }
}

?>

<div class="clear"></div>
<div id="ribbonPage">
  <?php
  // Output the featured image.
  if ( has_post_thumbnail() ) :
  ?>
    <div id="brHeaderImg"
      style="background-image: url('<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full'); ?>');">
      <div>Maker Faire Ribbon Winners</div>
    </div>
  <?php endif; ?>

  <div class="container ng-cloak" ng-app="ribbonApp">
    <div class="row">
      <div class="content col-xs-12">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
          <article <?php post_class(); ?>>
            <?php the_content(); ?>
          </article>
        <?php endwhile; ?>
        <ul class="pager">
          <li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
          <li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>
        </ul>
        <?php else: ?>
          <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
        <?php endif; ?>
        <!-- start blue ribbon data -->
        <div>
          <div ng-controller="ribbonController" class="my-controller" ng-init='loadData(<?php echo $firstYear;?>, [<?php echo $yearJSON;?>])'>
            <div class="ribbonFilter">
              <div class="pull-left">
                <div ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'" class="box gallery"><i class="fa fa-picture-o"></i>Gallery</div>
                <div ng-class="{active: layout == 'list'}" ng-click="layout = 'list'" class="box list"><i class="fa fa-list"></i>List</div>
              </div>
              <div class="ribbonHeader pull-right">
                <select ng-model="faireYear" ng-init="faireYear = '<?php echo $firstYear;?>'" ng-change="loadData(faireYear)">
                  <option ng-repeat="year in years" value="{{year.id}}">{{year.name}}</option>
                </select>
                <select ng-model="query.location">
                  <option value="" selected>All Faires</option>
                  <option ng-repeat="faire in faires" value="{{faire}}">{{faire}}</option>
                </select>
                <select ng-model="query.ribbonType">
                  <option value="" selected>All Ribbons</option>
                  <option value="blue">Blue</option>
                  <option value="red">Red</option>
                </select>
                <div class="textSearch pull-right">
                  <input ng-model="query.$" placeholder="Filter Winners">
                </div>
              </div>
            </div>
            <br/>
            <br/>
            <p ng-show="(ribbons | filter:query).length == 0" class="noData">I'm sorry. There are no winners found.</p>
            <!--| filter:year -->
            <div ng-show="layout == 'grid'" class="ribbonGrid row">
              <div class="ribbData col-xs-12 col-sm-4 col-md-3" dir-paginate="ribbon in ribbons| filter:query |itemsPerPage: 40" current-page="currentPage">
                <a href="{{ribbon.link}}" target="_blank">
                  <div class="projImg">
                    <img class="img-responsive" fallback-src="/wp-content/themes/makerfaire/images/grey-makey.png" ng-src="{{ribbon.project_photo != '' && ribbon.project_photo || '/wp-content/themes/makerfaire/images/grey-makey.png'}}" />
                    <div class="ribbons">
                      <div class="blueRibbon" ng-if="ribbon.blueCount > 0">
                        {{ribbon.blueCount}}
                      </div>
                      <div class="redRibbon" ng-if="ribbon.redCount > 0">
                        {{ribbon.redCount}}
                      </div>
                    </div>
                  </div>
                </a>
                <div class="makerData">
                  <div class="projName">
                    {{ribbon.project_name}}
                  </div>{{ribbon.maker_name}}
                  <br>
                  <br>
                  <span class="bluedata" ng-if="ribbon.blueCount > 0">
                    {{ribbon.location}} {{ribbon.faireYear}}
                  </span>
                  <span class="reddata" ng-if="ribbon.redCount > 0">
                    {{ribbon.location}} {{ribbon.faireYear}}
                  </span>
                </div>
              </div>
              <div class="text-center">
                <dir-pagination-controls boundary-links="true" template-url="<?php echo get_stylesheet_directory_uri();?>/partials/dirPagination.tpl.html">
                </dir-pagination-controls>
              </div>
            </div>
            <div ng-show="layout == 'list'" class="ribbonList">
              <div ng-repeat="ribbon in blueRibbons  | filter:query | groupBy: 'blueCount' | toArray: true | orderBy: -blueCount">
                <div ng-if="ribbon.$key > 0">
                  <div class="ribbonTitle">
                    <div class="blueMakey"></div>
                    <div>{{ribbon.$key}} Blue Ribbons</div>
                  </div>
                  <ul>
                    <li ng-repeat="bRibbonData in ribbon | orderBy: 'project_name'">
                      <a href="{{bRibbonData.link}}" target="_blank">{{ bRibbonData.project_name }}</a>
                    </li>
                  </ul>

                <div class="clear"></div>
                </div>
              </div>
              <div class="clear"></div>
              <div ng-repeat="rribbon in redRibbons | filter:query | groupBy: 'redCount' | toArray: true | orderBy: -redCount">
                <div ng-if="rribbon.$key > 0">
                  <div class="ribbonTitle">
                    <div class="redMakey"></div>
                    <div>{{rribbon.$key}} Red Ribbons</div>
                  </div>
                  <ul>
                    <li ng-repeat="rRibbonData in rribbon | orderBy: 'project_name'">
                      <a href="{{rRibbonData.link}}" target="_blank">{{ rRibbonData.project_name }}</a>
                    </li>
                  </ul>

                  <div class="clear"></div>
                </div>
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
