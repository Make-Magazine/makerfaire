<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use GravityKit\GravityMaps\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$r->headers->setCookie(new Cookie('CookieSamesiteLaxTest', 'LaxValue', 0, '/', null, false, true, false, Cookie::SAMESITE_LAX));
$r->sendHeaders();
