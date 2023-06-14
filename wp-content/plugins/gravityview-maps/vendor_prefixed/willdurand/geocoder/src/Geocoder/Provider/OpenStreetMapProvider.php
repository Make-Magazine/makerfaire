<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Geocoder\Provider;

use GravityKit\GravityMaps\Geocoder\HttpAdapter\HttpAdapterInterface;

/**
 * @author Niklas Närhinen <niklas@narhinen.net>
 */
class OpenStreetMapProvider extends NominatimProvider
{
    /**
     * @var string
     */
    const ROOT_URL = 'http://nominatim.openstreetmap.org';

    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     * @param string               $locale  A locale (optional).
     */
    public function __construct(HttpAdapterInterface $adapter, $locale = null)
    {
        parent::__construct($adapter, static::ROOT_URL, $locale);
    }
}
