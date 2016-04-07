<?php
/**
 * Browse charts for the admin panel
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WDTBrowseChartsTable extends WP_List_Table {
    
    public function get_columns(){
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => 'ID',
            'title' => 'Title',
            'engine' => 'Render Engine',
            'type' => 'Chart Type',
            'shortcode' => 'Shortcode'
        );
    }
    
    public function get_sortable_columns(){
        return array(
            'id' => array('id', true),
            'title' => array('title', false),
            'engine' => array('engine', false),
            'type' => array('type', false)
        );
    }
    
    public function get_chart_count(){
          global $wpdb;

          $query = "SELECT COUNT(*) FROM {$wpdb->prefix}wpdatacharts";

          $count = $wpdb->get_var( $query );

          return $count;
    }    
    
    /**
     * Get all tables for the browser
     */
     function get_all_charts(){
        global $wpdb;
        $query = "SELECT id, title, type, engine
                    FROM {$wpdb->prefix}wpdatacharts ";

        if(isset($_REQUEST['s'])){
            $query .= " WHERE title LIKE '%".sanitize_text_field($_POST['s'])."%' ";
        }

        if(isset($_REQUEST['orderby'])){
            if( in_array( 
                    $_REQUEST['orderby'], 
                    array(
                        'id',
                        'title',
                        'engine',
                        'type'
                        )
                    )
                    ){
                $query .= " ORDER BY ".$_GET['orderby'];
                if($_REQUEST['order'] == 'desc'){
                    $query .= " DESC ";
                }else{
                    $query .= " ASC ";
                }
            }
        }else{
            $query .= " ORDER BY id ASC ";
        }

        if(isset($_REQUEST['paged'])){
                $paged = (int) $_REQUEST['paged'];
        }else{
                $paged = 1;
        }

        $tables_per_page = get_option('wdtTablesPerPage') ? get_option('wdtTablesPerPage') : 10;

        $query .= " LIMIT ".($paged-1)*$tables_per_page.", ".$tables_per_page;

        $all_charts = apply_filters( 'wpdatatables_filter_browse_charts', $wpdb->get_results( $query, ARRAY_A ) );

        return $all_charts;
    }
    
    public function column_default( $item, $column_name ){
            switch( $column_name ){
                    case 'shortcode':
                        return '[wpdatachart id='.$item['id'].']';
                        break;
                    case 'id':
                    case 'title':
                    default:
                        return $item[ $column_name ];
                        break;
            }
    }
    
    public function column_title($item){
            $actions = array(
                    'edit' => '<a href="admin.php?page=wpdatatables-chart-wizard&chart_id='.$item['id'].'" title="'.__('Edit','wpdatatables').'">'.__('Edit','wpdatatables').'</a>',
                    'trash' => '<a class="submitdelete" title="'.__('Delete','wpdatatables').'" href="admin.php?page=wpdatatables-charts&action=delete&chart_id='.$item['id'].'" rel="'.$item['id'].'">'.__('Delete','wpdatatables').'</a>'
            );

            return '<a href="admin.php?page=wpdatatables-chart-wizard&chart_id='.$item['id'].'">'.$item['title'].'</a> '.$this->row_actions($actions);

    }
	
    public function get_bulk_actions() {
      $actions = array(
        'delete'    => 'Delete'
      );
      return $actions;
    }
	
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="chart_id[]" value="%s" />', $item['id']
        );
    }
    
    public function column_type( $item ){
        switch( $item['type'] ){
            case 'google_column_chart':
                return __( 'Google column chart', 'wpdatatables' );
                break;
            case 'google_histogram':
                return __( 'Google Histogram', 'wpdatatables' );
                break;
            case 'google_bar_chart':
                return __( 'Google Bar Chart', 'wpdatatables' );
                break;
            case 'google_area_chart':
                return __( 'Google Area Chart', 'wpdatatables' );
                break;
            case 'google_stepped_area_chart':
                return __( 'Google Stepped Area Chart', 'wpdatatables' );
                break;
            case 'google_line_chart':
                return __( 'Google Line Chart', 'wpdatatables' );
                break;
            case 'google_pie_chart':
                return __( 'Google Pie Chart', 'wpdatatables' );
                break;
            case 'google_bubble_chart':
                return __( 'Google Bubble Chart', 'wpdatatables' );
                break;
            case 'google_donut_chart':
                return __( 'Google Donut Chart', 'wpdatatables' );
                break;
            case 'google_gauge_chart':
                return __( 'Google Gauge Chart', 'wpdatatables' );
                break;
            case 'google_scatter_chart':
                return __( 'Google Scatter Chart', 'wpdatatables' );
                break;
            case 'highcharts_line_chart':
                return __( 'Highcharts Line Chart', 'wpdatatables' );
                break;
            case 'highcharts_basic_area_chart':
                return __( 'Highcharts Basic Area Chart', 'wpdatatables' );
                break;
            case 'highcharts_stacked_area_chart':
                return __( 'Highcharts Stacked Area Chart', 'wpdatatables' );
                break;
            case 'highcharts_basic_bar_chart':
                return __( 'Highcharts Basic Bar Chart', 'wpdatatables' );
                break;
            case 'highcharts_stacked_bar_chart':
                return __( 'Highcharts Stacked Bar Chart', 'wpdatatables' );
                break;
            case 'highcharts_basic_column_chart':
                return __( 'Highcharts Basic Column Chart', 'wpdatatables' );
                break;
            case 'highcharts_stacked_column_chart':
                return __( 'Highcharts Stacked Column Chart', 'wpdatatables' );
                break;
            case 'highcharts_pie_chart':
                return __( 'Highcharts Pie Chart', 'wpdatatables' );
                break;
            case 'highcharts_pie_with_gradient_chart':
                return __( 'Highcharts Pie With Gradient Chart', 'wpdatatables' );
                break;
            case 'highcharts_donut_chart':
                return __( 'Highcharts Donut Chart', 'wpdatatables' );
                break;
            case 'highcharts_scatter_plot':
                return __( 'Highcharts Scatter Plot', 'wpdatatables' );
                break;
            case 'highcharts_3d_column_chart':
                return __( 'Highcharts 3D Column Chart', 'wpdatatables' );
                break;
            case 'highcharts_3d_pie_chart':
                return __( 'Highcharts 3D Pie Chart', 'wpdatatables' );
                break;
            case 'highcharts_3d_donut_chart':
                return __( 'Highcharts 3D Donut Chart', 'wpdatatables' );
                break;
            case 'highcharts_angular_gauge_chart':
                return __( 'Highcharts Angular Gauge Chart', 'wpdatatables' );
                break;
            case 'highcharts_solid_gauge_chart':
                return __( 'Highcharts Solid Gauge Chart', 'wpdatatables' );
                break;
            default:
                return $item;
                break;
        }
    }
    
    public function prepare_items(){
        $current_page = $this->get_pagenum();

        $per_page = get_option('wdtTablesPerPage') ? get_option('wdtTablesPerPage') : 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->set_pagination_args(
                array(
                    'total_items' => $this->get_chart_count(),
                    'per_page' => $per_page
                )
        );

        $this->items = $this->get_all_charts();
    }
    
    public function no_items() {
      _e( 'No wpDataCharts in the system yet.', 'wpdatatables' );
    }    
    
    
}

?>