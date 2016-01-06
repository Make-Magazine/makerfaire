<div class="wrap">
	<h2>
		<?php _e( 'Keeping Your Links In Order', 'simple-links' ); ?>!
	</h2>

	<?php
	if( is_array( $categories ) ){
		?>
		<h3>
			<?php _e( 'Select a link category to sort links in that category only ( optional )', 'simple-links' ); ?>
		</h3>
		<p class="description">
			<?php _e( 'When setting up your short-codes and/or widgets, selecting a single category and Order By: "Link Order" will allow the links to display in the order they were sorted in that category.', 'simple-links' ); ?>
		</p>

		<?php do_action( 'simple-links-ordering-description' ); ?>
		
		<select id="simple-links-sort-cat">
			<option value="0">
				<?php _e( 'All Categories', 'simple-links' ); ?>
			</option>

			<?php
			foreach( $categories as $_cat ){
				printf( '<option value="%s">%s</option>', $_cat->term_id, $_cat->name );
			}
			?>
		</select>
	<?php

	} else {
		?>
		<h3>
			<?php _e( 'To sort by link categories, you must add some links to them', 'simple-links' ); ?>.
			<a href="/wp-admin/edit-tags.php?taxonomy=<?php echo Simple_Links_Categories::TAXONOMY; ?>&post_type=<?php echo SIMPLE_LINK::POST_TYPE; ?>">
				<?php _e( 'Follow Me', 'simple-links' ); ?>
			</a>
		</h3>
	<?php
	}
	?>
	<div id="simple-links-ordering-wrap">
		<?php
		require( SIMPLE_LINKS_DIR . 'admin-views/draggable-links.php' );
		?>
	</div>
</div>