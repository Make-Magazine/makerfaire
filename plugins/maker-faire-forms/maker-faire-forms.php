<?php
/*
Plugin Name: Maker Faire Form
Plugin URI: http://insourcecode.com
Description: Select the type of form and embed into post or page.
Version: 0.0.2
Author: inSourceCode
Author URI: http://insourcecode.com
License:  GPL2
*/

/*
* MAKER_FAIRE_FORM CLASS
* =====================================================================*/
class MAKER_FAIRE_FORM {

	/**
	 * The name of the current faire. This should be the title, not a slug. E.G. World Maker Faire New York 2014
	 * This variable is used in many locations that display this name. This is simply a one location to update many kind of deal.
	 * @var string
	 */
	public $faire_friendly_name = 'World Maker Faire New York 2014';


	/**
	 * The deadline of the commercial maker payment
	 * We'll store this into a variable for easier editing in the future instead of digging through this jungle of an application.
	 * @var string
	 */
	public $commercial_maker_deadline = 'September 5th';


	/*
	* All Form Keys with 1/0 for Required Field
	* =====================================================================*/
	var $fields = array(
		'exhibit' => array(
			's1' => array(
				'project_name'          => 1,
				'private_description'   => 1,
				'public_description'    => 1,
				'project_photo'         => 1,
				'project_photo_thumb'   => 0,
				'project_website'       => 0,
				'project_video'         => 0,
				'food'                  => 1,
				'food_details'          => 0,

				'org_type'              => 1,
				'large_non_profit'      => 0,

				'sales'                 => 0,
				'sales_details'         => 0,
				'crowdsource_funding'	=> 0,
				'cf_details'			=> 0,
				'booth_size'            => 1,
				'booth_size_details'    => 0,
				'tables_chairs'         => 1,
				'tables_chairs_details' => 0,
				'layout'                => 0,
				'activity'              => 0,
				'placement'             => 0,
				'booth_location'        => 1,
				'booth_options'         => 0,
				'lighting'              => 1,
				'noise'                 => 1,
				'power'                 => 1,
				'what_are_you_powering' => 0,
				'amps'                  => 0,
				'amps_details'          => 0,
				'internet'              => 1,
				'radio'                 => 1,
				'radio_frequency'       => 0,
				'radio_details'         => 0,
				'fire'                  => 0,
				'hands_on'              => 0,
				'solicit'               => 0,
				'safety_details'        => 0,
				),
			's2' => array(
				'email'               => 1,
				'name'                => 1,
				'maker'               => 1,

				'maker_name'          => 0,
				'maker_email'         => 0,
				'maker_bio'           => 0,
				'maker_twitter'		  => 0,
				'maker_photo'         => 0,
				'maker_photo_thumb'   => 0,

				'm_maker_name'        => 0,
				'm_maker_email'       => 0,
				'm_maker_photo'       => 0,
				'm_maker_photo_thumb' => 0,
				'm_maker_twitter'     => 0,
				'm_maker_bio'         => 0,
				'm_maker_gigyaid'     => 0,

				'group_name'          => 0,
				'group_bio'           => 0,
				'group_twitter'		  => 0,
				'group_photo'         => 0,
				'group_photo_thumb'   => 0,
				'group_website'       => 0,

				'phone1'              => 1,
				'phone1_type'         => 1,
				'phone2'              => 0,
				'phone2_type'         => 0,

				'private_address'     => 1,
				'private_address2'    => 0,
				'private_city'        => 1,
				'private_state'       => 0,
				'private_zip'         => 0,
				'private_country'     => 1
				),
			's3' => array(
				'supporting_documents' => 0,
				'references'           => 0,
				'referrals'            => 0,
				'hear_about'           => 0,
				'first_time'           => 1,
				'anything_else'        => 0,
			)
		),
		'performer' => array(
			's1' => array(
				'performer_name'        => 1,
				'private_description'   => 1,
				'length'                => 0,
				'public_description'    => 1,
				'performer_website'     => 0,
				'performer_photo'       => 1,
				'performer_photo_thumb' => 0,
				'performer_video'       => 0,
				'performance_time'      => 1,
				'schedule_comments'     => 0,
				'equipment'             => 0,
				'performer_count'       => 1,
				'compensation_type'     => 0,
				'compensation'          => 0,
				'guest_tickets'         => 1,
			),
			's2' => array(
				'name'             => 1,
				'email'            => 1,

				'phone1'           => 1,
				'phone1_type'      => 1,
				'phone2'           => 0,
				'phone2_type'      => 0,
				'onsite_phone'     => 0,

				'private_address'  => 1,
				'private_address2' => 0,
				'private_city'     => 1,
				'private_state'    => 0,
				'private_zip'      => 0,
				'private_country'  => 1,
			),
			's3' => array(
				'first_makerfaire' => 0,
				'exhibit'          => 0,
				'promotion'        => 0,
				'additional_info'  => 0,
			),
		),
		'presenter' => array(
			's1' => array(
				'presentation_type'        => 1,
				'private_description'	   => 0,
				'length_presentation'	   => 0,
				'availablity'              => 0,
				'special_requests'         => 0,
				'presentation_name'        => 1,
				'short_description'        => 1,
				'long_description'		   => 1,
				'presentation_photo'       => 1,
				'presentation_photo_thumb' => 0,
				'presentation_website'     => 0,
				'video'                    => 0,
			),
			's2' => array(
				'name'                  => 1,
				'email'                 => 1,
				'phone1'                => 1,
				'phone1_type'           => 0,
				'phone2'                => 0,
				'phone2_type'           => 0,

				'private_address'       => 1,
				'private_address2'      => 0,
				'private_city'          => 1,
				'private_state'         => 0,
				'private_zip'           => 0,
				'private_country'       => 1,

				'presenter_name'         => 1,
				'presenter_email'        => 1,
				'presenter_bio'          => 1,
				'presenter_org'          => 0,
				'presenter_twitter'		 => 0,
				'presenter_previous'	 => 0,
				'presenter_title'        => 0,
				'presenter_onsite_phone' => 1,
				'presenter_photo'        => 1,
				'presenter_photo_thumb'  => 0,
				'presenter_gigyaid'      => 0,
			),
			's3' => array(
				'maker_ask'        => 0,
				'first_makerfaire' => 0,
				'exhibit'          => 0,
				'promotion'        => 0,
				'additional_info'  => 0,
			),
		),
		'makerprofile' => array()
	);

	/*
	* Default Form Type
	* =====================================================================*/
	var $type        = 'exhibit';

	/**
	 * Faire internal code
	 */
	var $maker_faire = '2014_newyork';

	/*
	* Default Form Values
	* =====================================================================*/
	var $form        = array(
		'id'          => 0,
		'uid'         => 0,
		'maker_faire' => '2014_newyork', // For whatever lame reason we cannot use the maker_faire variable here.
		'tags'        => array(),
		'cats'        => array()
	);

	/*
	* Post Meta save for Columns View
	* =====================================================================*/
	var $post_meta;

	/*
	* Init the MakerFaire Class
	*
	* @access public
	* =====================================================================*/
	public function __construct() {
		add_action( 'init',                           array( &$this, 'init' ) );
		add_action( 'admin_init', 		 			  array( &$this, 'admin_init' ) );
		add_action( 'admin_menu',					  array( &$this, 'add_menus' ) );
		add_action( 'add_meta_boxes', 	              array( &$this, 'add_meta_boxes' ) );
		add_action( 'save_post',			          array( &$this, 'update_post' ) );


		add_shortcode( 'mfform', 					  array( &$this, 'shortcode_handler' ) );

		add_action( 'wp_ajax_nopriv_mfform_step', 	  array( &$this, 'ajax_handler' ) );
		add_action( 'wp_ajax_mfform_step', 			  array( &$this, 'ajax_handler' ) );

		add_action( 'wp_ajax_mf_delete_scheduled_event', array( &$this, 'mf_delete_scheduled_event' ) );

		add_action( 'wp_ajax_nopriv_mfform_getforms', array( &$this, 'ajax_getforms' ) );
		add_action( 'wp_ajax_mfform_getforms', 		  array( &$this, 'ajax_getforms' ) );

		add_action( 'wp_enqueue_scripts',             array( &$this, 'enqueue' ) );
		add_action( 'admin_enqueue_scripts',          array( &$this, 'admin_enqueue' ) );
	}

	/*
	* Callback for INIT - HOOK
	*
	* @access public
	* =====================================================================*/
	public function init() {

		$labels = array(
			'name'               => _x( 'Applications', 'post type general name' ),
			'singular_name'      => _x( 'Application', 'post type singular name' ),
			'add_new'            => _x( 'Add Application', 'mf_form' ),
			'add_new_item'       => __( 'Add New Application' ),
			'edit_item'          => __( 'Edit Application' ),
			'new_item'           => __( 'New Application' ),
			'all_items'          => __( 'All Applications' ),
			'view_item'          => __( 'View Application' ),
			'search_items'       => __( 'Search Applications' ),
			'not_found'          => __( 'No forms found' ),
			'not_found_in_trash' => __( 'No forms found in Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Applications' )
		);

		$args =   array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'revisions' ),
			'taxonomies'		 => array( 'category', 'post_tag' ),
			'rewrite'            => array( 'slug' => 'makers', 'with_front' => false )
		);

		//REGISTER MF FORM CUSTOM POST TYPE
		register_post_type( 'mf_form', $args );
	}


	/*
	* Callback for ADMIN INIT - HOOK
	*
	* @access public
	* =====================================================================*/
	public function admin_init() {

		// Our new form export tool
		if ( isset( $_GET['form_export'] ) ) {

			// Contain our export featurs into the proper array our build form export function wants
			$options = array(
				'filters' => $_GET,
			);

			// Remove the page, post_type, form export and nonce data.
			$options['filters'] = array_slice( $options['filters'], 4 );

			if ( isset( $options['filters']['makers'] ) && $options['filters']['makers'] == 'true' ) {
				// Export our makers
				$this->build_maker_export( $options );
			} else {
				// Default, Make sure you get some cookies and milk.
				$this->build_form_export( $options );
			}
		}

		// Maker Export
		if ( isset( $_GET['maker_csv'] ) ) {
			$options['filters']['faire'] = MF_CURRENT_FAIRE; // Only export the latest faire
			$this->build_comments_export( $options );
		}

		// Exhibit Signage
		if ( isset( $_GET['exhibit_signage_csv'] ) )
			$this->build_exhibit_signage_export( esc_url( $_GET['exhibit_signage_csv'] ) );

		// Clear Reprint Signs
		if ( isset( $_GET['exhibit_signage_clear_reprint'] ) )
			$this->reset_signage_reprints( esc_attr( $_GET['exhibit_signage_clear_reprint'] ) );

		// Presentation Export
		if ( isset( $_GET['presentation_csv'] ) )
			$this->build_presentation_exports( esc_attr( $_GET['presentation_csv'] ) );


		$this->upgrade();

		//ADD COLUMNS TO MF FORM SUBMISSION LIST
		add_filter( 'manage_mf_form_posts_columns',         array( &$this, 'columns' ) );
		add_filter( 'manage_edit-mf_form_sortable_columns', array( &$this, 'columns_sortable' ) );
		add_action( 'manage_mf_form_posts_custom_column',   array( &$this, 'custom_columns' ), 10, 2 );


		add_filter( 'request', array( &$this, 'columns_orderby' ) );

		//REMOVE ABILITY TO EDIT FORM
		remove_meta_box( 'submitdiv', 'mf_form', 'side' );
	}


	/*
	* Set Columns for the MakerFaire Custom Post Type
	*
	* @access public
	* @param array $cs Default columns
	* =====================================================================*/
	public function columns( $cs ) {
		unset(
			$cs['author'],
			$cs['title'],
			$cs['date'],
			$cs['editorial-metadata-featured'],
			$cs['editorial-metadata-temporary-location']
		);

		$ncs = array(
			'status'                                => 'Status',
			'faire'									=> 'Faire',
			'type'                                  => 'Application Type',
			'id'                                    => 'Project ID',
			'title'                                 => 'Project Name',
			'maker'                                 => 'Maker',
			'editorial-metadata-featured'           => 'Featured Maker',
			'editorial-metadata-temporary-location' => 'Temporary Location',
			'date'                                  => 'Date'
		);
		return array_merge( $cs, $ncs );
	}


	/*
	* Set Sortable Columns
	*
	* @access public
	* @param array $cs Default sortable columns
	* =====================================================================*/
	public function columns_sortable( $cs ) {
		$cs['id']                                     = 'ID';
		$cs['type']                                   = 'type';
		$cs['maker']                                  = 'maker';
		$cs['editorial-metadata-featured']            = 'featured';
		$cs['editorial-metadata-temporary-location']  = 'temp-location';

		return $cs;
	}


	/*
	* Allow for sorting of custom colulmns
	*
	* @access public
	* @param array $vars The variables for the orderby
	* =====================================================================*/
	public function columns_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && $vars['post_type'] == 'mf_form' ) {

			switch( $vars['orderby'] ) {
				case 'temp-location' :
					$vars = array_merge( $vars, array(
						'meta_key' => '_ef_editorial_meta_text_temporary-location',
						'orderby'  => 'meta_value'
					) );
				break;
				case 'featured' :
					$vars = array_merge( $vars, array(
						'meta_key' => '_ef_editorial_meta_checkbox_featured',
						'orderby'  => 'meta_value'
					) );
				break;
				case 'type' :
					$vars = array_merge( $vars, array(
						'meta_key' => '_mf_form_type',
						'orderby'  => 'meta_value'
					) );
				break;
				case 'maker' :
					$vars = array_merge( $vars, array(
						'meta_key' => '_mf_maker_name',
						'orderby'  => 'meta_value'
					) );
				break;
			}
		}
		return $vars;
	}


	/*
	* Adds Custom Columns to the MakerFaire Custom Post Type
	*
	* @access public
	* @param string $c Column ID
	* @param integer $pid Post ID
	* =====================================================================*/
	public function custom_columns( $c, $pid ) {
		global $post;
		$data = json_decode( str_replace( "\'", "'", $post->post_content ) );

		if( is_null( $this->post_meta ) || !isset( $this->post_meta[$pid] ) )
			$this->post_meta = array( $pid => get_post_meta( $pid ) );

		switch ( $c ) {
			case 'editorial-metadata-featured':
				echo isset( $this->post_meta[$pid]['_ef_editorial_meta_checkbox_featured'] ) && $this->post_meta[$pid]['_ef_editorial_meta_checkbox_featured'][0] == 1 ? 'Yes' : 'No';
				break;
			case 'editorial-metadata-temporary-location':
				echo isset( $this->post_meta[$pid]['_ef_editorial_meta_text_temporary-location'] ) ? esc_html( $this->post_meta[$pid]['_ef_editorial_meta_text_temporary-location'][0] ) : '';
				break;
			case 'id':
				echo '<strong>' . absint( $pid ) . '</strong>';
				break;
			case 'type':
				echo ( !empty( $data->form_type ) ) ? '<strong>' . esc_html( $data->form_type ) . '</strong>' : 'Not Set';
				break;
			case 'maker':
				echo ( !empty( $data->name ) ) ? esc_html( $data->name ) : '';
				break;
			case 'faire':
				echo ( ! empty( $data->maker_faire ) ) ? esc_html( $data->maker_faire ) : 'undefined';
		}
	}


	/*
	* Adds submenu pages to the MakerFaire Custom Post Type
	*
	* @access public
	* =====================================================================*/
	public function add_menus() {
		add_submenu_page( 'edit.php?post_type=mf_form', 'Project Images', 'Project Images', 'edit_others_posts', 'mf_project_images', array( &$this, 'show_project_images' ) );
		add_submenu_page( 'edit.php?post_type=mf_form', 'Reports', 'Reports', 'edit_others_posts', 'mf_reports', array( &$this, 'show_reports_page' ) );
	}


	/*
	* Inits all included meta boxes for the MakerFaire Custom Post Type
	*
	* @access public
	* =====================================================================*/
	public function add_meta_boxes() {
		global $post;

		if ( isset( $post->post_status ) && ( $post->post_status != 'auto-draft' ) ) {
			add_meta_box( 'mf_summary',   'Summary',   array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default' );
			add_meta_box( 'mf_details',   'Details',   array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default' );
			add_meta_box( 'mf_logistics', 'Edit Form', array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default' );
			if ( has_term( 'presenter', 'type' ) ) {
				add_meta_box( 'mf_presenter_promos', 'Eventbrite Promo Codes', array( &$this, 'meta_box' ), 'mf_form', 'side', 'default' );
			}
			add_meta_box( 'mf_presenter_promos', 'Eventbrite Promo Codes', array( &$this, 'meta_box' ), 'event-items', 'side', 'default' );
		} else {
			add_meta_box( 'mf_form_type', 'Application Type',  array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default' );
			add_meta_box( 'mf_exhibit',   'Exhibit Details',   array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default', array( 'type'=>'exhibit' ) );
			add_meta_box( 'mf_performer', 'Performer Details', array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default', array( 'type'=>'performer' ) );
			add_meta_box( 'mf_presenter', 'Presenter Details', array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default', array( 'type'=>'presenter' ) );
		}

		add_meta_box( 'mf_save', 'Edit Application', array( &$this, 'meta_box' ), 'mf_form', 'side', 'default' );
		add_meta_box( 'mf_logs', 'Status Changes &amp; Notifications Sent', array( &$this, 'meta_box' ), 'mf_form', 'normal', 'default' );

		if ( isset( $post->post_status ) && ( $post->post_status != 'auto-draft' ) ) {
			add_meta_box( 'mf_maker_contact', 'Contact Info', array( &$this, 'meta_box' ), 'mf_form', 'side', 'default' );
			add_meta_box( 'mf_maker_info', 'Makers', array( &$this, 'meta_box' ), 'mf_form', 'side', 'default' );
		}
	}


	/*
	* Controller for all backend Meta Boxes
	*
	* @access public
	* @param object $post Current Post to be edited
	* @param array $args Include arguments for the meta box.
	* =====================================================================*/
	public function meta_box( $post, $args ) {

		if ( get_post_type() == 'maker' ) {
			$id = get_post_meta( get_the_ID(), 'mfei_record', true );
			$post = get_post( absint( $id ) );
		}

		$bad  = array( '&#039;', "\'", '&#8217;', '&#38;', '&#038;', '&#34;', '&#034;', '&#8211;', '&lt;', '&#8230;', 'u2018', 'u2019', 'u2014', 'u2022', 'u2026', '<br />rn' );
		$good = array( "'",      "'",  "'",       "&",     "&",      '"',     '"',      '–',       '>',    '...',     "'",     "'",     "—",     '•',     '...',   '<br />' );
		$data = json_decode( str_replace( $bad, $good, $post->post_content ) );

		if( $args['id'] == 'mf_save' ) {
			$wf = get_post_meta( $post->ID, '_mf_waiting_for', true );
			$wf = $wf == '' ? 'Waiting on?' : $wf; ?>
				<div class="submitbox" id="submitpost">
					<?php wp_nonce_field( 'mf_nonce', 'mf_submit_nonce' ); ?>
					<?php $post_status_types    = array( 'in-progress', 'proposed', 'more-info', 'wait-list', 'accepted', 'rejected', 'cancelled' ); ?>
					<input name="mf_form" type="hidden" value="1" />
					<input id="id" name="id" type="hidden" value="<?php echo $post->post_status != 'auto-draft' ? intval( $post->ID ) : 0; ?>" />
					<div id="misc-publishing-actions">
						<div class="misc-pub-section">
							<label for="post_status" style="display: inline;">Status:</label>
							<select id="post_status" name="post_status">
								<?php
									// Get an array of our select options for setting the post status
									global $edit_flow;

									if ( is_object( $edit_flow ) && is_a( $edit_flow, 'edit_flow') ) :
										foreach ( $edit_flow->custom_status->get_custom_statuses() as $cso ) { ?>
											<option value="<?php echo esc_attr( $cso->slug ); ?>" title="<?php echo esc_attr( $cso->description ); ?>" <?php selected( $cso->slug, $post->post_status ); ?>>
												<?php echo esc_attr( $cso->name ); ?>
											</option>
										<?php }
									endif;
								?>
							</select>
							<div id="mff-post-status-display" style="padding-top:5px;"></div>
						</div>
					</div><!--[END #misc-pub-section]-->
					<div id="major-publishing-actions">
						<div id="mfquestionwait"></div>
						<div id="delete-action">
							<a href="<?php echo get_delete_post_link( $post->ID ); ?>" class="submitdelete deletion">Move to Trash</a>
						</div>
						<div id="publishing-action">
							<a href="<?php echo post_permalink( $post->ID ) ?>" class="button button-large" style="margin-bottom:8px;" title="<?php esc_attr( the_title() ); ?>">View Post</a><br>
							<input type="submit" value="Save Application" accesskey="p" id="publish" class="button button-primary button-large" name="save">
						</div>
					</div>
					<div class="clear"></div>
					<p class="misc-pub-section num-revisions">
					<?php
						$kids = wp_get_post_revisions( get_the_ID() );
						if ( count( $kids ) > 1 ) :
							printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( absint( count( $kids ) ) ) . '</b>' );
							$kid = array_splice( $kids, 0 );
					?>
						<a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( absint( $kid[0]->ID ) ) ); ?>"><?php _ex( 'Browse', 'revisions' ); ?></a>
					<?php endif; ?>
					</p>
					<script type="text/javascript">
						(function($){
							$(document).ready(function() {

								// Check if the post status is set, if not, go ahead and run this code.
								if ( ! $('#mff-post-status-display').length )
									return true;

								$('#post_status').change(function() {
									var postStatus = $('#post_status');
									var origPost = $('#original_post_status');
									var postStatusSelected = $('option:selected', postStatus);
									var postStatusDisplay = $('#mff-post-status-display');
									var origPostStatus =origPost.val();
									var postStatusSelectedTitle=postStatusSelected.attr('title');
									var newStatus=postStatus.val();

									console.log('change: %s -> %s (%s)', origPostStatus, newStatus, postStatusSelected.val() );

									postStatusDisplay.html(postStatusSelectedTitle);

									if ( newStatus == 'more-info' ) {
										$('#mfquestionwait').html('<textarea name="mf_waitingquestion" id="mf_waitingquestion" style="width:99%"><?php echo html_entity_decode( esc_textarea( $wf ) ); ?></textarea>');
									} else {
										$('#mfquestionwait').html('');
									}

								});

								setTimeout(function(){$('#post_status').change();}, 10);
							});
						})(jQuery.noConflict());
					</script>
				</div>
		<?php } elseif ( $args['id'] == 'mf_logs' ) {
			$post_status_log = get_post_meta( $post->ID, '_mf_log', true );

			if ( ! is_array( $post_status_log ) || $post_status_log == false || sizeof( $post_status_log ) == 0 ) {
				$post_status_log = array();
				$post_status_log[] = mysql2date('m/d/y h:i a', $post->post_date) .' - Proposed';
			}

			echo '<ul>';
			foreach ( array_reverse( $post_status_log ) as $log ) {
				echo '<li>'.esc_html( $log ).'</li>';
			}
			echo "</ul>";
		} elseif ( $args['id'] == 'mf_summary' ) {

			$jdb_success = get_post_meta( $post->ID, 'mf_jdb_sync', true );

			if ( $jdb_success == '' ) {
				$jdb_fail = get_post_meta( $post->ID, 'mf_jdb_sync_fail', true );
				$jdb      = '[FAILED] : N/A';
				if ( $jdb_success == '' )
					$jdb  = '[FAILED] : ' . date( 'M jS, Y g:i A', $jdb_fail - ( 7 * 3600 ) );
			} else {
				$jdb = '[SUCCESS] : ' . date( 'M jS, Y g:i A', $jdb_success - ( 7 * 3600 ) );
			}

			$photo = $data->{ $this->merge_fields( 'form_photo', $data->form_type ) };

			// Check if we are loading the public description or a short description
			if ( isset( $data->public_description ) ) {
				$main_description = $data->public_description;
			} else if ( isset( $data->short_description ) ) {
				$main_description = $data->short_description;
			} else {
				$main_description = '';
			}
			?>
			<h1><?php echo esc_html( $data->{ $this->merge_fields( 'project_name', $data->form_type ) } ); ?></h1>
			<input name="form_type" type="hidden" value="<?php echo esc_attr( $data->form_type ); ?>" />
			<table style="width:100%">
				<tr>
					<td style="width:210px;" valign="top"><img src="<?php echo esc_url( wpcom_vip_get_resized_remote_image_url( $photo, 200, 200 ) ); ?>" width="200" height="200" /></td>
					<td valign="top">
						<p><?php echo Markdown ( stripslashes( wp_filter_post_kses( mf_convert_newlines( $main_description, "\n" ) ) ) ); ?></p>
						<table style="width:100%">
							<tr>
								<td style="width:80px;" valign="top"><strong>Type:</strong></td>
								<td valign="top"><?php echo esc_attr( ucfirst( $data->form_type ) ); ?></td>
							</tr>
							<tr>
								<td style="width:80px;" valign="top"><strong>Status:</strong></td>
								<td valign="top"><?php echo esc_attr( $post->post_status ); ?></td>
							</tr>
							<?php if( $data->form_type == 'exhibit' ) : ?>
								<tr>
									<td valign="top"><strong>Commercial Maker:</strong></td>
									<td valign="top"><?php echo esc_html( $data->sales == '' ? 'N/A' : $data->sales ); ?></td>
								</tr>
							<?php endif; ?>
							<?php
								$wkey = $this->merge_fields( 'project_website', $data->form_type );
								$vkey = $this->merge_fields( 'project_video', $data->form_type );
							?>
							<tr>
								<td style="width:80px;" valign="top"><strong>Website:</strong></td>
								<td valign="top"><a href="<?php echo esc_url( $data->{ $wkey } ); ?>" target="_blank"><?php echo esc_url( $data->{ $wkey } ); ?></a></td>
							</tr>
							<tr>
							<td valign="top"><strong>Video:</strong></td>
								<?php
								  echo ( isset( $data->project_video ) ) ? '<td valign="top"><a href="' . esc_url( $data->project_video ) . '" target="_blank">' . esc_url( $data->project_video ) . '</a></td>' : null ;
								  echo ( isset( $data->performer_video ) ) ? '<td valign="top"><a href="' . esc_url( $data->performer_video ) . '" target="_blank">' . esc_url( $data->performer_video ) . '</a></td>' : null ;
								  echo ( isset( $data->video ) ) ? '<td valign="top"><a href="' . esc_url( $data->video ) . '" target="_blank">' . esc_url( $data->video ) . '</a></td>' : '<td></td>' ;
								?>
							</tr>
							<?php if( $data->form_type == 'exhibit' ) : ?>
								<tr>
									<td style="width:80px;" valign="top"><strong>Supporting Documents:</strong></td>
									<td valign="top"><a href="<?php echo esc_url( $data->supporting_documents ); ?>" target="_blank"><?php echo esc_url( $data->supporting_documents ); ?></a></td>
								</tr>
								<tr>
									<td style="width:80px;" valign="top"><strong>Layout:</strong></td>
									<td valign="top"><a href="<?php echo esc_url( $data->layout ); ?>" target="_blank"><?php echo esc_url( $data->layout ); ?></a></td>
								</tr>
							<?php endif; ?>
							<?php

								// Store the current application ID so we can return it within the loop
								$parent_post_id = get_the_ID();

								$args = array(
									'post_type'		=> 'event-items',
									'orderby' 		=> 'meta_value',
									'meta_key'		=> 'mfei_start',
									'order'			=> 'asc',
									'posts_per_page'=> '30',
									'meta_query' 	=> array(
										array(
											'key' 	=> 'mfei_record',
											'value'	=> $post->ID
									   ),
									)
								);
								$get_events = new WP_Query( $args );

								// Check that we have returned our query of events, if not, give the option to schedule the event
								if ( $get_events->found_posts >= 1 ) { ?>
									<tr>
										<td style="width:80px;"><strong>Scheduled:</strong></td>
										<td valign="top">Yes</a>
									</tr>
									<?php // Loop through theme
									while ( $get_events->have_posts() ) : $get_events->the_post();

										// Get an array of our event data
										$event_record = get_post_meta( get_the_ID() );

										// Setup the edit URL and add an edit link to the admin area
										$edit_event_url = get_edit_post_link();

										// Show the location this event is setup for
										if ( !empty( $event_record['faire_location'][0] ) ) : ?>
											<tr>
												<td style="width:80px;" valign="top"><strong>Location:</strong></td>
												<td valign="top"><?php echo esc_html( get_the_title( intval( unserialize( $event_record['faire_location'][0] )[0] ) ) ); ?></td>
											</tr>
										<?php endif;

										// Check that fields are set, and display them as needed.
										if ( ! empty( $event_record['mfei_day'][0] ) ) : ?>
											<tr <?php post_class(); ?>>
												<td style="width:80px;" valign="top"><strong>Day:</strong></td>
												<td valign="top"><?php echo esc_html( $event_record['mfei_day'][0] ); ?></td>
											</tr>
										<?php endif; if ( ! empty( $event_record['mfei_start'][0] ) ) : ?>
											<tr class="<?php post_class(); ?>">
												<td style="width:80px;" valign="top"><strong>Start Time:</strong></td>
												<td valign="top"><?php echo esc_html( $event_record['mfei_start'][0] ); ?></td>
											</tr>
										<?php endif; if ( ! empty( $event_record['mfei_stop'][0] ) ) : ?>
											<tr class="<?php post_class(); ?>">
												<td style="width:80px;" valign="top"><strong>Stop Time:</strong></td>
												<td valign="top"><?php echo esc_html( $event_record['mfei_stop'][0] ); ?></td>
											</tr>
										<?php endif; if ( ! empty( $event_record['mfei_schedule_completed'][0] ) ) : ?>
											<tr class="<?php post_class(); ?>">
												<td style="width:80px;" valign="top"><strong>Schedule Completed:</strong></td>
												<td valign="top"><?php echo esc_html( $event_record['mfei_schedule_completed'][0] ); ?></td>
											</tr>
										<?php endif; ?>
											<tr class="<?php post_class(); ?>">
												<td valign="top"><strong>Edit</strong></td>
												<td>
													<a href="<?php echo esc_url( $edit_event_url ); ?>" class="button" target="_blank">Edit the Time and Date</a> <button href="" class="deleteme button-small button-secondary delete" data-key="mfei_record" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_scheduled_post' ) ); ?>" data-postid="<?php echo esc_attr( get_the_id() ); ?>" data-value="<?php echo esc_attr( $event_record['mfei_record'][0] ); ?>" title="">Delete Scheduled Event</button>
												</td>
											</tr>

									<?php endwhile; ?>
									<tr>
										<td style="width:80px;" valign="top"><strong>Schedule:</strong></a></td>
										<td valign="top"><a href="<?php echo admin_url(); ?>post-new.php?post_type=event-items&amp;refer_id=<?php echo absint( $parent_post_id ); ?>">Schedule Another Event</a></td>
									</tr>
								<?php } else { ?>
									<tr>
										<td style="width:80px;" valign="top"><strong>Scheduled:</strong></a></td>
										<td valign="top"><a href="<?php echo admin_url(); ?>post-new.php?post_type=event-items&amp;refer_id=<?php echo get_the_ID(); ?>">Schedule This Event</a></td>
									</tr>
								<?php }
							?>
							<tr>
								<td style="width:80px;" valign="middle"><strong>MF Video:</strong></td>
								<td valign="top">
									<input type="text" id="video-coverage" name="video-coverage" style="width:25%;" value="<?php echo !empty( $event_record['mfei_coverage'][0] ) ? esc_url( $event_record['mfei_coverage'][0] ) : ''; ?>" />
									<input type="hidden" name="event-id" value="<?php echo get_the_ID(); ?>" />
								</td>
							</tr>
							<tr>
								<td valign="top"><strong>JDB Sync:</strong></td>
								<td valign="top"><?php echo esc_html( $jdb ); ?></td>
							</tr>
						</table>
						</td>
					</tr>
					</table>
				<?php
		} elseif ( $args['id'] == 'mf_details' ) { // Details Metabox

			// Why do we have so much crap in this method? Let's break this up... because I'm losing my mind here :P
			$this->app_details( $post, $args, $data );

		} elseif ( $args['id'] == 'mf_presenter_promos' ) {

			// Load the presenter promo meta box for Eventbrite. This is used to store promo codes to give to Presenters
			$this->presenter_promos( $post, $args, $data );

		} elseif ( $args['id'] == 'mf_maker_contact' ) { // Display "Contact Info" for the contact of the application.

			$photo_thumb_key = $this->merge_fields( 'user_photo_thumb', $data->form_type );
			$photo_key       = $this->merge_fields( 'user_photo', $data->form_type );
			$bio_key         = $this->merge_fields( 'user_bio', $data->form_type );

			$photo = isset( $data->{ $photo_thumb_key } ) ? $data->{ $photo_thumb_key } : '';

			if( '' == $photo )
				$photo = isset( $data->{ $photo_key } ) ? $data->{ $photo_key } : '';
			if( is_array( $photo ) )
				$photo = $photo[0];

			$bio = isset( $data->{ $bio_key } ) ? $data->{ $bio_key } : '';
			if( is_array( $bio ) )
				$bio = $bio[0]; ?>

			<img src="<?php echo esc_url( $photo ); ?>" style="float:left; margin-right:10px;" height="75" width="75" />
			<div style="float:left; width:150px;">
				<strong><?php echo esc_html( $data->name ); ?></strong><br />
				<?php echo esc_html( $data->phone1 ); ?><br />
				<?php echo esc_html( $data->email ); ?>
			</div>
			<div style="clear:both; height:15px;"></div>
			<strong>ADDRESS</strong><br />
			<?php echo esc_html( $data->private_address.' '.$data->private_address2 ); ?><br />
			<?php echo esc_html( $data->private_city.', '.$data->private_state.' '.$data->private_zip.' '.$data->private_country ); ?><br /><br />
			<strong>Bio</strong><br />
			<?php echo wp_kses_post( mf_convert_newlines( $bio ) ); ?>

		<?php } elseif ( $args['id'] == 'mf_maker_info' ) {

			$maker_type = ( ! empty( $data->maker ) ) ? $data->maker : $data->form_type;

			if ( $maker_type == 'One maker' ) {
				$result = esc_html( $data->maker_name ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $data->maker_name ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a>';
			} elseif ( $maker_type == 'A list of makers' ) {
				$result = '';
				foreach ( $data->m_maker_name as $maker_name ) {
					$result .= esc_html( $maker_name ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $maker_name ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a><br />';
				}
			} elseif ( $maker_type == 'A group or association' ) {
				$result = esc_html( $data->group_name ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $data->group_name ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a>';
			} elseif ( $maker_type == 'performer' ) {
				$result = esc_html( $data->performer_name ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $data->performer_name ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a>';
			} elseif ( $maker_type == 'presenter' ) {
				if ( is_array( $data->presenter_name ) ) {
					foreach ( $data->presenter_name as $presenter ) {
						$result = esc_html( $presenter ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $presenter ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a>';
					}
				} else {
					$result = esc_html( $data->presenter_name ) . ' <a target="_blank" style="float:right" href="' . esc_url( admin_url( '/edit.php?s=' . urlencode( $data->presenter_name ) . '&post_status=all&post_type=maker' ) ) . '">Lookup Maker</a>';
				}
			} else {
				$result = 'Could not find makers to list!';
			}

			echo '<strong>' . $result . '</strong>';

		} elseif ( $args['id'] == 'mf_form_type' ) { ?>

			<input class="mf_form_type" name="form_type" type="radio" value="exhibit" /> &nbsp; Exhibit Application &nbsp;
			<input class="mf_form_type" name="form_type" type="radio" value="performer" /> &nbsp; Performer Application &nbsp;
			<input class="mf_form_type" name="form_type" type="radio" value="presenter" /> &nbsp; Presenter Application
			<script type="text/javascript">
				(function($){
					$(document).ready(function() {
						$('#mf_exhibit, #mf_performer, #mf_presenter').hide();
						$('.mf_form_type').click(function(){
							$('#mf_exhibit, #mf_performer, #mf_presenter').hide();
							form_type = $(this).val();

							if ( form_type == 'exhibit' )
								$( '#maker input[value="One maker"]' ).click();

							$('#mf_'+form_type).show();
						} );
					} );
				} )(jQuery.noConflict())
			</script>

		<?php } elseif ( in_array( $args['id'], array( 'mf_exhibit', 'mf_performer', 'mf_presenter', 'mf_logistics' ) ) ) {

			if ( $args['id'] != 'mf_logistics' ) {
				$data = array( 'form_type' => $args['args']['type'], 'maker_faire' => $this->maker_faire );
				foreach ( $this->fields[$args['args']['type']] as $sn => $s ) {
					foreach ( array_keys( $s ) as $k ) {
						$data[$k] = '';
					}
				}

				$data = (object) $data;
			}

			if( !isset( $data->cats ) )
				$data = (object) array_merge( array( 'cats' => '' ), (array) $data);
			if( !isset( $data->tags ) )
				$data = (object) array_merge( array( 'tags' => '' ), (array) $data);
			if( !isset( $data->uid ) )
				$data = (object) array_merge( array( 'uid' => '' ), (array) $data);

			$cont = array(
				'm_maker_email',
				'm_maker_gigyaid',
				'm_maker_photo',
				'm_maker_bio',
				'presenter_email',
				'presenter_gigyaid',
				'presenter_bio',
				'presenter_onsite_phone',
				'presenter_org',
				'presenter_title',
				'presenter_photo',
				'presenter_twitter',
				'presenter_previous'
			); ?>

			<table style="width:100%">

				<?php foreach( (array) $data as $k => $v ) : if ( in_array( $k, $cont ) ) continue; ?>
					<tr class="mf-form-row" id="<?php echo esc_attr( $k ); ?>" <?php if ( strpos( $k, '_thumb' ) !== false ): ?>style="display:none"<?php endif; ?>>
					<td valign="top"><?php echo esc_html( ucwords( str_replace( '_', ' ', $k ) ) ); ?>:</td>
					<td>
						<?php $this->display_edit_field( $data->form_type, $k, $v, (array) $data ); ?>
					<?php if ( $k != 'm_maker_name' && $k != 'presenter_name' ) : ?>
					</td>
					</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				<tr>
					<td></td>
				</tr>

			</table>

			<?php if( $args['id'] == 'mf_logistics' || $args['id'] == 'mf_presenter' ) :
				if ( isset( $data->m_maker_name ) && is_array( $data->m_maker_name ) ) {
					$number_of_makers = count( $data->m_maker_name );
				} elseif ( isset( $data->presenter_name ) && is_array( $data->presenter_name ) ) {
					$number_of_makers = count( $data->presenter_name );
				} else {
					$number_of_makers = 1;
				} ?>
				<script type="text/javascript">

					jQuery(function($) {

						form_type      = '<?php echo esc_html( $data->form_type ); ?>';
						num_makers     = <?php echo intval( $number_of_makers ); ?>;
						num_presenters = <?php echo intval( isset( $data->presenter_name ) && is_array( $data->presenter_name ) ? count( $data->presenter_name ) + 1 : 1 ); ?>;

						$('#maker input[type=radio]').click(function(){
							$('#maker_name, #maker_email, #maker_photo, #maker_bio, #m_maker_name, .m_maker_name, .add-maker, #group_name, #group_website, #group_photo, #group_bio, .remove-maker').hide();
							if ( $(this).val() == 'One maker' ) {
								$('#maker_name, #maker_email, #maker_photo, #maker_bio').show();
							} else if ( $(this).val() == 'A list of makers' ) {
								$('#m_maker_name, .m_maker_name, .add-maker, .remove-maker').show();
							} else {
								$('#group_name, #group_website, #group_photo, #group_bio').show();
							}
						} );

						mf_insert_add_maker_btn();

						if( form_type == 'exhibit' ) {
							$( '#maker input[value="<?php echo esc_attr( isset( $data->maker ) ? $data->maker : 'One maker' ); ?>"]' ).click();
						}

						function mf_insert_add_maker_btn()
						{
							html = '<tr id="'+form_type+'-add-maker" class="mf-form-row add-maker add-maker-btn">'+
										'<td colspan="2">'+
											'<input type="button" value="+Add Maker" class="button button-primary button-large"> '+
											'<div style="float:right"><a href="<?php echo esc_url( admin_url( '/edit.php?post_type=maker' ) ); ?>" target="_blank">Lookup GIGYA ID</a></div>'+
										'</td>'+
									'</tr>';

							$(html).insertAfter( $('#m_maker_bio, #presenter_previous') );
							$('.add-maker-btn .button').unbind('click').click(mf_add_maker);
						}

						function mf_add_maker() {

							fields = {
								exhibit : {
									m_maker_name    : 'Add. Maker Name',
									m_maker_email   : 'Add. Maker Email',
									m_maker_gigyaid : 'Add. Maker Gigyaid',
									m_maker_bio     : 'Add. Maker Bio',
									m_maker_twitter : 'Add. Maker Twitter',
									m_maker_photo   : 'Add. Maker Photo URL'
								},
								presenter : {
									presenter_name     : 'Add. Presenter Name',
									presenter_email    : 'Add. Presenter Email',
									presenter_gigyaid  : 'Add. Presenter Gigyaid',
									presenter_bio      : 'Add. Presenter Bio',
									presenter_org      : 'Add. Presenter Organization',
									presenter_title    : 'Add. Presenter Title',
									presenter_photo    : 'Add. Presenter Photo URL',
									presenter_twitter  : 'Add. Presenter Twitter',
									presenter_previous : 'Add. Presenter Previous'
								}
							};

							html  = '';
							for(i in fields[form_type]) {
								if ( i === 'presenter_bio' || i === 'm_maker_bio' ) {
									html += console.log(i);
									html += '<tr class="mf-form-row add-maker"><td valign="top">'+fields[form_type][i]+':</td><td><textarea name="' + form_type + '[' + i + '][' + num_makers + ']" id="" cols="30" rows="10"></textarea></td></tr>';
								} else {
									html += '<tr class="mf-form-row add-maker"><td valign="top">'+fields[form_type][i]+':</td><td><input type="text" name="'+form_type+'['+i+']['+num_makers+']"></td></tr>';
								}
							}
							html += '<tr class="remove-maker">'+
										'<td colspan="2">'+
											'<input type="button" onclick="mf_remove_maker( this )" value="Remove Maker Above" class="button button-primary button-large"></td></tr>';

							$(html).insertAfter($('.add-maker-btn'));

							num_makers++;
						}
					} );

					function mf_remove_maker( el ) {
						p = jQuery( el ).parent().parent();
						l = form_type == 'exhibit' ? 5 : 7;
						for( i = 0; i < l; i++ ) {
							p.prev().remove();
						}
						p.remove();
					}

				</script>
			<?php endif;

		}
	}


	/**
	 * A quick solution to making the application review process easier for our team.
	 * Originally we had all the forms displaying every where which lead to difficulties in fast reviewing
	 * and also instances data was removed by accident. Let' pull that same data but in a static format with some interactions...
	 * @param  Object $post The post object. Probably don't need it, but for now we'll pass it in
	 * @param  Array  $args An array arguments for meta information of the application
	 * @param  Array  $data All the json data saved for application
	 * @return HTML
	 *
	 * @since Mechani-Kong
	 */
	private function app_details( $post, $args, $data ) {
		// Our current infrastructure forces us to use an array of fields we don't want
		// to display. I wish I could handle the titles of the fields better and reorganize them,
		// but due to how this system was built, that ain't happening easily :P
		$hidden_fields = array(
			'form_type',
			'uid',
			'project_name',
			'public_description',
			'performer_photo',
			'performer_photo_thumb',
			'project_photo',
			'project_photo_thumb',
			'presenter_photo_thumb',
			'presenter_gigyaid',
			'presentation_photo',
			'presentation_photo_thumb',
			'maker_photo_thumb',
			'm_maker_photo_thumb',
			'group_photo_thumb',
		); ?>
		<h3 style="background:#ddd;color:#000;margin:0;position:relative;top:2px;">Application Information</h3>
		<table class="app-details widefat fixed" style="">
			<tbody>
				<?php $i = 0; foreach ( $data as $key => $value ) :
					if ( ! in_array( $key, $hidden_fields ) ) :
						// Pass a Pretty way to display the faire this app belongs to
						if ( $key == 'maker_faire' )
							$value = $args['callback'][0]->faire_friendly_name;

						if ( $key != 'email' ) : ?>
							<tr class="<?php echo ( $i++ % 2 == 1 ) ? '' : 'alternate'; ?>">
								<td class="field-name" valign="top" style="width:15%;font-weight:bold;"><?php echo esc_html( str_replace( '_', ' ', strtoupper( $key ) ) ); ?></td>
								<td class="field-value" style="width:85%;"><?php echo wp_kses_post( $this->convert_content( $key, $value ) ); ?></td>
							</tr>
						<?php else : // We want to close our table and start a new when we hit the maker field ?>
								</tbody>
							</table>

							<h3 style="background:#ddd;color:#000;margin:30px 0 0;position:relative;top:2px;">Maker Information</h3>
							<table class="app-details maker-info widefat fixed">
								<tbody>
									<tr class="<?php echo ( $i++ % 2 == 1 ) ? '' : 'alternate'; ?>">
										<td class="field-name" valign="top" style="width:15%;font-weight:bold;"><?php echo esc_html( str_replace( '_', ' ', strtoupper( $key ) ) ); ?></td>
										<td class="field-value" style="width:85%;"><?php echo wp_kses_post( $this->convert_content( $key, $value ) ); ?></td>
									</tr>
						<?php endif;
					endif;
				endforeach; ?>
			</tbody>
		</table>
	<?php }


	/**
	 * A helper function primarily for app_details in adding some nice formatting for our returned data
	 * Returns the value only, not the key!
	 * @param  String $key   The array key
	 * @param  String $value The array value
	 * @return String
	 *
	 * @since Mechani-Kong
	 */
	private function convert_content( $key, $value ) {
		if ( $key == 'project_website' || $key == 'project_video' || $key == 'layout' || $key == 'group_website' || $key == 'presentation_website' || $key == 'video' ) {
			$output = '<a href="' . esc_url( $value ) . '" target="_blank">' . esc_url( $value ) . '</a>';
		} elseif (($key == 'tags') && (is_array($value))) {
				$tag_names = array();
				foreach ($value as $tag_slug) {
					$tag_term = wpcom_vip_get_term_by('slug', $tag_slug,'post_tag');
					$tag_names[] = $tag_term->name;
				}
				$output = join(", ",$tag_names);
		} elseif (($key == 'cats') && (is_array($value))) {

				$cat_names = array();
				foreach ($value as $cat_slug) {
					$cat_term = wpcom_vip_get_term_by('slug', $cat_slug,'category');
					$cat_names[] = $cat_term->name;
				}
				$output = join(", ",$cat_names);

		} elseif ( is_array( $value ) ) {
			if ( empty( $value ) ) {
				$output = '';
			} else {
				$output = ucfirst(join(", ",$value));
			}
		} elseif ( ( $key == 'maker_photo' || $key == 'm_maker_photo' || $key == 'group_photo' || $key == 'presenter_photo' ) && ! empty( $value ) ) {
			$output = '<a href="' . esc_url( $value ) . '"><img src="' . wpcom_vip_get_resized_remote_image_url( esc_url( $value ), 130, 130, true ) . '" width="130" height="130" target="_blank"></a>';
		} elseif ( $key == 'maker_twitter' || $key == 'm_maker_twitter' || $key == 'group_twitter' || $key == 'presenter_twitter' ) {
			$output = '<a href="http://twitter.com/' . sanitize_title_with_dashes( $value ) . '">' . $value . '</a>';
		} else {
			$output = $value;
		}

		return $output;
	}


	/**
	 * Displays a text field in a meta box so we can email promo codes to presenters on acceptance
	 * @param  [type] $post [description]
	 * @param  [type] $args [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function presenter_promos( $post ) {
		if ( get_post_type( $post ) == 'event-items' ) {
			$record = get_post_meta( absint( $post->ID ), 'mfei_record', true );
			$promo = get_post_meta( absint( $record ), 'app-presenter-promo-code', true );
		} else {
			$promo = get_post_meta( absint( $post->ID ), 'app-presenter-promo-code', true );
		} ?>
		<input type="text" placeholder="Insert the unique Eventbrite promo code" name="presenter-promo-code" value="<?php echo ( ! empty( $promo ) ) ? esc_attr( $promo ) : ''; ?>" style="width:100%;">
	<?php }


	/*
	* Creates Backend MakerFaire Application Forms
	*
	* @access public
	*
	* @param string $type     Form type
	* @param string $key      Key for this field
	* @param string $value    Value of this field
	* @param array  $all_data All the data from the form
	* =====================================================================*/
	public function display_edit_field( $type, $key, $value, $all_data ) {

		$checkboxes = array(
			'booth_options' => array(
				'With other Makers under a large tent',
				'Open air',
				'I can bring a tent/canopy with weights',
				'Asphalt',
				'Grass',
			),
			'radio_frequency' => array(
				'Remote Control (enter frequency band below)',
				'Robots',
				'Drones',
				'Amateur radio or CB',
				'ZigBee on 900 MHz',
				'ZigBee on 2.4 GHz',
				'Telepathy',
				'Bluetooth',
				'WiFi Internet access',
				'My own local WiFi cloud on 2.4 GHz',
				'My own local WiFi cloud on 5 GHz (discouraged)',
				'I would use a private WIFi cloud if you provided one',
				'Something else on 900 MHz',
				'Something else on 2.4 GHz',
				'Something else on 5 GHz (discouraged, please explain below',
				'Something with an antenna, but I have no idea what'
			),
			'length' => array(
				'10 minutes',
				'20 minutes',
				'45 minutes',
			),
			'length_presentation' => array(
				'12 minutes',
				'25 minutes',
				'45 minutes',
			),
		);
		$radios = array(
			'food'              => array( 'Yes', 'No' ),
			'sales'             => array( 'Yes', 'No' ),
			'activity'          => array( 'Yes', 'No' ),
			'power'             => array( 'Yes', 'No' ),
			'radio'             => array( 'Yes', 'No' ),
			'fire'              => array( 'Yes', 'No' ),
			'hands_on'          => array( 'Yes', 'No' ),
			'first_time'        => array( 'Yes', 'No' ),
			'first_makerfaire'  => array( 'Yes', 'No' ),
			'exhibit'           => array( 'Yes', 'No' ),
			'tables_chairs'     => array(
				'None'     => 'No tables or chairs needed',
				'Standard' => '1 table and 2 chairs',
				'Special'  => 'More than 1 table and 2 chairs. Specify your request below',
			),
			'booth_size' => array(
				'Mobile'   => 'My exhibit is mobile (no fixed exhibit space needed)',
				'Tabletop' => 'Tabletop',
				'10x10'    => '10\' x 10\'',
				'10x20'    => '10\' x 20\'',
				'Other'    => 'Other - Tell us your space size request below',
			),
			'booth_location' => array(
				'Inside',
				'Outside',
				'Either',
			),
			'lighting' => array( 'Normal', 'Dark' ),
			'noise' => array(
				'Normal - does not interfere with normal conversation',
				'Amplified - adjustable level of amplification',
				'Repetitive or potentially annoying sound', 'Loud!',
			),
			'amps' => array(
				'5 amp circuit (0-500 watts, 110V)',
				'5 amp circuit (0-500 watts, 120V)',
				'10 amp circuit (501-1000 watts, 110V)',
				'10 amp circuit (501-1000 watts, 120V)',
				'15 amp circuit (1001-1500 watts, 110V)',
				'15 amp circuit (1001-1500 watts, 120V)',
				'20 amp circuit (1501-2000 watts, 110V)',
				'20 amp circuit (1501-2000 watts, 120V)',
				'My exhibit requires power, but I need help determining my total power amperage',
				'Special power requirements noted below',
			),
			'internet'=> array(
				'No internet access needed',
				'It would be nice to have WiFi internet access',
				'My exhibit must have WiFi internet access to work',
			),
			'maker' => array(
				'One maker',
				'A list of makers',
				'A group or association',
			),
			'org_type' => array(
				'Non-profit',
				'Cause or mission-based organization',
				'Established company or commercial entity',
				'None of the above',
			),
			'performance_time' => array(
				'Saturday Only',
				'Sunday Only',
				'Either Saturday or Sunday; We\'re flexible but prefer to play only once.',
				'Both Saturday and Sunday; We\'d love to play both days if there\'s space and time in the schedule.',
				'All Weekend! We have our own separate setup and would like to play all weekend, if possible.',
			),
			'compensation_type' => array(
				'Thanks for the opportunity to play! We are happy to play without financial compensation.',
				'We will play for guest tickets only.',
				'$ amount',
			),
			'presentation_type' => array(
				'Presentation',
				'Panel Presentation',
			),
			'availability'      => array(
				'Either Saturday or Sunday',
				'Saturday only',
				'Sunday only',
			)
		);

		$san_value = is_array( $value ) ? esc_attr( implode( ', ', $value ) ) : esc_attr( $value );

		if ( $this->is_textarea( $key ) ) : ?>

			<textarea name="<?php echo esc_attr( $type . '[' . $key . ']' ); ?>" /><?php echo htmlspecialchars_decode( esc_textarea( $this->convert_newlines( $value, "\n" ) ) ); ?></textarea>

		<?php elseif ( array_key_exists( $key, $checkboxes ) ) : ?>

			<?php foreach( $checkboxes[$key] as $dkey => $data ) : $dkey = ! is_int( $dkey ) ? $dkey : $data; ?>
				<div>
					<input name="<?php echo esc_attr( $type.'['.$key.']' ); ?>[]" type="checkbox" value="<?php echo esc_attr( $dkey ); ?>" <?php checked( in_array( $dkey, (array) $value ) ); ?> />
					<?php echo esc_html( $data ) ?>
				</div>
			<?php endforeach; ?>

		<?php elseif ( array_key_exists( $key, $radios ) ) : ?>

			<?php foreach( $radios[ $key ] as $dkey => $data ) : $dkey = ! is_int( $dkey ) ? $dkey : $data; ?>
				<div>
					<input name="<?php echo esc_attr( $type . '[' . $key . ']' ); ?>" type="radio" value="<?php echo esc_attr( $dkey ); ?>" <?php checked( $dkey == $value ); ?> />
					<?php echo esc_html( $data ); ?>
				</div>
			<?php endforeach; ?>

		<?php elseif ( $key == 'm_maker_name' || $key == 'presenter_name' ) :

			$init_fields = array(
				'm_maker_name'   => array(
					'm_maker_email',
					'm_maker_gigyaid',
					'm_maker_photo',
					'm_maker_twitter',
					'm_maker_bio',
				),
				'presenter_name' => array(
					'presenter_gigyaid',
					'presenter_bio',
					'presenter_photo',
					'presenter_email',
					'presenter_onsite_phone',
					'presenter_org',
					'presenter_title',
					'presenter_twitter',
					'presenter_previous',
				)
			); ?>

			<input name="<?php echo esc_attr( $type . '[' . $key . '][0]' ); ?>" value="<?php echo esc_attr( isset( $all_data[ $key ][0] ) ? $all_data[ $key ][0] : '' ); ?>" type="text" />
			</td></tr>
			<?php foreach( $init_fields[ $key ] as $fn ) :
				if ( is_array( $all_data[ $fn ] ) && isset( $all_data[ $fn ][0] ) ) {
					$data = $all_data[ $fn ][0];
				} elseif ( is_string( $all_data[ $fn ] ) ) {
					$data = $all_data[ $fn ];
				} else {
					$data = '';
				}

				if ( ( $fn == 'm_maker_gigyaid' || $fn == 'presenter_gigyaid' ) && $data == '' && isset( $all_data['uid'] ) )
					$data = $all_data['uid']; ?>

				<tr class="mf-form-row <?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $fn ); ?>">
					<td valign="top"><?php echo esc_html( ucwords( str_replace( '_', ' ', $fn ) ) ); ?>:</td>

					<?php if ( $fn == 'm_maker_bio' || $fn == 'presenter_bio' ) :?>
						<td><textarea name="<?php echo esc_attr( $type . '[' . $fn . '][0]' ); ?>" id="<?php echo esc_attr( $type . '[' . $fn . '][0]' ); ?>" cols="30" rows="10"><?php echo ( ! empty( $data ) ) ? esc_attr( $data ) : ''; ?></textarea></td>
					<?php else : ?>
						<td><input style="width:100%;" name="<?php echo esc_attr( $type . '[' . $fn . '][0]' ); ?>" value="<?php echo esc_attr( $data ); ?>" type="text" /></td>
					<?php endif; ?>
				</tr>
			<?php endforeach;


			// If the data passed is an 'additional' maker
			if ( is_array( $all_data[ $key ] ) ) :

				$add_fields = array(
					'm_maker_name'   => array(
						'm_maker_name'    => 'Add. Maker Name',
						'm_maker_email'   => 'Add. Maker Email',
						'm_maker_gigyaid' => 'Add. Maker Gigyaid',
						'm_maker_bio'	  => 'Add. Maker Bio',
						'm_maker_twitter' => 'Add. Maker Twitter Handle',
						'm_maker_photo'   => 'Add. Maker Photo URL',
					),
					'presenter_name' => array(
						'presenter_name'     => 'Add. Presenter Name',
						'presenter_email'    => 'Add. Presenter Email',
						'presenter_gigyaid'  => 'Add. Presenter Gigyaid',
						'presenter_bio'		 => 'Add. Presenter Bio',
						'presenter_org'		 => 'Add. Presenter Organization',
						'presenter_title'	 => 'Add. Presenter Title',
						'presenter_photo'	 => 'Add. Presenter Photo',
						'presenter_bio'		 => 'Add. Presenter Bio',
						'presenter_twitter'  => 'Add. Presenter Twitter',
						'presenter_previous' => 'Add. Presenter Previous',
					)
				);

				for ( $i = 1; $i < count( $all_data[ $key ] ); $i++ ) :
					foreach( $add_fields[ $key ] as $fkey => $ftitle ) :
						if ( is_array( $all_data[ $fkey ] ) && isset( $all_data[ $fkey ][ $i ] ) ) {
							$data = $all_data[ $fkey ][ $i ];
						} elseif ( is_string( $all_data[ $fkey ] ) ) {
							$data = $all_data[ $fkey ];
						} else {
							$data = '';
						} ?>
						<tr class="mf-form-row add-maker">
							<td valign="top"><?php echo esc_html( $ftitle ); ?>:</td>

							<?php if ( $fkey == 'm_maker_bio' || $fkey == 'presenter_bio' ) : ?>
								<td>
									<textarea name="<?php echo esc_attr( $type . '[' . $fkey . '][' . $i . ']' ); ?>" id="<?php echo esc_attr( $type . '[' . $fkey . '][' . $i . ']' ); ?>" cols="30" rows="10"><?php echo esc_attr( $data ); ?></textarea>
								</td>
							<?php else : ?>
								<td>
									<input name="<?php echo esc_attr( $type . '[' . $fkey . '][' . $i . ']' ); ?>" value="<?php echo esc_attr( $data ); ?>" type="text" />
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					<tr class="remove-maker">
						<td colspan="2">
							<input onclick="mf_remove_maker(this)" type="button" value="Remove Maker Above" class="button button-primary button-large">
						</td>
					</tr>
				<?php endfor;
			endif; ?>

		<?php else : ?>
			<input name="<?php echo esc_attr( $type . '[' . $key . ']' ); ?>" value="<?php echo $san_value; ?>" type="text" />
		<?php endif;
	}


	/*
	* Updates mf_form object content and post meta data
	*
	* @access public
	* @param integer $id Post id
	* =====================================================================*/
	public function update_post( $id ) {

		// Bail if post is auto-draft/revision/nav-menu item
		if ( get_post_type( $id ) != 'mf_form' )
			return false;

		if ( empty( $_POST ) || ( ! isset( $_POST['mf_form'] ) && isset( $_POST['form_type'] ) && isset( $this->fields[ $_POST['form_type'] ] ) ) || isset( $_POST['mf_updated'] ) )
			return false;

		if ( ! isset( $_POST['form_type'] ) )
			return false;

		// If we are trying to save any presenter promo codes...
		if ( ( isset( $_POST['form_type'] ) ) && $_POST['form_type'] == 'presenter' && isset( $_POST['presenter-promo-code'] ) && ! empty( $_POST['presenter-promo-code'] ) )
			update_post_meta( absint( $id ), 'app-presenter-promo-code', esc_attr( $_POST['presenter-promo-code'] ) );

		// Set some variables yo.
		$form_type  = sanitize_text_field( $_POST['form_type'] );
		$is_trashed = ( isset( $_POST['trash-post'] ) ) ? $_POST['trash-post'] : '';
		$r = array(
			'form_type'   => $form_type,
			'maker_faire' => sanitize_text_field( $_POST[ $form_type ]['maker_faire'] ),
			'uid'         => sanitize_text_field( $_POST[ $form_type ]['uid'] ),
			'tags'        => sanitize_text_field( $_POST[ $form_type ]['tags'] ),
			'cats'        => sanitize_text_field( $_POST[ $form_type ]['cats'] ),
		);

		// For starters, lets get all of our data into one bucket and clean things up.
		foreach ( $this->fields[ $form_type ] as $sn => $s ) {

			// Loop through each array in the $s variable
			foreach ( array_keys( $s ) as $k ) {
				// Check if our data being submitted is in an array first, sanitize and add to the $r array.
				// Then check if we are passing in a textarea or text field and sanitize those fields accordingly.
				if ( isset( $_POST[ $form_type ][ $k ] ) && is_array( $_POST[ $form_type ][ $k ] ) ) {

					// Add new keys that are not there by default to the $r array (e.g. m_maker_name, m_maker_email, etc etc)
					$r[ $k ] = array();

					// Loop through each key passed and their value to the $r array
					foreach ( $_POST[ $form_type ][ $k ] as $v ) {
						$r[ $k ][] = sanitize_text_field( $v );
					}
				} elseif( $this->is_textarea( $k ) ) {
					// Sanitize our textareas for allowed HTML tags and then insert HTML line breaks before newlines.
			 		$r[ $k ] = wp_kses_post( nl2br( $_POST[ $form_type ][ $k ] ) );
				} else {
					// Sanitize the string in our text fields
					$r[ $k ] = ( isset( $_POST[ $form_type ][ $k ] ) ) ? sanitize_text_field( $_POST[ $form_type ][ $k ] ) : '';
			 	}
			}

			// Create a fallback for the public description field and repurpose it to the short_description field.
			// This is in place because in the presenter we removed this field, this will ensure older applications will be updated.
			if ( isset( $_POST['presenter']['public_description'] ) ) {
				$r[ 'short_description' ] = sanitize_text_field( $_POST['presenter']['public_description'] );
			}

			// Save our coverage video link if its set and save it into the Event Post Type post
			if ( ! empty( $_POST['video-coverage'] ) && ! empty( $_POST['event-id'] ) ) {
				$video_url = ( ! empty( $_POST['video-coverage'] ) ) ? esc_url( $_POST['video-coverage'] ) : '';

				update_post_meta( intval( $_POST['event-id'] ), 'mfei_coverage', esc_url( $_POST['video-coverage'] ) );
			}
		}



		// Check if the $r array contains less than 3 fields. If so, we want to return false and stop the processing of the code below.
		if ( count( $r ) < 3 )
			return false;

		//Check if Post Tags submitted - redo tag list.
		if(isset($_POST['tax_input']) && isset($_POST['tax_input']['post_tag'])&&($_POST['tax_input']['post_tag']!='')){
			$tags_clean = array();
			$tags = explode( ',', $_POST['tax_input']['post_tag']);
			foreach ($tags as $tag ) {
				$tags_clean[] = sanitize_title( $tag );
			}

			$r['tags'] = $tags_clean;
		}
		$this->update_mf_form( $r['form_type'], $id, $r, 0 );

		//MAKE SURE ALL GIGA IDS ARE ASSOCIATED WITH THIS FORM
		$connected_users = get_post_meta( $id, 'mf_gigya_id' );
		$new_gigya_ids   = array( $r['uid'] );

		if ( ( $form_type == 'exhibit' && $_POST['exhibit']['maker'] == 'A list of makers' ) || $form_type == 'presenter' )
			$new_gigya_ids = array_merge( $new_gigya_ids, $_POST[$form_type][ ( $form_type == 'presenter' ? 'presenter_gigyaid' : 'm_maker_gigyaid' ) ]);

		//ADD GIGYA IDS
		$add_ids = array_diff( $new_gigya_ids, $connected_users );

		foreach ( $add_ids as $gigya_id ) {
			add_post_meta( $id, 'mf_gigya_id',  $gigya_id );
		}

		//REMOVE GIGYA IDS
		$remove_ids = array_diff(  $connected_users, $new_gigya_ids );

		foreach( $remove_ids as $gigya_id ) {
			delete_post_meta( $id, 'mf_gigya_id',  $gigya_id );
		}

		//SYNC WITH JDB
		$this->sync_jdb( $id );

		if ( isset( $_POST['original_post_status'], $_POST['post_status'], $_POST['action'], $_POST['post_type'] ) )
			$this->handle_post_status_change();
	}

	/*
	* Controller to deal with post status change
	*
	* @access public
	* =====================================================================*/
	public function handle_post_status_change() {

		// return if _POST fields not set
		if ( ! isset( $_POST['original_post_status'], $_POST['post_status'], $_POST['action'], $_POST['form_type'], $_POST['ID'] ) )
			return;

		// return if action is not editpost
		if ( $_POST['action'] != 'editpost' )
			return;

		// return if post_type is not mf_form
		if ( $_POST['post_type'] != 'mf_form' )
			return;

		// get post id
		$post_id = absint( $_POST['ID'] );

		// grab post as ref to get the content
		$post = get_post( $post_id );
		$wf   = get_post_meta( $post_id, '_mf_waiting_for', true );

		//SANITIZE POST STATUS | post status ( proposed, more-info, accepted, rejected, cancelled, wait-list )
		$post_status = sanitize_key( $_POST['post_status'] );
		if( !in_array( $post_status, array( 'more-info', 'wait-list', 'accepted', 'rejected', 'cancelled' ) ) )
			return;

		// decode the content into data
		if ( isset( $post->post_content ) )
			$data = json_decode( str_replace( "\'", "'", $post->post_content ) );

		// SANITIZE FORM TYPE
		$form_type = sanitize_key( $_POST['form_type'] );
		if( !in_array( $form_type, array( 'exhibit', 'performer', 'presenter' ) ) )
			return;

		$form_type_pretty = ucfirst( $form_type );

		$form = $_POST[ $form_type ];

		if ( $_POST['original_post_status'] != $post_status )
			$this->sync_status_jdb( $post_id, $post_status );

		// if new status write the log
		if ( $_POST['original_post_status'] != $post_status || ( $post_status == 'more-info' && isset( $_POST['mf_waitingquestion'] ) && $wf != $_POST['mf_waitingquestion'] ) ) {

			$current_user = wp_get_current_user();
			$user_name    = $current_user->user_login;

			$date = date('m/d/y h:i a', time() - ( 3600 * 7 ) );

			$extra = '';
			if ( $post_status == 'more-info' ) {
				$extra = esc_textarea( $_POST['mf_waitingquestion'] );
				update_post_meta( $post_id, '_mf_waiting_for', $extra );
			}

			$post_status_pretty = '';
			if ( $post_status == 'accepted' ) {
				$post_status_pretty = ' Accepted';
			} elseif ( $post_status == 'more-info' ) {
				$post_status_pretty = ' Needs More Info: ' . $extra;
			} elseif ( $post_status == 'cancelled' ) {
				$post_status_pretty = ' Cancelled';
			} elseif ( $post_status == 'rejected' ) {
				$post_status_pretty = ' Rejected';
			} elseif ( $post_status == 'in-progress' ) {
				$post_status_pretty = ' In-Progress';
			} elseif ( $post_status == 'wait-list' ) {
				$post_status_pretty = ' Wait List';
			}


			$post_status_log = get_post_meta( $post_id, '_mf_log', true );

			if ( ! is_array( $post_status_log ) || $post_status_log == false || sizeof( $post_status_log ) == 0 ) {
				$post_status_log = array();
				$post_status_log[] = mysql2date('m/d/y h:i a', $post->post_date) .' Proposed';
			}

			$post_status_log[] = "{$date}{$post_status_pretty} by {$user_name}";

			//LIMIT STATUS LOG TO 15 ENTRIES
			$post_status_log   = array_slice($post_status_log, -15);

			update_post_meta( $post_id, '_mf_log', $post_status_log);
		} else {
			return;
		}

		//DON'T SEND EMAIL IF DISABLE AUTORESPONDER IS CHECKED.
		if( isset( $_POST['_ef_editorial_meta_checkbox_email-notifications'] ) )
			return;

		// Get Project Name
		$project_name = htmlspecialchars_decode( sanitize_text_field( $form[$this->merge_fields( 'project_name', $form_type )] ) );


		// Set default maker name
		$maker_name = 'Dear Maker';

		// If a name exists in the form (which it should), we'll output that name
		if ( isset( $form['name'] ) )
			$maker_name = esc_html( $form['name'] );

		// set maker_email
		$maker_email = '';
		if ( isset( $form['email'] ) && ! empty( $form['email'] )  && is_string( $form['email'] ) && is_email( $form['email'] ) )
			$maker_email = strtolower( $form['email'] );



		// the email recipients (main recipient)
		$tos = array();
		foreach ( array( 'maker_email', 'email', 'presenter_email' ) as $k ) {
			// if any of these are set add to tos array
			if ( isset( $form[ $k ] ) && ! empty( $form[ $k ] ) && is_string( $form[ $k ] ) )
				$tos[] = $form[ $k ];
		}

		// grab additional emails to tos array
		$maker_emails = array();

		if ( isset( $form['presenter_email'] ) && ! empty( $form['presenter_email'] ) && is_array( $form['presenter_email'] ) )
			$maker_emails = $form['presenter_email'];

		if ( $form_type != 'presenter' ) {
			if ( isset( $form['m_maker_email'] ) && ! empty( $form['m_maker_email'] ) && is_array( $form['m_maker_email'] ) )
				$maker_emails = $form['m_maker_email'];
		}

		// add to tos array
		foreach ( $maker_emails as $k => $v ) {
			$tos[] = $v;
		}



		// strtolower and unique the emails
		$tos = array_map( 'strtolower', $tos );
		$tos = array_unique( $tos );
		$toss = array();

		// make sure only valid emails in list and not the maker_email
		foreach ( $tos as $email ) {
			if ( is_email( $email ) && ( $email != $maker_email ) )
				$toss[] = $email;
		}

		$tos = array_merge( array( $maker_email ), $toss );
		// default bcc
		$bcc = 'makers@makerfaire.com';

		$local_server = array( 'localhost', 'make.com', 'vip.dev', 'staging.makerfaire.com' );

		// Send all emails in testing environments to one account.
		if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], $local_server ) ) {
			$tos = array( 'cgeissinger@makermedia.com' );
			$bcc = '';
		}

		// default from
		$from = 'Maker Faire <makers@makerfaire.com>';
		if ( $form_type == 'presenter' )
			$from = 'Sabrina Merlo, Maker Faire <sabrina@makerfaire.com>';

		// default msg to append to subject
		$subject_text = '';

		// get the subject_text based on post_status
		switch ( $post_status ) {
			case 'more-info':
				$subject_text = '– Information Needed for Maker Faire';
				break;
			case 'accepted':
				$subject_text = '- Acceptance for Maker Faire';
				break;
			case 'rejected':
			case 'wait-list':
				$subject_text = '- Application for Maker Faire';
				break;
			case 'cancelled':
				$subject_text = '- Cancelled for Maker Faire';
				break;
			case 'proposed':
				$subject_text = '';
				break;
		}

		// set the subject
		$subject = "[{$form_type_pretty}] #{$post_id} - {$project_name} {$subject_text}";


		// set the email template by formtype and poststatus, default to default.html
		$email_path = __DIR__ . '/emails/' .sanitize_file_name( $post_status . '-' . $form_type . '.html' );

		// if post_status is one of these, use post_status.html as the email template
		if ( in_array( $post_status, array( 'cancelled', 'rejected', 'more-info' ) ) )
			$email_path = __DIR__ . '/emails/' .sanitize_file_name( $post_status . '.html' );

		//Prevent Path Traversal
		if (strpos($email_path, '../') !== false || strpos($email_path, "..\\") !== false || strpos($email_path, '/..') !== false || strpos($email_path, '\..') !== false)
			return;

		// if the email template doesnt exist, use the default
		if ( ! file_exists( $email_path ) )
			return;


		// get the contents of the email_template as the body
		$body = file_get_contents( $email_path );


		// extras for inserting into the message dynamically
		$extras = '';

		// if editor is waiting on something.. add to the email
		if ( $post_status == 'more-info' && isset( $_POST['mf_waitingquestion'] ) && $_POST['mf_waitingquestion'] != 'Waiting on?' )
			$extras .= esc_textarea( force_balance_tags( stripslashes( $_POST['mf_waitingquestion'] ) ) );


		if ( $form_type == 'exhibit' && $post_status !== 'more-info' ) {
			if ( isset( $form['sales'] ) && strtolower( $form['sales'] ) == 'yes' ) {
				$extras .= '<p>In your application, you indicated that you are selling or marketing a product. ';
				$extras .= 'Pay your Commercial Maker Fee <a href="https://www.sp.makerfaire.com/ProductDetails.asp?ProductCode=MFCMAKER">here</a>.';
				$extras .= ' Deadline ' . esc_html( $this->commercial_maker_deadline ) . '. If you are not marketing or selling a product, let us know at <a href="mailto:makers@makerfaire.com">makers@makerfaire.com</a>.</p>';
			}

			// Per request from Kate Rowe: Remove Food Conditional Response for Food Maker. (Keep code. We’ll need this for BA15, but not for NY14.)
			// if ( isset( $form['food'] ) && strtolower( $form['food'] ) == 'yes' ) {
			// 	$extras .= '<p>You indicated that food would be included in your exhibit. Fill out the <a href="http://makerfaire.files.wordpress.com/2014/02/mf14_tff_vendor_application.pdf">';
			// 	$extras .= 'Health Permit Form</a> and pay the Health Permit Fee <a href="https://www.sp.makerfaire.com/ProductDetails.asp?ProductCode=MFHPF">here</a>.';
			// 	$extras .= ' Deadline April 4th. If you decided not to include food in your exhibit, email <a href="mailto:makers@makerfaire.com">makers@makerfaire.com</a>.</p>';
			// }

			if ( isset( $form['lighting'] ) && strtolower( $form['lighting'] ) == 'dark' ) {
				$extras .= '<p>You indicated that you would like to be placed in a dark space. Dark space is extremely limited and non-uniform. If you can exhibit in normal or low light, let us know.<p>';
			}
		}



		// search and replace (s-a-r) for alternative to eval
		$sar = array(
			'$subject' => $subject,
			'$form_type' => $form_type,
			'$post_id' => $post_id,
			'$project_name' => $project_name,
			'$contact_first_name' => ucfirst( $maker_name ),
			'$extras' => $extras,
		);

		$message = force_balance_tags( str_replace( array_keys( $sar ), array_values( $sar ), $body ) );

		$r = wp_mail( $tos, $subject, $message, array( 'Content-Type: text/html', "From: {$from}", "Bcc: {$bcc}" ) );
	}
	/*
	* MakerFaire Shortcode handler "mf_form"
	*
	* @access public
	* @param array $atts An array of atributes included in the shortcode
	* @return string The replacement text
	* =====================================================================*/
	public function shortcode_handler( $atts ) {
		foreach ( $atts as $k => $v ) {
			if ( isset( $this->fields[$v] ) ) //Check and Allow only White Labeled Attributes and Values
				$this->{$k} = $v;
		}

		//Only allow forms that exist in folder
		if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'forms/' . $this->type.'.php' ) )
			return '';

		ob_start();

		if ( $this->type == 'makerprofile' ) {
			//$forms = $this->getforms();
			include( plugin_dir_path( __FILE__ ) . 'forms/makerprofile.php' );
		} else {
			//GET FORM BY ID
			if ( isset( $_GET['id'] ) ) {
				$p = get_post( $_GET['id'] );

				if ( $p->post_type = 'mf_form' ) {
					$uid = get_post_meta( $p->ID, 'mf_gigya_id', true );

					$frm = json_decode( str_replace("\'", "'", $p->post_content ) );
					if ( $frm->form_type != $this->type || $frm->maker_faire != $this->maker_faire || $uid == '' )
						unset( $frm );
				}
			}

			//SET DEFAULT VALUES
			foreach ( $this->fields[$this->type] as $sn => $s ) {
				foreach ( array_keys( $s ) as $k ) {
					$v = isset( $frm ) && isset( $frm->$k ) ? $frm->$k : '';

					if ( gettype( $v ) == 'object' ) {
						$v = (array) $v;
					} elseif ( gettype( $v ) == 'array' ) {
						$v = (array) $v;
					}

					if ( $v == '' && $k == 'private_country' ) {
						$v = 'US';
					} elseif ( $v == '' && in_array( $k, array( 'presenter_bio', 'presenter_org', 'presenter_title', 'm_maker_bio' ) ) ) {
						$v = array( '' );
					}

					$this->form['data['.$sn.']['.$k.']'] = $v;
				}
			}

			//SET SOME GENERIC VALUES
			if ( isset( $frm ) ) {
				$this->form['id']          = $p->ID;
				$this->form['uid']         = $uid;
				$this->form['maker_faire'] = $frm->maker_faire;

				foreach ( $frm->tags as $t ) {
					$this->form['tags'][] = $t;
				}

				foreach ( $frm->cats as $c ) {
					$this->form['cats'][] = $c;
				}
			}

			//Show Requested Form
			include( plugin_dir_path( __FILE__ ) . 'forms/' . $this->type.'.php' );
		}

		$c = ob_get_clean();

		return $c;
	}
	/*
	* Get's all forms and echos back for AJAX Listener
	*
	* @access public
	* =====================================================================*/
	public function ajax_getforms() {
		$args = array(
			'post_type'  => 'mf_form',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'mf_gigya_id',
					'value' => sanitize_text_field( $_POST['uid'] )
				),
				array(
					'key' => 'mf_additional_user',
					'value' => sanitize_text_field( $_POST['e'] )
				)
			)
		);

		$q = new WP_Query( $args );
		$f = array(
			'exhibit'   => array(),
			'presenter' => array(),
			'performer' => array()
		);

		foreach ( $q->posts as $p ) {
			$d = json_decode( str_replace( "\'", "'", $p->post_content ) );
			$p->data = $d;
			$f[$d->form_type][$p->ID] = $p;
		}

		die( json_encode( array( 'status'=>'OK', 'forms' => $f ) ) );
	}
	/*
	* Handles frontend form submission
	*
	* @access public
	* =====================================================================*/
	public function ajax_handler() {
		if ( ! isset( $_POST['mf_submit_nonce'] ) || ! wp_verify_nonce( $_POST['mf_submit_nonce'], 'mf_nonce' ) )
			die( json_encode( array( 'status' => 'ERROR', 'errors' => array( 'BAD USER' ) ) ) );

		//POTENTIAL FILES TO BE UPLOADED
		$files  = array(
			'exhibit' => array(
				's1' => array( 'project_photo', 'layout' ),
				's2' => array( 'maker_photo', 'm_maker_photo', 'group_photo' ),
				's3' => array( 'supporting_documents' )
			),
			'performer' => array(
				's1' => array( 'performer_photo' )
			),
			'presenter' => array(
				's1' => array( 'presentation_photo' ),
				's2' => array( 'presenter_photo' )
			)
		);

		//ALL FIELDS FOR ALL 3 FORMS - BINARY IF FIELD IS REQUIRED OR NOT
		$errors = array();
		$res    = array(
			'form_type'   => sanitize_text_field( $_POST['form'] ),
			'maker_faire' => sanitize_text_field( $_POST['maker_faire'] ),
			'uid'         => sanitize_text_field( $_POST['uid'] ),
			'tags'        => array(),
			'cats'        => array()
		);

		//BLOCK ATTEMPTS TO SUBMIT FORMS OF AN UNKNOWN TYPE
		if ( ! in_array( $res['form_type'], array( 'exhibit', 'performer', 'presenter' ) ) )
			die( json_encode( array( 'status' => 'ERROR', 'errors' => array( 'BAD FORM TYPE' ) ) ) );

		/****************************************/
		/*  ERROR CHECK FORM SPECIFIC SITUATIONS
		/****************************************/

		//EXHIBIT STEP 1
		if ( $_POST['form'] == 'exhibit' && $_POST['step'] == 1 ) {
			if ( isset( $_POST['data']['s1']['booth_size'] ) && $_POST['data']['s1']['booth_size'] == 'Other' )
				$this->fields['exhibit']['s1']['booth_size_details'] = 1;

			if ( isset( $_POST['data']['s1']['power'] ) && $_POST['data']['s1']['power'] == 'Yes' && isset( $_POST['data']['s1']['amps'] ) && $_POST['data']['s1']['amps'] == 'special' )
				$this->fields['exhibit']['s1']['amps_details'] = 1;

			if ( isset( $_POST['data']['s1']['hands_on'] ) && $_POST['data']['s1']['hands_on'] == 'Yes' )
				$this->fields['exhibit']['s1']['safety_details'] = 1;

		//EXHIBIT STEP 2
		} elseif ( $_POST['form'] == 'exhibit' && $_POST['step'] == 2 ) {

			if ( isset( $_POST['data']['s2']['maker'] ) && $_POST['data']['s2']['maker'] == 'One maker' )
				$this->fields['exhibit']['s2']['maker_name'] = $this->fields['exhibit']['s2']['maker_email'] = $this->fields['exhibit']['s2']['maker_photo'] = $this->fields['exhibit']['s2']['maker_bio'] = 1;

			if ( isset( $_POST['data']['s2']['maker'] ) && $_POST['data']['s2']['maker'] == 'A list of makers' )
				$this->fields['exhibit']['s2']['m_maker_name'] = $this->fields['exhibit']['s2']['m_maker_email'] = $this->fields['exhibit']['s2']['m_maker_photo'] = $this->fields['exhibit']['s2']['m_maker_bio'] = 1;

			if ( isset( $_POST['data']['s2']['maker'] ) && $_POST['data']['s2']['maker'] == 'A group or association' )
				$this->fields['exhibit']['s2']['group_name'] = $this->fields['exhibit']['s2']['group_photo'] = $this->fields['exhibit']['s2']['group_bio'] = 1;

		//PERFORMER STEP 1
		} elseif ( $_POST['form'] == 'performer' && $_POST['step'] == 1 ) {

			if ( isset( $_POST['data']['s1']['compensation_type'] ) && strpos( $_POST['data']['s1']['compensation_type'], '$' ) !== false )
				$this->fields['performer']['s1']['compensation'] = 1;
		}

		/****************************************/
		/*  ERROR CHECK GENERIC
		/****************************************/
		if ( isset( $_POST['form'], $_POST['step'], $files[$_POST['form']]['s'.$_POST['step']] ) ):
			foreach ( $files[$_POST['form']]['s'.$_POST['step']] as $n ) {
				if ( $this->fields[$_POST['form']]['s'.$_POST['step']][$n] && ! isset( $_FILES[$n] ) && ! isset( $_POST['data']['s'.$_POST['step']][$n] ) )
					$errors['s'.$_POST['step']][$n] = 'Photo Required.';
			}
		endif;


		foreach ( $this->fields[$_POST['form']] as $s => $f ) {

			foreach ( $f as $k => $r ) {
				$v = isset( $_POST['data'][$s][$k] ) ? $_POST['data'][$s][$k] : '';

				if ( is_array( $v ) )
					$v = array_values( $v );

				//CHECK ERRORS ON THIS STEP ONLY
				if ( $s == 's'.$_POST['step'] ) {
					//FILE UPLOAD ERROR CHECK
					if ( is_array( $v ) ) {
						foreach ( $v as $i => $av ) {
							if ( $r && ! in_array( $k, $files[$_POST['form']][$s] ) && $av == '' ) { //Empty / Not Set / Blank
								$errors[$s][$k][$i] = 'Required.';
							} elseif ( $r && strpos( $k, 'email' ) !== false && ! is_email( $av ) )  { //Check Email
								$errors[$s][$k][$i] = 'Valid Email Required.';
							}
						}
					} elseif ( is_array( $files[$_POST['form']][$s] ) ) { // Check that our data is an array first before sending it or else the form breakes.
						if ( $r && ! in_array( $k, $files[$_POST['form']][$s] ) && $v == '' )  //Empty / Not Set / Blank
							$errors[$s][$k] = 'Required.';
					} elseif ( ( $k == 'public_description' && strlen( $v ) > 255 ) || ( ( $k == 'maker_bio' || $k == 'm_maker_bio' || $k == 'group_bio' ) && strlen( $v ) > 500 ) ) { //Input too many characters.
						$errors[$s][$k] = 'Too Long.';
					} elseif ( $r && $k == 'email' && ! is_email( $v ) ) { //Check Email
						$errors[$s][$k] = 'Valid Email Required.';
					} elseif ( $r && $k == 'compensation' && ! is_numeric( $v ) ) { //Check Value
						$errors[$s][$k] = 'Number Required.';
					}
				}

				//SPECIAL DATA FORMATS
				if ( $k == 'equipment' ) //Make Sure NewLines are Preserved on Equipment List
					$v = nl2br( $v );

				//SANATIZE ALL DATA
				if ( is_array( $v ) ) {
					foreach ( $v as $nn => $d ) {
						$v[$nn] = wp_filter_post_kses( $d );
					}
					$res[$k] = $v;
				} else {
					$res[$k] = wp_filter_post_kses( $v );
				}
			}
		}

		if ( isset( $_POST['radio_frequency'] ) ) {
			foreach ( $_POST['radio_frequency'] as $rf ) {
				$res['radio_radio_frequency'][] = $rf;
			}
		}

		if ( isset( $_POST['tag'] ) ) {
			foreach ( $_POST['tag'] as $t ) {
				$res['tags'][] = $t;
			}
		}

		if ( isset( $_POST['cat'] ) ) {
			foreach ( $_POST['cat'] as $c ) {
				$res['cats'][] = $c;
			}
		}

		//IF THERE ARE ERRORS DIE WITH ERRORS
		if ( ! empty( $errors ) )
			die( json_encode( array( 'status' => 'ERROR', 'errors' => $errors ) ) );

		$file_res = $thumbs_res = array();

		//ERROR CHECK AND UPLOAD FILES
		if ( isset( $_POST['form'], $_POST['step'], $files[$_POST['form']]['s'.$_POST['step']] ) ):
			foreach ( $files[$_POST['form']]['s'.$_POST['step']] as $n ) {
				if ( empty( $errors ) && ((isset( $_POST[$n] ) && $_POST[$n] != '' ) || isset( $_FILES[$n] ) ) ) {
					if ( isset( $_FILES[$n] ) && $_FILES[$n]['name'] != '' ) {
						if ( $r = $this->upload( $_FILES[$n], ! in_array( $n, array( 'layout', 'supporting_documents' ) ) ) ) {
							$file_res[$n]     = $r['url'];
							$thumbs_res[$n]   = $r['thumb'];
							$res[$n]          = $r['url'];
							$res[$n.'_thumb'] = $r['thumb'];
						// TODO: Fix error reporting for non-supported files. - CG
						// } elseif ( $_FILES[$n]['type'] != 'image/jpeg' || $_FILES[$n]['type'] != 'image/gif' || $_FILES[$n]['type'] != 'image/png') {
						// 	$error['s' . $_POST['step']][$n] = 'Not an accepted file type.';
						} else {
							$errors['s'.$_POST['step']][$n] = 'Valid 500px or Wider Image Required.';
						}
					}
				} elseif ( empty( $errors ) && $res[$n] == '' && $this->fields[$_POST['form']]['s'.$_POST['step']][$n] ) {
					$errors['s'.$_POST['step']][$n] = 'Photo Required.';
				}
			}
		endif; // isset( $_POST['form'], $_POST['step'], $files[$_POST['form']]['s'.$_POST['step']]

		//IF THERE ARE ERRORS DIE WITH ERRORS
		if ( ! empty( $errors ) )
			die( json_encode( array( 'status'=>'ERROR', 'errors' => $errors ) ) );

		//IF THERE ARE NO ERRORS SAVE AND DIE WITH ID
		if ( isset( $_POST['id'] ) && $_POST['id'] ) {
			$id = $this->update_mf_form( $_POST['form'], $_POST['id'], $res, $_POST['step'] );
		} else {
			$id = $this->create_mf_form( $res, $_POST['form'] );
		}

		die( json_encode( array( 'status'=>'OK', 'id' => $id, 'files' => $file_res, 'thumbs' => $thumbs_res ) ) );
	}

	/*
	* Upload images / documents and save as WP attachments
	*
	* @access public
	* @param  array   $ufile         File object $t Form type
	* @param  boolean $require_size  Check if deminsions are valid
	*
	* @return boolean|array False if error and upload array if valid
	* =====================================================================*/
	private function upload( $ufile, $require_size ) {
		if ( ! function_exists( 'wp_handle_upload' ) )
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

		// Check our uploaded file types.
		$info = pathinfo( $ufile['name'] );
		if ( ! in_array( strtolower( $info["extension"] ), array( 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'pps', 'ppsx', 'odt', 'xls', 'xlsx' ) ) )
			return false;

		if ( $require_size ) {
			list( $w, $h ) = getimagesize( $ufile['tmp_name'] );
		}

		$overrides = array( 'test_form' => false );
		$file      = wp_handle_upload( $ufile, $overrides );

		if ( ! $file )
			return false;

		$wp_upload_dir = wp_upload_dir();

		$attachment = array(
			'guid'           => $file['url'],
			'post_mime_type' => $file['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $ufile['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		//CREATE THUMBNAIL
		$thumb = image_make_intermediate_size( $file['file'], 500, 500 );

		$attach_id = wp_insert_attachment( $attachment, $file['file'] );


		if ( ! function_exists( 'wp_crop_image' ) )
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $ufile['name'] );

		wp_update_attachment_metadata( $attach_id, $attach_data );

		return array( 'id' => $attach_id, 'url' => $file['url'], 'thumb'=>( $thumb ? $wp_upload_dir['url'].'/'.$thumb['file'] : $file['url'] ) );
	}

	/*
	* Create a MakerFAIRE Form
	*
	* @access public
	* @param array  $r Form data
	* @param string $t Form type
	*
	* @return integer Form that's created's ID
	* =====================================================================*/
	private function create_mf_form( $r, $t ) {

		//Make Sure this is the first and only time through
		if ( isset( $_POST['mf_updated'] ) )
			return false;

		$n = $t == 'exhibit' ? $r['project_name'] : ( $t == 'performer' ? $r['performer_name'] : $r['presentation_name'] );

		$d = array(
			'post_author'  => 0,
			'post_content' => json_encode( $r ),
			'post_title'   => $n,
			'post_status'  => 'in-progress',
			'post_type'    => 'mf_form'
		);

		// if uid is 0, empty, or not set then just return and do not insert the post or update the post_meta
		if ( ! isset( $r['uid'] ) || $r['uid'] == '0' || $r['uid'] == '' )
			return;

		$_POST['mf_updated'] = 1;

		$pid = wp_insert_post( $d );

		add_post_meta( $pid, '_mf_form_type', $t );
		add_post_meta( $pid, 'mf_gigya_id',  $r['uid'] );
		wp_set_object_terms( $pid, $t, 'type' );
		wp_set_object_terms( $pid, $r['tag'], 'post_tag' );
		wp_set_object_terms( $pid, $r['cat'], 'category' );
		wp_set_object_terms( $pid, esc_html( $this->faire_friendly_name ), 'faire' );

		return $pid;
	}

	/*
	* Update a MakerFAIRE Form
	*
	* @access public
	* @param string  $t  Form type
	* @param integer $id Form ID
	* @param array   $r  Form data
	* @param integer $s  From Step
	*
	* @return integer Form that's updated ID
	* =====================================================================*/
	private function update_mf_form( $t, $id, $r, $s ) {

		//Make Sure this is the first and only time through
		if ( isset( $_POST['mf_updated'] ) )
			return false;

		$n = $t == 'exhibit' ? $r['project_name'] : ( $t == 'performer' ? $r['performer_name'] : $r['presentation_name'] );

		//Reset cats if back-end update
		if((isset($_POST['post_category'])) && (is_array($_POST['post_category'])) ) {
			$cats_clean = array();
			foreach ($_POST['post_category'] as $cat_id ) {
				$cat = wpcom_vip_get_term_by('id', $cat_id, 'category');
				if($cat !== false) {
					$cats_clean[] = $cat->slug;
				}
			}
			if(isset($r['cats']) ) {
				$r['cats'] = join(', ',$cats_clean);
			}
		}


		$d = array(
			'ID'           => $id,
			'post_content' => json_encode( $r ),
			'post_title'   => $n,
		);

		//SUBMIT FINAL FORM
		if ( $s == 4 ) {
			$d['post_status'] = 'proposed';
			$emails           = $t == 'exhibit' ? array_slice( $r['m_maker_email'], 1 ) : ( $t == 'presenter' ? array_slice( $r['presenter_email'], 1 ) : array() );

			foreach ( $r as $k => $v ) {
				if ( is_array( $v ) ) {
					$r[$k] = implode( ', ', $v );
				}
			}

			// if uid is 0, empty, or not set then just return and do not insert the post or update the post_meta
			if ( ! isset( $r['uid'] ) || $r['uid']=='0' || $r['uid']=='' )
				return;

			//SYNC WITH MAKE DB
			$this->sync_jdb( $id );

			//SEND CONFIRMATION EMAIL TO MAKER
			$this->send_maker_email( $r, $n, $id );

			//SEND EMAILS TO ADDITIONAL USERS
			if ( $t == 'exhibit' ) {
				if ( ! empty( $emails ) ) {
					foreach ( $emails as $e ) {
						add_post_meta( $id, 'mf_additional_user', $e );
					}
					$this->send_maker_invite_email( $id, $emails, $r, $n );
				}
			}
		}

		$_POST['mf_updated'] = 1;

		update_post_meta( $id, '_mf_form_type',  $t );
		update_post_meta( $id, '_mf_maker_name', $r['name'] );
		
		// Break out tags first
		/// Only used for wp-admin update entry tags. Disable 07.30.14
		/*        	
		if(isset($_POST['exhibit']) && isset($_POST['exhibit']['tags']) ) {
             
        		$tag_string = $_POST['exhibit']['tags'];
            		$tags_clean = array();
            		$tags = explode( ',', $tag_string );
            		foreach ($tags as $tag ) {
                    		$tags_clean[] = sanitize_title( $tag );
            		}
            
            		wp_set_object_terms( $id, $tags_clean, 'post_tag');

        	} */
        	
		//Only used for wp-admin disabled 8.6.2014		
        // need to break them out for categories...
		/*
		if( (isset($_POST['exhibit'])) && isset($_POST['exhibit']['cats']) ) {
			$cat_string = $_POST['exhibit']['cats'];
			$cats = explode( ',', $cat_string );
			foreach ($cats as $cat ) {
				#wp_set_object_terms( $id, sanitize_title( $cat ), 'category', true );
			}
		}*/

		// Handle front-end cats properly
		if((isset($r['cats'])) && (is_array($r['cats'])) ) {
            $cats_clean = array();
			$cats = $r['cats'];
			foreach ($cats as $category ) {
        		$cats_clean[] = sanitize_title( $category );
            }
			wp_set_object_terms( $id, $cats_clean, 'category');

		}

        // Handle front-end tags properly
        if((isset($r['tags'])) && (is_array($r['tags'])) ) {
            $tags_clean = array();
			$tags = $r['tags'];
			foreach ($tags as $tag ) {
				$tag_cleaned = sanitize_title( $tag );
				$term = wpcom_vip_get_term_by('slug', $tag_cleaned, 'post_tag');
				if($term !== false) {
        				$tags_clean[] = $tag_cleaned;
				}
				
            }
			wp_set_object_terms( $id, $tags_clean, 'post_tag' );

		}

		return wp_update_post( $d );
	}

	/*
	* Sends a email to form submitter
	*
	* @access public
	* @param array $r Form data
	* @param string $n Name
	* @return boolean if wp_mail was successful.
	* =====================================================================*/
	private function send_maker_email( $r, $n, $id ) {
		$bad  = array( '&#039;', "\'", '&#8217;', '&#38;', '&#038;', '&amp;', '&quot;', '&#34;', '&#034;', '&#8211;', '&lt;', '&#8230;', );
		$good = array( "'",      "'",  "'",       "&",     "&",      '&',     '"',      "'",     '"',      '–',       '>',    '...',     );

		// Don't send if there's no email or no form type
		if ( empty( $r['form_type'] ) || empty( $r['email'] ) )
			return false;

		// Setup app info
		$app_name = esc_html( $n );
		$app_info = esc_html( '[' . ucfirst( $r['form_type'] ) . ' ' . intval( $id ) . ']' );

		// Subject
		$subject  = str_replace( $bad, $good, stripslashes( sprintf( 'Maker Faire Application Submitted: %s %s', $app_name , $app_info ) ) );

		// Email Body
		$m = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
		$m.='<p>' . esc_html( ucfirst( $r['name'] ) ) . ',</p>';
		$m.='<p>Thanks for your interest in participating in ' . esc_html( $this->faire_friendly_name ) . '! We have received your application for: <strong>' . $app_name . '</strong> ' . $app_info . '.</p>';
		if (  $r['form_type'] == 'presenter' ) {
			$m .= '<p>You will be notified as to the status of your application no later than <strong>August 15th</strong>.</p>';
			$m .= '<p>If there are changes to your application (e.g. a new picture or a new panelist), you can update your presentation application as you receive new information. (Note that those submitting panel presentation proposals are responsible for collecting and entering all bio information from proposed panelists—so please come back and finish your application if those fields are not yet complete.)</p>';
			$m .= '<ul><li>Log into your maker account from <a href="' . esc_url( home_url() ) . '">makerfaire.com</a>. The login link is in the blue header at the top of every page.</li><li>After login, you\'ll see a link to edit any applications you\'ve started or submitted.</li></ul>';
			$m .= '<p>If your presentation is accepted, we will do our best to accommodate your requests. Please understand that your requests are not guaranteed. What we can provide will be confirmed in a follow-up letter after acceptance.</p>';
			$m .= '<p>In the meantime, please stay in touch and spread the word: like us on <a href="https://www.facebook.com/makerfaire">Facebook</a> and follow us on <a href="https://twitter.com/makerfaire">Twitter</a> and <a href="https://plus.google.com/communities/105823492396218903971">Google+</a></p>';
			$m .= '<p>Sabrina Merlo<br />Program Director<br />Maker Media, Inc.</p>';
			$m .= '<p>Maker Faire (<a href="' . esc_url( home_url() ) . '">makerfaire.com</a>)<br />MAKE (<a href="http://makezine.com">makezine.com</a>)</p>';
			// $m .= '<p>ps. You can update your application anytime until the Call For Makers closes:</p>';
			// $m .= '<ol><li>Log into your maker account from makerfaire.com. The login link is in the blue header at the top of every page.</li><li>After login, you\'ll see a link to edit any applications you\'ve started or submitted.</li><ol>';
		} elseif ( $r['form_type'] == 'performer' ) {
			// Call for Makers is running Auto-Responder text
			// $m .= '<p>You can update your application anytime until the Call For Makers closes:</p>';
			// $m .= '<ol><li>Log into your maker account from <a href="' . esc_url( home_url() ) . '">makerfaire.com</a>. The login link is in the blue header at the top of every page.</li>';
			// $m .= '<li>After login, you\'ll see a link to edit any applications you\'ve started or submitted.</li></ol>';
			$m .= '<p>You will be notified as to the status of your application no later than <strong>August 22nd</strong>.</p>';
			$m .= '<p>If your application is accepted, we have agreed to the concept of your performance. However, we are not able to guarantee all of your requests at this time. Our team will contact you before the event to finalize details.</p>';
			$m .= '<p>Spread the word - Like us on <a href="https://www.facebook.com/makerfaire">Facebook</a> and follow us on <a href="https://twitter.com/makerfaire">Twitter</a> and <a href="https://plus.google.com/communities/105823492396218903971">Google+</a></p>';
			// Call for Makers is closed Auto-Responder text
			// $m .= '<p>Please note that the deadline for Maker entries has passed. However, there are several ways that you can still participate!:</p>';
			// $m .= '<ol><li>We will consider your entry as a last-minute addition. If you do not receive an acceptance letter by April 29, we were not able to find space for your exhibit. We will do our best to notify you before then.</li>';
			// $m .= '<li>If you would like to volunteer your time and make an invaluable contribution to the success of Maker Faire, please sign up for our <a href="' . esc_url( home_url( '/bay-area-2014/traveler-program/' ) ) . '">Maker Faire Traveler Program</a>, which is a platform to enhance your skills and learn about the Maker Movement. You will have a behind-the-scenes experience, and help make the Greatest Show (and Tell) on Earth happen! <a href="' . esc_url( home_url( '/bay-area-2014/traveler-program/' ) ) . '">Learn more here</a>.</li>';
			// $m .= '<li>Plan to come as an attendee, enjoy the show and support the Maker movement by <a href="http://www.eventbrite.com/e/maker-faire-bay-area-2014-tickets-9098302267?aff=MFwbBuytix">purchasing your tickets early</a>!</li>';
			// $m .= '<li>Spread the word - Like us on <a href="https://www.facebook.com/makerfaire">Facebook</a> and follow us on <a href="https://twitter.com/makerfaire">Twitter</a> and <a href="https://plus.google.com/communities/105823492396218903971">Google+</a></li></ol>';
			$m .= '<p>Sherry Huss<br />Vice President<br />Maker Media, Inc.</p>';
			$m .= '<p>Maker Faire (<a href="' . esc_url( home_url() ) . '">makerfaire.com</a>)<br />MAKE (<a href="http://makezine.com">makezine.com</a>)</p>';
			// $m .= '<p>ps. You can update your application anytime until the Call For Makers closes:</p>';
			// $m .= '<ol><li>Log into your maker account from makerfaire.com. The login link is in the blue header at the top of every page.</li><li>After login, you\'ll see a link to edit any applications you\'ve started or submitted.</li><ol>';
		} else {
			// Call for Makers is running Auto-Responder text
			// $m .= '<p>You will be notified as to the status of your application no later than <strong>August 8th</strong>.</p>';
			// $m .= '<p>If your application is accepted, we have agreed to the concept of your exhibit. However, we are not able to guarantee all of your requests at this time. What we can provide will be outlined in a confirmation letter before the event.</p>';
			// $m .= '<p>Spread the word - Like us on <a href="https://www.facebook.com/makerfaire">Facebook</a> and follow us on <a href="https://twitter.com/makerfaire">Twitter</a> and <a href="https://plus.google.com/communities/105823492396218903971">Google+</a></p>';
			// Call for Makers is closed Auto-Responder text
			$m .= '<p>Please note that the deadline for Maker entries has passed. However, there are several ways that you can still participate!</p>';
			$m .= '<ol><li>We will consider your entry as a last-minute addition. If you do not receive an acceptance letter by September 5, we were not able to find space for your exhibit.</li>';
			$m .= '<li>Volunteer your time and make an invaluable contribution to the success of Maker Faire. Please sign up for our <a href="' . esc_url( home_url( '/new-york-2014/traveler-program/' ) ) . '">Maker Faire Traveler Program</a>, which is a platform to enhance your skills and learn about the Maker Movement. You will have a behind-the-scenes experience, and help make the Greatest Show (and Tell) on Earth happen! <a href="' . esc_url( home_url( '/new-york-2014/traveler-program/' ) ) . '">Learn more here</a>.</li>';
			$m .= '<li>Come as an attendee, enjoy the show and support the Maker movement by <a href="https://makerfaireny2014.eventbrite.com/">purchasing your tickets early</a>!</li>';
			$m .= '<li>Spread the word - Like us on <a href="https://www.facebook.com/makerfaire">Facebook</a> and follow us on <a href="https://twitter.com/makerfaire">Twitter</a> and <a href="https://plus.google.com/communities/105823492396218903971">Google+</a></li></ol>';
			$m .= '<p>Sherry Huss<br />Vice President<br />Maker Media, Inc.</p>';
			$m .= '<p>Maker Faire (<a href="' . esc_url( home_url() ) . '">makerfaire.com</a>)<br />MAKE (<a href="http://makezine.com">makezine.com</a>)</p>';
			// $m .= '<p>ps. You can update your application anytime until the Call For Makers closes:</p>';
			// $m .= '<ol><li>Log into your maker account from makerfaire.com. The login link is in the blue header at the top of every page.</li><li>After login, you\'ll see a link to edit any applications you\'ve started or submitted.</li><ol>';
		}
		$m.='<p>Maker Media, Inc.<br />1005 Gravenstein Hwy North<br />Sebastopol, CA 95472</p>';
		$m.='</body></html>';

		$body = htmlspecialchars_decode( stripslashes( $m ) );

		// Headers
		$headers = array(
			'Content-Type: text/html',
			'From: Maker Faire <makers@makerfaire.com>',
			'Bcc: Maker Faire <makers@makerfaire.com>'
		);

		// Send the email
		$r = wp_mail( $r['email'], $subject, $body, $headers );

		return $r;
	}
	/*
	* Sends a email to addtional makers
	*
	* @access public
	* @param integer $id Form ID
	* @param array $emails Emails to send
	* @param array $r Form data
	* @param string $i Include body
	* @param string $n Name
	* @return boolean if wp_mail was successful.
	* =====================================================================*/
	private function send_maker_invite_email( $id, $emails, $r, $n ) {
		$m = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
		$m .= '<p>Thanks for your interest in participating in ' . esc_html( $this->faire_friendly_name ) . '!</p><br />';
		$m .= '<p>'.esc_html( ucwords( $r['name'] ) ).' has submitted an application and indicated you were part of their exhibit or presentation. We need you to create a maker account at ';
		$m .= '<a href="' . esc_url( home_url() ) . '" alt="Maker Faire">makerfaire.com</a> and provide some additional details that we can include about you.</p>';
		$m .= '<p>Create an account by selecting "Register" in the header.</p>';
		$m .= '<br /><p>Spread the word - Like us on <a href="http://facebook.com/makerfaire" alt="Like Maker Faire Facebook">Facebook</a> and follow us on ';
		$m .= '<a href="https://twitter.com/makerfaire" alt="Follow Maker Faire Twitter">Twitter</a> and <a href="https://plus.google.com/+MAKE/posts" alt="Maker Faire Google+">G+</a></p>';

		$app_name = str_replace( '&amp;', '&', esc_html( $n ) );

		$r = wp_mail( $emails, 'Maker Faire Application: '.intval( $id ).': ' . $app_name, $m, array( 'Content-Type: text/html', 'From: Maker Faire <makers@makerfaire.com>','Bcc: Maker Faire <makers@makerfaire.com>' ) );

		return $r;
	}
	/*
	* Tests if a phone number is valid
	*
	* @access public
	* @param string $phone Phone to check
	* @return boolean if valid phone number
	* =====================================================================*/
	private function is_phone( $phone ) {
		$phone = preg_replace( '/[^0-9]/', '', $phone );

		return (bool) strlen( $phone ) == 10 || strlen( $phone ) == 11;
	}
	/*
	* Tests if a zipcode is valid
	*
	* @access public
	* @param string $zip Zipcode to check
	* @return boolean if valid zipcode
	* =====================================================================*/
	private function is_zip( $zip ) {
		return (bool) preg_match( '/^[0-9]{5}(-[0-9]{4}){0,1}$/', $zip );
	}
	/*
	* Outputs a text field
	*
	* @access public
	* @param string $n textbox name
	* @param array $attr An array of attributes to include
	* =====================================================================*/
	public function text( $n, $attr = array() ) {
		?>
		<input type="text" name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $this->form[$n] ); ?>" <?php
		foreach ( $attr as $a => $av ) {
			echo esc_html( $a ) . '="'.esc_attr( $av ).'" ';
		}
		?> />
		<?php
	}
	/*
	* Outputs a radio button
	*
	* @access public
	* @param string $n radio name
	* @param array $set An array of values and labels
	* =====================================================================*/
	public function radio( $n, $set ) {
		foreach ( $set as $k => $v ) :
			$k = is_numeric( $k ) ? $v : $k;
			?>
			<div><input name="<?php echo esc_attr( $n ); ?>" type="radio" value="<?php echo esc_attr( $k ); ?>" <?php checked( $k, $this->form[$n] ); ?> /> <?php echo esc_html( $v ); ?></div>
			<?php
		endforeach;
	}
	/*
	* Outputs a checkbox
	*
	* @access public
	* @param string $n checkbox name
	* @param array $set An array of values and labels
	* =====================================================================*/
	public function checkbox( $n, $set ) {
		foreach ( $set as $k => $v ) :
			$k = is_numeric( $k ) ? $v : $k;
			?>
			<div><input name="<?php echo esc_attr( $n ); ?>[]" type="checkbox" value="<?php echo esc_attr( $k ); ?>" <?php checked(in_array( $v, (array )$this->form[$n] ) ); ?> /> <?php echo esc_html( $v ) ?></div>
			<?php
		endforeach;
	}
	/*
	* Outputs a textarea
	*
	* @access public
	* @param string $n textarea name
	* @param array $attr An array of attributes to include
	* =====================================================================*/
	public function textarea( $n, $attr = array() ) {
		?>
		<textarea name="<?php echo esc_attr( $n ); ?>" <?php
		foreach ( $attr as $a => $av ) {
			echo esc_html( $a ).'="'. esc_attr( $av ) .'" ';
		}
		?>><?php echo esc_textarea( $this->form[$n] ); ?></textarea>
		<?php
	}
	/*
	* Outputs a select field
	*
	* @access public
	* @param string $n Select name
	* @param array $set An array of values and labels
	* =====================================================================*/
	public function select( $n, $set ) {
		?>
		<select name="<?php echo esc_attr( $n ); ?>">
			<option value="">-- Select One</option>
			<?php foreach ( $set as $v => $lbl ) : ?>
				<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $v, $this->form[$n] ); ?>> <?php echo esc_html( $lbl ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
	/*
	* Outputs a file field
	*
	* @access public
	* @param string $n key of file path
	* @param string $rn real key of path
	* =====================================================================*/
	public function file( $n, $rn ) {
		if ( $this->form[$n] == '' ) {
			?><input name="<?php echo esc_attr( $rn ); ?>" type="file" /><?php
		} else {
			$v = $this->form[$n];
			$thumb = $this->form[substr( $n, 0, -1 ).'_thumb]'];

			if ( $rn == 'supporting_documents' || $rn == 'layout' ) {
				?><a href"<?php echo esc_attr( $v ); ?>" /><?php echo esc_url( $v ); ?></a><?php
			} else {
				?><img src="<?php echo esc_attr( $this->form[substr( $n, 0, -1 ).'_thumb]'] ); ?>" style="max-width:600px" /><?php
			}

			?>
			<input type="hidden" name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $v ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( substr( $n, 0, -1 ).'_thumb]' ); ?>" value="<?php echo esc_attr( $thumb ); ?>" />
			<div id="<?php echo esc_attr( $rn ); ?>" class="info overwrite">Overwrite File</div>
			<?php
		}
	}
	/*
	* Queue up all JS and CSS
	*
	* @access public
	* =====================================================================*/
	public function enqueue() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mff_js',             plugins_url( 'assets/js/mff.js', __FILE__ ) );
		wp_enqueue_script( 'mff_jquery_form_js', plugins_url( 'assets/js/jquery.form.js', __FILE__ ) );

		wp_enqueue_style( 'mff_css', plugins_url( 'assets/css/style.css', __FILE__ ) );
	}

	/*
	* Queue up all Admin JS and CSS
	*
	* @access public
	* =====================================================================*/
	public function admin_enqueue( $hook ) {
		global $post_type;

    	// Load our custom search JS ONLY on the edit.php when viewing the mf_form CPT.
		if ( ( 'edit.php' == $hook && $post_type == 'mf_form' ) || ( 'post.php' == $hook && $post_type == 'mf_form' ) )
			wp_enqueue_script( 'mf-custom-search', get_stylesheet_directory_uri() . '/plugins/maker-faire-forms/assets/js/search-id.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'mf-reports', get_stylesheet_directory_uri() . '/plugins/maker-faire-forms/assets/js/reports.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_style( 'mff_css', get_stylesheet_directory_uri() . '/plugins/maker-faire-forms/assets/css/style.css' );
	}


	/*
	* Output list of project images
	*
	* @access public
	* =====================================================================*/
	public function show_project_images() {

		$projects = $this->get_all_forms( 'form_type' );
		?>
		<div class="wrap" id="iscic">
			<?php echo screen_icon(); ?>
			<h2><?php echo wpcom_vip_get_term_by( 'slug', MF_CURRENT_FAIRE, 'faire')->name; ?>
				<div style="font-size:75%">Report: View Project Images</div>
			</h2>
			<h3>Total: <?php echo intval( count( $projects['exhibit'] ) + count( $projects['performer'] ) + count( $projects['presenter'] ) ); ?> Proposed, Waiting for Info and Accepted Projects</h3>
			<?php
			foreach( $projects as $type => $list ) :
				krsort( $list );
				?>
				<div class="clear"></div>
				<h1><?php echo esc_html( ucwords( $type ) ); ?> Applications</h1>

				<?php
				foreach( $list as $id => $form ) :
				?>
					<div style="float:left; width:400px; margin:20px; height:500px;">
						<a href="<?php echo esc_url( get_edit_post_link( $id ) ) ?>">
							<img src="<?php echo esc_url( $form['form_photo_thumb'] ); ?>" style="max-width:400px; max-height:400px; margin-bottom:10px;" />
						</a>
						<div style="font-size:12px;">
							<a href="<?php echo esc_url( get_edit_post_link( $id ) ) ?>">
								<?php echo esc_html( ucwords( $form['form_type'] ) ); ?> : <?php echo esc_html( $form[ 'project_name' ] ); ?> (<?php echo intval( $id ); ?>)<br />
							</a>
							Maker: <?php echo esc_html( $form['name'] ); ?>
						</div>
					</div>
				<?php
				endforeach; ?>
			<?php
			endforeach; ?>
		</div><!--wrap-->
		<?php
	}
	/*
	* Output Reports List
	*
	* @access public
	* =====================================================================*/
	public function show_reports_page() {
		// Get the post statuses
		global $wp_post_statuses;

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			//NONCE CHECK
			if ( isset( $_POST['mf_syncjdb'] ) && wp_verify_nonce( $_POST['mf_syncjdb'], 'mf_syncjdb' ) )
				$this->sync_jdb();
			elseif ( isset( $_POST['mf_syncstatusjdb'] ) && wp_verify_nonce( $_POST['mf_syncstatusjdb'], 'mf_syncstatusjdb' ) ) {
				echo $this->sync_all_status_jdb( $_POST['offset'] );
			}
		}

		// $stats = $this->get_reports_stats();
		$fails = $this->get_failed_syncs();
		$app_types = array(
			'Exhibit' => 'exhibit',
			'Performer' => 'performer',
			'Presenter' => 'presenter',
		);
		$disallowed_post_statuses = array(
			'publish',
			'future',
			'pitch',
			'assigned',
			'trash',
			'spam',
			'inherit',
			'private',
			'auto-draft',
		);
		$locations = mf_get_all_locations();
		$faires = get_terms( 'faire', array( 'hide_empty' => false, 'order' => 'DESC' ) );
		$tags = get_terms( 'post_tag', array( 'hide_empty' => false ) ); ?>
		<div class="wrap" id="iscic">
			<?php echo screen_icon(); ?>
			<h2>Maker Faire Reports</h2>
			<div style="width:45%; float:left">
				<h3><?php echo wpcom_vip_get_term_by( 'slug', MF_CURRENT_FAIRE, 'faire')->name; ?> Stats</h3>
				<?php echo mf_count_post_statuses( 'table' ); ?>
			<h1 style="margin-top:20px;">Sync Status with JDB</h1>
			Syncs 100 applications at a time.<br />To do a full sync start at 0 and increase by 100 until you're done.
			<form action="" method="post">
				<p>
					<div style="float:left; width:50px">
						<strong>Start</strong><br />
						<select name="offset">
							<option value="0">0</option>
							<?php foreach( range( 100, intval( wp_count_posts( 'mf_form' )->accepted ), 100 ) as $v ) : ?>
							<option value="<?php echo intval( $v ); ?>"><?php echo intval( $v ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</p>
				<div class="clear"></div>
				<p class="submit"><input type="submit" value="Sync Statuses With JDB Now" class="button button-primary button-large" /></p>
				<?php wp_nonce_field( 'mf_syncstatusjdb', 'mf_syncstatusjdb' ); ?>
			</form>
			<h1 style="margin-top:40px;">Sync with JDB</h1>
			<?php if( !isset( $_SERVER['SERVER_ADDR_NAME'] ) || $_SERVER['SERVER_ADDR_NAME'] != 'iscrackweb1' ) : ?>
				Last Sync : <?php echo esc_html( get_option( 'mf_full_jdb_sync', 'NEVER' ) ); ?><br />
				<form action="" method="post">
					<p class="submit"><input type="submit" value="Sync With JDB Now" class="button button-primary button-large" /></p>
					<?php wp_nonce_field( 'mf_syncjdb', 'mf_syncjdb' ); ?>
				</form>
			<?php endif; ?>
			<h2>Failed Syncs</h2>
			<ul>
			<?php foreach( $fails as $fail ) : ?>
				<li><a href="post.php?post=<?php echo intval( $fail->ID ); ?>&amp;action=edit"><?php echo esc_html( $fail->post_title ); ?></a></li>
			<?php endforeach; ?>
			</ul>
			</div>
			<div style="width:45%; float:right; border:1px solid #DFDFDF; border-radius:3px; padding:10px; background:#F2F2F2">
				<h1>Generate Reports</h1>

				<form method="get" class="reports-form" style="border-bottom:1px solid #dfdfdf">
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $_REQUEST['post_type'] ); ?>" />
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
					<input type="hidden" name="form_export" value="true" />
					<?php wp_nonce_field( 'mf_export_check', 'export_nonce', false ); ?>

					<ul>
						<?php if ( ! empty( $faires ) ) : ?>
							<li>
								<label for="faire">Faire Applications</label>
								<select name="faire" id="faire">
									<?php foreach ( $faires as $faire ) : ?>
										<option value="<?php echo esc_attr( $faire->slug ); ?>"<?php selected( $faire->slug, MF_CURRENT_FAIRE ); ?>><?php echo esc_html( $faire->name ); ?></option>
									<?php endforeach; ?>
								</select>
							</li>
						<?php endif; ?>
						<li>
							<label for="app-type">Application Type</label>
							<select name="type" id="app-type">
								<option value="all">All Types</option>
								<?php foreach ( $app_types as $app_type => $app_value ) : ?>
									<option value="<?php echo esc_attr( $app_value ); ?>"><?php echo esc_html( $app_type ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<li>
							<label for="post-status">Application Status</label>
							<select name="post_status" id="post-status">
								<option value="all">All Statuses</option>
								<?php foreach ( $wp_post_statuses as $status => $name ) {
									if ( ! in_array( $status, $disallowed_post_statuses ) )
										echo '<option value="' . esc_attr( $status ) . '">' . esc_attr( $name->label ) . '</option>';
								} ?>
							</select>
						</li>
						<li>
							<label for="categories">Category</label>
							<?php wp_dropdown_categories( array(
								'hide_empty'	  => false,
								'id'		 	  => 'categories',
								'orderby'	 	  => 'title',
								'show_option_all' => 'All Categories',
								'hierarchical'	  => true,
							) ); ?>
						</li>
						<li>
							<label for="tags">Tags</label>
							<select name="tag" id="tags">
								<option value="all">All Tags</option>
								<?php foreach( $tags as $tag ) : ?>
									<option value="<?php echo esc_attr( $tag->slug ); ?>"><?php echo esc_html( $tag->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<li>
							<label for="location">Location</label>
							<select name="location" id="location">
								<option value="all">All Locations</option>
								<?php foreach ( $locations as $location ) : ?>
									<option value="<?php echo absint( $location->ID ); ?>"><?php echo esc_html( $location->post_title ); ?></option>
								<?php endforeach; ?>
							</select>
						</li>
						<li>
							<label for="makers">Makers</label>
							<input type="checkbox" name="makers" id="makers" value="true" /> Export Maker Based Report
						</li>
						<li style="margin:20px 0;">
							<input type="submit" class="button button-primary button-large" value="Process Report" />
						</li>
					</ul>
				</form>

				<h2 style="margin-top:40px;">Editorial Reports</h2>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&maker_csv=presenter', 'mf_export_check' ); ?>">Export Editorial Comments</a></h3>

				<h2 style="margin-top:40px;">Exhibit Reports</h2>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&exhibit_signage_csv', 'mf_export_check' ); ?>">Export Exhibit Signage</a></h3>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&exhibit_signage_csv&reprint', 'mf_export_check' ); ?>">Export Reprints</a></h3>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&exhibit_signage_clear_reprint', 'mf_export_check' ); ?>">Clear Reprints</a></h3>

				<h2 style="margin-top:40px;">Presentation Reports</h2>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&presentation_csv=manager', 'mf_export_check' ); ?>">Stage Manager Report</a></h3>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&presentation_csv=signage', 'mf_export_check' ); ?>">Stage Signage Report</a></h3>
				<h3><a href="<?php echo wp_nonce_url( 'edit.php?post_type=mf_form&page=mf_reports&presentation_csv=checkin', 'mf_export_check' ); ?>">Presenter CheckIn Report</a></h3>
			</div>
		</div>
	<?php }


	/*
	* Gathers and outputs application data
	*
	* @access private
	* @return array Associative array of all reports stats
	* =====================================================================*/
	private function get_reports_stats() {

		$posts  = $this->get_all_forms();
		$types  = array(
			'exhibit'   => 0,
			'presenter' => 0,
			'performer' => 0
		);

		$res    = array(
			'in-progress'      => $types,
			'proposed'         => $types,
			'more-info'		   => $types,
			'accepted'         => $types,
			'rejected'         => $types,
			'cancelled'        => $types,
			'wait-list'		   => $types,
		);

		foreach( $posts as $post ) {
			$form = (array) json_decode( str_replace( "\'", "'", $post->post_content ) );

			if ( array_key_exists( $form['form_type'], $types ) )
				$res[$post->post_status][$form['form_type']]++;
		}

		return $res;
	}


	/**
	 * Gathers and outputs application data.
	 *
	 * @param  array   $options      The array of filters we want to pass to WP_Query like application type, app status, and other fun stuff.
	 * @param  boolean $return_array Returns an array instead of the CSV
	 * @return void
	 */
	private function build_form_export( $options = array(), $return_array = false ) {

		// Check our nonce
		if ( isset( $_GET['export_nonce'] ) && ! wp_verify_nonce( $_GET['export_nonce'], 'mf_export_check' ) )
			return false;

		// Make sure the user requesting this has the privileges...
		if ( ! current_user_can( 'edit_others_posts' ) )
			return false;

		// Set to true
		$return_array = false;

		// Setup some default options
		$defaults = array(
			'sort'		  	=> null,
			'header_titles' => array(),
			'filters'		=> array(
				'type' 	  	  => 'all',
				'post_status' => 'all',
			),
		);

		// Parse our options with the defaults
		$options = wp_parse_args( $options, $defaults );

		// Merge any custom headers with our existing ones for custom columns.
		$fields = array_merge( $this->fields, $options['header_titles'] );

		$data  = array(
			'status'              => '',
			'form_type'           => '',
			'project_id'          => '',
			'project_name'        => '',
			'public_description'  => '',
			'private_description' => '',
			'cats'                => '',
			'tags'                => '',
			'form_photo'          => '',
			'uid'                 => '',
			'user_photo'          => '',
			'user_bio'            => ''
		);

		// Get our array of form fields so we can build the header of the csv export
		if ( $options['filters']['type'] == 'all' ) {
			$header_titles = array(
				's1' => array(),
			);

			foreach ( $fields as $type => $step_number ) {
				foreach ( $step_number as $key => $value ) {
					$header_titles['s1'] = array_merge( $header_titles['s1'], $value );
				}
			}
		} else {
			$header_titles = $this->fields[ $options['filters']['type'] ];
		}

		// Take the current header titles and merge them all into the $data variable.
		foreach ( $header_titles as $step_number => $value ) {
			foreach ( $value as $key => $value ) {
				$data[ $key ] = '';

				if ( $key == 'm_maker_gigyaid' ) {
					for ( $i = 2; $i < 5; $i++ ) {
						$data['m_maker_name_' . $i ] 	= '';
						$data['m_maker_email_' . $i ] 	= '';
						$data['m_maker_gigyaid_' . $i ] = '';
						$data['m_maker_photo_' . $i ]   = '';
						$data['m_maker_twitter_' . $i ] = '';
					}
				} elseif ( $key == 'presenter_gigyaid' ) {
					for ( $i = 2; $i < 5; $i++ ) {
						$data['presenter_name_' . $i ] 	  = '';
						$data['presenter_email_' . $i ]   = '';
						$data['presenter_gigyaid_' . $i ] = '';
						$data['presenter_photo_' . $i ]   = '';
						$data['presenter_twitter_' . $i ] = '';
					}
				}
			}
		}

		// Get our data setup
		$applications = $this->get_all_forms( $options['sort'], $options['filters']['post_status'], $options['filters'] );
		$ef_terms 	  = $this->get_editflow_terms();
		$ef_data 	  = $this->get_editflow_data( $ef_terms );
		$ef_def   	  = array(
			'checkbox' => 'No',
			'data'     => 'N/A',
		);

		// Set the array keys for the header
		$data = array_keys( $data );

		if ( 'accepted' == $options['filters']['post_status'] ) {
			array_splice( $data, 1, 0, 'accepted' );
		}

		// Begin setting up the code for the header
		$header = implode( "\t", $data );

		// Process our Edit Flow terms also into the header
		foreach ( $ef_terms as $ef_term ) {
			$header .= "\t" . $ef_term['name'];
		}

		// Add some more to the list..
		$header .= "\tLocation";
		$header .= "\tList of Makers";
		$header .= "\tTemp Location";
		$header .= "\tLocations";
		$header .= "\tList of Makers";

		// We'll also append any custom header titles
		if ( ! empty( $options['header_titles'] ) ) {
	 		foreach ( $options['header_titles'] as $header_title ) {
				$header .= "\t" . $header_title;
			}
		}

		// Any instance that has under scores, replace it with spaces and make the text uppercase
		$header = strtoupper( str_replace( '_', ' ', $header ) );

		// Start a new row which will be our content :)
		$header .= "\r\n";

		// Setup the results array
		$results = array();

		// Define the body variable that will contain the output
		$body = '';

		// Process our rows.
		foreach ( $applications as $app ) {
			// Get the current applications content (which is json data)
			$form = (array) json_decode( str_replace( '\\', '\\\\', $app->post_content ) );

			// Let's clean up the content of any special characters and other ugly things
			foreach ( $form as $key => $value ) {
				if ( is_array( $value ) ) {
					$key_value = array();
					foreach ( $value as $k => $v ) {
						$key_value[] = mf_clean_content( $v, true );
					}
				} else {
					$key_value = mf_clean_content( $value, true );
				}
				$form[ sanitize_key( $key ) ] = $key_value;
			}

			// Let's make sure we're actually getting what we expect..
			if ( $options['filters']['type'] != 'all' && $options['filters']['type'] != $form['form_type'] )
				continue;

			// Append some extra details to our data
			$form = array_merge( $form, array(
				'status'     => $app->post_status,
				'project_id' => $app->ID
			) );

			// Create an array key for this post in the results array
			$results[ $app->ID ] = array();

			// Create an empty string to append each CSV row to.
			$row = '';

			// Add our maker info
			$multi = array(
				'm_maker_name',
				'm_maker_email',
				'm_maker_gigyaid',
				'm_maker_photo',
				'm_maker_twitter',
				'presenter_name',
				'presenter_email',
				'presenter_gigyaid',
				'presenter_photo',
				'presenter_twitter',
			);

			// define our CSV rows
			foreach ( $data as $key ) {

				// For each applications type, we have different field keys. We'll pass it through this function to ensure we return the right type
				if ( $merged_key = $this->merge_fields( $key, $form['form_type'] ) )
					$key = $merged_key;

				// Separate each maker/presenter to a new row
				if ( in_array( $key, $multi ) ) {

					// If the current data is an array, then we'll point it to their 0 index. This will be the makers name, email and gigya ID's
					$maker_data = is_array( $form[ $key ] ) ? $form[ $key ][0] : $form[ $key ];

					// Add our maker data to the results array
					$results[ $app->ID][ $key ] = $maker_data;

					// Add our maker data to the row TODO: don't add if $maker_data is empty?? What happens to the results?
					$row .= "\t" . $maker_data;

					// Process a makers info
					if ( $key == 'm_maker_gigyaid' ) {
						for ( $i = 1; $i < 4; $i++ ) {
							foreach ( array( 'm_maker_name', 'm_maker_email', 'm_maker_gigyaid', 'm_maker_photo', 'm_maker_twitter' ) as $n ) {
								if ( $n != 'm_maker_twitter' ) {
									$results[ $app->ID ][ $n . '_' . ( $i + 1 ) ] = $form[ $n ][ $i ];
									$row .= "\t" . $form[ $n ][ $i ];
								} else {
									$results[ $app->ID ][ $n . '_' . ( $i + 1 ) ] = $form[ $n ];
									$row .= "\t" . $form[ $n ];
								}
							}
						}
					} elseif( $key == 'presenter_gigyaid' ) {
						for ( $i = 1; $i < 4; $i++ ) {
							foreach ( array( 'presenter_name', 'presenter_email', 'presenter_gigyaid', 'presenter_photo', 'presenter_twitter' ) as $n ) {
								if ( $n == 'presenter_name' || $n == 'presenter_email' || $n == 'presenter_twitter' ) {
									$output = ( ! empty( $form[ $n ][ $i ] ) ? $form[ $n ][ $i ] : '' );
									$results[ $app->ID ][ $n . '_' . ( $i + 1 ) ] = $output;
									$row .= "\t" . $output;
								} else {
									$output = ( ! empty( $form[ $n ] ) ? $form[ $n ] : '' );
									$results[ $app->ID ][ $n . '_' . ( $i + 1 ) ] = $output;
									$row .= "\t" . $output;
								}

							}
						}
					}
				// Catch all
				} elseif ( $key == 'accepted' ) {

					// Return the logged data
					$meta_log = get_post_meta( $app->ID, '_mf_log', true );

					// If data was returned
					if ( $meta_log ) {
						foreach ( $meta_log as $entry ) {
							if ( strpos( $entry, 'Accepted' ) !== false ) {
								$entry_a = explode( ' ', $entry );
								$row .= "\t" . $entry_a[0] . ' ' . $entry_a[1] . ' ' . strtoupper( $entry_a[2] );
								break;
							}
						}
					} else {
						$row .= "\tN/A";
					}

				} elseif ( isset( $form[ $key ] ) ) {
					$d = is_array( $form[ $key ] ) ? implode( ', ', $form[ $key ] ) : $form[ $key ];

					$results[ $app->ID ][ $key ] = $d;
					$row .= "\t" . $d;

				} else {

					// Check if we are dealing with a multi-listing maker of presenter.
					$is_multi = false;
					foreach ( $multi as $m ) {
						for ( $i = 2; $i < 5; $i++ ) {
							if ( $key == $m . '_' . $i ) {
								$is_multi = true;
								break;
							}
						}
					}

					if ( ! $is_multi ) {
						$results[ $app->ID ][ $key ] = '';
						$row .= "\t" . '';

					}
				}
			}

			// Get all of the Edit Flow terms and process them into our CSV
			foreach( $ef_terms as $ef_id => $ef_term ) {
				$results_ef = ( ! empty( $results[ $app->ID ][ $ef_term['slug'] ] ) ) ? $results[ $app->ID ][ $ef_term['slug'] ] : '';

				if ( isset( $ef_data[ $app->ID ][ $ef_id ] ) ) {

					// Handle any text field
					if ( $ef_term['type'] == 'text' ) {

						$ef_text_results = get_post_meta( $app->ID, '_' . $ef_term['taxonomy'] . '_' . $ef_term['type'] . '_' . $ef_term['slug'], true );
						$results[ $app->ID ][ $ef_term['slug'] ] = $ef_text_results;
						$row .= "\t" . $ef_text_results;

					} else {

						$results[ $app->ID ][ $ef_term['slug'] ] = $ef_def[ $ef_term['type'] ];
						$row .= "\t" . $ef_data[ $app->ID ][ $ef_id ];
					}

				} else {
					$output = ( ! empty( $ef_def[ $ef_term['type'] ] ) ) ? $ef_def[ $ef_term['type'] ] : '';
					$results[ $app->ID ][ $ef_term['slug'] ] = $output;
					$row .= "\t" . $output;

				}
			}

			// Get application locations and add to the CSV
			$locations = mf_get_locations( $app->ID );

			$results[ $app->ID ]['locations'] = ( ! empty( $locations ) ? esc_html( $locations ) : '' );
			$row .= "\t" . ( ! empty( $locations ) ? esc_html( $locations ) : '' );


			// Set List of Makers
			$makers = ( ! empty( $form['m_maker_name'] ) ) ? $form['m_maker_name'] : '';

			// $makers can return an array, a single string or empty. Handle them as needed.
			if ( empty( $makers ) ) {
				$results[ $app->ID ]['list_of_makers'] = '';
				$row .= "\t";
			} else {
				$ls = '';
				// Process each maker into a string.
				foreach( $makers as $maker ) {
					$ls .= ', ' . htmlspecialchars_decode( $maker );
				}

				$results[ $app->ID ]['list_of_makers'] = substr( $ls, 2 );
				$row .= "\t" . substr( $ls, 2 );
			}

			// Take the $row variable we have been feeding each of our applications into in a variable called $body
			$body .= substr( $row, 1) . "\r\n";
		}

		// Check if we are using this function to just return an array of our results.
		if ( $return_array )
			return $results;

		// Get the time this export was ran. This is used in the file name of the CSV
		$time_offset = time() - ( 3600 * 7 );

		// Now that we have everything, return the data.
		$this->output_csv( strtoupper( $options['filters']['type'] ) . '_APPLICATIONS_' . strtoupper( $options['filters']['faire'] ) . '_' . date( 'M-d-Y', $time_offset ), $header . $body );
	}


	/*
	* Associate all posts with EditFlow Data
	*
	* @access private
	* @param array $efterms An array of all EditFlow Terms/Types
	* @return array An Array of all posts associated with EditFlow Data
	* =====================================================================*/
	private function get_editflow_data( $efterms ) {

		global $wpdb;

		$term_list = '';
		foreach( $efterms as $meta_key => $term )
			$term_list .= ",'".sanitize_key( $meta_key )."'";

		$res = array();
		$pm  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE meta_key IN (".substr( $term_list, 1 ).") LIMIT 0,1999") );

		foreach( $pm as $data ) {
			if ( !isset( $res[ $data->post_id ] ) )
				$res[ $data->post_id ] = array();

			$value = $data->meta_value;

			switch( $efterms[ $data->meta_key ]['type'] ) {
				case 'checkbox' :
					$value = $value == 1 ? 'Yes' : 'No';
				break;
				case 'date' :
					$value = $value != 0 ? date('M d Y', $value) : 'N/A';
				break;
			}

			$res[ $data->post_id ][ $data->meta_key ] = $value;
		}

		return $res;
	}
	/*
	* Gets all EDITFLOW Terms and Types
	*
	* @access private
	* @return array An Array of all edit flow terms/types
	* =====================================================================*/
	private function get_editflow_terms() {

		$efterms = array();
		$terms   = get_terms( 'ef_editorial_meta', array( 'hide_empty' => false ) );

		foreach( $terms as $term ) {
			$properties = unserialize( base64_decode( $term->description ) );
			$efterms[ '_'.$term->taxonomy.'_'.$properties['type'].'_'.$term->slug ] = array_merge( (array) $term, $properties );
		}

		return $efterms;
	}


	/**
	 * Exports all Makers in an awesome, fancy, CSV file that you can use to organize Maker Faire. #magic. (Unicorns not included).
	 * @param  array  $options The array of filters we want to pass to WP_Query like application type, app status, and other fun stuff.
	 * @return void
	 */
	private function build_maker_export( $options = array() ) {

		// Check our nonce
		if ( isset( $_GET['export_nonce'] ) && ! wp_verify_nonce( $_GET['export_nonce'], 'mf_export_check' ) )
			return false;

		// Make sure the user requesting this has the privileges...
		if ( ! current_user_can( 'edit_others_posts' ) )
			return false;

		// Also ensure that we are passing the variable to trigger our maker export
		if ( ! isset( $_GET['makers'] ) && ! $_GET['makers'] )
			return false;

		// Setup some default options
		$defaults = array(
			'sort'		  	=> null,
			'header_titles' => array(),
			'filters'		=> array(
				'type' 	  	  => 'all',
				'post_status' => 'all',
			),
		);

		// Parse our options with the defaults
		$options = wp_parse_args( $options, $defaults );

		// Setup the headers we want
		$headers = array(
			'First Name',
			'Last Name',
			'Bio',
			'Photo',
			'Email',
			'Status',
			'Type',
			'ID',
			'Project',
			'Short Description',
			'Project Website',
			'Project Video',
			'Categories',
			'Tags',
			'Group Name',
			'Group Bio',
			'Group Phone',
			'Group Website',
			'Organization',
			'Job Title',
			'Phone 1',
			'Phone 1 Type',
			'Phone 2',
			'Phone 2 Type',
			'Onsite Phone',
			'Contact First Name',
			'Contact Last Name',
			'Contact Email',
			'City',
			'State',
			'Zip',
			'Country'
		);

		// Process our headers
		foreach ( $headers as $header ) {
			$header_titles .= "{$header}\t";
		}

		$header_titles .= "\r\n";


		// Get our applications
		$posts   = $this->get_all_forms( $options['sort'], $options['filters']['post_status'], $options['filters'] );
		$results = array();
		$body    = '';

		foreach ( $posts as $post ) {
			$form = (array) json_decode( str_replace( "\'", "'", $post->post_content ) );

			// Break up the maker name by first and last name
			$firstname = substr( $form['name'], 0, strpos( $form['name'], ' ' ) ) . "\t";
			$lastname  = substr( $form['name'], strpos( $form['name'], ' ' ) + 1 ) . "\t";

			// Maker's info
			$row  = $firstname . $lastname; // First and Last name columns
			$row .= ( ! is_array( $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] ) ? $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] : $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ][0] ) . "\t"; // Maker Bio
			$row .= $form[ $this->merge_fields( 'form_photo', $form['form_type'] ) ] . "\t"; // Maker Photo
			$row .= $form['email'] . "\t"; // Maker Email
			$row .= strtoupper( $post->post_status ) . "\t"; // Application Status
			$row .= strtoupper( $form['form_type'] ) . "\t"; // Application Type
			$row .= strtoupper( $post->ID ) . "\t"; // Application ID
			$row .= $form[ $this->merge_fields( 'project_name', $form['form_type'] ) ] . "\t"; // Application Name
			$row .= ( $form['form_type'] != 'presenter' ) ? $form['public_description'] . "\t" : $form['short_description'] . "\t"; // Application Short Description
			$row .= $form[ $this->merge_fields( 'project_website', $form['form_type'] ) ] . "\t"; // Maker Website
			$row .= $form[ $this->merge_fields( 'project_video', $form['form_type'] ) ] . "\t"; // Maker Video
			$row .= ( isset( $form['categories'] ) && is_array( $form['categories'] ) ? implode( ', ', $form['categories'] ) : '' ) . "\t"; // Application Categories
			$row .= ( isset( $form['tags'] ) 	   && is_array( $form['tags'] ) 	  ? implode( ', ', $form['tags'] ) 		 : '' ) . "\t"; // Application Tags

			// Group Data
			$row .= ( isset( $form['group_name'] ) 		   ? $form['group_name'] 	   			   	   : '' ) . "\t"; // Group Name
			$row .= ( isset( $form['group_bio'] ) 		   ? $form['group_bio'] 	   			   	   : '' ) . "\t"; // Group Bio
			$row .= ( isset( $form['group_photo'] ) 	   ? $form['group_photo'] 	   			   	   : '' ) . "\t"; // Group Photo
			$row .= ( isset( $form['group_website'] ) 	   ? $form['group_website']   				   : '' ) . "\t"; // Group Website
			$row .= ( is_array( $form['presenter_org'] )   ? implode( ', ', $form['presenter_org'] )   : $form['presenter_org'] ) . "\t"; // Organization
			$row .= ( is_array( $form['presenter_title'] ) ? implode( ', ', $form['presenter_title'] ) : $form['presenter_title'] ) . "\t"; // Job Title

			// Contact Data
			$row .= $form['phone1'] . "\t"; // Contact Phone 1
			$row .= $form['phone1_type'] . "\t"; // Contact Phone 1 Type
			$row .= $form['phone2'] . "\t"; // Contact Phone 2
			$row .= $form['phone2_type'] . "\t"; // Contact Phone 2 Type
			$row .= ( isset( $form['onsite_phone'] ) ? $form['onsite_phone'] : '' ) . "\t"; // Contacts Onsite Phone
			$row .= $firstname . $lastname; // Contact First and Last Name
			$row .= $form['email'] . "\t"; // Contact Email
			$row .= $form['private_city'] . "\t"; // Contact City
			$row .= $form['private_state'] . "\t"; // Contact Stats
			$row .= $form['private_zip'] . "\t"; // Contact Zip
			$row .= $form['private_country']; // Contact Country

			// Contain our entire row into the $body variable
			$body .= $row . "\r\n";

			// We need a way to handle and process applications with multiple makers. Let's do that okay?
			foreach ( array( 'exhibit' => 'm_maker_', 'presenter' => 'presenter_' ) as $type => $prefix ) {

				// Check if the form field contains more than one maker name and email.
				if ( $form['form_type'] == $type && is_array( $form[ $prefix . 'name' ] ) && is_array( $form[ $prefix . 'email' ] ) ) {

					// Loop through each maker and count them
					for ( $i = 1; $i < count( $form[ $prefix . 'name' ] ); $i++ ) {

						// Process their first and last name.
						$add_firstname = substr( $form[ $prefix . 'name' ][ $i ], 0, strpos( $form[ $prefix . 'name' ] [ $i ], ' ' ) ) . "\t";
						$add_lastname  = substr( $form[ $prefix . 'name'][ $i ], strpos( $form[ $prefix . 'name'][ $i ], ' ' ) ) . "\t";

						// Lets add their credentials to a new row
						$add_row = str_replace( $firstname . $lastname, $add_firstname . $add_lastname, $row );
						$add_row = str_replace( $form[ $prefix . 'email'][ $i ], $form['email'], $add_row );
					}
				}
			}
		}

		// Get the time this export was ran. This is used in the file name of the CSV
		$time_offset = time() - ( 3600 * 7 );

		// Process the list makers CSV
		$this->output_csv( 'ALLMAKERS_' . strtoupper( $options['filters']['faire'] ) . '_' . date( 'M-d-Y', $time_offset ), $header_titles . $body );
	}


	/*
	* Gathers and builds the output for EDITORIAL COMMENTS
	*
	* @access private
	* =====================================================================*/
	private function build_comments_export( $options ) {

		// Check our nonce
		if ( isset( $_GET['export_nonce'] ) && ! wp_verify_nonce( $_GET['export_nonce'], 'mf_export_check' ) )
			return false;

		// Make sure the user requesting this has the privileges...
		if ( ! current_user_can( 'edit_others_posts' ) )
			return false;

		global $wpdb;

		$sql = "SELECT * FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE comment_type = 'editorial-comment' AND post_type = 'mf_form' ORDER BY comment_date_gmt DESC LIMIT 1999";

		$comments        = $wpdb->get_results( $sql );
		$output          = "Project ID\tProject Name\tProject Status\tUsers & Groups Flagged\tDate Timestamp\tUser Name\tComment Text\r\n";
		$following_users = array();

		foreach( $comments as $comment ) {

			// Check that the post has our latest faire term.
			if ( ! has_term( $options['filters']['faire'], 'faire', $comment->ID ) )
				continue;

			if ( isset( $following_users[ $comment->ID ] ) ) {
				$users = $following_users[ $comment->ID ];
			} else {
				$users = get_the_terms( $comment->ID, 'following_users' );
				$following_users[ $comment->ID ] = $users;
			}

			$user_list = '';
			foreach ( $users as $user ) {
				$user_name = get_user_by( 'login', $user->name );
				$user_list .= ", " . $user_name->display_name;
			}

			$txt = strip_tags( str_replace( '"', "\'", iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $comment->comment_content ) ) );

			$row  = absint( $comment->ID ) . "\t";
			$row .= $comment->post_title . "\t";
			$row .= $comment->post_status . "\t";
			$row .= substr( $user_list, 2 ) . "\t";
			$row .= $comment->comment_date . "\t";
			$row .= $comment->comment_author . "\t";
			$row .= '"' . $txt . "\"\t";

			$output .= $row . "\r\n";
		}

		// Get the time this export was ran. This is used in the file name of the CSV
		$time_offset = time() - ( 3600 * 7 );

		$this->output_csv( 'EDITORIAL_COMMENTS_' . strtoupper( $options['filters']['faire'] ) . '_' . date( 'M-d-Y', $time_offset ), $output );
	}


	/**
	 * Exports all accepted exhibits so we can create printed signs for each
	 * @return void
	 */
	private function build_exhibit_signage_export() {

		// Check our nonce
		if ( isset( $_GET['export_nonce'] ) && ! wp_verify_nonce( $_GET['export_nonce'], 'mf_export_check' ) )
			return false;

		// Make sure the user requesting this has the privileges...
		if ( ! current_user_can( 'edit_others_posts' ) )
			return false;

  /**
		$options = array(
			'filters' => array(
				'type' => 'exhibit',
				'post_status' => 'accepted',
			),
		);
**/
    
    $options = array(
			'filters' => array(
      'post_status' => 'accepted',
        'tax_query' => array(
          array( 
            'taxonomy' => 'type',
            'field' => 'slug',
            'terms' => 'exhibit',
          ),
          array(
            'taxonomy' => 'faire',
            'field' => 'slug',
            'terms' => MF_CURRENT_FAIRE
          )
        ),
        'meta_query' => array(),
      ),
    );

		// Setup the headers we want.
		$headers = array(
			'ID',
			'Exhibit',
			'Photo',
			'@Photo_Slug',
			'Description',
			'Name',
			'Bio',
		);

		// Process our headers
		$header_titles = '';
		foreach ( $headers as $header ) {
			$header_titles .= "{$header}\t";
		}

		$header_titles .= "\r\n";

		// As we get closer to the faire we need to reprint signs for some applications. Let's customize the application retrival to do just that.
		if ( isset( $_GET['reprint'] ) ) {
			$options['filters']['meta_query'] = array(
				'key' => '_ef_editorial_meta_checkbox_reprint-sign',
				'value' => '1'
			);
		}

		// Get our applications
		$exhibits = $this->get_all_forms( null, $options['filters']['post_status'], $options['filters'], MF_CURRENT_FAIRE );
		$results  = array();
		$body     = '';

		foreach ( $exhibits as $exhibit ) {
			$form = (array) json_decode( str_replace( '\\', '\\\\', $exhibit->post_content ) );

			// Loop through all the content and clean it up
			foreach ( $form as $key => $value ) {
				if ( is_array( $value ) ) {
					$i = 0;
					foreach ( $value as $v ) {
						$form[ sanitize_key( $key ) ][ intval( $i ) ] = mf_clean_content( $v, true );
						$i++;
					}
				} else {
					$form[ sanitize_key( $key ) ] = mf_clean_content( $value, true );
				}
			}

			// Process our name field. We'll want to handle exhibit maker types differently.
			if ((array_key_exists('form_type', $form)) && ($form['form_type'] == 'exhibit' )) {
				switch ( $form['maker'] ) {
					case 'One maker':
						$maker_name = ( ! empty( $form['maker_name'] ) ? $form['maker_name'] : '' ) ."\t";
						$maker_bio  = ( ! empty( $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] ) ? $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] : '' ) . "\t";
						break;

					case 'A list of makers':
						$count = 1;
						if ( is_array( $form['m_maker_name'] ) ) {
							$maker_name = '';
							foreach( $form['m_maker_name'] as $m_name ) {
								$separator = ( $count == 1 ) ? '' : ', ';
								$maker_name .= ( ! empty( $m_name ) ? $separator . $m_name : $separator );
								$count++;
							}
							$maker_name .= "\t";
							$maker_bio = '';
						} else {
							$maker_name = ( ! empty( $form['m_maker_name'] ) ? $form['m_maker_name'] : '' ) . "\t";
							$maker_bio  = ( ! empty( $form['m_maker_bio'] ) ? $form['m_maker_bio'] : '' ) . "\t";
						}
						break;

					case 'A group or association':
						$maker_name = ( ! empty( $form['group_name'] ) ? $form['group_name'] : '' ) . "\t";
						$maker_bio  = ( ! empty( $form['group_bio'] ) ? $form['group_bio'] : '' ) . "\t";
						break;
				}
			} else {
				$maker_name = ( ! empty( $form['name'] ) ? $form['name'] : '' ) . "\t";
				$maker_bio  = ( ! is_array( $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] ) ? $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ] : $form[ $this->merge_fields( 'user_bio', $form['form_type'] ) ][0] ) . "\t";
			}

			$photo = $form[ $this->merge_fields( 'form_photo', $form['form_type'] ) ];

			$row  = $exhibit->ID . "\t";
			$row .= $form[ $this->merge_fields( 'project_name', $form['form_type'] ) ] . "\t";
			$row .= $photo . "\t";
			$row .= basename( $photo ) . "\t";
			$row .= ( $form['form_type'] != 'presenter' ) ? $form['public_description'] . "\t" : $form['short_description'] . "\t";
			$row .= $maker_name;
			$row .= $maker_bio;

			// Contain our entire row into the $body variable
			$body .= $row . "\r\n";
		}

		// Get the time this export was ran. This is used in the file name of the CSV
		$time_offset = time() - ( 3600 * 7 );

		// Process the list makers CSV
		$this->output_csv( 'EXHIBIT_SIGNAGE_' . date( 'M-d-Y', $time_offset ), $header_titles . $body );
	}


	/**
	 * THis little doo-dad will reset the "Reprint" option set in the Edit Flow Editorial Metadad
	 * @return void
	 *
	 * @L-Ron
	 */
	private function reset_signage_reprints() {

		// Check our nonce
		if ( isset( $_GET['export_nonce'] ) && ! wp_verify_nonce( $_GET['export_nonce'], 'mf_export_check' ) )
			return false;

		// Make sure the user requesting this has the privileges...
		if ( ! current_user_can( 'edit_others_posts' ) )
			return false;

		$options = array(
			'filters' => array(
				'type' => 'exhibit',
				'post_status' => 'all',
				'meta_query' => array(
					'key' => '_ef_editorial_meta_checkbox_reprint-sign',
					'value' => '1'
				),
			),
		);
		$apps = $this->get_all_forms( null, 'all', $options['filters'], MF_CURRENT_FAIRE );

		if ( empty( $apps ) || ! is_array( $apps ) )
			return false;

		// Loops through applications returned with the reprint option enabled and reset them
		foreach ( $apps as $app ) {
			update_post_meta( absint( $app->ID ), '_ef_editorial_meta_checkbox_reprint-sign', 0 );
		}
	}


	/*
	* Gathers and builds the output for PRESENTER EXPORTS
	*
	* @access private
	* =====================================================================*/
	private function build_presentation_exports( $type = 'manager' ) {

		//NONCE CHECK
		if ( isset( $_GET['_wpnonce'] ) && !wp_verify_nonce( $_GET['_wpnonce'], 'mf_export_check' ) )
			return false;
		//CAP CHECK
		if ( !current_user_can( 'edit_others_posts' ) )
			return false;

		//EXPORT TYPES
		if( $type == 'manager' ) {
			$output = "Start Time\tEnd Time\tDate\tLocation\tProject ID\tProject Name\tType\tFirst Name\tLast Name\tEmail\tPhone\tTwitter\tSpecial Requests\r\n";
			$title  = 'MANAGER_REPORT_';
		} elseif( $type == 'signage' ) {
			$output = "Location\tStart Time\tEnd Time\tDay\tProject Title\tPresenter Name(s)\r\n";
			$title  = 'STAGE_SIGNAGE_';
		} elseif( $type == 'checkin' ) {
			$output = "Presenter ID\tPresenter Last name\tPresenter First name\tProject Title\tLocation\tDate\tStart Time\tEnd Time\r\n";
			$title  = 'PRESENTER_CHECKIN_';
		} else {
			return false;
		}

		//GET PRESENTER FORMS
		$args = array(
			'posts_per_page' => 1999,
			'post_type' 	 => 'mf_form',
			'faire' 		 => MF_CURRENT_FAIRE,
			'type'			 => 'presenter'
		);

		$ps    = new WP_Query($args);
		$forms = array();

		foreach( $ps->posts as $post ) {
			$forms[ $post->ID ] = $post;
		}

		//GET EVENT ITEMS
		$mfeis = get_posts( array( 'post_type' => 'event-items', 'numberposts' => 1999 ) );

		//BUILD THE 3 FORMS
		foreach( $mfeis as $mfei ) {

			$data = get_post_custom( $mfei->ID );

			if( !isset( $forms[ $data['mfei_record'][0] ] ) )
				continue;

			$form  = (array) json_decode( str_replace( "\'", "'", $forms[ $data['mfei_record'][0] ]->post_content ) );

			$loc = mf_get_locations( $mfei->ID );

			$fname = substr( $form['name'], 0, strpos( $form['name'], ' ' ) );
			$lname = substr( $form['name'], strpos( $form['name'], ' ' ) + 1 );

			if( $type == 'manager' ) {

				if( !is_array( $form['presenter_name'] ) )
					$form['presenter_name'] = array( $form['presenter_name'] );

				$line = '';

				foreach( $form['presenter_name'] as $name ) {
					$fname = substr( $name, 0, strpos( $name, ' ' ) );
					$lname = substr( $name, strpos( $name, ' ' ) + 1 );

					$line .= $data['mfei_start'][0]."\t";
					$line .= $data['mfei_stop'][0]."\t";
					$line .= $data['mfei_day'][0]."\t";
					$line .= $loc."\t";
					$line .= intval( $data['mfei_record'][0] )."\t";
					$line .= $form['presentation_name'] . "\t";
					$line .= "Presentation\t";
					$line .= $fname."\t";
					$line .= $lname."\t";
					$line .= $form['email']."\t";
					$line .= $form['phone1']."\t";
					$line .= ( isset( $form['presenter_twitter'] ) && is_array( $form['presenter_twitter'] ) ) ? implode(", ", $form['presenter_twitter'] ) : $form['presenter_twitter']."\t";
					$line .= $form['special_requests']."\t\r\n";
				}

				$line = substr( $line, 0, -4 );

			} elseif( $type == 'signage' ) {
				$line  = $loc."\t";
				$line .= $data['mfei_start'][0]."\t";
				$line .= $data['mfei_stop'][0]."\t";
				$line .= $data['mfei_day'][0]."\t";
				$line .= $form['presentation_name']."\t";
				$line .= ( is_array( $form['presenter_name'] ) ? implode( ', ', $form['presenter_name'] ) . "\t" : $form['presenter_name'] )."\t";

			} elseif( $type == 'checkin' ) {

				if( !is_array( $form['presenter_name'] ) )
					$form['presenter_name'] = array( $form['presenter_name'] );

				$line = '';

				foreach( $form['presenter_name'] as $name ) {
					$fname = substr( $name, 0, strpos( $name, ' ' ) );
					$lname = substr( $name, strpos( $name, ' ' ) + 1 );

					$line .= intval( $data['mfei_record'][0] ) . "\t";
					$line .= $fname."\t";
					$line .= $lname."\t";
					$line .= $form['presentation_name']."\t";
					$line .= $loc."\t";
					$line .= $data['mfei_day'][0]."\t";
					$line .= $data['mfei_start'][0]."\t";
					$line .= $data['mfei_stop'][0]."\r\n";
				}

				$line = substr( $line, 0, -4 );

			} else {
				return false;
			}

			$output .= $line."\r\n";
		}

		$time_offset = time() - ( 3600 * 7 );
		$this->output_csv( $title.date('M-d-Y', $time_offset).'_'.date('G-i', $time_offset), $output );

	}


	/*
	* Combines certain attributes into single for output
	*
	* @access private
	* @param string $key The key to look for
	* @param boolean|string $reverse The form type to reverse look up or false
	* =====================================================================*/
	public function merge_fields( $key, $reverse = false ) {
		$conv = array(
			'project_name'     => array(
				'exhibit'   => 'project_name',
				'performer' => 'performer_name',
				'presenter' => 'presentation_name'
			),
			'form_photo'       => array(
				'exhibit'   => 'project_photo',
				'performer' => 'performer_photo',
				'presenter' => 'presentation_photo'
			),
			'form_photo_thumb' => array(
				'exhibit'   => 'project_photo_thumb',
				'performer' => 'performer_photo_thumb',
				'presenter' => 'presentation_photo_thumb'
			),
			'project_website'  => array(
				'exhibit'   => 'project_website',
				'performer' => 'performer_website',
				'presenter' => 'presentation_website'
			),
			'project_video'    => array(
				'exhibit'   => 'project_video',
				'performer' => 'performer_video',
				'presenter' => 'video'
			),
			'user_photo'       => array(
				'exhibit'   => 'maker_photo',
				'performer' => 'performer_photo',
				'presenter' => 'presenter_photo'
			),
			'user_photo_thumb' => array(
				'exhibit'   => 'maker_photo_thumb',
				'performer' => 'performer_photo_thumb',
				'presenter' => 'presenter_photo_thumb'
			),
			'user_bio'         => array(
				'exhibit'   => 'maker_bio',
				'performer' => 'private_description',
				'presenter' => 'presenter_bio'
			),
			'user_gigya'	   => array(
				'exhibit'	=> 'm_maker_gigyaid',
				'performer' => 'uid',
				'presenter' => 'presenter_gigyaid'
			),
			'project_description' => array(
				'exhibit' => 'public_description',
				'performer' => 'public_description',
				'presenter' => 'short_description'
			),
		);

		if ( $reverse && isset( $conv[ $key ][ $reverse ] ) )
			return $conv[ $key ][ $reverse ];

		if ( $reverse && ! isset( $conv[ $key ][ $reverse ] ) )
			return false;

		foreach ( $conv as $conv_key => $conv_a ) {
			if ( ! $reverse && in_array( $key, $conv_a ) )
				return $conv_key;
		}

		return false;
	}
	/*
	* Sets CSV headers and outputs all data
	*
	* @access private
	* @param string $name The name of the ouput CSV
	* @param string $data The data to be in the CSV
	* =====================================================================*/
	private function output_csv( $name, $data ) {
		header( 'Content-Type: application/ms-excel' );
		header( 'Content-Disposition: attachment; filename=' . str_replace( ' ', '-', $name ) . '.xls' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $data;
		exit();
	}
	/*
	* Get all posts with type mf_form
	*
	* @access public
	* @param string|null $sort Whether and how the forms should be sorted.
	* @param string $status The status of the application
	* @return array Maker Faire Forms
	* =====================================================================*/
	public function get_all_forms( $sort = NULL, $app_status = 'all', $filters = array(), $faire = '' ) {

		if ( empty( $faire ) )
			$faire = MF_CURRENT_FAIRE;

		$args = array(
			'posts_per_page' => 1999,
			'post_type'      => 'mf_form',
			'faire'			 => ( ! empty( $filters['faire'] ) ) ? sanitize_title_with_dashes( $filters['faire'] ) : sanitize_title_with_dashes( $faire ),
			'post_status'	 => ( $app_status != 'all' ) ? sanitize_text_field( $app_status ) : '',
			'sort'			 => $sort,
			'type'			 => ( isset( $filters['type'] ) && $filters['type'] != 'all' ) ? sanitize_text_field( $filters['type'] ) : '',
			'cat'			 => ( isset( $filters['cat'] ) && $filters['cat'] != 0 ) ? absint( $filters['cat'] ) : '',
			'tag'			 => ( isset( $filters['tag'] ) && $filters['tag'] != 'all' ) ? sanitize_text_field( $filters['tag'] ) : '',
		);

   //sanity check to avoid breaking old code
    if(array_key_exists('tax_query', $filters)) {
      if( isset( $filters['tag'] ) && $filters['tag'] != '' && $filters['tag'] != 'all' ) {
        $filters['tax_query'][] =  array('taxonomy' => 'post_tag', 'field' => 'slug', 
            'terms' => sanitize_text_field( $filters['tag']) );
            unset($filters['tag']);
            unset($args['tag']);
 
      } else {
            unset($filters['tag']);
            unset($args['tag']);
      }

      if( isset( $filters['type'] ) && $filters['type'] != '' && $filters['type'] != 'all' ) {
        $filters['tax_query'][] =  array('taxonomy' => 'type', 'field' => 'slug', 
            'terms' => sanitize_text_field( $filters['type']) );
            unset($filters['type']); 
            unset($args['type']);
      } else {
            unset($filters['type']);
            unset($args['type']);
      }

      if( isset( $filters['faire'] ) && $filters['faire'] != '' && $filters['faire'] != 'all' ) {
        $filters['tax_query'][] =  array('taxonomy' => 'faire', 'field' => 'slug', 
            'terms' => sanitize_text_field( $filters['faire']) );
            unset($filters['faire']); 
      } else {
            unset($filters['faire']);
            unset($args['faire']);
      }

      
    }
		// Modify our query if we are looking for applications by location
		if ( isset( $filters['location'] ) && $filters['location'] != 'all' ) {
			$args['meta_query'][] = array(
				array(
					'key' => 'faire_location',
					'value' => serialize( array( absint( $filters['location'] ) ) ), // Sadly we are saving locations as a serialized array.... soooooo
				),
			);
		}

		// Sometimes we'll need to pull application based on misc meta data, this little area will do just that
		// We'll also want to make sure we aren't passing any locations.
		// @todo Fix this up to allow locations and custom meta queries
		if ( isset( $filters['meta_query'] ) && ! empty( $filters['meta_query'] ) && ! isset( $args['meta_query'] ) ) {

			$count = 0;
			$last = end( $filters['meta_query'] );

			// Let's sanitize our options in the meta_query field
      $tmp_meta_query = array();
      if(!array_key_exists('meta_query', $args)) $args['meta_query'] = array();
        foreach ( $filters['meta_query'] as $meta_query ) {
			    foreach ( $meta_query as $key => $value ) {

				    // Ensure we are passing acceptable paramenters to WP_Query
             if ( in_array($key, array('key','value','compare','type' )) ) {
              $tmp_meta_query[sanitize_key( $key )] =  sanitize_text_field( $value );
             }

				}
        $args['meta_query'][] = $tmp_meta_query;
      }
		}

    if ( isset( $filters['tax_query'] ) && ! empty( $filters['tax_query'] ) && ! isset( $args['tax_query'] ) ) {
			$count = 0;
			$last = end( $filters['tax_query'] );
    	// Let's sanitize our options in the tax_query field
			foreach ( $filters['tax_query'] as $tax_query ) {
        $tmp_tax_query = array();
         foreach($tax_query as $key => $value) {
            if ( in_array($key, array('key','taxonomy', 'field','value','compare','type','operator' )) ) {
                $tmp_tax_query[sanitize_key( $key )] = sanitize_text_field( $value );
            }
            if(($key == 'terms') && (is_array($value))) {
              $tmp_terms = array();
              foreach($value as $term_key => $term_value) {
                $tmp_terms[] = sanitize_text_field( $term_value );
              }
              $tmp_tax_query['terms'] = ($tmp_terms[0] != '') ? $tmp_terms : array();
      
            } elseif(($key == 'terms')) {
              $terms_front_string = explode(',', $value);
              $tmp_terms = array();
              foreach($terms_front_string as $term_key => $term_value) {
                $tmp_terms[] = sanitize_text_field( $term_value );
              }
              $tmp_tax_query['terms'] = ($tmp_terms[0] != '') ? $tmp_terms : array();
            }
          }
          $args['tax_query'][] = $tmp_tax_query;
      if((count($args['tax_query']) > 1) && !(array_key_exists('relation',$args['tax_query'])) ) {
        $args['tax_query']['relation'] = 'AND';
      }


    }
		$ps    = new WP_Query( $args );
		$posts = $ps->get_posts();

		if ( is_null( $sort ) )
			return $posts;

		$res = array();

		foreach ( $posts as $post ) {
			$form = (array) json_decode( str_replace( "\'", "'", $post->post_content ) );

			if ( ! isset( $form[ $sort ] ) ) {
				continue;
			} elseif ( ! isset( $res[ $form[ $sort ] ] ) ) {
				$res[ $form[ $sort ] ] = array();
			}

			foreach ( $form as $key => $data ) {
				if ( $mkey = $this->merge_fields( $key ) )
					$form[ $mkey ] = $data;

				$form[ $key ] = is_array( $data ) ? implode( ',', $data ) : $data;
			}

			$res[ $form[ $sort ] ][ $post->ID ] = array_merge(
				$form, array(
					'status'     => $post->post_status,
					'project_id' => $post->ID
				)
			);
		}

		return $res;
	}


	/*
	* Get all posts that have failed to sync with JDB
	*
	* @access private
	* @return array WP Posts
	* =====================================================================*/
	private function get_failed_syncs() {
		$args = array(
			'posts_per_page' => 1999,
			'post_type'      => 'mf_form',
			'meta_key'       => 'mf_jdb_sync_fail'
		);

		$ps      = new WP_Query( $args );
		$posts   = $ps->get_posts();

		return $posts;
	}
	/*
	* Determines if a field is textarea
	*
	* @access private
	* @param string $key The key to check
	* @return boolean If the key is a textarea
	* =====================================================================*/
	private function is_textarea( $key ) {
		$text_areas = array(
			'public_description',
			'private_description',
			'short_description',
			'long_description',
			'food_details',
			'sales_details',
			'booth_size_details',
			'tables_chairs_details',
			'placement',
			'what_are_you_powering',
			'amps_details',
			'radio_detais',
			'safety_details',
			'maker_bio',
			'large_non_profit',
			'references',
			'anything_else',
			'equipment',
			'additional_info',
			'special_requests',
			'promotion'
		);

		return in_array( (string) $key, $text_areas );
	}
	/*
	* Fix newlines saving improperly to at least display correctly.
	*
	* @access private
	* @param string $str String to search
	* @param string $replace The string to replace the bad characters with.
	* =====================================================================*/
	private function convert_newlines( $str, $replace = '<br />' ) {
		$s = array('nn-', ' nn', '.nn', '<br />rn');
		return str_replace($s, $replace, $str);
	}
	/*
	* Sync all MakerFiare Application Objects with External JDB
	*
	* @access private
	* @param int $id Post id to SYNC
	* =====================================================================*/
	private function sync_jdb( $id = 0 ) {

		// Setup a list of our local servers...
		$local_server = array( 'localhost', 'make.com', 'vip.dev', 'staging.makerfaire.com' );

		// Don't sync from any of our testing locations.
		if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], $local_server ) )
			return false;

		if ( ! $id ) {
			$posts = $this->get_all_forms();
		} else {
			$post  = get_post( $id );

			if ( is_null( $post ) )
				return false;

			if ( $post->post_type != 'mf_form' )
				return false;

			$posts = array( $post );

		}

		foreach ( $posts as $post ) {
			$form = (array) json_decode( str_replace( "\'", "'", $post->post_content ) );
			$res  = wp_remote_post( 'http://db.makerfaire.com/updateExhibitInfo', array( 'body' => array_merge( array( 'eid' => $post->ID, 'mid' => $form['uid'] ), (array) $form ) ) );

			if ( 200 == wp_remote_retrieve_response_code( $res ) ) {
				$body = json_decode( $res['body'] );
				if ( $body->exhibit_id == '' && $body->exhibit_id == 0 ) {
					update_post_meta( $post->ID, 'mf_jdb_sync_fail', time() );
				} else {
					update_post_meta( $post->ID, 'mf_jdb_sync', time() );
					delete_post_meta( $post->ID, 'mf_jdb_sync_fail' );
				}
			} else {
				update_post_meta( $post->ID, 'mf_jdb_sync_fail', time() );
			}
		}

		if ( ! $id )
			update_option( 'mf_full_jdb_sync', date( 'M jS, Y g:s A', ( time() - ( 3600 * 7 ) ) ) );
	}
	/*
	* Sync MakerFiare Application Statuses
	*
	* @access private
	* @param int $id Post id to SYNC
	* @param string $app_status Post status
	* =====================================================================*/
	private function sync_status_jdb( $id = 0, $status = '' ) {

		$res = wp_remote_post( 'http://db.makerfaire.com/updateExhibitStatusForJSON', array( 'body' => array( 'eid' => intval( $id ), 'status' => esc_attr( $status ) ) ) );
		$er  = 0;

		if ( 200 == $res['response']['code'] ) {
			$body = json_decode( $res['body'] );
			if ( 'ERROR' != $body->status ) {
				$er = time();
			}
		}

		update_post_meta( $id, 'mf_jdb_status_sync', $er );

		return $er;
	}
	/*
	* Sync MakerFiare Application Statuses
	*
	* @access private
	* @param int $offset Offset of posts to sync
	* @param int $length Length of sync batch
	* =====================================================================*/
	private function sync_all_status_jdb( $offset = 0, $length = 100 ) {

		$args = array(
			'posts_per_page' => intval( $length ),
			'offset'         => intval( $offset ),
			'post_type'      => 'mf_form',
			'faire'			 => MF_CURRENT_FAIRE,
		);

		$ps      = new WP_Query( $args );
		$posts   = $ps->get_posts();
		$success = 0;

		foreach( $posts as $post ) {
			$r = $this->sync_status_jdb( $post->ID, $post->post_status );

			if( $r )
				$success++;
		}

		return $success;
	}

	/*
	* Updgrade plugin apporpriatly.
	*
	* @access private
	* =====================================================================*/
	private function upgrade() {
		global $wpdb;

		$opt = 'mf_plugin_version';
		$ver = get_option( $opt, 1 );

		//UPDATE POST STATUSES TO MATCH EDIT FLOW STATUSES
		if ( $ver < 2 )
		{
			$wpdb->update(
				$wpdb->posts,
				array( 'post_status' => 'proposed' ),
				array( 'post_status' => 'mf_complete' )
			);

			$wpdb->update(
				$wpdb->posts,
				array( 'post_status' => 'in-progress' ),
				array( 'post_status' => 'mf_pending' )
			);

			update_option( $opt, 2 );
		}
	}


	/**
	 * Creates new makers in our Makers custom post type. Primarily used for our mobile app for the time being.
	 * @param array  $maker      The array of a single maker
	 * @param string $faire_slug The default faire we want to associate this maker to.
	 */
	public function add_to_maker_cpt( $maker, $faire_slug = '' ) {

		if ( empty( $faire_slug ) )
			$faire_slug = MF_CURRENT_FAIRE;

		// Setup a array of messages
		$messages = array(
			'errors'   => array(),
			'success'  => array(),
			'maker_id' => '',
		);

		// Get the list of makers so we can ensure if a maker exists or not
		$list_of_makers = new WP_Query( array(
			'posts_per_page' => 1,
			'post_type' => 'maker',
			'meta_key' => 'email',
			'meta_value' => $maker['email'],
		) );
		$existing_maker = $list_of_makers->get_posts();

		// Fall back in-case a maker doesn't have an email setup, but still have an account.
		$existing_maker_title = wpcom_vip_get_page_by_title( htmlspecialchars( $maker['title'], ENT_QUOTES ), OBJECT, 'maker' );

		// Create our post array
		$maker_post = array(
			'post_title'   => esc_attr( $maker['title'] ),
			'post_content' => wp_kses_post( $maker['content'] ),
			'post_status'  => 'publish',
			'post_type'	   => 'maker',
		);

		// Check if a makers email or name doesn't exist
		if ( empty( $existing_maker ) || ! $existing_maker_title ) {

			// Create our post and save it's info to a variable for use in adding post meta and error checking
			$maker_id = wp_insert_post( $maker_post );

			// Cheack if everything went well when creating our post
			( is_wp_error( $maker_id ) ) ? $messages['errors'][] .= 'MAKER CREATION FAILED' : $messages['success'][] .= 'MAKER CREATED';

			$process_completed = 'Saved';

		} else {

			// Since our post already exists, we should return it's ID so we can update
			$maker_id = $existing_maker[0]->ID;

			// Add our ID to the post array
			$maker_post['ID'] = $maker_id;

			// Update our maker post
			$maker_id_updated = wp_update_post( $maker_post );

			// Cheack if everything went well when creating our post
			( is_wp_error( $maker_id_updated ) ) ? $messages['errors'][] .= 'MAKER UPDATE FAILED' : $messages['success'][] .= 'MAKER UPDATED';

			$process_completed = 'Updated';

		}

		// Add the maker email
		( update_post_meta( $maker_id, 'email', sanitize_email( $maker['email'] ) ) )     ? $messages['success'][] .= 'Email ' . $process_completed    : $messages['errors'][] .= 'Email Not ' . $process_completed;

		// Add the maker photo
		( update_post_meta( $maker_id, 'photo_url', esc_url( $maker['photo'] ) ) )     	  ? $messages['success'][] .= 'Photo ' . $process_completed    : $messages['errors'][] .= 'Photo Not ' . $process_completed;

		// Add the maker website
		( update_post_meta( $maker_id, 'website', esc_url( $maker['website'] ) ) ) 	   	  ? $messages['success'][] .= 'Website ' . $process_completed  : $messages['errors'][] .= 'Website Not ' . $process_completed;

		// Add the maker video
		( update_post_meta( $maker_id, 'video', esc_url( $maker['video'] ) ) )     	   	  ? $messages['success'][] .= 'Video ' . $process_completed    : $messages['errors'][] .= 'Video Not ' . $process_completed;

		// Add the MF Event ID
		( add_post_meta( $maker_id, 'mfei_record', absint( $maker['app_id'] ) ) ) 		  ? $messages['success'][] .= 'Event ID ' . $process_completed : $messages['errors'][] .= 'Event ID Not ' . $process_completed;

		// Add the Maker Gigya ID
		( update_post_meta( $maker_id, 'guid', sanitize_text_field( $maker['gigya'] ) ) ) ? $message['success'][]  .= 'Gigya ID ' . $process_completed : $messages['errors'][] .= 'Gigya ID Not ' . $process_completed;

		// Add Update the faire taxonomy and append any existing past faires that were assigned.
		$terms = wp_get_post_terms( absint( $maker_id ), 'faire' );
		$faire_terms = array();
		if ( ! empty( $terms ) ) {

			foreach ( $terms as $term ) {
				$faire_terms[] = $term->slug;
			}
		}
		$faire_terms[] = $maker['mfei_record'];
		$term_status = wp_set_object_terms( $maker_id, $faire_terms, 'faire', true );
		( $term_status ) ? $messages['success'][] .= 'MF ' . $process_completed       : $messages['errors'][] .= 'MF Not ' . $process_completed;

		// Add our New Maker ID to the messages
		$messages['maker_id'] .= $maker_id;

		return $messages;
	}

	/**
	 * Delete the scheduled event, and the related post.
	 */
	public function mf_delete_scheduled_event() {
		// Check our nonce and make sure it's correct
		if ( ! wp_verify_nonce( $_POST['nonce'], 'delete_scheduled_post' ) )
			die( json_encode( array( 'failed' => 'nonce failed.', 'post' => $_POST, ) ) );

		$del = wp_trash_post( intval( $_POST['postid'] ) );

		if ( $del ) {
			die( json_encode( array( 'message' => 'deleted', 'pid' => intval( $_POST['postid'] ) ) ) );
		} else {
			die( json_encode( array( 'message' => 'unable to delete...' ) ) );
		}

	}
}

$mfform = new MAKER_FAIRE_FORM();
