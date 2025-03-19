<?php
/**
 * Public Page Functions for Makers
 */

function mf_character_fixer( $str ) {
	$bad  = array( '""','&#039l', "\'", '&#8217;', '&#38;', '&amp;', '&quot;', '&#34;', '&#034;', '&#8211;', '&lt;', '&#8230;', 'u2018', 'u2019', 'u2014', 'u201d', 'u201c' );
	$good = array( "'",      "'",  "'",       "&",	   '&',		'\"',     '"',     '"',      '–',       '>',    '...',     "'",     "'",     "—",     '\"',    '\"'   );
	return str_replace($bad, $good, $str ?? '');
}

function mf_convert_newlines( $str, $replace = '<br />' ) {
	$s = array('nn-', ' nn', '.nn', '<br />rn');
	return str_replace($s, $replace, $str ?? '');
}

add_filter('get_avatar','mf_change_avatar_css');

function mf_change_avatar_css( $class ) {
	$class = str_replace("class='avatar", "class='media-object img-thumbnail pull-left avatar", $class ?? '') ;
	return $class;
}

/**
 * Function to spit out Featured Makers
 */
add_shortcode( 'featured', 'mf_featured_makers' );
function mf_featured_makers( $atts ) {
	$args = array(
		'meta_key'		=> '_ef_editorial_meta_checkbox_featured',
		'meta_value'	=> true,
		'post_type'		=> 'mf_form',
		'post_status'	=> 'accepted',
		'faire'			=> 'world-maker-faire-new-york-2014'
		);
	$args = wp_parse_args( $atts, $args );
	$query = new WP_Query( $args );
	$output = '<div id="featuredMakers" class="carousel slide"><div class="carousel-inner">';
	$i = 1;
	while ( $query->have_posts() ) :
	$query->the_post();
		$content = get_the_content();
		$json = json_decode( str_replace("\'", "'", $content ?? '') );
		if ($i == 1) {
			$output .= '<div class="item active ' . get_the_ID() . '">';
		} else {
			$output .= '<div class="item ' . get_the_ID() . '">';
		}
		$output .= '<a href="' . get_permalink( get_the_ID() ) . '">';
		if ( !empty( $json->presenter_photo[0] ) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->presenter_photo[0], 620, 400, true ) . '" class="Test"/>';
			$output .= '<!--Presenter Photo Array-->';
		} elseif (!empty( $json->project_photo ) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->project_photo, 620, 400, true ) . '" class="" />';
			$output .= '<!--Project Photo-->';
		} elseif ( !empty( $json->performer_photo ) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->performer_photo, 620, 400, true ) . '" class="" />';
			$output .= '<!--Performer Photo-->';
		} elseif ( !empty( $json->maker_photo ) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->maker_photo, 620, 400, true ) . '" class="" />';
			$output .= '<!--Maker Photo-->';
		} elseif ( !empty( $json->presentation_photo ) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->presentation_photo, 620, 400, true ) . '" class=""/>';
			$output .= '<!--Presentation Photo-->';
		} elseif ( isset( $json->presenter_photo) ) {
			$output .= '<img src="' . legacy_get_resized_remote_image_url( $json->presenter_photo, 620, 400, true ) . '" class=""/>';
			$output .= '<!--Presenter Photo-->';
		}
		$output .= '<div class="carousel-caption">';
		$output .= '<h4>' . get_the_title();
		if (!empty( $json->name ) ) {
			$output .= ' &mdash; ' . wp_kses_post( $json->name );
		}
		$output .= '</h4>';
		if ( !empty( $json->public_description ) ) {
			$output .= $json->public_description ? Markdown( wp_kses_post( $json->public_description ) ) : '';
		}
		$output .= '</div></a></div>';
		$i++;
	endwhile;
	$output .= '</div>
		<a class="left carousel-control" href="#featuredMakers" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span></a>
		</a>
		<a class="right carousel-control" href="#featuredMakers" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span></a></a>
	</div>';

	wp_reset_postdata();
	return $output;
}

function mf_add_custom_types( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_category() || $query->is_tag() || $query->is_author() || $query-> is_tax() ) && empty( $query->query_vars['suppress_filters'] ) ) {
		$query->set( 'post_type', array( 'post', 'mf_form' ));
		return $query;
	}
}
//add_filter( 'pre_get_posts', 'mf_add_custom_types' );

function mf_the_maker_image( $json ) {
	if (!empty($json->presentation_photo)) {
		echo '<img src="'. legacy_get_resized_remote_image_url( $json->presentation_photo, 140, 140 ) . '" class="" />';
	}
	if (!empty($json->project_photo)) {
		echo '<img src="'. legacy_get_resized_remote_image_url( $json->project_photo, 140, 140 ) . '" class="" />';
	}
	if (!empty($json->performer_photo)) {
		echo '<img src="'. legacy_get_resized_remote_image_url( $json->performer_photo, 140, 140 ) . '" class="" />';
	}
}


function mf_get_the_maker_image( $json ) {
	$output = null;
	if (!empty($json->presentation_photo)) {
		$output .= $json->presentation_photo;
	}
	if (!empty($json->project_photo)) {
		$output .= $json->project_photo;
	}
	if (!empty($json->performer_photo)) {
		$output .= $json->performer_photo;
	}
	return $output;
}


function the_mf_content() {
	if ( get_post_type() == 'mf_form' ) {
		$content = get_the_content();
		$json = json_decode( mf_convert_newlines( mf_character_fixer( str_replace( "\'", "'", $content ?? '' ) ) ) );
		echo '<div class="row"><div class="col-md-2">';
		mf_the_maker_image( $json );
		echo '</div><div class="col-md-6">';
		the_title( '<h3><a href="' . get_permalink() . '">', '</a></h3>' );
		echo ( isset( $json->form_type ) ) ? '<span class="label label-info">' . wp_kses_post( ucfirst( $json->form_type ) ) . '</span>' : '';
		if (!empty($json->public_description)) {
			echo Markdown( wp_kses_post( $json->public_description ) );
		} elseif ( !empty( $json->long_description ) ) {
			echo Markdown( wp_kses_post( $json->long_description ) );
		}
		echo '<ul class="unstyled">';
		$tags = get_the_terms( get_the_ID(), 'post_tag' );
		$cats = get_the_terms( get_the_ID(), 'category' );
		$terms = null;
		if ( is_array( $tags ) && is_array( $cats ) ) {
			$terms = array_merge($cats, $tags);
		} elseif ( is_array( $tags ) ) {
			$terms = $tags;
		} elseif ( is_array( $cats ) ) {
			$terms = $cats;
		}
		if (!empty($terms)) {
			echo '<li>Topics: ';
			$output = null;
			foreach ($terms as $idx => $term) {
				$output .= ', <a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			}
			echo substr( $output, 2 );
			echo '</li>';
		}
		echo '</ul>';
		echo '</div></div>';
	} else {
		the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
		the_content();
	}
}

function mf_get_terms ($term_types = array('category', 'post_tag'), $atts=array()) {
    $faire = ((isset($atts['faire'])) && ($atts['faire'] != '')) ? $atts['faire'] : '';
    $args = array(
			'hide_empty' => true, //unreliable
			'exclude'		=> array( '1' ),
			'show_tags' => '',
			'show_cats' => '',
			'faire' => $faire,
			);

		$args = wp_parse_args( $atts, $args );
		$topic_list = array('tags' => explode(',', $args['show_tags']), 'cats' => explode(',', $args['show_cats']) );
		$cats = get_terms( 'category', $args );

    if(in_array('post_tag', $term_types)) {
      $tag_args = $args; //Lets not override
      $tag_args['hide_empty'] = false;
      unset($tag_args['faire']);
      unset($tag_args['faire_url']);
      /*
      $tags = get_terms( 'post_tag' , $tag_args );

      if ((isset($topic_list['tags'])) && ($topic_list['tags'][0] !== '') && (count($topic_list['tags']) >= 1)) {
        foreach($tags as $tag_index => $tag) {
          if(!(in_array($tag->slug, $topic_list['tags']))) {
            unset($tags[$tag_index]);
          }
        }
      }
      */
			$tags = array();
		} else {
			$tags = array();
		}
		if(in_array('category', $term_types)) {
				$cat_args = $args; //Lets not override
				$cat_args['hide_empty'] = false;
				unset($cat_args['faire']);
				unset($cat_args['faire_url']);
				$cats = get_terms( 'category' , $cat_args );
        /*
        //??$cat_posts not set??
        if(($args['hide_empty']) && count($cat_posts) == 0) {
          unset($cats[$cat_index]);
        } else {
          //$cat->slug; not set???
          $topic_list['cats'][] = $cat->slug;
        }*/
        if ((isset($topic_list['cats'])) && ($topic_list['cats'][0] !== '') && (count($topic_list['cats']) >= 1)) {
          foreach($cats as $cat_index => $cat) {
            if(!( (in_array($cat->slug, $topic_list['cats'])) )) {
              unset($cats[$cat_index]);
            }
          }
        }

		} else {
				$cats = array();
		}

	 $cats_tags = array_merge($cats, $tags);

		usort($cats_tags, function($a, $b) { return strcmp($a->slug, $b->slug); } );
		return $cats_tags;
}
add_filter('the_title', function($title) {
	return str_replace('u03a9', '&#8486;', $title ?? '');
	}
);

add_filter('the_content', function($content) {
	return str_replace('u03a9', '&#8486;', $content ?? '');
}
);

/**
 * The Video/Image Gallery
 *
 * Wanted to extend our Bootstrap Slideshow so that you could put in Post IDs and get back a slideshow.
 * Basically the same thing that the default slideshow does, so why not use that!
 *
 * @since 1.0
 *
 * @param array $attr Attributes of the shortcode.
 * @return string HTML content to display gallery.
 */
function make_video_photo_gallery( $attr ) {

	$posts = explode( ',', $attr['ids'] );

	$rand = mt_rand( 0, get_the_ID() );

	global $post;

	$output = '<div id="myCarousel-' . $rand . '" class="carousel slide" data-interval=""><div class="carousel-inner">';
	$i = 0;

	foreach( $posts as $post ) {
		if ( isset($post) && strpos( $post, 'youtu' ) ) {
			$youtube = true;
		} else {
			$post = get_post( $post );
			setup_postdata( $post );
			$youtube = false;
		}
		$i++;

		if ($i == 1) {
			$output .= '<div class="item active">';
		} else {
			$output .= '<div class="item">';
		}
		if ( $youtube == false ) {
			if ( get_post_type() == 'video' ) {
				$url = get_post_meta( get_the_ID(), 'Link', true );
				$output .= do_shortcode('[youtube='. esc_url( $url ) .'&w=620]');
			} else {
				$output .= wp_get_attachment_image( get_the_ID(), 'medium' );
			}
			if (isset($post->post_title)) {
				$output .= '<div class="carousel-caption" style="position:relative;">';
				$output .= '<h4>' . get_the_title() . '</h4>';
				$output .= ( isset( $post->post_excerpt ) ) ? Markdown( wp_kses_post( $post->post_excerpt ) ) : '';
				$output .= '</div>';
			}
		} else {
			$output .= do_shortcode('[youtube='. esc_url( $post ) .'&w=620]');
		}
		$output .= '</div>';

	} //foreach
	wp_reset_postdata();
	$output .= '</div>
		<a class="topper left carousel-control" href="#myCarousel-' . $rand . '" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
			<span class="sr-only">Previous</span></a>
		</a>
		<a class="topper right carousel-control" href="#myCarousel-' . $rand . '" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
			<span class="sr-only">Next</span></a>
		</a>
	</div>';
	$output .= '<div class="clearfix"></div>';
	return $output;
}

add_shortcode( 'video_gallery', 'make_video_photo_gallery' );

