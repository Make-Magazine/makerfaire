<?php
/**
 * Template Name: Entry
 *
 * @version 2.0
 */

  global $wp_query;
  $entryId = $wp_query->query_vars['e_id'];
  $entry = GFAPI::get_entry($entryId);

  //entry not found
  if(isset($entry->errors)){
    $form_id = '';
    $formType = '';
    $entry=array();
    $faire = '';
  }else{
    //find outwhich faire this entry is for to set the 'look for more makers link'
    $form_id = $entry['form_id'];
    $form = GFAPI::get_form($form_id);
    $formType = $form['form_type'];
    $faire =$slug=$faireID=$show_sched=$faire_end='';
  }
  
  if($form_id!=''){
    $formSQL = "select replace(lower(faire_name),' ','-') as faire_name, faire, id,show_sched, faire_logo,start_dt, end_dt "
            . " from wp_mf_faire where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0";
    $results =  $wpdb->get_row( $formSQL );
    if($wpdb->num_rows > 0){
      $faire        =  $slug = $results->faire_name;
      $faireID      = $results->id;
      $show_sched   = $results->show_sched;
      $faire_logo   = $results->faire_logo;
      $faire_start  = $results->start_dt;
      $faire_end    = $results->end_dt;
    }
  }

  $makers = array();
  if (isset($entry['160.3']))
    $makers[] = array('firstname' => $entry['160.3'], 'lastname' => $entry['160.6'],
                      'bio'       => (isset($entry['234']) ? $entry['234']: ''),
                      'photo'     => (isset($entry['217']) ? $entry['217'] : '')
                );
  if (isset($entry['158.3']))
    $makers[] = array('firstname' => $entry['158.3'], 'lastname' => $entry['158.6'],
                      'bio'       => (isset($entry['258']) ? $entry['258'] : ''),
                      'photo'     => (isset($entry['224']) ? $entry['224'] : '')
                );
  if (isset($entry['155.3']))
      $makers[] = array('firstname' => $entry['155.3'], 'lastname' => $entry['155.6'],
                      'bio'         => (isset($entry['259']) ? $entry['259'] : ''),
                      'photo'       => (isset($entry['223']) ? $entry['223'] : '')
                );
  if (isset($entry['156.3']))
      $makers[] = array('firstname' => $entry['156.3'], 'lastname' => $entry['156.6'],
                      'bio'         => (isset($entry['260']) ? $entry['260'] : ''),
                      'photo'       => (isset($entry['222']) ? $entry['222'] : '')
                  );
  if (isset($entry['157.3']))
      $makers[] = array('firstname' => $entry['157.3'], 'lastname' => $entry['157.6'],
                      'bio'         => (isset($entry['261']) ? $entry['261'] : ''),
                      'photo'       => (isset($entry['220']) ? $entry['220'] : '')
                  );
  if (isset($entry['159.3']))
      $makers[] = array('firstname' => $entry['159.3'], 'lastname' => $entry['159.6'],
                      'bio'         => (isset($entry['262']) ? $entry['262'] : ''),
                      'photo'       => (isset($entry['221']) ? $entry['221'] : '')
                  );
  if (isset($entry['154.3']))
      $makers[] = array('firstname' => $entry['154.3'], 'lastname' => $entry['154.6'],
                      'bio'         => (isset($entry['263']) ? $entry['263'] : ''),
                      'photo'       => (isset($entry['219']) ? $entry['219'] : '')
                  );

  $groupname  = (isset($entry['109']) ? $entry['109']:'');
  $groupphoto = (isset($entry['111']) ? $entry['111']:'');
  $groupbio   = (isset($entry['110']) ? $entry['110']:'');

  // One maker
  // A list of makers (7 max)
  // A group or association
  $displayType = (isset($entry['105']) ? $entry['105']:'');

  $isGroup = $isList = $isSingle = false;
  $isGroup =(strpos($displayType, 'group') !== false);
  $isList =(strpos($displayType, 'list') !== false);
  $isSingle =(strpos($displayType, 'One') !== false);

  $sharing_cards = new mf_sharing_cards();

  //Change Project Name
  $project_name = (isset($entry['151']) ? $entry['151'] : '');

  // Url
  $project_photo = (isset($entry['22']) ? legacy_get_fit_remote_image_url($entry['22'],750,500) : '');
  $sharing_cards->project_photo = $project_photo;

  // Description
  $project_short = (isset($entry['16']) ? $entry['16']: '');
  $sharing_cards->project_short = $project_short;

  //Website
  $project_website = (isset($entry['27']) ? $entry['27']: '');
  //Video
  $project_video = (isset($entry['32'])?$entry['32']:'');
  //Title
  $project_title = (isset($entry['151'])?(string)$entry['151']:'');
  $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
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
    <div class="content col-md-12">
<?php //set the 'backlink' text and link (only set on valid entries)
      if($faire!=''){
        $url = parse_url(wp_get_referer()); //getting the referring URL
        $url['path'] = rtrim($url['path'], "/"); //remove any trailing slashes
        $path = explode("/", $url['path']); // splitting the path
        $slug = end($path); // get the value of the last element

        if($slug=='schedule'){
          $backlink = wp_get_referer();
          $backMsg = '&#65513; Back to the Schedule';
        }else{
          $backlink = "/".$faire."/meet-the-makers/";
          $backMsg = '&#65513; Look for More Makers';
        }
        ?>
        <div class="backlink"><a href="<?php echo $backlink;?>"><?php echo $backMsg;?></a></div>
        <?php
      }

      if(is_array($entry) && isset($entry['status']) && $entry['status']=='active' && isset($entry[303]) && $entry[303]=='Accepted'){
        //display schedule/location information if there is any
        if (!empty(display_entry_schedule($entryId))) {
          display_entry_schedule($entryId);
        }
?>
        <div class="page-header">
          <h1><?php echo $project_title; ?>
            <?php
             //check if this entry has one any awards
            $ribbons = checkForRibbons(0,$entryId);
            echo $ribbons;
            ?>
          </h1>
        </div>

        <img class="img-responsive" src="<?php echo $project_photo; ?>" />
        <p class="lead"><?php echo nl2br(make_clickable($project_short)); ?></p>

        <?php
        if (!empty($project_website)) {
          echo '<a href="' . $project_website . '" class="btn btn-info pull-left" target="_blank" style="margin-right:15px;">Project Website</a>';
        }
        ?>

        <!-- Button to trigger video modal -->
        <?php
        if (!empty($project_video)) {
          echo '<a href="#entryModal" role="button" id="modalButton" class="btn btn-info" data-toggle="modal">Project Video</a>';
        }
        ?>
        <br />

        <!-- Video Modal -->
        <div id="entryModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo $entry['151']; ?></h3>
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
      <div class="clearfix">&nbsp;</div>
      <div class="clearfix">&nbsp;</div>
      <?php if($formType!='Sponsor' && $formType != 'Startup Sponsor'){ ?>
        <h2>
        <?php
          if ($isGroup)
            echo 'Group';
          elseif($isList)
            echo 'Makers';
          else
            echo 'Maker';
        ?>
        </h2>
        <hr />
        <?php
        if ($isGroup) {
          echo '<div class="row center-block">
                  ',(!empty($groupphoto) ? '<img class="col-md-3 pull-left img-responsive" src="' . legacy_get_fit_remote_image_url($groupphoto,200,250) . '" alt="Group Image">' : '<img class="col-md-3 pull-left img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Group Image">');
          echo    '<div class="col-md-5">
                    <h3 style="margin-top: 0px;">' . $groupname . '</h3>
                    <p>' . make_clickable($groupbio) . '</p>
                  </div>
                </div>';
        } else {
          foreach($makers as $maker) {
            if($maker['firstname'] !='' && $maker['lastname'] !=''){
              echo '<div class="row center-block">
                      ',(!empty($maker['photo']) ? '<img class="col-md-3 pull-left img-responsive" src="' . legacy_get_fit_remote_image_url($maker['photo'],200,250) . '" alt="Maker Image">' : '<img class="col-md-3 pull-left img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Maker Image">');
              echo    '<div class="col-md-5">
                        <h3 style="margin-top: 0px;">' . $maker['firstname'] . ' ' . $maker['lastname'] . '</h3>
                        <p>' . make_clickable($maker['bio']) . '</p>
                      </div>
                    </div>';
            }
          }
        }
      }

      ?>
      <br />
      <?php
      echo display_groupEntries($entryId);
      } else { //entry is not active
        echo '<h2>Invalid entry</h2>';
      }
      ?>

    </div><!--col-md-8-->

  </div><!--row-->
</div><!--container-->



 <?php get_footer();

function display_entry_schedule($entry_id) {
  global $wpdb;global $faireID; global $faire; global $show_sched; global $faire_logo;
  if(!$show_sched){
    return;
  }
  $sql = "select location.entry_id, area.area, subarea.subarea, subarea.nicename, location.location, schedule.start_dt, schedule.end_dt
            from  wp_mf_location location
            join  wp_mf_faire_subarea subarea
                            ON  location.subarea_id = subarea.ID
            join wp_mf_faire_area area
                            ON subarea.area_id = area.ID and area.faire_id = $faireID
            left join wp_mf_schedule schedule
                    on location.ID = schedule.location_id
             where location.entry_id=$entry_id"
          . " group by area, subarea, location";
  $results = $wpdb->get_results($sql);

  if($wpdb->num_rows > 0){
    ?>
    <div id="entry-schedule">
      <span class="faireBadge pull-left">
      <?php
      if($faire_logo!=''){
        $faire_logo = legacy_get_fit_remote_image_url($faire_logo,51,51);
        echo '<a href="/bay-area"><img src="'.$faire_logo.'" alt="'.$faire.' - badge" /></a>';
      }
      ?>
      </span>
      <span class="faireTitle pull-left">
        <a href="/bay-area">
        <span class="faireLabel">Live at</span><br/>
        <div class="faireName"><?php echo (strpos($faireID,'NY')!== false?'World':'');?> Maker Faire <?php echo ucwords(str_replace('-',' ', $faire));?></div>
        </a>
      </span>
      <?php // TBD - dynamically set these links and images ?>
      <div class="faireActions">
        <span class="hidden pull-right">
          <a class="flagship-icon-link" href="/wp-content/uploads/2016/05/MF16_Map_8x11.pdf">
            <img class="actionIcon" src="http://makerfaire.com/wp-content/uploads/2016/01/icon-map.png" width="40px" scale="0">
            Event Map
          </a>
        </span>
        <span class="pull-right">
          <a class="flagship-icon-link" href="http://makerfaire.com/national-2016/schedule/">
            <img class="actionIcon" src="http://makerfaire.com/wp-content/uploads/2016/01/icon-schedule.png" width="40px" scale="0">
          </a>
          <span class="pull-right hidden"><a href="http://makerfaire.com/bay-area-2016/schedule/">View full schedule</a><br/>
            <a class="flagship-icon-link" href="/wp-content/uploads/2016/05/MF16_BA_ProgramGuide_LoRes.pdf">Download the program guide</a>
          </span>
        </span>
      </div>
      <div class="clear"></div>

      <table>
      <?php
      foreach($results as $row){
        echo '<tr>';
        if(!is_null($row->start_dt)){
          $start_dt   = strtotime( $row->start_dt);
          $end_dt     = strtotime($row->end_dt);
          echo '<td><b>'.date("l, F j",$start_dt).'<b></td>'
                  . ' <td>'. date("g:i a",$start_dt).' - '.date("g:i a",$end_dt).'</td>';
        }else{
          global $faire_start; global $faire_end;

          $faire_start = strtotime($faire_start);
          $faire_end   = strtotime($faire_end);

          //tbd change this to be dynamically populated
          echo '<td>Friday, Saturday and Sunday: '.date("F j",$faire_start).'-' . date("j",$faire_end).'</td>';
        }
        echo '<td>'.$row->area.'</td><td>'.($row->nicename!=''?$row->nicename:$row->subarea).'</td>';
        echo '</tr>';

      }
      ?>
      </table>
    </div>
    <?php
  }
}

/* This function is used to display grouped entries and links*/
function display_groupEntries($entryID){
  global $wpdb;global $faireID; global $faire;
  $return = '';

  $sql = "select * from wp_rg_lead_rel where parentID=".$entryID." or childID=".$entryID;
  $results = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    if($results[0]->parentID==$entryID){
        $title = 'Exhibits in this group:';
        $type = 'parent';
      }else{
        $title = 'Part of a group:';
        $type = 'child';
      }
    $return .= $title.'<br/>';
    $return .= '<div class="row">';
    foreach($results as $row){
      $link_entryID = ($type=='parent'?$row->childID:$row->parentID);
      $entry = GFAPI::get_entry($link_entryID);
      //Title
      $project_title = (string)$entry['151'];
      $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
      $return .= '<div class="col-md-4 col-sm-6">';
      $return .= '<a href="/maker/entry/'.$link_entryID.'">'.$project_title.'</a></div>';
    }
    $return .= '</div>';
  }
  echo $return;
}
?>