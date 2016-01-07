<?php get_header(); 

//determine the parent page slug
 $par_post = get_post($post->post_parent);
 $slug = $par_post->post_name;
 $search =  '';
 $yearNarr = (isset($_GET['py'])?$_GET['py']:'');
 $faireNarr = (isset($_GET['pf'])?$_GET['pf']:'');
 $ribbonNarr = (isset($_GET['pr'])?$_GET['pr']:'');
if($yearNarr!=''){ 
    $search .=  ' AND year = '.$yearNarr;
}
if($faireNarr!=''){ 
    $search .=  ' AND location = "'.urldecode($faireNarr).'"';
}
if($ribbonNarr!=''){ 
    $search .=  ' AND ribbonType = '.$ribbonNarr;
}
?>
<div ng-view></div>
<div class="clear"></div>
<?php
    // Output the featured image.
    if ( has_post_thumbnail() ) :			
                    the_post_thumbnail();			
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
                <div id="ribbonPage">              
                <?php      
                    //get a list of years and faires
                    $yearOption  = '<option>All Years</option>';
                    $faireOption = '<option>All Faires</option>';
                    //create faire drop down
                    $faireSql = $wpdb->get_results("SELECT distinct(location) FROM wp_mf_ribbons  where entry_id > 0 order by location ASC");
                    foreach($faireSql as $faire){  
                        $selected = ($faire->location == $faireNarr ? 'selected':'');
                        $faireOption .= '<option '.$selected.' value="'.$faire->location.'">'.$faire->location.'</option>';
                    }
                    
                    $yearSql  = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");
                    foreach($yearSql as $year){
                        $selected = ($year->year == $yearNarr ? 'selected':'');
                        $yearOption .= '<option '.$selected.' value="'.$year->year.'">'.$year->year.'</option>';
                    }
                    
                    ?>
                    <div id="ribbonPageNar">
                        
                        <select id="year"><?php echo $yearOption;?></select>
                        <select id="faire"><?php echo $faireOption;?></select>
                        <select id="ribbon">
                            <option>All Ribbons</option>
                            <option value="0">Blue</option>
                            <option value="1">Red</option>
                        </select>
                    </div>
                                
                     <div class="row">             
                    <?php
                    $rec_limit = 21;
                    /* Get total number of ribbons */
                    $countsql = "SELECT distinct(entry_id) FROM `wp_mf_ribbons` where entry_id > 0 ".$search." group by entry_id";                    
                    $countrow = $wpdb->get_results($countsql, OBJECT);

                    $rec_count = $wpdb->num_rows;
                    if($rec_count==0){
                        echo "I'm sorry. There are no ribbon winners for the selections you have chosen.  Please try again.";
                    }
                    
                    if( isset($wp_query->query_vars['page'] ) ){
                       $page = $wp_query->query_vars['page'];
                       $offset = $rec_limit * $page ;
                    }else{
                       $page = 1;
                       $offset = 0;
                    }                    
                    echo '<input type="hidden" id="currPage" value="'.$page.'" />';
                    $sql = "SELECT entry_id FROM `wp_mf_ribbons` where entry_id > 0 "
                        . $search                       
                        . " group by entry_id "
                        . " order by year DESC, location ASC "                       
                        . " LIMIT $offset, $rec_limit";
                    
                    $ribbons = $wpdb->get_results($sql,OBJECT);
                    
                    $ribbonData=array();
                    foreach($ribbons as $ribbon){
                        $Rsql = "SELECT location, year, ribbonType,sum(numRibbons) as ribbonCount "
                              . " FROM `wp_mf_ribbons` where entry_id = $ribbon->entry_id "
                              . " group by entry_id, location, year, ribbonType"
                              . " order by year DESC, location, ribbonType";
                        $rData = $wpdb->get_results($Rsql,OBJECT);                        
                        foreach($rData as $data){
                            $currCount = (isset($ribbonData[$ribbon->entry_id]['ribbon'][$data->ribbonType]['count']) ? $ribbonData[$ribbon->entry_id]['ribbon'][$data->ribbonType]['count']:0);
                            $ribbonData[$ribbon->entry_id]['ribbon'][$data->ribbonType]['count']  = $currCount + $data->ribbonCount;
                            $ribbonData[$ribbon->entry_id]['ribbon'][$data->ribbonType]['data'][] = 
                                    array( 'year'=>$data->year, 'faire'=>$data->location);
                        }
                     }
          
                     foreach($ribbonData as $entry_id=>$data){   
                        //pull by ACF field - entry id (cs format)
                        $my_posts = get_posts(array(
                                   'numberposts'	=> 1,
                                   'post_type'	=> 'maker-entry-archive',
                                   'meta_key'	=> 'entry_id',
                                   'post_status'    => 'accepted', //what about v1 records?
                                   'meta_value'	=> $entry_id
                           ));
                        $postID = 0;
                        if(!empty($my_posts)){  
                            $postID        = $my_posts[0]->ID;
                            //$projectName   = get_field('project_name',$postID);
                            //$attachment_id = get_field('project_photo',$postID);
                            $custom_fields      = get_field_objects($postID);                                                        
                            $project_name       = (isset($custom_fields['project_name']['value'])   ? $custom_fields['project_name']['value'] : '');
                            $attachment_id      = (isset($custom_fields['project_photo']['value'])  ? $custom_fields['project_photo']['value']        : '');
                            $project_photo = wp_get_attachment_url( $attachment_id);
                        }
                        
                        $blueCount = (isset($data['ribbon'][0]['count'])?$data['ribbon'][0]['count']:0);
                        $redCount  = (isset($data['ribbon'][1]['count'])?$data['ribbon'][1]['count']:0);
                         
                        if($postID > 0){
                            echo '<div class="ribbData col-lg-3 col-md-3 col-sm-3 col-xs-3">';
                              echo '<a href="/mfarchives/'.$entry_id.'">';
                                if($project_photo!=''){         
                                   //legacy_get_resized_remote_image_url( $scheduleditem['large_img_url'], 140, 140 );
                                   echo '<img img-responsive class="entryImg" src="' . $project_photo . '" style="width: 100%;height: 270px;" alt="'.$project_name.'">';

                                   //echo '<img src="'. $project_photo[0].'" width="'. $project_photo[1] .'" height="'. $project_photo[2].'">';
                                }else{
                                   echo '<img img-responsive class="entryImg" src="/wp-content/uploads/2015/10/grey-makey.png" alt="grey" />';
                                }
                                echo '<div class="ribbons">';
                                    //display blue ribbon with count
                                    if($blueCount>0){                                                                
                                        echo '<div class="blueRibbon">'.$blueCount.'</div>';
                                    } 
                                    //display red ribbon with count    
                                    if($redCount>0){
                                        echo '<div class="redRibbon">'.$redCount.'</div>';
                                    }
                                echo '</div>';
                              echo '</a>';
                              
                              echo '<div class="makerData">';
                                echo $project_name.'<br/>';
                                echo 'Maker Name here<br/>';
                                
                                //display faires with blue ribbon                                
                                foreach($data['ribbon'] as $ribbonType=>$data){
                                    if($ribbonType==0)  $rColor = 'blue';
                                    if($ribbonType==1)  $rColor = 'red';
                                    foreach($data['data'] as $faire){                                                                        
                                        echo '<span class="'.$rColor.'data">'.$faire['faire'].' ' .$faire['year'].'</span>';
                                    }
                                }                             
                              echo '</div>';
                            echo '</div>';
                        }
                     }
                     ?>
                     </div>                                         
                </div>                  
                <?php

                    if ($rec_count > $rec_limit) {
                        $search_term = '';

                        $current_url = $wp_query->query_vars['pagename'];
                            echo '<div class="row padtop padbottom">
                                    <div class="col-md-8">
                                            ' . buildPagination($current_url,$search_term,$page,$rec_limit,$rec_count) . '
                                    </div>
                            </div>';
                    }
                    ?>

		</div><!--Content-->

		<?php //get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>
