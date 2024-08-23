<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext;

abstract class BaseTranslator implements TranslatorInterface
{
    /** @var TranslatorInterface */
    public static $current;

    /**
     * @see TranslatorInterface
     */
    public function noop($original)
    {
        return $original;
    }

    /**
     * @see TranslatorInterface
     */
    public function register()
    {
        $previous = static::$current;

        static::$current = $this;

        static::includeFunctions();

        return $previous;
    }

    /**
     * Include the gettext functions
     */
    public static function includeFunctions()
    {
        include_once __DIR__.'/translator_functions.php';
    }
}
