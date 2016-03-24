<?php
/*
 * this script will hold all the cronjobs for makerfaire
 */


//for testing
/*define( 'BLOCK_LOAD', true );
require_once( '../../../../wp-config.php' );
require_once( '../../../../wp-includes/wp-db.php' );
$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
build_wp_mf_maker(); //for testing*/

//add_action('cron_wp_mf_maker', 'build_wp_mf_maker');
//add_action('cron_wp_mf_api_entity', 'build_wp_mf_api_entity');

function build_wp_mf_api_entity(){
    global $wpdb;
    $sql = "REPLACE INTO `wp_mf_api_entity`
            (`ID`,
            `project_title`,
            `project_description`,
            `project_url`,
            `category_id`,
            `child_id_ref`,
            `thumb_image_url`,
            `large_image_url`)
            SELECT
        `wp_rg_lead_detail`.`lead_id` AS `lead_id`,
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 151),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `Title`,COALESCE(
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 16),
                `l`.`value`,
                NULL)
            SEPARATOR ',')),trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 16),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ','))) AS `Description`,
       trim( GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 27),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `URL`,
         trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) BETWEEN 146.9999 AND 147.9999),
                    SUBSTRING_INDEX(SUBSTRING_INDEX(`wp_rg_lead_detail`.`value`, ':', 2), ':', -1),
               NULL)
            SEPARATOR ',')) AS `Categories`,
		trim(GROUP_CONCAT(distinct maker_id
            SEPARATOR ',')) AS `maker_ids`,
		trim(GROUP_CONCAT(distinct IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 22),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `Photo`,
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 22),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `ThumbPhoto`
    FROM
        (`wp_rg_lead_detail`
        left outer JOIN `wp_rg_lead_detail_long` `l` ON ((`wp_rg_lead_detail`.`id` = `l`.`lead_detail_id`)))
        JOIN `wp_rg_lead` `b` ON ((`wp_rg_lead_detail`.`lead_id` = `b`.`id`))
        JOIN `wp_mf_maker` `c` ON (`b`.`id` = `c`.lead_id) and not isnull(`c`.`First Name`)
    GROUP BY `wp_rg_lead_detail`.`lead_id`;";
     $wpdb->get_results($sql);
}

//this cron action will create the JSON files used by the blue ribbon page
add_action('cron_ribbonJSON', 'build_ribbonJSON');

function build_ribbonJSON(){
    global $wpdb;
    require_once( TEMPLATEPATH. '/partials/ribbonJSON.php' );

    $yearSql  = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");

    foreach($yearSql as $year){
        $json = createJSON($year->year);
        //write json file
        unlink(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json'); //delete json file if exists
        $fp = fopen(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json', 'w');//create json file

        fwrite($fp, $json);
        fclose($fp);
    }
}

add_action('cron_rmt_update', 'rmt_update');
function rmt_update(){
  global $wpdb;
  $sql = "Select id,form_id
          from wp_rg_lead
          where status <> 'trash' and
          form_id in (45,46,47,48,49) and
          id not in (select lead_id from wp_rg_lead_meta where meta_key='mf_jdb_sync') and
          id in (select lead_id from wp_rg_lead_detail where field_number = 303) ORDER BY `wp_rg_lead`.`id` ASC";
  $results = $wpdb->get_results($sql);
  foreach($results as $row){
    GFJDBHELPER::gravityforms_send_entry_to_jdb($row->id);
  }

}