<?php
/**
 * @license MIT
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use GravityKit\GravityImport\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$str = '?*():@&+$/%#[]';

$r->headers->setCookie(new Cookie($str, $str, 0, '/', null, false, false, true));
$r->sendHeaders();

setrawcookie($str, $str, 0, '/', null, false, false);
