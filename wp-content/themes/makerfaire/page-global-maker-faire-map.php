<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>
<div class="container faire-global-map-wrp" ng-app="faireMapsApp">
  <div class="col-md-12" ng-controller="MapCtrl as map">
    <h1>Faires around the world</h1>
    <input type="text"
      class="form-control input-sm"
      placeholder="Filter result"
      ng-model="filters" />
    <faires-map-filter default-state="true" filter="Featured Faires"></faires-map-filter>
    <faires-map-filter default-state="true" filter="All Maker Faires"></faires-map-filter>
    <faires-map-filter default-state="true" filter="2013/14 Mini Maker Faire applications"></faires-map-filter>
    <div faires-google-map
      id="faire-global-map"
      map-data="map.faireMarkers"
      ng-if="map.faireMarkers">
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
<?php get_footer(); ?>
