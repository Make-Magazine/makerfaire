<?php
/**
 * Deals with the booking info for an event
 *
 * @property EM_Booking[] $bookings
 * @property EM_Event $event
 */
class EM_Bookings extends EM_Object implements Iterator, ArrayAccess {
	
	/**
	 * Array of EM_Booking objects for a specific event
	 * @var EM_Booking[]
	 */
	protected $bookings;
	/**
	 * @var EM_Tickets
	 */
	var $tickets;
	/**
	 * @var int
	 */
	var $event_id;
	/**
	 * How many spaces this event has
	 * @var int
	 */
	var $spaces;
	/**
	 * @var bool Flag for Multilingual functionality, to help prevent unnecessary reloading of this object if already 'translated'
	 */
	var $translated;
	/**
	 * If flag is true, a registration will be attempted when booking whether the user is logged in or not. Used in cases such as manual bookings (a Pro feature) and should only be enabled during manipulation by an event admin.
	 * @var bool
	 */
	public static $force_registration;
	/**
	 * If flag is true, bookings and forms will not impose restrictions for roles. Future iterations will remove restrictions on dates, space capacity, etc. This is mainly for use by event admins such as for a manual booking (a Pro feature). 
	 * @var bool
	 */
	public static $disable_restrictions = false;
	
	protected $booked_spaces;
	protected $pending_spaces;
	protected $available_spaces;
	
	/**
	 * Reference to the event object if this object contains bookings of a specific event only
	 * @var EM_Event
	 */
	protected $event;
	
	/**
	 * Creates an EM_Bookings instance, currently accepts an EM_Event object (gets all bookings for that event) or array of any EM_Booking objects, which can be manipulated in bulk with helper functions.
	 * @param EM_Event $event
	 * @return null
	 */
	function __construct( $data = false ){
		if( is_object($data) && get_class($data) == "EM_Event" ){ //Creates a blank bookings object if needed
			$this->event_id = $data->event_id;
			$this->event = $data;
		}elseif( is_array($data) ){
			foreach( $data as $EM_Booking ){
				if( $EM_Booking instanceof EM_Booking ){
					$this->bookings[] = $EM_Booking;
				}
			}
		}
	}
	
	public function __get( $var ){
		if( $var == 'bookings' ){
			return $this->load();
		}elseif( $var == 'event' ){
			return $this->get_event();
		}
		return parent::__get( $var );
	}
	
	public function __set( $var, $val ){
		if( $var == 'bookings' ){
			if( is_array($val) ){
				$this->bookings = $val;
			}else{
				$this->bookings = null;
			}
		}elseif( $var == 'event' && $val instanceof EM_Event ){
			$this->event = $val;
			$this->event_id = $this->event->event_id;
		}
		parent::__set( $var, $val );
	}
	
	/**
	 * Counter-intuitive but __isset works against isset() but for our purpose it's mainly aimed at empty() calls, which also references this function.
	 * We don't expect nor do we want people using isset on things like the bookings property.
	 * Assume every property in EM_Bookings isset() == true and avoid it, only use empty() calls to check if there's anything in that property.
	 * Therefore, we'd return !empty($this->bookings) because if there's bookings, isset() should return true 
	 * @param string $var
	 * @return boolean
	 */
	public function __isset( $prop ){
		//if isset is invoked on $EM_Bookings->bookings then we'll assume it's only set if the bookings property is empty, not if null.
		if( $prop == 'bookings' ){
			return $this->bookings !== null;
		}elseif ($prop == 'event') {
			return !empty($this->event);
		}
		return parent::__isset( $prop );
	}
	
	public function load( $refresh = false ){
		if( $refresh || $this->bookings === null ){
			global $wpdb;
			$bookings = $this->bookings = array();
			if( $this->event_id > 0 ){
				$sql = "SELECT * FROM ". EM_BOOKINGS_TABLE ." WHERE event_id ='{$this->event_id}' ORDER BY booking_date";
				$bookings = $wpdb->get_results($sql, ARRAY_A);
			}
			foreach ($bookings as $booking){
				$this->bookings[] = em_get_booking($booking);
			}
		}
		return apply_filters('em_bookings_load', $this->bookings);
	}
	
	/**
	 * Add a booking into this event (or add spaces if person already booked this). We assume at this point that the booking has already been validated usin $EM_Booking->validate()
	 * @param EM_Booking $EM_Booking
	 * @return boolean
	 */
	function add( $EM_Booking ){
		global $wpdb,$EM_Mailer;
		//Save the booking
		$email = false;
		//set status depending on approval settings
		if( empty($EM_Booking->booking_status) ){ //if status is not set, give 1 or 0 depending on approval settings
			$EM_Booking->booking_status = get_option('dbem_bookings_approval') ? 0:1;
		}
		$result = $EM_Booking->save(false);
		if($result){
			//Success
		    do_action('em_bookings_added', $EM_Booking);
			if( $this->bookings === null ) $this->bookings = array();
			$this->bookings[] = $EM_Booking;
			$email = $EM_Booking->email();
			if( get_option('dbem_bookings_approval') == 1 && $EM_Booking->booking_status == 0){
				$this->feedback_message = get_option('dbem_booking_feedback_pending');
			}else{
				$this->feedback_message = get_option('dbem_booking_feedback');
			}
			if(!$email){
				$EM_Booking->email_not_sent = true;
				$this->feedback_message .= ' '.get_option('dbem_booking_feedback_nomail');
				if( current_user_can('activate_plugins') ){
					if( count($EM_Booking->get_errors()) > 0 ){
						$this->feedback_message .= '<br/><strong>Errors:</strong> (only admins see this message)<br/><ul><li>'. implode('</li><li>', $EM_Booking->get_errors()).'</li></ul>';
					}else{
						$this->feedback_message .= '<br/><strong>No errors returned by mailer</strong> (only admins see this message)';
					}
				}
			}
			return apply_filters('em_bookings_add', true, $EM_Booking);
		}else{
			//Failure
			$this->errors[] = "<strong>".get_option('dbem_booking_feedback_error')."</strong><br />". implode('<br />', $EM_Booking->errors);
		}
		return apply_filters('em_bookings_add', false, $EM_Booking);
	}

	/**
	 * Get POST data and create a booking for each ticket requested. If successful, a booking object is returned, false if not.
	 * @return false|object
	 */
	function add_from_post(){
		$EM_Booking = new EM_booking();
		$result = $EM_Booking->get_post();
		if($result){
			$result = $this->add($EM_Booking);
			if($result){
				$result = $EM_Booking;
			}
			$this->feedback_message = sprintf(__('%s created.','events-manager'),__('Booking','events-manager'));
		}else{
			$this->errors = array_merge($this->errors, $EM_Booking->errors);
		}
		return apply_filters('em_bookings_add_from_post',$result,$EM_Booking,$this);
	}
	
	/**
	 * Gets an initial booking intent object, not saved to DB but containing the minimum information required to make a booking, including required spaces already selected (if set by event owner).
	 * If the booking intent has no value, i.e. no spaces to be booked initially, then the sapces will be 0
	 * @return EM_Booking
	 */
	public function get_intent_default(){
		// calculate minimum number of spaces booked required (i.e. non-optional tickets) and create a booking if there is one, otherwise return null as no booking necessary yet
		$EM_Event = $this->get_event();
		$EM_Booking = new EM_Booking();
		$EM_Booking->booking_status = 10; // booking intent status
		if( $EM_Event->event_id ){
			$EM_Booking->event_id = $EM_Event->event_id;
			$EM_Tickets = $this->get_available_tickets();
			$is_single_ticket = $EM_Tickets->count() == 1;
			foreach( $EM_Tickets as $EM_Ticket ){
				$spaces = !empty($_REQUEST['em_tickets'][$EM_Ticket->ticket_id]['spaces']) ? $_REQUEST['em_tickets'][$EM_Ticket->ticket_id]['spaces']:0;
				$min_spaces = $EM_Ticket->get_spaces_minimum();
				// if ticket spaces defined by post, or if a ticket selection is required (by being only ticket or required)
				if( $spaces > 0 ||  $is_single_ticket || $EM_Ticket->ticket_required ) {
					// make sure we meet the minimum
					$spaces = $min_spaces > $spaces ? $min_spaces : $spaces;
				}
				// impose ticket spaces if required
				if( $spaces > 0 ){
					$EM_Ticket_Bookings = $EM_Booking->get_tickets_bookings()->get_ticket_bookings($EM_Ticket->ticket_id);
					for( $i = 0; $i < $spaces; $i++ ){
						$ticket_booking = array(
							'ticket' => $EM_Ticket,
							'booking' => $EM_Booking,
							'ticket_booking_spaces' => 1,
						);
						$EM_Ticket_Booking = new EM_Ticket_Booking($ticket_booking);
						$EM_Ticket_Booking->calculate_price( true );
						$EM_Ticket_Bookings->tickets_bookings[ $EM_Ticket_Booking->ticket_uuid ] = $EM_Ticket_Booking;
					}
				}
			}
			if( $EM_Booking->get_spaces(true) > 0 ){
				$EM_Booking->get_price();
			}
		}
		return apply_filters('em_bookings_get_intent_default', $EM_Booking);
	}
	
	/**
	 * Smart event locator, saves a database read if possible. Note that if an event doesn't exist, a blank object will be created to prevent duplicates.
	 */
	function get_event(){
		if( $this->event && $this->event->event_id == $this->event_id ){
			return $this->event;
		}
		global $EM_Event;
		if( is_object($EM_Event) && $EM_Event->event_id == $this->event_id ){
			$this->event = $EM_Event;
			return $EM_Event;
		}else{
			if( is_numeric($this->event_id) && $this->event_id > 0 ){
				$this->event = em_get_event($this->event_id);
				return $this->event;
			}elseif( is_array($this->bookings) ){
				foreach($this->bookings as $EM_Booking){
					/* @var $EM_Booking EM_Booking */
					return em_get_event($EM_Booking->event_id);
				}
			}
		}
		return em_get_event($this->event_id);
	}
	
	/**
	 * Retrieve and save the bookings belonging to instance. If called again will return cached version, set $force_reload to true to create a new EM_Tickets object.
	 * @param boolean $force_reload
	 * @return EM_Tickets
	 */
	function get_tickets( $force_reload = false ){
		if( !is_object($this->tickets) || $force_reload ){
			$this->tickets = new EM_Tickets($this->get_event());
			if( get_option('dbem_bookings_tickets_single') && count($this->tickets->tickets) == 1 ){
				//if in single ticket mode, then the event booking cut-off is the ticket end date
		    	$EM_Ticket = $this->tickets->get_first();
		    	$EM_Event = $this->get_event();
		    	//if ticket has cut-off date, that should take precedence as we save the ticket cut-off date/time to the event in single ticket mode
		    	if( !empty($EM_Ticket->ticket_end) ){
		    		//if ticket end dates are set, move to event
		    		$EM_Event->event_rsvp_date = $EM_Ticket->end()->format('Y-m-d');
		    		$EM_Event->event_rsvp_time = $EM_Ticket->end()->format('H:i:00');
		    		if( $EM_Event->is_recurring() && !empty($EM_Ticket->ticket_meta['recurrences']) ){
		    			$EM_Event->recurrence_rsvp_days = $EM_Ticket->ticket_meta['recurrences']['end_days'];		    			
		    		}
		    	}else{
		    		//if no end date is set, use event end date (which will have defaulted to the event start date
		    		if( !$EM_Event->is_recurring() ){
		    			//save if we have a valid rsvp end date
		    			if( $EM_Event->rsvp_end()->valid ){
						    $EM_Ticket->ticket_end = $EM_Event->rsvp_end()->getDateTime();
					    }
		    		}else{
			    		if( !isset($EM_Ticket->ticket_meta['recurrences']['end_days']) ){
			    			//for recurrences, we take the recurrence_rsvp_days and feed it into the ticket meta that'll handle recurrences
			    			$EM_Ticket->ticket_meta['recurrences']['end_days'] = !empty($EM_Event->recurrence_rsvp_days) ? $EM_Event->recurrence_rsvp_days : 0;
			    			if( !isset($EM_Ticket->ticket_meta['recurrences']['end_time']) ){
			    				iF( empty($EM_Event->event_rsvp_time) ){
								    $EM_Ticket->ticket_meta['recurrences']['end_time'] = $EM_Event->start()->getTime();
							    }else{
								    $EM_Ticket->ticket_meta['recurrences']['end_time'] = $EM_Event->event_rsvp_time;
							    }
			    			}
						    $EM_Ticket->ticket_end = $EM_Event->start()->format('Y-m-d') . $EM_Ticket->ticket_meta['recurrences']['end_time'];
			    		}
		    		}
		    	}
			}
		}else{
			$this->tickets->event_id = $this->event_id;
		}
		return apply_filters('em_bookings_get_tickets', $this->tickets, $this);
	}
	
	/**
	 * Returns EM_Tickets object with available tickets
	 * @param boolean $include_member_tickets - if set to true, member-ony tickets will be considered available even if logged out
	 * @return EM_Tickets
	 */
	function get_available_tickets( $include_member_tickets = false ){
		$tickets = array();
		foreach ($this->get_tickets() as $EM_Ticket){
			/* @var $EM_Ticket EM_Ticket */
			if( static::$disable_restrictions || $EM_Ticket->is_available($include_member_tickets) ){
				//within time range
				if( static::$disable_restrictions || $EM_Ticket->get_available_spaces() > 0 ){
					$tickets[] = $EM_Ticket;
				}
			}
		}
		$EM_Tickets = new EM_Tickets($tickets);
		return apply_filters('em_bookings_get_available_tickets', $EM_Tickets, $this);
	}
	
	/**
	 * Deprecated - was never used and therefore is deprecated, will always return an array() and will eventually be removed entirely.
	 * @return array
	 */
	function get_user_list(){
		return array();
	}
	
	/**
	 * Returns a boolean indicating whether this ticket exists in this bookings context.
	 * @return bool 
	 */
	function ticket_exists($ticket_id){
		$EM_Tickets = $this->get_tickets();
		foreach( $EM_Tickets->tickets as $EM_Ticket){
			if($EM_Ticket->ticket_id == $ticket_id){
				return apply_filters('em_bookings_ticket_exists',true, $EM_Ticket, $this);
			}
		}
		$EM_Ticket = new EM_Ticket();
		$EM_Ticket->ticket_id = $ticket_id;
		return apply_filters('em_bookings_ticket_exists',false, $EM_Ticket, $this);
	}
	
	function has_space( $include_member_tickets = false ){
		return count($this->get_available_tickets( $include_member_tickets )->tickets) > 0;
	}
	
	function has_open_time(){
	    $return = false;
	    $EM_Event = $this->get_event();
		if( $EM_Event->event_active_status !== 0 ){
		    if( $EM_Event->rsvp_end()->getTimestamp() > time()){
		    	$return = true;
	        }
		}
	    return $return;
	}
	
	function is_open($include_member_tickets = false){
		//TODO extend booking options
		if( static::$disable_restrictions ){
			$return = true;
		}else{
			$return = $this->has_open_time() && $this->has_space($include_member_tickets);
		}
		return apply_filters('em_bookings_is_open', $return, $this, $include_member_tickets);
	}
	
	/**
	 * Delete bookings on this id
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$booking_ids = $event_ids = array();
		if( !empty($this->bookings) ){
			//get the booking ids tied to this event or preloaded into this object
			foreach( $this->bookings as $EM_Booking ){
				$booking_ids[] = $EM_Booking->booking_id;
			}
			$result_tickets = true;
			$result = true;
			if( count($booking_ids) > 0 ){
				// before deleting, get all the event ids associated with these bookings, in case we need to do any checks on those events via filters
				$event_ids = $wpdb->get_col("SEELCT event_id FROM ". EM_BOOKINGS_TABLE ." WHERE booking_id IN (".implode(',',$booking_ids).");");
				//Delete bookings and ticket bookings
				$result_tickets = $wpdb->query("DELETE FROM ". EM_TICKETS_BOOKINGS_TABLE ." WHERE booking_id IN (".implode(',',$booking_ids).");");
				$result = $wpdb->query("DELETE FROM ".EM_BOOKINGS_TABLE." WHERE booking_id IN (".implode(',',$booking_ids).")");
			}
		}elseif( !empty($this->event_id) ){
			//faster way of deleting bookings for an event circumventing the need to load all bookings if it hasn't been loaded already
			$event_id = absint($this->event_id);
			$event_ids = array($event_id);
			$booking_ids = $wpdb->get_col("SELECT booking_id FROM ".EM_BOOKINGS_TABLE." WHERE event_id = '$event_id'");
			$result_tickets = $wpdb->query("DELETE FROM ". EM_TICKETS_BOOKINGS_TABLE ." WHERE booking_id IN (SELECT booking_id FROM ".EM_BOOKINGS_TABLE." WHERE event_id = '$event_id')");
			$result = $wpdb->query("DELETE FROM ".EM_BOOKINGS_TABLE." WHERE event_id = '$event_id'");
		}else{
			//we have not bookings loaded to delete, nor an event to delete bookings from, so bookings are considered 'deleted' since there's nothing ot delete
			$result = $result_tickets = true;
		}
		do_action('em_bookings_deleted', $result, $booking_ids, $event_ids);
		return apply_filters('em_bookings_delete', $result !== false && $result_tickets !== false, $booking_ids, $this, $event_ids);
	}

	
	/**
	 * Will approve all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function approve( $booking_ids ){
		$this->set_status(1, $booking_ids);
		return false;
	}
	
	/**
	 * Will reject all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function reject( $booking_ids ){
		return $this->set_status(2, $booking_ids);
	}
	
	/**
	 * Will unapprove all supplied booking ids, which must be in the form of a numeric array or a single number.
	 * @param array|int $booking_ids
	 * @return boolean
	 */
	function unapprove( $booking_ids ){
		return $this->set_status(0, $booking_ids);
	}
	
	/**
	 * @param int $status
	 * @param array|int $booking_ids
	 * @param bool $send_email
	 * @param bool $ignore_spaces
	 * @return bool
	 */
	function set_status( $status, $booking_ids, $send_email = true, $ignore_spaces = false ){
		//FIXME status should work with instantiated object
		if( self::array_is_numeric($booking_ids) ){
			//Get all the bookings
			$results = array();
			$mails = array();
			foreach( $booking_ids as $booking_id ){
				$EM_Booking = em_get_booking($booking_id);
				if( !$EM_Booking->can_manage() ){
					$this->feedback_message = __('Bookings %s. Mails Sent.', 'events-manager');
					return false;
				}
				$results[] = $EM_Booking->set_status($status, $send_email, $ignore_spaces);
			}
			if( !in_array('false',$results) ){
				$this->feedback_message = __('Bookings %s. Mails Sent.', 'events-manager');
				return true;
			}else{
				//TODO Better error handling needed if some bookings fail approval/failure
				$this->feedback_message = __('An error occurred.', 'events-manager');
				return false;
			}
		}elseif( is_numeric($booking_ids) || is_object($booking_ids) ){
			$EM_Booking = ( $booking_ids instanceof EM_Booking ) ? $booking_ids : em_get_booking($booking_ids);
			$result = $EM_Booking->set_status($status);
			$this->feedback_message = $EM_Booking->feedback_message;
			return $result;
		}
		return false;	
	}
	

	/**
	 * Get the total number of spaces this event has. This will show the lower value of event global spaces limit or total ticket spaces. Setting $force_refresh to true will recheck spaces, even if previously done so.
	 * @param boolean $force_refresh
	 * @return int
	 */
	function get_spaces( $force_refresh=false ){
		if ( $this->get_event()->event_active_status === 0 ) {
			$this->spaces = 0;
		} else {
			if ( $force_refresh || $this->spaces == 0 ) {
				$this->spaces = $this->get_tickets()->get_spaces();
			}
			//check overall events cap
			if ( ! empty( $this->get_event()->event_spaces ) && $this->get_event()->event_spaces < $this->spaces ) {
				$this->spaces = $this->get_event()->event_spaces;
			}
		}
		return apply_filters('em_booking_get_spaces',$this->spaces,$this);
	}
	
	/**
	 * Returns number of available spaces for this event. If approval of bookings is on, will include pending bookings depending on em option.
	 * @return int
	 */
	function get_available_spaces( $force_refresh = false ){
		if ( $this->get_event()->event_active_status === 0 ) {
			$available_spaces = 0;
		}else{
			$spaces = $this->get_spaces($force_refresh);
			$available_spaces = $spaces - $this->get_booked_spaces($force_refresh);
			if( get_option('dbem_bookings_approval_reserved') ){ //deduct reserved/pending spaces from available spaces
				$available_spaces -= $this->get_pending_spaces($force_refresh);
			}
		}
		// @deprecated use em_bookings_get_available_spaces instead
		$available_spaces = apply_filters('em_booking_get_available_spaces', $available_spaces, $this);
		return apply_filters('em_bookings_get_available_spaces', $available_spaces, $this, $force_refresh);
	}

	/**
	 * Returns number of booked spaces for this event. If approval of bookings is on, will return number of booked confirmed spaces.
	 * @return int
	 */
	function get_booked_spaces($force_refresh = false){
		global $wpdb;
		if( $this->booked_spaces === null || $force_refresh ){
			$status_cond = !get_option('dbem_bookings_approval') ? 'booking_status IN (0,1)' : 'booking_status = 1';
			$sql = 'SELECT SUM(booking_spaces) FROM '.EM_BOOKINGS_TABLE. " WHERE $status_cond AND event_id=".absint($this->event_id);
			$booked_spaces = $wpdb->get_var($sql);
			$this->booked_spaces = $booked_spaces > 0 ? $booked_spaces : 0;
		}
		return apply_filters('em_bookings_get_booked_spaces', $this->booked_spaces, $this, $force_refresh);
	}
	
	/**
	 * Gets number of pending spaces awaiting approval. Will return 0 if booking approval is not enabled.
	 * @return int
	 */
	function get_pending_spaces( $force_refresh = false ){
		if( get_option('dbem_bookings_approval') == 0 ){
			return apply_filters('em_bookings_get_pending_spaces', 0, $this);
		}
		global $wpdb;
		if( $this->pending_spaces === null || $force_refresh ){
			$sql = 'SELECT SUM(booking_spaces) FROM '.EM_BOOKINGS_TABLE. ' WHERE booking_status=0 AND event_id='.absint($this->event_id);
			$pending_spaces = $wpdb->get_var($sql);
			$this->pending_spaces = $pending_spaces > 0 ? $pending_spaces : 0;
		}
		return apply_filters('em_bookings_get_pending_spaces', $this->pending_spaces, $this, $force_refresh);
	}
	
	/**
	 * Gets booking objects (not spaces). If booking approval is enabled, only the number of approved bookings will be shown.
	 * @param boolean $all_bookings If set to true, then all bookings with any status is returned
	 * @return EM_Bookings
	 */
	function get_bookings( $all_bookings = false ){
		$confirmed = array();
		foreach ( $this->load() as $EM_Booking ){
			if( $EM_Booking->booking_status == 1 || (get_option('dbem_bookings_approval') == 0 && $EM_Booking->booking_status == 0) || $all_bookings ){
				$confirmed[] = $EM_Booking;
			}
		}
		$EM_Bookings = new EM_Bookings($confirmed);
		return $EM_Bookings;		
	}
	
	/**
	 * Get pending bookings. If booking approval is disabled, will return no bookings. 
	 * @return EM_Bookings
	 */
	function get_pending_bookings(){
		if( get_option('dbem_bookings_approval') == 0 ){
			return new EM_Bookings();
		}
		$pending = array();
		foreach ( $this->load() as $EM_Booking ){
			if($EM_Booking->booking_status == 0){
				$pending[] = $EM_Booking;
			}
		}
		$EM_Bookings = new EM_Bookings($pending);
		return $EM_Bookings;	
	}	
	
	/**
	 * Get rejected bookings. If booking approval is disabled, will return no bookings. 
	 * @return array EM_Bookings
	 */
	function get_rejected_bookings(){
		$rejected = array();
		foreach ( $this->load() as $EM_Booking ){
			if($EM_Booking->booking_status == 2){
				$rejected[] = $EM_Booking;
			}
		}
		$EM_Bookings = new EM_Bookings($rejected);
		return $EM_Bookings;
	}	
	
	/**
	 * Get cancelled bookings. 
	 * @return array EM_Booking
	 */
	function get_cancelled_bookings(){
		$cancelled = array();
		foreach ( $this->load() as $EM_Booking ){
			if($EM_Booking->booking_status == 3){
				$cancelled[] = $EM_Booking;
			}
		}
		$EM_Bookings = new EM_Bookings($cancelled);
		return $EM_Bookings;
	}
	
	/**
	 * Checks if a person with similar details has booked for this before
	 * @param $person_id
	 * @return EM_Booking
	 */
	function find_previous_booking($EM_Booking){
		//First see if we have a similar person on record that's making this booking
		$EM_Booking->person->load_similar();
		//If person exists on record, see if they've booked this event before, if so return the booking.
		if( is_numeric($EM_Booking->person->ID) && $EM_Booking->person->ID > 0 ){
			$EM_Booking->person_id = $EM_Booking->person->ID;
			foreach ($this->load() as $booking){
				if( $booking->person_id == $EM_Booking->person->ID ){
					return $booking;
				}
			}
		}
		return false;
	}
	
	/**
	 * Checks to see if user has a booking for this event
	 * @param int $user_id
	 */
	function has_booking( $user_id = false ){
		if( $user_id === false ){
			$user_id = get_current_user_id();
		}
		if( is_numeric($user_id) && $user_id > 0 ){
			global $wpdb;
			// get the first booking ID available and return that
			$sql = $wpdb->prepare('SELECT booking_id FROM '.EM_BOOKINGS_TABLE.' WHERE event_id = %d AND person_id = %d AND booking_status NOT IN (2,3)', $this->event_id, $user_id);
			$booking_id = $wpdb->get_var($sql);
			if( (int) $booking_id > 0 ){
				$EM_Booking = em_get_booking($booking_id);
				return apply_filters('em_bookings_has_booking', $EM_Booking, $this);
			}
		}
		return apply_filters('em_bookings_has_booking', false, $this);
	}
	
	/**
	 * Get bookings that match the array of arguments passed.
	 * @return array 
	 * @static
	 */
	public static function get( $args = array(), $count = false ){
		global $wpdb,$current_user;
		$bookings_table = EM_BOOKINGS_TABLE;
		$events_table = EM_EVENTS_TABLE;
		$locations_table = EM_LOCATIONS_TABLE;
		
		//Quick version, we can accept an array of IDs, which is easy to retrieve
		if( self::array_is_numeric($args) ){ //Array of numbers, assume they are event IDs to retreive
			//We can just get all the events here and return them
			$sql = "
				SELECT * FROM $bookings_table b 
				LEFT JOIN $events_table e ON e.event_id=b.event_id 
				WHERE booking_id=".implode(" OR booking_id=", $args);
			$results = $wpdb->get_results(apply_filters('em_bookings_get_sql',$sql),ARRAY_A);
			$bookings = array();
			foreach($results as $result){
				$bookings[] = em_get_booking($result);
			}
			return $bookings; //We return all the bookings matched as an EM_Booking array. 
		}
		
		//We assume it's either an empty array or array of search arguments to merge with defaults			
		$args = self::get_default_search($args);
		$limit = ( $args['limit'] && is_numeric($args['limit'])) ? "LIMIT {$args['limit']}" : '';
		$offset = ( $limit != "" && is_numeric($args['offset']) ) ? "OFFSET {$args['offset']}" : '';
		
		//Get the default conditions
		$conditions = self::build_sql_conditions($args);
		//Put it all together
		$where = ( count($conditions) > 0 ) ? " WHERE " . implode ( " AND ", $conditions ):'';
		
		//Get ordering instructions
		$accepted_fields = self::get_sql_accepted_fields();
		$orderby = self::build_sql_orderby($args, $accepted_fields['orderby']);
		//Now, build orderby sql
		$orderby_sql = ( count($orderby) > 0 ) ? 'ORDER BY '. implode(', ', $orderby) : 'ORDER BY booking_date';
		//Selectors
		if( $count ){
			$selectors = 'COUNT(*)';
		}elseif( is_array($args['array']) ){
			$selectors = implode(',', $args['array']);
		}else{
			$selectors = '*';
		}
		
		//check if we need to join a location table for this search, which is necessary if any location-specific are supplied, or if certain arguments such as orderby contain location fields
		$optional_joins = array();
		$required_joins = array();
		foreach( $accepted_fields['tables'] as $table_name => $table_data ){
			$join_table = false;
			if( $table_name == EM_EVENTS_TABLE ) $join_table = true; // temporary whilst we work out some kinks with metadata
			if( in_array($table_name, $required_joins) ){
				$join_table = true;
			}
			foreach( $table_data['args'] as $arg ) {
				$ignore_arg_values = array();
				if( isset($table_data['ignore_args'][$arg]) ){
					$ignore_arg_values = is_array($table_data['ignore_args'][$arg]) ? $table_data['ignore_args'][$arg] : array($table_data['ignore_args'][$arg]);
				}
				if ( !empty($args[$arg]) && !in_array($args[$arg], $ignore_arg_values) ) {
					$join_table = true;
				}elseif( !empty($table_data['empty_args'][$arg]) && isset($args[$arg]) ) {
					// it could either be an array of 'legit' empty values vs any empty value meaning this table-specific arg isn't used, therefore no join needed
					if( is_array($table_data['empty_args'][$arg]) ){
						$join_table = in_array($args[$arg], $table_data['empty_args'][$arg]);
					}elseif( $args[$arg] !== $table_data['empty_args'][$arg] ){
						$join_table = true;
					}
				}
				unset($ignore_arg_values);
			}
			//check ordering and grouping arguments for precense of location fields requiring a join
			if( !$join_table ){
				foreach( array('groupby', 'orderby', 'groupby_orderby') as $arg ){
					if( !is_array($args[$arg]) ) continue; //ignore this argument if set to false
					//we assume all these arguments are now array thanks to self::get_search_defaults() cleaning it up
					foreach( $args[$arg] as $field_name ){
						if( !empty($accepted_fields['tables'][$table_name]['fields']) && in_array($field_name, $accepted_fields['tables'][$table_name]['fields']) ){
							$join_table = true;
							break; //we join, no need to keep searching
						}elseif( !empty($table_data['orderby_extras'][$field_name]) ){
							// account for meta joins, which requires a cheeky condition insertion here instead
							$optional_joins[$table_name] = $wpdb->prepare($table_data['join'], $table_data['orderby_extras'][$field_name]);
							$join_table = true;
							break; //we join, no need to keep searching
						}
					}
				}
			}
			if( $join_table ){
				// check if there's any other required joins
				if( !empty($table_data['requires']) ) {
					$required_joins[] = $table_data['requires'];
				}
				if( empty($optional_joins[$table_name]) ) {
					$optional_joins[ $table_name ] = $table_data['join'];
				}
			}
		}
		//plugins can override this optional joining behaviour here in case they add custom WHERE conditions or something like that
		$optional_joins = apply_filters('em_bookings_get_optional_joins', array_reverse($optional_joins), $args, $accepted_fields);
		
		//Create the SQL statement and execute
		$sql = apply_filters('em_bookings_get_sql',"
			SELECT $selectors FROM $bookings_table
			". implode("\r\n", $optional_joins)."
			$where
			$orderby_sql
			$limit $offset
		", $args);
		
		//If we're only counting results, return the number of results
		if( $count ){
			return apply_filters('em_bookings_get_count', $wpdb->get_var($sql), $args);		
		}
		$results = $wpdb->get_results($sql, ARRAY_A);

		//If we want results directly in an array, why not have a shortcut here?
		if( !empty($args['array']) ){
			return $results;
		}
		
		//Make returned results EM_Booking objects
		$results = (is_array($results)) ? $results:array();
		$bookings = array();
		foreach ( $results as $booking ){
			$bookings[] = em_get_booking($booking);
		}
		$EM_Bookings = new EM_Bookings($bookings);
		return apply_filters('em_bookings_get', $EM_Bookings);
	}
	
	public static function count( $args = array() ){
		return self::get($args, true);
	}
	

	//List of patients in the patient database, that a user can choose and go on to edit any previous treatment data, or add a new admission.
	//Deprecated
	//@todo remove in 6.0
	function export_csv() {
		global $EM_Event;
		if($EM_Event->event_id != $this->event_id ){
			$event = $this->get_event();
			$event_name = $event->name;
		}else{
			$event_name = $EM_Event->name;
		}
		// The name of the file on the user's pc
		$file_name = sanitize_title($event_name). "-bookings.csv";
		
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: Attachment; filename=$file_name");
		em_locate_template('templates/csv-event-bookings.php', true);
		exit();
	}
	
	static function enqueue_js(){
        if( !defined('EM_BOOKING_JS_LOADED') ){ //request loading of JS file in footer of page load
        	add_action('wp_footer','EM_Bookings::em_booking_js_footer', 20);
        	add_action('admin_footer','EM_Bookings::em_booking_js_footer', 20);
        	define('EM_BOOKING_JS_LOADED',true);
        }
	}
	
	static function em_booking_js_footer(){
		?>		
		<script type="text/javascript">
			<?php
			$include_path = dirname(dirname(__FILE__)); //get path to parent directory
			include($include_path.'/includes/js/bookingsform.js');
			?>
			jQuery(document).ready( function($){	
				<?php
					//we call the segmented JS files and include them here
					do_action('em_gateway_js'); //deprecated use em_booking_js below instead
					do_action('em_booking_js'); //use this instead
				?>							
			});
			<?php
			do_action('em_booking_js_footer');
			?>
		</script>
		<?php
	}
	
	/**
	 * Checks whether a booking being made should register user information as a booking from another user whilst an admin is logged in
	 * @return boolean
	 */
	public static function is_registration_forced(){
		return ( defined('EM_FORCE_REGISTRATION') || self::$force_registration );
	}
	
	public static function get_sql_accepted_fields(){
		$EM_Booking = new EM_Booking();
		$EM_Event = new EM_Event();
		$EM_Location = (new EM_Location())->get_fields(true);
		$accepted_fields = array(
			'tables' => array(
				/* WIP - Meta will work like this to allow re-ordring by custom data, paused temporarily to sort out inconcsistencies with stored data in WP Users vs. meta for guests
				// see event table key for docs on each array item
				EM_TICKETS_BOOKINGS_META_TABLE => array(
					'args' => array(),
					'join' => "LEFT JOIN ".EM_TICKETS_BOOKINGS_META_TABLE." ON ".EM_TICKETS_BOOKINGS_META_TABLE.".ticket_booking_id=".EM_TICKETS_BOOKINGS_TABLE.".ticket_booking_id",
					'requires' => EM_TICKETS_BOOKINGS_TABLE,
					// see booking meta, could add custom attendee data the same way
				),
				EM_TICKETS_TABLE => array(
					'args' => array('ticket_id'),
					'join' => "LEFT JOIN ".EM_TICKETS_TABLE." ON ".EM_TICKETS_TABLE.".event_id=".EM_EVENTS_TABLE.".event_id",
					'requires' => EM_TICKETS_BOOKINGS_TABLE,
					'fields' => (new EM_Ticket())->get_fields(true),
				),
				EM_TICKETS_BOOKINGS_TABLE => array(
					'args' => array(),
					'join' => "LEFT JOIN ".EM_TICKETS_BOOKINGS_TABLE." ON ". EM_BOOKINGS_TABLE.".booking_id=".EM_BOOKINGS_META_TABLE.".booking_id",
					'fields' => (new EM_Ticket_Booking())->get_fields(true),
				),
				EM_BOOKINGS_META_TABLE => array(
					'args' => array(),
					'join' => "LEFT JOIN ".EM_BOOKINGS_META_TABLE." ON ".EM_BOOKINGS_TABLE.".booking_id=".EM_BOOKINGS_META_TABLE.".booking_id AND meta_key=%s",
					'fields' => array(
						'user_name' => 'meta_value',
						'user_email' => 'meta_value',
					),
					'orderby_extras' => array(
						'user_name' => '_registration_user_name',
						'user_email' => '_registration_user_email',
					),
				),
				/**/
				EM_LOCATIONS_TABLE => array(
					'args' => array('town', 'state', 'country', 'region', 'near', 'geo', 'search', 'location_status'), // if we have these we need to join
					'join' => "LEFT JOIN ".EM_LOCATIONS_TABLE." ON ".EM_LOCATIONS_TABLE.".location_id=".EM_EVENTS_TABLE.".location_id",
					'requires' => EM_EVENTS_TABLE,
					'fields' => (new EM_Location())->get_fields(true),
				),
				EM_EVENTS_TABLE => array(
					// accepted args that would require events table to be joined
					'args' => array('scope', 'timezone', 'status', 'recurring', 'private', 'private_only', 'post_id', 'mode', 'has_location', 'no_location', 'event_location_type', 'has_event_location', 'category', 'tag', 'event_status','recurrence', 'recurrences', 'month', 'year', 'owner', 'language'),
					// any args that may have a specific empty value that still means it's 'set', could also be an array of empty value types
					'empty_args' => array('status' => false ),
					// any args here that match the value or that within the array of values will be considered as ignored, for example scope 'all' doesn't actually require any SQL conditions
					'ignore_args' => array('scope' => 'all'),
					// the JOIN SQL required to join this table
					'join' => "LEFT JOIN ".EM_EVENTS_TABLE." ON ".EM_BOOKINGS_TABLE.".event_id=".EM_EVENTS_TABLE.".event_id",
					// field names with shortcut field name as key
					'fields' => (new EM_Event())->get_fields(true),
					// name of table (or array of tables) this join would also require, if joining based on a dependent table that links it to bookings
					'requires' => null,
				),
			),
			'prefixes' => array(
				EM_EVENTS_TABLE => 'event',
				EM_LOCATIONS_TABLE => 'location',
				EM_TICKETS_TABLE => 'ticket',
				EM_BOOKINGS_META_TABLE => 'booking_meta',
				EM_TICKETS_BOOKINGS_TABLE => 'ticket_booking',
				EM_TICKETS_BOOKINGS_META_TABLE => 'ticket_booking_meta',
			),
			'orderby' => array_combine(array_keys($EM_Booking->fields), array_keys($EM_Booking->fields)), // maps field names to absolute DB field names
		);
		$accepted_fields['orderby']['booking_date'] = 'booking_date'; // not covered in fields array
		// reserved field names that the main object here has right to, other tables use full or prefix with type to map orderby fields
		$reserved_field_names = array(
			'spaces', 'post_id', 'blog_id', 'post_content', 'content', 'slug', 'name', 'owner', 'status', 'private', 'language', 'parent', 'translation', 'attributes', 'date_created', 'date_modified',
		);
		// go through each table and add to accepted fields. Go in reverse as preference is from least to greatest (for joining dependences optimally)
		foreach( $accepted_fields['tables'] as $table_name => $table_data ){
			if( !empty($table_data['fields']) ){
				foreach( $table_data['fields'] as $field_key => $field_name ){
					if( in_array($field_key, $reserved_field_names) || !empty($accepted_fields['orderby'][$field_name]) ){
						$prefix = $accepted_fields['prefixes'][$table_name];
						$field_name_unique = in_array($field_name, $reserved_field_names) ? $prefix . '_' . $field_name : $field_name;
						$accepted_fields['orderby'][$field_name_unique] = $table_name.'.'.$field_name;
					}else{
						if( !empty($table_data['orderby_extras'][$field_key]) ){
							// special meta table, so we map the key even though it's not a real field
							if( empty($accepted_fields['orderby'][$field_key]) ){
								// duplicates will just be ignored, at this point overriders should be more specific
								$accepted_fields['orderby'][$field_key] = $table_name.'.'.$field_name;
							}
						}else{
							$accepted_fields['orderby'][$field_name] = $table_name.'.'.$field_name;
						}
					}
				}
			}
		}
		return $accepted_fields;
		$event_fields = $EM_Event->get_fields(true);
		$location_fields = $EM_Location->get_fields(true); //will contain location-specific fields, not ambiguous ones
		// location fields will clash, so they may need special treatment
		foreach( $location_fields as $field_key => $field_name ){
			if( in_array($field_name, $event_fields) ){
				// event takes priority
				unset($location_fields[$field_key]);
			}
			if( empty($event_fields[$field_name]) ){ // map it in entire long-handed name
				$location_fields[$field_name] = $field_name;
			}else{
				$location_fields['location_'.$field_name] = EM_LOCATIONS_TABLE.'.'.$field_name;
			}
		}
		$accepted_fields['tables'] = array(
			EM_BOOKINGS_TABLE => $EM_Booking->get_fields(true),
			EM_EVENTS_TABLE => $event_fields,
			EM_LOCATIONS_TABLE => $location_fields,
		);
		$accepted_fields['orderby']['date'] = 'booking_date';
		if( get_option('dbem_locations_enabled') ){
			$accepted_fields = array_merge($location_fields, $event_fields, $accepted_fields);
		}else{
			//if locations disabled then we don't accept location-specific fields
			$accepted_fields = array_merge($event_fields, $accepted_fields);
		}
	}
	
	public static function build_sql_groupby_orderby( $args, $accepted_fields, $default_order = 'ASC' ) {
		return parent::build_sql_groupby_orderby( $args, $accepted_fields, $default_order ); // TODO: Change the autogenerated stub
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_orderby()
	 */
	public static function build_sql_orderby( $args, $accepted_fields, $default_order = 'ASC' ){
		return apply_filters( 'em_bookings_build_sql_orderby', parent::build_sql_orderby($args, $accepted_fields, get_option('dbem_bookings_default_order','booking_date')), $args, $accepted_fields, $default_order );
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_conditions()
	 */
	public static function build_sql_conditions( $args = array() ){
		global $wpdb;
		$conditions = apply_filters( 'em_bookings_build_sql_conditions', parent::build_sql_conditions($args), $args );
		if( is_numeric($args['status']) ){
			$conditions['status'] = 'booking_status='.$args['status'];
		}elseif( self::array_is_numeric($args['status']) && count($args['status']) > 0 ){
			$conditions['status'] = 'booking_status IN ('.implode(',',$args['status']).')';
		}elseif( !is_array($args['status']) && preg_match('/^([0-9],?)+$/', $args['status']) ){
			$conditions['status'] = 'booking_status IN ('.$args['status'].')';
		}
		// RSVP status
		if( $args['rsvp_status'] !== false ){
			if( is_numeric($args['rsvp_status']) ){
				$conditions['rsvp_status'] = 'booking_rsvp_status='.$args['rsvp_status'];
			}elseif( $args['rsvp_status'] === null || $args['rsvp_status'] === 'null' ){
				$conditions['rsvp_status'] = 'booking_rsvp_status IS NULL';
			}elseif( self::array_is_numeric($args['rsvp_status']) && count($args['rsvp_status']) > 0 ){
				$conditions['rsvp_status'] = 'booking_status IN ('.implode(',',$args['rsvp_status']).')';
			}elseif( !is_array($args['rsvp_status']) && preg_match('/^(([0-9]|null),?)+$/i', $args['rsvp_status']) ){
				$conditions['rsvp_status'] = 'booking_rsvp_status IN ('.$args['rsvp_status'].')';
			}
		}
		// person/owner
		if( is_numeric($args['person']) ){
			$conditions['person'] = EM_BOOKINGS_TABLE.'.person_id='.$args['person'];
		}
		if( EM_MS_GLOBAL && !empty($args['blog']) && is_numeric($args['blog']) ){
			if( is_main_site($args['blog']) ){
				$conditions['blog'] = "(".EM_EVENTS_TABLE.".blog_id={$args['blog']} OR ".EM_EVENTS_TABLE.".blog_id IS NULL)";
			}else{
				$conditions['blog'] = "(".EM_EVENTS_TABLE.".blog_id={$args['blog']})";
			}
		}
		if( empty($conditions['event']) && $args['event'] === false ){
			$conditions['event'] = EM_BOOKINGS_TABLE.'.event_id != 0';
		}
		if( !empty($args['search']) ){
			$conditions['search'] = $wpdb->prepare(EM_BOOKINGS_TABLE.'.person_id IN (SELECT user_id FROM '.$wpdb->usermeta ." WHERE meta_value LIKE %s)", '%'.$args['search'].'%');
			$conditions['search'] .= ' OR ' . $wpdb->prepare(EM_BOOKINGS_TABLE.'.booking_id IN (SELECT booking_id FROM '.EM_BOOKINGS_META_TABLE." WHERE meta_value LIKE %s)", '%'.$args['search'].'%');
			$conditions['search'] = '('.$conditions['search'].')';
		}
		if( is_numeric($args['ticket_id']) ){
			$EM_Ticket = new EM_Ticket($args['ticket_id']);
			if( $EM_Ticket->can_manage() ){
				$conditions['ticket'] = EM_BOOKINGS_TABLE.'.booking_id IN (SELECT booking_id FROM '.EM_TICKETS_BOOKINGS_TABLE." WHERE ticket_id='{$args['ticket_id']}')";
			}
		}
		return apply_filters('em_bookings_build_sql_conditions', $conditions, $args);
	}
	
	/* 
	 * Adds custom Events search defaults
	 * @param array $array_or_defaults may be the array to override defaults
	 * @param array $array
	 * @return array
	 * @uses EM_Object#get_default_search()
	 */
	public static function get_default_search( $array_or_defaults = array(), $array = array() ){
		$defaults = array(
			'status' => false,
			'rsvp_status' => false,
			'person' => true, //to add later, search by person's bookings...
			'blog' => get_current_blog_id(),
			'ticket_id' => false,
			'array' => false //returns an array of results if true, if an array or text it's assumed an array of specific table fields or single field name requested 
		);
		//sort out whether defaults were supplied or just the array of search values
		if( empty($array) ){
			$array = $array_or_defaults;
		}else{
			$defaults = array_merge($defaults, $array_or_defaults);
		}
		//clean up array value
		if( !empty($array['array']) ){
			$EM_Booking = new EM_Booking();
			if( is_array($array['array']) ){
				$clean_arg = array();
				foreach( $array['array'] as $k => $field ){
					if( array_key_exists($field, $EM_Booking->fields) ){
						$clean_arg[] = $field;
					}
				}
				$array['array'] = !empty($clean_arg) ? $clean_arg : true; //if invalid args given, just return all fields
			}elseif( is_string($array['array']) && array_key_exists($array['array'], $EM_Booking->fields) ){
				$array['array'] = array($array['array']);
			}else{
				$array['array'] = true;
			}
		}else{
			$array['array'] = false;
		}
		//figure out default owning permissions
		if( !current_user_can('edit_others_events') ){
			$defaults['owner'] = get_current_user_id();
		}else{
			$defaults['owner'] = false;
		}
		if( EM_MS_GLOBAL && !is_admin() ){
			if( empty($array['blog']) && is_main_site() && get_site_option('dbem_ms_global_events') ){
			    $array['blog'] = false;
			}
		}
		return apply_filters('em_bookings_get_default_search', parent::get_default_search($defaults,$array), $array, $defaults);
	}

	//Iterator Implementation - if we iterate this object, we automatically invoke the load() function first
	//and load up all bookings to go through from the database.
	#[\ReturnTypeWillChange]
    public function rewind(){
    	$this->load();
        reset($this->bookings);
    }
	#[\ReturnTypeWillChange]
    public function current(){
    	$this->load();
        $var = current($this->bookings);
        return $var;
    }
	#[\ReturnTypeWillChange]
    public function key(){
    	$this->load();
        $var = key($this->bookings);
        return $var;
    }
	#[\ReturnTypeWillChange]
    public function next(){
    	$this->load();
        $var = next($this->bookings);
        return $var;
    }
	#[\ReturnTypeWillChange]
    public function valid(){
    	$this->load();
        $key = key($this->bookings);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
	
	// ArrayAccess Implementation
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @param $value
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->bookings[] = $value;
		} else {
			$this->bookings[$offset] = $value;
		}
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->bookings[$offset]);
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->bookings[$offset]);
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return EM_Ticket_Bookings|null
	 */
	public function offsetGet($offset) {
		return isset($this->bookings[$offset]) ? $this->bookings[$offset] : null;
	}
}
?>