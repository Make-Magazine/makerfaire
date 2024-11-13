<?php
/*
Custom RSS Template - faire_ribbons
pulls ribbon data and entries for a specific faire based on form id
*/

$form_id = (isset($_GET['form_id'])?$_GET['form_id']:'');
if($form_id!=''){
    global $wpdb;
    
    $sql = 'SELECT entry_id, wp_mf_ribbons.project_name as ribbon_proj_name, date_created,
    (select meta_value from wp_gf_entry_meta where meta_key="151" and wp_gf_entry_meta.entry_id=wp_mf_ribbons.entry_id) as entry_title, 
    wp_mf_ribbons.project_photo as ribbon_proj_photo, 
    (select meta_value from wp_gf_entry_meta where meta_key="22" and wp_gf_entry_meta.entry_id=wp_mf_ribbons.entry_id) as entry_photo, 
    (select meta_value from wp_gf_entry_meta where meta_key="16" and wp_gf_entry_meta.entry_id=wp_mf_ribbons.entry_id) as entry_desc, 
    SUM(case when ribbonType = 0 then numRibbons else 0 end) as blue_ribbon_cnt 
    FROM `wp_mf_ribbons` 
    left outer join wp_gf_entry on wp_gf_entry.id=entry_id 
    where wp_gf_entry.form_id='.$form_id.' 
    group by entry_id ORDER BY rand() limit 10;';
    
    $entries = $wpdb->get_results($sql,ARRAY_A);
}else{
    $entries = array();
}

// randomize entries, as the feed seems static and not updating every hour
//shuffle($entries);

header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
        <?php do_action('rss2_ns'); ?>>
<channel>
        <title><?php bloginfo_rss('name'); ?> - Ribbon Winners Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php foreach($entries as $entry){ 
                $project_photo = (isset($entry['ribbon_proj_photo']) ? $entry['ribbon_proj_photo'] : $entry['entry_photo']);
                //for BA24, the single photo was changed to a multi image which messed things up a bit
                $photo = json_decode($project_photo);
                if (is_array($photo)) {
                    $project_photo = $photo[0];
                }               
                
            ?>
                <item>
                        <title><?php echo ($entry['ribbon_proj_name']!='' ? $entry['ribbon_proj_name'] : $entry['entry_title']); ?></title>
                        <link>https://makerfaire.com/maker/entry/<?php echo $entry['entry_id']; ?></link>
                        <pubDate><?php echo $entry['date_created']; ?></pubDate>
                        <dc:creator></dc:creator>
                        <guid isPermaLink="true"><?php the_guid(); ?></guid>                        
                        <description><![CDATA[<?php echo $entry['entry_desc']; ?>]]></description>
                        <content:encoded><![CDATA[<img src="<?php echo $project_photo; ?>" />]]></content:encoded>                        
                </item>
        <?php } ?>
</channel>
</rss>