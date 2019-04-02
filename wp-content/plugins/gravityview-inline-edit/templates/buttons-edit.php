<?php
/**
 * @global $buttons
 */
?>
<button type="submit"
        class="editable-submit <?php echo esc_html( $buttons['ok']['class'] ); ?>"><?php echo esc_attr( $buttons['ok']['text'] ); ?></button>
<button type="button"
        class="editable-cancel <?php echo esc_html( $buttons['cancel']['class'] ); ?>"><?php echo esc_attr( $buttons['cancel']['text'] ); ?></button>