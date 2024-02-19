<?php

/**
 * Put a function name that should be run (for example a database migration) on your version update.
 * If nothing to run, then it's safe to leave this file unchanged.
 */
$updater_functions = array(
	'2.3.2' => 'mpfy_mlqm_migrate',
);