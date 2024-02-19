<?php

use \Acf_Mapifypro\Model\Mapify_ACF_API_Helper;
use \Acf_Mapifypro\Model\Mapify_Map;

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if( ! class_exists('Acf_Mapifypro_Map_Locations_Relationship_Fields') ) :

class Acf_Mapifypro_Map_Locations_Relationship_Fields extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct( $settings ) {
		
		// vars
		$this->name     = 'mapify_map_locations_relationship';
		$this->label    = __( "Map Locations Relationship", 'acf-mapifypro' );
		$this->category = 'Mapify';
		$this->defaults = array(
			'post_type'     => mpfy_get_supported_post_types(),
			'taxonomy'      => array(),
			'min'           => 0,
			'max'           => 0,
			'filters'       => array('search', 'post_type', 'taxonomy'),
			'elements'      => array(),
			'return_format' => 'object'
		);

		$this->settings = $settings;

		// extra
		add_action('wp_ajax_acf/fields/mapify_map_locations_relationship/query', array($this, 'ajax_query'));
		add_action('wp_ajax_nopriv_acf/fields/mapify_map_locations_relationship/query',	array($this, 'ajax_query'));		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		global $post_type, $post;

		$url     = $this->settings['url'];
		$version = $this->settings['version'];

		// should be loaded only on post type map
		if ( 'map' === $post_type ) {			
			wp_register_script( 'mpfy-map-locations-relationship', "{$url}admin/js/script-map-locations-relationship.js", array( 'jquery', 'acf-input' ), $version );
			wp_enqueue_script( 'mpfy-map-locations-relationship' );
		}

		// localize
		acf_localize_text(array(
			//'Minimum values reached ( {min} values )'	=> __('Minimum values reached ( {min} values )', 'acf'),
			'Maximum values reached ( {max} values )'	=> __('Maximum values reached ( {max} values )', 'acf'),
			'Loading'									=> __('Loading', 'acf'),
			'No matches found'							=> __('No matches found', 'acf'),
		));

	}
	
	
	/*
	*  ajax_query
	*
	*  description
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_query() {
		
		// validate
		if( !acf_verify_ajax() ) die();
		
		
		// get choices
		$response = $this->get_ajax_query( $_POST );
		
		
		// return
		acf_send_ajax_results($response);
			
	}
	
	
	/*
	*  get_ajax_query
	*
	*  This function will return an array of data formatted for use in a select2 AJAX response
	*
	*  @type	function
	*  @date	15/10/2014
	*  @since	5.0.9
	*
	*  @param	$options (array)
	*  @return	(array)
	*/
	
	function get_ajax_query( $options = array() ) {
		
   		// defaults
   		$options = wp_parse_args($options, array(
			'post_id'		=> 0,
			's'				=> '',
			'field_key'		=> '',
			'paged'			=> 1,
			'post_type'		=> '',
			'taxonomy'		=> ''
		));
		
		
		// load field
		$field = acf_get_field( $options['field_key'] );
		if( !$field ) return false;
		
		
		// vars
   		$results = array();
		$args = array();
		$s = false;
		$is_search = false;
		
		
   		// paged
   		$args['posts_per_page'] = 20;
   		$args['paged'] = intval($options['paged']);
   		
   		
   		// search
		if( $options['s'] !== '' ) {
			
			// strip slashes (search may be integer)
			$s = wp_unslash( strval($options['s']) );
			
			
			// update vars
			$args['s'] = $s;
			$is_search = true;
			
		}
		
		
		// post_type
		if( !empty($options['post_type']) ) {
			
			$args['post_type'] = acf_get_array( $options['post_type'] );
		
		} elseif( !empty($field['post_type']) ) {
		
			$args['post_type'] = acf_get_array( $field['post_type'] );
			
		} else {
			
			$args['post_type'] = acf_get_post_types();
			
		}
		
		
		// taxonomy
		if( !empty($options['taxonomy']) ) {
			
			// vars
			$term = acf_decode_taxonomy_term($options['taxonomy']);
			
			
			// tax query
			$args['tax_query'] = array();
			
			
			// append
			$args['tax_query'][] = array(
				'taxonomy'	=> $term['taxonomy'],
				'field'		=> 'slug',
				'terms'		=> $term['term'],
			);
			
			
		} elseif( !empty($field['taxonomy']) ) {
			
			// vars
			$terms = acf_decode_taxonomy_terms( $field['taxonomy'] );
			
			
			// append to $args
			$args['tax_query'] = array(
				'relation' => 'OR',
			);
			
			
			// now create the tax queries
			foreach( $terms as $k => $v ) {
			
				$args['tax_query'][] = array(
					'taxonomy'	=> $k,
					'field'		=> 'slug',
					'terms'		=> $v,
				);
				
			}
			
		}	
		
		/**
		 * Whether the user is looking for a post type or all post types.
		 * On this section we are limited the post type to MapifyPro supported post types,
		 * which is the default value is `map-location` and `post`.
		 */
		if ( 1 === count( $args['post_type'] ) ) {
			if ( 'map-location' === $args['post_type'][0] ) {
				// get MapifyPro map locations data
				$groups = $this->get_map_location_data( $args );
			} else {
				// get MapifyPro supported post types data
				$groups = $this->get_supported_post_types_location_data( $args, $args['post_type'] );
			}
		} else {
			// get MapifyPro supported post types
			$post_types        = mpfy_get_supported_post_types();
			$args['post_type'] = $post_types;
			$map_location_key  = array_search( 'map-location', $post_types );
			
			unset( $post_types[ $map_location_key ] );
			
			// get MapifyPro supported post types data
			$supported_post_types_data = $this->get_supported_post_types_location_data( $args, $post_types );
			
			// get MapifyPro map locations data
			$map_locations_data = $this->get_map_location_data( $args );
			
			// merge all data
			$groups = array_merge( $map_locations_data, $supported_post_types_data );
		}
		

		// bail early if no posts
		if( empty($groups) ) return false;
				
		// loop
		foreach( array_keys($groups) as $group_title ) {
			
			// vars
			$posts = acf_extract_var( $groups, $group_title );
			
			
			// data
			$data = array(
				'text'		=> $group_title,
				'children'	=> array()
			);
			
			
			// convert post objects to post titles
			foreach( array_keys($posts) as $post_id ) {
				
				$posts[ $post_id ] = $this->get_post_title( $posts[ $post_id ], $field, $options['post_id'] );
				
			}
			
			
			// order posts by search
			if( $is_search && empty($args['orderby']) && isset($args['s']) ) {
				
				$posts = acf_order_by_search( $posts, $args['s'] );
				
			}
			
			
			// append to $data
			foreach( array_keys($posts) as $post_id ) {
				
				$data['children'][] = $this->get_post_result( $post_id, $posts[ $post_id ]);
				
			}
			
			
			// append to $results
			$results[] = $data;
			
		}
		
		
		// add as optgroup or results
		if( count($args['post_type']) == 1 ) {
			
			$results = $results[0]['children'];
			
		}
		
		
		// vars
		$response = array(
			'results'	=> $results,
			'limit'		=> $args['posts_per_page']
		);
		
		
		// return
		return $response;
			
	}

	/**
	 * Get MapifyPro map locations data
	 */
	function get_map_location_data( $args ) {		
		$new_args              = $args;
		$new_args['post_type'] = 'map-location';

		return Mapify_ACF_API_Helper::acf_get_grouped_posts( $new_args );
	}

	/**
	 * Get MapifyPro supported post types data
	 */
	function get_supported_post_types_location_data( $args, $post_types ) {
		$new_args               = $args;
		$new_args['post_type']  = $post_types;
		$new_args['meta_query'] = array(
			array(
				'key'     => 'mapify_blog_post_as_map_location',
				'value'   => '1',
				'compare' => '=',
			),
		);

		return Mapify_ACF_API_Helper::acf_get_grouped_posts( $new_args );
	}
	
	
	/*
	*  get_post_result
	*
	*  This function will return an array containing id, text and maybe description data
	*
	*  @type	function
	*  @date	7/07/2016
	*  @since	5.4.0
	*
	*  @param	$id (mixed)
	*  @param	$text (string)
	*  @return	(array)
	*/
	
	function get_post_result( $id, $text ) {
		
		// vars
		$result = array(
			'id'	=> $id,
			'text'	=> $text
		);
		
		
		// return
		return $result;
			
	}
	
	
	/*
	*  get_post_title
	*
	*  This function returns the HTML for a result
	*
	*  @type	function
	*  @date	1/11/2013
	*  @since	5.0.0
	*
	*  @param	$post (object)
	*  @param	$field (array)
	*  @param	$post_id (int) the post_id to which this value is saved to
	*  @return	(string)
	*/
	
	function get_post_title( $post, $field, $post_id = 0, $is_search = 0 ) {
		
		// get post_id
		if( !$post_id ) $post_id = acf_get_form_data('post_id');
		
		
		// vars
		$title = acf_get_post_title( $post, $is_search );
		
		
		// featured_image
		if( acf_in_array('featured_image', $field['elements']) ) {
			
			// vars
			$class = 'thumbnail';
			$thumbnail = acf_get_post_thumbnail($post->ID, array(17, 17));
			
			
			// icon
			if( $thumbnail['type'] == 'icon' ) {
				
				$class .= ' -' . $thumbnail['type'];
				
			}
			
			
			// append
			$title = '<div class="' . $class . '">' . $thumbnail['html'] . '</div>' . $title;
			
		}
		
		
		// return
		return $title;
		
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$post_type = acf_get_array( $field['post_type'] );
		$taxonomy = acf_get_array( $field['taxonomy'] );
		$filters = acf_get_array( $field['filters'] );
		
		// filters
		$filter_count = count($filters);
		$filter_post_type_choices = array();
		$filter_taxonomy_choices = array();
		
		// post_type filter
		if( in_array('post_type', $filters) ) {
			
			$post_types_selection = Mapify_ACF_API_Helper::acf_get_pretty_post_types( $post_type );
			$filter_post_type_choices = array(
				''	=> __('Filter by Post Type...', 'acf')
			);

			foreach ( $post_types_selection as $key => $value ) {
				$filter_post_type_choices[ $key ] = "&nbsp;-&nbsp;$value";
			}
			
		}
		
		// taxonomy filter
		if( in_array('taxonomy', $filters) ) {
			
			$term_choices = array();
			$filter_taxonomy_choices = array(
				''	=> __('Select taxonomy', 'acf')
			);
			
			// check for specific taxonomy setting
			if( $taxonomy ) {
				$terms = acf_get_encoded_terms( $taxonomy );
				$term_choices = acf_get_choices_from_terms( $terms, 'slug' );
			
			// if no terms were specified, find all terms
			} else {
				
				// restrict taxonomies by the post_type selected
				$term_args = array();
				if( $post_type ) {
					$term_args['taxonomy'] = acf_get_taxonomies(array(
						'post_type'	=> $post_type
					));
				}
				
				// get terms
				$terms = acf_get_grouped_terms( $term_args );
				$term_choices = acf_get_choices_from_grouped_terms( $terms, 'slug' );
			}
			
			// append term choices
			$filter_taxonomy_choices = $filter_taxonomy_choices + $term_choices;
			
		}
		
		// div attributes
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> "acf-relationship {$field['class']}",
			'data-min'			=> $field['min'],
			'data-max'			=> $field['max'],
			'data-s'			=> '',
			'data-paged'		=> 1,
			'data-post_type'	=> '',
			'data-taxonomy'		=> '',
		);
		
		?>
<div <?php acf_esc_attr_e($atts); ?>>
	
	<?php acf_hidden_input( array('name' => $field['name'], 'value' => '') ); ?>
	
	<?php 
	
	/* filters */	
	if( $filter_count ): ?>
	<div class="filters -f<?php echo esc_attr($filter_count); ?>">
		<?php 
	
		/* search */	
		if( in_array('search', $filters) ): ?>
		<div class="filter -search">
			<?php acf_text_input( array('placeholder' => __("Search...",'acf'), 'data-filter' => 's') ); ?>
		</div>
		<?php endif; 
		
		
		/* post_type */	
		if( in_array('post_type', $filters) ): ?>
		<div class="filter -post_type">
			<?php acf_select_input( array('choices' => $filter_post_type_choices, 'data-filter' => 'post_type') ); ?>
		</div>
		<?php endif; 
		
		
		/* post_type */	
		if( in_array('taxonomy', $filters) ): ?>
		<div class="filter -taxonomy">
			<?php acf_select_input( array('choices' => $filter_taxonomy_choices, 'data-filter' => 'taxonomy') ); ?>
		</div>
		<?php endif; ?>		
	</div>
	<?php endif; ?>
	
	<div class="selection">
		<div class="choices">
			<ul class="acf-bl list choices-list"></ul>
		</div>
		<div class="values">
			<ul class="acf-bl list values-list">
			<?php if( !empty($field['value']) ): 
				
				// get posts
				$posts = acf_get_posts(array(
					'post__in' => $field['value'],
					'post_type'	=> $field['post_type']
				));
				
				
				// loop
				foreach( $posts as $post ): ?>
					<li>
						<?php acf_hidden_input( array('name' => $field['name'].'[]', 'value' => $post->ID) ); ?>
						<span data-id="<?php echo esc_attr($post->ID); ?>" class="acf-rel-item">
							<?php echo acf_esc_html( $this->get_post_title( $post, $field ) ); ?>
							<a href="#" class="acf-icon -minus small dark" data-name="remove_item"></a>
						</span>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
		<?php
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// vars
		$field['min'] = empty($field['min']) ? '' : $field['min'];
		$field['max'] = empty($field['max']) ? '' : $field['max'];
		
		
		// post_type
		acf_render_field_setting( $field, array(
			'label'			=> __('Filter by Post Type','acf'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'post_type',
			'choices'		=> acf_get_pretty_post_types(),
			'multiple'		=> 1,
			'ui'			=> 1,
			'allow_null'	=> 1,
			'placeholder'	=> __("All post types",'acf'),
		));
		
		
		// taxonomy
		acf_render_field_setting( $field, array(
			'label'			=> __('Filter by Taxonomy','acf'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'taxonomy',
			'choices'		=> acf_get_taxonomy_terms(),
			'multiple'		=> 1,
			'ui'			=> 1,
			'allow_null'	=> 1,
			'placeholder'	=> __("All taxonomies",'acf'),
		));
		
		
		// filters
		acf_render_field_setting( $field, array(
			'label'			=> __('Filters','acf'),
			'instructions'	=> '',
			'type'			=> 'checkbox',
			'name'			=> 'filters',
			'choices'		=> array(
				'search'		=> __("Search",'acf'),
				'post_type'		=> __("Post Type",'acf'),
				'taxonomy'		=> __("Taxonomy",'acf'),
			),
		));
		
		
		// filters
		acf_render_field_setting( $field, array(
			'label'			=> __('Elements','acf'),
			'instructions'	=> __('Selected elements will be displayed in each result','acf'),
			'type'			=> 'checkbox',
			'name'			=> 'elements',
			'choices'		=> array(
				'featured_image'	=> __("Featured Image",'acf'),
			),
		));
		
		
		// min
		acf_render_field_setting( $field, array(
			'label'			=> __('Minimum posts','acf'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
		));
		
		
		// max
		acf_render_field_setting( $field, array(
			'label'			=> __('Maximum posts','acf'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
		));
		
		
		
		
		// return_format
		acf_render_field_setting( $field, array(
			'label'			=> __('Return Format','acf'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'choices'		=> array(
				'object'		=> __("Post Object",'acf'),
				'id'			=> __("Post ID",'acf'),
			),
			'layout'	=>	'horizontal',
		));
		
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) {
		
			return $value;
			
		}
		
		
		// force value to array
		$value = acf_get_array( $value );
		
		
		// convert to int
		$value = array_map('intval', $value);
		
		
		// load posts if needed
		if( $field['return_format'] == 'object' ) {
			
			// get posts
			$value = acf_get_posts(array(
				'post__in' => $value,
				'post_type'	=> $field['post_type']
			));
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// default
		if( empty($value) || !is_array($value) ) {
		
			$value = array();
			
		}
		
		
		// min
		if( count($value) < $field['min'] ) {
		
			$valid = _n( '%s requires at least %s selection', '%s requires at least %s selections', $field['min'], 'acf' );
			$valid = sprintf( $valid, $field['label'], $field['min'] );
			
		}
		
		
		// return		
		return $valid;
		
	}

	/**
	 * load_value()
	 *
	 * This filter is applied to the $value after it is loaded from the db
	 *
	 * @type    filter
	 * @since   3.6
	 * @date    23/01/13
	 *
	 * @param   $value (mixed) the value found in the database
	 * @param   $post_id (mixed) the $post_id from which the value was loaded
	 * @param   $field (array) the field array holding all the field options
	 * @return  $value
	 */
		
	function load_value( $value, $post_id, $field ) {
	
		$map = new Mapify_Map( $post_id );
		
		// get map locations
		return $map->get_location_ids();
		
	}
		
	
	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// Bail early if no value.
		if( empty($value) ) {
			return $value;
		}
		
		// Format array of values.
		// - ensure each value is an id.
		// - Parse each id as string for SQL LIKE queries.
		if( acf_is_sequential_array($value) ) {
			$value = array_map('acf_idval', $value);
			$value = array_map('strval', $value);
		
		// Parse single value for id.
		} else {
			$value = acf_idval( $value );
		}

		// set map locations
		$map = new Mapify_Map( $post_id );
		$map->set_location_ids( $value );
		
		// Return value.
		return $value;
	}
		
}

endif; // class_exists check

?>
