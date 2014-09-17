<?php
WP_CLI::add_command( 'makerfaire', 'MAKE_CLI' );

class MAKE_CLI extends WP_CLI_Command {

	/**
	 * Add tags and cats to posts.
	 * Read the category and tag out of the JSON array, and then assign to the post.
	 *
	 * @subcommand cats
	 *
	 */
	public function copy_category_to_tag( $args, $assoc_args ) {

		$args = array(
			'posts_per_page'			=> 500,
			'post_type'					=> 'mf_form',
			'post_status'				=> 'any',
			'faire'						=> MF_CURRENT_FAIRE,

			// Prevent new posts from affecting the order
			'orderby' 					=> 'ID',
			'order' 					=> 'ASC',

			// Speed this up
			'no_found_rows' => true,
			'update_post_meta_cache'	=> false,
			'update_post_term_cache'	=> false,
		);

		// Get the first set of posts
		$query = new WP_Query( $args );

		while ( $query->have_posts() ) : $query->the_post();
			global $post;
			setup_postdata($post);
			WP_CLI::line( get_the_title() );
			$json_post = json_decode( str_replace( "\'", "'", get_the_content() ) );

			if ( isset( $json_post->cats ) ) {
				$catsobj = $json_post->cats;
			} else {
				$catsobj = null;
			}
			$cats = null;
			if ( is_array( $catsobj ) ) {
				$cats = $catsobj;
			} else {
        $cats = explode(',', trim($catsobj));
			}
			if ( !empty( $cats[0] ) ) {
				WP_CLI::line('Categories:');
				foreach ($cats as $cat) {
          $term = wpcom_vip_get_term_by('slug', $cat, 'category');
          if($term !== false) {
					$result = wp_set_object_terms( get_the_ID(), $cat, 'category', true );
					if ( !empty( $result ) ) {
						WP_CLI::success( $cat );
					} else {
						WP_CLI::warning( $cat );
            }
					}
				}
			}
			if ( isset( $json_post->tags ) ) {
				$tagsobj = $json_post->tags;
			} else {
				$tagsobj = null;
			}
			$tags = null;
			if ( is_array( $tagsobj ) ) {
				$tags = $tagsobj;
			} else {
				$tags = explode(',', $tagsobj);
			}
			if ( !empty( $tags[0] ) ) {
				WP_CLI::line('Tags:');
				foreach ($tags as $tag) {
					$term = wpcom_vip_get_term_by('slug', trim($tag), 'post_tag');
					if($term !== false) {
					$result = wp_set_object_terms( get_the_ID(), $tag, 'post_tag', true );
					if ( !empty( $result ) ) {
						WP_CLI::success( $tag );
					} else {
								WP_CLI::warning( $tag." not set" );
					}
					} else {
							$tag_split = explode('-',trim($tag));
							$tag_split_count = count($tag_split);
							if($tag_split_count > 2) {
								WP_CLI::line( $tag." un-separated in [".get_the_ID()."]!" );
									$ix = 0;
									do {
										$term_split = wpcom_vip_get_term_by('slug', $tag_split[$ix], 'post_tag');
										if($term_split !== false) { 
											wp_set_object_terms(get_the_ID(), $tag_split[$ix], 'post_tag', true);
											WP_CLI::success( $tag."({$tag_split[$ix]}) separated!" );
										} elseif(($ix+2) < $tag_split_count) {
												$tag_1 = $tag_split[$ix];
												$term_split = wpcom_vip_get_term_by('slug', $tag_1, 'post_tag');
												//WP_CLI::line( $tag."({$tag_split[$ix]}) maybe squished 1!" );
												if($term_split !== false) { 
													WP_CLI::success( $tag."({$tag_split[$ix]}) separated!" );
													wp_set_object_terms(get_the_ID(), $tag_split[$ix], 'post_tag', true);
													//no need to increment $ix twice if tag found
												} else {
													$tag_2 = $tag_split[$ix].'-'.$tag_split[$ix+1];
													//WP_CLI::line( $tag."({$tag_2}) maybe squished 2!" );
													$term_split = wpcom_vip_get_term_by('slug', $tag_2, 'post_tag');
													if($term_split !== false) { 
														WP_CLI::success( $tag."({$tag_2}) separated!" );
														wp_set_object_terms(get_the_ID(), $tag_2, 'post_tag', true);
														$ix++; //increment twice if tag found
													} else {
														$tag_3 = $tag_split[$ix].'-'.$tag_split[$ix+1].'-'.$tag_split[$ix+2];
														$term_split = wpcom_vip_get_term_by('slug', $tag_3, 'post_tag');
														if($term_split !== false) {
															WP_CLI::success( $tag."({$tag_3}) separated!" );
															wp_set_object_terms(get_the_ID(), $tag_3, 'post_tag', true);
															$ix = $ix + 2; //increment 3 times if tags found.
														} else { 
															WP_CLI::warning( $tag."({$tag_3}) not found!" );
				}
			}
												}
										} elseif(($ix+1) < $tag_split_count) {
											$tag_1 = $tag_split[$ix];
											$term_split = wpcom_vip_get_term_by('slug', $tag_1, 'post_tag');
											if($term_split !== false) { 
												WP_CLI::success( $tag."({$tag_split[$ix]}) separated!" );
												wp_set_object_terms(get_the_ID(), $tag_split[$ix], 'post_tag', true);
												//no need to increment $ix twice if tag found
											} else {
												$tag_2 = $tag_split[$ix].'-'.$tag_split[$ix+1];
												$term_split = wpcom_vip_get_term_by('slug', $tag_2, 'post_tag');
												if($term_split !== false) { 
													WP_CLI::success( $tag."({$tag_2}) separated!" );
													wp_set_object_terms(get_the_ID(), $tag_2, 'post_tag', true);
													$ix++; //increment twice if tag found
												} else {
													WP_CLI::warning( $tag."({$tag_2}) not found!" );
												}
											}
										} else {
											WP_CLI::warning( $tag."({$tag_split[$ix]}) not found!" );
										}
										$ix++;
									} while ($ix < $tag_split_count);
									$term_split = wpcom_vip_get_term_by('slug', $tag_split[0], 'post_tag');
							} else {
									WP_CLI::warning( $tag." not found" );
							}
					}
				}
			}

		WP_CLI::line( '' );
		endwhile;
		WP_CLI::success( "Boom!" );

	}
	/**
	 * Find hands on posts, and then assign them as a tag.
	 * Read the category and tag out of the JSON array, and then assign to the post.
	 *
	 * @subcommand hands
	 *
	 */
	public function hands_on( $args, $assoc_args ) {

		$args = array(
			'posts_per_page'			=> 2000,
			'post_type'					=> 'mf_form',
			'post_status'				=> 'any',

			// Prevent new posts from affecting the order
			'orderby' 					=> 'ID',
			'order' 					=> 'ASC',

			// Speed this up
			'no_found_rows' => true,
			'update_post_meta_cache'	=> false,
			'update_post_term_cache'	=> false,
		);

		// Get the first set of posts
		$query = new WP_Query( $args );
		WP_CLI::line( 'Number of posts found: ' . $query->found_posts );
		while ( $query->have_posts() ) : $query->the_post();
			global $post;
			setup_postdata($post);
			WP_CLI::line( get_the_title() );
			$json_post = json_decode( str_replace( "\'", "'", get_the_content() ) );

			if ( isset( $json_post->hands_on ) && ( $json_post->hands_on == 'Yes' ) ) {
				$result = wp_set_object_terms( get_the_ID(), 'hands-on', 'post_tag', true );
				WP_CLI::line('Hands On:');
				if ( !empty( $result ) ) {
					WP_CLI::success( get_the_title() );
				}
			}
		endwhile;
		WP_CLI::success( "Boom!" );

	}

	/**
	 * Inserts places from Make: Projects
	 *
	 * @subcommand places
	 *
	 */
	public function mf_location_import() {
		include_once 'placement.php';
		foreach ( $placement as $place ) {
			WP_CLI::line( $place['Name'] . ' ' . $place['Subarea'] );
			WP_CLI::line( get_the_title( $place['ID'] ) );

			// Do the area lookup:
			// Let's find the parent first, as there are grand children that have the same name:
			$parent = get_page_by_title( $place['Area'], OBJECT, 'location' );
			WP_CLI::line( 'Parent: ' . esc_html( $parent->post_title ) . ' [' . intval( $parent->ID ) . ']'  );

			// Let's get the children now...
			$args = array(
				'post_parent'	=> intval( $parent->ID ),
				'post_name'		=> sanitize_title( $place['Subarea'] ),
				'post_type'		=> 'location',
			);
			$posts = new WP_Query( $args );

			// We found a child!
			$child = $posts->posts[0];

			// Sanitize and save the locations
			if ( $child ) {
				WP_CLI::line( 'Found child: ' . esc_html( $child->post_title ) . ' [' . intval( $child->ID ) . ']' );
				$locations = array( intval( $parent->ID), intval( $child->ID ) );
				$loc = update_post_meta( intval( $place['ID'] ), 'faire_location', $locations );
			} elseif ( $parent ) {
				WP_CLI::line( 'Using parent: ' . esc_html( $parent->post_title ) . ' [' . intval( $parent->ID ) . ']' );
				$locations = array( intval( $parent->ID ) );
				$loc = update_post_meta( intval( $place['ID'] ), 'faire_location', $locations );
			} else {
				$loc = delete_post_meta( intval( $place['ID'] ), 'faire_location' );
			}
		}
	}


	/**
	 * Add makers to the maker custom post type from mf_form
	 *
	 * @subcommand makers
	 * @synopsis [--faire=<faire>]
	 *
	 */
	public function mf_create_makers( $args, $assoc_args ) {
		global $mfform;

		WP_CLI::line( 'Fetching Maker Faire Appliactions...' );

		// Go and get all accepted applications
		$applications = $mfform->get_all_forms( NULL, 'accepted' );

		foreach( $applications as $app ) {
			$content = (array) json_decode( str_replace( "\'", "'", $app->post_content ) );
			$mfei_records = wp_get_post_terms($app->ID, 'faire');

			// Get whatever faire taxonomy a maker already exists plus the current faire
			$count = 1;
			$mfei_record = '';
			foreach ( $mfei_records as $single_mfei_record ) {
				$mfei_record .= ( $count >= 2 ) ? ', ' . $single_mfei_record->slug : $single_mfei_record->slug;
				$count++;
			}

			// Setup a new array based on our application ID.
			$maker = array(
				'app_type'    => $content['form_type'],
				'app_id'   	  => $app->ID,
				'title'    	  => $content['name'],
				'content'  	  => ( ! is_array( $content[ $mfform->merge_fields( 'user_bio', $content['form_type'] ) ] ) ? $content[ $mfform->merge_fields( 'user_bio', $content['form_type'] ) ] : $content[ $mfform->merge_fields( 'user_bio', $content['form_type'] ) ][0] ),
				'email'    	  => $content['email'],
				'photo'    	  => $content[ $mfform->merge_fields( 'form_photo', $content['form_type'] ) ],
				'website'  	  => $content[ $mfform->merge_fields( 'project_website', $content['form_type'] ) ],
				'video'    	  => $content[ $mfform->merge_fields( 'project_video', $content['form_type'] ) ],
				'gigya'    	  => ( ! is_array( $content[ $mfform->merge_fields( 'user_gigya', $content['form_type'] ) ] ) ? $content[ $mfform->merge_fields( 'user_gigya', $content['form_type'] ) ] : $content[ $mfform->merge_fields( 'user_gigya', $content['form_type'] ) ][0] ),
				'mfei_record' => $mfei_record,
			);

			// Update or create our individual maker and report back.
			if ( ! empty( $maker['title'] ) )
				$maker_status = $mfform->add_to_maker_cpt( $maker );

			// Show the output of what we just did.
			if ( isset( $maker_status ) && is_array( $maker_status ) ) {
				foreach ( $maker_status['errors'] as $errors ) {
					WP_CLI::warning( $errors );
				}

				foreach ( $maker_status['success'] as $success ) {
					WP_CLI::success( $success );
				}
			}

			// Output some feed back..
			WP_CLI::line( ' | APP TYPE: ' . $maker['app_type'] );
			WP_CLI::line( ' | APP ID: ' . $maker['app_id'] );
			WP_CLI::line( ' | MAKER ID: ' . $maker_status['maker_id'] );
			WP_CLI::line( ' | NAME: ' . $maker['title'] );
			WP_CLI::line( ' | EMAIL: ' . $maker['email'] );
			WP_CLI::line( ' | PHOTO: ' . $maker['photo'] );
			WP_CLI::line( ' | WEBSITE: ' . $maker['website'] );
			WP_CLI::line( ' | VIDEO: ' . $maker['video'] );
			WP_CLI::line( ' | GUID: ' . $maker['gigya'] );

			// Leave some evidence we are done with this application.
			WP_CLI::line( ' ----------------------------------------' );
			WP_CLI::line( '' );

			// Process any additional makers that are setup in an application
			foreach ( array( 'exhibit' => 'm_maker_', 'presenter' => 'presenter' ) as $type => $prefix ) {

				// Check if the form field contains more than one maker name and email.
				if ( $content['form_type'] == $type && is_array( $content[ $prefix . 'name' ] ) && is_array( $content[ $prefix . 'email' ] ) ) {

					// Loop through each maker and count them
					for ( $i = 1; $i < count( $content[ $prefix . 'name' ] ); $i++ ) {

						// Lets add their credentials to a new row
						$maker['title'] = $content[ $prefix . 'name' ][ $i ];
						$maker['email'] = $content[ $prefix . 'email' ][ $i ];
						$mkaer['gigya'] = $content[ $mfform->merge_fields( 'user_gigya', $content['form_type'] ) ][ $i ];

						// Update or create our individual maker and report back.
						if ( ! empty( $maker['title'] ) )
							$maker_status = $mfform->add_to_maker_cpt( $maker );

						// Show the output of what we just did.
						if ( isset( $maker_status ) && is_array( $maker_status ) ) {
							foreach ( $maker_status['errors'] as $errors ) {
								WP_CLI::warning( $errors );
							}

							foreach ( $maker_status['success'] as $success ) {
								WP_CLI::success( $success );
							}
						}

						// Output some feed back for the additional makers
						WP_CLI::line( ' | APP TYPE: ' . $maker['app_type'] );
						WP_CLI::line( ' | APP ID: ' . $maker['app_id'] );
						WP_CLI::line( ' | MAKER ID: ' . $maker_status['maker_id'] );
						WP_CLI::line( ' | NAME: ' . $maker['title'] );
						WP_CLI::line( ' | EMAIL: ' . $maker['email'] );
						WP_CLI::line( ' | PHOTO: ' . $maker['photo'] );
						WP_CLI::line( ' | WEBSITE: ' . $maker['website'] );
						WP_CLI::line( ' | VIDEO: ' . $maker['video'] );
						WP_CLI::line( ' | GUID: ' . $maker['gigya'] );

						// Leave some evidence we are done with this application.
						WP_CLI::line( ' ----------------------------------------' );
						WP_CLI::line( '' );
					}
				}
			}
		}

		WP_CLI::success( 'SHAZAM! Job is DONE. Get a beer! Or a Dr. Pepper...' );
	}


	/**
	 * Delete all of the Makers in the makers custom post type
	 *
	 * @subcommand mjolnir
	 *
	 */
	public function mf_delete_makers( $args, $assoc_args ) {

		WP_CLI::line( '
                                                  zee.
        z**=.                                  .P"  $
         %   ^c                               z"   $
          b    %                             d    4"
          4     $            ....           4"    $
           F     L       .P"       "%.      $     $
           $     4     e"             "c    "     $
           $      F  z"                 *  4      $
           P      $ d                    3.$      $
           %      $d       ..eeeec..      *$      \'b
          d       $%   .e$*c d" ".z**$%.   $       $
          F       $  e" $   *F   $   ^F.db.$        b
         J        $d\" ^$   4b   $    $  3/$        *
         $        $*$   $c  P *P" * ."F  .$$         b
        4F        $ $c .EeP""      ^C$$..*F F        $
        J         $ $.*"-"*.      ."    "b$ F        \'r
        $         *"$   zc. ..  -"..-""\  $$%         $
        $          $      ..  L P  ebe    4$          $
        $          ^F   d%*$J%3 $ *$* "   4F          $
        *          4b       @   3 ^r      4$          $
        4          d$.          4         $3.        4F
         L         $$*.                  %$ $        J
         $        d $ $      -   ^.     P $c $       $
          r      z".$L L    .$%..*$    J  $P. *.    d
          "     z" P$$ ^%  z"      ".  L d$$"c "e  z%
           *  .P .*$$$  "*"   .$c        $$$$.b  ^$"
            *$  dL$$$$r ^4. ./" ""%..r"  $$$$$$J$e"
             *$b$$$$$$F        ""        $$$$$$$P
              ^$$$$$$$$                  $$$$$"
                 "*$$$$                  $"
                      \'                 4
                       *  $         .$  F
                        % 4F       .$  "
                          *$%     .$dr
                            *.    .*
                              ".." ' );

		$args = array(
			'posts_per_page' => 2000,
			'post_type' => 'maker',
			'post_status' => 'any',

			// Prevent new posts from affecting the order
			'orderby' => 'ID',
			'order' => 'ASC',

			// Speed this up
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Get the first set of posts
		$query = new WP_Query( $args );

		while ( $query->have_posts() ) : $query->the_post();

		$title = get_the_title( get_the_ID() );

		$del = wp_delete_post( get_the_ID() );

		if ( $del ) {
			WP_CLI::success( 'Deleted ' . $title );
		} else {
			WP_CLI::warning( 'Failed to delete ' . $title );
		}
		endwhile;
	}

	/**
	 * Assign all of the Maker Faire Applications to the Bay Area 2013 Faire
	 *
	 * @subcommand faires
	 *
	 */
	public function mf_assign_faire( $args, $assoc_args ) {

		$args = array(
			'posts_per_page' => 2000,
			'post_type' => 'mf_form',
			'post_status' => 'any',

			// Speed this up
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Get the first set of posts
		$query = new WP_Query( $args );

		while ( $query->have_posts() ) : $query->the_post();

		$title = get_the_title( get_the_ID() );

		$faire = wp_set_object_terms( get_the_ID(), 'Maker Faire Bay Area 2013', 'faire' );

		if ( is_array( $faire ) ) {
			WP_CLI::success( 'Updated ' . $title );
		} elseif (is_wp_error( $faire )) {
			WP_CLI::warning( 'Wasn\'t able to update ' . $title );
		}
		endwhile;
	}

	/**
	 * Assign the type of form to a taxonomy
	 *
	 * @subcommand types
	 *
	 */
	public function mf_application_type() {

		$args = array(
			'posts_per_page' => 2000,
			'post_type' => 'mf_form',
			'post_status' => 'any',

			// Prevent new posts from affecting the order
			'orderby' => 'ID',
			'order' => 'ASC',

			// Speed this up
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Get the first set of posts
		$query = new WP_Query( $args );

		while ( $query->have_posts() ) : $query->the_post();
		global $post;
			setup_postdata($post);
			//WP_CLI::line( get_the_title() );
			$json = json_decode( str_replace( array("\'", "u03a9", "u2019"), array("'", '&#8486;', '&rsquo;'), get_the_content() ) );
			//WP_CLI::line( $json->form_type );
			$type = wp_set_object_terms( get_the_ID(), $json->form_type, 'type' );
			if ( is_array( $type ) ) {
				WP_CLI::success( 'Updated ' . get_the_title() );
			} elseif (is_wp_error( $type )) {
				WP_CLI::warning( 'Wasn\'t able to update ' . get_the_title() );
			}
		endwhile;
		WP_CLI::success( "Boom!" );

	}

}