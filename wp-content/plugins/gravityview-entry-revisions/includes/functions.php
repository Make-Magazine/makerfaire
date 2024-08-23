<?php

/**
 * Alias for ob_start(), since output buffering and actions don't get along
 * @since 1.0
 * @return bool true on success or false on failure.
 */
function _gk_gravityrevisions_ob_start() {
	return ob_start();
}

/**
 * Alias for ob_clean(), since output buffering and actions don't get along
 * @since 1.0
 * @return @return string the contents of the output buffer and end output buffering. If output buffering isn't active then false is returned.
 */
function _gk_gravityrevisions_ob_get_clean() {
	return ob_get_clean();
}
