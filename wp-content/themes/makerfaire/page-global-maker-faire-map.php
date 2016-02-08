<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>

<style>
  #faire-global-map {
    height: 400px;
  }
</style>

<div class="container" ng-app="faireMapsApp">
  <div class="col-md-12" ng-controller="MapCtrl">
    <h1>Faires around the world</h1>
    <div faires-google-map id="faire-global-map"></div>
    <faires-map-filter filter="Featured Faires"></faires-map-filter>
    <faires-map-filter filter="All Maker Faires"></faires-map-filter>
    <faires-map-filter filter="2013/14 Mini Maker Faire applications"></faires-map-filter>
  </div>
</div>
<?php get_footer(); ?>
<script src="https://code.angularjs.org/1.4.8/angular.js"></script>
<script src="/wp-content/themes/makerfaire/pages/map/map.js"></script>
