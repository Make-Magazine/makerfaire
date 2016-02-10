<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>
<div class="faire-global-map-wrp" ng-app="faireMapsApp">
  <div ng-controller="MapCtrl as map">
    <div class="container">
      <div class="col-md-12">
        <h1>Faires around the world</h1>
      </div>
    </div>
    <div class="map-filters-wrp">
      <div class="container">
        <div class="col-md-12">
          <h2>Explore Maker Faires</h2>
          <!-- <label> -->
            <input type="text"
              class="form-control input-sm"
              placeholder="Location, name or type"
              ng-model="filters" />
          <!-- </label> -->
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
        <div class="loading-spinner" ng-if="!map.faireMarkers">
          <i class="fa fa-circle-o-notch fa-spin"></i>
        </div>
        <div faires-google-map
          id="faire-global-map"
          map-data="map.faireMarkers"
          ng-if="map.faireMarkers">
        </div>
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
        <div tasty-table
          bind-resource="map.faireMarkers"
          ng-if="map.faireMarkers"
          bind-filters="filters"
          class="faire-list-table">
          <table class="table table-striped table-condensed">
            <thead tasty-thead></thead>
            <tbody>
              <tr ng-repeat="row in rows">
                <td width="33%">{{row.name}}</td>
                <td width="33%">{{row.category}}</td>
                <td width="33%">{{row.description}}</td>
              </tr>
            </tbody>
          </table>
        <div tasty-pagination></div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>
