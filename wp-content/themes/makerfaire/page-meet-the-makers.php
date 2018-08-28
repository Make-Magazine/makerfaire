<?php
/*
 * Template name: Meet the Makers New
 */
get_header();

$noMakerText = get_field('no_makers_found_text');
if ($noMakerText == '')
   $noMakerText = 'No makers found';
?>

<div class="mtm" ng-app="mtm">
   <div ng-controller="mtmMakers"  ng-cloak="">
      <input type="hidden" id="forms2use" value="<?php echo get_field('faire-forms'); ?>" />
      <input type="hidden" id="mtm-faire" value="<?php echo get_field('faire'); ?>" />
      <input type="hidden" id="noMakerText" value="<?php echo $noMakerText; ?>" />
      <div class="container">
         <h1 class="page-title text-center"><?php echo get_the_title(); ?></h1>
      </div>

      <div class="flag-banner"></div>

      <div class="mtm-search">
         <form class="form-inline container">
            <label for="mtm-search-input"><?php _e("Search by category, keyword, project, sponsor or maker name", 'makerfaire') ?></label><br/>
            <input ng-model="makerSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Enter your search", 'makerfaire') ?>" type="text">        
         </form>
      </div>


      <div class="mtm-filter container">
         <div class="row">
            <div class="col-md-4">
               <div class="mtm-filter-view">
                  <span class="mtm-view-by"><?php _e("View by:", 'makerfaire') ?></span>
                  <a ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'" class="mtm-filter-g pointer-on-hover box gallery"><i class="fa fa-picture-o" aria-hidden="true"></i> <?php _e("GALLERY", 'makerfaire') ?></a>
                  <span class="mtm-pipe">|</span>
                  <a ng-class="{active: layout == 'list'}" ng-click="layout = 'list'" class="mtm-filter-l pointer-on-hover box list" ><i class="fa fa-th-list" aria-hidden="true"></i> <?php _e("LIST", 'makerfaire') ?></a>
               </div>
            </div>
            <div class="col-md-4">
               <ul class="nav nav-pills">
                  <li class="nav-item">
                     <button ng-class="{active: makerSearch.flag == 'Featured Maker'}" type="button" ng-click="makerSearch.flag = 'Featured Maker'" class="btn btn-default">Featured</button>
                  </li>
                  <li class="nav-item">
                     <button  ng-class="{active: makerSearch.flag == ''}" type="button" ng-click="makerSearch.flag = ''" class="btn btn-default">All</button>
                  </li>   
               </ul>            
            </div>
            <div class="col-md-4">
               <div class="row">
                  <div class="col-md-12 col-md-6">
                     <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                           <span ng-show="makerSearch.categories != ''">{{makerSearch.categories}}</span>
                           <span ng-show="makerSearch.categories == ''">All Topics</span>
                           <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>

                        <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
                           <li>
                              <a class="pointer-on-hover" ng-click="makerSearch.categories = ''"><?php _e("All", 'makerfaire') ?></a>
                           </li>
                           <li ng-repeat="tag in tags| orderBy: tag">                     
                              <a class="pointer-on-hover" ng-click="makerSearch.categories = tag">{{ tag}}</a>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-md-12 col-md-6">                     
                     <div class="dropdown" ng-if="locations.length > 0">
                        <button class="btn btn-link dropdown-toggle" type="button" id="location-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">                       
                           <span ng-show="makerSearch.location != ''">{{makerSearch.location}}</span>
                           <span ng-show="makerSearch.location == ''">All Locations</span>
                           <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>

                        <ul class="dropdown-menu" aria-labelledby="location-dropdownMenu">
                           <li>
                              <a class="pointer-on-hover" ng-click="makerSearch.location = ''"><?php _e("All", 'makerfaire') ?></a>
                           </li>                  
                           <li ng-repeat="location in locations| orderBy: location">                     
                              <a class="pointer-on-hover" ng-click="makerSearch.location = location">{{ location}}</a>
                           </li>
                        </ul>                              
                     </div>
                  </div>
               </div>
            </div>
         </div>     
      </div>

      <div class="mtm-results" mtm-scroll="">
         <div ng-show="!makers.length" class="container loading">
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
            <span class="sr-only"><?php _e("Loading", 'makerfaire') ?>...</span>
         </div>
         <!-- Grid View -->
         <div ng-show="layout == 'grid'" class="mtm-results-cont">
            limit is: {{limit}}
            <div ng-repeat="maker in makers| filter : makerSearch | byCategory:category | limitTo: limit">
               <a target="none" href="/maker/entry/{{maker.id}}">
                  <article class="mtm-maker" style="background-image: url('{{ maker.large_img_url}}')">
                     <h3>{{ maker.name}}</h3>
                  </article>
               </a>
            </div>
            <div class="clearfix"></div>
         </div>

         <!-- List View -->
         <div ng-show="layout == 'list'" class="mtm-results-cont container">
            <div class="filter-alpha-wrapper">
               <span class="filterAlpha" ng-repeat="searchLetter in alphabet.split('') track by $index">
                  <a href=""  target="none" class="pointer-on-hover" ng-click="setLetter(searchLetter)">{{ searchLetter}}</a>
               </span>
               <span class="filterAlpha" ><a href=""  class="pointer-on-hover" ng-click="setLetter('')">Reset</a></span>
            </div>
            <div ng-repeat="maker in makers| filter : makerSearch | byCategory:category | orderBy: 'name' | startsWithLetter:letter">
               <a href="/maker/entry/{{maker.id}}">
                  <article class="mtm-maker" style="background-image: url('{{ maker.large_img_url}}')">
                     <h3>{{ maker.name}}</h3>
                     <h6 style="font-weight: lighter;padding-left: 21px;">{{maker.makerList}}</h6>
                  </article>
               </a>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>



</div>

<script>
   jQuery(document).ready(function () {
      var slider = jQuery('#carouselImgs');
      // Carousel left right
      jQuery("#right-trigger").click(function () {
         slider.trigger('next.owl.carousel');
      });
      jQuery("#left-trigger").click(function () {
         slider.trigger('prev.owl.carousel');
      });
   });
</script>

<?php get_footer(); ?>
