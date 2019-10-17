<?php

/**
 * Class GravityView_Maps_Icon
 *
 * Marker sizes are expressed as a Size of X,Y where the origin of the image (0,0) is located in the top left of the image.
 * Origins, anchor positions and coordinates of the marker increase in the X direction to the right and in the Y direction down.
 *
 * <code>
 * // This marker is 20 pixels wide by 32 pixels tall.
 * // The origin for this image is 0,0.
 * // The anchor for this image is the base of the flagpole at 0,32.
 * $icon = new GravityView_Maps_Icon('images/beachflag.png', array(20, 32), array(0, 0), array(0, 32) );
 * </code>
 *
 * @link https://developers.google.com/maps/documentation/javascript/markers#complex_icons
 */
class GravityView_Maps_Icon {

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

	function __construct( $url, $size = array(), $origin = array(), $anchor = array(), $scaledSize = array() ) {
		$this->url        = $url;
		$this->size       = $size;
		$this->origin     = $origin;
		$this->anchor     = $anchor;
		$this->scaledSize = $scaledSize;
	}


}