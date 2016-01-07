<?php get_header(); 
$yearOption='';
$yearSql  = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");
$firstYear = $yearSql[0]->year;

foreach($yearSql as $year){
    $selected='';
    //$selected = ($year->year==$firstYear ? 'ng-selected="faire_year==\''.$year->year.'\'"':''); //select the first year
    $yearOption .= '<option '.$selected.' value="'.$year->year.'">'.$year->year.'</option>';
    if($year->year == $firstYear){
        $yearJSON = '{"id" : "'.$year->year.'", "name": "'.$year->year.'"}';
    }else{
        $yearJSON .= ',{"id" : "'.$year->year.'", "name": "'.$year->year.'"}';
    }    
}

?>
<script>
 var yearJson =  [<?php echo $yearJSON;?>]
</script>
<div class="clear"></div>
<?php
    // Output the featured image.
    if ( has_post_thumbnail() ) :
        ?>
        <div id='brHeaderImg'>
            <?php the_post_thumbnail(); ?>
            <div>Maker Faire Ribbon Winners</div>
        </div><?php
    endif;?>



<div class="container">

	<div class="row">
                <div class="content col-xs-12">   
                    
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                            <article <?php post_class(); ?>>
                                     <?php the_content(); ?>
                            </article>
			<?php endwhile; ?>
                            <ul class="pager">
                                    <li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
                                    <li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>
                            </ul>
			<?php else: ?>
				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php endif; ?>
		<!-- start blue ribbon data -->
                <div id="ribbonPage">                           
                 <div ng-controller="ribbonController" class="my-controller">   
                     <div class="ribbonFilter">
                     <div class="ribbonToggle" style="float:left">                         
                        <a ng-class="{active: layout == 'grid'}" ng-click="layout = 'grid'"  class="box gallery"><i class="fa fa-picture-o"></i>Gallery</a>                                                    
                        <a ng-class="{active: layout == 'list'}" ng-click="layout = 'list'"  class="box list"><i class="fa fa-list"></i>List</a>
                     </div>
                     <div class="ribbonHeader" style="float:right">
                        <select ng-model="faire_year"
                                ng-init="faire_year = '<?php echo $firstYear;?>'"  ng-change="loadData(faire_year)">
                           <option ng-repeat="year in years" value="{{year.id}}">{{year.name}}</option>         
                        </select>
                        <select ng-model="query.faireData.faire">
                           <option value=" " selected>All Faires</option>
                           <option ng-repeat="faire in faires" value="{{faire}}">{{faire}}</option>
                       </select>
                       <select ng-model="query.faireData.ribbonType">
                           <option value="" selected>All Ribbons</option>
                           <option value="blue">Blue</option>
                           <option value="red">Red</option>
                       </select>
                       <div class="textSearch fancybox-wrap">
                           <input ng-model="query.$" placeholder="Filter Winners">
                       </div>
                    </div> 
                     </div>
                     <br/><br/>
                    <p ng-show="(ribbons | filter:query).length == 0" class="noData" >I'm sorry. There are no winners found.</p>
                    <!--| filter:year -->
                     <div ng-show="layout == 'grid'"  class="ribbonGrid row">                          
                          
                        <div class="ribbData col-xs-12 col-sm-4 col-md-3" dir-paginate="ribbon in ribbons| filter:query |itemsPerPage: 40" current-page="currentPage">                          
                            
                            <a href="{{ribbon.link}}" target="_blank">
                                 <div class="projImg"> 
                                     <img class="img-responsive" fallback-src="/wp-content/themes/makerfaire/images/grey-makey.png" ng-src="{{ribbon.project_photo != '' && ribbon.project_photo || '/wp-content/uploads/2015/10/grey-makey.png'}}" />
                                 
                                <div class="ribbons">

                                   <div class="blueRibbon" ng-if="ribbon.blueCount > 0">
                                       {{ribbon.blueCount}}
                                   </div>
                                   <div class="redRibbon" ng-if="ribbon.redCount > 0">
                                       {{ribbon.redCount}}
                                   </div>
                                 </div>
                                </div>
                                
                              </a>
                            <div class="makerData" ng-class="{'':'show'}[ribbon.project_photo]" >
                                <div class="projName">
                                {{ribbon.project_name}}
                                </div>{{ribbon.maker_name}}<br><br>
                                <span ng-repeat="faire in ribbon.faireData" class="{{faire.ribbonType}}data"> {{faire.faire}} {{faire.year}}</span>                             
                            </div>
                         </div>
                        <div class="text-center">
                            <dir-pagination-controls boundary-links="true" on-page-change="pageChangeHandler(newPageNumber)" template-url="<?php echo get_stylesheet_directory_uri();?>/partials/dirPagination.tpl.html"></dir-pagination-controls>
                        </div> 
                    </div> 
                    <div  ng-show="layout == 'list'" class="ribbonList">                        
                        <div ng-repeat="blueRibbons in blueList">
                            <div ng-show="(blueRibbons.winners | filter:query).length">
                                <div class="ribbonTitle">
                                    <div class="blueMakey"></div>
                                    <div>{{blueRibbons.numRibbons}} Blue Ribbons</div>
                                </div>                            

                                <ul>
                                    <li ng-repeat="bRibbonData in blueRibbons.winners  | filter:query">
                                        <a href="{{bRibbonData.link}}" target="_blank">{{ bRibbonData.project_name }}</a>
                                    </li>    
                                </ul> 
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                        <div ng-repeat="redRibbons in redList">
                            <div ng-show="(redRibbons.winners | filter:query).length">
                                <div class="ribbonTitle">
                                    <div class="redMakey"></div>
                                    <div>{{redRibbons.numRibbons}} Red Ribbons</div>
                                </div>                            

                                <ul>
                                    <li ng-repeat="rRibbonData in redRibbons.winners  | filter:query">
                                        <a href="{{bribbonData.link}}" target="_blank">{{ rRibbonData.project_name }}</a>
                                    </li>    
                                </ul> 
                            </div> 
                            <div class="clear"></div>
                        </div>
                    </div>                     
                 </div>                                         
              </div>                                            
            </div>
                <div class="container-fluid">
  
                </div><!--Content-->
	</div>
</div><!--Container-->

<?php get_footer(); ?>
