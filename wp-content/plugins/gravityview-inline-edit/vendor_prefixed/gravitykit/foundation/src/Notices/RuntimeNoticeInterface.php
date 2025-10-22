<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 16-October-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

/**
 * Marker interface for notices evaluated at runtime via a callable condition.
 *
 * @since 1.3.0
 */
interface RuntimeNoticeInterface extends NoticeInterface {
	/**
	 * Evaluates whether the notice should currently be shown.
	 * Consumers should keep the callback side-effect free and fast.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function show_if();
}
