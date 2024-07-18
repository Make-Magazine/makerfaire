<?php
/**
 * @author marcus
 * Standard events calendar widget
 */
class EM_Widget_Calendar extends WP_Widget {
	
	var $defaults = array();
	
	public static function init(){
		return register_widget("EM_Widget_Calendar");
	}
	
    /** constructor */
    function __construct() {
    	$this->defaults = array(
    		'title' => __('Calendar','events-manager'),
    		'long_events' => 0,
    		'category' => 0,
		    'scope' => 'all',
		    'calendar_size' => 'auto',
    	);
    	$widget_ops = array('description' => __( "Display your events in a calendar widget.", 'events-manager') );
        parent::__construct(false, $name = __('Events Calendar','events-manager'), $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	$instance = array_merge($this->defaults, $instance);

    	echo $args['before_widget'];
    	if( !empty($instance['title']) ){
		    echo $args['before_title'];
		    echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
		    echo $args['after_title'];
    	}
    	//Shall we show a specific month?
		if ( !empty($_REQUEST['calendar_day']) ) {
			$date = explode('-', $_REQUEST['calendar_day']);
			$instance['month'] = $date[1];
			$instance['year'] = $date[0];
		}else{
			$instance['month'] = date("m");
			$instance['year'] = date('Y');
		}
	    
	    //Our Widget Content  
	    echo EM_Calendar::output(apply_filters('em_widget_calendar_get_args',$instance));
	    
	    echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	//filter the new instance and replace blanks with defaults
    	$new_instance['title'] = (!isset($new_instance['title'])) ? $this->defaults['title']:$new_instance['title'];
    	$new_instance['long_events'] = isset($new_instance['long_events']) ? empty($new_instance['long_events']) : $this->defaults['long_events'];
	    $new_instance['category'] = ($new_instance['category'] == '') ? $this->defaults['category']:$new_instance['category'];
	    $new_instance['scope'] = ($new_instance['scope'] == 'future') ? 'future':$this->defaults['scope'];
	    $allowed_sizes = apply_filters('em_calendar_output_sizes', array('large', 'medium', 'small'));
	    $new_instance['calendar_size'] = in_array( $new_instance['calendar_size'], $allowed_sizes) ? $new_instance['calendar_size'] : $this->defaults['calendar_size'];
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	$instance = array_merge($this->defaults, $instance);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'events-manager'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('long_events'); ?>"><?php _e('Show Long Events?', 'events-manager'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('long_events'); ?>" name="<?php echo $this->get_field_name('long_events'); ?>" value="1" <?php echo ($instance['long_events'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('scope'); ?>"><?php _e('Future Events Only?','events-manager'); ?>: </label>
		    <input type="checkbox" id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>" value="future" <?php echo ($instance['scope'] == 'future') ? 'checked="checked"':''; ?>/>
	    </p>
		<p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category IDs','events-manager'); ?>: </label>
            <input type="text" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" size="3" value="<?php echo esc_attr($instance['category']); ?>" /><br />
            <em><?php _e('1,2,3 or 2 (0 = all)','events-manager'); ?> </em>
        </p>
	    <p>
		    <label for="<?php echo $this->get_field_id('calendar_size'); ?>"><?php _e('Calendar Size','events-manager'); ?>: </label>
		    <select id="<?php echo $this->get_field_id('calendar_size'); ?>" name="<?php echo $this->get_field_name('calendar_size'); ?>">
			    <option value="auto" <?php selected($instance['calendar_size'], 'auto'); ?>><?php esc_html_e('Responsive', 'events-manager'); ?></option>
			    <option value="large" <?php selected($instance['calendar_size'], 'large'); ?>><?php esc_html_e('Large', 'events-manager'); ?></option>
			    <option value="medium" <?php selected($instance['calendar_size'], 'medium'); ?>><?php esc_html_e('Medium', 'events-manager'); ?></option>
			    <option value="small" <?php selected($instance['calendar_size'], 'small'); ?>><?php esc_html_e('Small', 'events-manager'); ?></option>
		    </select>
	    </p>
        <?php 
    }

}
add_action('widgets_init', 'EM_Widget_Calendar::init');