<?php
/**
 * Template Name: Maker Faire Map
 *
 */

get_header(); 

?>

   <div class="container-fluid directory-container" id="directory">

      <div class="row map-header">
         <div class="col-md-12 map-header-inner">
            <h1>Maker Faires Around the World</h1>
            <!--<div class="admin-buttons">
               <a class="btn btn-blue" href="/register">Add yours <i class="fa fa-plus"></i></a>
					<a class="btn btn-blue" href="/edit-your-makerfaire">Manage <i class="fa fa-pencil-square-o"></i></a>
            </div>-->
            <ul id="type-filters">Select by Type:
					<li v-for="type in types">
					  <label v-bind:for="type.name" data-toggle="tooltip" v-bind:title="type.description" data-placement="bottom">
					  <input type="checkbox" v-on:click="typeFilter" v-bind:name="type.name" v-bind:value="type.name" v-bind:id="type.name" checked  />
						  <i class="fa fa-fw fa-circle-o unchecked"></i>
    					  <i class="fa fa-fw fa-circle checked"></i>
						  <span>{{type.name}}</span>
					  </label>
					</li>
				</ul>
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
							<label for="filter">Explore Faires</label>
							<input class="form-control input-sm" type="search" id="filter" name="filter" ref="filterField" v-model="filterVal" @input="searchFilter" placeholder="Search by Name, Date or Location">
							
                  </form>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-md-12">
               <v-client-table :data="filteredData" :columns="columns" :options="options" @row-click="onRowClick" ref="directoryGrid">
                  <span slot="faire_name" slot-scope="props">
                     <a :href="props.row.faire_url" target="_blank" title="Visit site in new window">{{ props.row.faire_name }}</a>
                  </span>
						<span slot="event_start_dt" slot-scope="props">
                     {{ props.row.event_dt }}
                  </span>
               </v-client-table>
            </div>
         </div>
<div id="past-faires-btn">
								<label><input class="form-control input-sm" type="checkbox" id="pastFaires" name="pastFaires" ref="filterField" v-model="pastFaires" @input="psFilter"><span>{{buttonMessage}}</span></label>
							</div>
      </div>  <!-- end map-table-wrapper -->

   </div>

<!--<div class="container-fluid light-blue">
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
</div>  -->



<?php get_footer(); ?>