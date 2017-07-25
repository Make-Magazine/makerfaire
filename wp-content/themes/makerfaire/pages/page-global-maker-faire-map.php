<?php /* Template Name: Global Maker Faire Map */  ?>
<?php get_header(); ?>
<div class="faire-global-map-wrp" ng-app="faireMapsApp" ng-strict-di>
  <div ng-controller="MapCtrl as $ctrl">
    <div class="container">
      <div class="col-md-12">
        <h1>Faires around the world</h1>
      </div>
    </div>
    <nav class="map-filters-wrp affix-bottom" data-spy="affix" data-offset-top="210">
      <div class="container">
        <div class="col-md-12">
          <div class="searchbox">
            <h2>Explore Maker Faires</h2>
            <input type="text"
              class="form-control input-sm"
              placeholder="Location, Name, Type, Date"
              ng-model="$ctrl.filterText"
              ng-model-options="{debounce: 500}"
              ng-change="$ctrl.applyMapFilters()" />
            <div class="fa fa-times-circle cursor-pointer"
              ng-click="$ctrl.filterText = ''; $ctrl.applyMapFilters();">
            </div>
          </div>
          <div class="filters ng-cloak" ng-if="$ctrl.faireMarkers">
            <p class="map-filter-title">Sort By Type</p>
            <faires-map-filter class="map-filter-flagship" default-state="true" filter="Flagship">
              Flagship
            </faires-map-filter>
            <faires-map-filter class="map-filter-featured" default-state="true" filter="Featured">
              Featured
            </faires-map-filter>
            <faires-map-filter class="map-filter-mini" default-state="true" filter="Mini">
              Mini
            </faires-map-filter>
            <faires-map-filter class="map-filter-school" default-state="true" filter="School">
              School
            </faires-map-filter>
          </div>
        </div>
      </div>
    </nav>
    <div class="container">
      <div class="col-md-12">
        <div class="loading-spinner" ng-if="!$ctrl.faireMarkers">
          <i class="fa fa-circle-o-notch fa-spin"></i>
        </div>
        <!-- Map Angular Component -->
        <faires-google-map
          id="faire-global-map"
          map-data="::$ctrl.faireMarkers"
          ng-if="$ctrl.faireMarkers">
        </faires-google-map>
        <!-- Color Key -->
        <div class="faire-key-boxes">
          <div class="flagship-key" ng-click="$ctrl.toggleBox('Flagship')">
            Flagship Maker Faires
            <p>Faires curated and produced by the Maker Media team</p>
          </div>
          <div class="featured-key" ng-click="$ctrl.toggleBox('Featured')">
            Featured Maker Faires
            <p>Larger-scale regional events</p>
          </div>
          <div class="mini-key" ng-click="$ctrl.toggleBox('Mini')">
            Mini Maker Faires
            <p>Community events</p>
          </div>
          <div class="school-key" ng-click="$ctrl.toggleBox('School')">
            School Maker Faires
            <p>K-12 Faires (closed to general public)</p>
          </div>
        </div>
        <!-- List of Faires -->
        <div class="faire-date-toggle"
          ng-class="{'active': !$ctrl.pastEvents}"
          ng-click="$ctrl.pastEvents = false;$ctrl.applyMapFilters();">
          Upcoming
        </div>
        <div class="faire-date-toggle"
          ng-class="{'active': $ctrl.pastEvents}"
          ng-click="$ctrl.pastEvents = true;$ctrl.applyMapFilters();">
          Past
        </div>
        <div class="faire-list-table table-responsive">
          <table class="table table-striped table-condensed">
            <tr></tr>
            <tr ng-init="sort='event_start_dt';reverse=false">
              <th class="cursor-pointer" ng-click="sort='annual';reverse=!reverse">
                ANNUAL
                <span ng-show="sort == 'annual'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                </span>
              </th>
              <th class="cursor-pointer" ng-click="sort='category';reverse=!reverse">FAIRE TYPE
                <span ng-show="sort == 'category'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                 </span>
                </th>
              <th class="cursor-pointer" ng-click="sort='event_start_dt';reverse=!reverse">DATE
                <span ng-show="sort == 'event_start_dt'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                </span>
              </th>
              <th class="cursor-pointer" ng-click="sort='name';reverse=!reverse">EVENT NAME
                <span ng-show="sort == 'name'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                </span>
              </th>
              <th>LOCATION</th>
              <th class="cursor-pointer" ng-click="sort='venue_address_country';reverse=!reverse">COUNTRY
                <span ng-show="sort == 'venue_address_country'">
                  <span ng-show="!reverse">^</span>
                  <span ng-show="reverse">v</span>
                </span>
              </th>
            </tr>
            <tr dir-paginate="(index, row) in $ctrl.faireMarkers | orderBy:sort:reverse | itemsPerPage: 20">
              <td>{{row.annual | ordinal}}</td>
              <td class="map-category-{{row.category}}"><i class="fa fa-circle"></i> {{row.category}}</td>
              <td>{{row.event_dt}}</td>
              <td>
                <a target="_blank" ng-if="row.faire_url" href="{{row.faire_url}}">{{row.name}}</a>
                <span ng-if="!row.faire_url">{{row.name}}</span>
              </td>
              <td>
                {{row.venue_address_city}}{{row.venue_address_state && ', '+row.venue_address_state || ''}}
              </td>
              <td>{{row.venue_address_country}}</td>
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
