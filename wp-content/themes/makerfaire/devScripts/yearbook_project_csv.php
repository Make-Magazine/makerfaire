<?php
/*
 *  This devscript creates a CSV of all fields in all forms across all global sites
 */
include 'db_connect.php';
global $wpdb;
error_reporting(E_ALL);
ini_set('display_errors', 1);
$faire_year = isset($_GET['faire_year']) ? $_GET['faire_year'] : '';
if ($faire_year == '' || !is_numeric($faire_year)) {
    die('Please set the faire_year variable');
}

//define the field headers
$fieldHdrs = array(
    'Title',
    'Content',
    'featured_imate',
    'exhibit_description',
    'exhibit_video_link',
    'exhibit_inspiration',
    'exhibit_website',
    'exhibit_social_url',
    'maker_email',
    'maker_or_group_name',
    'maker_bio',
    'maker_photo',
    'maker_website',
    'maker_social_link',
    'faire_year',
    'faire_post',
    'maker_location_state',
    'maker_location_country',
    'main_category',
    'all_categories',
    'mf_exhibit_link', 
    'original_main_cat',
    'original_add_cats'
);


//build output data
$blogSql = "SELECT wp_mf_dir_entry.entry_id, title, status, project_photo, public_desc, project_video, inspiration, website, faire_year, 
        faire_name, state, country, social, main_category, category,
        (select GROUP_CONCAT(maker_type separator '|') 
         	from  wp_mf_dir_maker_to_entry 
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as type, 
                (select GROUP_CONCAT(email separator '|') 
         	from  wp_mf_dir_maker_to_entry
                 left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_email,
        (select GROUP_CONCAT(concat(first_name,' ',last_name) separator '|') 
         	from  wp_mf_dir_maker_to_entry 
            left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_or_group_name,
        (select GROUP_CONCAT(bio separator '|') 
         	from  wp_mf_dir_maker_to_entry 
            left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_bio,
        (select GROUP_CONCAT(photo separator '|') 
         	from  wp_mf_dir_maker_to_entry 
            left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_photo,
        (select GROUP_CONCAT(website separator '|') 
         	from  wp_mf_dir_maker_to_entry 
            left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_website,
        (select GROUP_CONCAT(social separator '|') 
         	from  wp_mf_dir_maker_to_entry 
            left outer join wp_mf_dir_maker on wp_mf_dir_maker.maker_id = wp_mf_dir_maker_to_entry.maker_id
         	where wp_mf_dir_maker_to_entry.entry_id=wp_mf_dir_entry.entry_id 
         		AND   wp_mf_dir_maker_to_entry.blog_id=wp_mf_dir_entry.blog_id
	         	AND maker_type <> 'contact'
        	group by wp_mf_dir_maker_to_entry.entry_id
        ) as maker_social_link
FROM `wp_mf_dir_entry` 
where faire_year='" . $faire_year . "' and status='Accepted'  
ORDER BY blog_id ASC;";

//maker fields separated with a |
$results = $wpdb->get_results($blogSql, ARRAY_A);

$buildOutput = true;
//loop thru data
foreach ($results as $data) {
    //exhibit social link
    $social_array = explode('|', $data['social']);
    $link_arr = array();
    if (is_array($social_array)) {
        foreach ($social_array as $social) {
            if (empty($social))  continue;

            $social_array = @unserialize($social);
            if (is_array($social_array)) {
                foreach ($social_array as $social_data) {
                    //the link is always the second item in the array, but the key can be named differently
                    $social_num = array_values($social_data);
                    $url = $social_num[1];

                    //validate we have a valid url 
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $link_arr[] = $url;
                    }
                }
            } else {
                $url = $social;
                //validate we have a valid url 
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $link_arr[] = $url;
                }
            }
        }
    }
    $exhibit_social = implode('|', $link_arr);

    //maker social link    
    if (
        $data['maker_social_link'] == 'N/A'  || $data['maker_social_link'] == 'n/a' ||
        $data['maker_social_link'] == 'None' || $data['maker_social_link'] == 'none'
    ) {
        $data['maker_social_link'] = '';
    }

    $maker_social_array = explode('|', $data['maker_social_link']);

    $link_arr = array();
    if (is_array($maker_social_array)) {
        foreach ($maker_social_array as $social) {            
            if (empty($social))  continue;

            $social_array = unserialize(html_entity_decode($social,ENT_QUOTES));

            if (is_array($social_array)) {                
                foreach ($social_array as $social_data) {                    
                    //the link is always the second item in the array, but the key can be named differently
                    $social_num = array_values($social_data);
                    $url = $social_num[1];

                    //validate we have a valid url 
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $link_arr[] = $url;                   
                    } else {
                        //echo("invalid URL(".$url.")<br/>");
                    }
                }
            } else {
                $url = $social;
                //validate we have a valid url 
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $link_arr[] = $url;
                }
            }
        }
    }

    $maker_social = implode('|', $link_arr);
    /*
    if(empty($maker_social) && !empty($data['maker_social_link'])){                       
        echo '<br/>';
        echo '$maker_social='.$maker_social.' and $data[maker_social_link]='.$data['maker_social_link'].'<br/>';
        die('we are missing the maker social data for '.$data['entry_id']);   
    }*/

    //convert the categories using the xfef table    
    $org_main_cat   = $data['main_category'];
    $org_add_cat    = $data['category'];
    $main_category  = category_xref($data['main_category'], $data['entry_id']);
    $all_categories = category_xref($data['category'], $data['entry_id']);
    if ($main_category == '' && $all_categories == '' && $data['main_category'] != '' && $data['category'] != '') {
        echo 'In faire ' . $data['faire_name'] . ' for exhibit ' . $data['title'] . ' categories are blank after xref. submitted categories are:<br/>';
        echo '    Main Category = ' . $data['main_category'] . '<br/>';
        echo '    Add  Categories = ' . $data['category'] . '<br/>';
        echo '<br/>';
        $buildOutput = false;
    }

    //build the output 
    $output[] = array(
        $data['title'],
        'Maker Names (not publicly visible) ' . $data['maker_or_group_name'],
        $data['project_photo'],
        $data['public_desc'],
        $data['project_video'],
        $data['inspiration'],
        $data['website'],
        $exhibit_social,
        ($data['maker_email'] != '|' ? $data['maker_email'] : ''),
        ($data['maker_or_group_name'] != '|' ? $data['maker_or_group_name'] : ''),
        $data['maker_bio'],
        $data['maker_photo'],
        ($data['maker_website'] != '|' ? $data['maker_website'] : ''),
        $maker_social,
        $data['faire_year'],
        $data['faire_name'],
        $data['state'],
        $data['country'],
        $main_category,
        $all_categories,
        'https://makerfaire.com/maker/entry/'.$data['entry_id'],
        $org_main_cat,
        $org_add_cat       
    );
}
if (!$buildOutput) {
    die();
}

// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="yb_bay-area_projects-' . $faire_year . '.csv"');

// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); //set as utf-8 encoding

// send the column headers
fputcsv($file, $fieldHdrs);

//loop thru blog data, and write output
foreach ($output as $out_line) {
    fputcsv($file, $out_line);
}

fclose($file);

function category_xref($category, $entry_id) {
    if ($category == '')   return $category;
    global $wpdb;
    global $buildOutput;

    //321 is delimeted by a |    
    $cat_arr = explode('|', $category);

    $xref_cat = array();
    foreach ($cat_arr as $category) {
        $from_cat = html_entity_decode($category);
        $sql = 'select to_cat from yearbook_category_conversion where from_cat="' . $from_cat . '"';
        $to_cat = $wpdb->get_var($sql);

        //Do not write the category if it is marked as delete
        if ($to_cat != 'DELETE') {
            if (is_null($to_cat)) {
                echo 'For Entry ' . $entry_id . ' ' . $from_cat . '->To Cat is null<br/>';
                $buildOutput = false;
                //die();
            }
            $xref_cat[] = $to_cat;
        }
    }

    //now let's translate the xref_cat back into a | separated string

    return implode('|', $xref_cat);
}
