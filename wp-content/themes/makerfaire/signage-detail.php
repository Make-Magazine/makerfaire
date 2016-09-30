<?php // Template Name: Signage Detail

$location =  ( isset( $_GET['loc'] )?intval( $_GET['loc'] ):'' );
$faire = (isset($_GET['faire']) ? $_GET['faire']:'ny15');

if ( ! isset( $_GET['description'] ) ) {
	$short_description = true;
} else {
	$short_description = false;
}

$orderBy = (isset( $_GET['orderBy'])?$_GET['orderBy']:'' );


	$day = (isset( $_GET['day'] )?sanitize_title_for_query( $_GET['day'] ):'');

if ( ! empty( $location ) )
	$term = get_term_by( 'name', $location, 'location' );

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Stage Signage - <?php echo sanitize_title( $location ); ?></title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		<style>
			body { font-family: 'Benton Sans', Helvetica, sans-serif; }
			a {
        text-decoration:none; color:#00

      }
      page {-webkit-transform: rotate(-90deg);
     -moz-transform:rotate(-90deg);
     filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);}
      .detail td{vertical-align: text-top;}
      .title {font-size: 1.5em;     display: block;

    font-weight: bold;}
		</style>
    <style type="text/css" media="print">


  </style>
	</head>
	<body>
		<?php echo get_schedule_list( $location, $short_description, $day, $faire ); ?>
	</body>
</html>
<?php
/**
 * Get our schedule stuff
 * @param  String $location [description]
 * @return [type]           [description]
 */
function get_schedule_list( $location, $short_description = false, $day_set = '' , $faire = 'ny15') {
  global $orderBy;
  global $wpdb;
  $output = '';
  //retrieve Data
  $sql = "Select 	DATE_FORMAT(wp_mf_schedule.start_dt,'%h:%i %p') as 'Start Time', 		DATE_FORMAT(wp_mf_schedule.end_dt,'%h:%i %p') as 'End Time',
                  DAYNAME(`wp_mf_schedule`.`start_dt`) AS  `Day`,
                  wp_mf_schedule.entry_id,
                  wp_mf_entity.presentation_title,
                  wp_mf_entity.special_request,
                  concat(maker.`First Name`,' ', maker.`Last Name`) as 'contact',
                  maker.`Email` as 'contact-email',
                  maker.`phone` as 'contact-phone',

                  (select  group_concat( TWITTER separator ', ') as TWITTER
                    from    wp_mf_maker maker,
                            wp_mf_maker_to_entity maker_to_entity
                    where   wp_mf_schedule.entry_id           = maker_to_entity.entity_id  AND maker_to_entity.maker_id    = maker.maker_id AND maker_to_entity.maker_type != 'Contact'  )  as 'twitter',
                  (select  group_concat( distinct concat(maker.`FIRST NAME`,' ',maker.`LAST NAME`) separator ', ') as Makers
                    from    wp_mf_maker maker,
                            wp_mf_maker_to_entity maker_to_entity
                    where   wp_mf_schedule.entry_id           = maker_to_entity.entity_id  AND maker_to_entity.maker_id    = maker.maker_id AND maker_to_entity.maker_type != 'Contact'  )  as 'presenters',
                  wp_mf_faire_subarea.subarea as 'stage',area.area,
                  if(wp_mf_faire_subarea.niceName = '' or wp_mf_faire_subarea.niceName is null,wp_mf_faire_subarea.subarea,wp_mf_faire_subarea.niceName) as nicename

          from wp_mf_schedule,
               wp_mf_entity,
                wp_mf_maker_to_entity m2e,
                wp_mf_maker maker,
                wp_mf_location,
                wp_mf_faire_subarea,
                wp_mf_faire_area area

          where wp_mf_entity.status = 'Accepted'
            and m2e.entity_id  = wp_mf_entity.lead_id
            and m2e.maker_type = 'contact'
            and m2e.maker_id   = maker.maker_id
            and wp_mf_schedule.entry_id = wp_mf_entity.lead_id
            and wp_mf_schedule.faire    = '".$faire."'
            and wp_mf_schedule.location_id = wp_mf_location.ID
            and wp_mf_location.subarea_id  = wp_mf_faire_subarea.ID
            and wp_mf_faire_subarea.area_id = area.id"
          .($day_set!=''?" and DAYNAME(`schedule`.`start_dt`)='".ucfirst($day_set)."'":'');


  if($orderBy=='time'){
    $sql .= " order by wp_mf_schedule.start_dt ASC, wp_mf_schedule.end_dt ASC, nicename ASC, 'Exhibit' ASC";
  }else{
    $sql .= " order by nicename ASC, wp_mf_schedule.start_dt ASC, wp_mf_schedule.end_dt ASC,  'Exhibit' ASC";
  }

  //group by stage and date
  $dayOfWeek = '';
  $stage     = '';

  foreach( $wpdb->get_results($sql, ARRAY_A ) as $key=>$row) {
    if($orderBy=='time'){ //break by stage. day goes in h1
      $stage = $row['nicename'];
      if( $dayOfWeek!=$row['Day']){
        //skip the page break after if this is the first time
        if($dayOfWeek != '')    $output.= '<div style="page-break-after: always;"></div>';
        $dayOfWeek=$row['Day'];

        $output .='<h1 style="font-size:2.2em; margin:31px 0 0; max-width:75%;float:left">'.$dayOfWeek.'</h1>
                   <h2 style="float:right;margin-top:31px;"><img src="/wp-content/uploads/2016/01/mf_logo.jpg" style="width:200px;" alt="" ></h2>
                   <p></p>
                   <p></p>
                   <p></p>';
      }
    }else{
      if($stage!=$row['nicename'] || $dayOfWeek!=$row['Day']){
        //skip the page break after if this is the first time
        if($stage != '')    $output.= '<div style="page-break-after: always;"></div>';
        $stage = $row['nicename'];
        $dayOfWeek=$row['Day'];

        $output .='<h1 style="font-size:2.2em; margin:31px 0 0; max-width:75%;float:left">'.$stage.' <small>('.$row['area'].')</small> </h1>
                   <h2 style="float:right;margin-top:31px;"><img src="/wp-content/uploads/2016/01/mf_logo.jpg" style="width:200px;" alt="" ></h2>
                   <p></p>
                   <p></p>
                   <p></p>';
        $output .= '<div style="clear:both"><h2>'.$dayOfWeek.'</h2></div>';
      }
    }

    $output .= '<table style="width:100%;">';
    $output .= '<tr>';
    $output .= '  <td width="20%" valign="top">';
    $output .= '    <h2 style="font-size:.9em; color:#333; margin-top:3px;">' . $row['Start Time']  . ' &mdash; ' . $row['End Time']  . '</h2>';
    if($orderBy=='time')    {
        $output .= $stage.' ('.$row['area'].')' ;
    }

    $output .= '  </td>';
    $output .= '  <td>'
                . ' <span class="title">'. $row['presentation_title']  . ' ('.$row['entry_id'].')</span>'
                .   '<b>Presenters: </b>'.$row['presenters']. '<br/>'
                .   '<b>Contact: </b>'.$row['contact'].' - '.$row['contact-email'].' - '.$row['contact-phone'] . '<br/>'
                .   ($row['twitter']!='' ?'<b>Twitter: </b>'.$row['twitter'].'<br/>':'')
                .   ($row['special_request']!='' ? '<b>Requets: </b>'.$row['special_request'] : '')
              . ' </td>';
    $output .= '</tr>';

    $output .= '<tr><td colspan="2"><div style="border-bottom:2px solid #ccc;"></div></td></tr>';
    $output .= '</td></tr>';
    $output .= '</table>';
  }
	return $output;
}
