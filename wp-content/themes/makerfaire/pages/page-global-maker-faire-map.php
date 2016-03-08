<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>
<div class="faire-global-map-wrp" ng-app="faireMapsApp">
  <div ng-controller="MapCtrl as $ctrl">
    <div class="container">
      <div class="col-md-12">
        <h1>Faires around the world</h1>
      </div>
    </div>
    <div class="map-filters-wrp">
      <div class="container">
        <div class="col-md-12">
          <h2>Explore Maker Faires</h2>
          <input type="text"
            class="form-control input-sm"
            placeholder="Location, name or type"
            ng-model="$ctrl.filterText"
            ng-model-options="{debounce: 500}"
            ng-change="$ctrl.toggleMapSearch()" />
          <div class="filters">
            <faires-map-filter default-state="true" filter="Flagship">Flagship Faires</faires-map-filter>
            <faires-map-filter default-state="true" filter="Featured">Featured Faires</faires-map-filter>
            <faires-map-filter default-state="true" filter="Mini">Mini Maker Faires</faires-map-filter>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="col-md-12">
        <div class="loading-spinner" ng-if="!$ctrl.faireMarkers">
          <i class="fa fa-circle-o-notch fa-spin"></i>
        </div>
        <!-- Map Angular Component -->
        <faires-google-map
          id="faire-global-map"
          map-data="$ctrl.faireMarkers"
          ng-if="$ctrl.faireMarkers">
        </faires-google-map>
        <!-- Color Key -->
        <div class="faire-key-boxes">
          <div class="flagship-key">
            <i class="fa fa-map-marker"></i>
            Flagship Maker Faires
          </div>
          <div class="featured-key">
            <i class="fa fa-map-marker"></i>
            Featured Maker Faires
          </div>
          <div class="mini-key">
            <i class="fa fa-map-marker"></i>
            Mini Maker Faires
          </div>
        </div>
        <!-- List of Faires -->
        <div class="faire-list-table">
          <table class="table table-striped table-condensed">
            <tr></tr>
            <tr>
              <th class="cursor-pointer" ng-click="sort='annual';reverse=!reverse">ANNUAL</th>
              <th class="cursor-pointer" ng-click="sort='category';reverse=!reverse">FAIRE TYPE</th>
              <th class="cursor-pointer" ng-click="sort='date';reverse=!reverse">DATE</th>
              <th class="cursor-pointer" ng-click="sort='name';reverse=!reverse">EVENT NAME</th>
              <th>LOCATION</th>
            </tr>
            <tr dir-paginate="(index, row) in $ctrl.faireMarkers.rows | filter:q | orderBy:sort:reverse | itemsPerPage: 10">
              <td>{{row.faire_year | ordinal}}</td>
              <td>{{row.category}}</td>
              <td>{{row.event_start_dt | date}}</td>
              <td>{{row.name}}</td>
              <td>
                {{row.venue_address_city}}{{
                  row.venue_address_state && ', '+row.venue_address_state || ''
                }}{{row.venue_address_country && ', '+row.venue_address_country || ''}}
              </td>
            </tr>
          </table>
          <div class="text-center">
            <dir-pagination-controls
              boundary-links="true"
              template-url="/wp-content/themes/makerfaire/bower_components/angularUtils-pagination/dirPagination.tpl.html">
            </dir-pagination-controls>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
