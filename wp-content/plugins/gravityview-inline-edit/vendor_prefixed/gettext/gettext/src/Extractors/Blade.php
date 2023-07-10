<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 11-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\Extractors;

use GravityKit\GravityEdit\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityEdit\Foundation\ThirdParty\Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Class to get gettext strings from blade.php files returning arrays.
 */
class Blade extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        if (empty($options['facade'])) {
            $cachePath = empty($options['cachePath']) ? sys_get_temp_dir() : $options['cachePath'];
            $bladeCompiler = new BladeCompiler(new Filesystem(), $cachePath);

            if (method_exists($bladeCompiler, 'withoutComponentTags')) {
                $bladeCompiler->withoutComponentTags();
            }

            $string = $bladeCompiler->compileString($string);
        } else {
            $string = $options['facade']::compileString($string);
        }

        PhpCode::fromString($string, $translations, $options);
    }
}
