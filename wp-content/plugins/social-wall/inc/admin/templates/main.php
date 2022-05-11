<?php
$vars = $this->get_vars();
$text_domain = $vars->text_domain();
$setup_url = $vars->setup_url();
$social_network = 'Social Wall';
$sn_with_a_an = ' a social wall feed';
$plugin_version = $vars->version();

if ( isset( $_POST[ $this->get_option_name() . '_validate' ] ) && $this->verify_post( $_POST ) ) {
    $tab = isset( $_POST[ $this->get_option_name() . '_tab_marker' ] ) ? sanitize_text_field( $_POST[ $this->get_option_name() . '_tab_marker' ] ) : 'main';
    $new_settings = $this->validate_options( $_POST[ $this->get_option_name() ], $tab );
    $this->update_options( $new_settings );
    ?>
    <div class="updated"><p><strong><?php _e('Settings saved.', $text_domain ); ?></strong></p></div>

	<?php
}
$plugin_name = $this->get_plugin_name();
$active_tab = $this->get_active_tab();
$slug = $this->get_slug();
$tabs = $this->get_tabs();
?>

<div id="sbspf_admin" class="wrap sbspf-admin sbsw_admin" data-sb-plugin="sbspf">
	<h1><?php echo esc_html( $plugin_name ); ?></h1>

	<!-- Display the tabs along with styling for the 'active' tab -->
	<h2 class="nav-tab-wrapper">
		<?php
		$i = 1;
		foreach ( $tabs as $tab ) :
			$title = isset( $tab['numbered_tab'] ) && ! $tab['numbered_tab'] ? __( $tab['title'], $text_domain ) : $i . '. ' . __( $tab['title'], $text_domain );
			?>
			<a href="admin.php?page=<?php echo esc_attr( $slug ); ?>&tab=<?php echo esc_attr( $tab['slug'] ); ?>" class="nav-tab <?php if ( $active_tab === $tab['slug'] ){ echo 'nav-tab-active'; } ?>"><?php echo $title; ?></a>
		<?php
		$i ++;
		endforeach; ?>
	</h2>
	<?php
	settings_errors();

	include $this->get_path( $active_tab );

	$next_step = $this->next_step();
	if ( ! empty( $next_step ) ) : ?>
    <p class="sbspf_footer_help">
        <?php echo sbsw_admin_icon( 'chevron-right', 'sbspf_small_svg' ) ; ?>&nbsp; <?php _e('Next Step', $text_domain ); ?>: <a href="?page=<?php echo esc_attr( $slug ); ?>&tab=<?php echo esc_attr( $next_step['next_tab'] ); ?>"><?php echo esc_html( __( $next_step['instructions'], '$text_domain' ) ); ?></a>
    </p>
	<?php endif; ?>

	<p class="sbspf_footer_help"><?php echo sbsw_admin_icon( 'life-ring', 'sbspf_small_svg' ); ?>&nbsp; <?php _e('Need help setting up the plugin? Check out our <a href="' . esc_url( $setup_url ) . '" target="_blank">setup directions</a>', $text_domain); ?></p>

	<div class="sbspf-quick-start">
		<h3><?php echo sbsw_admin_icon( 'rocket', 'sbspf_small_svg' ); ?>&nbsp; <?php _e( 'Display your Social Wall', $text_domain); ?></h3>
		<p><?php echo sprintf( __( 'Use the %s to create a shortcode. Then, paste it into any page, post, or widget to display your wall.', $text_domain ), '<a href="admin.php?page=sbsw&tab=configure">Configure page</a>' ); ?></p>
	</div>

</div>
<div class="wp-clearfix"></div>