<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 11-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

spl_autoload_register(
    function ($class) {
        if (strpos($class, 'GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\\Languages\\') !== 0) {
            return;
        }
        $file = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen('GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\\Languages'))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
);
