<?php
/**
 * Browse table for the admin panel
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WDTBrowseTable extends WP_List_Table {
	
	function get_columns(){
		return array(
			'cb' => '<input type="checkbox" />',
			'id' => 'ID',
			'title' => 'Title',
			'table_type' => 'Type',
			'shortcode' => 'Shortcode',
			'functions' => 'Functions'
		);
	}
	
	function get_sortable_columns(){
		return array(
			'id' => array('id', true),
			'title' => array('title', false),
			'table_type' => array('table_type', false)
		);
	}
	
	function prepare_items(){
		
		$current_page = $this->get_pagenum();
		
		$per_page = get_option('wdtTablesPerPage') ? get_option('wdtTablesPerPage') : 10;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$this->set_pagination_args(
			array(
				'total_items' => wdt_get_table_count(),
				'per_page' => $per_page
			)
		);
		
		$this->items = wdt_get_all_tables();
	}
	
	function column_default( $item, $column_name ){
		switch( $column_name ){
			case 'shortcode':
				return '[wpdatatable id='.$item['id'].']';
				break;
			case 'functions':
                                $return_string = '<button class="button wpDataTablesDuplicateTable" data-table_id="'.$item['id'].'" data-table_name="'.$item['title'].'"><div class="dashicons dashicons-admin-page"></div>'.__('Duplicate','wpdatatables').'</button>';
                                if( $item['table_type'] == 'manual' ){
                                    $return_string .= ' <button class="button wpDataTablesManualEdit" data-table_id="'.$item['id'].'" data-table_name="'.$item['title'].'"><div class="dashicons dashicons-welcome-write-blog"></div>'.__('Edit data','wpdatatables').'</button>';
                                }
				return $return_string;
				break;
			case 'id':
			case 'title':
			case 'table_type':
			default:
				return $item[ $column_name ];
				break;
		}
	}
	
	
	function column_title($item){
		$actions = array(
			'edit' => '<a href="admin.php?page=wpdatatables-administration&action=edit&table_id='.$item['id'].'" title="'.__('Edit','wpdatatables').'">'.__('Edit','wpdatatables').'</a>',
			'trash' => '<a class="submitdelete" title="'.__('Delete','wpdatatables').'" href="admin.php?page=wpdatatables-administration&action=delete&table_id='.$item['id'].'" rel="'.$item['id'].'">'.__('Delete','wpdatatables').'</a>'
		);
		
		return '<a href="admin.php?page=wpdatatables-administration&action=edit&table_id='.$item['id'].'">'.$item['title'].'</a> '.$this->row_actions($actions);
		
	}
	
	function get_bulk_actions() {
	  $actions = array(
	    'delete'    => 'Delete'
	  );
	  return $actions;
	}
	
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="table_id[]" value="%s" />', $item['id']
        );    
    }	
    
    function no_items() {
      _e( 'No wpDataTables in the system yet.', 'wpdatatables' );
    }    
	
}


?>