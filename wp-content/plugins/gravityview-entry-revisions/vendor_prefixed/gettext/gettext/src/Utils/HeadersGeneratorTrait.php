<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext\Utils;

use GravityKit\GravityRevisions\Foundation\ThirdParty\Gettext\Translations;

/**
 * Trait to provide the functionality of extracting headers.
 */
trait HeadersGeneratorTrait
{
    /**
     * Returns the headers as a string.
     *
     * @param Translations $translations
     *
     * @return string
     */
    protected static function generateHeaders(Translations $translations)
    {
        $headers = '';

        foreach ($translations->getHeaders() as $name => $value) {
            $headers .= sprintf("%s: %s\n", $name, $value);
        }

        return $headers;
    }
}
