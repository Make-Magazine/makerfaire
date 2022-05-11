<h3><?php _e( 'Display your Feed', $text_domain ); ?></h3>
<p><?php _e( "Copy and paste the following shortcode directly into the page, post or widget where you'd like the feed to show up:", $text_domain ); ?></p>
<input type="text" value="[<?php echo $slug; ?>]" size="20" readonly="readonly" style="text-align: center;" onclick="this.focus();this.select()" title="<?php _e( 'To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).', $text_domain ); ?>" />

<h3 style="padding-top: 10px;"><?php _e( 'Multiple Feeds', $text_domain ); ?></h3>
<p><?php _e( "If you'd like to display multiple feeds then you can set different settings directly in the shortcode like so:", $text_domain ); ?>
	</br><code>[<?php echo $slug; ?> user=gopro num=9]</code></p>
<p><?php _e( "You can display as many different feeds as you like, on either the same page or on different pages, by just using the shortcode options below. For example:", $text_domain ); ?><br />
	<code>[<?php echo $slug; ?>]</code><br />
	<code>[<?php echo $slug; ?> type=playlist playlist="PLLLm1a2b3c4D6g7i8j9k_1a2b3c4D57i8j9k"]</code><br />
	<code>[<?php echo $slug; ?> num=4 showheader=false]</code>
</p>
<p><?php _e( "See the table below for a full list of available shortcode options:", $text_domain ); ?></p>

<table class="sbspf_shortcode_table">
	<tbody>
	<tr valign="top">
		<th scope="row"><?php _e( 'Shortcode option', $text_domain ); ?></th>
		<th scope="row"><?php _e( 'Description', $text_domain ); ?></th>
		<th scope="row"><?php _e( 'Example', $text_domain ); ?></th>
	</tr>

	<?php foreach ( $this->display_your_feed_sections as $display_your_feed_section ) : ?>
		<tr class="sbspf_table_header"><td colspan=3><?php echo $display_your_feed_section['label'] ?></td></tr>
		<?php foreach ( $display_your_feed_section['settings'] as $setting ) : ?>
			<tr>
				<td><?php echo $setting['key']; ?></td>
				<td><?php echo $setting['description']; ?></td>
				<td><code>[<?php echo $slug; ?> <?php echo $setting['key']; ?>="<?php echo str_replace('"', '', $setting['example'] ); ?>"]</code></td>
			</tr>
		<?php endforeach; ?>

	<?php endforeach; ?>

	</tbody>
</table>
<p><?php echo sby_admin_icon( 'question-circle', 'sbspf_small_svg' ); ?>&nbsp; <?php _e('Need help?', $text_domain ); ?> <a href="?page=<?php echo esc_attr( $slug ); ?>&tab=support"><?php _e('Get Support', $text_domain ); ?></a></p>
