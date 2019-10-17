<?php
/**
 * Template Name: Maker Faire Map
 *
 */

get_header(); 

?>

   <div class="container directory-container" id="directory">

      <div class="row map-header">
         <div class="col-md-12">
            <h1>Maker Faire Map</h1>
            <div class="admin-buttons">
               <a class="btn btn-blue" href="/register">Add yours <i class="fa fa-plus"></i></a>
					<a class="btn btn-blue" href="/edit-your-makerfaire">Manage <i class="fa fa-pencil-square-o"></i></a>
            </div>
            <p><?php //echo the_content(); ?></p>
         </div>
      </div>
      <div class="message-container">
         <div class="loading-indicator" ref="loadingIndicator">Loading... <i class="fa fa-spinner"></i></div>
         <div class="error-indicator hidden text-danger" ref="errorIndicator">Sorry! We couldn't load the map... please try again later. <i class="fa fa-exclamation-triangle"></i></div>
      </div>
      <div class="map-table-hidden" ref="mapTableWrapper" >

         <div class="row">
            <div class="col-md-12">
               <div id="map" ref="map" style="height: 40px;"></div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-12">

               <div class="map-filters-wrp">
                  <form action="" class="" @submit="filterOverride">
                     <div class="">
                        <label for="filter">Find a Maker Faire</label>
                        <input class="form-control input-sm" type="search" id="filter" name="filter" ref="filterField" v-model="filterVal" @input="doFilter" placeholder="Search by Name">
                     </div>
                  </form>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-12">
               <v-client-table :data="tableData" :columns="columns" :options="options" @row-click="onRowClick" ref="directoryGrid">
                  <span slot="mmap_eventname" slot-scope="props">
                     <a :href="props.row.mmap_url" target="_blank" title="Visit site in new window">{{ props.row.mmap_eventname }}</a>
                  </span>
               </v-client-table>
            </div>
         </div>

      </div>  <!-- end map-table-wrapper -->

   </div>

<div class="container-fluid light-blue">
   <div class="container">
      <div class="row">
         <div class="col-md-6 col-sm-6 col-xs-12 makerfaire-bottom-nav">
            <h4>Join our global network of Maker Faires</h4>
            <a class="btn btn-blue" href="/register">Add your Maker Faire</a>
         </div>
         <div class="col-md-6 col-sm-6 col-xs-12 makerfaire-bottom-nav">
            <h4>See an error or need to update your info?</h4>
            <a class="btn btn-blue" href="/edit-your-makerfaire">Manage your listing</a>					
         </div>
      </div>
   </div>
</div>  



<?php get_footer(); ?>
