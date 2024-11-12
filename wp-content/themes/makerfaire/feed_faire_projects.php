<?php
/*
Custom RSS Template - faire_projects
pulls form specific entries for a faire based on form id
*/
global $wpdb;

$form_id = (isset($_GET['form_id'])?$_GET['form_id']:'');
if($form_id!=''){
    $search_criteria = array(
        'status'        => 'active',
        'field_filters' => array(
            'mode' => 'all',
            array(
                'key'       => '303',
                'value'     => 'Accepted'
            ),
            array(
                'key'       => '304',
                'value'     => 'Featured Maker'
            ),
            array(
                'key'       => '304', 
                'operator'  => 'IS NOT', 
                'value'     => 'no-public-view'
            )
            
        )
    );
    $sorting = array( 'key' => '10', 'direction' => 'RAND' );
    $entries = GFAPI::get_entries($form_id, $search_criteria );
}else{
    $entries = array();
}
// randomize entries, as the feed seems static and not updating every hour
shuffle($entries);

$postCount = 5; // The number of posts to show in the feed
//$posts = query_posts('showposts=' . $postCount);
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
        <title><?php bloginfo_rss('name'); ?> - Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php foreach($entries as $entry){ 
                $project_photo = (isset($entry['22']) ? $entry['22'] : '');
                //for BA24, the single photo was changed to a multi image which messed things up a bit
                $photo = json_decode($project_photo);
                if (is_array($photo)) {
                    $project_photo = $photo[0];
                }               
            ?>
                <item>
                        <title><?php echo $entry['151']; ?></title>
                        <link>https://makerfaire.com/maker/entry/<?php echo $entry['id']; ?></link>
                        <pubDate><?php echo $entry['date_created']; ?></pubDate>
                        <dc:creator><?php echo $entry['96.3'].' '.$entry['96.6']; ?></dc:creator>
                        <guid isPermaLink="true"><?php the_guid(); ?></guid>
                        <description><![CDATA[<?php echo $entry['16']; ?>]]></description>
                        <content:encoded><![CDATA[<img src="<?php echo $project_photo; ?>" />]]></content:encoded>                        
                     
                </item>
        <?php } ?>
</channel>
</rss>