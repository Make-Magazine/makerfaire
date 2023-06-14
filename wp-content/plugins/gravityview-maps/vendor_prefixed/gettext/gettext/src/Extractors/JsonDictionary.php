<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Extractors;

use GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityMaps\Foundation\ThirdParty\Gettext\Utils\DictionaryTrait;

/**
 * Class to get gettext strings from plain json.
 */
class JsonDictionary extends Extractor implements ExtractorInterface
{
    use DictionaryTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $messages = json_decode($string, true);

        if (is_array($messages)) {
            static::fromArray($messages, $translations);
        }
    }
}
