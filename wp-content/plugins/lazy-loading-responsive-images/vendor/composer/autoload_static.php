<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb6f2a05dfecb650db91c2dd494498704
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FlorianBrinkmann\\LazyLoadResponsiveImages\\' => 42,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FlorianBrinkmann\\LazyLoadResponsiveImages\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'FlorianBrinkmann\\LazyLoadResponsiveImages\\Helpers' => __DIR__ . '/../..' . '/src/Helpers.php',
        'FlorianBrinkmann\\LazyLoadResponsiveImages\\Plugin' => __DIR__ . '/../..' . '/src/Plugin.php',
        'FlorianBrinkmann\\LazyLoadResponsiveImages\\Settings' => __DIR__ . '/../..' . '/src/Settings.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb6f2a05dfecb650db91c2dd494498704::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb6f2a05dfecb650db91c2dd494498704::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb6f2a05dfecb650db91c2dd494498704::$classMap;

        }, null, ClassLoader::class);
    }
}