
<form method="post" action="">
	<?php $this->hidden_fields_for_tab( 'customize' ); ?>

	<?php foreach ( $this->get_sections( 'customize' ) as $section ) : ?>
		<?php do_settings_sections( $section['id'] ); // matches the section name ?>
		<?php if ( $section['save_after'] ) : ?>
            <p class="submit"><input class="button-primary" type="submit" name="save" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
		<?php endif; ?>
    <hr>
	<?php endforeach; ?>

</form>
