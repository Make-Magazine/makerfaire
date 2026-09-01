<?php
/**
 * @package GravityKit\GravityView\Foundation\Notices
 */

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Helpers\Output;

/**
 * Warns an administrator that a file on the site prints before every response.
 *
 * Nothing here runs on its own. A product that cares — one serving downloads,
 * typically — calls register() to opt in, or reads Output::inspect() directly
 * and says so wherever it is relevant to what the user is about to do.
 *
 * When registered, the check runs late on an ordinary admin page load, by which
 * point themes and plugins have loaded and had their chance to print, and
 * nothing legitimate has reached the browser yet. It speaks only when the cause
 * can be proven from the offending file, so it always names a file and a fix
 * rather than describing a symptom.
 *
 * @since 1.30.0
 */
class StrayOutputNotice {
	/**
	 * Notice namespace.
	 *
	 * @since 1.30.0
	 */
	const NAMESPACE_ID = 'gk-foundation';

	/**
	 * Notice slug prefix. The offending source is appended so that dismissing
	 * one incident does not hide a different file breaking later.
	 *
	 * @since 1.30.0
	 */
	const SLUG_PREFIX = 'stray-output';

	/**
	 * Hook the check onto admin page loads.
	 *
	 * Called by a consuming product, never by Foundation itself: whether this
	 * belongs on a dashboard is a decision each product makes for its own users.
	 *
	 * @since 1.30.0
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_init', [ __CLASS__, 'maybe_add' ], PHP_INT_MAX );
	}

	/**
	 * Add the notice when a file is proven to be printing early.
	 *
	 * @since 1.30.0
	 *
	 * @return void
	 */
	public static function maybe_add(): void {
		if ( ! self::is_page_load() ) {
			return;
		}

		// Nobody else can see this notice, and inspecting reads from disk, so
		// there is no reason to spend that on a subscriber loading a profile.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$report = Output::inspect();

		if ( Output::CAUSE_TRAILING_TAG !== $report['cause'] ) {
			return;
		}

		NoticeManager::get_instance()->add_runtime(
			[
				'namespace'    => self::NAMESPACE_ID,
				'slug'         => self::slug( $report['file'], $report['line'] ),
				'message'      => self::message( $report['file'], $report['line'] ),
				'severity'     => 'warning',
				'dismissible'  => true,
				'capabilities' => [ 'manage_options' ],
				// The default omits the main site of a network, where the admin
				// most able to edit a shared theme file actually works.
				'context'      => [ 'site', 'ms_subsite', 'ms_main' ],
			]
		);
	}

	/**
	 * Slug identifying one offending source.
	 *
	 * Dismissal is recorded against the notice id, so a slug that never changes
	 * would silence every future incident once a user dismisses the first. The
	 * source is folded in to keep each distinct defect separately dismissible.
	 *
	 * @since 1.30.0
	 *
	 * @param string $file Absolute path to the offending file.
	 * @param int    $line Line output began on.
	 *
	 * @return string
	 */
	public static function slug( string $file, int $line ): string {
		// The full path, not the display path: two files can share a name, and
		// this value is never shown to anyone.
		$source = $file . ':' . $line;

		return self::SLUG_PREFIX . '-' . substr( md5( $source ), 0, 8 );
	}

	/**
	 * Whether this request is an admin page a notice could be rendered on.
	 *
	 * @since 1.30.0
	 *
	 * @return bool
	 */
	private static function is_page_load(): bool {
		$background = wp_doing_ajax()
			|| wp_doing_cron()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ( defined( 'WP_CLI' ) && WP_CLI )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE );

		return ! $background;
	}

	/**
	 * Build the message for a file that closes its PHP block too early.
	 *
	 * @since 1.30.0
	 *
	 * @param string $file Absolute path to the offending file.
	 * @param int    $line Line output began on.
	 *
	 * @return string
	 */
	public static function message( string $file, int $line ): string {
		$problem = esc_html__( 'A file on this site prints blank characters before every page begins. This is invisible on a normal page, but it corrupts files downloaded from the WordPress admin, such as installers and backups.', 'gk-gravityview' );

		/* translators: placeholders in [brackets] are replaced and must not be translated. */
		$source = strtr(
			esc_html__( 'The output starts at [file], line [line]. That file ends with [tag] followed by blank lines, and PHP prints everything after that tag. Deleting it, and everything after it, stops the output — a file containing only PHP does not need a closing tag.', 'gk-gravityview' ),
			[
				'[file]' => '<code>' . esc_html( Output::relative_path( $file ) ) . '</code>',
				'[line]' => (int) $line,
				'[tag]'  => '<code>?&gt;</code>',
			]
		);

		return $problem . ' ' . $source;
	}
}
