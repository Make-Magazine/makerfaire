<?php
/**
 * Class wdtFilterWidget is used to create filtering widget for wpDataTables
 *
 * @author Alexander Gilmanov
 *
 * @since March 2014
 */
 
 class wdtFilterWidget extends WP_Widget {
 	
 	public function wdtFilterWidget(){
 		parent::__construct(false, 'wpDataTables filtering widget');
 	}
 	
	function widget( $args, $instance ) {
		// Widget output
		if( !isset($instance['title']) ) {
			$title = __( 'Filter', 'wpdatatables' );
		}else{
                    $title = $instance['title'];
                }
		$title = apply_filters( 'widget_title', $title );

		echo $args['before_widget'];

		$title = $args['before_title'] . $title . $args['after_title'];
				
		$tpl = new PDTTpl();
		$tpl->setTemplate('filter_widget.inc.php');
		$tpl->addData('title',$title);
		echo $tpl->showData();
		echo $args['after_widget'];
	}

	function form( $instance ) {
		// Output admin widget options form
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	} 	
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}	
 	
 }

?>