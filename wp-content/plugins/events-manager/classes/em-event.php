<?php
use EM_Event_Locations\Event_Location, EM_Event_Locations\Event_Locations;
/**
 * Get an event in a db friendly way, by checking globals, cache and passed variables to avoid extra class instantiations.
 * @param mixed $id can be either a post object, event object, event id or post id
 * @param mixed $search_by default is post_id, otherwise it can be by event_id as well. In multisite global mode, a blog id can be supplied to load events from another blog.
 * @return EM_Event
 */
function em_get_event($id = false, $search_by = 'event_id') {
	global $EM_Event;
	//check if it's not already global so we don't instantiate again
	if( is_object($EM_Event) && get_class($EM_Event) == 'EM_Event' ){
		if( is_object($id) && $EM_Event->post_id == $id->ID ){
			return apply_filters('em_get_event', $EM_Event);
		}elseif( !is_object($id) ){
			if( $search_by == 'event_id' && $EM_Event->event_id == $id ){
				return apply_filters('em_get_event', $EM_Event);
			}elseif( $search_by == 'post_id' && $EM_Event->post_id == $id ){
				return apply_filters('em_get_event', $EM_Event);
			}
		}
	}
	if( is_object($id) && get_class($id) == 'EM_Event' ){
		return apply_filters('em_get_event', $id);
	}elseif( !defined('EM_CACHE') || EM_CACHE ){
		//check the cache first
		$event_id = false;
		if( is_numeric($id) ){
			if( $search_by == 'event_id' ){
				$event_id = absint($id);
			}elseif( $search_by == 'post_id' ){
				$event_id = wp_cache_get($id, 'em_events_ids');
			}
		}elseif( !empty($id->ID) && !empty($id->post_type) && ($id->post_type == EM_POST_TYPE_EVENT || $id->post_type == 'event-recurring') ){
			$event_id = wp_cache_get($id->ID, 'em_events_ids');
		}
		if( $event_id ){
			$event = wp_cache_get($event_id, 'em_events');
			if( is_object($event) && !empty($event->event_id) && $event->event_id){
				return apply_filters('em_get_event', $event);
			}
		}
	}
	//if we get this far, just create a new event
	return apply_filters('em_get_event', new EM_Event($id,$search_by));
}
/**
 * Event Object. This holds all the info pertaining to an event, including location and recurrence info.
 * An event object can be one of three "types" a recurring event, recurrence of a recurring event, or a single event.
 * The single event might be part of a set of recurring events, but if loaded by specific event id then any operations and saves are 
 * specifically done on this event. However, if you edit the recurring group, any changes made to single events are overwritten.
 *
 * @property string $language           Language of the event, shorthand for event_language
 * @property string $translation        Whether or not a event is a translation (i.e. it was translated from an original event), shorthand for event_translation
 * @property int $parent                Event ID of parent event, shorthand for event_parent
 * @property int $id                    The Event ID, case sensitive, shorthand for event_id
 * @property string $slug               Event slug, shorthand for event_slug
 * @property string name                Event name, shorthand for event_name
 * @property int owner                  ID of author/owner, shorthand for event_owner
 * @property int status                 ID of post status, shorthand for event_status
 * @property string $event_start_time   Start time of event
 * @property string $event_end_time     End time of event
 * @property string $event_start_date   Start date of event
 * @property string $event_end_date     End date of event
 * @property string $event_start        The event start date in local time. represented by a mysql DATE format
 * @property string $event_end          The event end date in local time. represented by a mysql DATE format
 * @property string $event_timezone     Timezone representation in PHP string or WP-style UTC offset
 * @property string $event_rsvp_date    Start rsvo date of event
 * @property string $event_rsvp_time    End rsvp time of event
 * @property int $event_active_status   End rsvp time of event
 * @property int $previous_active_status End rsvp time of event
 *
 */
//TODO Can add more recurring functionality such as "also update all future recurring events" or "edit all events" like google calendar does.
//TODO Integrate recurrences into events table
//FIXME If you create a super long recurrence timespan, there could be thousands of events... need an upper limit here.
class EM_Event extends EM_Object{
	/* Field Names */
	public $event_id;
	public $post_id;
	public $event_parent;
	public $event_slug;
	public $event_owner;
	public $event_name;
	/**
	 * The event start time in local time, represented by a mysql TIME format or 00:00:00 default.
	 * Protected so when set in PHP it will reset the EM_Event->start property (EM_DateTime object) so it will have the correct UTC time according to timezone.
	 * @var string
	 */
	protected $event_start_time = '00:00:00';
	/**
	 * The event end time in local time, represented by a mysql TIME format or 00:00:00 default.
	 * Protected so when set in PHP it will reset the EM_Event->end property (EM_DateTime object) so it will have the correct UTC time according to timezone.
	 * @var string
	 */
	protected $event_end_time = '00:00:00';
	/**
	 * The event start date in local time. represented by a mysql DATE format.
	 * Protected so when set in PHP it will reset the EM_Event->start property (EM_DateTime object) so it will have the correct UTC time according to timezone.
	 * @var string
	 */
	protected $event_start_date;
	/**
	 * The event end date in local time. represented by a mysql DATE format.
	 * Protected so when set in PHP it will reset the EM_Event->start property (EM_DateTime object) so it will have the correct UTC time according to timezone.
	 * @var string
	 */
	protected $event_end_date;
	/**
	 * The event start date/time in UTC timezone, represented as a mysql DATETIME value. Protected non-accessible property. 
	 * Use $EM_Event->start() to obtain the date/time via the returned EM_DateTime object.
	 * @var string
	 */
	protected $event_start;
	/**
	 * The event end date/time in UTC timezone, represented as a mysql DATETIME value. Protected non-accessible property.
	 * Use $EM_Event->end() to obtain the date/time via the returned EM_DateTime object.
	 * @var string
	 */
	protected $event_end;
	/**
	 * Whether an event is all day or at specific start/end times. When set to true, event start/end times are assumed to be 00:00:00 and 11:59:59 respectively.
	 * @var boolean
	 */
	public $event_all_day;
	/**
	 * Timezone representation in PHP string or WP-style UTC offset.
	 * @var string
	 */
	protected $event_timezone;
	public $post_content;
	public $event_rsvp = 0;
	protected $event_rsvp_date;
	protected $event_rsvp_time;
	public $event_rsvp_spaces;
	public $event_spaces;
	public $event_private;
	public $location_id;
	/**
	 * Key name of event location type associated to this event.
	 *
	 * Events can have an event-specific location type, such as a url, webinar or another custom type instead of a regular geographical location. If this value is set, then a registered event location type is loaded and relevant saved event meta is used.
	 *
	 * @var string
	 */
	public $event_location_type;
	public $recurrence_id;
	public $event_status;
	protected $event_active_status = 1;
	protected $previous_active_status = 1;
	public $blog_id = 0;
	public $group_id;
	public $event_language;
	public $event_translation = 0;
	/**
	 * Populated with the non-hidden event post custom fields (i.e. not starting with _) 
	 * @var array
	 */
	public $event_attributes = array();
	/* Recurring Specific Values */
	public $recurrence = 0;
	public $recurrence_interval;
	public $recurrence_freq;
	public $recurrence_byday;
	public $recurrence_days = 0;
	public $recurrence_byweekno;
	public $recurrence_rsvp_days;
	/* anonymous submission information */
	public $event_owner_anonymous;
	public $event_owner_name;
	public $event_owner_email;
	/**
	 * Previously used to give this object shorter property names for db values (each key has a name) but this is now deprecated, use the db field names as properties. This propertey provides extra info about the db fields.
	 * @var array
	 */
	public $fields = array(
		'event_id' => array( 'name'=>'id', 'type'=>'%d' ),
		'post_id' => array( 'name'=>'post_id', 'type'=>'%d' ),
		'event_parent' => array( 'type'=>'%d', 'null'=>true ),
		'event_slug' => array( 'name'=>'slug', 'type'=>'%s', 'null'=>true ),
		'event_owner' => array( 'name'=>'owner', 'type'=>'%d', 'null'=>true ),
		'event_name' => array( 'name'=>'name', 'type'=>'%s', 'null'=>true ),
		'event_timezone' => array('type'=>'%s', 'null'=>true ),
		'event_start_time' => array( 'name'=>'start_time', 'type'=>'%s', 'null'=>true ),
		'event_end_time' => array( 'name'=>'end_time', 'type'=>'%s', 'null'=>true ),
		'event_start' => array('type'=>'%s', 'null'=>true ),
		'event_end' => array('type'=>'%s', 'null'=>true ),
		'event_all_day' => array( 'name'=>'all_day', 'type'=>'%d', 'null'=>true ),
		'event_start_date' => array( 'name'=>'start_date', 'type'=>'%s', 'null'=>true ),
		'event_end_date' => array( 'name'=>'end_date', 'type'=>'%s', 'null'=>true ),
		'post_content' => array( 'name'=>'notes', 'type'=>'%s', 'null'=>true ),
		'event_rsvp' => array( 'name'=>'rsvp', 'type'=>'%d' ),
		'event_rsvp_date' => array( 'name'=>'rsvp_date', 'type'=>'%s', 'null'=>true ),
		'event_rsvp_time' => array( 'name'=>'rsvp_time', 'type'=>'%s', 'null'=>true ),
		'event_rsvp_spaces' => array( 'name'=>'rsvp_spaces', 'type'=>'%d', 'null'=>true ),
		'event_spaces' => array( 'name'=>'spaces', 'type'=>'%d', 'null'=>true),
		'location_id' => array( 'name'=>'location_id', 'type'=>'%d', 'null'=>true ),
		'event_location_type' => array( 'type'=>'%s', 'null'=>true ),
		'recurrence_id' => array( 'name'=>'recurrence_id', 'type'=>'%d', 'null'=>true ),
		'event_status' => array( 'name'=>'status', 'type'=>'%d', 'null'=>true ),
		'event_active_status' => array( 'name'=>'active_status', 'type'=>'%d', 'null'=>true ),
		'event_private' => array( 'name'=>'status', 'type'=>'%d', 'null'=>true ),
		'blog_id' => array( 'name'=>'blog_id', 'type'=>'%d', 'null'=>true ),
		'group_id' => array( 'name'=>'group_id', 'type'=>'%d', 'null'=>true ),
		'event_language' => array( 'type'=>'%s', 'null'=>true ),
		'event_translation' => array( 'type'=>'%d'),
		'recurrence' => array( 'name'=>'recurrence', 'type'=>'%d', 'null'=>false ), //is this a recurring event template
		'recurrence_interval' => array( 'name'=>'interval', 'type'=>'%d', 'null'=>true ), //every x day(s)/week(s)/month(s)
		'recurrence_freq' => array( 'name'=>'freq', 'type'=>'%s', 'null'=>true ), //daily,weekly,monthly?
		'recurrence_days' => array( 'name'=>'days', 'type'=>'%d', 'null'=>true ), //each event spans x days
		'recurrence_byday' => array( 'name'=>'byday', 'type'=>'%s', 'null'=>true ), //if weekly or monthly, what days of the week?
		'recurrence_byweekno' => array( 'name'=>'byweekno', 'type'=>'%d', 'null'=>true ), //if monthly which week (-1 is last)
		'recurrence_rsvp_days' => array( 'name'=>'recurrence_rsvp_days', 'type'=>'%d', 'null'=>true ), //days before or after start date to generat bookings cut-off date
	);
	public $post_fields = array('event_slug','event_owner','event_name','event_private','event_status','event_attributes','post_id','post_content'); //fields that won't be taken from the em_events table anymore
	public $recurrence_fields = array('recurrence', 'recurrence_interval', 'recurrence_freq', 'recurrence_days', 'recurrence_byday', 'recurrence_byweekno', 'recurrence_rsvp_days');
	
	public static $field_shortcuts = array(
		'language' => 'event_language',
		'translation' => 'event_translation',
		'parent' => 'event_parent',
		'id' => 'event_id',
		'slug' => 'event_slug',
		'name' => 'event_name',
		'status' => 'event_status',
		'owner' => 'event_owner',
		'start_time' => 'event_start_time',
		'end_time' => 'event_end_time',
		'start_date' => 'event_start_date',
		'end_date' => 'event_end_date',
		'start' => 'event_start',
		'end' => 'event_end',
		'all_day' => 'event_all_day',
		'timezone' => 'event_timezone',
		'rsvp' => 'event_rsvp',
		'rsvp_date' => 'event_rsvp_date',
		'rsvp_time' => 'event_rsvp_time',
		'rsvp_spaces' => 'event_rsvp_spaces',
		'spaces' => 'event_spaces',
		'private' => 'event_private',
		'location_type' => 'event_location_type',
		'owner_anonymous' => 'event_owner_anonymous',
		'owner_name' => 'event_owner_name',
		'owner_email' => 'event_owner_email',
		'active_status' => 'event_active_status',
		'notes' => 'post_content',
	);
	
	public $image_url = '';
	/**
	 * EM_DateTime of start date/time in local timezone.
	 * As of EM 5.8 this property is protected and accessible via __get(). For backwards compatibility accessing this property directly returns the timestamp as before with an offset to timezone.
	 * To access the object use EM_Event::start(), do not try to access it directly for better accuracy use EM_Event::start()->getTimestamp();
	 * @var EM_DateTime
	 */
	protected $start;
	/**
	 * EM_DateTime of end date/time in local timezone.
	 * As of EM 5.8 this property is protected and accessible via __get(). For backwards compatibility accessing this property directly returns the timestamp as before, with an offset to timezone.
	 * To access the object use EM_Event::end(), do not try to access it directly for better accuracy use EM_Event::start()->getTimestamp();
	 * @var EM_DateTime
	 */
	protected $end;
	/**
	 * Timestamp for booking cut-off date/time
	 * @var EM_DateTime
	 */
	protected $rsvp_end;
	
	/**
	 * @var EM_Location
	 */
	public $location;
	/**
	 * @var Event_Location
	 */
	public $event_location;
	/**
	 * If we're switching event location types, previous event location is kept here and deleted upon save()
	 * @var Event_Location
	 */
	public $event_location_deleted = null;
	/**
	 * @var EM_Bookings
	 */
	public $bookings;
	/**
	 * The contact person for this event
	 * @var WP_User
	 */
	public $contact;
	/**
	 * The categories object containing the event categories
	 * @var EM_Categories
	 */
	public $categories;
	/**
	 * The tags object containing the event tags
	 * @var EM_Tags
	 */
	public $tags;
	/**
	 * If there are any errors, they will be added here.
	 * @var array
	 */
	public $errors = array();
	/**
	 * If something was successful, a feedback message might be supplied here.
	 * @var string
	 */
	public $feedback_message;
	/**
	 * Any warnings about an event (e.g. bad data, recurrence, etc.)
	 * @var string
	 */
	public $warnings;
	/**
	 * Array of dbem_event field names required to create an event 
	 * @var array
	 */
	public $required_fields = array('event_name', 'event_start_date');
	public $mime_types = array(1 => 'gif', 2 => 'jpg', 3 => 'png'); 
	/**
	 * previous status of event when instantiated
	 * @access protected
	 * @var mixed
	 */
	public $previous_status = false;
	/**
	 * If set to true, recurring events will delete and recreate recurrences when saved.
	 * @var boolean
	 */
	public $recurring_reschedule = false;
	/**
	 * If set to true, recurring events will delete bookings and tickets of recurrences and recreate tickets. If set explicitly to false bookings will be ignored when creating recurrences.
	 * @var boolean
	 */
	public $recurring_recreate_bookings;
	/**
	 * Flag used for when saving a recurring event that previously had bookings enabled and then subsequently disabled.
	 * If set to true, and $this->recurring_recreate_bookings is false, bookings and tickets of recurrences will be deleted.
	 * @var boolean
	 */
	public $recurring_delete_bookings = false;
	/**
	 * If the event was just added/created during this execution, value will be true. Useful when running validation or making decisions on taking actions when events are saved/created for the first time.
	 * @var boolean
	 */
	public $just_added_event = false;
	
	/* Post Variables - copied out of post object for easy IDE reference */
	public $ID;
	public $post_author;
	public $post_date;
	public $post_date_gmt;
	public $post_title;
	public $post_excerpt = '';
	public $post_status;
	public $comment_status;
	public $ping_status;
	public $post_password;
	public $post_name;
	public $to_ping;
	public $pinged;
	public $post_modified;
	public $post_modified_gmt;
	public $post_content_filtered;
	public $post_parent;
	public $guid;
	public $menu_order;
	public $post_type;
	public $post_mime_type;
	public $comment_count;
	public $ancestors;
	public $filter;
	
	/**
	 * @var array   List of status types an event can have, mapped by status number as keys and name of status for value. Consider states 0-9 reserved by core for future features.
	 */
	public static $active_statuses;
	
	/**
	 * Initialize an event. You can provide event data in an associative array (using database table field names), an id number, or false (default) to create empty event.
	 * @param mixed $id
	 * @param mixed $search_by default is post_id, otherwise it can be by event_id as well. In multisite global mode, a blog id can be supplied to load events from another blog.
	 */
	function __construct($id = false, $search_by = 'event_id') {
		global $wpdb;
		if( is_array($id) ){
			//deal with the old array style, but we can't supply arrays anymore
			$id = (!empty($id['event_id'])) ? absint($id['event_id']) : absint($id['post_id']);
			$search_by = (!empty($id['event_id'])) ? 'event_id':'post_id';
		}
		$is_post = !empty($id->ID) && ($id->post_type == EM_POST_TYPE_EVENT || $id->post_type == 'event-recurring');
		if( $is_post ){
			$id->ID = absint($id->ID);
		}else{
			$id = absint($id);
			if( $id == 0 ) $id = false;
		}
		if( is_numeric($id) || $is_post ){ //only load info if $id is a number
			$event_post = null;
			if($search_by == 'event_id' && !$is_post ){
				//search by event_id, get post_id and blog_id (if in ms mode) and load the post
				$results = $wpdb->get_row($wpdb->prepare("SELECT post_id, blog_id FROM ".EM_EVENTS_TABLE." WHERE event_id=%d",$id), ARRAY_A);
				if( !empty($results['post_id']) ){ $this->post_id = $results['post_id']; $this->event_id = $id; }
				if( is_multisite() && (is_numeric($results['blog_id']) || $results['blog_id']=='' ) ){
				    if( $results['blog_id']=='' )  $results['blog_id'] = get_current_site()->blog_id;
					$event_post = get_blog_post($results['blog_id'], $results['post_id']);
					$search_by = $this->blog_id = $results['blog_id'];
				}elseif( !empty($results['post_id']) ){
					$event_post = get_post($results['post_id']);	
				}
			}else{
				//if searching specifically by post_id and in MS Global mode, then assume we're looking in the current blog we're in
				if( $search_by == 'post_id' && EM_MS_GLOBAL ) $search_by = get_current_blog_id();
				//get post data based on ID and search context
				if(!$is_post){
					if( is_multisite() && (is_numeric($search_by) || $search_by == '') ){
					    if( $search_by == '' ) $search_by = get_current_site()->blog_id;
						//we've been given a blog_id, so we're searching for a post id
						$event_post = get_blog_post($search_by, $id);
						$this->blog_id = $search_by;
					}else{
						//search for the post id only
						$event_post = get_post($id);
					}
				}else{
					$event_post = $id;
					//if we're in MS Global mode, then unless a blog id was specified, we assume the current post object belongs to the current blog
					if( EM_MS_GLOBAL && !is_numeric($search_by) ){
						$this->blog_id = get_current_blog_id();
					}
				}
				$this->post_id = !empty($id->ID) ? $id->ID : $id;
			}
			$this->load_postdata($event_post, $search_by);
			// check if active status is enabled, if not set to 1 by default
			if( !get_option('dbem_event_status_enabled') && $this->event_active_status == 0 ){
				$this->event_active_status = 1;
			}
			$this->previous_active_status = $this->event_active_status;
		}
		//set default timezone
		if( empty($this->event_timezone) ){
			if( get_option('dbem_timezone_enabled') ){
				//get default timezone for event, and sanitize UTC variations
				$this->event_timezone = get_option('dbem_timezone_default');
				if( $this->event_timezone == 'UTC+0' || $this->event_timezone == 'UTC +0' ){ $this->event_timezone = 'UTC'; }
			}else{
				$this->event_timezone = EM_DateTimeZone::create()->getName(); //set a default timezone if none exists
			}
		}
		//set recurrence value already
		$this->recurrence = $this->is_recurring() ? 1:0;
		// fire hook to add any extra info to an event
		do_action('em_event', $this, $id, $search_by);
		//add this event to the cache
		if( $this->event_id && $this->post_id ){
			wp_cache_set($this->event_id, $this, 'em_events');
			wp_cache_set($this->post_id, $this->event_id, 'em_events_ids');
		}
	}
	
	function __get( $var ){
	    //get the modified or created date from the DB only if requested, and save to object
	    if( $var == 'event_date_modified' || $var == 'event_date_created'){
	        global $wpdb;
	        $row = $wpdb->get_row($wpdb->prepare("SELECT event_date_created, event_date_modified FROM ".EM_EVENTS_TABLE.' WHERE event_id=%s', $this->event_id));
	        if( $row ){
	            $this->event_date_modified = $row->event_date_modified;
	            $this->event_date_created = $row->event_date_created;
	            return $this->$var;
	        }
	    }elseif( in_array($var, array('event_start_date', 'event_start_time', 'event_end_date', 'event_end_time', 'event_rsvp_date', 'event_rsvp_time')) ){
	    	return $this->$var;
	    }elseif( $var == 'event_timezone' ){
	    	return $this->get_timezone()->getName();
	    }
	    //deprecated properties for external access, use the start(), end() and rsvp_end() functions to access any of this data.
	    if( $var == 'start' ) return $this->start()->getTimestampWithOffset();
	    if( $var == 'end' ) return $this->end()->getTimestampWithOffset();    	
	    if( $var == 'rsvp_end' ) return $this->rsvp_end()->getTimestampWithOffset();
		if( $var == 'event_active_status' || $var == 'active_status' ) {
			return get_option('dbem_event_status_enabled') ? absint($this->event_active_status) : 1;
		}
	    return parent::__get( $var );
	}
	
	public function __set( $prop, $val ){
		if( $prop == 'event_start_date' || $prop == 'event_end_date' || $prop == 'event_rsvp_date' ){
			//if date is valid, set it, if not set it to null
			$this->$prop = preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) ? $val : null;
			if( $prop == 'event_start_date') $this->start = $this->event_start = null;
			elseif( $prop == 'event_end_date') $this->end = $this->event_end = null;
			elseif( $prop == 'event_rsvp_date') $this->rsvp_end = null;
		}elseif( $prop == 'event_start_time' || $prop == 'event_end_time' || $prop == 'event_rsvp_time' ){
			//if time is valid, set it, otherwise set it to midnight
			$this->$prop = preg_match('/^\d{2}:\d{2}:\d{2}$/', $val) ? $val : '00:00:00';
			if( $prop == 'event_start_date') $this->start = null;
			elseif( $prop == 'event_end_date') $this->end = null;
			elseif( $prop == 'event_rsvp_date') $this->rsvp_end = null;
		}
		//deprecated properties, use start()->setTimestamp() instead
		elseif( $prop == 'start' || $prop == 'end' || $prop == 'rsvp_end' ){
			if( is_numeric($val) ){
				$this->$prop()->setTimestamp( (int) $val);
			}elseif( is_string($val) ){
				$this->$val = new EM_DateTime($val, $this->event_timezone);
			}
		}
		// active status
		elseif ( $prop == 'event_active_status' ) {
			$this->event_active_status = absint($val);
		}
		//anything else
		else{
			parent::__set( $prop, $val );
		}
	}
	
	public function __isset( $prop ){
		if( in_array($prop, array('event_start_date', 'event_end_date', 'event_start_time', 'event_end_time', 'event_rsvp_date', 'event_rsvp_time', 'event_start', 'event_end')) ){
			return !empty($this->$prop);
		}elseif( $prop == 'event_timezone' ){
			return true;
		}elseif( $prop == 'event_active_status' ){
			return !empty($this->event_active_status);
		}elseif( $prop == 'start' || $prop == 'end' || $prop == 'rsvp_end' ){
			return $this->$prop()->valid;
		}
		return parent::__isset( $prop );
	}
	
	/**
	 * When cloning this event, we get rid of the bookings and location objects, since they can be retrieved again from the cache instead. 
	 */
	public function __clone(){
		$this->bookings = null;
		$this->location = null;
		if( is_object($this->event_location) ){
			$this->event_location = clone $this->event_location;
			$this->event_location->event = $this;
		}
	}
	
	function load_postdata($event_post, $search_by = false){
		//load event post object if it's an actual object and also a post type of our event CPT names
		if( is_object($event_post) && ($event_post->post_type == 'event-recurring' || $event_post->post_type == EM_POST_TYPE_EVENT) ){
			//load post data - regardless
			$this->post_id = absint($event_post->ID);
			$this->event_name = $event_post->post_title;
			$this->event_owner = $event_post->post_author;
			$this->post_content = $event_post->post_content;
			$this->post_excerpt = $event_post->post_excerpt;
			$this->event_slug = $event_post->post_name;
			foreach( $event_post as $key => $value ){ //merge post object into this object
				$this->$key = $value;
			}
			$this->recurrence = $this->is_recurring() ? 1:0;
			//load meta data and other related information
			if( $event_post->post_status != 'auto-draft' ){
			    $event_meta = $this->get_event_meta($search_by);
			    if( !empty($event_meta['_event_location_type']) ) $this->event_location_type = $event_meta['_event_location_type']; //load this directly so we know further down whether this has an event location type to load
				//Get custom fields and post meta
				$other_event_attributes = apply_filters('em_event_load_postdata_other_attributes', array(), $this);
				foreach($event_meta as $event_meta_key => $event_meta_val){
					$field_name = substr($event_meta_key, 1);
					if($event_meta_key[0] != '_'){
						$this->event_attributes[$event_meta_key] = ( is_array($event_meta_val) ) ? $event_meta_val[0]:$event_meta_val;
					}elseif( is_string($field_name) && !in_array($field_name, $this->post_fields) ){
						if( array_key_exists($field_name, $this->fields) ){
							$this->$field_name = $event_meta_val[0];
						}elseif( in_array($field_name, array('event_owner_name','event_owner_anonymous','event_owner_email')) ){
							$this->$field_name = $event_meta_val[0];
						}elseif( in_array($field_name, $other_event_attributes) ){
							$this->event_attributes[$field_name] = ( is_array($event_meta_val) ) ? $event_meta_val[0]:$event_meta_val;
						}
					}
				}
				if( $this->has_event_location() ) $this->get_event_location()->load_postdata($event_meta);
				//quick compatability fix in case _event_id isn't loaded or somehow got erased in post meta
				if( empty($this->event_id) && !$this->is_recurring() ){
					global $wpdb;
					if( EM_MS_GLOBAL ){
						$event_array = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".EM_EVENTS_TABLE." WHERE post_id=%d && blog_id=%d",$this->post_id, $this->blog_id), ARRAY_A);
					}else{
						$event_array = $wpdb->get_row('SELECT * FROM '.EM_EVENTS_TABLE. ' WHERE post_id='.$this->post_id, ARRAY_A);	
					}
					if( !empty($event_array['event_id']) ){
						foreach($event_array as $key => $value){
							if( !empty($value) && empty($this->$key) ){
								update_post_meta($event_post->ID, '_'.$key, $value);
								$this->$key = $value;
							}
						}
					}
				}
			}
			$this->get_status();
			$this->compat_keys();
		}elseif( !empty($this->post_id) ){
			//we have an orphan... show it, so that we can at least remove it on the front-end
			global $wpdb;
			if( EM_MS_GLOBAL ){ //if MS Global mode enabled, make sure we search by blog too so there's no cross-post confusion
				if( !empty($this->event_id) ){
					$event_array = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".EM_EVENTS_TABLE." WHERE event_id=%d",$this->event_id), ARRAY_A);
				}else{
					if( $this->blog_id == get_current_blog_id() || empty($this->blog_id) ){
						$event_array = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".EM_EVENTS_TABLE." WHERE post_id=%d AND (blog_id=%d OR blog_id IS NULL)",$this->post_id, $this->blog_id), ARRAY_A);
					}else{
						$event_array = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".EM_EVENTS_TABLE." WHERE post_id=%d AND blog_id=%d",$this->post_id, $this->blog_id), ARRAY_A);
					}
				}
			}else{
				$event_array = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".EM_EVENTS_TABLE." WHERE post_id=%d",$this->post_id), ARRAY_A);
			}
		    if( is_array($event_array) ){
				$this->orphaned_event = true;
				$this->post_id = $this->ID = $event_array['post_id'] = null; //reset post_id because it doesn't really exist
				$this->to_object($event_array);
		    }
		}
		if( empty($this->location_id) && !empty($this->event_id) ) $this->location_id = 0; //just set location_id to 0 and avoid any doubt
		if( EM_MS_GLOBAL && empty($this->blog_id) ) $this->blog_id = get_current_site()->blog_id; //events created before going multisite may have null values, so we set it to main site id
	}
	
	function get_event_meta($blog_id = false){
		if( !empty($this->blog_id) ) $blog_id = $this->blog_id; //if there's a blog id already, there's no doubt where to look for
		if( empty($this->post_id) ) return array();
		if( is_numeric($blog_id) && $blog_id > 0 && is_multisite() ){
			// if in multisite mode, switch blogs quickly to get the right post meta.
			switch_to_blog($blog_id);
			$event_meta = get_post_meta($this->post_id);
			restore_current_blog();
			$this->blog_id = $blog_id;
		}elseif( EM_MS_GLOBAL ){
			// if a blog ID wasn't defined then we'll check the main blog, in case the event was created in the past
			$this->ms_global_switch();
			$event_meta = get_post_meta($this->post_id);
			$this->ms_global_switch_back();
		}else{
			$event_meta = get_post_meta($this->post_id);
		}
		if( !is_array($event_meta) ) $event_meta = array();
		return apply_filters('em_event_get_event_meta', $event_meta);
	}
	
	/**
	 * Retrieve event information via POST (only used in situations where posts aren't submitted via WP)
	 * @return boolean
	 */
	function get_post($validate = true){	
		global $allowedposttags;
		do_action('em_event_get_post_pre', $this);
		//we need to get the post/event name and content.... that's it.
		$this->post_content = isset($_POST['content']) ? wp_kses( wp_unslash($_POST['content']), $allowedposttags):'';
		$this->post_excerpt = !empty($this->post_excerpt) ? $this->post_excerpt:''; //fix null error
		$this->event_name = ( !empty($_POST['event_name']) ) ? sanitize_post_field('post_title', $_POST['event_name'], $this->post_id, 'db'):'';
		$this->post_type = ($this->is_recurring() || !empty($_POST['recurring'])) ? 'event-recurring':EM_POST_TYPE_EVENT;
		//don't forget categories!
		if( get_option('dbem_categories_enabled') ) $this->get_categories()->get_post();
		//get the rest and validate (optional)
		$this->get_post_meta();
		//anonymous submissions and guest basic info
		if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && empty($this->event_id) ){
			$this->event_owner_anonymous = 1;
			$this->event_owner_name = !empty($_POST['event_owner_name']) ? wp_kses_data(wp_unslash($_POST['event_owner_name'])):'';
			$this->event_owner_email = !empty($_POST['event_owner_email']) ? wp_kses_data($_POST['event_owner_email']):'';
			if( empty($this->location_id) && !($this->location_id === 0 && !get_option('dbem_require_location',true)) ){
				$this->get_location()->owner_anonymous = 1;
				$this->location->owner_email = $this->event_owner_email;
				$this->location->owner_name = $this->event_owner_name;
			}
		}
		//validate and return results
		$result = $validate ? $this->validate():true; //validate both post and meta, otherwise return true
		return apply_filters('em_event_get_post', $result, $this);
	}
	
	/**
	 * Retrieve event post meta information via POST, which should be always be called when saving the event custom post via WP.
	 * @return boolean
	 */
	function get_post_meta(){
		do_action('em_event_get_post_meta_pre', $this);
		
		//Check if this is recurring or not early on so we can take appropriate action further down
		if( !empty($_POST['recurring']) ){
			$this->recurrence = 1;
			$this->post_type = 'event-recurring';
		}
		//Set Event Timezone to supplied value or alternatively use blog timezone value by default.
		if( !empty($_REQUEST['event_timezone']) ){
			$this->event_timezone = EM_DateTimeZone::create($_REQUEST['event_timezone'])->getName();
		}elseif( empty($this->event_timezone ) ){ //if timezone was already set but not supplied, we don't change it
			$this->event_timezone = EM_DateTimeZone::create()->getName();
		}
		//Dates and Times - dates ignored if event is recurring being updated (not new) and not specifically chosen to reschedule event
		$this->event_start = $this->event_end = null;
		if( !$this->is_recurring() || (empty($this->event_id) || !empty($_REQUEST['event_reschedule'])) ){
			//Event Dates
			$this->event_start_date = ( !empty($_POST['event_start_date']) ) ? wp_kses_data($_POST['event_start_date']) : null;
			$this->event_end_date = ( !empty($_POST['event_end_date']) ) ? wp_kses_data($_POST['event_end_date']) : $this->event_start_date;
		}
		//Sort out time
		$this->event_all_day = ( !empty($_POST['event_all_day']) ) ? 1 : 0;
		if( $this->event_all_day ){
			$times_array = array('event_rsvp_time');
			$this->event_start_time = '00:00:00';
			$this->event_end_time = '23:59:59';
		}else{
			$times_array = array('event_start_time','event_end_time', 'event_rsvp_time');
		}
		foreach( $times_array as $timeName ){
			$match = array();
			if( !empty($_POST[$timeName]) && preg_match ( '/^([01]\d|[0-9]|2[0-3])(:([0-5]\d))? ?(AM|PM)?$/', $_POST[$timeName], $match ) ){
				if( empty($match[3]) ) $match[3] = '00';
				if( strlen($match[1]) == 1 ) $match[1] = '0'.$match[1];
				if( !empty($match[4]) && $match[4] == 'PM' && $match[1] != 12 ){
					$match[1] = 12+$match[1];
				}elseif( !empty($match[4]) && $match[4] == 'AM' && $match[1] == 12 ){
					$match[1] = '00';
				}
				$this->$timeName = $match[1].":".$match[3].":00";
			}else{
				$this->$timeName = ($timeName == 'event_start_time') ? "00:00:00":$this->event_start_time;
			}
		}
		//reset start and end objects so they are recreated with the new dates/times if and when needed
		$this->start = $this->end = null;
		
		// set status, if supplied
		if ( isset($_POST['event_active_status']) && array_key_exists( $_POST['event_active_status'], static::get_active_statuses() ) ) {
			$this->previous_active_status = $this->event_active_status;
			$this->event_active_status = absint($_POST['event_active_status']);
		}
		
		//Get Location Info
		if( get_option('dbem_locations_enabled') ){
			// determine location type, with backward compatibility considerations for those overriding the location forms
			$location_type = isset($_POST['location_type']) ? sanitize_key($_POST['location_type']) : 'location';
			if( !empty($_POST['no_location']) ) $location_type = 0; //backwards compat
			if( $location_type == 'location' && empty($_POST['location_id']) && get_option('dbem_use_select_for_locations')) $location_type = 0; //backward compat
			// assign location data
			if( $location_type === 0 || $location_type === '0' ){
				// no location
				$this->location_id = 0;
				$this->event_location_type = null;
			}elseif( $location_type == 'location' && EM_Locations::is_enabled() ){
				// a physical location, old school
				$this->event_location_type = null; // if location resides in locations table, location type is null since we have a location_id table value
				if(  !empty($_POST['location_id']) && is_numeric($_POST['location_id']) ){
					// we're using a previously created location
					$this->location_id = absint($_POST['location_id']);
				}else{
					$this->location_id = null;
					//we're adding a new location place, so create an empty location and populate
					$this->get_location()->get_post(false);
					$this->get_location()->post_content = ''; //reset post content, as it'll grab the event description otherwise
				}
			}else{
				// we're dealing with an event location such as a url or webinar
				$this->location_id = null; // no location ID
				if( $this->event_id && $this->has_event_location() && $location_type != $this->event_location_type ){
					// if we're changing location types, then we'll delete all the previous data upon saving
					$this->event_location_deleted = $this->event_location;
				}
				$this->event_location_type = $location_type;
				if( Event_Locations::is_enabled($location_type) ){
					$this->get_event_location()->get_post();
				}
			}
		}else{
			$this->location_id = 0;
			$this->event_location_type = null;
		}
		
		//Bookings
		$can_manage_bookings = $this->can_manage('manage_bookings','manage_others_bookings');
		$preview_autosave = is_admin() && !empty($_REQUEST['_emnonce']) && !empty($_REQUEST['wp-preview']) && $_REQUEST['wp-preview'] == 'dopreview'; //we shouldn't save new data during a preview auto-save
		if( !$preview_autosave && $can_manage_bookings && !empty($_POST['event_rsvp']) && $_POST['event_rsvp'] ){
			//get tickets only if event is new, non-recurring, or recurring but specifically allowed to reschedule by user
			if( !$this->is_recurring() || (empty($this->event_id) || !empty($_REQUEST['event_recreate_tickets'])) || !$this->event_rsvp ){
				$this->get_bookings()->get_tickets()->get_post();
			}
			$this->event_rsvp = 1;
			$this->rsvp_end = null;
			//RSVP cuttoff TIME is set up above where start/end times are as well
				if( get_option('dbem_bookings_tickets_single') && count($this->get_tickets()->tickets) == 1 ){
					//single ticket mode will use the ticket end date/time as cut-off date/time
			    	$EM_Ticket = $this->get_tickets()->get_first();
			    	$this->event_rsvp_date = null;
			    	if( !empty($EM_Ticket->ticket_end) ){
			    		$this->event_rsvp_date = $EM_Ticket->end()->getDate();
			    		$this->event_rsvp_time = $EM_Ticket->end()->getTime();
			    	}else{
			    		//no default ticket end time, so make it default to event start date/time
			    		$this->event_rsvp_date = $this->event_start_date;
			    		$this->event_rsvp_time = $this->event_start_time;
			    		if( $this->event_all_day && empty($_POST['event_rsvp_date']) ){ $this->event_rsvp_time = '00:00:00'; } //all-day events start at 0 hour
			    	}
			    }else{
			    	//if no rsvp cut-off date supplied, make it the event start date
			    	$this->event_rsvp_date = ( !empty($_POST['event_rsvp_date']) ) ? wp_kses_data($_POST['event_rsvp_date']) : $this->event_start_date;
					//if no specificed time, default to event start time
			    	if ( empty($_POST['event_rsvp_time']) ) $this->event_rsvp_time = $this->event_start_time;
			    }
			    //reset EM_DateTime object
				$this->rsvp_end = null;
			$this->event_spaces = ( isset($_POST['event_spaces']) ) ? absint($_POST['event_spaces']):0;
			$this->event_rsvp_spaces = ( isset($_POST['event_rsvp_spaces']) ) ? absint($_POST['event_rsvp_spaces']):0;
		}elseif( !$preview_autosave && ($can_manage_bookings || !$this->event_rsvp) ){
			if( empty($_POST['event_rsvp']) && $this->event_rsvp ) $deleting_bookings = true;
			$this->event_rsvp = 0;
			$this->event_rsvp_date = $this->event_rsvp_time = $this->rsvp_end = null;
		}
		
		//Sort out event attributes - note that custom post meta now also gets inserted here automatically (and is overwritten by these attributes)
		global $allowedtags;
		if(get_option('dbem_attributes_enabled')){
			if( !is_array($this->event_attributes) ){ $this->event_attributes = array(); }
			$event_available_attributes = !empty($event_available_attributes) ? $event_available_attributes : em_get_attributes(); //we use this in locations, no need to repeat if needed
			if( !empty($_POST['em_attributes']) && is_array($_POST['em_attributes']) ){
				foreach($_POST['em_attributes'] as $att_key => $att_value ){
					if( (in_array($att_key, $event_available_attributes['names']) || array_key_exists($att_key, $this->event_attributes) ) ){
						$this->event_attributes[$att_key] = '';
						$att_vals = isset($event_available_attributes['values'][$att_key]) ? count($event_available_attributes['values'][$att_key]) : 0;
						if( $att_value != '' ){
							if( $att_vals <= 1 || ($att_vals > 1 && in_array($att_value, $event_available_attributes['values'][$att_key])) ){
								$this->event_attributes[$att_key] = wp_unslash($att_value);
							}
						}
						if( $att_value == '' && $att_vals > 1){
							$this->event_attributes[$att_key] = wp_unslash(wp_kses($event_available_attributes['values'][$att_key][0], $allowedtags));
						}
					}
				}
			}
		}
		// get other event attributes, we may want to
		$other_event_attributes = apply_filters('em_event_get_post_meta_other_attributes', array(), $this);
		foreach( $other_event_attributes as $event_attribute ){
			if( isset($_POST[$event_attribute]) ){
				$this->event_attributes[$event_attribute] = wp_unslash(wp_kses($_POST[$event_attribute], $allowedtags));
			}
		}
		
		//group id
		$this->group_id = (!empty($_POST['group_id']) && is_numeric($_POST['group_id'])) ? absint($_POST['group_id']):0;
		
		//Recurrence data
		if( $this->is_recurring() ){
			$this->recurrence = 1; //just in case
			
			//If event is new or reschedule is requested, then proceed with new time pattern
			if( empty($this->event_id) || !empty($_REQUEST['event_reschedule']) ){
				//dates and time schedules of events
				$this->recurrence_freq = ( !empty($_POST['recurrence_freq']) && in_array($_POST['recurrence_freq'], array('daily','weekly','monthly','yearly')) ) ? $_POST['recurrence_freq']:'daily';
				if( !empty($_POST['recurrence_bydays']) && $this->recurrence_freq == 'weekly' && self::array_is_numeric($_POST['recurrence_bydays']) ){
					$this->recurrence_byday = str_replace(' ', '', implode( ",", $_POST['recurrence_bydays'] ));
				}elseif( isset($_POST['recurrence_byday']) && $this->recurrence_freq == 'monthly' ){
					$this->recurrence_byday = wp_kses_data($_POST['recurrence_byday']);
				}else{
					$this->recurrence_byday = null;
				}
				$this->recurrence_interval = ( !empty($_POST['recurrence_interval']) && is_numeric($_POST['recurrence_interval']) ) ? $_POST['recurrence_interval']:1;
				$this->recurrence_byweekno = ( !empty($_POST['recurrence_byweekno']) ) ? wp_kses_data($_POST['recurrence_byweekno']):'';
				$this->recurrence_days = ( !empty($_POST['recurrence_days']) && is_numeric($_POST['recurrence_days']) ) ? (int) $_POST['recurrence_days']:0;
			}
			
			//here we do a comparison between new and old event data to see if we are to reschedule events or recreate bookings
			if( $this->event_id ){ //only needed if this is an existing event needing rescheduling/recreation
				//Get original recurring event so we can tell whether event recurrences or bookings will be recreated or just modified
				$EM_Event = new EM_Event($this->event_id);
				
				//first check event times
				$recurring_event_dates = array(
						'event_start_date' => $EM_Event->event_start_date,
						'event_end_date' => $EM_Event->event_end_date,
						'recurrence_byday' => $EM_Event->recurrence_byday,
						'recurrence_byweekno' => $EM_Event->recurrence_byweekno,
						'recurrence_days' => $EM_Event->recurrence_days,
						'recurrence_freq' => $EM_Event->recurrence_freq,
						'recurrence_interval' => $EM_Event->recurrence_interval
				);
				//check previously saved event info compared to current recurrence info to see if we need to reschedule
				foreach($recurring_event_dates as $k => $v){
					if( $this->$k != $v ){
						$this->recurring_reschedule = true; //something changed, so we reschedule
					}
				}
				
				//now check tickets if we don't already have to reschedule
				if( !$this->recurring_reschedule && $this->event_rsvp ){
					//@TODO - ideally tickets could be independent of events, it'd make life easier here for comparison and editing without rescheduling
					$EM_Tickets = $EM_Event->get_tickets();
					//we compare tickets
					foreach( $this->get_tickets()->tickets as $EM_Ticket ){
						if( !empty($EM_Ticket->ticket_id) && !empty($EM_Tickets->tickets[$EM_Ticket->ticket_id]) ){
							$new_ticket = $EM_Ticket->to_array(true);
							foreach( $EM_Tickets->tickets[$EM_Ticket->ticket_id]->to_array() as $k => $v ){
								if( !(empty($new_ticket[$k]) && empty($v)) && ((empty($new_ticket[$k]) && $v) || $new_ticket[$k] != $v) ){
									if( $k == 'ticket_meta' && is_array($v) && is_array($new_ticket['ticket_meta']) ){
										foreach( $v as $k_meta => $v_meta ){
											if( (empty($new_ticket['ticket_meta'][$k_meta]) && !empty($v_meta)) || $new_ticket['ticket_meta'][$k_meta] != $v_meta ){
												$this->recurring_recreate_bookings = true; //something changed, so we reschedule
											}
										}
									}else{
										$this->recurring_recreate_bookings = true; //something changed, so we reschedule
									}
								}
							}
						}else{
							$this->recurring_recreate_bookings = true; //we have a new ticket
						}
					}
				}elseif( !empty($deleting_bookings) ){
					$this->recurring_delete_bookings = true;
				}
				unset($EM_Event);
			}else{
				//new event so we create everything from scratch
				$this->recurring_reschedule = $this->recurring_recreate_bookings = true;
			}
			//recurring events may have a cut-off date x days before or after the recurrence start dates
			$this->recurrence_rsvp_days = null;
			if( get_option('dbem_bookings_tickets_single') && count($this->get_tickets()->tickets) == 1 ){
				//if in single ticket mode then ticket cut-off date determines event cut-off date
				$EM_Ticket = $this->get_tickets()->get_first();
				if( !empty($EM_Ticket->ticket_meta['recurrences']) ){
					$this->recurrence_rsvp_days = $EM_Ticket->ticket_meta['recurrences']['end_days'];
					$this->event_rsvp_time = $EM_Ticket->ticket_meta['recurrences']['end_time'];
				}
			}else{
				if( array_key_exists('recurrence_rsvp_days', $_POST) ){
					if( !empty($_POST['recurrence_rsvp_days_when']) && $_POST['recurrence_rsvp_days_when'] == 'after' ){
						$this->recurrence_rsvp_days = absint($_POST['recurrence_rsvp_days']);
					}else{ //by default the start date is the point of reference
						$this->recurrence_rsvp_days = absint($_POST['recurrence_rsvp_days']) * -1;
					}
				}
			}
			//create timestamps and set rsvp date/time for a normal event
			if( !is_numeric($this->recurrence_rsvp_days) ){
				//falback in case nothing gets set for rsvp cut-off
				$this->event_rsvp_date = $this->event_rsvp_time = $this->rsvp_end = null;
			}else{
				$this->event_rsvp_date = $this->start()->copy()->modify($this->recurrence_rsvp_days.' days')->getDate();
			}
		}else{
			foreach( $this->recurrence_fields as $recurrence_field ){
				$this->$recurrence_field = null;
			}
			$this->recurrence = 0; // to avoid any doubt
		}
		//event language
		if( EM_ML::$is_ml && !empty($_POST['event_language']) && array_key_exists($_POST['event_language'], EM_ML::$langs) ){
			$this->event_language = $_POST['event_language'];
		}
		//categories in MS GLobal
		if(EM_MS_GLOBAL && !is_main_site() && get_option('dbem_categories_enabled') ){
			$this->get_categories()->get_post(); //it'll know what to do
		}
		$this->compat_keys(); //compatability
		return apply_filters('em_event_get_post_meta', count($this->errors) == 0, $this);
	}
	
	function validate(){
		$validate_post = true;
		if( empty($this->event_name) ){
			$validate_post = false; 
			$this->add_error( sprintf(__("%s is required.", 'events-manager'), __('Event name','events-manager')) );
		}
		//anonymous submissions and guest basic info
		if( !empty($this->event_owner_anonymous) ){
			if( !is_email($this->event_owner_email) ){
				$this->add_error( sprintf(__("%s is required.", 'events-manager'), __('A valid email','events-manager')) );
			}
			if( empty($this->event_owner_name) ){
				$this->add_error( sprintf(__("%s is required.", 'events-manager'), __('Your name','events-manager')) );
			}
		}
		$validate_tickets = true; //must pass if we can't validate bookings
		if( $this->can_manage('manage_bookings','manage_others_bookings') ){
		    $validate_tickets = $this->get_bookings()->get_tickets()->validate();
		}
		$validate_image = $this->image_validate();
		$validate_meta = $this->validate_meta();
		return apply_filters('em_event_validate', $validate_post && $validate_image && $validate_meta && $validate_tickets, $this );		
	}
	
	function validate_meta(){
		$missing_fields = Array ();
		foreach ( array('event_start_date') as $field ) {
			if ( $this->$field == "") {
				$missing_fields[$field] = $field;
			}
		}
		if( preg_match('/\d{4}-\d{2}-\d{2}/', $this->event_start_date) && preg_match('/\d{4}-\d{2}-\d{2}/', $this->event_end_date) ){
			if( $this->start()->getTimestamp() > $this->end()->getTimestamp() ){
				$this->add_error(__('Events cannot start after they end.','events-manager'));
			}elseif( $this->is_recurring() && $this->recurrence_days == 0 && $this->start()->getTimestamp() > $this->end()->getTimestamp() ){
				$this->add_error(__('Events cannot start after they end.','events-manager').' '.__('For recurring events that end the following day, ensure you make your event last 1 or more days.'));
			}
		}else{
			if( !empty($missing_fields['event_start_date']) ) { unset($missing_fields['event_start_date']); }
			if( !empty($missing_fields['event_end_date']) ) { unset($missing_fields['event_end_date']); }
			$this->add_error(__('Dates must have correct formatting. Please use the date picker provided.','events-manager'));
		}
		if( $this->event_rsvp ){
		    if( !$this->get_bookings()->get_tickets()->validate() ){
		        $this->add_error($this->get_bookings()->get_tickets()->get_errors());
		    }
		    if( !empty($this->event_rsvp_date) && !preg_match('/\d{4}-\d{2}-\d{2}/', $this->event_rsvp_date) ){
				$this->add_error(__('Dates must have correct formatting. Please use the date picker provided.','events-manager'));
		    }
		}
		if( get_option('dbem_locations_enabled') ){
			if( $this->location_id === 0 && get_option('dbem_require_location',true) ){
				// no location chosen, yet we require a location
				$this->add_error(__('No location associated with this event.', 'events-manager'));
			}elseif( $this->has_location() ){
				// physical location
				if( empty($this->location_id) && !$this->get_location()->validate() ){
					// new location doesn't validate
					$this->add_error($this->get_location()->get_errors());
				}elseif( !empty($this->location_id) && !$this->get_location()->location_id ){
					// non-existent location selected
					$this->add_error( __('Please select a valid location.', 'events-manager') );
				}
			}elseif( $this->has_event_location() ){
				// event location, validation applies errors directly to $this
				$this->get_event_location()->validate();
			}
		}
		if ( count($missing_fields) > 0){
			// TODO Create friendly equivelant names for missing fields notice in validation
			$this->add_error( __( 'Missing fields: ', 'events-manager') . implode ( ", ", $missing_fields ) . ". " );
		}
		if ( $this->is_recurring() ){
		    if( $this->event_end_date == "" || $this->event_end_date == $this->event_start_date){
		        $this->add_error( __( 'Since the event is repeated, you must specify an event end date greater than the start date.', 'events-manager'));
		    }
		    if( $this->recurrence_freq == 'weekly' && !preg_match('/^[0-9](,[0-9])*$/',$this->recurrence_byday) ){
		        $this->add_error( __( 'Please specify what days of the week this event should occur on.', 'events-manager'));
		    }
		}
		return apply_filters('em_event_validate_meta', count($this->errors) == 0, $this );
	}
	
	/**
	 * Will save the current instance into the database, along with location information if a new one was created and return true if successful, false if not.
	 * Will automatically detect whether it's a new or existing event. 
	 * @return boolean
	 */
	function save(){
		global $wpdb, $current_user, $blog_id, $EM_SAVING_EVENT;
		$EM_SAVING_EVENT = true; //this flag prevents our dashboard save_post hooks from going further
		if( !$this->can_manage('edit_events', 'edit_others_events') && !( get_option('dbem_events_anonymous_submissions') && empty($this->event_id)) ){
			//unless events can be submitted by an anonymous user (and this is a new event), user must have permissions.
			return apply_filters('em_event_save', false, $this);
		}
		//start saving process
		do_action('em_event_save_pre', $this);
		$post_array = array();
		//Deal with updates to an event
		if( !empty($this->post_id) ){
			//get the full array of post data so we don't overwrite anything.
			if( !empty($this->blog_id) && is_multisite() ){
				$post_array = (array) get_blog_post($this->blog_id, $this->post_id);
			}else{
				$post_array = (array) get_post($this->post_id);
			}
		}
		//Overwrite new post info
		$post_array['post_type'] = ($this->recurrence && get_option('dbem_recurrence_enabled')) ? 'event-recurring':EM_POST_TYPE_EVENT;
		$post_array['post_title'] = $this->event_name;
		$post_array['post_content'] = !empty($this->post_content) ? $this->post_content : '';
		$post_array['post_excerpt'] = $this->post_excerpt;
		//decide on post status
		if( empty($this->force_status) ){
			if( count($this->errors) == 0 ){
				$post_array['post_status'] = ( $this->can_manage('publish_events','publish_events') ) ? 'publish':'pending';
			}else{
				$post_array['post_status'] = 'draft';
			}
		}else{
		    $post_array['post_status'] = $this->force_status;
		}
		//anonymous submission only
		if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') && empty($this->event_id) ){
			$post_array['post_author'] = get_option('dbem_events_anonymous_user');
			if( !is_numeric($post_array['post_author']) ) $post_array['post_author'] = 0;
		}
		//Save post and continue with meta
		$post_id = wp_insert_post($post_array);
		$post_save = false;
		$meta_save = false;
		if( !is_wp_error($post_id) && !empty($post_id) ){
			$post_save = true;
			//refresh this event with wp post info we'll put into the db
			$post_data = get_post($post_id);
			$this->post_id = $this->ID = $post_id;
			$this->post_type = $post_data->post_type;
			$this->event_slug = $post_data->post_name;
			$this->event_owner = $post_data->post_author;
			$this->post_status = $post_data->post_status;
			$this->get_status();
			//Categories
			if( get_option('dbem_categories_enabled') ){
    			$this->get_categories()->event_id = $this->event_id;
    			$this->categories->post_id = $this->post_id;
    			$this->categories->save();
			}
			//anonymous submissions should save this information
			if( !empty($this->event_owner_anonymous) ){
				update_post_meta($this->post_id, '_event_owner_anonymous', 1);
				update_post_meta($this->post_id, '_event_owner_name', $this->event_owner_name);
				update_post_meta($this->post_id, '_event_owner_email', $this->event_owner_email);
			}
			//save the image, errors here will surface during $this->save_meta()
			$this->image_upload();
			//now save the meta
			$meta_save = $this->save_meta();
		}
		$result = $meta_save && $post_save;
		if($result) $this->load_postdata($post_data, $blog_id); //reload post info
		//do a dirty update for location too if it's not published
		if( $this->is_published() && !empty($this->location_id) ){
			$EM_Location = $this->get_location();
			if( $EM_Location->location_status !== 1 ){
				//let's also publish the location
				$EM_Location->set_status(1, true);
			}
		}
		$return = apply_filters('em_event_save', $result, $this);
		$EM_SAVING_EVENT = false;
		//reload post data and add this event to the cache, after any other hooks have done their thing
		//cache refresh when saving via admin area is handled in EM_Event_Post_Admin::save_post/refresh_cache
		if( $result && $this->is_published() ){ 
			//we won't depend on hooks, if we saved the event and it's still published in its saved state, refresh the cache regardless
			$this->load_postdata($this);
			wp_cache_set($this->event_id, $this, 'em_events');
			wp_cache_set($this->post_id, $this->event_id, 'em_events_ids');
		}
		return $return;
	}
	
	function save_meta(){
		global $wpdb, $EM_SAVING_EVENT;
		$EM_SAVING_EVENT = true;
		//sort out multisite blog id if appliable
		if( is_multisite() && empty($this->blog_id) ){
			$this->blog_id = get_current_blog_id();
		}
		//trigger setting of event_end and event_start in case it hasn't been set already
		$this->start();
		$this->end();
		//continue with saving if permissions allow
		if( ( get_option('dbem_events_anonymous_submissions') && empty($this->event_id)) || $this->can_manage('edit_events', 'edit_others_events') ){
			do_action('em_event_save_meta_pre', $this);
			//language default
			if( !$this->event_language ) $this->event_language = EM_ML::$current_language;
			//first save location
			if( empty($this->location_id) && !($this->location_id === 0 && !get_option('dbem_require_location',true)) ){
				//pass language on
				$this->get_location()->location_language = $this->event_language;
			    //proceed with location save
				if( !$this->get_location()->save() ){ //soft fail
					global $EM_Notices;
					if( !empty($this->get_location()->location_id) ){
						$EM_Notices->add_error( __('There were some errors saving your location.','events-manager').' '.sprintf(__('It will not be displayed on the website listings, to correct this you must <a href="%s">edit your location</a> directly.'),$this->get_location()->output('#_LOCATIONEDITURL')), true);
					}
				}
				if( !empty($this->location->location_id) ){ //only case we don't use get_location(), since it will fail as location has an id, whereas location_id isn't set in this object
					$this->location_id = $this->location->location_id;
				}
			}
			//Update Post Meta
			$current_meta_values = $this->get_event_meta();
			foreach( $this->fields as $key => $field_info ){
				//certain keys will not be saved if not needed, including flags with a 0 value. Older databases using custom WP_Query calls will need to use an array of meta_query items using NOT EXISTS - OR - value=0
				if( !in_array($key, $this->post_fields) && $key != 'event_attributes' ){
					//ignore certain fields and delete if not new
					$save_meta_key = true;
					if( !$this->is_recurring() && in_array($key, $this->recurrence_fields) ) $save_meta_key = false;
					if( !$this->is_recurrence() && $key == 'recurrence_id' ) $save_meta_key = false;
					if( !EM_ML::$is_ml && $key == 'event_language' ) $save_meta_key = false;
					$ignore_zero_keys = array('location_id', 'group_id', 'event_all_day', 'event_parent', 'event_translation');
					if( in_array($key, $ignore_zero_keys) && empty($this->$key) ) $save_meta_key = false;
					if( $key == 'blog_id' ) $save_meta_key = false; //not needed, given postmeta is stored on the actual blog table in MultiSite
					//we don't need rsvp info if rsvp is not set, including the RSVP flag too
					if( empty($this->event_rsvp) && in_array($key, array('event_rsvp','event_rsvp_date', 'event_rsvp_time', 'event_rsvp_spaces', 'event_spaces')) ) $save_meta_key = false;
					//save key or ignore/delete key
					if( $save_meta_key ){
						update_post_meta($this->post_id, '_'.$key, $this->$key);
					}elseif( array_key_exists('_'.$key, $current_meta_values) ){
						//delete if this event already existed, in case this event already had the values before
						delete_post_meta($this->post_id, '_'.$key);
					}
				}elseif( array_key_exists('_'.$key, $current_meta_values) && $key != 'event_attributes' ){ //we should delete event_attributes, but maybe something else uses it without us knowing
					delete_post_meta($this->post_id, '_'.$key);
				}
			}
			if( get_option('dbem_attributes_enabled') ){
				//attributes get saved as individual keys
				$atts = em_get_attributes(); //get available attributes that EM manages
				$this->event_attributes = maybe_unserialize($this->event_attributes);
				foreach( $atts['names'] as $event_attribute_key ){
					if( array_key_exists($event_attribute_key, $this->event_attributes) && $this->event_attributes[$event_attribute_key] != '' ){
						update_post_meta($this->post_id, $event_attribute_key, $this->event_attributes[$event_attribute_key]);
					}else{
						delete_post_meta($this->post_id, $event_attribute_key);
					}
				}
			}
			// save other event attributes, we may want to
			$other_event_attributes = apply_filters('em_event_save_post_meta_other_attributes', array(), $this);
			foreach( $other_event_attributes as $key ){
				if( isset($this->event_attributes[$key]) ) {
					update_post_meta( $this->post_id, '_'.$key, $this->event_attributes[$key]);
				}else{
					delete_post_meta( $this->post_id, '_'.$key);
				}
			}
			//update timestamps, dates and times
			update_post_meta($this->post_id, '_event_start_local', $this->start()->getDateTime());
			update_post_meta($this->post_id, '_event_end_local', $this->end()->getDateTime());
			//Deprecated, only for backwards compatibility, these meta fields will eventually be deleted!
			$site_data = get_site_option('dbem_data');
			if( !empty($site_data['updates']['timezone-backcompat']) ){
				update_post_meta($this->post_id, '_start_ts', str_pad($this->start()->getTimestamp(), 10, 0, STR_PAD_LEFT));
				update_post_meta($this->post_id, '_end_ts', str_pad($this->end()->getTimestamp(), 10, 0, STR_PAD_LEFT));
			}
			//sort out event status			
			$result = count($this->errors) == 0;
			$this->get_status();
			$this->event_status = ($result) ? $this->event_status:null; //set status at this point, it's either the current status, or if validation fails, null
			//Save to em_event table
			$event_array = $this->to_array(true);
			unset($event_array['event_id']);
			//decide whether or not event is private at this point
			$event_array['event_private'] = ( $this->post_status == 'private' ) ? 1:0;
			//check if event truly exists, meaning the event_id is actually a valid event id
			if( !empty($this->event_id) ){
				$blog_condition = '';
				if( !empty($this->orphaned_event ) && !empty($this->post_id) ){
				    //we're dealing with an orphaned event in wp_em_events table, so we want to update the post_id and give it a post parent 
				    $event_truly_exists = true;
				}else{
					if( EM_MS_GLOBAL ){
					    if( is_main_site() ){
					        $blog_condition = " AND (blog_id='".get_current_blog_id()."' OR blog_id IS NULL)";
					    }else{
							$blog_condition = " AND blog_id='".get_current_blog_id()."' ";
					    }
					}
					$event_truly_exists = $wpdb->get_var('SELECT post_id FROM '.EM_EVENTS_TABLE." WHERE event_id={$this->event_id}".$blog_condition) == $this->post_id;
				}
			}else{
				$event_truly_exists = false;
			}
			//save all the meta
			if( empty($this->event_id) || !$event_truly_exists ){
				$this->previous_status = 0; //for sure this was previously status 0
				$this->event_date_created = $event_array['event_date_created'] = current_time('mysql');
				if ( !$wpdb->insert(EM_EVENTS_TABLE, $event_array) ){
					$this->add_error( sprintf(__('Something went wrong saving your %s to the index table. Please inform a site administrator about this.','events-manager'),__('event','events-manager')));
				}else{
					//success, so link the event with the post via an event id meta value for easy retrieval
					$this->event_id = $wpdb->insert_id;
					update_post_meta($this->post_id, '_event_id', $this->event_id);
					$this->feedback_message = sprintf(__('Successfully saved %s','events-manager'),__('Event','events-manager'));
					$this->just_added_event = true; //make an easy hook
					$this->get_bookings()->bookings = array(); //set bookings array to 0 to avoid an extra DB query
					do_action('em_event_save_new', $this);
				}
			}else{
			    $event_array['post_content'] = $this->post_content; //in case the content was removed, which is acceptable
			    $this->get_previous_status();
				$this->event_date_modified = $event_array['event_date_modified'] = current_time('mysql');
				if ( $wpdb->update(EM_EVENTS_TABLE, $event_array, array('event_id'=>$this->event_id) ) === false ){
					$this->add_error( sprintf(__('Something went wrong updating your %s to the index table. Please inform a site administrator about this.','events-manager'),__('event','events-manager')));			
				}else{
					//Also set the status here if status != previous status
					if( $this->previous_status != $this->get_status() ) $this->set_status($this->get_status());
					$this->feedback_message = sprintf(__('Successfully saved %s','events-manager'),__('Event','events-manager'));
				}
				//check anonymous submission information
    			if( !empty($this->event_owner_anonymous) && get_option('dbem_events_anonymous_user') != $this->event_owner ){
    			    //anonymous user owner has been replaced with a valid wp user account, so we remove anonymous status flag but leave email and name for future reference
    			    update_post_meta($this->post_id, '_event_owner_anonymous', 0);
    			}elseif( get_option('dbem_events_anonymous_submissions') && get_option('dbem_events_anonymous_user') == $this->event_owner && is_email($this->event_owner_email) && !empty($this->event_owner_name) ){
    			    //anonymous user account has been reinstated as the owner, so we can restore anonymous submission status
    			    update_post_meta($this->post_id, '_event_owner_anonymous', 1);
    			}
			}
			//update event location via post meta
			if( $this->has_event_location() ){
				$this->get_event_location()->save();
			}elseif( !empty($this->event_location) ){
				// we previously had an event location and then switched to no location or a physical location
				$this->event_location->delete();
			}
			if( !empty($this->event_location_deleted) ){
				// we've switched event location types
				$this->event_location_deleted->delete();
			}
			//Add/Delete Tickets
			if($this->event_rsvp == 0){
				if( !$this->just_added_event ){
					$this->get_bookings()->delete();
					$this->get_tickets()->delete();
				}
			}elseif( $this->can_manage('manage_bookings','manage_others_bookings') ){
				if( !$this->get_bookings()->get_tickets()->save() ){
					$this->add_error( $this->get_bookings()->get_tickets()->get_errors() );
				}
			}
			$result = count($this->errors) == 0;
			//deal with categories
			if( get_option('dbem_categories_enabled') ){
				if( EM_MS_GLOBAL ){ //EM_MS_Globals should look up original blog
					//If we're saving event categories in MS Global mode, we'll add them here, saving by term id (cat ids are gone now)
	                if( !is_main_site() ){
	                    $this->get_categories()->save(); //it'll know what to do
	                }else{
	                    $this->get_categories()->save_index(); //just save to index, we assume cats are saved in $this->save();
	                }
				}elseif( get_option('dbem_default_category') > 0 ){
					//double-check for default category in other instances
					if( count($this->get_categories()) == 0 ){
						$this->get_categories()->save(); //let the object deal with this...
					}
				}
			}
		    $this->compat_keys(); //compatability keys, loaded before saving recurrences
			//build recurrences if needed
			if( $this->is_recurring() && $result && ($this->is_published() || $this->post_status == 'future' || (defined('EM_FORCE_RECURRENCES_SAVE') && EM_FORCE_RECURRENCES_SAVE)) ){ //only save events if recurring event validates and is published or set for future
				global $EM_EVENT_SAVE_POST;
				//If we're in WP Admin and this was called by EM_Event_Post_Admin::save_post, don't save here, it'll be done later in EM_Event_Recurring_Post_Admin::save_post
				if( empty($EM_EVENT_SAVE_POST) ){
					if( $this->just_added_event ) $this->recurring_reschedule = true;
				 	if( !$this->save_events() ){
						$this->add_error(__ ( 'Something went wrong with the recurrence update...', 'events-manager'). __ ( 'There was a problem saving the recurring events.', 'events-manager'));
				 	}
				}
			}
			if( !empty($this->just_added_event) ){
				do_action('em_event_added', $this);
			}
			// set active statuses if changed
			if( $this->event_active_status === 0 && $this->previous_active_status !== 0 ){
				$this->cancel();
			}
		}
		$EM_SAVING_EVENT = false;
		return apply_filters('em_event_save_meta', count($this->errors) == 0, $this);
	}
	
	/**
	 * Duplicates this event and returns the duplicated event. Will return false if there is a problem with duplication.
	 * @return EM_Event
	 */
	function duplicate(){
		global $wpdb;
		//First, duplicate.
		if( $this->can_manage('edit_events','edit_others_events') ){
			$EM_Event = clone $this;
			if( get_option('dbem_categories_enabled') ) $EM_Event->get_categories(); //before we remove event/post ids
			$EM_Event->get_bookings()->get_tickets(); //in case this wasn't loaded and before we reset ids
			$EM_Event->event_id = null;
			$EM_Event->post_id = null;
			$EM_Event->ID = null;
			$EM_Event->post_name = '';
			$EM_Event->location_id = (empty($EM_Event->location_id)  && !get_option('dbem_require_location')) ? 0:$EM_Event->location_id;
			$EM_Event->get_bookings()->event_id = null;
			$EM_Event->get_bookings()->get_tickets()->event_id = null;
			//if bookings reset ticket ids and duplicate tickets
			foreach($EM_Event->get_bookings()->get_tickets()->tickets as $EM_Ticket){
				$EM_Ticket->ticket_id = null;
				$EM_Ticket->event_id = null;
			}
			do_action('em_event_duplicate_pre', $EM_Event, $this);
			$EM_Event->duplicated = true;
			$EM_Event->force_status = 'draft';
			if( $EM_Event->save() ){
				$EM_Event->feedback_message = sprintf(__("%s successfully duplicated.", 'events-manager'), __('Event','events-manager'));
				//save tags here - eventually will be moved into part of $this->save();
				if( get_option('dbem_tags_enabled') ){
					$EM_Tags = new EM_Tags($this);
					$EM_Tags->event_id = $EM_Event->event_id;
					$EM_Tags->post_id = $EM_Event->post_id;
					$EM_Tags->save();
				}
			 	//other non-EM post meta inc. featured image
				$event_meta = $this->get_event_meta($this->blog_id);
				$new_event_meta = $EM_Event->get_event_meta($EM_Event->blog_id);
				$event_meta_inserts = array();
			 	//Get custom fields and post meta - adapted from $this->load_post_meta()
			 	foreach($event_meta as $event_meta_key => $event_meta_vals){
			 		if( $event_meta_key == '_wpas_' ) continue; //allow JetPack Publicize to detect this as a new post when published
			 		if( is_array($event_meta_vals) ){
			 		    if( !array_key_exists($event_meta_key, $new_event_meta) &&  !in_array($event_meta_key, array('_event_attributes', '_edit_last', '_edit_lock', '_event_owner_name','_event_owner_anonymous','_event_owner_email')) ){
				 			foreach($event_meta_vals as $event_meta_val){
				 			    $event_meta_inserts[] = "({$EM_Event->post_id}, '{$event_meta_key}', '{$event_meta_val}')";
				 			}
			 			}
			 		}
			 	}
			 	//save in one SQL statement
			 	if( !empty($event_meta_inserts) ){
			 		$wpdb->query('INSERT INTO '.$wpdb->postmeta." (post_id, meta_key, meta_value) VALUES ".implode(', ', $event_meta_inserts));
			 	}
				if( array_key_exists('_event_approvals_count', $event_meta) ) update_post_meta($EM_Event->post_id, '_event_approvals_count', 0);
				//copy anything from the em_meta table too
				$wpdb->query('INSERT INTO '.EM_META_TABLE." (object_id, meta_key, meta_value) SELECT '{$EM_Event->event_id}', meta_key, meta_value FROM ".EM_META_TABLE." WHERE object_id='{$this->event_id}'");
			 	//set event to draft status
				return apply_filters('em_event_duplicate', $EM_Event, $this);
			}
		}
		//TODO add error notifications for duplication failures.
		return apply_filters('em_event_duplicate', false, $this);;
	}
	
	function duplicate_url($raw = false){
	    $url = add_query_arg(array('action'=>'event_duplicate', 'event_id'=>$this->event_id, '_wpnonce'=> wp_create_nonce('event_duplicate_'.$this->event_id)));
	    $url = apply_filters('em_event_duplicate_url', $url, $this);
	    $url = $raw ? esc_url_raw($url):esc_url($url);
	    return $url;
	}
	
	/**
	 * Delete whole event, including bookings, tickets, etc.
	 * @param boolean $force_delete
	 * @return boolean
	 */
	function delete( $force_delete = false ){
		if( $this->can_manage('delete_events', 'delete_others_events') ){
		    if( !is_admin() ){
				include_once('em-event-post-admin.php');
				if( !defined('EM_EVENT_DELETE_INCLUDE') ){
					EM_Event_Post_Admin::init();
					EM_Event_Recurring_Post_Admin::init();
					define('EM_EVENT_DELETE_INCLUDE',true);
				}
		    }
		    do_action('em_event_delete_pre', $this);
			if( $force_delete ){
				$result = wp_delete_post($this->post_id,$force_delete);
			}else{
				$result = wp_trash_post($this->post_id);
				if( !$result && $this->post_status == 'trash' ){
				    //we're probably dealing with a trashed post already, but the event_status is null from < v5.4.1
				    $this->set_status(-1);
				    $result = true;
				}
			}
			if( !$result && !empty($this->orphaned_event) ){
			    //this is an orphaned event, so the wp delete posts would have never worked, so we just delete the row in our events table
				$result = $this->delete_meta();
			}
		}else{
			$result = false;
		}
		return apply_filters('em_event_delete', $result != false, $this);
	}
	
	function delete_meta(){
		global $wpdb;
		$result = false;
		if( $this->can_manage('delete_events', 'delete_others_events') ){
			do_action('em_event_delete_meta_event_pre', $this);
			$result = $wpdb->query ( $wpdb->prepare("DELETE FROM ". EM_EVENTS_TABLE ." WHERE event_id=%d", $this->event_id) );
			if( $result !== false ){
				$this->get_bookings()->delete();
				$this->get_tickets()->delete();
				if( $this->has_event_location() ) {
					$this->get_event_location()->delete();
				}
				//Delete the recurrences then this recurrence event
				if( $this->is_recurring() ){
					$result = $this->delete_events(); //was true at this point, so false if fails
				}
				//Delete categories from meta if in MS global mode
				if( EM_MS_GLOBAL ){
					$wpdb->query('DELETE FROM '.EM_META_TABLE.' WHERE object_id='.$this->event_id." AND meta_key='event-category'");
				} 
			}
		}
		return apply_filters('em_event_delete_meta', $result !== false, $this);
	}
	
	/**
	 * Deprecated, use $this->get_bookings->delete() instead.
	 * Shortcut function for $this->get_bookings()->delete(), because using the EM_Bookings requires loading previous bookings, which isn't neceesary. 
	 */
	function delete_bookings(){
		global $wpdb;
		do_action('em_event_delete_bookings_pre', $this);
		$result = false;
		if( $this->can_manage('manage_bookings','manage_others_bookings') ){
			$result_bt = $wpdb->query( $wpdb->prepare("DELETE FROM ".EM_TICKETS_BOOKINGS_TABLE." WHERE booking_id IN (SELECT booking_id FROM ".EM_BOOKINGS_TABLE." WHERE event_id=%d)", $this->event_id) );
			$result = $wpdb->query( $wpdb->prepare("DELETE FROM ".EM_BOOKINGS_TABLE." WHERE event_id=%d", $this->event_id) );
		}
		return apply_filters('em_event_delete_bookings', $result !== false && $result_bt !== false, $this);
	}
	
	/**
	 * Deprecated, use $this->get_bookings->delete() instead.
	 * Shortcut function for $this->get_bookings()->delete(), because using the EM_Bookings requires loading previous bookings, which isn't neceesary. 
	 */
	function delete_tickets(){
		global $wpdb;
		do_action('em_event_delete_tickets_pre', $this);
		$result = false;
		if( $this->can_manage('manage_bookings','manage_others_bookings') ){
			$result_bt = $wpdb->query( $wpdb->prepare("DELETE FROM ".EM_TICKETS_BOOKINGS_TABLE." WHERE ticket_id IN (SELECT ticket_id FROM ".EM_TICKETS_TABLE." WHERE event_id=%d)", $this->event_id) );
			$result = $wpdb->query( $wpdb->prepare("DELETE FROM ".EM_TICKETS_TABLE." WHERE event_id=%d", $this->event_id) );
		}
		return apply_filters('em_event_delete_tickets', $result, $this);
	}
	
	/**
	 * Change the status of the event. This will save to the Database too. 
	 * @param int $status 				A number to change the status to, which may be -1 for trash, 1 for publish, 0 for pending or null if draft.
	 * @param boolean $set_post_status 	If set to true the wp_posts table status will also be changed to the new corresponding status.
	 * @return string
	 */
	function set_status($status, $set_post_status = false){
		global $wpdb;
		//decide on what status to set and update wp_posts in the process
		if( EM_MS_GLOBAL ) switch_to_blog( $this->blog_id );
		if($status === null){ 
			$set_status='NULL'; //draft post
			if($set_post_status){
				//if the post is trash, don't untrash it!
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $this->post_id ) );
			}
			$this->post_status = 'draft'; //set post status in this instance
		}elseif( $status == -1 ){ //trashed post
			$set_status = -1;
			if($set_post_status){
				//set the post status of the location in wp_posts too
				$wpdb->update( $wpdb->posts, array( 'post_status' => $this->post_status ), array( 'ID' => $this->post_id ) );
			}
			$this->post_status = 'trash'; //set post status in this instance
		}else{
			$set_status = $status ? 1:0; //published or pending post
			$post_status = $set_status ? 'publish':'pending';
			if( empty($this->post_name) ){
				//published or pending posts should have a valid post slug
				$slug = sanitize_title($this->post_title);
				$this->post_name = wp_unique_post_slug( $slug, $this->post_id, $post_status, EM_POST_TYPE_EVENT, 0);
				$set_post_name = true;
			}
			if($set_post_status){
				$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status, 'post_name' => $this->post_name ), array( 'ID' => $this->post_id ) );
			}elseif( !empty($set_post_name) ){
				//if we've added a post slug then update wp_posts anyway
				$wpdb->update( $wpdb->posts, array( 'post_name' => $this->post_name ), array( 'ID' => $this->post_id ) );
			}
			$this->post_status = $post_status;
		}
		if( EM_MS_GLOBAL ) restore_current_blog();
		//save in the wp_em_locations table
		$this->get_previous_status();
		$result = $wpdb->query( $wpdb->prepare("UPDATE ".EM_EVENTS_TABLE." SET event_status=$set_status, event_slug=%s WHERE event_id=%d", array($this->post_name, $this->event_id)) );
		$this->get_status(); //reload status
		return apply_filters('em_event_set_status', $result !== false, $status, $this);
	}
	
	public function cancel(){
		return $this->set_active_status( 0 );
	}
	
	public function set_active_status( $active_status ){
		global $wpdb;
		if( is_int($active_status) && $active_status >= 0 ){
			$em_result = $wpdb->update( EM_EVENTS_TABLE, array('event_active_status' => $active_status ), array( 'event_id' => $this->event_id ), array('%d'), array('%d') );
			if( EM_MS_GLOBAL ) switch_to_blog( $this->blog_id );
			$meta_result = $wpdb->update( $wpdb->postmeta, array( 'meta_key' => '_event_active_status', 'meta_value' => $active_status ), array( 'meta_key' => '_event_active_status', 'post_id' => $this->post_id ), array('%s', '%d'), array('%s', '%d') );
			if( EM_MS_GLOBAL ) restore_current_blog();
			$result = $em_result !== false && $meta_result !== false;
			if( $result ){
				$this->previous_active_status = $this->event_active_status;
				$this->event_active_status = $active_status;
				if( $active_status === 0 ) {
					// cancelled event, let's cancel bookings and send out emails (if set)
					if ( get_option( 'dbem_event_cancelled_bookings' ) ) {
						$bookings_array = array(
							$this->get_bookings()->get_bookings(),
							$this->get_bookings()->get_pending_bookings()
						);
						foreach( $bookings_array as $EM_Bookings ) {
							foreach ( $EM_Bookings as $EM_Booking ) {
								$EM_Booking->manage_override = true;
								$EM_Booking->cancel( get_option( 'dbem_event_cancelled_bookings_email' ), array( 'email_admin' => false ) );
							}
						}
					}
					if ( get_option('dbem_event_cancelled_email') ) {
						if( !isset($bookings_array) ) {
							$bookings_array = array(
								$this->get_bookings()->get_bookings(),
								$this->get_bookings()->get_pending_bookings()
							);
						}
						foreach( $bookings_array as $EM_Bookings ) {
							foreach ( $EM_Bookings as $EM_Booking ) {
								$message = array(
									'user' => array(
										'subject' => get_option('dbem_event_cancelled_email_subject'),
										'body' => get_option('dbem_event_cancelled_email_body'),
									),
								);
								$EM_Booking->email_attendee( $message );
							}
						}
					}
				}
			}
			return apply_filters('em_event_set_active_status', $result, $active_status, $this);
		}
		return false;
	}
	
	public function set_timezone( $timezone = false ){
		//reset UTC times and objects so they're recreated with local time and new timezone
		$this->event_start = $this->event_end = $this->start = $this->end = $this->rsvp_end = null;
		$EM_DateTimeZone = EM_DateTimeZone::create($timezone);
		//modify the timezone string name itself
		$this->event_timezone = $EM_DateTimeZone->getName();
	}
	
	public function get_timezone(){
		return $this->start()->getTimezone();
	}
	
	function is_published(){
		return apply_filters('em_event_is_published', ($this->post_status == 'publish' || $this->post_status == 'private'), $this);
	}
	
	/**
	 * Returns an EM_DateTime object of the event start date/time in local timezone of event.
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime
	 * @see EM_Event::get_datetime()
	 */
	public function start( $utc_timezone = false ){
		return apply_filters('em_event_start', $this->get_datetime('start', $utc_timezone), $this);
	}
	
	/**
	 * Returns an EM_DateTime object of the event end date/time in local timezone of event
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime
	 * @see EM_Event::get_datetime()
	 */
	public function end( $utc_timezone = false ){
		return apply_filters('em_event_end', $this->get_datetime('end', $utc_timezone), $this);
	}
	
	/**
	 * Returns an EM_DateTime representation of when bookings close in local event timezone. If no valid date defined, event start date/time will be used.
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default.
	 * @return EM_DateTime
	 */
	public function rsvp_end( $utc_timezone = false ){
		if( empty($this->rsvp_end) || !$this->rsvp_end->valid ){
			if( !empty($this->event_rsvp_date ) ){
				$rsvp_time = !empty($this->event_rsvp_time) ? $this->event_rsvp_time : $this->event_start_time;
			    $this->rsvp_end = new EM_DateTime($this->event_rsvp_date." ".$rsvp_time, $this->event_timezone);
			    if( !$this->rsvp_end->valid ){
			    	//invalid date will revert to start time
			    	$this->rsvp_end = $this->start()->copy();
			    }
			}else{
				//no date defined means event start date/time is used
		    	$this->rsvp_end = $this->start()->copy();
		    }
		}
		//Set to UTC timezone if requested, local by default
		$tz = $utc_timezone ? 'UTC' : $this->event_timezone;
		$this->rsvp_end->setTimezone($tz);
		return $this->rsvp_end;
	}
	
	/**
	 * Generates an EM_DateTime for the the start/end date/times of the event in local timezone, as well as setting a valid flag if dates and times are valid.
	 * The generated object will be derived from the local date and time values. If no date exists, then 1970-01-01 will be used, and 00:00:00 if no valid time exists. 
	 * If date is invalid but time is, only use local timezones since a UTC conversion will provide inaccurate timezone differences due to unknown DST status.	 * 
	 * @param string $when 'start' or 'end' date/time
	 * @param bool $utc_timezone Returns EM_DateTime with UTC timezone if set to true, returns local timezone by default. Do not use if EM_DateTime->valid is false. 
	 * @return EM_DateTime
	 */
	public function get_datetime( $when = 'start', $utc_timezone = false ){
		if( $when != 'start' && $when != 'end') return new EM_DateTime(); //currently only start/end dates are relevant
		//Initialize EM_DateTime if not already initialized, or if previously initialized object is invalid (e.g. draft event with invalid dates being resubmitted)
		$when_date = 'event_'.$when.'_date';
		$when_time = 'event_'.$when.'_time';
		//we take a pass at creating a new datetime object if it's empty, invalid or a different time to the current start date
		if( empty($this->$when) || !$this->$when->valid ){
			$when_utc = 'event_'.$when;
			$date_regex = '/^\d{4}-\d{2}-\d{2}$/';
			$valid_time = !empty($this->$when_time) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $this->$when_time);
			//If there now is a valid date string for local or UTC timezones, create a new object which will set the valid flag to true by default
			if( !empty($this->$when_date) && preg_match($date_regex, $this->$when_date) && $valid_time ){
				$EM_DateTime = new EM_DateTime(trim($this->$when_date.' '.$this->$when_time), $this->event_timezone);
				if( $EM_DateTime->valid && empty($this->$when_utc) ){
					$EM_DateTime->setTimezone('UTC');
					$this->$when_utc = $EM_DateTime->format();
				}
			}
			//If we didn't attempt to create a date above, or it didn't work out, create an invalid date based on time.
			if( empty($EM_DateTime) || !$EM_DateTime->valid ){
				//create a new datetime just with the time (if set), fake date and set the valid flag to false
				$time = $valid_time ? $this->$when_time : '00:00:00';
				$EM_DateTime = new EM_DateTime('1970-01-01 '.$time, $this->event_timezone);
				$EM_DateTime->valid = false;
			} 
			//set new datetime
			$this->$when = $EM_DateTime;
		}else{
			/* @var EM_DateTime $EM_DateTime */
			$EM_DateTime = $this->$when;
		}
		//Set to UTC timezone if requested, local by default
		$tz = $utc_timezone ? 'UTC' : $this->event_timezone;
		$EM_DateTime->setTimezone($tz);
		return $EM_DateTime;
	}
	
	function get_status($db = false){
		switch( $this->post_status ){
			case 'private':
				$this->event_private = 1;
				$this->event_status = $status = 1;
				break;
			case 'publish':
				$this->event_private = 0;
				$this->event_status = $status = 1;
				break;
			case 'pending':
				$this->event_private = 0;
				$this->event_status = $status = 0;
				break;
			case 'trash':
				$this->event_private = 0;
				$this->event_status = $status = -1;
				break;
			default: //draft or unknown
				$this->event_private = 0;
				$status = $db ? 'NULL':null;
				$this->event_status = null;
				break;
		}
		return apply_filters('em_event_get_status', $status, $this);
	}
	
	function get_previous_status( $force = false ){
		global $wpdb;
		if( $this->event_id > 0 && ($this->previous_status === false || $force) ){
			$this->previous_status = $wpdb->get_var('SELECT event_status FROM '.EM_EVENTS_TABLE.' WHERE event_id='.$this->event_id); //get status from db, not post_status, as posts get saved quickly
		}
		return $this->previous_status;
	}
	
	function get_active_status(){
		if ( !get_option('dbem_event_status_enabled') ) {
			return __('Active', 'events-manager');
		}
		switch( absint($this->event_active_status) ){
			case 0:
				$status = __('Cancelled', 'events-manager');
				break;
			default: // active
				$status = __('Active', 'events-manager');
				break;
		}
		return apply_filters('em_event_get_active_status', $status, $this);
	}
	
	/**
	 * Returns an EM_Categories object of the EM_Event instance.
	 * @return EM_Categories
	 */
	function get_categories() {
		if( empty($this->categories) ){
			$this->categories = new EM_Categories($this);
		}elseif(empty($this->categories->event_id)){
			$this->categories->event_id = $this->event_id;
			$this->categories->post_id = $this->post_id;			
		}
		return apply_filters('em_event_get_categories', $this->categories, $this);
	}
	
	
	/**
	 * Returns an array of colors of this event based on the category assigned. Will return a pre-formatted CSS variables assignment for use in the style attribute of HTML elements.
	 * @param bool $css_vars
	 * @return array|string
	 */
	public function get_colors( $css_vars = false ){
		$orig_color = get_option('dbem_category_default_color');
		$color = $borderColor = $orig_color;
		$textColor = '#fff';
		if ( get_option('dbem_categories_enabled') && !empty ( $this->get_categories()->categories )) {
			foreach($this->get_categories()->categories as $EM_Category){
				/* @public $EM_Category EM_Category */
				if( $EM_Category->get_color() != '' ){
					$color = $borderColor = $EM_Category->get_color();
					if( preg_match("/#fff(fff)?/i",$color) ){
						$textColor = '#777';
						$borderColor = '#ccc';
					}
					break;
				}
			}
		}
		$event_color = array(
			'background-color' => $color,
			'border-color' => $borderColor,
			'color' => $textColor,
		);
		$event_color = apply_filters('em_event_get_colors', $event_color, $this);
		if( $css_vars ){
			// get event colors
			$css_color_vars = array();
			foreach( $event_color as $k => $v ){
				$css_color_vars[] = '--event-'.$k.':'.$v.';';
			}
			return implode(';', $css_color_vars);
		}else{
			return $event_color;
		}
	}
	
	/**
	 * Gets the parent of this event, if none exists, null is returned.
	 * @return EM_Event|null
	 */
	public function get_parent(){
		if( $this->event_parent ){
			return em_get_event( $this->event_parent );
		}
		return null;
	}
	
	/**
	 * Returns the physical location object this event belongs to.
	 * @return EM_Location
	 */
	function get_location() {
		global $EM_Location;
		if( is_object($EM_Location) && $EM_Location->location_id == $this->location_id ){
			$this->location = $EM_Location;
		}else{
			if( !is_object($this->location) || $this->location->location_id != $this->location_id ){
				$this->location = apply_filters('em_event_get_location', em_get_location($this->location_id), $this);
			}
		}
		return $this->location;
	}
	
	/**
	 * Returns whether this event has a phyisical location assigned to it.
	 * @return bool
	 */
	public function has_location(){
		return !empty($this->location_id) || (!empty($this->location) && !empty($this->location->location_name));
	}
	
	/**
	 * Gets the event's event location (note, different from a regular event location, which uses get_location())
	 * Returns implementation of Event_Location or false if no event location assigned.
	 * @return EM_Event_Locations\URL|Event_Location|false
	 */
	public function get_event_location(){
		if( is_object($this->event_location) && $this->event_location->type == $this->event_location_type ) return $this->event_location;
		$Event_Location = false;
		if( $this->has_event_location() ){
			$this->event_location = $Event_Location = Event_Locations::get( $this->event_location_type, $this );
		}
		return apply_filters('em_event_get_event_location', $Event_Location, $this);
	}
	
	/**
	 * Returns whether the event has an event location associated with it (different from a physical location). If supplied, can check against a specific type.
	 * @param string $event_location_type
	 * @return bool
	 */
	public function has_event_location( $event_location_type = null ){
		if( $event_location_type !== null ){
			return !empty($this->event_location_type) && $this->event_location_type === $event_location_type && Event_Locations::is_enabled($event_location_type);
		}
		return !empty($this->event_location_type) && Event_Locations::is_enabled($this->event_location_type);
	}
	
	/**
	 * Returns the location object this event belongs to.
	 * @return EM_Person
	 */	
	function get_contact(){
		if( !is_object($this->contact) ){
			$this->contact = new EM_Person($this->event_owner);
			//if this is anonymous submission, change contact email and name
			if( $this->event_owner_anonymous ){
				$this->contact->user_email = $this->event_owner_email;
				$name = explode(' ',$this->event_owner_name);
				$first_name = array_shift($name);
				$last_name = (count($name) > 0) ? implode(' ',$name):'';
				$this->contact->user_firstname = $this->contact->first_name = $first_name;
				$this->contact->user_lastname = $this->contact->last_name = $last_name;
				$this->contact->display_name = $this->event_owner_name;
			}
		}
		return $this->contact;
	}
	
	/**
	 * Retrieve and save the bookings belonging to instance. If called again will return cached version, set $force_reload to true to create a new EM_Bookings object.
	 * @param boolean $force_reload
	 * @return EM_Bookings
	 */
	function get_bookings( $force_reload = false ){
		if( get_option('dbem_rsvp_enabled') ){
			if( (!$this->bookings || $force_reload) ){
				$this->bookings = new EM_Bookings($this);
			}
			$this->bookings->event_id = $this->event_id; //always refresh event_id
			$this->bookings = apply_filters('em_event_get_bookings', $this->bookings, $this);
		}else{
			return new EM_Bookings();
		}
		//TODO for some reason this returned instance doesn't modify the original, e.g. try $this->get_bookings()->add($EM_Booking) and see how $this->bookings->feedback_message doesn't change
		return $this->bookings;
	}
	
	/**
	 * Get the tickets related to this event.
	 * @param boolean $force_reload
	 * @return EM_Tickets
	 */
	function get_tickets( $force_reload = false ){
		return $this->get_bookings($force_reload)->get_tickets();
	}

	/* Provides the tax rate for this event.
	 * @see EM_Object::get_tax_rate()
	 */
	function get_tax_rate( $decimal = false ){
		$tax_rate = apply_filters('em_event_get_tax_rate', parent::get_tax_rate( false ), $this); //we get tax rate but without decimal
		$tax_rate = ( $tax_rate > 0 ) ? $tax_rate : 0;
		if( $decimal && $tax_rate > 0 ) $tax_rate = $tax_rate / 100;
		return $tax_rate;
	}
	
	/**
	 * Deprecated - use $this->get_bookings()->get_spaces() instead.
	 * Gets number of spaces in this event, dependent on ticket spaces or hard limit, whichever is smaller.
	 * @param boolean $force_refresh
	 * @return int 
	 */
	function get_spaces($force_refresh=false){
		return $this->get_bookings()->get_spaces($force_refresh);
	}
	
	/* 
	 * Extends the default EM_Object function by switching blogs as needed if in MS Global mode  
	 * @param string $size
	 * @return string
	 * @see EM_Object::get_image_url()
	 */
	function get_image_url($size = 'full'){
	    if( EM_MS_GLOBAL && get_current_blog_id() != $this->blog_id ){
	        switch_to_blog($this->blog_id);
	        $switch_back = true;
	    }
		$return = parent::get_image_url($size);
		if( !empty($switch_back) ){ restore_current_blog(); }
		return $return;
	}
	
	function get_edit_reschedule_url(){
		if( $this->is_recurrence() ){
			$EM_Event = em_get_event($this->recurrence_id);
			return $EM_Event->get_edit_url();
		}
	}
	
	function get_edit_url(){
		if( $this->can_manage('edit_events','edit_others_events') ){
			if( EM_MS_GLOBAL && get_site_option('dbem_ms_global_events_links') && !empty($this->blog_id) && is_main_site() && $this->blog_id != get_current_blog_id() ){
				if( get_blog_option($this->blog_id, 'dbem_edit_events_page') ){
					$link = em_add_get_params(get_permalink(get_blog_option($this->blog_id, 'dbem_edit_events_page')), array('action'=>'edit','event_id'=>$this->event_id), false);
				}
				if( empty($link))
					$link = get_admin_url($this->blog_id, "post.php?post={$this->post_id}&action=edit");
			}else{
				if( get_option('dbem_edit_events_page') && !is_admin() ){
					$link = em_add_get_params(get_permalink(get_option('dbem_edit_events_page')), array('action'=>'edit','event_id'=>$this->event_id), false);
				}
				if( empty($link))
					$link = admin_url()."post.php?post={$this->post_id}&action=edit";
			}
			return apply_filters('em_event_get_edit_url', $link, $this);
		}
	}
	
	function get_bookings_url(){
		if( get_option('dbem_edit_bookings_page') && (!is_admin() || !empty($_REQUEST['is_public'])) ){
			$my_bookings_page = get_permalink(get_option('dbem_edit_bookings_page'));
			$bookings_link = em_add_get_params($my_bookings_page, array('event_id'=>$this->event_id), false);
		}else{
			if( is_multisite() && $this->blog_id != get_current_blog_id() ){
				$bookings_link = get_admin_url($this->blog_id, 'edit.php?post_type='.EM_POST_TYPE_EVENT."&page=events-manager-bookings&event_id=".$this->event_id);
			}else{
				$bookings_link = EM_ADMIN_URL. "&page=events-manager-bookings&event_id=".$this->event_id;
			}
		}
		return apply_filters('em_event_get_bookings_url', $bookings_link, $this);
	}
	
	function get_permalink(){
		if( EM_MS_GLOBAL ){
			//if no blog id defined, assume it's the main blog
			$blog_id = empty($this->blog_id) ? get_current_site()->blog_id:$this->blog_id;
			//if we're not on the same blog as this event then decide whether to link to main blog or to source blog 
			if( $blog_id != get_current_blog_id() ){
				if( !get_site_option('dbem_ms_global_events_links') && is_main_site() &&  get_option('dbem_events_page') ){
					//if on main site, and events page exists and direct links are disabled then show link to main site
					$event_link = trailingslashit(get_permalink(get_option('dbem_events_page')).get_site_option('dbem_ms_events_slug',EM_EVENT_SLUG).'/'.$this->event_slug.'-'.$this->event_id);					
				}else{
					//linking directly to the source blog by default
					$event_link = get_blog_permalink( $blog_id, $this->post_id);
				}
			}
		}
		if( empty($event_link) ){
			$event_link = get_post_permalink($this->post_id);
		}
		return apply_filters('em_event_get_permalink', $event_link, $this);
	}
	
	function get_ical_url(){
		global $wp_rewrite;
		if( !empty($wp_rewrite) && $wp_rewrite->using_permalinks() ){
			$return = trailingslashit($this->get_permalink()).'ical/';
		}else{
			$return = em_add_get_params($this->get_permalink(), array('ical'=>1));
		}
		return apply_filters('em_event_get_ical_url', $return);
	}
	
	function is_free( $now = false ){
		$free = true;
		foreach($this->get_tickets() as $EM_Ticket){
		    /* @public $EM_Ticket EM_Ticket */
			if( $EM_Ticket->get_price() > 0 ){
				if( !$now || $EM_Ticket->is_available() ){	
				    $free = false;
				}
			}
		}
		return apply_filters('em_event_is_free',$free, $this, $now);
	}
	
	/**
	 * Will output a single event format of this event. 
	 * Equivalent of calling EM_Event::output( get_option ( 'dbem_single_event_format' ) )
	 * @param string $target
	 * @return string
	 */
	function output_single($target='html'){
		$format = get_option ( 'dbem_single_event_format' );
		return apply_filters('em_event_output_single', $this->output($format, $target), $this, $target);
	}
	
	/**
	 * Will output a event in the format passed in $format by replacing placeholders within the format.
	 * @param string $format
	 * @param string $target
	 * @return string
	 */	
	function output($format, $target="html") {	
		global $wpdb;
		//$format = do_shortcode($format); //parse shortcode first, so that formats within shortcodes are parsed properly, however uncommenting this will break shortcode containing placeholders for arguments
	 	$event_string = $format;
		//Time place holder that doesn't show if empty.
		preg_match_all('/#@?__?\{[^}]+\}/', $format, $results);
		foreach($results[0] as $result) {
			if(substr($result, 0, 3 ) == "#@_"){
				$date = 'end';
				if( substr($result, 0, 4 ) == "#@__" ){
					$offset = 5;
					$show_site_timezone = true;
				}else{
					$offset = 4;
				}
			}else{
				$date = 'start';
				if( substr($result, 0, 3) == "#__" ){
					$offset = 4;
					$show_site_timezone = true;
				}else{
					$offset = 3;
				}
			}
			if( $date == 'end' && $this->event_start_date == $this->event_end_date ){
				$replace = apply_filters('em_event_output_placeholder', '', $this, $result, $target, array($result));
			}else{
				$date_format = substr( $result, $offset, (strlen($result)-($offset+1)) );
				if( !empty($show_site_timezone) ){
					$date_formatted = $this->$date()->copy()->setTimezone()->i18n($date_format);
				}else{
					$date_formatted = $this->$date()->i18n($date_format);
				}
				$replace = apply_filters('em_event_output_placeholder', $date_formatted, $this, $result, $target, array($result));
			}
			$event_string = str_replace($result,$replace,$event_string );
		}
		//This is for the custom attributes
		preg_match_all('/#_ATT\{([^}]+)\}(\{([^}]+\}?)\})?/', $event_string, $results);
		$attributes = em_get_attributes();
		foreach($results[0] as $resultKey => $result) {
			//check that we haven't mistakenly captured a closing bracket in second bracket set
			if( !empty($results[3][$resultKey]) && $results[3][$resultKey][0] == '/' ){
				$result = $results[0][$resultKey] = str_replace($results[2][$resultKey], '', $result);
				$results[3][$resultKey] = $results[2][$resultKey] = '';
			}
			//Strip string of placeholder and just leave the reference
			$attRef = substr( substr($result, 0, strpos($result, '}')), 6 );
			$attString = '';
			$placeholder_atts = array('#_ATT', $results[1][$resultKey]);
			if( is_array($this->event_attributes) && array_key_exists($attRef, $this->event_attributes) ){
				$attString = $this->event_attributes[$attRef];
			}elseif( !empty($results[3][$resultKey]) ){
				//Check to see if we have a second set of braces;
				$placeholder_atts[] = $results[3][$resultKey];
				$attStringArray = explode('|', $results[3][$resultKey]);
				$attString = $attStringArray[0];
			}elseif( !empty($attributes['values'][$attRef][0]) ){
			    $attString = $attributes['values'][$attRef][0];
			}
			$attString = apply_filters('em_event_output_placeholder', $attString, $this, $result, $target, $placeholder_atts);
			$event_string = str_replace($result, $attString ,$event_string );
		}
	 	//First let's do some conditional placeholder removals
	 	for ($i = 0 ; $i < EM_CONDITIONAL_RECURSIONS; $i++){ //you can add nested recursions by modifying this setting in your wp_options table
			preg_match_all('/\{([a-zA-Z0-9_\-,]+)\}(.+?)\{\/\1\}/s', $event_string, $conditionals);
			if( count($conditionals[0]) > 0 ){
				//Check if the language we want exists, if not we take the first language there
				foreach($conditionals[1] as $key => $condition){
					$show_condition = false;
					if ($condition == 'has_bookings') {
						//check if there's a booking, if not, remove this section of code.
						$show_condition = ($this->event_rsvp && get_option('dbem_rsvp_enabled'));
					}elseif ($condition == 'no_bookings') {
						//check if there's a booking, if not, remove this section of code.
						$show_condition = (!$this->event_rsvp && get_option('dbem_rsvp_enabled'));
					}elseif ($condition == 'no_location'){
						//does this event have a valid location?
						$show_condition = !$this->has_event_location() && !$this->has_location();
					}elseif ($condition == 'has_location'){
						//does this event have a valid location?
						$show_condition = ( $this->has_location() && $this->get_location()->location_status ) || $this->has_event_location();
					}elseif ($condition == 'has_location_venue'){
						//does this event have a valid physical location?
						$show_condition = ( $this->has_location() && $this->get_location()->location_status ) || $this->has_location();
					}elseif ($condition == 'no_location_venue'){
						//does this event NOT have a valid physical location?
						$show_condition = !$this->has_location();
					}elseif ($condition == 'has_event_location'){
						//does this event have a valid event location?
						$show_condition = $this->has_event_location();
					}elseif ( preg_match('/^has_event_location_([a-zA-Z0-9_\-]+)$/', $condition, $type_match)){
						//event has a specific category
						$show_condition = $this->has_event_location($type_match[1]);
					}elseif ($condition == 'no_event_location'){
						//does this event not have a valid event location?
						$show_condition = !$this->has_event_location();
					}elseif ( preg_match('/^no_event_location_([a-zA-Z0-9_\-]+)$/', $condition, $type_match)){
						//does this event NOT have a specific event location?
						$show_condition = !$this->has_event_location($type_match[1]);
					}elseif ($condition == 'has_image'){
						//does this event have an image?
						$show_condition = ( $this->get_image_url() != '' );
					}elseif ($condition == 'no_image'){
						//does this event have an image?
						$show_condition = ( $this->get_image_url() == '' );
					}elseif ($condition == 'has_time'){
						//are the booking times different and not an all-day event
						$show_condition = ( $this->event_start_time != $this->event_end_time && !$this->event_all_day );
					}elseif ($condition == 'no_time'){
						//are the booking times exactly the same and it's not an all-day event.
						$show_condition = ( $this->event_start_time == $this->event_end_time && !$this->event_all_day );
					}elseif ($condition == 'different_timezone'){
						//current event timezone is different to blog timezone
						$show_condition = $this->event_timezone != EM_DateTimeZone::create()->getName();
					}elseif ($condition == 'same_timezone'){
						//current event timezone is different to blog timezone
						$show_condition = $this->event_timezone == EM_DateTimeZone::create()->getName();
					}elseif ($condition == 'all_day'){
						//is it an all day event
						$show_condition = !empty($this->event_all_day);
					}elseif ($condition == 'not_all_day'){
						//is not an all day event
						$show_condition = !empty($this->event_all_day);
					}elseif ($condition == 'logged_in'){
						//user is logged in
						$show_condition = is_user_logged_in();
					}elseif ($condition == 'not_logged_in'){
						//not logged in
						$show_condition = !is_user_logged_in();
					}elseif ($condition == 'has_spaces'){
						//there are still empty spaces
						$show_condition = $this->event_rsvp && $this->get_bookings()->get_available_spaces() > 0;
					}elseif ($condition == 'fully_booked'){
						//event is fully booked
						$show_condition = $this->event_rsvp && $this->get_bookings()->get_available_spaces() <= 0;
					}elseif ($condition == 'bookings_open'){
						//bookings are still open
						$show_condition = $this->event_rsvp && $this->get_bookings()->is_open();
					}elseif ($condition == 'bookings_closed'){
						//bookings are still closed
						$show_condition = $this->event_rsvp && !$this->get_bookings()->is_open();
					}elseif ($condition == 'is_free' || $condition == 'is_free_now'){
						//is it a free day event, if _now then free right now
						$show_condition = !$this->event_rsvp || $this->is_free( $condition == 'is_free_now' );
					}elseif ($condition == 'not_free' || $condition == 'not_free_now'){
						//is it a paid event, if _now then paid right now
						$show_condition = $this->event_rsvp && !$this->is_free( $condition == 'not_free_now' );
					}elseif ($condition == 'is_long'){
						//is it an all day event
						$show_condition = $this->event_start_date != $this->event_end_date;
					}elseif ($condition == 'not_long'){
						//is it an all day event
						$show_condition = $this->event_start_date == $this->event_end_date;
					}elseif ($condition == 'is_past'){
						//if event is past
						if( get_option('dbem_events_current_are_past') ){
						    $show_condition = $this->start()->getTimestamp() <= time();
						}else{
							$show_condition = $this->end()->getTimestamp() <= time();
						}
					}elseif ($condition == 'is_future'){
						//if event is upcoming
						$show_condition = $this->start()->getTimestamp() > time();
					}elseif ($condition == 'is_current'){
						//if event is currently happening
						$show_condition = $this->start()->getTimestamp() <= time() && $this->end()->getTimestamp() >= time();
					}elseif ($condition == 'is_recurring'){
						//if event is a recurring event
						$show_condition = $this->is_recurring();
					}elseif ($condition == 'not_recurring'){
						//if event is not a recurring event
						$show_condition = !$this->is_recurring();
					}elseif ($condition == 'is_recurrence'){
						//if event is a recurrence
						$show_condition = $this->is_recurrence();
					}elseif ($condition == 'not_recurrence'){
						//if event is not a recurrence
						$show_condition = !$this->is_recurrence();
					}elseif ($condition == 'is_private'){
						//if event is a recurrence
						$show_condition = $this->event_private == 1;
					}elseif ($condition == 'not_private'){
						//if event is not a recurrence
						$show_condition = $this->event_private == 0;
					}elseif ($condition == 'is_cancelled'){
						//if event is not a recurrence
						$show_condition = $this->event_active_status == 0;
					}elseif ($condition == 'is_active'){
						//if event is not a recurrence
						$show_condition = $this->event_active_status == 1;
					}elseif ( strpos($condition, 'is_user_attendee') !== false || strpos($condition, 'not_user_attendee') !== false ){
						//if current user has a booking at this event
						$show_condition = false;
						if( is_user_logged_in() ){
							//we only need a user id, booking id and booking status so we do a direct SQL lookup and once for the loop
							if( !isset($user_bookings) || !is_array($user_bookings) ){
								$sql = $wpdb->prepare('SELECT booking_status FROM '.EM_BOOKINGS_TABLE.' WHERE person_id=%d AND event_id=%d', array(get_current_user_id(), $this->event_id));
								$user_bookings = $wpdb->get_col($sql);
							}
							if( $condition == 'is_user_attendee' && count($user_bookings) > 0 ){
								//user has a booking for this event, could be any booking status
								$show_condition = true;
							}elseif( $condition == 'not_user_attendee' && count($user_bookings) == 0 ){
								//user has no bookings to this event
								$show_condition = true;
							}elseif( strpos($condition, 'is_user_attendee_') !== false ){
								//user has a booking for this event, and we'll now look for a specific status
								$attendee_booking_status = str_replace('is_user_attendee_', '', $condition);
								$show_condition = in_array($attendee_booking_status, $user_bookings);
							}elseif( strpos($condition, 'not_user_attendee_') !== false ){
								//user has a booking for this event, and we'll now look for a specific status
								$attendee_booking_status = str_replace('not_user_attendee_', '', $condition);
								$show_condition = !in_array($attendee_booking_status, $user_bookings);
							}
						}
					}elseif ( $condition == 'has_category' ||  $condition == 'no_category' ){
						//event is in this category
						if( get_option('dbem_categories_enabled') ) {
							$terms = get_the_terms($this->post_id, EM_TAXONOMY_CATEGORY);
							$show_condition = $condition == 'has_category' ? !empty($terms) : empty($terms);
						}else{
							$show_condition = $condition !== 'has_category'; // no categories
						}
					}elseif ( preg_match('/^has_category_([a-zA-Z0-9_\-,]+)$/', $condition, $category_match)){
						//event is in this category
						$show_condition = get_option('dbem_categories_enabled') && has_term(explode(',', $category_match[1]), EM_TAXONOMY_CATEGORY, $this->post_id);
					}elseif ( preg_match('/^no_category_([a-zA-Z0-9_\-,]+)$/', $condition, $category_match)){
					    //event is NOT in this category
						$show_condition = !get_option('dbem_categories_enabled') || !has_term(explode(',', $category_match[1]), EM_TAXONOMY_CATEGORY, $this->post_id);
					}elseif ( $condition == 'has_tag' ||  $condition == 'no_tag' ){
						//event is in this category
						if( get_option('dbem_tags_enabled') ) {
							$terms = get_the_terms( $this->post_id, EM_TAXONOMY_TAG);
							$show_condition = $condition == 'has_tag' ? !empty($terms) : empty($terms);
						} else {
							$show_condition = $condition !== 'has_tag'; // no tags
						}
					}elseif ( $condition == 'has_taxonomy' ||  $condition == 'no_taxonomy' ){
						//event is in this category
						$cats = get_option('dbem_categories_enabled') ? get_the_terms( $this->post_id, EM_TAXONOMY_CATEGORY) : array();
						$tax = get_option('dbem_tags_enabled') ? get_the_terms( $this->post_id, EM_TAXONOMY_TAG) : array();
						$show_condition = $condition == 'has_taxonomy' ? !empty($tax) || !empty($cats) : empty($tax) && empty($cats);
					}elseif ( preg_match('/^has_tag_([a-zA-Z0-9_\-,]+)$/', $condition, $tag_match)){
						//event has this tag
						$show_condition = get_option('dbem_tags_enabled') && has_term(explode(',', $tag_match[1]), EM_TAXONOMY_TAG, $this->post_id);
					}elseif ( preg_match('/^no_tag_([a-zA-Z0-9_\-,]+)$/', $condition, $tag_match)){
					   //event doesn't have this tag
						$show_condition = !get_option('dbem_tags_enabled') || !has_term(explode(',', $tag_match[1]), EM_TAXONOMY_TAG, $this->post_id);
					}elseif ( preg_match('/^has_att_([a-zA-Z0-9_\-,]+)$/', $condition, $att_match)){
						//event has a specific custom field
						$show_condition = !empty($this->event_attributes[$att_match[1]]) || !empty($this->event_attributes[str_replace('_', ' ', $att_match[1])]);
					}elseif ( preg_match('/^no_att_([a-zA-Z0-9_\-,]+)$/', $condition, $att_match)){
						//event has a specific custom field
						$show_condition = empty($this->event_attributes[$att_match[1]]) && empty($this->event_attributes[str_replace('_', ' ', $att_match[1])]);
					}
					//other potential ones - has_attribute_... no_attribute_... has_categories_...
					$show_condition = apply_filters('em_event_output_show_condition', $show_condition, $condition, $conditionals[0][$key], $this);
					if($show_condition){
						//calculate lengths to delete placeholders
						$placeholder_length = strlen($condition)+2;
						$replacement = substr($conditionals[0][$key], $placeholder_length, strlen($conditionals[0][$key])-($placeholder_length *2 +1));
					}else{
						$replacement = '';
					}
					$event_string = str_replace($conditionals[0][$key], apply_filters('em_event_output_condition', $replacement, $condition, $conditionals[0][$key], $this), $event_string);
				}
			}
	 	}
		//Now let's check out the placeholders.
	 	preg_match_all("/(#@?_?[A-Za-z0-9_]+)({([^}]+)})?/", $event_string, $placeholders);
	 	$replaces = array();
		foreach($placeholders[1] as $key => $result) {
			$match = true;
			$replace = '';
			$full_result = $placeholders[0][$key];
			$placeholder_atts = array($result);
			if( !empty($placeholders[3][$key]) ) $placeholder_atts[] = $placeholders[3][$key];
			switch( $result ){
				//Event Details
				case '#_EVENTID':
					$replace = $this->event_id;
					break;
				case '#_EVENTPOSTID':
					$replace = $this->post_id;
					break;
				case '#_NAME': //deprecated
				case '#_EVENTNAME':
					$replace = $this->event_name;
					break;
				case '#_EVENTSTATUS':
					$statuses = static::get_active_statuses();
					if( array_key_exists($this->event_active_status, $statuses) ){
						$replace = $statuses[$this->event_active_status];
					}else{
						$replace = $statuses[1];
					}
					break;
				case '#_NOTES': //deprecated
				case '#_EVENTNOTES':
					$replace = $this->post_content;
					break;
				case '#_EXCERPT': //deprecated
				case '#_EVENTEXCERPT':
				case '#_EVENTEXCERPTCUT':
					if( !empty($this->post_excerpt) && $result != "#_EVENTEXCERPTCUT" ){
						$replace = $this->post_excerpt;
					}else{
						$excerpt_length = ( $result == "#_EVENTEXCERPTCUT" ) ? 55:false;
						$excerpt_more = apply_filters('em_excerpt_more', ' ' . '[...]');
						if( !empty($placeholders[3][$key]) ){
							$ph_args = explode(',', $placeholders[3][$key]);
							if( is_numeric($ph_args[0]) || empty($ph_args[0]) ) $excerpt_length = $ph_args[0];
							if( !empty($ph_args[1]) ) $excerpt_more = $ph_args[1];
						}
						$replace = $this->output_excerpt($excerpt_length, $excerpt_more, $result == "#_EVENTEXCERPTCUT");
					}
					break;
				case '#_EVENTIMAGEURL':
				case '#_EVENTIMAGE':
	        		if($this->get_image_url() != ''){
						if($result == '#_EVENTIMAGEURL'){
		        			$replace =  esc_url($this->image_url);
						}else{
							if( empty($placeholders[3][$key]) ){
								$replace = "<img src='".esc_url($this->image_url)."' alt='".esc_attr($this->event_name)."'/>";
							}else{
								$image_size = explode(',', $placeholders[3][$key]);
								$image_url = $this->image_url;
								if( self::array_is_numeric($image_size) && count($image_size) > 1 ){
								    //get a thumbnail
								    if( get_option('dbem_disable_thumbnails') ){
    								    $image_attr = '';
    								    $image_args = array();
    								    if( empty($image_size[1]) && !empty($image_size[0]) ){    
    								        $image_attr = 'width="'.$image_size[0].'"';
    								        $image_args['w'] = $image_size[0];
    								    }elseif( empty($image_size[0]) && !empty($image_size[1]) ){
    								        $image_attr = 'height="'.$image_size[1].'"';
    								        $image_args['h'] = $image_size[1];
    								    }elseif( !empty($image_size[0]) && !empty($image_size[1]) ){
    								        $image_attr = 'width="'.$image_size[0].'" height="'.$image_size[1].'"';
    								        $image_args = array('w'=>$image_size[0], 'h'=>$image_size[1]);
    								    }
								        $replace = "<img src='".esc_url(em_add_get_params($image_url, $image_args))."' alt='".esc_attr($this->event_name)."' $image_attr />";
								    }else{
    								    if( EM_MS_GLOBAL && get_current_blog_id() != $this->blog_id ){
    								        switch_to_blog($this->blog_id);
    								        $switch_back = true;
    								    }
								        $replace = get_the_post_thumbnail($this->ID, $image_size, array('alt' => esc_attr($this->event_name)) );
								        if( !empty($switch_back) ){ restore_current_blog(); }
								    }
								}else{
									$replace = "<img src='".esc_url($image_url)."' alt='".esc_attr($this->event_name)."'/>";
								}
							}
						}
	        		}
					break;
				//Times & Dates
				case '#_24HSTARTTIME':
				case '#_24HENDTIME':
					$replace = ($result == '#_24HSTARTTIME') ? $this->start()->format('H:i'):$this->end()->format('H:i');
					break;
				case '#_24HSTARTTIME_SITE':
				case '#_24HENDTIME_SITE':
					$replace = ($result == '#_24HSTARTTIME_SITE') ? $this->start()->copy()->setTimezone(false)->format('H:i'):$this->end()->copy()->setTimezone(false)->format('H:i');
					break;
				case '#_24HSTARTTIME_LOCAL':
				case '#_24HENDTIME_LOCAL':
				case '#_24HTIMES_LOCAL':
					$ts = ($result == '#_24HENDTIME_LOCAL') ? $this->end()->getTimestamp():$this->start()->getTimestamp();
					$date_end = ($result == '#_24HTIMES_LOCAL' && $this->event_start_time !== $this->event_end_time) ? 'data-time-end="'. esc_attr($this->end()->getTimestamp()) .'" data-separator="'. esc_attr(get_option('dbem_times_separator')) . '"' : '';
					$replace = '<span class="em-time-localjs" data-time-format="24"  data-time="'. esc_attr($ts) .'" '. $date_end .'>JavaScript Disabled</span>';
					break;
				case '#_12HSTARTTIME':
				case '#_12HENDTIME':
					$replace = ($result == '#_12HSTARTTIME') ? $this->start()->format('g:i A'):$this->end()->format('g:i A');
					break;
				case '#_12HSTARTTIME_SITE':
				case '#_12HENDTIME_SITE':
					$replace = ($result == '#_12HSTARTTIME_SITE') ? $this->start()->copy()->setTimezone(false)->format('g:i A'):$this->end()->copy()->setTimezone(false)->format('g:i A');
					break;
				case '#_12HSTARTTIME_LOCAL':
				case '#_12HENDTIME_LOCAL':
				case '#_12HTIMES_LOCAL':
					$ts = ($result == '#_12HENDTIME_LOCAL') ? $this->end()->getTimestamp():$this->start()->getTimestamp();
					$date_end = ($result == '#_24HTIMES_LOCAL' && $this->end()->getTimestamp() !== $ts) ? 'data-time-end="'. esc_attr($this->end()->getTimestamp()) .'" data-separator="'. esc_attr(get_option('dbem_times_separator')) . '"' : '';
					$replace = '<span class="em-time-localjs" data-time-format="12"  data-time="'. esc_attr($ts) .'" '. $date_end .'>JavaScript Disabled</span>';
					break;
				case '#_EVENTTIMES':
					//get format of time to show
					$replace = $this->output_times();
					break;
				case '#_EVENTTIMES_SITE':
					//get format of time to show but show timezone of site rather than local time
					$replace = $this->output_times(false, false, false, true);
					break;
				case '#_EVENTTIMES_LOCAL':
				case '#_EVENTDATES_LOCAL':
					if( !defined('EM_JS_MOMENTJS_PH') || EM_JS_MOMENTJS_PH ){
						// check for passed parameters, in which case we skip replacements entirely and use pure moment formats
						$time_format = $separator = null;
						if( !empty($placeholder_atts[1]) ){
							$params = explode(',', $placeholder_atts[1]);
							if( !empty($params[0]) ) $time_format = $params[0];
							if( !empty($params[1]) ) $separator = $params[1];
						}
						// if no moment format provided, we convert the one stored for times in php
						$start_time = $this->start()->getTimestamp();
						if( empty($time_format) ){
							// convert EM format setting to moment formatting, adapted from https://stackoverflow.com/questions/30186611/php-dateformat-to-moment-js-format
							$replacements = array(
								/* Day */ 'jS' => 'Do', /*o doesn't exist on its own, so we find/replase jS only*/ 'd' => 'DD', 'D' => 'ddd', 'j' => 'D', 'l' => 'dddd', 'N' => 'E', /*'S' => 'o' - see jS*/ 'w' => 'e', 'z' => 'DDD',
								/* Week */ 'W' => 'W',
								/* Month */ 'F' => 'MMMM', 'm' => 'MM', 'M' => 'MMM', 'n' => 'M', 't' => '#t', /* days in the month => moment().daysInMonth(); */
								/* Year */ 'L' => '#L', /* Leap year? => moment().isLeapYear(); */ 'o' => 'YYYY', 'Y' => 'YYYY', 'y' => 'YY',
								/* Time */ 'a' => 'a', 'A' => 'A', 'B' => '', /* Swatch internet time (.beats), no equivalent */ 'g' => 'h', 'G' => 'H', 'h' => 'hh', 'H' => 'HH', 'i' => 'mm', 's' => 'ss', 'u' => 'SSSSSS', /* microseconds */ 'v' => 'SSS',    /* milliseconds (from PHP 7.0.0) */
								/* Timezone */ 'e' => '##T', /* Timezone - deprecated since version 1.6.0 of moment.js, we'll use Intl.DateTimeFromat().resolvedOptions().timeZone instead. */ 'I' => '##t',       /* Daylight Saving Time? => moment().isDST(); */ 'O' => 'ZZ', 'P' => 'Z', 'T' => '#T', /* deprecated since version 1.6.0 of moment.js, using GMT difference with colon to keep it shorter than full timezone */ 'Z' => '###t',    /* time zone offset in seconds => moment().zone() * -60 : the negative is because moment flips that around; */
								/* Full Date/Time */ 'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', /* ISO 8601 */ 'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', /* RFC 2822 */ 'U' => 'X',
							);
							// Converts escaped characters.
							foreach ($replacements as $from => $to) {
								$replacements['\\' . $from] = '[' . $from . ']';
							}
							if( $result === '#_EVENTDATES_LOCAL' ){
								$time_format = ( get_option('dbem_date_format') ) ? get_option('dbem_date_format'):get_option('date_format');
								$end_time = $this->event_start_date == $this->event_end_date ? $start_time : $this->end()->getTimestamp();
								if( empty($separator) ) $separator = get_option('dbem_dates_separator');
							}else{
								$time_format = ( get_option('dbem_time_format') ) ? get_option('dbem_time_format'):get_option('time_format');
								$end_time = $this->event_start_time == $this->event_end_time ? $start_time : $this->end()->getTimestamp();
								if( empty($separator) ) $separator = get_option('dbem_times_separator');
							}
							$time_format = strtr($time_format, $replacements);
						} else {
							$end_time = $this->end()->getTimestamp();
							// decide whether to use dates separator or time separator, if there's a day format, use date, if not use time
							if( empty($separator) ) $separator = $result === '#_EVENTDATES_LOCAL' ? get_option('dbem_dates_separator') : get_option('dbem_times_separator');
						}
						wp_enqueue_script('moment', '', array(), false, true); //add to footer if not already
						// start output
						ob_start();
						?>
						<span class="em-date-momentjs" data-date-format="<?php echo esc_attr($time_format); ?>" data-date-start="<?php echo $start_time ?>" data-date-end="<?php echo $end_time ?>" data-date-separator="<?php echo esc_attr($separator); ?>">JavaScript Disabled</span>
						<?php
						$replace = ob_get_clean();
					}
					break;
				case '#_EVENTDATES':
					//get format of time to show
					$replace = $this->output_dates();
					break;
				case '#_EVENTSTARTDATE':
					//get format of time to show
					if( empty($date_format) ) $date_format = ( get_option('dbem_date_format') ) ? get_option('dbem_date_format'):get_option('date_format');
					$replace = $replace = $this->start()->i18n($date_format);
					break;
				case '#_EVENTDATES_SITE':
					//get format of time to show but use timezone of site rather than event
					$replace = $this->output_dates(false, false, true);
					break;
				case '#_EVENTTIMEZONE':
					$replace = str_replace('_', ' ', $this->event_timezone);
					break;
				case '#_EVENTTIMEZONERAW':
					$replace = $this->event_timezone;
					break;
				case '#_EVENTTIMEZONE_LOCAL':
					$rand = rand();
					ob_start();
					?>
					<span id="em-start-local-timezone-<?php echo $rand ?>">JavaScript Disabled</span>
					<script>
						document.getElementById("em-start-local-timezone-<?php echo $rand ?>").innerHTML = Intl.DateTimeFormat().resolvedOptions().timeZone;
					</script>
					<?php
					$replace = ob_get_clean();
					break;
				//Recurring Placeholders
				case '#_RECURRINGDATERANGE': //Outputs the #_EVENTDATES equivalent of the recurring event template pattern.
					$replace = $this->get_event_recurrence()->output_dates(); //if not a recurrence, we're running output_dates on $this
					break;
				case '#_RECURRINGPATTERN':
					$replace = '';
					if( $this->is_recurrence() || $this->is_recurring() ){
						$replace = $this->get_event_recurrence()->get_recurrence_description();
					}
					break;
				case '#_RECURRINGID':
					$replace = $this->recurrence_id;
					break;
				//Links
				case '#_EVENTPAGEURL': //deprecated	
				case '#_LINKEDNAME': //deprecated
				case '#_EVENTURL': //Just the URL
				case '#_EVENTLINK': //HTML Link
					$event_link = esc_url($this->get_permalink());
					if($result == '#_LINKEDNAME' || $result == '#_EVENTLINK'){
						$replace = '<a href="'.$event_link.'">'.esc_attr($this->event_name).'</a>';
					}else{
						$replace = $event_link;	
					}
					break;
				case '#_EDITEVENTURL':
				case '#_EDITEVENTLINK':
					if( $this->can_manage('edit_events','edit_others_events') ){
						$link = esc_url($this->get_edit_url());
						if( $result == '#_EDITEVENTLINK'){
							$replace = '<a href="'.$link.'">'.esc_html(sprintf(__('Edit Event','events-manager'))).'</a>';
						}else{
							$replace = $link;
						}
					}	 
					break;
				//Bookings
				case '#_ADDBOOKINGFORM': //deprecated
				case '#_REMOVEBOOKINGFORM': //deprecated
				case '#_BOOKINGFORM':
					if( get_option('dbem_rsvp_enabled')){
						if( !defined('EM_XSS_BOOKINGFORM_FILTER') && locate_template('plugins/events-manager/placeholders/bookingform.php') ){
							//xss fix for old overridden booking forms
							add_filter('em_booking_form_action_url','esc_url');
							define('EM_XSS_BOOKINGFORM_FILTER',true);
						}
						ob_start();
						// We are firstly checking if the user has already booked a ticket at this event, if so offer a link to view their bookings.
						$EM_Booking = $this->get_bookings()->has_booking();
						//count tickets and available tickets
						$template_vars = array(
							'EM_Event' => $this,
							'tickets_count' =>  count($this->get_bookings()->get_tickets()->tickets),
							'available_tickets_count' =>  count($this->get_bookings()->get_available_tickets()),
							//decide whether user can book, event is open for bookings etc.
							'can_book' =>  is_user_logged_in() || (get_option('dbem_bookings_anonymous') && !is_user_logged_in()),
							'is_open' =>  $this->get_bookings()->is_open(), //whether there are any available tickets right now
							'is_free' =>  $this->is_free(),
							'show_tickets' =>  true,
							'id' =>  absint($this->event_id),
							'already_booked' => is_object($EM_Booking) && $EM_Booking->booking_id > 0,
							'EM_Booking' => $this->get_bookings()->get_intent_default(), // get the booking intent if not supplied already
						);
						//if user is logged out, check for member tickets that might be available, since we should ask them to log in instead of saying 'bookings closed'
						if( !$template_vars['is_open'] && !is_user_logged_in() && $this->get_bookings()->is_open(true) ){
							$template_vars['is_open'] = true;
							$template_vars['can_book'] = false;
							$template_vars['show_tickets'] = get_option('dbem_bookings_tickets_show_unavailable') && get_option('dbem_bookings_tickets_show_member_tickets');
						}
						em_locate_template('placeholders/bookingform.php', true, $template_vars);
						EM_Bookings::enqueue_js();
						$replace = ob_get_clean();
					}
					break;
				case '#_BOOKINGBUTTON':
					if( get_option('dbem_rsvp_enabled') && $this->event_rsvp ){
						ob_start();
						em_locate_template('placeholders/bookingbutton.php', true, array('EM_Event'=>$this));
						$replace = ob_get_clean();
					}
					break;
				case '#_EVENTPRICERANGEALL':				    
				    $show_all_ticket_prices = true; //continues below
				case '#_EVENTPRICERANGE':
					//get the range of prices
					$min = false;
					$max = 0;
					if( $this->get_bookings()->is_open() || !empty($show_all_ticket_prices) ){
						foreach( $this->get_tickets()->tickets as $EM_Ticket ){
							/* @public $EM_Ticket EM_Ticket */
							if( $EM_Ticket->is_available() || get_option('dbem_bookings_tickets_show_unavailable') || !empty($show_all_ticket_prices) ){
								if($EM_Ticket->get_price() > $max ){
									$max = $EM_Ticket->get_price();
								}
								if($EM_Ticket->get_price() < $min || $min === false){
									$min = $EM_Ticket->get_price();
								}						
							}
						}
					}
					if( empty($min) ) $min = 0;
					if( $min != $max ){
						$replace = em_get_currency_formatted($min).' - '.em_get_currency_formatted($max);
					}else{
						$replace = em_get_currency_formatted($min);
					}
					break;
				case '#_EVENTPRICEMIN':
				case '#_EVENTPRICEMINALL':
					//get the range of prices
					$min = false;
					foreach( $this->get_tickets()->tickets as $EM_Ticket ){
						/* @public $EM_Ticket EM_Ticket */
						if( $EM_Ticket->is_available() || $result == '#_EVENTPRICEMINALL'){
							if( $EM_Ticket->get_price() < $min || $min === false){
								$min = $EM_Ticket->get_price();
							}
						}
					}
					if( $min === false ) $min = 0;
					$replace = em_get_currency_formatted($min);
					break;
				case '#_EVENTPRICEMAX':
				case '#_EVENTPRICEMAXALL':
					//get the range of prices
					$max = 0;
					foreach( $this->get_tickets()->tickets as $EM_Ticket ){
						/* @public $EM_Ticket EM_Ticket */
						if( $EM_Ticket->is_available() || $result == '#_EVENTPRICEMAXALL'){
							if( $EM_Ticket->get_price() > $max ){
								$max = $EM_Ticket->get_price();
							}
						}			
					}
					$replace = em_get_currency_formatted($max);
					break;
				case '#_AVAILABLESEATS': //deprecated
				case '#_AVAILABLESPACES':
					if ($this->event_rsvp && get_option('dbem_rsvp_enabled')) {
					   $replace = $this->get_bookings()->get_available_spaces();
					} else {
						$replace = "0";
					}
					break;
				case '#_BOOKEDSEATS': //deprecated
				case '#_BOOKEDSPACES':
					//This placeholder is actually a little misleading, as it'll consider reserved (i.e. pending) bookings as 'booked'
					if ($this->event_rsvp && get_option('dbem_rsvp_enabled')) {
						$replace = $this->get_bookings()->get_booked_spaces();
						if( get_option('dbem_bookings_approval_reserved') ){
							$replace += $this->get_bookings()->get_pending_spaces();
						}
					} else {
						$replace = "0";
					}
					break;
				case '#_PENDINGSPACES':
					if ($this->event_rsvp && get_option('dbem_rsvp_enabled')) {
					   $replace = $this->get_bookings()->get_pending_spaces();
					} else {
						$replace = "0";
					}
					break;
				case '#_SEATS': //deprecated
				case '#_SPACES':
					$replace = $this->get_spaces();
					break;
				case '#_BOOKINGSURL':
				case '#_BOOKINGSLINK':
					if( $this->can_manage('manage_bookings','manage_others_bookings') ){
						$bookings_link = esc_url($this->get_bookings_url());
						if($result == '#_BOOKINGSLINK'){
							$replace = '<a href="'.$bookings_link.'" title="'.esc_attr($this->event_name).'">'.esc_html($this->event_name).'</a>';
						}else{
							$replace = $bookings_link;	
						}
					}
					break;
				case '#_BOOKINGSCUTOFF':
				case '#_BOOKINGSCUTOFFDATE':
				case '#_BOOKINGSCUTOFFTIME':
					$replace = '';
					if ($this->event_rsvp && get_option('dbem_rsvp_enabled') ) {
						$replace_format = em_get_date_format() .' '. em_get_hour_format();
						if( $result == '#_BOOKINGSCUTOFFDATE' ) $replace_format = em_get_date_format();
						if( $result == '#_BOOKINGSCUTOFFTIME' ) $replace_format = em_get_hour_format();
						$replace = $this->rsvp_end()->i18n($replace_format);
					}
					break;
				//Contact Person
				case '#_CONTACTNAME':
				case '#_CONTACTPERSON': //deprecated (your call, I think name is better)
					$replace = $this->get_contact()->display_name;
					break;
				case '#_CONTACTUSERNAME':
					$replace = $this->get_contact()->user_login;
					break;
				case '#_CONTACTEMAIL':
				case '#_CONTACTMAIL': //deprecated
					$replace = $this->get_contact()->user_email;
					break;
				case '#_CONTACTURL':
					$replace = $this->get_contact()->user_url;
					break;
				case '#_CONTACTID':
					$replace = $this->get_contact()->ID;
					break;
				case '#_CONTACTPHONE':
		      		$replace = ( $this->get_contact()->phone != '') ? $this->get_contact()->phone : __('N/A', 'events-manager');
					break;
				case '#_CONTACTAVATAR': 
					$replace = get_avatar( $this->get_contact()->ID, $size = '50' ); 
					break;
				case '#_CONTACTPROFILELINK':
				case '#_CONTACTPROFILEURL':
					if( function_exists('bp_core_get_user_domain') ){
						$replace = bp_core_get_user_domain($this->get_contact()->ID);
						if( $result == '#_CONTACTPROFILELINK' ){
							$replace = '<a href="'.esc_url($replace).'">'.__('Profile', 'events-manager').'</a>';
						}
					}
					break;
				case '#_CONTACTMETA':
					if( !empty($placeholders[3][$key]) ){
						$replace = get_user_meta($this->event_owner, $placeholders[3][$key], true);
					}
					break;
				case '#_ATTENDEES':
					ob_start();
					em_locate_template('placeholders/attendees.php', true, array('EM_Event'=>$this));
					$replace = ob_get_clean();
					break;
				case '#_ATTENDEESLIST':
					ob_start();
					em_locate_template('placeholders/attendeeslist.php', true, array('EM_Event'=>$this));
					$replace = ob_get_clean();
					break;
				case '#_ATTENDEESPENDINGLIST':
					ob_start();
					em_locate_template('placeholders/attendeespendinglist.php', true, array('EM_Event'=>$this));
					$replace = ob_get_clean();
					break;
				//Categories and Tags
				case '#_EVENTCATEGORIESIMAGES':
				    $replace = '';
				    if( get_option('dbem_categories_enabled') ){
    					ob_start();
    					em_locate_template('placeholders/eventcategoriesimages.php', true, array('EM_Event'=>$this));
    					$replace = ob_get_clean();
				    }
					break;
				case '#_EVENTTAGS':
				    $replace = '';
                    if( get_option('dbem_tags_enabled') ){
    					ob_start();
    					em_locate_template('placeholders/eventtags.php', true, array('EM_Event'=>$this));
    					$replace = ob_get_clean();
                    }
					break;
				case '#_EVENTTAGSLINE':
					$tags = get_the_terms($this->post_id, EM_TAXONOMY_TAG);
					if( is_array($tags) && count($tags) > 0 ){
						$tags_list = array();
						foreach($tags as $tag) {
							$link = get_term_link($tag->slug, EM_TAXONOMY_TAG);
							if( is_wp_error($link) ) $link = '';
							$tags_list[] = '<a href="' . $link . '">' . $tag->name . '</a>';
						}
					}
					if( !empty($tags_list) ) {
						$replace = implode(', ', $tags_list);
					}else{
						$replace = get_option ( 'dbem_no_tags_message' );
					}
					break;
				case '#_CATEGORIES': //deprecated
				case '#_EVENTCATEGORIES':
				    $replace = '';
				    if( get_option('dbem_categories_enabled') ){
    					ob_start();
    					em_locate_template('placeholders/categories.php', true, array('EM_Event'=>$this));
    					$replace = ob_get_clean();
				    }
					break;
				case '#_EVENTCATEGORIESLINE':
					$categories = array();
					foreach( $this->get_categories() as $EM_Category ){
						$categories[] = $EM_Category->output("#_CATEGORYLINK");
					}
					if( !empty($categories) ) {
						$replace = implode(', ', $categories);
					}else{
						$replace = get_option ( 'dbem_no_categories_message' );
					}
					break;
				//Ical Stuff
				case '#_EVENTICALURL':
				case '#_EVENTICALLINK':
					$replace = $this->get_ical_url();
					if( $result == '#_EVENTICALLINK' ){
						$replace = '<a href="'.esc_url($replace).'">iCal</a>';
					}
					break;
				case '#_EVENTWEBCALURL':
				case '#_EVENTWEBCALLINK':
					$replace = $this->get_ical_url();
					$replace = str_replace(array('http://','https://'), 'webcal://', $replace);
					if( $result == '#_EVENTWEBCALLINK' ){
						$replace = '<a href="'.esc_url($replace).'">webcal</a>';
					}
					break;
				case '#_EVENTGCALURL':
				case '#_EVENTGCALLINK':
					//get dates in UTC/GMT time
					if($this->event_all_day && $this->event_start_date == $this->event_end_date){
						$dateStart	= $this->start()->format('Ymd');
						$dateEnd	= $this->end()->copy()->add('P1D')->format('Ymd');
					}else{
						$dateStart	= $this->start()->format('Ymd\THis');
						$dateEnd = $this->end()->format('Ymd\THis');
					}
					//build url
					$gcal_url = 'https://www.google.com/calendar/event?action=TEMPLATE&text=event_name&dates=start_date/end_date&details=post_content&location=location_name&trp=false&sprop=event_url&sprop=name:blog_name&ctz=event_timezone';
					$replace = $this->generate_ical_url($gcal_url, $dateStart, $dateEnd);
					if( $result == '#_EVENTGCALLINK' ){
						$img_url = 'https://www.google.com/calendar/images/ext/gc_button2.gif';
						$replace = '<a href="'.esc_url($replace).'" target="_blank"><img src="'.esc_url($img_url).'" alt="0" border="0"></a>';
					}
					break;
				case '#_EVENTOUTLOOKLIVELINK':
				case '#_EVENTOUTLOOKLIVEURL':
				case '#_EVENTOFFICE365LINK':
				case '#_EVENTOFFICE365URL':
					$base_url = $result == '#_EVENTOUTLOOKLIVELINK' || $result == '#_EVENTOUTLOOKLIVEURL' ? 'https://outlook.live.com':'https://outlook.office.com';
					if($this->event_all_day && $this->event_start_date == $this->event_end_date){
						$dateStart	= $this->start()->copy()->format('c');
						$dateEnd	= $this->end()->copy()->sub('P1D')->format('c');
						$url = $base_url.'/calendar/0/deeplink/compose?allday=true&body=post_content&location=location_name&path=/calendar/action/compose&rru=addevent&startdt=start_date&enddt=end_date&subject=event_name';
					}else{
						$dateStart	= $this->start()->copy()->format('c');
						$dateEnd = $this->end()->copy()->format('c');
						$url = $base_url.'/calendar/0/deeplink/compose?allday=false&body=post_content&location=location_name&path=/calendar/action/compose&rru=addevent&startdt=start_date&enddt=end_date&subject=event_name';
					}
					$replace = $this->generate_ical_url( $url, $dateStart, $dateEnd );
					if( $result == '#_EVENTOUTLOOKLIVELINK' ){
						$replace = '<a href="'.esc_url($replace).'" target="_blank">Outlook Live</a>';
					} elseif ( $result == '#_EVENTOFFICE365LINK' ){
						$replace = '<a href="'.esc_url($replace).'" target="_blank">Office 365</a>';
					}
					break;
				//Event location (not physical location)
				case '#_EVENTADDTOCALENDAR':
					ob_start();
					$rand_id = rand();
					?>
					<button type="button" class="em-event-add-to-calendar em-tooltip-ddm em-clickable input" data-button-width="match" data-tooltip-class="em-add-to-calendar-tooltip" data-content="em-event-add-to-colendar-content-<?php echo $rand_id; ?>"><span class="em-icon em-icon-calendar"></span> <?php esc_html_e('Add To Calendar', 'events-manager'); ?></button>
					<div class="em-tooltip-ddm-content em-event-add-to-calendar-content" id="em-event-add-to-colendar-content-<?php echo $rand_id; ?>">
						<a class="em-a2c-download" href="<?php echo esc_url($this->get_ical_url()); ?>" target="_blank"><?php echo sprintf(esc_html__('Download %s', 'events-manager'), 'ICS'); ?></a>
						<a class="em-a2c-google" href="<?php echo esc_url($this->output('#_EVENTGCALURL')); ?>" target="_blank"><?php esc_html_e('Google Calendar', 'events-manager'); ?></a>
						<a class="em-a2c-apple" href="<?php echo esc_url(str_replace(array('http://','https://'), 'webcal://', $this->get_ical_url())); ?>" target="_blank">iCalendar</a>
						<a class="em-a2c-office" href="<?php echo esc_url($this->output('#_EVENTOFFICE365URL')); ?>" target="_blank">Office 365</a>
						<a class="em-a2c-outlook" href="<?php echo esc_url($this->output('#_EVENTOUTLOOKLIVEURL')); ?>" target="_blank">Outlook Live</a>
					</div>
					<?php
					$replace = ob_get_clean();
					break;
				case '#_EVENTLOCATION':
					if( $this->has_event_location() ) {
						if (!empty($placeholders[3][$key])) {
							$replace = $this->get_event_location()->output( $placeholders[3][$key], $target );
						} else {
							$replace = $this->get_event_location()->output( null, $target );
						}
					}
					break;
				default:
					$replace = $full_result;
					break;
			}
			$replaces[$full_result] = apply_filters('em_event_output_placeholder', $replace, $this, $full_result, $target, $placeholder_atts);
		}
		//sort out replacements so that during replacements shorter placeholders don't overwrite longer varieties.
		krsort($replaces);
		foreach($replaces as $full_result => $replacement){
			if( !in_array($full_result, array('#_NOTES','#_EVENTNOTES')) ){
				$event_string = str_replace($full_result, $replacement , $event_string );
			}else{
			    $new_placeholder = str_replace('#_', '__#', $full_result); //this will avoid repeated filters when locations/categories are parsed
			    $event_string = str_replace($full_result, $new_placeholder , $event_string );
				$desc_replace[$new_placeholder] = $replacement;
			}
		}
		//Time placeholders
		foreach($placeholders[1] as $result) {
			// matches all PHP START date and time placeholders
			if (preg_match('/^#[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]$/', $result)) {
				$replace = $this->start()->i18n(ltrim($result, "#"));
				$replace = apply_filters('em_event_output_placeholder', $replace, $this, $result, $target, array($result));
				$event_string = str_replace($result, $replace, $event_string );
			}
			// matches all PHP END time placeholders for endtime
			if (preg_match('/^#@[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU]$/', $result)) {
				$replace = $this->end()->i18n(ltrim($result, "#@"));
				$replace = apply_filters('em_event_output_placeholder', $replace, $this, $result, $target, array($result));
				$event_string = str_replace($result, $replace, $event_string ); 
		 	}
		}
		//Now do dependent objects
		if( get_option('dbem_locations_enabled') ){
			if( !empty($this->location_id) && ($this->get_location()->location_status || $this->get_location()->location_status === $this->event_status) ){
				$event_string = $this->get_location()->output($event_string, $target);
			}else{
				$EM_Location = new EM_Location();
				$event_string = $EM_Location->output($event_string, $target);
			}
		}
		
		if( get_option('dbem_categories_enabled') ){
    		//for backwards compat and easy use, take over the individual category placeholders with the frirst cat in th elist.
    		if( count($this->get_categories()) > 0 ){
    			$EM_Category = $this->get_categories()->get_first();
    		}
    		if( empty($EM_Category) ) $EM_Category = new EM_Category();
    		$event_string = $EM_Category->output($event_string, $target);
		}
		
		if( get_option('dbem_tags_enabled') ){
			$EM_Tags = new EM_Tags($this);
			if( count($EM_Tags) > 0 ){
				$EM_Tag = $EM_Tags->get_first();
			}
			if( empty($EM_Tag) ) $EM_Tag = new EM_Tag();
			$event_string = $EM_Tag->output($event_string, $target);
		}
		
		//Finally, do the event notes, so that previous placeholders don't get replaced within the content, which may use shortcodes
		if( !empty($desc_replace) ){
			foreach($desc_replace as $full_result => $replacement){
				$event_string = str_replace($full_result, $replacement , $event_string );
			}
		}
		
		//do some specific formatting
		//TODO apply this sort of formatting to any output() function
		if( $target == 'ical' ){
		    //strip html and escape characters
		    $event_string = str_replace('\\','\\\\',strip_tags($event_string));
		    $event_string = str_replace(';','\;',$event_string);
		    $event_string = str_replace(',','\,',$event_string);
		    //remove and define line breaks in ical format
		    $event_string = str_replace('\\\\n','\n',$event_string);
		    $event_string = str_replace("\r\n",'\n',$event_string);
		    $event_string = str_replace("\n",'\n',$event_string);
		}
		return apply_filters('em_event_output', $event_string, $this, $format, $target);
	}
	
	public function generate_ical_url($url, $dateStart, $dateEnd, $description_max_length = 1350 ){
		//replace url template placeholders
		$url = str_replace('event_name', urlencode($this->event_name), $url);
		$url = str_replace('start_date', urlencode($dateStart), $url);
		$url = str_replace('end_date', urlencode($dateEnd), $url);
		$url = str_replace('location_name', urlencode($this->get_location()->get_full_address(', ', true)), $url);
		$url = str_replace('blog_name', urlencode(get_bloginfo()), $url);
		$url = str_replace('event_url', urlencode($this->get_permalink()), $url);
		$url = str_replace('event_timezone', urlencode($this->event_timezone), $url); // Google specific
		//calculate URL length so we know how much we can work with to make a description.
		if( !empty($this->post_excerpt) ){
			$description = $this->post_excerpt;
		}else{
			$matches = explode('<!--more', $this->post_content);
			$description = wp_kses_data($matches[0]);
		}
		$url_length = strlen($url) - 9;
		// truncate
		if( $description_max_length && strlen($description) + $url_length > $description_max_length ){
			$description = substr($description, 0, $description_max_length - $url_length - 3 ).'...';
		}
		$url = str_replace('post_content', urlencode($description), $url);
		return $url;
	}
	
	function output_times( $time_format = false, $time_separator = false , $all_day_message = false, $use_site_timezone = false ){
		if( !$this->event_all_day ){
			if( empty($time_format) ) $time_format = ( get_option('dbem_time_format') ) ? get_option('dbem_time_format'):get_option('time_format');
			if( empty($time_separator) ) $time_separator = get_option('dbem_times_separator');
			if( $this->event_start_time != $this->event_end_time ){
				if( $use_site_timezone ){
					$replace = $this->start()->copy()->setTimezone()->i18n($time_format). $time_separator . $this->end()->copy()->setTimezone()->i18n($time_format);
				}else{
					$replace = $this->start()->i18n($time_format). $time_separator . $this->end()->i18n($time_format);
				}
			}else{
				if( $use_site_timezone ){
					$replace = $this->start()->copy()->setTimezone()->i18n($time_format);
				}else{
					$replace = $this->start()->i18n($time_format);
				}
			}
		}else{
			$replace = $all_day_message ? $all_day_message : get_option('dbem_event_all_day_message');
		}
		return $replace;
	}
	
	function output_dates( $date_format = false, $date_separator = false, $use_site_timezone = false ){
		if( empty($date_format) ) $date_format = ( get_option('dbem_date_format') ) ? get_option('dbem_date_format'):get_option('date_format');
		if( empty($date_separator) ) $date_separator = get_option('dbem_dates_separator');
		if( $this->event_start_date != $this->event_end_date){
			if( $use_site_timezone ){
				$replace = $this->start()->copy()->setTimezone()->i18n($date_format). $date_separator . $this->end()->copy()->setTimezone()->i18n($date_format);
			}else{
				$replace = $this->start()->i18n($date_format). $date_separator . $this->end()->i18n($date_format);
			}
		}else{
			if( $use_site_timezone ){
				$replace = $this->start()->copy()->setTimezone()->i18n($date_format);
			}else{
				$replace = $this->start()->i18n($date_format);
			}
		}
		return $replace;
	}
	
	/**********************************************************
	 * RECURRENCE METHODS
	 ***********************************************************/
	
	/**
	 * Returns true if this is a recurring event.
	 * @return boolean
	 */
	function is_recurring(){
		return $this->post_type == 'event-recurring' && get_option('dbem_recurrence_enabled');
	}	
	/**
	 * Will return true if this individual event is part of a set of events that recur
	 * @return boolean
	 */
	function is_recurrence(){
		return ( $this->recurrence_id > 0 && get_option('dbem_recurrence_enabled') );
	}
	/**
	 * Returns if this is an individual event and is not a recurrence
	 * @return boolean
	 */
	function is_individual(){
		return ( !$this->is_recurring() && !$this->is_recurrence() );
	}
	
	/**
	 * Gets the event recurrence template, which is an EM_Event object (based off an event-recurring post)
	 * @return EM_Event
	 */
	function get_event_recurrence(){
		if(!$this->is_recurring()){
			return em_get_event($this->recurrence_id, 'event_id');
		}else{
			return $this;
		}
	}
	
	function get_detach_url(){
		return admin_url().'admin.php?event_id='.$this->event_id.'&amp;action=event_detach&amp;_wpnonce='.wp_create_nonce('event_detach_'.get_current_user_id().'_'.$this->event_id);
	}
	
	function get_attach_url($recurrence_id){
		return admin_url().'admin.php?undo_id='.$recurrence_id.'&amp;event_id='.$this->event_id.'&amp;action=event_attach&amp;_wpnonce='.wp_create_nonce('event_attach_'.get_current_user_id().'_'.$this->event_id);
	}
	
	/**
	 * Returns if this is an individual event and is not recurring or a recurrence
	 * @return boolean
	 */
	function detach(){
		global $wpdb;
		if( $this->is_recurrence() && !$this->is_recurring() && $this->can_manage('edit_recurring_events','edit_others_recurring_events') ){
			//remove recurrence id from post meta and index table
			$url = $this->get_attach_url($this->recurrence_id);
			$wpdb->update(EM_EVENTS_TABLE, array('recurrence_id'=>null), array('event_id' => $this->event_id));
			delete_post_meta($this->post_id, '_recurrence_id');
			$this->feedback_message = __('Event detached.','events-manager') . ' <a href="'.$url.'">'.__('Undo','events-manager').'</a>';
			$this->recurrence_id = 0;
			return apply_filters('em_event_detach', true, $this);
		}
		$this->add_error(__('Event could not be detached.','events-manager'));
		return apply_filters('em_event_detach', false, $this);
	}
	
	/**
	 * Returns if this is an individual event and is not recurring or a recurrence
	 * @return boolean
	 */
	function attach($recurrence_id){
		global $wpdb;
		if( !$this->is_recurrence() && !$this->is_recurring() && is_numeric($recurrence_id) && $this->can_manage('edit_recurring_events','edit_others_recurring_events') ){
			//add recurrence id to post meta and index table
			$wpdb->update(EM_EVENTS_TABLE, array('recurrence_id'=>$recurrence_id), array('event_id' => $this->event_id));
			update_post_meta($this->post_id, '_recurrence_id', $recurrence_id);
			$this->feedback_message = __('Event re-attached to recurrence.','events-manager');
			return apply_filters('em_event_attach', true, $recurrence_id, $this);
		}
		$this->add_error(__('Event could not be attached.','events-manager'));
		return apply_filters('em_event_attach', false, $recurrence_id, $this);
	}
	
	/**
	 * Saves events and replaces old ones. Returns true if sucecssful or false if not.
	 * @return boolean
	 */
	function save_events() {
		global $wpdb;
		if( !$this->can_manage('edit_events','edit_others_events') ) return apply_filters('em_event_save_events', false, $this, array(), array());
		$event_ids = $post_ids = $event_dates = $events = array();
		if( $this->is_published() || 'future' == $this->post_status ){
			$result = false;
			//check if there's any events already created, if not (such as when an event is first submitted for approval and then published), force a reschedule.
			if( $wpdb->get_var('SELECT COUNT(event_id) FROM '.EM_EVENTS_TABLE.' WHERE recurrence_id='. absint($this->event_id)) == 0 ){
				$this->recurring_reschedule = true;
			}
			do_action('em_event_save_events_pre', $this); //actions/filters only run if event is recurring
			//Make template event index, post, and meta (we change event dates, timestamps, rsvp dates and other recurrence-relative info whilst saving each event recurrence)
			$event = $this->to_array(true); //event template - for index
			if( !empty($event['event_attributes']) ) $event['event_attributes'] = serialize($event['event_attributes']);
			$post_fields = $wpdb->get_row('SELECT * FROM '.$wpdb->posts.' WHERE ID='.$this->post_id, ARRAY_A); //post to copy
			$post_fields['post_type'] = 'event'; //make sure we'll save events, not recurrence templates
			$meta_fields_map = $wpdb->get_results('SELECT meta_key,meta_value FROM '.$wpdb->postmeta.' WHERE post_id='.$this->post_id, ARRAY_A);
			$meta_fields = array();
			//convert meta_fields into a cleaner array
			foreach($meta_fields_map as $meta_data){
				$meta_fields[$meta_data['meta_key']] = $meta_data['meta_value'];
			}
			if( isset($meta_fields['_edit_last']) ) unset($meta_fields['_edit_last']);
			if( isset($meta_fields['_edit_lock']) ) unset($meta_fields['_edit_lock']);
			//remove id and we have a event template to feed to wpdb insert
			unset($event['event_id'], $event['post_id']); 
			unset($post_fields['ID']);
			unset($meta_fields['_event_id']);
			if( isset($meta_fields['_post_id']) ) unset($meta_fields['_post_id']); //legacy bugfix, post_id was never needed in meta table
			//remove recurrence meta info we won't need in events
			foreach( $this->recurrence_fields as $recurrence_field){
				$event[$recurrence_field] = null;
				if( isset($meta_fields['_'.$recurrence_field]) ) unset($meta_fields['_'.$recurrence_field]);
			}
			//Set the recurrence ID
			$event['recurrence_id'] = $meta_fields['_recurrence_id'] = $this->event_id;
			$event['recurrence'] = 0;
			
			//Let's start saving!
			$event_saves = $meta_inserts = array();
			$recurring_date_format = apply_filters('em_event_save_events_format', 'Y-m-d');
			$post_name = $this->sanitize_recurrence_slug( $post_fields['post_name'], $this->start()->format($recurring_date_format)); //template sanitized post slug since we'll be using this
			//First thing - times. If we're changing event times, we need to delete all events and recreate them with the right times, no other way
			
			if( $this->recurring_reschedule ){
				$this->delete_events(); //Delete old events beforehand, this will change soon
				$matching_days = $this->get_recurrence_days(); //Get days where events recur
				$event['event_date_created'] = current_time('mysql'); //since the recurrences are recreated
				unset($event['event_date_modified']);
				if( count($matching_days) > 0 ){
					//first save event post data
					$EM_DateTime = $this->start()->copy();
					foreach( $matching_days as $day ) {
						//set start date/time to $EM_DateTime for relative use further on
						$EM_DateTime->setTimestamp($day)->setTimeString($event['event_start_time']);
						$start_timestamp = $EM_DateTime->getTimestamp(); //for quick access later
						//rewrite post fields if needed
						//set post slug, which may need to be sanitized for length as we pre/postfix a date for uniqueness
						$event_slug_date = $EM_DateTime->format( $recurring_date_format );
						$event_slug = $this->sanitize_recurrence_slug($post_name, $event_slug_date);
						$event_slug = apply_filters('em_event_save_events_recurrence_slug', $event_slug.'-'.$event_slug_date, $event_slug, $event_slug_date, $day, $this); //use this instead
						$post_fields['post_name'] = $event['event_slug'] = apply_filters('em_event_save_events_slug', $event_slug, $post_fields, $day, $matching_days, $this); //deprecated filter
						//set start date
						$event['event_start_date'] = $meta_fields['_event_start_date'] = $EM_DateTime->getDate();
						$event['event_start'] = $meta_fields['_event_start'] = $EM_DateTime->getDateTime(true);
						//add rsvp date/time restrictions
						if( !empty($this->recurrence_rsvp_days) && is_numeric($this->recurrence_rsvp_days) ){
							if( $this->recurrence_rsvp_days > 0 ){
								$event_rsvp_date = $EM_DateTime->copy()->add('P'.absint($this->recurrence_rsvp_days).'D')->getDate(); //cloned so original object isn't modified
							}elseif($this->recurrence_rsvp_days < 0 ){
								$event_rsvp_date = $EM_DateTime->copy()->sub('P'.absint($this->recurrence_rsvp_days).'D')->getDate(); //cloned so original object isn't modified
							}else{
								$event_rsvp_date = $EM_DateTime->getDate();
							}
				 			$event['event_rsvp_date'] = $meta_fields['_event_rsvp_date'] = $event_rsvp_date;
						}else{
							$event['event_rsvp_date'] = $meta_fields['_event_rsvp_date'] = $event['event_start_date'];
						}
						$event['event_rsvp_time'] = $meta_fields['_event_rsvp_time'] = $event['event_rsvp_time'];
						//set end date
						$EM_DateTime->setTimeString($event['event_end_time']);
						if($this->recurrence_days > 0){
							//$EM_DateTime modified here, and used further down for UTC end date
							$event['event_end_date'] = $meta_fields['_event_end_date'] = $EM_DateTime->add('P'.$this->recurrence_days.'D')->getDate();
						}else{
							$event['event_end_date'] = $meta_fields['_event_end_date'] = $event['event_start_date'];
						}
						$end_timestamp = $EM_DateTime->getTimestamp(); //for quick access later
						$event['event_end'] = $meta_fields['_event_end'] = $EM_DateTime->getDateTime(true);
						//add extra date/time post meta
						$meta_fields['_event_start_local'] = $event['event_start_date'].' '.$event['event_start_time'];
						$meta_fields['_event_end_local'] = $event['event_end_date'].' '.$event['event_end_time'];
						//Deprecated meta fields
						$site_data = get_site_option('dbem_data');
						if( !empty($site_data['updates']['timezone-backcompat']) ){
							$meta_fields['_start_ts'] = $start_timestamp;
							$meta_fields['_end_ts'] = $end_timestamp;
						}
						//create the event
						if( $wpdb->insert($wpdb->posts, $post_fields ) ){
							$event['post_id'] = $post_id = $post_ids[$start_timestamp] = $wpdb->insert_id; //post id saved into event and also as a var for later user
							// Set GUID and event slug as per wp_insert_post
							$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_id ) ), array('ID'=>$post_id) );
					 		//insert into events index table
							$event_saves[] = $wpdb->insert(EM_EVENTS_TABLE, $event);
							$event_ids[$post_id] = $event_id = $wpdb->insert_id;
							$event_dates[$event_id] = $start_timestamp;
					 		//create the meta inserts for each event
					 		$meta_fields['_event_id'] = $event_id;
					 		foreach($meta_fields as $meta_key => $meta_val){
					 			$meta_inserts[] = $wpdb->prepare("(%d, %s, %s)", array($post_id, $meta_key, $meta_val));
					 		}
						}else{
							$event_saves[] = false;
						}
						//if( EM_DEBUG ){ echo "Entering recurrence " . date("D d M Y", $day)."<br/>"; }
				 	}
				 	//insert the metas in one go, faster than one by one
				 	if( count($meta_inserts) > 0 ){
					 	$result = $wpdb->query("INSERT INTO ".$wpdb->postmeta." (post_id,meta_key,meta_value) VALUES ".implode(',',$meta_inserts));
					 	if($result === false){
					 		$this->add_error(esc_html__('There was a problem adding custom fields to your recurring events.','events-manager'));
					 	}
				 	}
				}else{
			 		$this->add_error(esc_html__('You have not defined a date range long enough to create a recurrence.','events-manager'));
			 		$result = false;
			 	}
			}else{
				//we go through all event main data and meta data, we delete and recreate all meta data
				//now unset some vars we don't need to deal with since we're just updating data in the wp_em_events and posts table
				unset( $event['event_date_created'], $event['recurrence_id'], $event['recurrence'], $event['event_start_date'], $event['event_end_date'], $event['event_parent'] );
				$event['event_date_modified'] = current_time('mysql'); //since the recurrences are modified but not recreated
				unset( $post_fields['comment_count'], $post_fields['guid'], $post_fields['menu_order']);
				unset( $meta_fields['_event_parent'] ); // we'll ignore this and add it manually
				// clean the meta fields array to contain only the fields we actually need to overwrite i.e. delete and recreate, to avoid deleting unecessary individula recurrence data
				$exclude_meta_update_keys = apply_filters('em_event_save_events_exclude_update_meta_keys', array('_parent_id'), $this);
				//now we go through the recurrences and check whether things relative to dates need to be changed
				$events = EM_Events::get( array('recurrence'=>$this->event_id, 'scope'=>'all', 'status'=>'everything', 'array' => true ) );
			 	foreach($events as $event_array){ /* @public $EM_Event EM_Event */
			 		//set new start/end times to obtain accurate timestamp according to timezone and DST
			 		$EM_DateTime = $this->start()->copy()->modify($event_array['event_start_date']. ' ' . $event_array['event_start_time']);
			 		$start_timestamp = $EM_DateTime->getTimestamp();
			 		$event['event_start'] = $meta_fields['_event_start'] = $EM_DateTime->getDateTime(true);
			 		$end_timestamp = $EM_DateTime->modify($event_array['event_end_date']. ' ' . $event_array['event_end_time'])->getTimestamp();
			 		$event['event_end'] = $meta_fields['_event_end'] = $EM_DateTime->getDateTime(true);
			 		//set indexes for reference further down
			 		$event_ids[$event_array['post_id']] = $event_array['event_id'];
			 		$event_dates[$event_array['event_id']] = $start_timestamp;
			 		$post_ids[$start_timestamp] = $event_array['post_id'];
			 		//do we need to change the slugs?
				    //(re)set post slug, which may need to be sanitized for length as we pre/postfix a date for uniqueness
				    $EM_DateTime->setTimestamp($start_timestamp);
				    $event_slug_date = $EM_DateTime->format( $recurring_date_format );
				    $event_slug = $this->sanitize_recurrence_slug($post_name, $event_slug_date);
				    $event_slug = apply_filters('em_event_save_events_recurrence_slug', $event_slug.'-'.$event_slug_date, $event_slug, $event_slug_date, $start_timestamp, $this); //use this instead
				    $post_fields['post_name'] = $event['event_slug'] = apply_filters('em_event_save_events_slug', $event_slug, $post_fields, $start_timestamp, array(), $this); //deprecated filter
			 		//adjust certain meta information relative to dates and times
			 		if( !empty($this->recurrence_rsvp_days) && is_numeric($this->recurrence_rsvp_days) ){
			 			$event_rsvp_days = $this->recurrence_rsvp_days >= 0 ? '+'. $this->recurrence_rsvp_days: $this->recurrence_rsvp_days;
			 			$event_rsvp_date = $EM_DateTime->setTimestamp($start_timestamp)->modify($event_rsvp_days.' days')->getDate();
			 			$event['event_rsvp_date'] = $meta_fields['_event_rsvp_date'] = $event_rsvp_date;
			 		}else{
			 			$event['event_rsvp_date'] = $meta_fields['_event_rsvp_date'] = $event_array['event_start_date'];
			 		}
			 		$event['event_rsvp_time'] = $meta_fields['_event_rsvp_time'] = $event['event_rsvp_time'];
			 		//add meta fields we deleted and are specific to this event
			 		$meta_fields['_event_start_date'] = $event_array['event_start_date'];
			 		$meta_fields['_event_start_local'] = $event_array['event_start_date']. ' ' . $event_array['event_start_time'];
			 		$meta_fields['_event_end_date'] = $event_array['event_end_date'];
			 		$meta_fields['_event_end_local'] = $event_array['event_end_date']. ' ' . $event_array['event_end_time'];
					$site_data = get_site_option('dbem_data');
					if( !empty($site_data['updates']['timezone-backcompat']) ){
				 		$meta_fields['_start_ts'] = $start_timestamp;
				 		$meta_fields['_end_ts'] = $end_timestamp;
					}
			 		//overwrite event and post tables
			 		$wpdb->update(EM_EVENTS_TABLE, $event, array('event_id' => $event_array['event_id']));
			 		$wpdb->update($wpdb->posts, $post_fields, array('ID' => $event_array['post_id']));
			 		//save meta field data for insertion in one go
			 		foreach($meta_fields as $meta_key => $meta_val){
			 			$meta_inserts[] = $wpdb->prepare("(%d, %s, %s)", array($event_array['post_id'], $meta_key, $meta_val));
			 		}
			 	}
			 	// delete all meta we'll be updating
			 	if( !empty($post_ids) ){
			 		$sql = "DELETE FROM {$wpdb->postmeta} WHERE post_id IN (".implode(',', $post_ids).")";
			 		if( !empty($exclude_meta_update_keys) ){
			 			$sql .= " AND meta_key NOT IN (";
			 			$i = 0;
			 			foreach( $exclude_meta_update_keys as $k ){
			 				$sql.= ( $i > 0 ) ? ',%s' : '%s';
						    $i++;
					    }
					    $sql .= ")";
			 			$sql = $wpdb->prepare($sql, $exclude_meta_update_keys);
				    }
			 		$wpdb->query($sql);
			 	}
			 	// insert the metas in one go, faster than one by one
			 	if( count($meta_inserts) > 0 ){
				 	$result = $wpdb->query("INSERT INTO ".$wpdb->postmeta." (post_id,meta_key,meta_value) VALUES ".implode(',',$meta_inserts));
				 	if($result === false){
				 		$this->add_error(esc_html__('There was a problem adding custom fields to your recurring events.','events-manager'));
				 	}
			 	}
			}
		 	//Next - Bookings. If we're completely rescheduling or just recreating bookings, we're deleting them and starting again
		 	if( ($this->recurring_reschedule || $this->recurring_recreate_bookings) && $this->recurring_recreate_bookings !== false && EM_ML::is_original($this) ){ //if set specifically to false, we skip bookings entirely (ML translations for example)
			 	//first, delete all bookings & tickets if we haven't done so during the reschedule above - something we'll want to change later if possible so bookings can be modified without losing all data
			 	if( !$this->recurring_reschedule ){
				 	//create empty EM_Bookings and EM_Tickets objects to circumvent extra loading of data and SQL queries
			 		$EM_Bookings = new EM_Bookings();
			 		$EM_Tickets = new EM_Tickets();
			 		foreach($events as $event){ //$events was defined in the else statement above so we reuse it
			 			if($event['recurrence_id'] == $this->event_id){
			 				//trick EM_Bookings and EM_Tickets to think it was loaded, and make use of optimized delete functions since 5.7.3.4
			 				$EM_Bookings->event_id = $EM_Tickets->event_id = $event['event_id'];
			 				$EM_Bookings->delete();
			 				$EM_Tickets->delete();
			 			}
			 		}
			 	}
			 	//if bookings hasn't been disabled, delete it all
			 	if( $this->event_rsvp ){
			 		$meta_inserts = array();
			 		foreach($this->get_tickets() as $EM_Ticket){
			 			/* @public $EM_Ticket EM_Ticket */
			 			//get array, modify event id and insert
			 			$ticket = $EM_Ticket->to_array();
			 			//empty cut-off dates of ticket, add them at per-event level
			 			unset($ticket['ticket_start']); unset($ticket['ticket_end']);
		 				if( !empty($ticket['ticket_meta']['recurrences']) ){
		 					$ticket_meta_recurrences = $ticket['ticket_meta']['recurrences'];
		 					unset($ticket['ticket_meta']['recurrences']);
		 				}
		 				//unset id
			 			unset($ticket['ticket_id']);
		 				$ticket['ticket_parent'] = $EM_Ticket->ticket_id;
					    //clean up ticket values
			 			foreach($ticket as $k => $v){
			 				if( empty($v) && $k != 'ticket_name' ){ 
			 					$ticket[$k] = 'NULL';
			 				}else{
			 					$data_type = !empty($EM_Ticket->fields[$k]['type']) ? $EM_Ticket->fields[$k]['type']:'%s';
			 					if(is_array($ticket[$k])) $v = serialize($ticket[$k]);
			 					$ticket[$k] = $wpdb->prepare($data_type,$v);
			 				}
			 			}
			 			//prep ticket meta for insertion with relative info for each event date
			 			$EM_DateTime = $this->start()->copy();
			 			foreach($event_ids as $event_id){
			 				$ticket['event_id'] = $event_id;
			 				$ticket['ticket_start'] = $ticket['ticket_end'] = 'NULL';
			 				//sort out cut-of dates
			 				if( !empty($ticket_meta_recurrences) ){
			 					$EM_DateTime->setTimestamp($event_dates[$event_id]); //by using EM_DateTime we'll generate timezone aware dates
			 					if( array_key_exists('start_days', $ticket_meta_recurrences) && $ticket_meta_recurrences['start_days'] !== false && $ticket_meta_recurrences['start_days'] !== null  ){
			 						$ticket_start_days = $ticket_meta_recurrences['start_days'] >= 0 ? '+'. $ticket_meta_recurrences['start_days']: $ticket_meta_recurrences['start_days'];
			 						$ticket_start_date = $EM_DateTime->modify($ticket_start_days.' days')->getDate();
			 						$ticket['ticket_start'] = "'". $ticket_start_date . ' '. $ticket_meta_recurrences['start_time'] ."'";
			 					}
			 					if( array_key_exists('end_days', $ticket_meta_recurrences) && $ticket_meta_recurrences['end_days'] !== false && $ticket_meta_recurrences['end_days'] !== null ){
			 						$ticket_end_days = $ticket_meta_recurrences['end_days'] >= 0 ? '+'. $ticket_meta_recurrences['end_days']: $ticket_meta_recurrences['end_days'];
			 						$EM_DateTime->setTimestamp($event_dates[$event_id]);
			 						$ticket_end_date = $EM_DateTime->modify($ticket_end_days.' days')->getDate();
			 						$ticket['ticket_end'] = "'". $ticket_end_date . ' '. $ticket_meta_recurrences['end_time'] . "'";
			 					}
			 				}
			 				//add insert data
			 				$meta_inserts[] = "(".implode(",",$ticket).")";
			 			}
			 		}
			 		$keys = "(".implode(",",array_keys($ticket)).")";
			 		$values = implode(',',$meta_inserts);
			 		$sql = "INSERT INTO ".EM_TICKETS_TABLE." $keys VALUES $values";
			 		$result = $wpdb->query($sql);
			 	}
		 	}elseif( $this->recurring_delete_bookings ){
		 		//create empty EM_Bookings and EM_Tickets objects to circumvent extra loading of data and SQL queries
		 		$EM_Bookings = new EM_Bookings();
		 		$EM_Tickets = new EM_Tickets();
		 		foreach($events as $event){ //$events was defined in the else statement above so we reuse it
		 			if($event['recurrence_id'] == $this->event_id){
		 				//trick EM_Bookings and EM_Tickets to think it was loaded, and make use of optimized delete functions since 5.7.3.4
		 				$EM_Bookings->event_id = $EM_Tickets->event_id = $event['event_id'];
		 				$EM_Bookings->delete();
		 				$EM_Tickets->delete();
		 			}
		 		}
		 	}
		 	//copy the event tags and categories, which are automatically deleted/recreated by WP and EM_Categories
		 	foreach( self::get_taxonomies() as $tax_name => $tax_data ){
		 		//In MS Global mode, we also save category meta information for global lookups so we use our objects
				if( $tax_name == 'category' ){
					//we save index data for each category in in MS Global mode
					foreach($event_ids as $post_id => $event_id){
						//set and trick category event and post ids so it saves to the right place
						$this->get_categories()->event_id = $event_id;
						$this->get_categories()->post_id = $post_id;
						$this->get_categories()->save();
					}
				 	$this->get_categories()->event_id = $this->event_id;
				 	$this->get_categories()->post_id = $this->post_id;
				}else{
					//general taxonomies including event tags
				 	$terms = get_the_terms( $this->post_id, $tax_data['name']);
			 		$term_slugs = array();
			 		if( is_array($terms) ){
						foreach($terms as $term){
							if( !empty($term->slug) ) $term_slugs[] = $term->slug; //save of category will soft-fail if slug is empty
						}
			 		}
				 	foreach($post_ids as $post_id){
						wp_set_object_terms($post_id, $term_slugs, $tax_data['name']);
				 	}
				}
		 	}
			if( 'future' == $this->post_status ){
				$time = strtotime( $this->post_date_gmt . ' GMT' );
				foreach( $post_ids as $post_id ){
					if( !$this->recurring_reschedule ){
						wp_clear_scheduled_hook( 'publish_future_post', array( $post_id ) ); // clear anything else in the system
					}
					wp_schedule_single_event( $time, 'publish_future_post', array( $post_id ) );
				}
			}
			return apply_filters('em_event_save_events', !in_array(false, $event_saves) && $result !== false, $this, $event_ids, $post_ids);
		}elseif( !$this->is_published() && $this->get_previous_status() != $this->get_status() && defined('EM_FORCE_RECURRENCES_SAVE') && EM_FORCE_RECURRENCES_SAVE ){
			$this->set_status_events($this->get_status());
		}
		return apply_filters('em_event_save_events', false, $this, $event_ids, $post_ids);
	}
	
	/**
	 * Ensures a post slug is the correct length when the date postfix is added, which takes into account multibyte and url-encoded characters and WP unique suffixes.
	 * If a url-encoded slug is nearing 200 characters (the data character limit in the db table), adding a date to the end will cause issues when saving to the db.
	 * This function checks if the final slug is longer than 200 characters and removes one entire character rather than part of a hex-encoded character, until the right size is met.
	 * @param string $post_name
	 * @param string $post_slug_postfix
	 * @return string
	 */
	public function sanitize_recurrence_slug( $post_name, $post_slug_postfix ){
		if( strlen($post_name.'-'.$post_slug_postfix) > 200 ){
			if( preg_match('/^(.+)(\-[0-9]+)$/', $post_name, $post_name_parts) ){
				$post_name_decoded = urldecode($post_name_parts[1]);
				$post_name_suffix =  $post_name_parts[2];
			}else{
				$post_name_decoded = urldecode($post_name);
				$post_name_suffix = '';
			}
			$post_name_maxlength = 200 - strlen( $post_name_suffix . '-' . $post_slug_postfix);
			if ( $post_name_parts[0] === $post_name_decoded.$post_name_suffix ){
				$post_name = substr( $post_name_decoded, 0, $post_name_maxlength );
			}else{
				$post_name = utf8_uri_encode( $post_name_decoded, $post_name_maxlength );
			}
			$post_name = rtrim( $post_name, '-' ). $post_name_suffix;
		}
		return apply_filters('em_event_sanitize_recurrence_slug', $post_name, $post_slug_postfix, $this);
	}
	
	/**
	 * Removes all recurrences of a recurring event.
	 * @return null
	 */
	function delete_events(){
		global $wpdb;
		do_action('em_event_delete_events_pre', $this);
		//So we don't do something we'll regret later, we could just supply the get directly into the delete, but this is safer
		$result = false;
		$events_array = array();
		if( $this->can_manage('delete_events', 'delete_others_events') ){
			//delete events from em_events table
			$sql = $wpdb->prepare('SELECT event_id FROM '.EM_EVENTS_TABLE.' WHERE (recurrence!=1 OR recurrence IS NULL)  AND recurrence_id=%d', $this->event_id);
			$event_ids = $wpdb->get_col( $sql );
			// go through each event and delete individually so individual hooks are fired appropriately
			foreach($event_ids as $event_id){
				$EM_Event = em_get_event( $event_id );
				if($EM_Event->recurrence_id == $this->event_id){
					$EM_Event->delete(true);
					$events_array[] = $EM_Event;
				}
			}
			$result = !empty($events_array) || (is_array($event_ids) && empty($event_ids)); // success if we deleted something, or if there was nothing to delete in the first place
		}
		$result = apply_filters('delete_events', $result, $this, $events_array); //Deprecated, use em_event_delete_events
		return apply_filters('em_event_delete_events', $result, $this, $events_array);
	}
	
	/**
	 * Returns the days that match the recurrance array passed (unix timestamps)
	 * @param array $recurrence
	 * @return array
	 */
	function get_recurrence_days(){
		//get timestampes for start and end dates, both at 12AM
		$start_date = $this->start()->copy()->setTime(0,0,0)->getTimestamp();
		$end_date = $this->end()->copy()->setTime(0,0,0)->getTimestamp();
		
		$weekdays = explode(",", $this->recurrence_byday); //what days of the week (or if monthly, one value at index 0)
		$matching_days = array(); //the days we'll be returning in timestamps
		
		//generate matching dates based on frequency type
		switch ( $this->recurrence_freq ){ /* @var EM_DateTime $current_date */
			case 'daily':
				//If daily, it's simple. Get start date, add interval timestamps to that and create matching day for each interval until end date.
				$current_date = $this->start()->copy()->setTime(0,0,0);
				while( $current_date->getTimestamp() <= $end_date ){
					$matching_days[] = $current_date->getTimestamp();
					$current_date->add('P'. $this->recurrence_interval .'D');
				}
				break;
			case 'weekly':
				//sort out week one, get starting days and then days that match time span of event (i.e. remove past events in week 1)
				$current_date = $this->start()->copy()->setTime(0,0,0);
				$start_of_week = get_option('start_of_week'); //Start of week depends on WordPress
				//then get the timestamps of weekdays during this first week, regardless if within event range
				$start_weekday_dates = array(); //Days in week 1 where there would events, regardless of event date range
				for($i = 0; $i < 7; $i++){
					if( in_array( $current_date->format('w'), $weekdays) ){
						$start_weekday_dates[] = $current_date->getTimestamp(); //it's in our starting week day, so add it
					}
					$current_date->add('P1D'); //add a day
				}					
				//for each day of eventful days in week 1, add 7 days * weekly intervals
				foreach ($start_weekday_dates as $weekday_date){
					//Loop weeks by interval until we reach or surpass end date
					$current_date->setTimestamp($weekday_date);
					while($current_date->getTimestamp() <= $end_date){
						if( $current_date->getTimestamp() >= $start_date && $current_date->getTimestamp() <= $end_date ){
							$matching_days[] = $current_date->getTimestamp();
						}
						$current_date->add('P'. ($this->recurrence_interval * 7 ) .'D');
					}
				}//done!
				break;  
			case 'monthly':
				//loop months starting this month by intervals
				$current_date = $this->start()->copy();
				$current_date->modify($current_date->format('Y-m-01 00:00:00')); //Start date on first day of month, done this way to avoid 'first day of' issues in PHP < 5.6
				while( $current_date->getTimestamp() <= $this->end()->getTimestamp() ){
					$last_day_of_month = $current_date->format('t');
					//Now find which day we're talking about
					$current_week_day = $current_date->format('w');
					$matching_month_days = array();
					//Loop through days of this years month and save matching days to temp array
					for($day = 1; $day <= $last_day_of_month; $day++){
						if((int) $current_week_day == $this->recurrence_byday){
							$matching_month_days[] = $day;
						}
						$current_week_day = ($current_week_day < 6) ? $current_week_day+1 : 0;							
					}
					//Now grab from the array the x day of the month
					$matching_day = false;
					if( $this->recurrence_byweekno > 0 ){
						//date might not exist (e.g. fifth Sunday of a month) so only add if it exists
						if( !empty($matching_month_days[$this->recurrence_byweekno-1]) ){
							$matching_day = $matching_month_days[$this->recurrence_byweekno-1];
						}
					}else{
						//last day of month, so we pop the last matching day
						$matching_day = array_pop($matching_month_days);
					}
					//if we have a matching day, get the timestamp, make sure it's within our start/end dates for the event, and add to array if it is
					if( !empty($matching_day) ){
						$matching_date = $current_date->setDate( $current_date->format('Y'), $current_date->format('m'), $matching_day )->getTimestamp();
						if($matching_date >= $start_date && $matching_date <= $end_date){
							$matching_days[] = $matching_date;
						}
					}
					//add the monthly interval to the current date, but set to 1st of current month first so we don't jump months where $current_date is 31st and next month there's no 31st (so a month is skipped)
					$current_date->modify($current_date->format('Y-m-01')); //done this way to avoid 'first day of ' PHP < 5.6 issues
					$current_date->add('P'.$this->recurrence_interval.'M');
				}
				break;
			case 'yearly':
				//Yearly is easy, we get the start date as a cloned EM_DateTime and keep adding a year until it surpasses the end EM_DateTime value. 
				$EM_DateTime = $this->start()->copy();
				while( $EM_DateTime <= $this->end() ){
					$matching_days[] = $EM_DateTime->getTimestamp();
					$EM_DateTime->add('P'.absint($this->recurrence_interval).'Y');
				}			
				break;
		}
		sort($matching_days);
		return apply_filters('em_events_get_recurrence_days', $matching_days, $this);
	}
	
	/**
	 * If event is recurring, set recurrences to same status as template
	 * @param $status
	 */
	function set_status_events($status){
		//give sub events same status
		global $wpdb;
		//get post and event ids of recurrences
		$post_ids = $event_ids = array();
		$events_array = EM_Events::get( array('recurrence'=>$this->event_id, 'scope'=>'all', 'status'=>false, 'array'=>true) ); //only get recurrences that aren't trashed or drafted
		foreach( $events_array as $event_array ){
			$post_ids[] = absint($event_array['post_id']);
			$event_ids[] = absint($event_array['event_id']);
		}
		if( !empty($post_ids) ){
			//decide on what status to set and update wp_posts in the process
			if($status === null){ 
				$set_status='NULL'; //draft post
				$post_status = 'draft'; //set post status in this instance
			}elseif( $status == -1 ){ //trashed post
				$set_status = -1;
				$post_status = 'trash'; //set post status in this instance
			}else{
				$set_status = $status ? 1:0; //published or pending post
				$post_status = $set_status ? 'publish':'pending';
			}
			if( EM_MS_GLOBAL ) switch_to_blog( $this->blog_id );
			$result = $wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->posts." SET post_status=%s WHERE ID IN (". implode(',', $post_ids) .')', array($post_status)) );
			restore_current_blog();
			$result = $result && $wpdb->query( $wpdb->prepare("UPDATE ".EM_EVENTS_TABLE." SET event_status=%s WHERE event_id IN (". implode(',', $event_ids) .')', array($set_status)) );
		}
		return apply_filters('em_event_set_status_events', $result !== false, $status, $this, $event_ids, $post_ids);
	}
	
	/**
	 * Returns a string representation of this recurrence. Will return false if not a recurrence
	 * @return string
	 */
	function get_recurrence_description() {
		$EM_Event_Recurring = $this->get_event_recurrence(); 
		$recurrence = $this->to_array();
		$weekdays_name = array( translate('Sunday'),translate('Monday'),translate('Tuesday'),translate('Wednesday'),translate('Thursday'),translate('Friday'),translate('Saturday'));
		$monthweek_name = array('1' => __('the first %s of the month', 'events-manager'),'2' => __('the second %s of the month', 'events-manager'), '3' => __('the third %s of the month', 'events-manager'), '4' => __('the fourth %s of the month', 'events-manager'), '5' => __('the fifth %s of the month', 'events-manager'), '-1' => __('the last %s of the month', 'events-manager'));
		$output = sprintf (__('From %1$s to %2$s', 'events-manager'),  $EM_Event_Recurring->event_start_date, $EM_Event_Recurring->event_end_date).", ";
		if ($EM_Event_Recurring->recurrence_freq == 'daily')  {
			$freq_desc =__('everyday', 'events-manager');
			if ($EM_Event_Recurring->recurrence_interval > 1 ) {
				$freq_desc = sprintf (__("every %s days", 'events-manager'), $EM_Event_Recurring->recurrence_interval);
			}
		}elseif ($EM_Event_Recurring->recurrence_freq == 'weekly')  {
			$weekday_array = explode(",", $EM_Event_Recurring->recurrence_byday);
			$natural_days = array();
			foreach($weekday_array as $day){
				array_push($natural_days, $weekdays_name[$day]);
			}
			$output .= implode(", ", $natural_days);
			$freq_desc = " " . __("every week", 'events-manager');
			if ($EM_Event_Recurring->recurrence_interval > 1 ) {
				$freq_desc = " ".sprintf (__("every %s weeks", 'events-manager'), $EM_Event_Recurring->recurrence_interval);
			}
			
		}elseif ($EM_Event_Recurring->recurrence_freq == 'monthly')  {
			$weekday_array = explode(",", $EM_Event_Recurring->recurrence_byday);
			$natural_days = array();
			foreach($weekday_array as $day){
				if( is_numeric($day) ){
					array_push($natural_days, $weekdays_name[$day]);
				}
			}
			$freq_desc = sprintf (($monthweek_name[$EM_Event_Recurring->recurrence_byweekno]), implode(" and ", $natural_days));
			if ($EM_Event_Recurring->recurrence_interval > 1 ) {
				$freq_desc .= ", ".sprintf (__("every %s months",'events-manager'), $EM_Event_Recurring->recurrence_interval);
			}
		}elseif ($EM_Event_Recurring->recurrence_freq == 'yearly')  {
			$freq_desc = __("every year", 'events-manager');
			if ($EM_Event_Recurring->recurrence_interval > 1 ) {
				$freq_desc .= sprintf (__("every %s years",'events-manager'), $EM_Event_Recurring->recurrence_interval);
			}
		}else{
			$freq_desc = "[ERROR: corrupted database record]";
		}
		$output .= $freq_desc;
		return  $output;
	}	
	
	/**********************************************************
	 * UTILITIES
	 ***********************************************************/
	function to_array( $db = false ){
		$event_array = parent::to_array($db);
		//we reset start/end datetimes here, based on the EM_DateTime objects if they are valid
		$event_array['event_start'] = $this->start()->valid ? $this->start(true)->format('Y-m-d H:i:s') : null;
		$event_array['event_end'] = $this->end()->valid ? $this->end(true)->format('Y-m-d H:i:s') : null;
		return apply_filters('em_event_to_array', $event_array, $this);
	}
	
	/**
	 * Can the user manage this? 
	 */
	function can_manage( $owner_capability = false, $admin_capability = false, $user_to_check = false ){
		if( ($this->just_added_event || $this->event_id == '') && !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ){
			$user_to_check = get_option('dbem_events_anonymous_user');
		}
		return apply_filters('em_event_can_manage', parent::can_manage($owner_capability, $admin_capability, $user_to_check), $this, $owner_capability, $admin_capability, $user_to_check);
	}
	
	/**
	 * Outputs a JSON-encodable associative array of data to output to REST or other remote operations
	 * @return array
	 */
	function to_api(){
		$event = array (
			'name' => $this->event_name,
			'id' => $this->event_id,
			'post_id' => $this->post_id,
			'parent' => $this->event_parent,
			'owner' => $this->event_owner, // overwritten further down
			'blog_id' => $this->blog_id,
			'group_id' => $this->group_id,
			'slug' => $this->event_slug,
			'status' => $this->event_private,
			'content' => $this->post_content,
			'bookings' => array (
				'end_date' => $this->event_rsvp_date,
				'end_time' => $this->event_rsvp_time,
				'rsvp_spaces' => $this->event_rsvp_spaces,
				'spaces' => $this->event_spaces,
			),
			'when' => array(
				'all_day' => $this->event_all_day,
				'start' => $this->event_start,
				'start_date' => $this->event_start_date,
				'start_time' => $this->event_start_time,
				'end' => $this->event_end,
				'end_date' => $this->event_end_date,
				'end_time' => $this->event_end_time,
				'timezone' => $this->event_timezone,
			),
			'location' => false,
			'recurrence' => false,
			'language' => $this->event_language,
			'translation' => $this->event_translation,
		);
		if( $this->event_owner ){
			// anonymous
			$event['owner'] = array(
				'guest' => true,
				'email' => $this->get_contact()->user_email,
				'name' => $this->get_contact()->get_name(),
			);
		}else{
			// user
			$event['owner'] = array(
				'guest' => false,
				'email' => $this->get_contact()->user_email,
				'name' => $this->get_contact()->get_name(),
			);
		}
		if( $this->recurrence_id ){
			$event['recurrence_id'] = $this->recurrence_id;
		}
		if( $this->recurrence ){
			$event['recurrence'] = array (
				'interval' => $this->recurrence_interval,
				'freq' => $this->recurrence_freq,
				'days' => $this->recurrence_days,
				'byday' => $this->recurrence_byday,
				'byweekno' => $this->recurrence_byweekno,
				'rsvp_days' => $this->recurrence_rsvp_days,
			);
		}
		if( $this->has_location() ) {
			$EM_Location = $this->get_location();
			$event['location'] = $EM_Location->to_api();
		}elseif( $this->has_event_location() ){
			$event['location_type'] = $this->event_location_type;
			$event['location'] = $this->get_event_location()->to_api();
		}
		return $event;
	}
	
	public static function get_active_statuses(){
		if( !empty(static::$active_statuses) ) {
			return static::$active_statuses;
		}
		$statuses = array(
			0 => __('Cancelled', 'events-manager'),
			1 => __('Active', 'events-manager')
		);
		static::$active_statuses = apply_filters('event_get_active_statuses', $statuses);
		return static::$active_statuses;
	}
}

//TODO placeholder targets filtering could be streamlined better
/**
 * This is a temporary filter function which mimicks the old filters in the old 2.x placeholders function
 * @param string $result
 * @param EM_Event $event
 * @param string $placeholder
 * @param string $target
 * @return mixed
 */
function em_event_output_placeholder($result,$event,$placeholder,$target='html'){
	if( $target == 'raw' ) return $result;
	if( in_array($placeholder, array("#_EXCERPT",'#_EVENTEXCERPT','#_EVENTEXCERPTCUT', "#_LOCATIONEXCERPT")) && $target == 'html' ){
		$result = apply_filters('dbem_notes_excerpt', $result);
	}elseif( $placeholder == '#_CONTACTEMAIL' && $target == 'html' ){
		$result = em_ascii_encode($event->get_contact()->user_email);
	}elseif( in_array($placeholder, array('#_EVENTNOTES','#_NOTES','#_DESCRIPTION','#_LOCATIONNOTES','#_CATEGORYNOTES','#_CATEGORYDESCRIPTION')) ){
		if($target == 'rss'){
			$result = apply_filters('dbem_notes_rss', $result);
			$result = apply_filters('the_content_rss', $result);
		}elseif($target == 'map'){
			$result = apply_filters('dbem_notes_map', $result);
		}elseif($target == 'ical'){
			$result = apply_filters('dbem_notes_ical', $result);
		}elseif ($target == "email"){    
			$result = apply_filters('dbem_notes_email', $result); 
	  	}else{ //html
			$result = apply_filters('dbem_notes', $result);
		}
	}elseif( in_array($placeholder, array("#_NAME",'#_LOCATION','#_TOWN','#_ADDRESS','#_LOCATIONNAME',"#_EVENTNAME","#_LOCATIONNAME",'#_CATEGORY')) ){
		if ($target == "rss"){
			$result = apply_filters('dbem_general_rss', $result);
	  	}elseif ($target == "ical"){    
			$result = apply_filters('dbem_general_ical', $result); 
	  	}elseif ($target == "email"){    
			$result = apply_filters('dbem_general_email', $result); 
	  	}else{ //html
			$result = apply_filters('dbem_general', $result); 
	  	}				
	}
	return $result;
}
add_filter('em_category_output_placeholder','em_event_output_placeholder',1,4);
add_filter('em_event_output_placeholder','em_event_output_placeholder',1,4);
add_filter('em_location_output_placeholder','em_event_output_placeholder',1,4);
// FILTERS
// filters for general events field (corresponding to those of  "the _title")
add_filter('dbem_general', 'wptexturize');
add_filter('dbem_general', 'convert_chars');
add_filter('dbem_general', 'trim');
// filters for the notes field in html (corresponding to those of  "the _content")
add_filter('dbem_notes', 'wptexturize');
add_filter('dbem_notes', 'convert_smilies');
add_filter('dbem_notes', 'convert_chars');
add_filter('dbem_notes', 'wpautop');
add_filter('dbem_notes', 'prepend_attachment');
add_filter('dbem_notes', 'do_shortcode');
// filters for the notes field in html (corresponding to those of  "the _content")
add_filter('dbem_notes_excerpt', 'wptexturize');
add_filter('dbem_notes_excerpt', 'convert_smilies');
add_filter('dbem_notes_excerpt', 'convert_chars');
add_filter('dbem_notes_excerpt', 'wpautop');
add_filter('dbem_notes_excerpt', 'prepend_attachment');
add_filter('dbem_notes_excerpt', 'do_shortcode');
// RSS content filter
add_filter('dbem_notes_rss', 'convert_chars', 8);
add_filter('dbem_general_rss', 'esc_html', 8);
// Notes map filters
add_filter('dbem_notes_map', 'convert_chars', 8);
add_filter('dbem_notes_map', 'js_escape');
//embeds support if using placeholders
if ( is_object($GLOBALS['wp_embed']) ){
	add_filter( 'dbem_notes', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
	add_filter( 'dbem_notes', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
}

// booking form notices, overridable to inject other content (e.g. waiting list)
function em_booking_form_status_disabled(){
	echo '<p>'. get_option('dbem_bookings_form_msg_disabled') .'</p>';
}
add_action('em_booking_form_status_disabled', 'em_booking_form_status_disabled');

function em_booking_form_status_full(){
	echo '<p>'. get_option('dbem_bookings_form_msg_full') .'</p>';
}
add_action('em_booking_form_status_full', 'em_booking_form_status_full');

function em_booking_form_status_closed(){
	echo '<p>'. get_option('dbem_bookings_form_msg_closed') .'</p>';
}
add_action('em_booking_form_status_closed', 'em_booking_form_status_closed');

function em_booking_form_status_cancelled(){
	echo '<p>'. get_option('dbem_bookings_form_msg_cancelled') .'</p>';
}
add_action('em_booking_form_status_cancelled', 'em_booking_form_status_cancelled');

function em_booking_form_status_already_booked(){
	echo get_option('dbem_bookings_form_msg_attending');
	echo '<a href="'. em_get_my_bookings_url() .'">'. get_option('dbem_bookings_form_msg_bookings_link') .'</a>';
}
add_action('em_booking_form_status_already_booked', 'em_booking_form_status_already_booked');

/**
 * This function replaces the default gallery shortcode, so it can check if this is a recurring event recurrence and pass on the parent post id as the default post. 
 * @param array $attr
 */
function em_event_gallery_override( $attr = array() ){
	global $post;
	if( !empty($post->post_type) && $post->post_type == EM_POST_TYPE_EVENT && empty($attr['id']) && empty($attr['ids']) ){
		//no id specified, so check if it's recurring and override id with recurrence template post id
		$EM_Event = em_get_event($post->ID, 'post_id');
		if( $EM_Event->is_recurrence() ){
			$attr['id'] = $EM_Event->get_event_recurrence()->post_id;
		}
	}
	return gallery_shortcode($attr);
}
function em_event_gallery_override_init(){
	remove_shortcode('gallery');
	add_shortcode('gallery', 'em_event_gallery_override');
}
add_action('init','em_event_gallery_override_init', 1000); //so that plugins like JetPack don't think we're overriding gallery, we're not i swear!
?>