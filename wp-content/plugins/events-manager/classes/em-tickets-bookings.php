<?php
/**
 * Deals with the each ticket booked in a single booking.
 * Each ticket is grouped by EM_Ticket_Bookings, which is stored as an array in the tickets_bookings object.
 *
 * You can access/add/unset the array of EM_Ticket_Bookings and its sub array of EM_Ticket_Booking objects in a few ways, with example ticket ID # 34884:
 *
 * Access the EM_Ticket_Bookings of a ticket:
 * $EM_Tickets_Bookings[34884]
 * $EM_Tickets_Bookings->tickets_bookings[34884]
 *
 * Add a new EM_Ticket_Bookings for a ticket:
 * $EM_Tickets_Bookings[1234] = new EM_Tickets_Bookings(...)
 * $EM_Tickets_Bookings->tickets_bookings[1234] = new EM_Tickets_Bookings(...)
 *
 * Add a new EM_Ticket_Booking object to existing EM_Ticket_Bookings objects
 * $EM_Tickets_Bookings[34884]['uuid'] = new EM_Ticket_Booking(...); // text key - should be a uuid
 * $EM_Tickets_Bookings->tickets_bookings[34884]['uuid'] = new EM_Ticket_Booking(...);
 * $EM_Tickets_Bookings->tickets_bookings[34884]->tickets_bookings['uuid'] = new EM_Ticket_Booking(...);
 *
 * Unset works the same way:
 * unset($EM_Tickets_Bookings[35280]);
 * unset($EM_Tickets_Bookings->tickets_bookings[34884]);
 * etc.
 *
 * @author marcus
 *
 */
class EM_Tickets_Bookings extends EM_Object implements Iterator, Countable, ArrayAccess {
	
	/**
	 * Array of EM_Ticket_Booking objects for a specific event
	 * @var EM_Ticket_Bookings[]
	 */
	public $tickets_bookings = array();
	protected $tickets_bookings_loaded;
	/**
	 * When adding existing booked tickets via add() with 0 spaces, they get slotted here for deletion during save() so they circumvent validation.
	 * @var array[EM_Ticket_Booking]
	 */
	var $tickets_bookings_deleted = array();
	/**
	 * This object belongs to this booking object
	 * @var EM_Booking
	 */
	protected $booking;
	/**
	 * This object belongs to this booking object
	 * @var EM_Ticket
	 */
	var $spaces;
	var $price;
	/**
	 * Used to prefix any actions/filters on this class, so that extended classes can force their own prefix.
	 * @var string
	 */
	public static $n = 'em_tickets_bookings';
	
	/**
	 * Creates an EM_Tickets instance.
	 * @note This function will eventually require an EM_Booking object. At time of writing, this means versions of Events Manager Pro < 3.0 will break.
	 * @param EM_Booking $EM_Booking
	 */
	function __construct( $EM_Booking = null ){
		if( is_object($EM_Booking) && !empty($EM_Booking->booking_uuid) ){ // all booking objects have a uuid
			$this->booking = $EM_Booking;
		}elseif( is_numeric($EM_Booking) ){
			$this->booking = em_get_booking($EM_Booking);
		}
		$this->get_ticket_bookings();
		do_action( static::$n, $this, $EM_Booking);
	}
	
	public function __get( $shortname ){
		if( $shortname === 'booking_id' ){
			return $this->booking->booking_id;
		} elseif ( $shortname === 'booking' ){
			return $this->booking;
		}
		return parent::__get($shortname);
	}
	
	public function __set( $prop, $val ){
		if( $prop === 'booking' && !empty($val->booking_uuid) ){
			$this->booking = $val;
			$this->tickets_bookings_loaded = false;
			$this->get_ticket_bookings(); // refresh ticket bookings
			return;
		}
		parent::__set( $prop, $val );
	}
	
	/**
	 * Return relevant fields that will be used for storage, excluding things such as event and ticket objects that should get reloaded
	 * @return string[]
	 */
	public function __sleep(){
		$array = array('tickets_bookings','tickets_bookings_loaded');
		return apply_filters('em_tickets_bookings_sleep', $array, $this);
	}
	
	public function get_post( $override_availability = false ){
		if( !empty($_REQUEST['em_tickets']) ){
			foreach( $_REQUEST['em_tickets'] as $ticket_id => $values){
				//make sure ticket exists
				$ticket_id = absint($ticket_id);
				if( !empty($values['spaces']) || $this->booking->booking_id ){ // if spaces booked for first time, editing and spaces are 0 (in case we need to delete anything)
					// get an EM_Ticket_Bookings object, which will be added if non-existent, $EM_Ticket_Bookings is therefore passed by reference.
					$EM_Ticket_Bookings = $this->get_ticket_bookings($ticket_id);
					if( !$EM_Ticket_Bookings->get_post() ){
						$this->add_error($EM_Ticket_Bookings->get_errors());
					}
					// make sure things are recalculated
					$this->price = 0; //so price calculations are reset
					$this->get_spaces(true);
					$this->get_price();
				}
			}
		}
		return apply_filters( static::$n . '_get_post', empty($this->errors), $this, $override_availability );
	}
	
	public function validate( $override_availability = false ){
		if( count($this->tickets_bookings) > 0 ){
			foreach($this->tickets_bookings as $EM_Ticket_Bookings){ /* @var $EM_Ticket_Bookings EM_Ticket_Bookings */
				if ( !$EM_Ticket_Bookings->validate( $override_availability ) ){
					$this->errors = array_merge($this->errors, $EM_Ticket_Bookings->get_errors());
				}
			}
		}
		return apply_filters( static::$n . '_validate', empty($this->errors), $this, $override_availability );
	}
	
	/**
	 * Saves the ticket bookings for this booking into the database, whether a new or existing booking
	 * @return boolean
	 */
	function save(){
		do_action(static::$n . '_save_pre',$this);
		//save/update tickets
		foreach( $this->tickets_bookings as $EM_Ticket_Booking ){
			$result = $EM_Ticket_Booking->save();
			if(!$result){
				$this->errors = array_merge($this->errors, $EM_Ticket_Booking->get_errors());
			}
		}
		//delete old tickets if set to 0 in an update
		foreach($this->tickets_bookings_deleted as $EM_Ticket_Booking ){
			$result = $EM_Ticket_Booking->delete();
			if(!$result){
				$this->errors = array_merge($this->errors, $EM_Ticket_Booking->get_errors());
			}
		}
		//return result
		if( count($this->errors) > 0 ){
			$this->feedback_message = __('There was a problem saving the booking.', 'events-manager');
			$this->errors[] = __('There was a problem saving the booking.', 'events-manager');
			return apply_filters(static::$n . '_save', false, $this);
		}
		return apply_filters(static::$n . '_save', true, $this);
	}
	
	/**
	 * Adds a ticket booking to the object, equivalent of adding directly to the array of tickets_bookings
	 *
	 * @param EM_Ticket_Booking $EM_Ticket_Booking
	 * @return bool
	 */
	function add( $EM_Ticket_Booking ){
		if( $EM_Ticket_Booking instanceof EM_Ticket_Booking ) {
			$this->get_ticket_bookings($EM_Ticket_Booking->ticket_id)->tickets_bookings[$EM_Ticket_Booking->ticket_uuid] = $EM_Ticket_Booking;
			return true;
		}
		return false;
	}
	
	/**
	 * Checks if this set has a specific ticket booked, returning the key of the ticket in the EM_Tickets_Bookings->ticket_bookings array
	 * @param int $ticket_id
	 * @return mixed
	 */
	function has_ticket( $ticket_id ){
		foreach ($this->tickets_bookings as $key => $EM_Ticket_Booking){
			if( $EM_Ticket_Booking->ticket_id == $ticket_id ){
				return apply_filters(static::$n . '_has_ticket',$key,$this);
			}
		}
		return apply_filters(static::$n . '_has_ticket',false,$this);
	}
	
	/**
	 * Smart event locator, saves a database read if possible. 
	 */
	function get_booking(){
		return apply_filters(static::$n . '_get_booking', $this->booking, $this);
	}
	
	/**
	 * Delete all ticket bookings
	 * @return boolean
	 */
	function delete(){
		global $wpdb;
		$result = false;
		if( $this->get_booking()->can_manage() ){
			$result_meta = $wpdb->query("DELETE FROM ".EM_TICKETS_BOOKINGS_META_TABLE." WHERE ticket_booking_id IN (SELECT ticket_booking_id FROM ".EM_TICKETS_BOOKINGS_TABLE." WHERE booking_id='{$this->booking_id}')");
			$result = $wpdb->query("DELETE FROM ".EM_TICKETS_BOOKINGS_TABLE." WHERE booking_id='{$this->booking_id}'");
		}
		return apply_filters(static::$n . '_delete', ($result !== false && $result_meta !== false), $this);
	}
	
	/**
	 * Get the total number of spaces booked in this booking. Seting $force_reset to true will recheck spaces, even if previously done so.
	 * @param unknown_type $force_refresh
	 * @return mixed
	 */
	function get_spaces( $force_refresh = false ){
		if( $force_refresh || $this->spaces == 0 ){
			$spaces = 0;
			foreach( $this->tickets_bookings as $EM_Ticket_Bookings ){
				$spaces += $EM_Ticket_Bookings->get_spaces( $force_refresh );
			}
			$this->spaces = $spaces;
		}
		return apply_filters(static::$n . '_get_spaces',$this->spaces,$this);
	}
	
	/**
	 * Gets the total price for this whole booking by adding up subtotals of booked tickets. Seting $force_reset to true will recheck spaces, even if previously done so.
	 * @param boolean $format
	 * @return float
	 */
	function get_price( $format = false ){
		if( $this->price == 0 ){
			$price = $this->calculate_price( true );
			// deprecated, use the _calculate_price filter instead
			$this->price = apply_filters(static::$n . '_get_price', $price, $this);
		}
		if($format){
			return $this->format_price($this->price);
		}
		return $this->price;
	}
	
	function calculate_price( $force_refresh = false ){
		if( $this->price == null || $force_refresh ){
			$price = 0;
			foreach($this->tickets_bookings as $EM_Ticket_Bookings ){
				$price += $EM_Ticket_Bookings->calculate_price( $force_refresh );
			}
			$this->price = apply_filters(static::$n . '_calculate_price', $price, $this, $force_refresh);
		}
		return $this->price;
	}
	
	/**
	 * Return a specific EM_Ticket_Bookings object if a valid $ticket_id is supplied, or alternatively returns all EM_Ticket_Bookings objects registered to this object.
	 * If when requesting a $ticket_id and no EM_Ticket_Bookings object exists for it within the object, a new blank object is created and appended to the tickets_bookings property, with 0 spaces and 0 price.
	 * @param EM_Ticket|int $ticket
	 * @return EM_Ticket_Bookings|EM_Ticket_Bookings[]
	 */
	function get_ticket_bookings( $ticket = false ){
		$ticket_id = is_object($ticket) ? $ticket->ticket_id : absint($ticket);
		if( !$this->tickets_bookings_loaded && !empty($this->booking->booking_id) ){
			// we could get tickets individually via EM_Ticket_Bookings, but this is one db call vs multiple
			global $wpdb;
			$sql = "SELECT * FROM ". EM_TICKETS_BOOKINGS_TABLE ." WHERE booking_id ='{$this->booking->booking_id}' ORDER BY ticket_booking_id ASC";
			$results = $wpdb->get_results($sql, ARRAY_A);
			//Get tickets belonging to this tickets booking.
			$tickets_bookings = array();
			foreach ($results as $ticket_booking){
				$ticket_booking['booking'] = $this->booking;
				$EM_Ticket_Booking = new EM_Ticket_Booking($ticket_booking);
				if( empty($tickets_bookings[$EM_Ticket_Booking->ticket_id]) ) $tickets_bookings[$EM_Ticket_Booking->ticket_id] = array();
				$tickets_bookings[$EM_Ticket_Booking->ticket_id][]= $EM_Ticket_Booking;
			}
			foreach( $tickets_bookings as $id => $ticket_bookings ){
				$this->tickets_bookings[$id] = new EM_Ticket_Bookings($ticket_bookings);
			}
		}
		$this->tickets_bookings_loaded = true;
		if( $ticket_id ){
			if( empty($this->tickets_bookings[$ticket_id]) ){
				$this->tickets_bookings[$ticket_id] = new EM_Ticket_Bookings( array('ticket_id' => $ticket_id, 'booking' => $this->get_booking() ) );
			}
			return $this->tickets_bookings[$ticket_id];
		}
		return $this->tickets_bookings;
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_conditions()
	 */
	public static function build_sql_conditions( $args = array() ){
		$conditions = parent::build_sql_conditions($args);
		if( is_numeric($args['status']) ){
			$conditions['status'] = 'ticket_status='.$args['status'];
		}
		return apply_filters(static::$n . '_build_sql_conditions', $conditions, $args);
	}
	
	/* Overrides EM_Object method to apply a filter to result
	 * @see wp-content/plugins/events-manager/classes/EM_Object#build_sql_orderby()
	 */
	public static function build_sql_orderby( $args, $accepted_fields, $default_order = 'ASC' ){
		return apply_filters( static::$n . '_build_sql_orderby', parent::build_sql_orderby($args, $accepted_fields, get_option('dbem_events_default_order')), $args, $accepted_fields, $default_order );
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
			'person' => true //to add later, search by person's tickets...
		);	
		//sort out whether defaults were supplied or just the array of search values
		if( empty($array) ){
			$array = $array_or_defaults;
		}else{
			$defaults = array_merge($defaults, $array_or_defaults);
		}
		//specific functionality
		$defaults['owner'] = !current_user_can('manage_others_bookings') ? get_current_user_id():false;
		return apply_filters(static::$n . '_get_default_search', parent::get_default_search($defaults,$array), $array, $defaults);
	}

	//Iterator Implementation
	
	#[\ReturnTypeWillChange]
	/**
	 * @return void
	 */
    public function rewind(){
	    $this->get_ticket_bookings();
        reset($this->tickets_bookings);
    }
	
	#[\ReturnTypeWillChange]
	/**
	 * @return EM_Ticket_Bookings
	 */
    public function current(){
        return current($this->tickets_bookings);
    }
	#[\ReturnTypeWillChange]
	/**
	 * @return int Ticket ID
	 */
    public function key(){
        return key($this->tickets_bookings);
    }
	#[\ReturnTypeWillChange]
	/**
	 * @return EM_Ticket_Bookings
	 */
	public function next(){
        return next($this->tickets_bookings);
    }
	#[\ReturnTypeWillChange]
	public function valid(){
        $key = key($this->tickets_bookings);
        return ($key !== NULL && $key !== FALSE);
    }
    //Countable Implementation
	
	#[\ReturnTypeWillChange]
	/**
	 * @return int
	 */
	public function count(){
		return count($this->tickets_bookings);
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
			$this->tickets_bookings[] = $value;
		} else {
			$this->tickets_bookings[$offset] = $value;
		}
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->tickets_bookings[$offset]);
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->tickets_bookings[$offset]);
	}
	#[\ReturnTypeWillChange]
	/**
	 * @param $offset
	 * @return EM_Ticket_Bookings|null
	 */
	public function offsetGet($offset) {
		return isset($this->tickets_bookings[$offset]) ? $this->tickets_bookings[$offset] : null;
	}
	
	public function __debugInfo(){
		$object = clone($this);
		$object->booking = !empty($this->booking->booking_id) ? 'Booking ID #'.$this->booking->booking_id : 'New Booking - No ID';
		$object->shortnames = 'Removed for export, uncomment from __debugInfo()';
		$object->mime_types = 'Removed for export, uncomment from __debugInfo()';
		if( empty($object->errors) ) $object->errors = false;
		return (Array) $object;
	}
}
?>