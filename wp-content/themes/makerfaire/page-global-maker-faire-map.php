<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>

<style>
  #faire-global-map {
    height: 400px;
  }
</style>

<div class="container">
  <div class="col-md-12">
    <h1>Faires around the world</h1>
    <div id="faire-global-map"></div>
  </div>
</div>
<?php get_footer(); ?>
<script src="/wp-content/themes/makerfaire/pages/map/map.js"></script>
<!-- 
<script src="https://maps.googleapis.com/maps/api/js??v=3.exp&key=AIzaSyBITa21JMkxsELmGoDKQ3owasOW48113w4&callback=initMap" async defer></script> 
-->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&callback=FairesGlobalMap.initMap" async defer></script>
