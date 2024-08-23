<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use GravityKit\GravityRevisions\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$r->headers->setCookie(new Cookie('CookieSamesiteLaxTest', 'LaxValue', 0, '/', null, false, true, false, Cookie::SAMESITE_LAX));
$r->sendHeaders();
