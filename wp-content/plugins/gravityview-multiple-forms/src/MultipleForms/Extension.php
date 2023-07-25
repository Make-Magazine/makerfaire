<?php

namespace GravityKit\MultipleForms;

use GFCommon;
use GFFormsModel;

/**
 * Class Extension
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class Extension extends \GravityView_Extension {

	protected $_title = 'Multiple Forms';

	protected $_version = GV_MF_VERSION;

	protected $_item_id = 575477;

	protected $_text_domain = 'gravityview-multiple-forms';

	protected $_min_gravityview_version = '2.6';

	protected $_min_php_version = '7.2';

	protected $_author = 'GravityView';

	protected $_path = GV_MF_PATH;

	/**
	 * @since 0.1-beta
	 */
	public function add_hooks() {
	}
}
