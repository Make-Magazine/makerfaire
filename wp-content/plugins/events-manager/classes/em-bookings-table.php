<?php
use EM\List_Table;

//Builds a table of bookings, still work in progress...
class EM_Bookings_Table extends EM\List_Table {
	/**
	 * associative array of collumns that'll be shown in order from left to right
	 * 
	 * * key - collumn name in the databse, what will be used when searching
	 * * value - label for use in collumn headers 
	 * @var array
	 */
	public $cols = array('user_name','event_name','booking_spaces','booking_status','booking_price');
	/**
	 * Asoociative array of available collumn keys and corresponding headers, which will be used to display this table of bookings
	 * @var array
	 */
	public $cols_template = array();
	public $cols_events_template = array();
	public $cols_tickets_template = array();
	public $cols_attendees_template = array();
	public $cols_template_groups = array();
	public $checkbox_id = 'booking_id';
	/**
	 * Object we're viewing bookings in relation to.
	 * @var object
	 */
	public $cols_view;
	/**
	 * Index key used for looking up status information we're filtering in the booking table 
	 * @var string
	 */
	public $string = 'needs-attention';
	/**
	 * Associative array of status information.
	 * 
	 * * key - status index value
	 * * value - associative array containing keys
	 * ** label - the label for use in filter forms
	 * ** search - array or integer status numbers to search 
	 * 
	 * @var array
	 */
	public $statuses = array();
	public $status = 'confirmed';
	public $id = 'em-bookings-table';
	/**
	 * Set to a unique id made up of $this->id dash rand() int
	 * @var string
	 */
	public $uid = 'em-bookings-table-0';
	/**
	 * Maximum number of rows to show
	 * @var int
	 */
	public $limit = 20;
	public $order = 'ASC';
	public $orderby = 'booking_name';
	public $page = 1;
	public $offset = 0;
	public $scope = 'future';
	public $search = '';
	public $show_tickets = false;
	/**
	 * @var EM_Ticket
	 */
	public $ticket;
	public $bookings;
	public $bookings_count = 0;
	
	public $person;
	public $event;
	public $item_type;
	
	function __construct($show_tickets = true){
		$this->uid = $this->id . '-' . rand(1,99999);
		$this->statuses = array(
			'all' => array('label'=>__('All','events-manager'), 'search'=>false),
			'pending' => array('label'=>__('Pending','events-manager'), 'search'=>0),
			'confirmed' => array('label'=>__('Confirmed','events-manager'), 'search'=>1),
			'cancelled' => array('label'=>__('Cancelled','events-manager'), 'search'=>3),
			'rejected' => array('label'=>__('Rejected','events-manager'), 'search'=>2),
			'needs-attention' => array('label'=>__('Needs Attention','events-manager'), 'search'=>array(0)),
			'incomplete' => array('label'=>__('Incomplete Bookings','events-manager'), 'search'=>array(0))
		);
		if( !get_option('dbem_bookings_approval') ){
			unset($this->statuses['pending']);
			unset($this->statuses['incomplete']);
			$this->statuses['confirmed']['search'] = array(0,1);
		}
		//Set basic vars
		$this->order = ( !empty($_REQUEST ['order']) && ($_REQUEST['order'] == 'DESC' || $_REQUEST['order'] == 'desc') ) ? 'DESC':'ASC';
		$this->orderby = ( !empty($_REQUEST ['orderby']) ) ? sanitize_sql_orderby($_REQUEST['orderby']):'booking_date';
		if( defined('DOING_AJAX') ) {
			$_GET['order'] = strtolower($this->order); // for WP_List_Table
			$_GET['orderby'] = $this->orderby;
		}
		$this->limit = ( !empty($_REQUEST['limit']) && is_numeric($_REQUEST['limit'])) ? $_REQUEST['limit'] : 20;//Default limit
		$this->page = ( !empty($_REQUEST['pno']) && is_numeric($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
		$_REQUEST['paged'] = $this->page;
		$this->offset = ( $this->page > 1 ) ? ($this->page-1)*$this->limit : 0;
		$this->scope = ( !empty($_REQUEST['scope']) && array_key_exists($_REQUEST ['scope'], em_get_scopes()) ) ? sanitize_text_field($_REQUEST['scope']):'future';
		$this->status = get_option('dbem_bookings_approval') ? 'needs-attention':'confirmed';
		$this->status = ( !empty($_REQUEST['status']) && array_key_exists($_REQUEST['status'], $this->statuses) ) ? sanitize_text_field($_REQUEST['status']):$this->status;
		$this->search = ( !empty($_REQUEST['em_search']) ) ? sanitize_text_field($_REQUEST['em_search']):$this->search;
		if( !empty($_REQUEST['ticket_id']) ) $this->ticket = new EM_Ticket(absint($_REQUEST['ticket_id']));
		// Basic Vars for List_Table
		$this->per_page = $this->limit;
		//build template of possible collumns
		$this->cols_template = apply_filters('em_bookings_table_cols_template', array(
			'user_login' => __('Username', 'events-manager'),
			'user_name'=>__('Name','events-manager'),
			'first_name'=>__('First Name','events-manager'),
			'last_name'=>__('Last Name','events-manager'),
			'event_name'=>__('Event','events-manager'), // will soon be removed and merged during generation from cols_events_tamplate
			'event_date'=>__('Event Date(s)','events-manager'), // will soon be removed and merged during generation from cols_events_tamplate
			'event_time'=>__('Event Time(s)','events-manager'), // will soon be removed and merged during generation from cols_events_tamplate
			'user_email'=>__('E-mail','events-manager'),
			'dbem_phone'=>__('Phone Number','events-manager'),
			'booking_spaces'=>__('Spaces','events-manager'),
			'booking_status'=>__('Status','events-manager'),
			'booking_rsvp_status'=>__('RSVP Status','events-manager'),
			'booking_date'=>__('Booking Date','events-manager'),
			'booking_price'=>__('Total','events-manager'),
			'booking_id'=>__('Booking ID','events-manager'),
			'booking_comment'=>__('Booking Comment','events-manager')
		), $this);
		$this->cols_events_template = apply_filters('em_bookings_table_cols_events_template', array(
			'event_name'=>__('Event','events-manager'),
			'event_date'=>__('Event Date(s)','events-manager'),
			'event_time'=>__('Event Time(s)','events-manager'),
		), $this);
		$this->cols_attendees_template = apply_filters('em_bookings_table_cols_attendees_template', array(), $this);
		$this->cols_tickets_template = apply_filters('em_bookings_table_cols_tickets_template', array(
			'ticket_spaces'=>__('Ticket Spaces','events-manager'),
			'ticket_name'=>__('Ticket Name','events-manager'),
			'ticket_description'=>__('Ticket Description','events-manager'),
			'ticket_price'=>__('Ticket Price','events-manager'),
			'ticket_total'=>__('Ticket Total','events-manager'),
			'ticket_id'=>__('Ticket ID','events-manager')
		), $this);
		$this->cols_template_groups = apply_filters('em_bookings_table_cols_template_groups', array(
			'user'=> array(
					'label' => __('User Fields','events-manager'),
					'fields' => array('user_login', 'user_name', 'first_name', 'last_name', 'user_email', ),
				),
			'event'=> array(
					'label' => __('Event','events-manager'),
					'fields' => array('event_name', 'event_date', 'event_time', ),
				),
			'booking' => array(
					'label' => __('Booking Data','events-manager'),
					'fields' => array('dbem_phone',  'booking_comment', 'booking_spaces', 'boking_status', 'booking_date', 'booking_price', 'booking_id', ),
				),
			'ticket'=> array(
					'label' => __('Ticket','events-manager'),
					'fields' => array('ticket_spaces', 'ticket_name', 'ticket_description', 'ticket_price', 'ticket_total', 'ticket_id', ),
				),
			'attendee' => array(
					'label' => __('Attendee (Per Space Booked)','events-manager'),
					'fields' => array(),
				),
			'payment' => array(
					'label' => __('Payment Information','events-manager'),
					'fields' => array(),
				),
		), $this);
		//add tickets to template if we're showing rows by booking-ticket
		if( $show_tickets ){
			$this->show_tickets = true;
			$this->cols = array('user_name','event_name','ticket_name','ticket_price','booking_spaces','booking_status');
			if( !is_admin() ){
				$this->cols[] = 'actions';
			}
			$this->cols_template = array_merge( $this->cols_template, $this->cols_tickets_template);
		}elseif( !is_admin() ){
			$this->cols[] = 'actions';
		}
		$this->cols_template['actions'] = __('Actions','events-manager');
		//calculate collumns if post requests		
		if( !empty($_REQUEST['cols']) ){
		    if( is_array($_REQUEST['cols']) ){
			    $this->cols = array();
		    	foreach( $_REQUEST['cols'] as $k => $col ){
		    		$this->cols[$k] = sanitize_text_field($col);
			    }
    		}else{
    			foreach( explode(',',$_REQUEST['cols']) as $k => $col ){
    				if( array_key_exists($col, $this->cols_template) ){
					    $this->cols[$k] = $col;
				    }
			    }
    		}
		}
		//load collumn view settings
		if( $this->get_person() !== false ){
			$this->cols_view = $this->get_person();
		}elseif( $this->get_ticket() !== false ){
			$this->cols_view = $this->get_ticket();
		}elseif( $this->get_event() !== false ){
			$this->cols_view = $this->get_event();
		}
		//save collumns depending on context and user preferences
		if( empty($_REQUEST['cols']) ){
			if(!empty($this->cols_view) && is_object($this->cols_view)){
				//check if user has settings for object type
				$settings = get_user_meta(get_current_user_id(), 'em_bookings_view-'.get_class($this->cols_view), true );
			}else{
				$settings = get_user_meta(get_current_user_id(), 'em_bookings_view', true );
			}
			if( !empty($settings) ){
				$this->cols = $settings;
			}
		}elseif( !empty($_REQUEST['cols']) && empty($_REQUEST['no_save']) ){ //save view settings for next time
		    if( !empty($this->cols_view) && is_object($this->cols_view) ){
				update_user_meta(get_current_user_id(), 'em_bookings_view-'.get_class($this->cols_view), $this->cols );
			}else{
				update_user_meta(get_current_user_id(), 'em_bookings_view', $this->cols );
			}
		}
		//clean any columns from saved views that no longer exist
		foreach($this->cols as $col_key => $col_name){
			if( !array_key_exists($col_name, $this->cols_template)){
				unset($this->cols[$col_key]);
			}
		}
		do_action('em_bookings_table', $this);
		
		// LIST TABLE
		if( empty($GLOBALS['hook_suffix']) ){
			$GLOBALS['hook_suffix'] = 'events-manager-bookings';
		}
		if( !empty($_GET['page']) ) {
			$this->item_type = str_replace('events-manager-', '', $_GET['page']);
		}else{
			$this->item_type = 'bookings';
		}
		// handle convert_to_screen lacking in places
		if( !function_exists('convert_to_screen')){
			function convert_to_screen( $hook_name ) {
				return new EM_WP_Screen;
			}
		}
		if( !function_exists('get_column_headers') ){
			function get_column_headers( $screen ) {
				return array();
			}
		}
		parent::__construct();
	}

	
	/**
	 * @return EM_Person|false
	 */
	function get_person(){
		global $EM_Person;
		if( !empty($this->person) && is_object($this->person) ){
			return $this->person;
		}elseif( !empty($_REQUEST['person_id']) && !empty($EM_Person) && is_object($EM_Person) ){
			return $EM_Person;
		}
		return false;
	}
	/**
	 * @return EM_Ticket|false
	 */
	function get_ticket(){
		global $EM_Ticket;
		if( !empty($this->ticket) && is_object($this->ticket) ){
			return $this->ticket;
		}elseif( !empty($EM_Ticket) && is_object($EM_Ticket) ){
			return $EM_Ticket;
		}
		return false;
	}
	/**
	 * @return $EM_Event|false
	 */
	function get_event(){
		global $EM_Event;
		if( !empty($this->event) && is_object($this->event) ){
			return $this->event;
		}elseif( !empty($EM_Event) && is_object($EM_Event) ){
			return $EM_Event;
		}
		return false;
	}
	
	/**
	 * Gets the bookings for this object instance according to its settings
	 * @param boolean $force_refresh
	 * @return EM_Bookings
	 */
	function get_bookings($force_refresh = true){	
		if( empty($this->bookings) || $force_refresh ){
			$EM_Ticket = $this->get_ticket();
			$EM_Event = $this->get_event();
			$EM_Person = $this->get_person();
			$default_args = apply_filters('em_bookings_table_get_bookings_args', array('limit'=>$this->limit,'offset'=>$this->offset), $this);
			if( $EM_Person !== false ){
				$args = array('person'=>$EM_Person->ID,'scope'=>$this->scope,'status'=>$this->get_status_search(),'order'=>$this->order,'orderby'=>$this->orderby, 'search' => $this->search);
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, $default_args));
			}elseif( $EM_Ticket !== false ){
				//searching bookings with a specific ticket
				$args = array('ticket_id'=>$EM_Ticket->ticket_id, 'order'=>$this->order,'orderby'=>$this->orderby, 'search' => $this->search);
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, $default_args));
			}elseif( $EM_Event !== false ){
				//bookings for an event
				$args = array('event'=>$EM_Event->event_id,'scope'=>false,'status'=>$this->get_status_search(),'order'=>$this->order,'orderby'=>$this->orderby, 'search' => $this->search);
				$args['owner'] = !current_user_can('manage_others_bookings') ? get_current_user_id() : false;
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, $default_args));
			}else{
				//all bookings for a status
				$args = array('status'=>$this->get_status_search(),'scope'=>$this->scope,'order'=>$this->order,'orderby'=>$this->orderby, 'search' => $this->search);
				$args['owner'] = !current_user_can('manage_others_bookings') ? get_current_user_id() : false;
				$this->bookings_count = EM_Bookings::count($args);
				$this->bookings = EM_Bookings::get(array_merge($args, $default_args));
			}
		}
		return $this->bookings;
	}
	
	function get_count(){
		return $this->bookings_count;
	}
	
	function get_status_search(){
		if(is_array($this->statuses[$this->status]['search'])){
			return implode(',',$this->statuses[$this->status]['search']);
		}
		return $this->statuses[$this->status]['search'];
	}
	
	/**
	 * @deprecated use WP_List_Table functions now
	 * @return void
	 */
	function output(){
		do_action('em_bookings_table_header',$this); //won't be overwritten by JS	
		$this->output_overlays();
		$this->output_table();
		do_action('em_bookings_table_footer',$this); //won't be overwritten by JS	
	}
	
	function output_overlays(){
		$EM_Ticket = $this->get_ticket();
		$EM_Event = $this->get_event();
		$EM_Person = $this->get_person();
		// join all fields into one set of cols, with general taking precendence over events, then tickets then attendees
		$cols_template = array_merge( $this->cols_attendees_template, $this->cols_tickets_template, $this->cols_events_template, $this->cols_template );
		$grouped_fields = array();
		foreach( $this->cols_template_groups as $group_type => $group_data ){
			$grouped_fields = array_merge($grouped_fields, $group_data['fields']);
		}
		$ungrouped_fields = array_diff(array_keys($cols_template), $grouped_fields);
		$uid = esc_attr($this->uid);
		$id = esc_attr($this->id);
		?>
		<div class="em pixelbones em-modal <?php em_template_classes('bookings-table'); ?> em-bookings-table-settings em-bookings-table-modal" id="<?php echo $uid; ?>-settings-modal">
			<form id="<?php echo $uid; ?>-settings-form" class="em-bookings-table-form em-bookings-table-settings-form" action="" method="post" rel="#<?php echo $uid . '-form'; ?>">
				<div class="em-modal-popup">
					<header>
						<a class="em-close-modal"></a><!-- close modal -->
						<div class="em-modal-title"><?php esc_attr_e('Bookings Table Settings','events-manager'); ?></div>
					</header>
					<div class="em-modal-content input">
						<p><?php _e('Modify what information is displayed in this booking table.','events-manager') ?></p>
						<div class="<?php echo $uid; ?>-rows-setting"">
							<label for="<?php echo $uid; ?>-rows-setting"><strong><?php esc_html_e('Results per Page', 'events-manager'); ?></strong></label>
							<select name="limit" class="<?php echo $id; ?>-filter" id="<?php echo $uid; ?>-rows-setting">
								<option value="<?php echo esc_attr($this->limit) ?>"><?php echo esc_html(sprintf(__('%s Rows','events-manager'),$this->limit)); ?></option>
								<option value="5">5</option>
								<option value="10">10</option>
								<option value="25">25</option>
								<option value="50">50</option>
								<option value="100">100</option>
							</select>
						</div>
						<div class="em-bookings-table-cols">
							<div class="em-bookings-cols-selected">
								<p>
									<strong><?php _e('Columns to show','events-manager')?></strong><br>
									<?php _e('Remove or reorder columns below.','events-manager'); ?>
								</p>
								<div class="em-bookings-cols-sortable">
									<?php foreach( $this->cols as $col_key ): ?>
										<div class="item" data-value="<?php echo esc_attr($col_key); ?>">
											<span><?php echo esc_html($this->cols_template[$col_key]); ?></span>
											<a href="#" class="remove" tabindex="-1" title="Remove">×</a>
											<input type="hidden" name="cols[<?php echo esc_attr($col_key); ?>]" value="1" class="em-bookings-col-item" />
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<div class="em-bookings-cols-select">
								<p>
									<strong><?php _e('Columns to choose','events-manager')?></strong><br>
									<?php _e('Add to your table from the columns below.','events-manager') ?>
								</p>
								<select class="em-bookings-cols-inactive em-selectize always-open checkboxes" multiple>
									<?php foreach( $this->cols_template_groups as $col_type => $col_group_data ): ?>
										<optgroup label="<?php echo esc_attr($col_group_data['label']); ?>"  data-data='{"data":{"type":"<?php echo esc_attr($col_type) ?>"}}'>
											<?php foreach( $col_group_data['fields'] as $col_key  ): ?>
												<?php if( !empty($cols_template[$col_key]) ): ?>
													<option value="<?php echo esc_attr($col_key); ?>" data-data='{"data":{"type":"<?php echo esc_attr($col_type) ?>"}}' <?php if( in_array($col_key, $this->cols) ) echo 'selected'; ?>>
														<?php echo esc_html($cols_template[$col_key]); ?>
													</option>
												<?php endif; ?>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
									<optgroup label="<?php esc_html_e('Other', 'events-manager'); ?>" data-type="booking">
										<?php foreach( $ungrouped_fields as $col_key  ): ?>
											<?php if( !empty($cols_template[$col_key]) ): ?>
												<option value="<?php echo esc_attr($col_key); ?>" data-data='{"data":{"type":"booking"}}' <?php if( in_array($col_key, $this->cols) ) echo 'selected'; ?>>
													<?php echo esc_html($cols_template[$col_key]); ?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</optgroup>
								</select>
							</div>
						</div>
					</div>
					<footer class="em-submit-section input">
						<div>
							<button type="submit" class="button button-primary"><?php esc_html_e('Save Settings', 'events-manager'); ?></button>
						</div>
					</footer>
				</div>
			</form>
		</div>
		<div class="em pixelbones em-modal <?php em_template_classes('bookings-table'); ?> em-bookings-table-export em-bookings-table-modal" id="<?php echo $uid; ?>-export-modal">
			<form id="<?php echo $uid; ?>-export-form" class="em-bookings-table-form em-bookings-table-export-form" action="" method="post" rel="#<?php echo $uid . '-form'; ?>">
				<div class="em-modal-popup">
					<header>
						<a class="em-close-modal"></a><!-- close modal -->
						<div class="em-modal-title"><?php esc_attr_e('Bookings Table Settings','events-manager'); ?></div>
					</header>
					<div class="em-modal-content input">
						<p><?php esc_html_e('Select the options below and export all the bookings you have currently filtered (all pages) into a CSV spreadsheet format.','events-manager') ?></p>
						<p><?php esc_html_e('Split bookings by ticket type','events-manager')?> <input type="checkbox" name="show_tickets" value="1" />
						<a href="#" class="em-tooltip" aria-label="<?php esc_attr_e('If your events have multiple tickets, enabling this will show a separate row for each ticket within a booking.','events-manager'); ?>">?</a>
						<?php do_action('em_bookings_table_export_options'); ?>
						<div class="em-bookings-table-cols">
							<div class="em-bookings-cols-selected">
								<p><strong><?php esc_html_e('Columns to export','events-manager')?></strong></p>
								<div class="em-bookings-cols-sortable">
									<?php foreach( $this->cols as $col_key ): ?>
										<div class="item" data-value="<?php echo esc_attr($col_key); ?>">
											<span><?php echo esc_html($this->cols_template[$col_key]); ?></span>
											<a href="#" class="remove" tabindex="-1" title="Remove">×</a>
											<input type="hidden" name="cols[<?php echo esc_attr($col_key); ?>]" value="1" class="em-bookings-col-item" />
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<div class="em-bookings-cols-select">
								<p><strong><?php esc_html_e('Exportable columns','events-manager')?></strong></p>
								<select class="em-bookings-cols-inactive em-selectize always-open checkboxes" multiple>
									<?php foreach( $this->cols_template_groups as $col_type => $col_group_data ): ?>
										<optgroup label="<?php echo esc_attr($col_group_data['label']); ?>"  data-data='{"data":{"type":"<?php echo esc_attr($col_type) ?>"}}'>
											<?php foreach( $col_group_data['fields'] as $col_key  ): ?>
												<?php if( !empty($cols_template[$col_key]) ): ?>
													<option value="<?php echo esc_attr($col_key); ?>" data-data='{"data":{"type":"<?php echo esc_attr($col_type) ?>"}}' <?php if( in_array($col_key, $this->cols) ) echo 'selected'; ?>>
														<?php echo esc_html($cols_template[$col_key]); ?>
													</option>
												<?php endif; ?>
											<?php endforeach; ?>
										</optgroup>
									<?php endforeach; ?>
									<optgroup label="<?php esc_html_e('Other', 'events-manager'); ?>" data-type="booking">
										<?php foreach( $ungrouped_fields as $col_key  ): ?>
											<?php if( !empty($cols_template[$col_key]) ): ?>
												<option value="<?php echo esc_attr($col_key); ?>" data-data='{"data":{"type":"booking"}}' <?php if( in_array($col_key, $this->cols) ) echo 'selected'; ?>>
													<?php echo esc_html($cols_template[$col_key]); ?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									</optgroup>
								</select>
							</div>
						</div>
					</div>
					<footer class="em-submit-section input">
						<div class="<?php echo $id; ?>-filters" style="display:none; visibility:hidden;"><!-- houses filter data as modified for export --></div>
						<div>
							<?php if( $EM_Event !== false ): ?>
								<input type="hidden" name="event_id" value='<?php echo esc_attr($EM_Event->event_id); ?>' />
							<?php endif; ?>
							<?php if( $EM_Ticket !== false ): ?>
								<input type="hidden" name="ticket_id" value='<?php echo esc_attr($EM_Ticket->ticket_id); ?>' />
							<?php endif; ?>
							<?php if( $EM_Person !== false ): ?>
								<input type="hidden" name="person_id" value='<?php echo esc_attr($EM_Person->ID); ?>' />
							<?php endif; ?>
							<input type="hidden" name="no_save" value='1' />
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('export_bookings_csv'); ?>" />
							<input type="hidden" name="_emnonce" value="<?php echo wp_create_nonce('export_bookings_csv'); ?>" />
							<input type="hidden" name="action" value="export_bookings_csv" />
							<button type="submit" class="button button-primary"><?php esc_html_e('Export', 'events-manager'); ?></button>
						</div>
					</footer>
				</div>
			</form>
		</div>
		<br class="clear" />
		<?php
	}
	
	function output_table(){
		$EM_Ticket = $this->get_ticket();
		$EM_Event = $this->get_event();
		$EM_Person = $this->get_person();
		$this->get_bookings(true); //get bookings and refresh
		$uid = esc_attr($this->uid);
		$id = esc_attr($this->id);
		?>
		<div class='<?php echo $id; ?> em_obj frontend' id="<?php echo $id; ?>">
			<form class='bookings-filter' method='post' action='' id="<?php echo $uid; ?>-form">
				<?php if( $EM_Event !== false ): ?>
				<input type="hidden" name="event_id" value='<?php echo esc_attr($EM_Event->event_id); ?>' >
				<?php endif; ?>
				<?php if( $EM_Ticket !== false ): ?>
				<input type="hidden" name="ticket_id" value='<?php echo esc_attr($EM_Ticket->ticket_id); ?>' >
				<?php endif; ?>
				<?php if( $EM_Person !== false ): ?>
				<input type="hidden" name="person_id" value='<?php echo esc_attr($EM_Person->ID); ?>' >
				<?php endif; ?>
				<input type="hidden" name="is_public" value="<?php echo ( !empty($_REQUEST['is_public']) || !is_admin() ) ? 1:0; ?>" >
				<input type="hidden" name="pno" value='<?php echo esc_attr($this->page); ?>' >
				<input type="hidden" name="order" value='<?php echo esc_attr($this->order); ?>' >
				<input type="hidden" name="orderby" value='<?php echo esc_attr($this->orderby); ?>' >
				<input type="hidden" name="_wpnonce" value="<?php echo ( !empty($_REQUEST['_wpnonce']) ) ? esc_attr($_REQUEST['_wpnonce']):wp_create_nonce('em_bookings_table'); ?>" >
				<input type="hidden" name="action" value="em_bookings_table" >
				<input type="hidden" name="cols" value="<?php echo esc_attr(implode(',', $this->cols)); ?>" >
				<input type="hidden" name="table_id" value="<?php echo absint( str_replace($this->id . '-', '', $this->uid) ); ?>">
				
				<div class='tablenav'>
					<?php
					$this->extra_tablenav('top');
					if ( $this->bookings_count >= $this->limit ) {
						$bookings_nav = em_admin_paginate( $this->bookings_count, $this->limit, $this->page, array(),'#%#%','#');
						echo $bookings_nav;
					}
					?>
				</div>
				<div class="clear"></div>
				<div class='table-wrap'>
				<table id='dbem-bookings-table' class='widefat post '>
					<thead>
						<tr>
							<?php /*						
							<th class='manage-column column-cb check-column' scope='col'>
								<input class='select-all' type="checkbox" value='1' />
							</th>
							*/ ?>
							<th class='manage-column' scope='col'><?php echo implode("</th><th class='manage-column' scope='col'>", $this->get_headers()); ?></th>
						</tr>
					</thead>
					<?php if( $this->bookings_count > 0 ): ?>
					<tbody>
						<?php 
						$rowno = 0;
						$event_count = (!empty($event_count)) ? $event_count:0;
						foreach ($this->bookings->bookings as $EM_Booking) {
							?>
							<tr>
								<?php  /*
								<th scope="row" class="check-column" style="padding:7px 0px 7px;"><input type='checkbox' value='<?php echo $EM_Booking->booking_id ?>' name='bookings[]'/></th>
								*/ 
								/* @var $EM_Booking EM_Booking */
								/* @var $EM_Ticket_Booking EM_Ticket_Booking */
								if( $this->show_tickets && empty($this->ticket) ){
									$count = 1;
									foreach($EM_Booking->get_tickets_bookings()->get_ticket_bookings() as $EM_Ticket_Booking){
										if( $count > 1 ){
											echo '</tr><tr>';
										}
										$this->ticket = $EM_Ticket_Booking->get_ticket();
										$row = $this->get_row($EM_Booking);
										foreach( $row as $key => $row_cell ){
										?><td class="em-bt-col-<?php echo esc_attr($key); ?>"class="em-bt-col-<?php echo esc_attr($key); ?>"><?php echo $row_cell; ?></td><?php
										}
										$this->ticket = null;
										$count++;
									}
								}else{
									$row = $this->get_row($EM_Booking);
									foreach( $row as $key => $row_cell ){
									?><td class="em-bt-col-<?php echo esc_attr($key); ?>"><?php echo $row_cell; ?></td><?php
									}
								}
								?>
							</tr>
							<?php
						}
						?>
					</tbody>
					<?php else: ?>
						<tbody>
							<tr><td scope="row" colspan="<?php echo count($this->cols); ?>"><?php esc_html_e('No bookings.', 'events-manager'); ?></td></tr>
						</tbody>
					<?php endif; ?>
				</table>
				</div>
				<?php if( !empty($bookings_nav) && $this->bookings_count >= $this->limit ) : ?>
				<div class='tablenav'>
					<?php echo $bookings_nav; ?>
					<div class="clear"></div>
				</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}
	
	function get_headers($csv = false){
		$headers = array();
		foreach($this->cols as $col){
			if( $col == 'actions' ){
				if( !$csv ) $headers[$col] = '&nbsp;';
			}elseif(array_key_exists($col, $this->cols_template)){
				/* for later - col ordering!
				if($this->orderby == $col){
					if($this->order == 'ASC'){
						$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].' (^)</a>';
					}else{
						$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].' (d)</a>';
					}
				}else{
					$headers[] = '<a class="em-bookings-orderby" href="#'.$col.'">'.$this->cols_template[$col].'</a>';
				}
				*/
				$v = $this->cols_template[$col];
				if( $csv ){
					$v = self::sanitize_spreadsheet_cell($v);
				}
				$headers[$col] = $v;
			}
		}
		return apply_filters('em_bookings_table_get_headers', $headers, $csv, $this);
	}
	
	function get_table(){
		
	}
	
	/**
	 * @param Object $object
	 * @return array()
	 */
	function get_row( $object, $format = 'html' ){
		/* @var $EM_Ticket EM_Ticket */
		/* @var $EM_Ticket_Booking EM_Ticket_Booking */
		/* @var $EM_Booking EM_Booking */
		$this->format = $format === true ? 'csv' : $format;  //backwards compatibility, previously $format was $csv which was a boolean
		if( $object instanceof EM_Ticket_Booking || $object instanceof EM_Ticket_Bookings ){
			$EM_Ticket_Booking = $object;
			$EM_Booking = $EM_Ticket_Booking->get_booking();
			$this->ticket = $EM_Ticket_Booking->get_ticket();
		}elseif( $object instanceof EM_Booking ){
			$EM_Booking = $object;
		}else{
			// unrecognized $object, return empty padded array
			return array_pad( array(), count($this->cols), '' );
		}
		$cols = array();
		foreach($this->cols as $col){
			//TODO fix urls so this works in all pages in front as well
			$val = $this->default_column_data( $EM_Booking, $col );
			//add to cols
			$cols[$col] = $val;
		}
		return $cols;
	}
	
	function get_row_csv($EM_Booking){
	    $row = $this->get_row($EM_Booking, 'csv');
	    foreach($row as $k=>$v){
			if ( empty($v) ) {
				$row[ $k ] = '';
			} else {
				$row[ $k ] = html_entity_decode( $v );
			}
	    } //remove things like &amp; which may have been saved to the DB directly
	    return $row;
	}
	
	public static function sanitize_spreadsheet_cell( $cell ){
		if ( !empty($cell) ) {
			return preg_replace( '/^([;=@\+\-])/', "'$1", $cell );
		}
		return '';
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @return mixed
	 */
	function get_booking_actions($EM_Booking){
		$booking_actions = array();
		$url = $EM_Booking->get_event()->get_bookings_url();
		$s = absint($EM_Booking->booking_status);
		switch($EM_Booking->booking_status){
			case 0: //pending
				if( get_option('dbem_bookings_approval') ){
					$booking_actions = array(
						'approve' => '<a class="em-bookings-approve em-bookings-approve-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_approve',  'nonce' => wp_create_nonce('bookings_approve'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','events-manager').'</a>',
						'reject' => '<a class="em-bookings-reject em-bookings-reject-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_reject',  'nonce' => wp_create_nonce('bookings_reject'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Reject','events-manager').'</a>',
						'delete' => '<span class="trash"><a class="em-bookings-delete em-bookings-delete-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_delete',  'nonce' => wp_create_nonce('bookings_delete'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','events-manager').'</a></span>',
						'edit' => '<a class="em-bookings-edit em-bookings-edit-'. $s .'" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','events-manager').'</a>',
					);
					break;
				}//if approvals are off, treat as a 1
			case 1: //approved
				$booking_actions = array(
					'unapprove' => '<a class="em-bookings-unapprove" href="'.em_add_get_params($url, array('action'=>'bookings_unapprove',  'nonce' => wp_create_nonce('bookings_unapprove'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Unapprove','events-manager').'</a>',
					'reject' => '<a class="em-bookings-reject em-bookings-reject-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_reject',  'nonce' => wp_create_nonce('bookings_reject'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Reject','events-manager').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete em-bookings-delete-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_delete',  'nonce' => wp_create_nonce('bookings_delete'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','events-manager').'</a></span>',
					'edit' => '<a class="em-bookings-edit em-bookings-edit-'. $s .'" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','events-manager').'</a>',
				);
				break;
			case 2: //rejected
				$booking_actions = array(
					'approve' => '<a class="em-bookings-approve em-bookings-approve-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_approve',  'nonce' => wp_create_nonce('bookings_approve'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','events-manager').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete em-bookings-delete-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_delete',  'nonce' => wp_create_nonce('bookings_delete'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','events-manager').'</a></span>',
					'edit' => '<a class="em-bookings-edit em-bookings-edit-'. $s .'" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','events-manager').'</a>',
				);
				break;
			case 3: //cancelled
			case 4: //awaiting online payment - similar to pending but always needs approval in EM Free
			case 5: //awaiting payment - similar to pending but always needs approval in EM Free
				$booking_actions = array(
					'approve' => '<a class="em-bookings-approve em-bookings-approve-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_approve',  'nonce' => wp_create_nonce('bookings_approve'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Approve','events-manager').'</a>',
					'reject' => '<a class="em-bookings-reject em-bookings-reject-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_reject',  'nonce' => wp_create_nonce('bookings_reject'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Reject','events-manager').'</a>',
					'delete' => '<span class="trash"><a class="em-bookings-delete em-bookings-delete-'. $s .'" href="'.em_add_get_params($url, array('action'=>'bookings_delete',  'nonce' => wp_create_nonce('bookings_delete'), 'booking_id'=>$EM_Booking->booking_id)).'">'.__('Delete','events-manager').'</a></span>',
					'edit' => '<a class="em-bookings-edit em-bookings-edit-'. $s .'" href="'.em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null)).'">'.__('Edit/View','events-manager').'</a>',
				);
				break;
				
		}
		if( !get_option('dbem_bookings_approval') ) unset($booking_actions['unapprove']);
		$booking_actions = apply_filters('em_bookings_table_booking_actions_'.$EM_Booking->booking_status ,$booking_actions, $EM_Booking);
		return apply_filters('em_bookings_table_cols_col_action', $booking_actions, $EM_Booking);
	}
	
	function get_booking_bulk_actions(){
		$booking_actions = array(
			'approve' => __('Approve','events-manager'),
			'unapprove' => __('Unapprove','events-manager'),
			'reject' => __('Reject','events-manager'),
			'delete' => __('Delete','events-manager'),
		);
		return apply_filters('em_bookings_table_get_booking_bulk_actions', $booking_actions, $this);
	}
	
	/**
	 * Get the table data
	 *
	 * @return array
	 */
	protected function table_data(){
		//Do the search
		$EM_Bookings = $this->get_bookings();
		$this->total_items = $this->bookings_count;
		//Prepare data
		return $EM_Bookings->bookings;
	}
	
	
	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns(){
		$fields = EM_Bookings::get_sql_accepted_fields();
		$sortable_cols = array();
		foreach( $fields['orderby'] as $field => $col ){
			$sortable_cols[$field] = array( $field, false );
		}
		// some specific fields that still map
		$sortable_cols['event_date'] = array('event_start', false);
		return apply_filters('em_bookings_table_get_sortable_columns', $sortable_cols, $this);
	}
	
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns(){
		$columns = parent::get_columns()  + $this->get_headers();
		return apply_filters('em_bookings_table_get_columns', $columns, $this);
	}
	
	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  EM_Booking   $object             Data
	 * @param  string       $column_name        Current column name
	 *
	 * @return string
	 */
	function column_default( $object, $column_name ){
		/* @var $EM_Ticket EM_Ticket */
		/* @var $EM_Ticket_Booking EM_Ticket_Booking */
		/* @var $EM_Booking EM_Booking */
		$EM_Booking = $object;
		$val = '';
		if( in_array($column_name, $this->cols) || $column_name === 'actions' ){
			$val = $this->default_column_data( $EM_Booking, $column_name );
			$primary_column = reset($this->cols);
			if( $primary_column === $column_name ){
				// add responsive extra data
				/* WIP for frontend
				ob_start();
				?>
				<div class="em-table-row-responsive-meta">
					<?php echo $EM_Booking->get_event()->output('#_EVENTLINK - #_EVENTSTARTDATE @ #_EVENTTIMES'); ?><br>
					[<?php echo esc_html($EM_Booking->get_status()); ?>] <?php echo sprintf(__('%d Spaces','events-manager'), $EM_Booking->get_spaces()); ?> @ <?php echo $EM_Booking->get_price(true); ?>
				</div>
				<?php
				$val .= ob_get_clean();
				*/
				// ajax status icons
				if( !empty($EM_Booking->feedback_message) ){
					
					if( $EM_Booking->booking_status === false && DOING_AJAX && !empty($_REQUEST['row_action']) && $_REQUEST['row_action'] == 'bookings_delete' ){
						$css_icon = empty( $EM_Booking->errors ) ? 'trash' : 'cross-circle';
					} else {
						$css_icon = empty( $EM_Booking->errors ) ? 'updated' : 'cross-circle';
					}
					$val = '<span href="#" class="em-icon em-icon-'.$css_icon.' em-tooltip" aria-label="'. $EM_Booking->feedback_message .'"></span> ' . $val;
				}
			}
		}
		return $val;
	}
	
	public function default_column_data( $EM_Booking, $col ){
		if( $this->show_tickets && $this->ticket ){
			$EM_Ticket = $this->ticket;
			$EM_Tickets_Bookings = $EM_Booking->get_tickets_bookings();
			if( !empty($EM_Tickets_Bookings->tickets_bookings[$EM_Ticket->ticket_id]) ){
				$EM_Ticket_Booking = $EM_Tickets_Bookings->tickets_bookings[$EM_Ticket->ticket_id];
			}
		}
		$format = $this->format;
		$val = ''; //reset value
		//is col a user col or else?
		//TODO fix urls so this works in all pages in front as well
		if( $col == 'user_email' ){
			$val = $EM_Booking->get_person()->user_email;
		}elseif($col == 'user_login'){
			if( $EM_Booking->is_no_user() ){
				$val = esc_html__('Guest User', 'events-manager');
			}else{
				if( $format == 'csv' ){
					$val = $EM_Booking->get_person()->user_login;
				}else{
					$val = '<a href="'.esc_url(add_query_arg(array('person_id'=>$EM_Booking->person_id, 'event_id'=>null), $EM_Booking->get_event()->get_bookings_url())).'">'. esc_html($EM_Booking->person->user_login) .'</a>';
				}
			}
		}elseif($col == 'dbem_phone'){
			$val = $EM_Booking->get_person()->phone;
		}elseif($col == 'user_name'){
			if( $format == 'csv' ){
				$val = $EM_Booking->get_person()->get_name();
			}elseif( $EM_Booking->is_no_user() ){
				$val = esc_html($EM_Booking->get_person()->get_name());
			}else{
				$val = '<a href="'.esc_url(add_query_arg(array('person_id'=>$EM_Booking->person_id, 'event_id'=>null), $EM_Booking->get_event()->get_bookings_url())).'">'. esc_html($EM_Booking->person->get_name()) .'</a>';
			}
		}elseif($col == 'first_name'){
			$val = $EM_Booking->get_person()->first_name;
		}elseif($col == 'last_name'){
			$val = $EM_Booking->get_person()->last_name;
		}elseif($col == 'event_name'){
			if( $format == 'csv' ){
				$val = $EM_Booking->get_event()->event_name;
			}else{
				$val = '<a href="'.$EM_Booking->get_event()->get_bookings_url().'">'. esc_html($EM_Booking->get_event()->event_name) .'</a>';
			}
		}elseif($col == 'event_date'){
			$val = $EM_Booking->get_event()->output('#_EVENTDATES');
		}elseif($col == 'event_time'){
			$val = $EM_Booking->get_event()->output('#_EVENTTIMES');
		}elseif($col == 'booking_price'){
			$val = $EM_Booking->get_price(true);
		}elseif($col == 'booking_status'){
			$val = $EM_Booking->get_status(true);
		} elseif ( $col == 'booking_rsvp_status' ) {
			$val = $EM_Booking->get_rsvp_status( true );
		}elseif($col == 'booking_date'){
			$val = $EM_Booking->date()->i18n( get_option('dbem_date_format').' '. get_option('dbem_time_format') );
		}elseif($col == 'actions' && $format !== 'csv' ) {
			// html only
			$val = implode(' | ', $this->get_booking_actions($EM_Booking));
		}elseif( $col == 'booking_spaces' ){
			$val = $EM_Booking->get_spaces();
		}elseif( $col == 'booking_id' ){
			$val = $EM_Booking->booking_id;
		}elseif( $col == 'ticket_spaces' ){
			if ( !empty($EM_Ticket_Booking) ) {
				$val = $EM_Ticket_Booking->get_spaces();
			} elseif ( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking, 'ticket_spaces' );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$val = $EM_Booking->get_spaces();
			}
		}elseif( $col == 'ticket_name' ){
			if ( !empty($EM_Ticket) ) {
				$val = $EM_Ticket->name;
			} elseif( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$val = $EM_Booking->get_tickets()->get_first()->name;
			}
		}elseif( $col == 'ticket_description' ){
			if ( !empty($EM_Ticket) ) {
				$val = $EM_Ticket->description;
			} elseif( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$val = $EM_Booking->get_tickets()->get_first()->description;
			}
		}elseif( $col == 'ticket_price' ){
			if ( !empty($EM_Ticket) ) {
				$val = $EM_Ticket->get_price(true);
			} elseif( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking, 'ticket_price' );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$val = $EM_Booking->get_tickets()->get_first()->get_price(true);
			}
		}elseif( $col == 'ticket_total' ){
			if ( !empty($EM_Ticket_Booking) ) {
				$val = apply_filters('em_bookings_table_row_booking_price_ticket', $EM_Ticket_Booking->get_price(false), $EM_Booking, true);
				$val = $EM_Booking->format_price($val * (1 + $EM_Booking->get_tax_rate(true)));
			} elseif( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking, 'ticket_total' );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$price = $EM_Booking->get_tickets()->get_first()->get_price(false);
				$val = apply_filters('em_bookings_table_row_booking_price_ticket', $price, $EM_Booking, true);
				$val = $EM_Booking->format_price($val * (1 + $EM_Booking->get_tax_rate(true)));
			}
		}elseif( $col == 'ticket_id' ){
			if ( !empty($EM_Ticket) ) {
				$val = $EM_Ticket->ticket_id;
			} elseif( $EM_Booking->get_tickets()->count() > 1 ) {
				$val = $this->multiple_tickets_col( $EM_Booking, 'ticket_id' );
				$allow_html = true;
			} elseif ( $EM_Booking->get_tickets()->get_first() ) {
				$val = $EM_Booking->get_tickets()->get_first()->ticket_id;
			}
		}elseif( $col == 'booking_comment' ){
			$val = $EM_Booking->booking_comment;
		}
		//escape all HTML if destination is HTML or not defined
		if( empty($allow_html) && ($format == 'html' || empty($format)) && !in_array($col, array('user_login', 'user_name', 'event_name', 'actions')) ){
			$val = esc_html($val);
		}
		//use this
		$val = apply_filters('em_bookings_table_rows_col_'.$col, $val, $EM_Booking, $this, $format, $EM_Booking);
		$val = apply_filters('em_bookings_table_rows_col', $val, $col, $EM_Booking, $this, $format, $EM_Booking); //use the above filter instead for better performance
		//csv/excel escaping
		if( $format == 'csv' || $format == 'xls' || $format == 'xlsx' ){
			$val = self::sanitize_spreadsheet_cell($val);
		}
		return $val;
	}
	
	/**
	 * @param EM_Booking $EM_Booking
	 * @param string|false $show_meta
	 *
	 * @return false|string
	 */
	public function multiple_tickets_col( $EM_Booking, $show_meta = false ){
		ob_start();
		$value = $show_meta === 'ticket_spaces' ? $EM_Booking->get_spaces() : __( 'Multiple Tickets', 'events-manager' );
		if( $this->format !== 'csv' ){
			?>
			<a href="#" class="em-bookings-table-col-tickets-tooltip em-tooltip input " data-content=".em-bookings-table-col-tickets-tooltip-content" data-tippy-interactive="true"><?php echo esc_html($value); ?></a>
			<div class="em-bookings-table-col-tickets-tooltip-content hidden">
				<?php foreach( $EM_Booking->get_tickets_bookings() as $EM_Ticket_Bookings ): ?>
					<a class="em-bookings-table-col-tickets-tooltip-ticket" href="<?php echo esc_url(add_query_arg('ticket_id', $EM_Ticket_Bookings->ticket_id, $EM_Booking->get_event()->get_bookings_url())); ?>" target="_blank">
						<?php
							echo esc_html( $EM_Ticket_Bookings->get_ticket()->name );
							if( $show_meta === 'ticket_price' ){
								echo ' - ' . $EM_Ticket_Bookings->get_ticket()->get_price(true);
							} elseif( $show_meta === 'ticket_total' ){
								$price = $EM_Ticket_Bookings->get_price(false);
								echo ' - ' . $EM_Booking->format_price($price * (1 + $EM_Booking->get_tax_rate(true)));
							} elseif( $show_meta === 'ticket_id' ){
								echo ' - ' . $EM_Ticket_Bookings->get_ticket()->ticket_id;
							} elseif( $show_meta === 'ticket_spaces' ){
								echo ' - ' . $EM_Ticket_Bookings->get_spaces();
							}
						?>
					</a>
					<br>
				<?php endforeach; ?>
			</div>
			<?php
			return ob_get_clean();
		}
		return $value;
	}
	
	public function extra_tablenav( $which ) {
		if ( $which != 'top' ) {
			parent::extra_tablenav( $which );
			return null;
		}
		$EM_Event = $this->get_event();
		$EM_Person = $this->get_person();
		$uid = esc_attr($this->uid);
		$id = esc_attr($this->id);
		?>
		<div class="alignleft actions <?php echo $id; ?>-settings">
			<a href="#" class="<?php echo $id; ?>-export <?php echo $id; ?>-trigger em-icon em-icon-download em-tooltip" id="<?php echo $id; ?>-export-trigger" rel="#<?php echo $uid; ?>-export-modal" aria-label="<?php _e('Export these bookings.','events-manager'); ?>"></a>
			<a href="#" class="<?php echo $id; ?>-settings <?php echo $id; ?>-trigger em-icon em-icon-settings em-tooltip" id="<?php echo $id; ?>-settings-trigger" rel="#<?php echo $uid; ?>-settings-modal" aria-label="<?php _e('Settings','events-manager'); ?>"></a>
		</div>
		<div class="alignleft actions bulkactions">
			<label for="<?php echo $uid; ?>-bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e('Select bulk action'); ?></label>
			<select class="bulk-action-selector" id="<?php echo $uid; ?>-bulk-action-selector-top">
				<option value="-1"><?php esc_html_e('Bulk actions', 'events-manager'); ?></option>
				<?php foreach( $this->get_booking_bulk_actions() as $action => $label ): ?>
					<option value="<?php echo esc_attr($action); ?>"><?php echo esc_html($label); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="button <?php echo $id; ?>-bulk-action"><?php esc_html_e('Apply'); ?></button>
		</div>
		<div class="alignleft actions <?php echo $id; ?>-filters">
			<input name="em_search" type="text" class="inline <?php echo $id; ?>-filter" placeholder="<?php esc_attr_e('search', 'events-manager'); ?> ..." value="<?php if( !empty($_REQUEST['em_search']) ) echo esc_attr($_REQUEST['em_search']);?>">
			<?php if( $EM_Event === false ): ?>
				<select name="scope" class="<?php echo $id; ?>-filter">
					<?php
					foreach ( em_get_scopes() as $key => $value ) {
						$selected = "";
						if ($key == $this->scope)
							$selected = "selected='selected'";
						echo "<option value='".esc_attr($key)."' $selected>".esc_html($value)."</option>  ";
					}
					?>
				</select>
			<?php endif; ?>
			<select name="status" class="<?php echo $id; ?>-filter">
				<?php
				foreach ( $this->statuses as $key => $value ) {
					$selected = "";
					if ($key == $this->status)
						$selected = "selected='selected'";
					echo "<option value='".esc_attr($key)."' $selected>".esc_html($value['label'])."</option>  ";
				}
				?>
			</select>
			<?php do_action('em_bookings_table_output_table_filters', $this); ?>
			<input name="pno" type="hidden" value="1" />
			<input id="post-query-submit" class="button-secondary" type="submit" value="<?php esc_attr_e( 'Filter' ); ?>" />
			<?php /* if( $EM_Event !== false ): ?>
				<?php esc_html_e('Displaying Event','events-manager'); ?> : <?php echo esc_html($EM_Event->event_name); ?>
			<?php elseif( $EM_Person !== false ): ?>
				<?php esc_html_e('Displaying User','events-manager'); echo ' : '.esc_html($EM_Person->get_name()); ?>
			<?php endif; */ ?>
		</div>
		<?php
		parent::extra_tablenav( $which );
	}
	
	public function column_cb( $item ){
		$EM_Booking = $item;
		$html = parent::column_cb( $EM_Booking );
		if( $EM_Booking->booking_status === false && DOING_AJAX && !empty($_REQUEST['row_action']) && $_REQUEST['row_action'] == 'bookings_delete' ){
			// booking deleted, no editing/actions possible
			return $html;
		}
		$actions = $this->get_booking_actions($EM_Booking);
		$edit_url = em_add_get_params($EM_Booking->get_event()->get_bookings_url(), array('booking_id'=>$EM_Booking->booking_id, 'em_ajax'=>null, 'em_obj'=>null));
		ob_start();
		?>
		<button type="button" class="<?php echo esc_attr($this->id) ?>-action-shortcuts em-tooltip-ddm em-clickable" data-tooltip-class="<?php echo esc_attr($this->id) ?>-action-shortcuts-tooltip" title="<?php esc_attr_e('Booking Actions', 'events-manager'); ?>">&centerdot;&centerdot;&centerdot;</button>
		<div class="em-tooltip-ddm-content em-bookings-admin-get-invoice-content">
			<?php echo implode("<br>", $actions); ?>
		</div>
		<a class="em-icon em-icon-edit em-tooltip" href="<?php echo esc_url($edit_url); ?>" aria-label="<?php esc_attr_e('Edit/View', 'events-manager'); ?>"></a>
		<div class="em-loader"></div>
		<?php
		$html .= ob_get_clean();
		return $html;
	}
	
	public function display(){
		do_action('em_bookings_table_header',$this); //won't be overwritten by JS
		$this->prepare_items();
		$EM_Ticket = $this->get_ticket();
		$EM_Event = $this->get_event();
		$EM_Person = $this->get_person();
		$uid = esc_attr($this->uid);
		$id = esc_attr($this->id);
		?>
		<div class="<?php echo $id; ?> em_obj" id="<?php echo $uid; ?>">
			<form class='bookings-filter' method='post' action='' id="<?php echo $uid; ?>-form">
				<?php if( $EM_Event !== false ): ?>
					<input type="hidden" name="event_id" value='<?php echo esc_attr($EM_Event->event_id); ?>' />
				<?php endif; ?>
				<?php if( $EM_Ticket !== false ): ?>
					<input type="hidden" name="ticket_id" value='<?php echo esc_attr($EM_Ticket->ticket_id); ?>' />
				<?php endif; ?>
				<?php if( $EM_Person !== false ): ?>
					<input type="hidden" name="person_id" value='<?php echo esc_attr($EM_Person->ID); ?>' />
				<?php endif; ?>
				<input type="hidden" name="is_public" value="<?php echo ( !empty($_REQUEST['is_public']) || !is_admin() ) ? 1:0; ?>" />
				<input type="hidden" name="pno" value='<?php echo esc_attr($this->page); ?>' />
				<input type="hidden" name="order" value='<?php echo esc_attr($this->order); ?>' />
				<input type="hidden" name="orderby" value='<?php echo esc_attr($this->orderby); ?>' />
				<input type="hidden" name="action" value="em_bookings_table" />
				<input type="hidden" name="cols" value="<?php echo esc_attr(implode(',', $this->cols)); ?>" />
				<input type="hidden" name="limit" value="<?php echo esc_attr($this->limit); ?>" />
				<input type="hidden" name="_emnonce" value="<?php echo ( !empty($_REQUEST['_nonce']) ) ? esc_attr($_REQUEST['_nonce']):wp_create_nonce('em_bookings_table'); ?>" />
				<?php
				parent::display();
				?>
			</form>
			<?php
			$this->output_overlays();
			?>
		</div>
		<?php
		do_action('em_bookings_table_footer',$this); //won't be overwritten by JS
	}
	
	public function single_row( $EM_Booking ) {
		echo '<tr data-id="'. esc_attr($EM_Booking->booking_id) .'">';
		$this->single_row_columns( $EM_Booking );
		echo '</tr>';
	}
}

class EM_WP_Screen {
	public function __get($prop){
		return '';
	}
	
	public static function __callStatic( $string, $args ){
		return '';
	}
	
	public function __call( $string, $args ){
		return '';
	}
}
?>