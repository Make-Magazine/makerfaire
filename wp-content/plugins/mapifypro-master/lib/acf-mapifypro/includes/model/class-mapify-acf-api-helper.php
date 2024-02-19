<?php

/**
 * The model class that contains ACF API helper static functions 
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifypro\Model;

/**
 * Class Mapify_ACF_API_Helper
 * 
 * @since    1.0.0
 */
class Mapify_ACF_API_Helper {

	/**
	 *  acf_get_grouped_posts
	 * 
	 *  UPDATED FROM THE ORIGINAL ACF FUNCTION.
	 *  BECAUSE WE NEED THE PLURAL POST TYPE LABELS, INSTEAD OF SINGULAR.
	 *
	 *  This function will return all posts grouped by post_type
	 *  This is handy for select settings
	 *
	 *  @type	function
	 *  @date	27/02/2014
	 *  @since	5.0.0
	 *
	 *  @param	$args (array)
	 *  @return	(array)
	 */

	public static function acf_get_grouped_posts( $args ) {		
		// vars
		$data = array();
		
		// defaults
		$args = wp_parse_args( $args, array(
			'posts_per_page'			=> -1,
			'paged'						=> 0,
			'post_type'					=> 'post',
			'orderby'					=> 'menu_order title',
			'order'						=> 'ASC',
			'post_status'				=> 'any',
			'suppress_filters'			=> false,
			'update_post_meta_cache'	=> false,
		));

		
		// find array of post_type
		$post_types = acf_get_array( $args['post_type'] );
		$post_types_labels = self::acf_get_pretty_post_types($post_types);
		$is_single_post_type = ( count($post_types) == 1 );		
		
		// attachment doesn't work if it is the only item in an array
		if( $is_single_post_type ) {
			$args['post_type'] = reset($post_types);
		}		
		
		// add filter to orderby post type
		if( !$is_single_post_type ) {
			add_filter('posts_orderby', '_acf_orderby_post_type', 10, 2);
		}		
		
		// get posts
		$posts = get_posts( $args );		
		
		// remove this filter (only once)
		if( !$is_single_post_type ) {
			remove_filter('posts_orderby', '_acf_orderby_post_type', 10, 2);
		}		
		
		// loop
		foreach( $post_types as $post_type ) {			
			// vars
			$this_posts = array();
			$this_group = array();			
			
			// populate $this_posts
			foreach( $posts as $post ) {
				if( $post->post_type == $post_type ) {
					$this_posts[] = $post;
				}
			}			
			
			// bail early if no posts for this post type
			if( empty($this_posts) ) continue;
			
			
			// sort into hierachial order!
			// this will fail if a search has taken place because parents wont exist
			if( is_post_type_hierarchical($post_type) && empty($args['s'])) {
				
				// vars
				$post_id = $this_posts[0]->ID;
				$parent_id = acf_maybe_get($args, 'post_parent', 0);
				$offset = 0;
				$length = count($this_posts);				
				
				// get all posts from this post type
				$all_posts = get_posts(array_merge($args, array(
					'posts_per_page'	=> -1,
					'paged'				=> 0,
					'post_type'			=> $post_type
				)));				
				
				// find starting point (offset)
				foreach( $all_posts as $i => $post ) {
					if( $post->ID == $post_id ) {
						$offset = $i;
						break;
					}
				}				
				
				// order posts
				$ordered_posts = get_page_children($parent_id, $all_posts);
								
				// compare aray lengths
				// if $ordered_posts is smaller than $all_posts, WP has lost posts during the get_page_children() function
				// this is possible when get_post( $args ) filter out parents (via taxonomy, meta and other search parameters) 
				if( count($ordered_posts) == count($all_posts) ) {
					$this_posts = array_slice($ordered_posts, $offset, $length);
				}
				
			}
						
			// populate $this_posts
			foreach( $this_posts as $post ) {
				$this_group[ $post->ID ] = $post;
			}
						
			// group by post type
			$label = $post_types_labels[ $post_type ];
			$data[ $label ] = $this_group;						
		}		
		
		// return
		return $data;		
	}

	/**
	 * acf_get_pretty_post_types
	 * 
	 * @param array $post_types WordPress post types.
	 * @return array Post type labels.
	 */
	public static function acf_get_pretty_post_types( $post_types = array() ) {	
		// get post types
		if( empty($post_types) ) {			
			// get all custom post types
			$post_types = acf_get_post_types();			
		}		
		
		// get labels
		$ref = array();
		$r = array();
		
		foreach( $post_types as $post_type ) {			
			// vars
			$label = self::acf_get_post_type_label($post_type);
						
			// append to r
			$r[ $post_type ] = $label;
						
			// increase counter
			if( !isset($ref[ $label ]) ) {				
				$ref[ $label ] = 0;				
			}
			
			$ref[ $label ]++;
		}
				
		// get slugs
		foreach( array_keys($r) as $i ) {			
			// vars
			$post_type = $r[ $i ];
			
			if( $ref[ $post_type ] > 1 ) {				
				$r[ $i ] .= ' (' . $i . ')';				
			}			
		}
				
		// return
		return $r;		
	}

	/**
	 *  acf_get_post_type_label
	 *
	 *  This function will return a pretty label for a specific post_type
	 *
	 *  @type	function
	 *  @date	5/07/2016
	 *  @since	5.4.0
	 *
	 *  @param	$post_type (string)
	 *  @return	(string)
	 */
	public static function acf_get_post_type_label( $post_type ) {		
		// vars
		$label = $post_type;		
			
		// check that object exists
		// - case exists when importing field group from another install and post type does not exist
		if( post_type_exists($post_type) ) {			
			$obj = get_post_type_object($post_type);
			$label = $obj->label;	
		}		
		
		// return
		return $label;
	}

}