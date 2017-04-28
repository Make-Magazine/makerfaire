<?php get_header(); 

//determine the parent page slug
 $par_post = get_post($post->post_parent);
 $slug = $par_post->post_name;

$sql = 'select * from maker_checkin'; 
$results = $wpdb->get_results($sql);
if($wpdb->num_rows > 0){
    $count = $wpdb->num_rows;
    $i = 0;
   
   $markers = '';
   foreach($results as $row){   
       $i++;
       //$markers .=  "['$row->comments', $row->latitude,$row->longitude]";               
       $markers .=  '["'.$row->comments.'",'. $row->latitude.','.$row->longitude.']';               
       if($i<$count){
           $markers .=  ',';
       }
   }
   
}    
 
 
?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-16">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class(); ?>>					
                                            <?php the_content(); ?>
                                            <div class="clear"></div>
                                               <div id="map" style='height:500px;width:auto;'></div>
                                            <script>

                                        function initMap() {
                                          var map;
                                            var bounds = new google.maps.LatLngBounds();
                                            var mapOptions = {
                                                mapTypeId: 'roadmap'
                                            };

                                            // Display a map on the page
                                            map = new google.maps.Map(document.getElementById("map"), mapOptions);
                                                                                       
                                            // Loop through our array of markers & place each one on the map  
                                            var markers = [<?php echo $markers;?>]
                                            for( i = 0; i < markers.length; i++ ) {
                                                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                                                bounds.extend(position);
                                                marker = new google.maps.Marker({
                                                    position: position,
                                                    map: map,
                                                    title: markers[i][0]
                                                });

                                                // Automatically center the map fitting all markers on the screen
                                                map.fitBounds(bounds);
                                            }
                                        }

                                            </script>
                                            <script async defer
                                                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9JAfUnhjQ2zVG1PtN4_SYt9O1dXp5C1M&signed_in=true&callback=initMap"></script>

				</article>

			<?php endwhile; ?>

				<ul class="pager">

					<li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
					<li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>

				</ul>

			<?php else: ?>

				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

			<?php endif; ?>

		</div><!--Content-->

		<?php //get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>
