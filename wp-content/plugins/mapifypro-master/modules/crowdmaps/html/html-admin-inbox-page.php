<div class="wrap">
	<h1 class="wp-heading-inline"><?php _e( 'CrowdMaps Inbox', 'crowd' ) ?></h1>

	<?php if($updated): ?>
	<div class="updated notice mapifypro-notice">
		<p><?php _e( 'A location has been successfully published', 'crowd' ) ?></p>
	</div>
	<?php endif ?>

	<hr class="wp-header-end">
	<ul class="subsubsub">
		<li>
			<a href="<?php echo admin_url( 'admin.php?page=crowd-inbox' ) ?>" class="current">
				<?php _e( 'CrowdMaps Pending Review', 'crowd' ) ?> <span class="count">(<?php echo esc_html( $count_posts->pending ) ?>)</span>
			</a> |
		</li>
		<li>
			<a href="<?php echo admin_url( 'edit.php?post_type=map-location&post_status=publish' ) ?>">
				<?php _e( 'Published', 'crowd' ) ?> <span class="count">(<?php echo esc_html( $count_posts->publish ) ?>)</span>
			</a> |
		</li>
		<li>
			<a href="<?php echo admin_url( 'edit.php?post_type=map-location' ) ?>">
				<?php _e( 'All', 'crowd' ) ?> <span class="count">(<?php echo esc_html( $count_posts->publish + $count_posts->pending + $count_posts->draft + $count_posts->future ) ?>)</span>
			</a>
		</li>
	</ul>
	
	<form id="posts-filter">
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input"><?php _e( 'Search CrowdMaps Inbox', 'crowd' ) ?>:</label>
			<input type="search" id="post-search-input" name="s" value="<?php echo $search ?>">
			<input type="submit" id="search-submit" class="button" value="Search CrowdMaps Inbox">
			<input type="hidden" name="page" value="crowd-inbox">
		</p>

		<div class="tablenav top">
			<div class="alignleft actions">
				<?php 
					// months dropdown
					echo $wp_post_list_table->months_dropdown( 'map-location' );
					
					// users dropdown
					wp_dropdown_users( array(
						'show_option_all' => __( 'All Authors' ),
						'selected'        => isset( $_GET['user'] ) ? absint( $_GET['user'] ) : 0,
					) );

					// maps dropdown
					echo crowd_dropdown_maps();
				?>

				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
			</div>

			<div class="tablenav-pages <?php echo $max_page > 1 ? '' : 'one-page' ?>">
				<span class="displaying-num"><?php echo $total_number ?> <?php _e( 'items' ) ?></span>
				<span class="pagination-links">
					<a class="first-page button" href="<?php echo admin_url( 'admin.php?page=crowd-inbox' ) ?>"><span aria-hidden="true">«</span></a>
					<a class="prev-page button" href="<?php crowd_page_inbox_paginate_url( $paged - 1, $max_page ) ?>"><span aria-hidden="true">‹</span></a>
					<span class="paging-input"><label for="current-page-selector" class="screen-reader-text"><?php _e( 'Current Page' ) ?></label>
						<input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $paged ?>" size="1" aria-describedby="table-paging">
						<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $max_page ?></span></span>
					</span>
					<a class="next-page button" href="<?php crowd_page_inbox_paginate_url( $paged + 1, $max_page ) ?>"><span aria-hidden="true">›</span></a>
					<a class="last-page button" href="<?php crowd_page_inbox_paginate_url( $max_page, $max_page ) ?>"><span aria-hidden="true">»</span></a>
				</span>
			</div>
			<br class="clear">
		</div>
	</form>

	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th class="column-title column-primary"><?php _e( 'Title' ) ?></th>
				<th class="column-author"><?php _e( 'Author' ) ?></th>
				<th><?php _e( 'Maps', 'crowd' ) ?></th>
				<th class="column-categories"><?php _e( 'Categories' ) ?></th>
				<th class="column-date"><?php _e( 'Date' ) ?></th>				
			</tr>
		</thead>
		<tbody id="the-list">
			<?php 
				while ( $the_query->have_posts() ) : $the_query->the_post(); 
					global $post;					
					$map_location = new \Acf_Mapifypro\Model\Mapify_Map_Location( get_the_ID() );
					$map_ids      = $map_location->get_map_ids();
					$map_links    = '';
					?>
			
					<tr>
						<td>
							<strong><a class="row-title" href="<?php echo get_edit_post_link() ?>"><?php echo $map_location->get_name(); ?></a></strong>
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo get_edit_post_link() ?>"><?php _e( 'Edit' ) ?></a> | 
								</span>
								<span class="trash">
									<a href="<?php echo get_delete_post_link() ?>"><?php _e( 'Trash' ) ?></a> |
								</span>
								<span class="view">
									<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?action=crowd_publish_location&id=' . $map_location->post_id ), 'Lijtw52BURKDNmb3' ); ?>"><?php _e( 'Publish Map Location' ) ?></a> 
								</span>
							</div>
						</td>
						<td><?php $wp_post_list_table->column_author( $post ) ?></td>
						<td>
							<?php 
								foreach ( $map_ids as $map_id ) {
									$map_name     = get_the_title( $map_id );
									$map_edit_url = get_edit_post_link( $map_id );
									$map_links   .= sprintf( '<a href="%s">%s</a>, ', $map_edit_url, $map_name );
								}
								
								echo rtrim( $map_links, ', ' );
								?>
						</td>
						<td><?php $wp_post_list_table->column_default( $post, 'categories' ) ?></td>
						<td class="date column-date"><?php $wp_post_list_table->column_date( $post ) ?></td>
					</tr>

					<?php 
				endwhile; 

				// on empty
				if ( ! $the_query->have_posts() ) {
					?>

					<tr>
						<td colspan=5><?php esc_html_e( 'No Map Locations found' ) ?></td>
					</tr>

					<?php
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th><?php _e( 'Title' ) ?></th>
				<th><?php _e( 'Author' ) ?></th>
				<th><?php _e( 'Maps', 'crowd' ) ?></th>
				<th><?php _e( 'Categories' ) ?></th>
				<th><?php _e( 'Date' ) ?></th>				
			</tr>
		</tfoot>
	</table>

	<div class="tablenav bottom">
		<div class="tablenav-pages <?php echo $max_page > 1 ? '' : 'one-page' ?>">
			<span class="displaying-num"><?php echo $total_number ?> <?php _e( 'items' ) ?></span>
			<span class="pagination-links">
				<a class="first-page button" href="<?php echo admin_url( 'admin.php?page=crowd-inbox' ) ?>"><span aria-hidden="true">«</span></a>
				<a class="prev-page button" href="<?php crowd_page_inbox_paginate_url( $paged - 1, $max_page ) ?>"><span aria-hidden="true">‹</span></a>
				<span id="table-paging" class="paging-input"><label for="current-page-selector" class="screen-reader-text"><?php _e( 'Current Page' ) ?></label>
					<span class="tablenav-paging-text"><?php echo $paged ?> of <span class="total-pages"><?php echo $max_page ?></span></span>
				</span>
				<a class="next-page button" href="<?php crowd_page_inbox_paginate_url( $paged + 1, $max_page ) ?>"><span aria-hidden="true">›</span></a>
				<a class="last-page button" href="<?php crowd_page_inbox_paginate_url( $max_page, $max_page ) ?>"><span aria-hidden="true">»</span></a>
			</span>
		</div>
	</div>
</div>