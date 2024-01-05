<?php
namespace EM;
use WP_List_Table;

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class List_Table extends WP_List_Table {
	
	public $per_page = 20;
	public $total_items = 0;
	public $per_page_var = 'limit';
	public $has_checkboxes = true;
	public $checkbox_id = 'id';
	
	public $format;
	
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items(){
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		
		$this->per_page = $this->get_items_per_page( $this->per_page_var, $this->per_page );
		$this->items = $this->table_data();
		
		$this->set_pagination_args( array(
			'total_items' => $this->total_items,
			'per_page'    => $this->per_page,
			'total_pages' => ceil($this->total_items / $this->per_page),
		) );
		
		$this->_column_headers = array($columns, $hidden, $sortable);
	}
	
	/**
	 * Adds a wrapper to the top/bottom of the actual table of values
	 *
	 * @param $which
	 *
	 * @return void
	 */
	public function display_tablenav( $which ) {
		if ( $which == 'top' ) {
			parent::display_tablenav( $which );
			echo '<div class="table-wrap">';
		}else{
			echo '</div>';
			parent::display_tablenav( $which );
		}
	}
	
	/**
	 * Define which columns are hidden
	 *
	 * @return array
	 */
	public function get_hidden_columns(){
		return array();
	}
	
	/**
	 * Should be overriden, obtains data for populating the table.
	 * @return array
	 */
	protected function table_data(){
		return array();
	}
	
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array();
		if( $this->has_checkboxes ) {
			$columns['cb'] = '<input type="checkbox" />';
		}
		return $columns;
	}
	
	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ){
		if( !empty( $item->$column_name) ){
			return $item->$column_name;
		}
		return '';
	}
	
	/**
	 * Bulk Edit Checkbox
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		if( is_object($item) ){
			$id = $item->{$this->checkbox_id};
		}else{
			$id = $item[$this->checkbox_id];
		}
		return sprintf('<input type="checkbox" name="column_id[]" value="%s" />', $id);
	}
}