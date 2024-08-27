<?php
/**
 * @global string $revision_title      Title of the table ("Entry modified by {user} {time ago}.")
 * @global string $nonce_field
 * @global bool   $user_can_edit_entry Can the user edit the current entry
 * @global string $url_cancel          URL to return to Entry Detail
 */

$is_wp_57_plus = version_compare( $GLOBALS['wp_version'], '5.7-beta1', '>=' );
?>
<table class="diff gv-entry-diff">
    <caption>
		<?php echo esc_html( $revision_title ); ?>
    </caption>

    <colgroup>
        <col class="content diffsplit" style="width: 20%;"/>
        <col class="content diffsplit left"/>
		<?php if ( ! $is_wp_57_plus ) { echo '<col class="content diffsplit middle"/>'; } ?>
        <col class="content diffsplit right"/>
    </colgroup>

    <thead>
        <tr class="diff-sub-title">
            <th scope="col" id="diff-col-field" class="screen-reader-text">
                <?php esc_html_e( 'Field', 'gk-gravityrevisions' ); ?>
            </th>
            <th scope="col" id="diff-col-revision">
                <?php esc_html_e( 'Revision Value', 'gk-gravityrevisions' ); ?>
                <div class="diff-date" title="<?php echo esc_attr( $revision_title ); ?>">
                    <?php echo esc_html( $date ); ?>
                </div>
                <?php
                if ( $restore_links && 1 < sizeof( $diffs ) ) {
                    ?>
                    <label class="screen-reader-text" for="toggle_revisions_tfoot">
                        <?php esc_html_e( 'Toggle all changes', 'gk-gravityrevisions' ); ?>
                    </label>
                    <input type="checkbox" id="toggle_revisions_tfoot" class="toggle-all-revisions" title="<?php esc_html_e( 'Toggle all changes', 'gk-gravityrevisions' ); ?>"/>
                <?php } ?>
            </th>
            <?php if ( ! $is_wp_57_plus ) { echo '<th scope="col" id="diff-col-gap"></th>'; } ?>
            <th scope="col" id="diff-col-current">
                <?php esc_html_e( 'Current Value', 'gk-gravityrevisions' ); ?>
            </th>
        </tr>
    </thead>

	<?php
        if ( $restore_links ) {?>
            <tfoot>
                <tr>
                    <?php
                        if ( !$user_can_edit_entry ) {
                            ?>
                            <td colspan="<?php echo ! $is_wp_57_plus ? '4' : '3'; ?>">
                                <a href="<?php echo esc_url( $url_cancel ); ?>" class="button button-secondary button-large alignleft" title="<?php esc_attr_e( 'Cancel and do not modify the current entry', 'gk-gravityrevisions' ); ?>">
                                    <?php esc_html_e( 'Return to Entry', 'gk-gravityrevisions' ); ?>
                                </a>
                            </td>
                            <?php
                        } else {
                            ?>
                            <td></td>
                            <td>
                                <input type="submit" class="button button-primary button-large alignleft hide-if-js" value="<?php
                                echo esc_attr( _n( 'Restore This Value', 'Restore These Values', count( $diffs ), 'gk-gravityrevisions' ) ) ?>"/>
                                <?php
                                if ( 1 < sizeof( $diffs ) ) { ?>
                                    <label class="screen-reader-text" for="toggle_revisions_tfoot">
                                        <?php esc_html_e( 'Toggle all changes', 'gk-gravityrevisions' ); ?>
                                    </label>
                                    <input type="checkbox" id="toggle_revisions_tfoot" class="toggle-all-revisions alignright" title="<?php esc_html_e( 'Toggle all changes', 'gk-gravityrevisions' ); ?>"/>
                                    <?php
                                } ?>
                            </td>
                            <?php if ( ! $is_wp_57_plus ) { echo "<td></td>"; } ?>
                            <td>
                                <a href="<?php echo esc_url( $url_cancel ); ?>" class="button alignright" title="<?php esc_attr_e( 'Cancel and do not modify the current entry', 'gk-gravityrevisions' ); ?>">
                                    <?php esc_html_e( 'Cancel', 'gk-gravityrevisions' ); ?>
                                </a>
                            </td>
                            <?php
                        }

                        printf( '<input type="hidden" name="revision" value="%d" />', (int) $revision['id'] );

                        echo $nonce_field;
                    ?>
                </tr>
            </tfoot>
            <?php
        }
	?>
    <tbody>
	    <?php
    	    echo implode( "\n", $diffs );
	    ?>
    </tbody>
</table>
