<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Support\Facades;

use GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Filesystem\Filesystem;

/**
 * @see \GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Filesystem\FilesystemManager
 */
class Storage extends Facade
{
    /**
     * Replace the given disk with a local testing disk.
     *
     * @param  string  $disk
     *
     * @return void
     */
    public static function fake($disk)
    {
        (new Filesystem)->cleanDirectory(
            $root = storage_path('framework/testing/disks/'.$disk)
        );

        static::set($disk, self::createLocalDriver(['root' => $root]));
    }

    /**
     * Replace the given disk with a persistent local testing disk.
     *
     * @param  string  $disk
     * @return void
     */
    public static function persistentFake($disk)
    {
        static::set($disk, self::createLocalDriver([
            'root' => storage_path('framework/testing/disks/'.$disk),
        ]));
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filesystem';
    }
}
