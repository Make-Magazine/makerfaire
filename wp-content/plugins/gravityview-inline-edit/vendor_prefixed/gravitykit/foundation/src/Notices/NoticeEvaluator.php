<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

use GravityKit\GravityEdit\Foundation\Helpers\Users;
use GravityKit\GravityEdit\Foundation\Notices\NoticeException;
use GravityKit\GravityEdit\Foundation\Exceptions\UserException;
use Throwable;

/**
 * Evaluates which notices should be visible for the current request/user.
 *
 * @since 1.3.0
 */
final class NoticeEvaluator {
	/**
	 * Collection of notices to evaluate.
	 *
	 * @since 1.3.0
	 *
	 * @var NoticeInterface[]
	 */
	private $notices;

	/**
	 * Cached evaluation result keyed by notice ID.
	 *
	 * @since 1.3.0
	 *
	 * @var array<string,NoticeInterface>|null
	 */
	private $cached_results;

	/**
	 * NoticeEvaluator constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $notices Notices to evaluate.
	 */
	public function __construct( array $notices ) {
		$this->notices = $notices;
	}

	/**
	 * Returns the list of notices that pass all filters for the current request.
	 *
	 * @since 1.3.0
	 *
	 * @param string|null $context    Optional admin context to filter by: 'network', 'user', or 'site'.
	 * @param array       $user_state User notice state data (dismissed/snoozed status).
	 *
	 * @throws NoticeException When evaluation fails.
	 *
	 * @return NoticeInterface[] Notices that passed all checks.
	 */
	public function evaluate( ?string $context = null, array $user_state = [] ): array {
		try {
			if ( null !== $this->cached_results ) {
				return array_values( $this->cached_results );
			}

			$current_user_obj = Users::get();
			$user_id          = $current_user_obj instanceof UserException ? 0 : $current_user_obj->ID;

			/**
			 * Filters the list of notices before evaluation.
			 *
			 * @filter `gk/foundation/notices/evaluation/before`
			 *
			 * @since 1.3.0
			 *
			 * @param NoticeInterface[] $original_notices Notices to evaluate.
			 * @param string|null       $context          Current admin context.
			 * @param array             $user_state       User state data.
			 */
			$original_notices = apply_filters( 'gk/foundation/notices/evaluation/before', $this->notices, $context, $user_state );

			$evaluated_notices = [];

			foreach ( $original_notices as $notice ) {
				/**
				 * Filters whether a notice should be displayed.
				 * Return true to include, false to exclude, or null to continue normal evaluation.
				 *
				 * @filter `gk/foundation/notices/evaluation/notice`
				 *
				 * @since 1.3.0
				 *
				 * @param bool|null       $should_display Whether to display the notice. Default null.
				 * @param NoticeInterface $notice         The notice being evaluated_notices.
				 * @param string|null     $context        Current admin context.
				 * @param array           $user_state     User state data.
				 */
				$should_display = apply_filters( 'gk/foundation/notices/evaluation/notice', null, $notice, $context, $user_state );

				if ( true === $should_display ) {
					$evaluated_notices[ $notice->get_id() ] = $notice;
					continue;
				}

				if ( false === $should_display ) {
					continue;
				}

				if ( $notice->is_scheduled() ) {
					continue;
				}

				if ( $notice instanceof StoredNotice && $notice->is_expired() ) {
					continue;
				}

				if ( ! $this->check_runtime( $notice ) ) {
					continue;
				}

				if ( ! $this->check_capabilities( $notice ) ) {
					continue;
				}

				if ( ! $this->check_screen( $notice ) ) {
					continue;
				}

				if ( ! $this->check_user_state( $notice, $user_state ) ) {
					continue;
				}

				if ( ! $this->check_context( $notice, $context ) ) {
					continue;
				}

				// Check if user is excluded (for notices with excluded_users).
				if ( $user_id && $notice instanceof StoredNotice ) {
					$definition = $notice->as_definition();

					if ( ! empty( $definition['excluded_users'] ) && in_array( $user_id, $definition['excluded_users'], true ) ) {
						continue;
					}
				}

				$evaluated_notices[ $notice->get_id() ] = $notice;
			}

			/**
			 * Filters the evaluated notices.
			 *
			 * @filter `gk/foundation/notices/evaluation/after`
			 *
			 * @since 1.3.0
			 *
			 * @param NoticeInterface[] $evaluated_notices Evaluated notices.
			 * @param NoticeInterface[] $original_notices  Original notices.
			 * @param string|null       $context           Current admin context.
			 * @param array             $user_state        User state data.
			 */
			$evaluated_notices = apply_filters( 'gk/foundation/notices/evaluation/after', $evaluated_notices, $original_notices, $context, $user_state );

			$evaluated_notices = $this->sort_notices( array_values( $evaluated_notices ) );

			$this->cached_results = [];

			foreach ( $evaluated_notices as $notice ) {
				$this->cached_results[ $notice->get_id() ] = $notice;
			}

			return $evaluated_notices;
		} catch ( Throwable $e ) {
			throw NoticeException::evaluation( $e->getMessage(), [ 'trace' => $e->getTraceAsString() ] );
		}
	}

	/**
	 * Checks whether the current user has any of the required capabilities.
	 * Supports 'not:' prefix for exclusion rules.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface $notice Notice instance.
	 *
	 * @return bool True when the capability guard passes.
	 */
	public function check_capabilities( NoticeInterface $notice ): bool {
		$capabilities = $notice->get_capabilities();

		if ( empty( $capabilities ) ) {
			return true;
		}

		// Separate includes and excludes.
		$includes = [];
		$excludes = [];

		foreach ( $capabilities as $capability ) {
			if ( strpos( $capability, 'not:' ) === 0 ) {
				$excludes[] = substr( $capability, 4 ); // Remove 'not:' from capability.
			} else {
				$includes[] = $capability;
			}
		}

		// Check excludes first - if user has any excluded capability, fail.
		foreach ( $excludes as $capability ) {
			if ( current_user_can( $capability ) ) {
				return false;
			}
		}

		// If only excludes were specified, user passes (wasn't excluded).
		if ( empty( $includes ) ) {
			return true;
		}

		// Check includes - user must have at least one.
		foreach ( $includes as $capability ) {
			if ( current_user_can( $capability ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if a runtime notice is currently active.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface $notice Notice instance.
	 *
	 * @return bool True when the notice should be considered, false to skip.
	 */
	private function check_runtime( NoticeInterface $notice ): bool {
		if ( $notice instanceof RuntimeNoticeInterface ) {
			return $notice->show_if();
		}

		$condition = $notice->get_condition();

		if ( ! $condition || ! is_callable( $condition ) ) {
			return true;
		}

		return (bool) $condition( $notice );
	}

	/**
	 * Evaluates the screen rules for a notice.
	 * Accepts string IDs and/or callable guards provided via {@see Notice::get_screens()}.
	 * Supports 'not:' prefix for exclusion rules.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface $notice Notice instance.
	 *
	 * @return bool True when the current admin screen matches the rules.
	 */
	private function check_screen( NoticeInterface $notice ): bool {

		$rules = $notice->get_screens();

		if ( empty( $rules ) ) {
			return true; // Global notice.
		}

		$screen_obj = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		$current_id = $screen_obj ? $screen_obj->id : null;

		// Separate includes, excludes, and callables.
		$includes  = [];
		$excludes  = [];
		$callables = [];

		foreach ( $rules as $rule ) {
			if ( is_callable( $rule ) ) {
				$callables[] = $rule;
			} elseif ( is_string( $rule ) ) {
				if ( 0 === strpos( $rule, 'not:' ) ) {
					$excludes[] = substr( $rule, 4 ); // Remove 'not:' from screen ID.
				} else {
					$includes[] = $rule;
				}
			}
		}

		// Check excludes first - if current screen is excluded, fail.
		if ( $current_id && in_array( $current_id, $excludes, true ) ) {
			return false;
		}

		// Check callables - if any return false for exclusion.
		foreach ( $callables as $callable ) {
			$result = $callable( $notice, $screen_obj );

			if ( false === $result ) {
				return false; // Explicit exclusion.
			}
		}

		// If only excludes/callables were specified, we're on an allowed screen.
		if ( empty( $includes ) ) {
			// But we need at least one callable to return true if there are any.
			if ( ! empty( $callables ) ) {
				foreach ( $callables as $callable ) {
					if ( true === $callable( $notice, $screen_obj ) ) {
						return true;
					}
				}

				return false;
			}

			return true;
		}

		// Check includes - must match at least one.
		if ( $current_id ) {
			foreach ( $includes as $screen_id ) {
				if ( $screen_id === $current_id ) {
					return true;
				}
			}
		}

		// Check if any callable returns true for inclusion.
		foreach ( $callables as $callable ) {
			if ( true === $callable( $notice, $screen_obj ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Verifies a notice has not been dismissed or snoozed for the user.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface     $notice     Notice instance.
	 * @param array<string,array> $user_state Cached user-state array.
	 *
	 * @return bool True when the notice is not dismissed or snoozed.
	 */
	private function check_user_state( NoticeInterface $notice, array $user_state ): bool {
		$notice_id = $notice->get_id();

		if ( isset( $user_state[ $notice_id ]['dismissed'] ) && $user_state[ $notice_id ]['dismissed'] ) {
			return false;
		}

		if ( isset( $user_state[ $notice_id ]['snoozed_until'] ) && $user_state[ $notice_id ]['snoozed_until'] >= time() ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks whether the notice should be displayed in the current admin context.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface $notice  Notice instance.
	 * @param string|null     $context Current admin context being rendered.
	 *
	 * @return bool True when the notice should be shown in this context.
	 */
	private function check_context( NoticeInterface $notice, ?string $context ): bool {
		// If no specific context is being rendered, show all notices.
		if ( null === $context ) {
			return true;
		}

		$notice_contexts = $notice->get_context();

		if ( empty( $notice_contexts ) ) {
			return true; // All contexts.
		}

		return in_array( $context, $notice_contexts, true );
	}

	/**
	 * Sorts notices by sticky status first, then by severity, text-domain, and ID.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeInterface[] $notices Array of notices to sort.
	 *
	 * @return NoticeInterface[] Sorted array of notices.
	 */
	private function sort_notices( array $notices ): array {
		$sticky     = [];
		$non_sticky = [];

		foreach ( $notices as $notice ) {
			if ( $notice->is_sticky() ) {
				$sticky[] = $notice;
			} else {
				$non_sticky[] = $notice;
			}
		}

		$sort = function ( NoticeInterface $a, NoticeInterface $b ) {
			// Priorities (lower = higher priority).
			$severity_priority = [
				'error'   => 1,
				'warning' => 2,
				'success' => 3,
				'info'    => 4,
			];

			$a_severity_priority = $severity_priority[ $a->get_severity() ] ?? 999;
			$b_severity_priority = $severity_priority[ $b->get_severity() ] ?? 999;

			// 1. Sort by severity.
			if ( $a_severity_priority !== $b_severity_priority ) {
				return $a_severity_priority <=> $b_severity_priority;
			}

			// 2. Sort by text namespace.
			$namespace_comparison = strcmp( $a->get_namespace(), $b->get_namespace() );
			if ( 0 !== $namespace_comparison ) {
				return $namespace_comparison;
			}

			// 3. Sort by ID for stability.
			return strcmp( $a->get_id(), $b->get_id() );
		};

		usort( $sticky, $sort );
		usort( $non_sticky, $sort );

		return array_merge( $sticky, $non_sticky );
	}
}
