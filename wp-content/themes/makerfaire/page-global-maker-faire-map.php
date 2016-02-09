<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>
<div class="container faire-global-map-wrp" ng-app="faireMapsApp">
  <div class="col-md-12" ng-controller="MapCtrl">
    <h1>Faires around the world</h1>
    <div faires-google-map id="faire-global-map"></div>
    <faires-map-filter filter="Featured Faires"></faires-map-filter>
    <faires-map-filter filter="All Maker Faires"></faires-map-filter>
    <faires-map-filter filter="2013/14 Mini Maker Faire applications"></faires-map-filter>
  </div>
</div>
<?php get_footer(); ?>
