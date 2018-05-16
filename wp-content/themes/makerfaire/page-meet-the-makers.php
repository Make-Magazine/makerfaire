<?php
/*
* Template name: Meet the Makers New
*/
get_header();

$noMakerText = get_field('no_makers_found_text');
if($noMakerText =='') $noMakerText = 'No makers found';
?>

<div class="mtm" ng-app="mtm">
  <div ng-controller="mtmMakers"  ng-cloak="">
    <input type="hidden" id="forms2use" value="<?php echo get_field('faire-forms'); ?>" />
    <input type="hidden" id="noMakerText" value="<?php echo $noMakerText; ?>" />
    <div class="container">
      <h1 class="text-center"><?php echo get_the_title(); ?></h1>
    </div>
    <div class="mtm-carousel-cont">
      <div id="carouselImgs" class="mtm-carousel owl-carousel"></div>

      <a id="left-trigger" class="left carousel-control" href="#" role="button" data-slide="prev">
        <img class="glyphicon-chevron-right" src="<?php echo get_bloginfo('template_directory');?>/img/arrow_left.png" alt="Image Carousel button left" />
        <span class="sr-only"><?php _e("Previous",'makerfaire')?></span>
      </a>
      <a id="right-trigger" class="right carousel-control" href="#" role="button" data-slide="next">
        <img class="glyphicon-chevron-right" src="<?php echo get_bloginfo('template_directory');?>/img/arrow_right.png" alt="Image Carousel button right" />
        <span class="sr-only"><?php _e("Next",'makerfaire')?></span>
      </a>
    </div>
    <!--//end old-->
    <div class="container">
      <h2 class="text-center"><?php _e("Explore our Maker Exhibits!",'makerfaire')?></h2>
    </div>
    <div class="flag-banner"></div>

    <div class="mtm-search">
      <form class="form-inline">
        <label for="mtm-search-input"><?php _e("Search:",'makerfaire')?></label>
        <input ng-model="makerSearch.$" id="mtm-search-input" class="form-control" placeholder="<?php _e("Looking for a specific Exhibit or Maker?",'makerfaire')?>" type="text">
        <!--input class="form-control btn-w-ghost" value="GO" type="submit"-->
      </form>
    </div>


    <div class="mtm-filter container">
      <div class="mtm-filter-view">
        <span class="mtm-view-by"><?php _e("View by:",'makerfaire')?></span>
        <a ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'" class="mtm-filter-g pointer-on-hover box gallery"><i class="fa fa-picture-o" aria-hidden="true"></i> <?php _e("GALLERY",'makerfaire')?></a>
        <span class="mtm-pipe">|</span>
        <a ng-class="{active: layout == 'list'}" ng-click="layout = 'list'" class="mtm-filter-l pointer-on-hover box list" ><i class="fa fa-th-list" aria-hidden="true"></i> <?php _e("LIST",'makerfaire')?></a>
      </div>

      <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" type="button" id="mtm-dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
          <?php _e("Filter by Topics:",'makerfaire')?> {{category}}
          <i class="fa fa-chevron-down" aria-hidden="true"></i>
        </button>

        <ul class="dropdown-menu" aria-labelledby="mtm-dropdownMenu">
          <li>
            <a class="pointer-on-hover" ng-click="clearFilter()"><?php _e("All",'makerfaire')?></a>
          </li>
          <li ng-repeat="tag in tags | orderBy: tag">
            <a class="pointer-on-hover" ng-click="setTagFilter(tag)">{{ tag }}</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="mtm-results">
      <div ng-show="!makers.length" class="container loading">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only"><?php _e("Loading",'makerfaire')?>...</span>
      </div>
      <!-- Grid View -->
      <div ng-show="layout == 'grid'" class="mtm-results-cont">
        <div ng-repeat="maker in makers | filter : makerSearch | byCategory:category">
          <a href="/maker/entry/{{maker.id}}">
            <article class="mtm-maker" style="background-image: url('{{ maker.large_img_url }}')">
              <h3>{{ maker.name }}</h3>
            </article>
          </a>
        </div>
        <div class="clearfix"></div>
      </div>

      <!-- List View -->
      <div ng-show="layout == 'list'" class="mtm-results-cont container">
        <span class="filterAlpha" ng-repeat="searchLetter in alphabet.split('') track by $index">
          <a href=""  class="pointer-on-hover" ng-click="setLetter(searchLetter)">{{ searchLetter }}</a>
        </span>
        <a href=""  class="pointer-on-hover" ng-click="setLetter('')">Reset</a>
        <div ng-repeat="maker in makers | filter : makerSearch | byCategory:category | orderBy: 'name' | startsWithLetter:letter">
          <a href="/maker/entry/{{maker.id}}">
            <article class="mtm-maker" style="background-image: url('{{ maker.large_img_url }}')">
              <h3>{{ maker.name }}</h3>
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
  jQuery(document).ready(function(){
    // Carousel left right
    jQuery( "#right-trigger" ).click(function() {
      jQuery( ".owl-next" ).click();
    });
    jQuery( "#left-trigger" ).click(function() {
      jQuery( ".owl-prev" ).click();
    });
  });
</script>

<?php get_footer(); ?>
