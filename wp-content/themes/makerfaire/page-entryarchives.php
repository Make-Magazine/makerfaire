<?php
/**
 * Template Name: EntryArchives
 * 
 *
 * @version 2.0
 */

global $wp_query;
$postID = 0;
//pull by the slug name 
$the_slug = $wp_query->query_vars['entryslug']; //entry-id

if($the_slug==''){
    $the_slug = $wp_query->query_vars['maker-entry-archive'];
}
$makers = array();

//pull by ACF field - entry id (cs format)
$my_posts = get_posts(array(
           'numberposts'	=> 1,
           'post_type'		=> 'maker-entry-archive',
           'meta_key'		=> 'entry_id',
           'post_status'        => 'accepted',
           'meta_value'	        => $the_slug
   ));

if(empty($my_posts)){     
    //if that fails, try pulling by post name or slug wp v1 format
    $args = array(
                  'name'          => $the_slug,              
                  'post_type'	  => 'maker-entry-archive',
                  'post_status'   => 'accepted',
                  'numberposts'   => 1
    );    

    $my_posts = get_posts($args);   
}

if(!empty($my_posts)){  
    $postID = $my_posts[0]->ID;
    $custom_fields      = get_post_custom($my_posts[0]->ID);     
    if(is_array($custom_fields)){
        
    }
    $project_faire      = (isset($custom_fields['faire'][0])        ? $custom_fields['faire'][0]        : '');
    $project_name       = (isset($custom_fields['project_name'][0]) ? $custom_fields['project_name'][0] : '');
    $attachment_id      = (isset($custom_fields['project_photo'][0])  ? $custom_fields['project_photo'][0]        : '');
    $project_photo = wp_get_attachment_url( $attachment_id);
    
    $project_short      = (isset($custom_fields['project_description'][0]) ? $custom_fields['project_description'][0] : '');
    $project_website    = (isset($custom_fields['website'][0])             ? $custom_fields['website'][0]             : '');
    $project_video      = (isset($custom_fields['video'][0])               ? $custom_fields['video'][0]               : '');
    $project_title      = (isset($custom_fields['project_name'][0])        ? $custom_fields['project_name'][0]        : '');
    
    //get maker info
    for($x=0;$x<=6;$x++){
        $mname  = 'maker_information_'.$x.'_maker_name';
        $mdesc  = 'maker_information_'.$x.'_maker_description';
        $memail = 'maker_information_'.$x.'_maker_email';
        $mweb   = 'maker_information_'.$x.'_maker_website';
        $mtitle = 'maker_information_'.$x.'_maker_title';
        $mphoto = 'maker_information_'.$x.'_maker_photo';
        $mattachment_id = (isset($custom_fields[$mphoto][0]) ? $custom_fields[$mphoto][0] : '');
        $photo = wp_get_attachment_url( $mattachment_id);     
        
        if(isset($custom_fields[$mname][0]) && $custom_fields[$mname][0]!=''){
        $makers[] = array('name'    => (isset($custom_fields[$mname][0])  ? $custom_fields[$mname][0]  : ''),
                            'bio'     => (isset($custom_fields[$mdesc][0])  ? $custom_fields[$mdesc][0]  : ''),
                            'photo'   => $photo
                            );
        }
        
        
    }
    
//    $makers
    $entrySlug =$the_slug;
    if($my_posts[0]->post_content!=''){
        $json =  stripslashes(urldecode( $my_posts[0]->post_content )) ;
        $json = json_decode($json);
        //print_r($json);
         switch ($json->form_type) {
              case 'exhibit':
                      $project_faire = $json->maker_faire;
                      $project_name = $json->project_name;
                      $project_photo = $json->project_photo;
                      $project_short = $json->public_description;
                      $project_website = $json->project_website;
                      $project_video = $json->project_video;
                      $project_title = $json->project_name;
                      $makers = array();
                      if (strlen($json->m_maker_name[0]) > 0)
                              $makers[] = array('name' => $json->m_maker_name[0],
                                              'bio'=>$json->m_maker_bio[0],
                                              'photo'=>$json->m_maker_photo[0]);
              break;
              case 'performer':
                      $project_faire = $json->maker_faire;
                      $project_name = $json->performer_name;
                      $project_photo = $json->performer_photo;
                      $project_short = $json->public_description;
                      $project_website = $json->performer_website;
                      $project_video = $json->performer_video;
                      $project_title = $json->performer_name;
                      $makers = array();

                      break;
              default:
                      $project_faire = $json->maker_faire; 
                        $project_name = $json->presentation_name; 
                        $project_photo = $json->presentation_photo;
                        $project_short = $json->short_description;
                        $project_website = $json->presentation_website;
                        $project_video = $json->video;
                        $project_title = $json->presentation_name;
                        $makers = array();
                        if (strlen($json->presenter_name[0]) > 0)
                              $makers[] = array('name' => $json->presenter_name[0],
                                              'bio'=>$json->presenter_bio[0],
                                              'photo'=>$json->presenter_photo[0]);
                        break;
            }
    }
  $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
}else{
    //determine if we are looking by faire or by entry id

    $posts = get_posts(array(
        'numberposts'	=> -1,
	'post_type'	=> 'maker-entry-archive',
	'meta_key'	=> 'faire',
	'meta_value'	=> urldecode($the_slug)
    ));
    //echo urldecode($the_slug);
    if(!empty($posts)){  
        ?>
        <ul>		
	<?php foreach( $posts as $post ): 		
		setup_postdata( $post )		
		?>
		<li>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>	
	<?php endforeach; ?>	
	</ul>	
	<?php wp_reset_postdata();
    }else{
        $project_title = 'I\'m sorry, we can\'t find this maker.'; 
        $project_video = $project_website = $project_photo = '';
        $project_short = $project_name    = $project_faire = '';
        $makers = array();
    }
}
    
    //Setup Sharing Cards
    $sharing_cards = new mf_sharing_cards();
    // Url
    $sharing_cards->project_photo = $project_photo;
    // Description
    $sharing_cards->project_short = $project_short;
    //Title
    $sharing_cards->project_title = $project_title;
    //Url
    global $wp;
    $canonical_url = home_url( $wp->request ) . '/' ;    
    $sharing_cards->canonical_url = $canonical_url;
    $sharing_cards->set_values();
    
  get_header();
  
?>

<div class="clear"></div>

<div class="container modal-fix">

  <div class="row">

    <div class="content col-md-8">

      <div class="page-header">

        <h1><?php echo $project_title; ?>
        <?php if($postID!=0){
          //check if this entry has one any awards
          $ribbons = checkForRibbons($postID);          
          echo $ribbons;
      } ?>
            </h1>
        <?php echo ( !empty( $project_faire ) ) ? '<h5><small>' . archives_better_name( $project_faire ) . '</small></h5>' : ''; ?>
        

      </div>
      <?php ?>

      <img class="img-responsive padbottom" src="<?php echo $project_photo; ?>" />

      <p class="lead"><?php echo nl2br(make_clickable($project_short)); ?></p> 

      <?php if (!empty($project_website)) {
          echo '<a href="' . $project_website . '" class="btn btn-info pull-left" target="_blank" style="margin-right:15px;">Project Website</a>';
      } ?>
      
      <!-- Button to trigger video modal -->
      <?php if (!empty($project_video)) {
            //echo '<a href="#entryModal" role="button" class="btn btn-info" data-toggle="modal">Project Video</a>';
            echo '<a href="#entryModal" role="button" id="modalButton" class="btn btn-info" data-toggle="modal">Project Video</a>';

      } ?>
      <br />

      <!-- Video Modal -->      
      <div id="entryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 id="myModalLabel"><?php echo $project_title; ?></h3>
        </div>
        <div class="modal-body">
          
           <?php  
           $dispVideo = str_replace('//vimeo.com','//player.vimeo.com/video',$project_video);
           //youtube has two type of url formats we need to look for and change
           $videoID = parse_yturl($dispVideo);
           if($videoID!=''){
               $dispVideo = 'https://www.youtube.com/embed/'.$videoID;
           }
           ?>
            
            <input id="entryVideo" type="hidden" value="<?php echo $dispVideo; ?>" />
            <iframe width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

        </div>
      </div>
      
<!-- Commenting out for now via Clair
      <h2>Schedule</h2>
      <hr />
      <?php
        //if (!empty(display_entry_schedule($entryId))) {
          //display_entry_schedule($entryId);
       // }
      ?>
      <br />
-->
      <div class="clearfix">&nbsp;</div>
      <div class="clearfix">&nbsp;</div>
      <?php
      if (!empty($groupbio)) {
		echo '<h2>Group</h2><hr />';
		
        echo '<div class="row padbottom">
                ',(!empty($groupphoto) ? '<img class="col-md-3 pull-left img-responsive" src="' . $groupphoto . '" alt="Group Image">' : '<img class="col-md-3 pull-left img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Group Image">');
        echo    '<div class="col-md-5">
                  <h3 style="margin-top: 0px;">' . $groupname . '</h3>
                  <p>' . make_clickable($groupbio) . '</p>
                </div>
              </div>';
      } 
      else {
		  if (!empty($makers)) echo '<h2>Group</h2><hr />';

    		foreach($makers as $maker) {
      		echo '<div class="row padbottom">
                  ',(!empty($maker['photo']) ? '<img class="col-md-3 pull-left img-responsive" src="' . $maker['photo'] . '" alt="Maker Image">' : '<img class="col-md-3 pull-left img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Maker Image">');
          echo    '<div class="col-md-5">
                    <h3 style="margin-top: 0px;">' . $maker['name'] . '</h3>
                    <p>' . make_clickable($maker['bio']) . '</p>
                  </div>
                </div>';
    		}
      }
      ?>

    </div><!--col-md-8-->

    <?php get_sidebar(); ?>

  </div><!--row-->

</div><!--container-->


<!-- What do i do with these?
Duplicate to $project_website;
<p>Homepage: <i><?php echo $entry['27']; ?></i></p>

Duplicate $entry['22']
<li>Project Photo: <?php echo $project_photo; ?></li> 

Duplicate to $entry['151']
<li>Short Desription<?php echo $entry['16']; ?></li>
-->

 <?php
 
  get_footer();
  
  function archives_better_name( $str ) {
  	if ( $str == '2013_bayarea' ) {
  		return 'Maker Faire Bay Area 2013';
  	} elseif ( $str == '2013_newyork' ) {
  		return 'World Maker Faire New York 2013';
  	} elseif ( $str == '2014_bayarea' ) {
  		return 'Maker Faire Bay Area 2014';
  	} elseif ( $str == '2014_newyork' ) {
  		return 'World Maker Faire New York 2014';
  	}else{
            //replace hyphen and underscore with space and use sentance case
            $str =  str_replace('-',' ',$str);
            $str =  str_replace('_',' ',$str);
            return ucwords($str);
        }
  }
function display_entry_schedule($entry_id) {
  //echo ('<link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/>
  echo ('<h4><label class="detail-label">Schedule:</label></h4>');
  $mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $result = $mysqli->query("SELECT `wp_mf_schedule`.`ID`,
    `wp_mf_schedule`.`entry_id`,
    `wp_mf_schedule`.`location_id`,
    `wp_mf_schedule`.`faire`,
    `wp_mf_schedule`.`start_dt`,
    `wp_mf_schedule`.`end_dt`,
    `wp_mf_schedule`.`day`
  FROM `wp_mf_schedule` where entry_id=$entry_id");

  if ($result)
  {
    if ($result->num_rows === 0) echo 'No schedule found';
    else 
    {
    echo '<ul>';
    while($row = $result->fetch_row())
    {
      $start_dt = strtotime( $row[4]);
      $end_dt = strtotime($row[5]);
      $schedule_entry_id = $row[0];
      echo ('<li>'.date("l",$start_dt).': '. date("H:i:s",$start_dt).' to '.date("H:i:s",$end_dt).'</li>');
    }
    echo '</ul>';
    }
  }
}

?>