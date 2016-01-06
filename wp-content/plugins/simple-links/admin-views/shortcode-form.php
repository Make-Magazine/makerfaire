<?php
/**
 * The Form for the MCE Shortcode Generator
 *
 * @uses   called with a template redirect using a query var send from the mce plugin
 * @see    simple_links->loadShortcodeForm();
 * @see    js/editor_plugin.js
 *
 * @author Mat Lipe <mat@matlipe.com>
 *
 */

?>
<title>Add Simple Links</title>
<?php
wp_head();
?>

<style type="text/css">

	<?php
	if( get_bloginfo('version') >= 3.8 ){
		?>
	html {
		margin-top : 46px important !;
	}

	body {
		margin-top : -66px;
		important;
	}

	<?php
} else {
	?>
	html {
		margin-top : 28px important !;
	}

	body {
		margin-top : -23px;
		important;
	}

	<?php
}
?>
</style>
</head>

<body>
<div class="wrap">

	<h4><?php _e( 'This Will Generate the Shortcode to Display Simple Links', 'simple-links' ); ?></h4>

	<p><em><?php _e( 'If no links match the options chosen, this will not display anything', 'simple-links' ); ?>.</em>
	</p>

	<label><?php _e( 'Title <small>(optional)</small>', 'simple-links' ); ?>:
		<br/>
		<input type="text" id="title"
			size="50"/>
	</label>

	<fieldset>
		<legend><?php _e( 'Categories <small>(optional)</small>', 'simple-links' ); ?></legend>
		<ul class="sl-categories">
			<?php
			$cats = Simple_Links_Categories::get_category_names();
			if( ! empty( $cats ) ){
				$term_args = array(
					'walker'        => new Simple_Links_Category_Checklist(),
					'taxonomy'      => Simple_Links_Categories::TAXONOMY,
					'checked_ontop' => false
				);

				wp_terms_checklist( 0, $term_args );

			} else {
				_e( 'No link categories have been created yet.', 'simple-links' );
			}
			?>
		</ul>

	</fieldset>

	<p>
		<label><?php _e( 'Include Child Categories Of Selected Categories', 'simple-links' ); ?>
			<input type="checkbox"
				id="child-categories"
				value="true"/>
		</label>
	</p>

    <hr>

	<p>
		<label><?php _e( 'Number Of Links', 'simple-links' ); ?>:
			<select id="count">
				<option value=""><?php _e( 'All', 'simple-links' ); ?></option>
				<?php
				for( $i = 1; $i < 30; $i ++ ){
					printf( '<option value="%s">%s</option>', $i, $i );
				}
				?>
			</select>
		</label>
	</p>


	<p>
		<label><?php _e( 'Order By', 'simple-links' ); ?>:
			<select id="orderby">
				<option value=""><?php _e( '- select an order by - ', 'simple-links' ); ?></option>
				<?php
				simple_links::orderby_options();
				?>
			</select>
		</label>
	</p>

	<p>
		<label><?php _e( 'Order', 'simple-links' ); ?>:
			<select id="order">
				<option value=""><?php _e( '- select an order -', 'simple-links' ); ?></option>
				<option value="ASC"><?php _e( 'Ascending', 'simple-links' ); ?></option>
				<option value="DESC"><?php _e( 'Descending', 'simple-links' ); ?></option>
			</select>
		</label>
	</p>

	<hr>

	<p>
		<label><?php _e( 'Show Description', 'simple-links' ); ?>
			<input type="checkbox" id="description" value="true"/>
		</label>
	</p>

	<p>
		<label><?php _e( 'Include Description Paragraph Format', 'simple-links' ); ?>
			<input type="checkbox"
				id="description-formatting"
				value="true"/>
		</label>
	</p>

	<hr>

	<p>
		<label><?php _e( 'Show Image', 'simple-links' ); ?>
			<input type="checkbox" id="show_image"
				value="true"/>
		</label>
	</p>

	<p>
		<label><?php _e( 'Display Image Without Title', 'simple-links' ); ?>
			<input type="checkbox" id="show_image_only"
				value="true"/>
		</label>

	</p>

	<p>
		<label>
			<?php _e( 'Image Size', 'simple-links' ); ?>
			<select id="image-size">
				<?php
				foreach( simple_links()->image_sizes() as $size ){
					printf( '<option value="%s">%s</a>', $size, $size );
				}
				?>
			</select>
		</label>
	</p>

	<p>
		<label><?php _e( 'Remove Line Break Between Image And Link', 'simple-links' ); ?>
			<input type="checkbox"
				id="line_break"
				value="1"/>
		</label>
	</p>

	<fieldset>
		<legend><?php _e( 'Include Additional Fields', 'simple-links' ); ?></legend>
		<?php
		$fields = simple_links()->getAdditionalFields();
		if( empty( $fields ) ){
			echo '<em>' . __( 'There have been no additional fields added', 'simple-links' ) . '</em>';
		} else {
			?>
			<ul>
				<?php
				foreach( $fields as $field ){
					printf( '<li><label>%1$s<input class="additional" type="checkbox" value="%1$s"></label></li>', $field );
				}
				?>
			</ul>
		<?php
		}
		?>
	</fieldset>

	<label><?php _e( 'Field Separator', 'simple-links' ); ?>:
		<br/>
		<em>
			<small><?php _e( 'HTML is allowed and will show up formatted in the editor', 'simple-links' ); ?>:</small>
		</em>
		<br/>
		<input type="text" value="-" id="separator" size="50"/>
	</label>

	<?php do_action( 'simple_links_shortcode_form' ); ?>

	<?php if( get_bloginfo( 'version' ) < 3.8 ){
		?>
		<p>$nbsp;</p><?php
	}
	?>
	<p>
		<input type="button" id="generate" class="button-primary" value="Generate">
	</p>

	<p>
		&nbsp;
	</p>


</div>
</body>



