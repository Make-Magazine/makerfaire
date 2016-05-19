<?php // Template Name: Signage

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
			a { text-decoration:none; color:#000; }
			h1, h2, h3, h4 { margin:5px 0 0; }
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
         $sql ="SELECT  DAYNAME(schedule.start_dt) as Day,
                       DATE_FORMAT(schedule.start_dt,'%h:%i %p') as 'Start Time',
                       DATE_FORMAT(schedule.end_dt,'%h:%i %p') as 'End Time',
                       if(subarea.niceName = '' or subarea.niceName is null,subarea.subarea,subarea.niceName) as nicename,
                       area.area, entity.presentation_title as 'Exhibit',

                                (select  group_concat( distinct concat(maker.`FIRST NAME`,' ',maker.`LAST NAME`) separator ', ') as Makers
                                    from    wp_mf_maker maker,
                                            wp_mf_maker_to_entity maker_to_entity
                                    where   schedule.entry_id           = maker_to_entity.entity_id  AND
                                            maker_to_entity.maker_id    = maker.maker_id AND
                                            maker_to_entity.maker_type != 'Contact'
                                    group by maker_to_entity.entity_id                        ) as Presenters

                FROM    wp_mf_schedule schedule
                join    wp_mf_entity entity on
                            schedule.entry_id   = entity.lead_id and
                            entity.status       = 'Accepted'
                join    wp_mf_location location on
                            schedule.location_id  = location.ID and
                            schedule.entry_id = location.entry_id
                join    wp_mf_faire_subarea subarea on
                            location.subarea_id = subarea.id
                join    wp_mf_faire_area area on
                            subarea.area_id = area.id

                where   schedule.faire          = '".$faire."'

                        "

                        .($day_set!=''?" and DAYNAME(`schedule`.`start_dt`)='".ucfirst($day_set)."'":'');

        if($orderBy=='time'){
            $sql .= " order by schedule.start_dt ASC, schedule.end_dt ASC, nicename ASC, 'Exhibit' ASC";
        }else{
            $sql .= " order by nicename ASC, schedule.start_dt ASC, schedule.end_dt ASC,  'Exhibit' ASC";
        }
        //echo $sql;
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
            $output .= '<td width="25%" style="padding:15px 0;" valign="top">';

            $output .= '<h2 style="font-size:.9em; color:#333; margin-top:3px;">' . $row['Start Time']  . ' &mdash; ' . $row['End Time']  . '</h2>';
            if($orderBy=='time')    {
                $output .= $stage.' ('.$row['area'].')' ;
            }
            $output .= '</td>';
            $output .= '<td>';
            $output .= '<h3 style="margin-top:0;">' . $row['Exhibit']  . '</h3>';
	    $output .= $row['Presenters'];
            $output .= '<tr><td colspan="2"><div style="border-bottom:2px solid #ccc;"></div></td></tr>';
            $output .= '</td>';
            $output .= '</tr>';

            $output .= '</table>';
        }


	return $output;
}
