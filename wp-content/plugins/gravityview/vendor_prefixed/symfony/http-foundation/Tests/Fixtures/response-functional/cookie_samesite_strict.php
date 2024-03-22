<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 19-March-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

use GravityKit\GravityView\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$r->headers->setCookie(new Cookie('CookieSamesiteStrictTest', 'StrictValue', 0, '/', null, false, true, false, Cookie::SAMESITE_STRICT));
$r->sendHeaders();
