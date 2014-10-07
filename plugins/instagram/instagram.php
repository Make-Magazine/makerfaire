<?php
/**
 * Instagram class for MAKE
 */
class Make_Instagram {

	/**
	 * THE CONSTRUCT.
	 *
	 * @return  void
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'load_resources' ), 30 );

	}

	/**
	 * Let's add all of our resouces to make our magic happen.
	 */
	public function load_resources() {

	}

	/**
	 * Load the Maker Faire data feed.
	 * Might update this at some point so that you can pass in any user.
	 *
	 * @return OBJECT Instagram response
	 */
	public function load_data() {

		$base_url = 'https://api.instagram.com/v1/tags/makerfaire/media/recent';
		$params = array(
			'access_token' => '227901753.1fb234f.b4dd629a578c47cda3f6fd579935190e',
			'count' => 20
		);

		// Build the URL
		$url = add_query_arg( $params, $base_url );

		// Request the data
		$json = wpcom_vip_file_get_contents( $url );

		// Parse the JSON
		$data = json_decode( $json );

		// Send it off...
		return $data->data;
	}

	public function show_images() {
		// TODO:	This whole function is a bit of a mess of entangled php and html.
		//			Would do a lot cleaner with some sort of templating engine.

		$ps = $this->load_data();

		// make sure $output exists, otherwise we may get an error in local environment
		if(!isset($output) || !is_string($output)) {
			$output = "";
		}


		$images_per_page = 3;
		$num_images = count($ps);
        $pages = array_chunk($ps, $images_per_page);
        $num_pages = count($pages);

		$output ="<div id=\"instagram-carousel\" class=\"carousel slide\" data-interval=\"3000\" data-ride=\"carousel\">"
        		.	"<div class=\"carousel-inner\">";
        // start outputting carousel pages
        foreach( $pages as $page ) {

			$output .=	"<div class=\"item\" >"
					.		"<div class=\"carousel row-fluid instagram-rows\">";
			// in each page, output $images_per_page images
			foreach( $page as $img ) {

					$output .=	"<div class=\"span4\">"
							.		"<a href=\"" . esc_url( $img->link ) . "\" class=\"instagram-link\">"
							.			"<div class=\"thumbnail\">"
							.				"<img style=\"max-width:180px; height: auto;\" src=\"" . esc_url( $img->images->standard_resolution->url ) . "\">"
							.				"<div class=\"caption insta-caption\">"
							.			 		wp_kses_post( Markdown( wp_trim_words( $img->caption->text, 10, '...' ) ) )
							.				"</div>"
							.			"</div>"
							.		"</a>"
							.	"</div>";
				}
			$output .=		"</div>"		// instagram-rows
					.	"</div>";	// item
		}

		// output carousel buttons
		$output .=   "</div>" // carousel-inner
					."<a class=\"carousel-control left\" href=\"#instagram-carousel\" data-slide=\"prev\">"
        			."    <span >‹</span>"
        			."</a>"
        			."<a class=\"carousel-control right\" href=\"#instagram-carousel\" data-slide=\"next\">"
        			."    <span >›</span>"
        			."</a>"
        		."</div>"
        		."<div class=\"spacer\"></div>";
		return $output;
	}

}

$instagram = new Make_Instagram();

function make_show_images() {
	$instagram = new Make_Instagram();
	return $instagram->show_images();
}

add_shortcode( 'show_instagram', 'make_show_images' );
