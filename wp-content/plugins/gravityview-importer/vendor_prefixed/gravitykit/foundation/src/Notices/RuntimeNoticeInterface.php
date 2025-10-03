<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Notices;

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
