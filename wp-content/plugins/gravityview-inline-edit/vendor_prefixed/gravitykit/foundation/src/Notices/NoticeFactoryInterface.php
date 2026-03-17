<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

/**
 * Interface for creating notice instances from associative array specifications.
 *
 * @since 1.3.0
 */
interface NoticeFactoryInterface {
	/**
	 * Creates a runtime notice instance.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @throws NoticeException When notice creation fails.
	 *
	 * @return RuntimeNoticeInterface Immutable runtime-notice instance.
	 */
	public function make_runtime( array $data ): RuntimeNoticeInterface;

	/**
	 * Creates a stored notice instance.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @throws NoticeException When notice creation fails.
	 *
	 * @return StoredNoticeInterface Immutable stored-notice instance.
	 */
	public function make_stored( array $data ): StoredNoticeInterface;
}
