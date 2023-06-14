<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Generators;

use GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Utils\MultidimensionalArrayTrait;

class Json extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'json' => 0,
        'includeHeaders' => false,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return json_encode(static::toArray($translations, $options['includeHeaders'], true), $options['json']);
    }
}
