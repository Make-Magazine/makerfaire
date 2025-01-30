<?php
/*
Custom RSS Template - yb_projects
pulls yearbook project data and entries for a specific year
*/

$year  = (isset($_GET['faire_year'])?$_GET['faire_year']:date("Y",strtotime("-1 year")));
$count = (isset($_GET['count'])?$_GET['count']:10);

if($year!='') {
    $args = array(
        'post_type'		=> 'projects',
        'post_status'	=> 'publish',
        'orderby' 		=> 'rand',
        'order'			=> 'asc',
        'posts_per_page'=> $count,
        'meta_query' => array(
            array(
                'key' => 'faire_information_faire_year',
                'value' => $year,
                'compare' => '='
            )                   

        ),
    );
    $query = new WP_Query( $args );
    $posts = $query->posts;
}

// process data
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
        <title><?php bloginfo_rss('name'); ?> - <?php $year; ?> Yearbook Projects Feed</title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php foreach($posts as $post){ 
                $featured_photo = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'thumbnail' );
                $project_photo = (isset($featured_photo) ? $featured_photo : "https://make.co/wp-content/universal-assets/v2/images/default-card-image.jpg");
            ?>
                <item>
                        <title><?php echo htmlspecialchars($post->post_title); ?></title>
                        <link><?php echo $post->guid; ?></link>
                        <pubDate><?php echo $post->post_date; ?></pubDate>
                        <dc:creator></dc:creator>
                        <guid isPermaLink="true"><?php the_guid(); ?></guid>                        
                        <description><![CDATA[<?php echo htmlspecialchars($post->post_excerpt); ?>]]></description>
                        <content:encoded><![CDATA[<img src="<?php echo $project_photo; ?>" />]]></content:encoded>                        
                </item>
        <?php } ?>
</channel>
</rss>