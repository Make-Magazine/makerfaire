<?php

namespace GravityKit\GravityMaps;

/**
 * Class Icon
 *
 * Marker sizes are expressed as a Size of X,Y where the origin of the image (0,0) is located in the top left of the image.
 * Origins, anchor positions and coordinates of the marker increase in the X direction to the right and in the Y direction down.
 *
 * <code>
 * // This marker is 20 pixels wide by 32 pixels tall.
 * // The origin for this image is 0,0.
 * // The anchor for this image is the base of the flagpole at 0,32.
 * $icon = new Icon('images/beachflag.png', array(20, 32), array(0, 0), array(0, 32) );
 * </code>
 *
 * @link https://developers.google.com/maps/documentation/javascript/markers#complex_icons
 */
class Icon {
	/**
	 * URL of the icon
	 * @var string
	 */
	var $url = '';

	/**
	 * Array of the size of the icon in pixels. Example: [20,30]
	 * @var array
	 */
	var $size;

	/**
	 * If using an image sprite, the start of the icon from top-left.
	 * @var array
	 */
	var $origin;

	/**
	 * Where the "pin" of the icon should be, example [0,32] for the bottom of a 32px icon
	 * @var array
	 */
	var $anchor;

	/**
	 * How large should the icon appear in px (scaling down image for Retina)
	 * @var array
	 */
	var $scaledSize;

	public function __construct( $url, array $size = null, array $origin = null, array $anchor = null, array $scaledSize = null ) {
		$this->url        = $url;
		$this->size       = $size;
		$this->origin     = $origin;
		$this->anchor     = $anchor;
		$this->scaledSize = $scaledSize;
	}

	/**
	 * Convert icon to array, reduced to only return defined properties.
	 *
	 * @since 1.9
	 *
	 * @param bool $remove_empty Remove empty properties from the array?
	 *
	 * @return array Array of icon properties. If $remove_empty, only defined properties will be returned.
	 */
	function to_array( $remove_empty = true ) {
		$icon_array = array(
			'url'        => $this->url,
			'size'       => $this->size,
			'origin'     => $this->origin,
			'anchor'     => $this->anchor,
			'scaledSize' => $this->scaledSize,
		);

		if ( ! $remove_empty ) {
			return $icon_array;
		}

		return array_filter( $icon_array, function( $item ) {
			return ! is_null( $item );
		} );
	}
}
