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
            <faires-map-filter default-state="true" filter="Featured Faires">Flagship Faires</faires-map-filter>
            <faires-map-filter default-state="true" filter="All Maker Faires">Featured Faires</faires-map-filter>
            <faires-map-filter default-state="true" filter="2013/14 Mini Maker Faire applications">Mini Maker Faires</faires-map-filter>
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
            <tr>
              <th ng-click="sort='name'">Name</th>
              <th ng-click="sort='category'">Category</th>
              <th ng-click="sort='description'">Description</th>
              <th></th>
              <th></th>
            </tr>
            <tr dir-paginate="row in $ctrl.faireMarkers.rows | filter:q | orderBy:sort | itemsPerPage: 10">
              <td width="20%">{{row.name}}</td>
              <td width="20%">{{row.category}}</td>
              <td width="20%">{{row.description}}</td>
              <td width="20%"><button class="btn-sm btn-default pull-right">Call for Makers</button></td>
              <td width="20%"><button class="btn-sm btn-danger pull-right">Buy Tickets</button></td>
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
