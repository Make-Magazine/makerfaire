<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Symfony\Component\HttpFoundation\Tests\File;

use GravityKit\GravityImport\Symfony\Component\HttpFoundation\File\File as OrigFile;

class FakeFile extends OrigFile
{
    private $realpath;

    public function __construct($realpath, $path)
    {
        $this->realpath = $realpath;
        parent::__construct($path, false);
    }

    public function isReadable()
    {
        return true;
    }

    public function getRealpath()
    {
        return $this->realpath;
    }

    public function getSize()
    {
        return 42;
    }

    public function getMTime()
    {
        return time();
    }
}
