<?php
/**
 * Frontend entry attachment download bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0-beta.3
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GF_Field_FileUpload;
use GFFormsModel;
use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityKit\GravityView\Foundation\Components\SecureDownload;
use GravityKit\GravityView\View\View;
use GVCommon;
use Throwable;
use WP_Error;
use ZipArchive;

/**
 * Builds a ZIP containing selected entries' uploaded files.
 *
 * @since 3.0.0-beta.3
 */
final class DownloadAttachmentsAction implements BulkAction {
	const CLEANUP_HOOK = 'gk_gravityview_bulk_actions_cleanup_download_attachments';

	private const SETTING_FIELDS              = 'fields';
	private const SETTING_EXCLUSION_PATTERNS  = 'exclusion_patterns';
	private const SETTING_PER_FILE_LIMIT_MB   = 'per_file_limit_mb';
	private const SETTING_PER_ENTRY_LIMIT_MB  = 'per_entry_limit_mb';
	private const SETTING_TOTAL_LIMIT_MB      = 'total_limit_mb';
	private const SETTING_FILE_COUNT_LIMIT    = 'file_count_limit';
	private const DEFAULT_PER_FILE_LIMIT_MB   = 500;
	private const DEFAULT_PER_ENTRY_LIMIT_MB  = 1024;
	private const DEFAULT_TOTAL_LIMIT_MB      = 5120;
	private const DEFAULT_FILE_COUNT_LIMIT    = 5000;
	private const ARTIFACT_INCLUDE_LIST       = 'include-list.jsonl';
	private const ARTIFACT_MANIFEST           = 'manifest.jsonl';
	private const ARTIFACT_MANIFEST_CSV       = 'manifest.csv';
	private const ZIP_FILENAME                = 'attachments.zip';
	private const BYTES_PER_MB                = 1048576;
	private const GF_POST_IMAGE_DELIMITER     = '|:|';

	/**
	 * Per-request View form cache.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @var array<int,array>
	 */
	private $view_forms_cache = [];

	/**
	 * Registers background cleanup hooks.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( self::CLEANUP_HOOK, [ __CLASS__, 'cleanup_scheduled_download' ], 10, 3 );
	}

	/**
	 * Cleans up a scheduled download directory.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $dir     Download directory.
	 * @param int    $view_id View ID.
	 * @param int    $blog_id Blog ID.
	 *
	 * @return void
	 */
	public static function cleanup_scheduled_download( $dir, $view_id, $blog_id = 0 ) {
		$dir     = (string) $dir;
		$view_id = absint( $view_id );
		$blog_id = absint( $blog_id );

		if ( is_multisite() && $blog_id && get_current_blog_id() !== $blog_id ) {
			switch_to_blog( $blog_id );

			try {
				self::cleanup_scheduled_download( $dir, $view_id );
			} finally {
				restore_current_blog();
			}

			return;
		}

		if ( '' === $dir || ! self::is_download_directory_path_for_view( $dir, $view_id ) ) {
			return;
		}

		self::delete_path( $dir, true );
	}

	/**
	 * Cleans generated download files and scheduled cleanup events for the current site.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return void
	 */
	public static function cleanup_all_downloads_for_current_site() {
		self::unschedule_cleanup_events();

		$root = self::get_download_site_root();

		if ( '' === $root || ! self::is_download_site_root( $root ) ) {
			return;
		}

		self::delete_path( $root, true );
	}

	/**
	 * Unschedules all download cleanup events for the current site.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return void
	 */
	private static function unschedule_cleanup_events() {
		$current_blog_id = get_current_blog_id();

		foreach ( (array) _get_cron_array() as $timestamp => $hooks ) {
			if ( empty( $hooks[ self::CLEANUP_HOOK ] ) || ! is_array( $hooks[ self::CLEANUP_HOOK ] ) ) {
				continue;
			}

			foreach ( $hooks[ self::CLEANUP_HOOK ] as $event ) {
				$args          = isset( $event['args'] ) ? (array) $event['args'] : [];
				$event_blog_id = absint( $args[2] ?? $current_blog_id );

				if ( $event_blog_id !== $current_blog_id ) {
					continue;
				}

				wp_unschedule_event( (int) $timestamp, self::CLEANUP_HOOK, $args );
			}
		}
	}

	/**
	 * Unschedules cleanup for one token directory.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $dir     Download directory.
	 * @param int    $view_id View ID.
	 * @param int    $blog_id Blog ID.
	 *
	 * @return void
	 */
	private static function unschedule_cleanup_event( $dir, $view_id, $blog_id = 0 ) {
		$args = [
			(string) $dir,
			absint( $view_id ),
			absint( $blog_id ?: get_current_blog_id() ),
		];

		$timestamp = wp_next_scheduled( self::CLEANUP_HOOK, $args );

		while ( false !== $timestamp ) {
			wp_unschedule_event( (int) $timestamp, self::CLEANUP_HOOK, $args );
			$timestamp = wp_next_scheduled( self::CLEANUP_HOOK, $args );
		}
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0-beta.3
	 */
	public function key() {
		return 'download_attachments';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0-beta.3
	 */
	public function config() {
		return [
			'label'                    => __( 'Download Attachments', 'gk-gravityview' ),
			'callback'                 => [ $this, 'process' ],
			'dismiss_callback'         => [ $this, 'dismiss' ],
			'available_callback'       => [ $this, 'is_available' ],
			'capability'               => $this->get_capability(),
			'request_callback'         => [ $this, 'sanitize_request' ],
			'frontend_data_callback'   => [ $this, 'get_frontend_data' ],
			'supports_multi_form_view' => true,
			'settings_schema'          => [
				self::SETTING_FIELDS             => [
					'type'                  => 'multiselect',
					'label'                 => __( 'Allowed file fields', 'gk-gravityview' ),
					'default'               => [],
					'options_callback'      => [ $this, 'get_field_setting_options' ],
					'empty_options_message' => [ $this, 'get_empty_field_options_message' ],
					'class'                 => 'gv-tom-select',
					'placeholder'           => __( 'Select file fields', 'gk-gravityview' ),
					'submit_empty_value'    => true,
					'desc'                  => __( 'Only selected File Upload and Post Image fields can be downloaded from the front end.', 'gk-gravityview' ),
				],
				self::SETTING_EXCLUSION_PATTERNS => [
					'type'              => 'textarea',
					'label'             => __( 'Exclude filenames', 'gk-gravityview' ),
					'default'           => '',
					'sanitize_callback' => [ $this, 'sanitize_patterns_setting' ],
					'desc'              => __( 'Enter one case-insensitive glob pattern per line.', 'gk-gravityview' ),
				],
				self::SETTING_PER_FILE_LIMIT_MB  => [
					'type'              => 'number',
					'label'             => __( 'Maximum file size (MB)', 'gk-gravityview' ),
					'default'           => self::DEFAULT_PER_FILE_LIMIT_MB,
					'min'               => 1,
					'class'             => 'small-text',
					'sanitize_callback' => [ $this, 'sanitize_positive_int_setting' ],
				],
				self::SETTING_PER_ENTRY_LIMIT_MB => [
					'type'              => 'number',
					'label'             => __( 'Maximum per entry (MB)', 'gk-gravityview' ),
					'default'           => self::DEFAULT_PER_ENTRY_LIMIT_MB,
					'min'               => 1,
					'class'             => 'small-text',
					'sanitize_callback' => [ $this, 'sanitize_positive_int_setting' ],
				],
				self::SETTING_TOTAL_LIMIT_MB     => [
					'type'              => 'number',
					'label'             => __( 'Maximum total size (MB)', 'gk-gravityview' ),
					'default'           => self::DEFAULT_TOTAL_LIMIT_MB,
					'min'               => 1,
					'class'             => 'small-text',
					'sanitize_callback' => [ $this, 'sanitize_positive_int_setting' ],
				],
				self::SETTING_FILE_COUNT_LIMIT   => [
					'type'              => 'number',
					'label'             => __( 'Maximum files', 'gk-gravityview' ),
					'default'           => self::DEFAULT_FILE_COUNT_LIMIT,
					'min'               => 1,
					'class'             => 'small-text',
					'sanitize_callback' => [ $this, 'sanitize_positive_int_setting' ],
				],
			],
			'background'               => [
				'enabled'             => true,
				'batch_size'          => 1,
				'threshold'           => 5,
				'default_enabled'     => true,
				'sticky_result'       => true,
				'completion_behavior' => Config::BACKGROUND_COMPLETE_SHOW_MESSAGE,
				'queued_message'      => __( 'Preparing attachment download in the background.', 'gk-gravityview' ),
				'complete_callback'   => [ $this, 'complete' ],
			],
			'lock'                     => false,
		];
	}

	/**
	 * Returns the capability required to use this action.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return string
	 */
	public function get_capability() {
		/**
		 * Filters the capability required to download attachments from frontend bulk actions.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param string $capability Required capability.
		 * @param string $action_key Action key.
		 */
		return (string) apply_filters( 'gk/gravityview/bulk-actions/download-attachments/capability', 'gravityview_edit_entries', $this->key() );
	}

	/**
	 * Sanitizes the action request.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $input      Raw action input.
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return array
	 */
	public function sanitize_request( array $input, View $view, $action_key = '', array $action = [] ) {
		unset( $input, $view, $action_key, $action );

		return [];
	}

	/**
	 * Returns frontend data for the shared confirmation UI.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View   $view       View.
	 * @param array  $action     Action config.
	 * @param string $action_key Action key.
	 *
	 * @return array
	 */
	public function get_frontend_data( View $view, array $action = [], $action_key = '' ) {
		unset( $action_key );

		return [
			'fields' => array_values(
				array_map(
					static function ( $item ) {
						return [
							'token'      => $item['token'],
							'formId'     => (int) $item['form_id'],
							'fieldId'    => $item['field_id'],
							'label'      => $item['field_label'],
							'formLabel'  => $item['form_label'],
							'adminLabel' => $item['admin_label'],
						];
					},
					$this->get_allowed_field_map( $view, $action )
				)
			),
		];
	}

	/**
	 * Returns flattened file field picker options for the action settings UI.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view       View context.
	 * @param array     $action     Action config.
	 * @param string    $action_key Action key.
	 * @param array     $setting    Setting schema.
	 *
	 * @return array
	 */
	public function get_field_setting_options( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $action, $action_key, $setting );

		$options = [];

		foreach ( $this->get_upload_field_map( $view ) as $token => $item ) {
			$options[ $token ] = $item['admin_label'];
		}

		natcasesort( $options );

		return $options;
	}

	/**
	 * Returns the admin empty-state message when no upload fields are available.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view       View context.
	 * @param array     $action     Action config.
	 * @param string    $action_key Action key.
	 * @param array     $setting    Setting schema.
	 *
	 * @return string
	 */
	public function get_empty_field_options_message( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $action, $action_key, $setting );

		$forms = $this->get_view_forms( $view );

		if ( [] === $forms ) {
			return strtr(
				/* translators: [action] is the Download Attachments bulk action label. */
				__( 'Select a form to choose allowed file fields for [action].', 'gk-gravityview' ),
				[
					'[action]' => __( 'Download Attachments', 'gk-gravityview' ),
				]
			);
		}

		if ( [] !== $this->get_upload_field_map( $view ) ) {
			return '';
		}

		if ( 1 === count( $forms ) ) {
			$form_id = (int) key( $forms );

			return strtr(
				/* translators: [link]Add a file field[/link] links to the Gravity Forms form editor. */
				__( 'No File Upload or Post Image fields are configured for this form. [link]Add a file field[/link].', 'gk-gravityview' ),
				[
					'[link]'  => '<a href="' . esc_url( $this->get_form_editor_admin_url( $form_id ) ) . '" target="_blank" rel="noopener noreferrer">',
					'[/link]' => '</a>',
				]
			);
		}

		$links = [];

		foreach ( $forms as $form_id => $form ) {
			$label   = (string) ( $form['title'] ?? $form_id );
			$links[] = '<a href="' . esc_url( $this->get_form_editor_admin_url( (int) $form_id ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a>';
		}

		return strtr(
			/* translators: [links] is a comma-separated list of form links to the Gravity Forms form editor. */
			__( 'No File Upload or Post Image fields are configured for this View. Add one for: [links].', 'gk-gravityview' ),
			[
				'[links]' => implode( ', ', $links ),
			]
		);
	}

	/**
	 * Sanitizes newline-delimited exclusion patterns.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return string
	 */
	public function sanitize_patterns_setting( $value ) {
		return implode( "\n", DownloadAttachmentRules::normalize_patterns( is_scalar( $value ) ? (string) wp_unslash( $value ) : '' ) );
	}

	/**
	 * Sanitizes a positive integer setting.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $value Raw value.
	 * @param array $setting Setting schema.
	 *
	 * @return int
	 */
	public function sanitize_positive_int_setting( $value, array $setting = [] ) {
		$value = is_numeric( $value ) ? (int) $value : (int) ( $setting['default'] ?? 1 );
		$min   = isset( $setting['min'] ) && is_numeric( $setting['min'] ) ? (int) $setting['min'] : 1;

		return max( $min, $value );
	}

	/**
	 * Returns whether this action should appear for the View.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return bool
	 */
	public function is_available( View $view, array $action = [] ) {
		if ( ! class_exists( ZipArchive::class ) ) {
			return false;
		}

		return [] !== $this->get_allowed_field_map( $view, $action );
	}

	/**
	 * Writes per-batch attachment artifacts and finalizes sync requests.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int[]  $entry_ids  Entry IDs.
	 * @param array  $entries    Entries keyed by ID.
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Optional background context.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries, View $view, $action_key = '', array $action = [], array $context = [] ) {
		unset( $entry_ids );

		$download = $this->get_download_location( $view, $context );

		if ( is_wp_error( $download ) ) {
			return $download;
		}

		$validation = $this->validate_allowed_field_settings( $view, $action );

		if ( is_wp_error( $validation ) ) {
			$this->schedule_cleanup( $download );

			return $validation;
		}

		$result = $this->write_batch_artifacts( $entries, $view, $action, $download, $validation, $context );

		if ( is_wp_error( $result ) ) {
			$this->schedule_cleanup( $download );

			return $result;
		}

		$result['cleanup'] = $this->get_cleanup_result( $download );

		if ( $this->is_background_context( $context ) ) {
			return $result;
		}

		$complete = $this->complete(
			$view,
			$action_key,
			$action,
			[
				'processed'      => $result['processed'],
				'failed'         => $result['failed'],
				'batch_result'   => $result,
				'download_token' => $download['token'],
			]
		);

		if ( is_wp_error( $complete ) ) {
			return $complete;
		}

		if ( isset( $complete['notice']['message'] ) ) {
			$result['message'] = $complete['notice']['message'];
		}

		return array_merge( $result, $complete );
	}

	/**
	 * Creates the final ZIP after all batch artifacts have been written.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Background or sync completion context.
	 *
	 * @return array|WP_Error
	 */
	public function complete( View $view, $action_key, array $action, array $context ) {
		unset( $action_key );

		$download = $this->get_download_location( $view, $context );

		if ( is_wp_error( $download ) ) {
			return $download;
		}

		$counts       = $this->get_completion_counts( $context );
		$include_rows = $this->read_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_INCLUDE_LIST );

		if ( is_wp_error( $include_rows ) ) {
			$this->schedule_cleanup( $download );

			return $include_rows;
		}

		if ( [] === $include_rows ) {
			$counts['skip_reasons'] = $this->get_skip_reason_counts( $download );
			$result = $this->build_complete_result( $counts, '', '' );
			$message = $this->format_empty_result_message( $counts );

			$this->schedule_cleanup( $download );
			$this->fire_complete_action( $result, $view, $action );

			return [
				'notice'  => [
					'message' => $message,
				],
				'cleanup' => $this->get_cleanup_result( $download ),
			];
		}

		$zip = $this->create_zip( $download, $include_rows );

		if ( is_wp_error( $zip ) ) {
			$this->schedule_cleanup( $download );

			return $zip;
		}

		if ( ! empty( $zip['skipped_files'] ) ) {
			$counts['skipped']       += (int) $zip['skipped_files'];
			$counts['skipped_files'] += (int) $zip['skipped_files'];
			$counts['included_files'] = max( 0, (int) $counts['included_files'] - (int) $zip['skipped_files'] );
		}

		if ( ! empty( $zip['empty'] ) ) {
			$counts['skip_reasons'] = $this->get_skip_reason_counts( $download );
			$result = $this->build_complete_result( $counts, '', '' );

			$this->schedule_cleanup( $download );
			$this->fire_complete_action( $result, $view, $action );

			return [
				'notice'  => [
					'message' => $this->format_empty_result_message( $counts ),
				],
				'cleanup' => $this->get_cleanup_result( $download ),
			];
		}

		$result = $this->build_complete_result( $counts, $zip['url'], $zip['path'] );
		$result['skip_reasons'] = $this->get_skip_reason_counts( $download );
		$counts['skip_reasons'] = $result['skip_reasons'];

		$this->schedule_cleanup( $download );
		$this->fire_complete_action( $result, $view, $action );

		return [
			'notice'  => [
				'message' => $this->format_success_message( $counts, $zip['url'] ),
			],
			'cleanup' => $this->get_cleanup_result( $download ),
		];
	}

	/**
	 * Returns skipped manifest reason counts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download Download location.
	 *
	 * @return array
	 */
	private function get_skip_reason_counts( array $download ) {
		$rows = $this->read_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST );

		if ( is_wp_error( $rows ) ) {
			return [];
		}

		$reasons = [];

		foreach ( $rows as $row ) {
			if ( DownloadAttachmentRules::STATUS_SKIPPED !== (string) ( $row['status'] ?? '' ) ) {
				continue;
			}

			$reason             = (string) ( $row['reason'] ?? '' );
			$reasons[ $reason ] = max( 0, (int) ( $reasons[ $reason ] ?? 0 ) ) + 1;
		}

		ksort( $reasons );

		return $reasons;
	}

	/**
	 * Cleans up generated files when a sticky result notice is dismissed.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $result     Stored result data.
	 * @param string $token      Result token.
	 *
	 * @return void
	 */
	public function dismiss( View $view, $action_key, array $action, array $result, $token ) {
		unset( $action_key, $action, $token );

		$cleanup = isset( $result['result']['cleanup'] ) && is_array( $result['result']['cleanup'] ) ? $result['result']['cleanup'] : [];
		$dir     = (string) ( $cleanup['dir'] ?? '' );

		if ( '' === $dir || ! $this->is_download_directory_for_view( $dir, $view ) ) {
			return;
		}

		self::unschedule_cleanup_event( $dir, (int) $view->ID, (int) ( $cleanup['blog_id'] ?? get_current_blog_id() ) );
		self::delete_path( $dir, true );
	}

	/**
	 * Writes one batch of manifest and include-list artifacts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $entries    Entries keyed by ID.
	 * @param View  $view       View.
	 * @param array $action     Action config.
	 * @param array $download   Download location.
	 * @param array $field_map  Allowed fields keyed by token.
	 * @param array $context    Processing context.
	 *
	 * @return array|WP_Error
	 */
	private function write_batch_artifacts( array $entries, View $view, array $action, array $download, array $field_map, array $context ) {
		$settings          = $this->get_settings( $action );
		$tokens            = DownloadAttachmentFieldToken::normalize_list( $settings[ self::SETTING_FIELDS ] ?? [] );
		$patterns          = DownloadAttachmentRules::normalize_patterns( $settings[ self::SETTING_EXCLUSION_PATTERNS ] ?? '' );
		$limits            = $this->get_limits( $action );
		$processed         = 0;
		$failed            = 0;
		$skipped           = 0;
		$included_files    = 0;
		$skipped_files     = 0;
		$manifest_rows     = 0;
		$total_bytes       = $this->get_previous_result_count( $context, 'bytes_total' );
		$total_count       = $this->get_previous_result_count( $context, 'included_files' );
		$previous_skipped  = $this->get_previous_result_count( $context, 'skipped_files' );
		$used_zip_names    = $this->get_used_zip_names_from_include_list( $download );
		$view_form_ids     = array_fill_keys( array_keys( $this->get_view_forms( $view ) ), true );
		$fields_by_form_id = $this->get_selected_fields_by_form( $tokens, $field_map );

		if ( is_wp_error( $used_zip_names ) ) {
			return $used_zip_names;
		}

		foreach ( $this->expand_entries( $entries ) as $entry ) {
			$form_id = (int) ( $entry['form_id'] ?? 0 );

			if ( ! $form_id || ! isset( $view_form_ids[ $form_id ] ) ) {
				$written = $this->append_entry_skip_row( $download, $entry, DownloadAttachmentRules::REASON_ENTRY_FORM_NOT_IN_VIEW );

				if ( is_wp_error( $written ) ) {
					return $written;
				}

				++$skipped;
				++$skipped_files;
				++$manifest_rows;
				continue;
			}

			if ( empty( $fields_by_form_id[ $form_id ] ) ) {
				continue;
			}

			$entry_id       = (int) ( $entry['id'] ?? 0 );
			$entry_bytes    = 0;
			$entry_included = false;
			$entry_skipped  = false;

			foreach ( $fields_by_form_id[ $form_id ] as $item ) {
				$field = $item['field'];
				$value = $entry[ (string) $field->id ] ?? '';
				$urls  = $this->get_file_urls_from_field_value( $field, $value );

				if ( [] === $urls ) {
					continue;
				}

				foreach ( $urls as $url ) {
					$normalized = $this->normalize_file_url( $url, $form_id );

					if ( '' === $normalized ) {
						continue;
					}

					$decision = $this->evaluate_file( $normalized, $entry, $item, $action, $patterns, $limits, $entry_bytes, $total_bytes, $total_count, $used_zip_names );
					$row      = $decision['manifest'];

					$written = $this->append_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST, $row );

					if ( is_wp_error( $written ) ) {
						return $written;
					}

					++$manifest_rows;

					if ( DownloadAttachmentRules::STATUS_INCLUDED !== $row['status'] ) {
						++$skipped;
						++$skipped_files;
						$entry_skipped = true;
						continue;
					}

					$written = $this->append_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_INCLUDE_LIST, $decision['include'] );

					if ( is_wp_error( $written ) ) {
						return $written;
					}

					$entry_included = true;
					++$included_files;
					++$total_count;
					$total_bytes += (int) $row['size_bytes'];
					$entry_bytes += (int) $row['size_bytes'];
				}
			}

			if ( $entry_included ) {
				++$processed;
				continue;
			}

			if ( $entry_skipped ) {
				continue;
			}
		}

		return [
			'processed'      => $processed,
			'failed'         => $failed,
			'skipped'        => $previous_skipped + $skipped,
			'included_files' => $total_count,
			'skipped_files'  => $previous_skipped + $skipped_files,
			'bytes_total'    => $total_bytes,
			'manifest_rows'  => $this->get_previous_result_count( $context, 'manifest_rows' ) + $manifest_rows,
			'message'        => $this->format_batch_message( $included_files, $skipped_files ),
		];
	}

	/**
	 * Rehydrates ZIP names already written by previous batches.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download Download location.
	 *
	 * @return array|WP_Error
	 */
	private function get_used_zip_names_from_include_list( array $download ) {
		$rows = $this->read_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_INCLUDE_LIST );

		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		$used = [];

		foreach ( $rows as $row ) {
			$zip_path = (string) ( $row['zip_path'] ?? '' );

			if ( '' === $zip_path || ! $this->is_safe_zip_path( $zip_path ) ) {
				continue;
			}

			$folder = dirname( $zip_path );
			$name   = basename( $zip_path );

			if ( '.' === $folder || '' === $name ) {
				continue;
			}

			$used[ $folder ] = $used[ $folder ] ?? [];
			$used[ $folder ][ strtolower( $name ) ] = true;
		}

		return $used;
	}

	/**
	 * Writes a manifest row for an entry-level skip.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $download Download location.
	 * @param array  $entry    Entry.
	 * @param string $reason   Skip reason.
	 *
	 * @return true|WP_Error
	 */
	private function append_entry_skip_row( array $download, array $entry, $reason ) {
		$row = $this->manifest_row(
			(int) ( $entry['id'] ?? 0 ),
			(int) ( $entry['form_id'] ?? 0 ),
			'',
			'',
			0,
			DownloadAttachmentRules::STATUS_SKIPPED,
			$reason
		);

		return $this->append_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST, $row );
	}

	/**
	 * Evaluates one file value and returns manifest/include rows.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $raw_url        Raw file URL.
	 * @param array  $entry          Entry.
	 * @param array  $item           Field map item.
	 * @param array  $action         Action config.
	 * @param array  $patterns       Exclusion patterns.
	 * @param array  $limits         Size/count limits.
	 * @param int    $entry_bytes    Entry bytes already included.
	 * @param int    $total_bytes    Operation bytes already included.
	 * @param int    $total_count    Operation files already included.
	 * @param array  $used_zip_names ZIP names already used in this batch.
	 *
	 * @return array
	 */
	private function evaluate_file( $url, array $entry, array $item, array $action, array $patterns, array $limits, $entry_bytes, $total_bytes, $total_count, array &$used_zip_names ) {
		$form_id     = (int) $item['form_id'];
		$field_id    = (string) $item['field_id'];
		$entry_id    = (int) ( $entry['id'] ?? 0 );
		$basename    = $this->get_url_basename( $url );
		$safe_name   = DownloadAttachmentRules::sanitize_basename( $basename );
		$manifest    = $this->manifest_row( $entry_id, $form_id, $field_id, $safe_name, 0, DownloadAttachmentRules::STATUS_SKIPPED, DownloadAttachmentRules::REASON_MISSING_FILE );
		$upload_root = $this->get_upload_root_for_file( $url, $entry_id, $form_id );

		if ( '' === $safe_name ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_INVALID_FILENAME;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		$path = $this->resolve_physical_file_path( $url, $entry_id, $form_id );

		if ( '' === $upload_root || ! $this->path_can_be_checked_under_directory( $path, $upload_root ) ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_OUTSIDE_UPLOADS;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		if ( ! file_exists( $path ) ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_MISSING_FILE;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		if ( ! $this->path_is_inside_directory( $path, $upload_root ) ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_OUTSIDE_UPLOADS;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		if ( ! is_readable( $path ) ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_UNREADABLE_FILE;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		$size = filesize( $path );
		$size = false === $size ? 0 : (int) $size;

		$manifest['size_bytes'] = $size;

		if ( DownloadAttachmentRules::basename_matches_any_glob( $basename, $patterns ) ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_EXCLUDED_PATTERN;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		$file_meta = [
			'url'         => $url,
			'src'         => $path,
			'upload_root' => $upload_root,
			'basename'    => $basename,
			'file_name'   => $safe_name,
			'size'        => $size,
			'entry_id'    => $entry_id,
			'form_id'     => $form_id,
			'field_id'    => $field_id,
			'field_type'  => $item['field_type'],
		];
		$field_meta = [
			'id'      => $field_id,
			'form_id' => $form_id,
			'label'   => $item['field_label'],
			'type'    => $item['field_type'],
			'token'   => $item['token'],
		];

		/**
		 * Filters whether an attachment should be included in the frontend bulk download.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param bool  $included Whether the file should be included.
		 * @param array $file_meta File metadata.
		 * @param array $entry     Entry object.
		 * @param array $field     Field metadata.
		 * @param array $action    Action config.
		 */
		$included = (bool) apply_filters( 'gk/gravityview/bulk-actions/download-attachments/file-included', true, $file_meta, $entry, $field_meta, $action );

		if ( ! $included ) {
			$manifest['reason'] = DownloadAttachmentRules::REASON_FILTERED;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		$limit_reason = DownloadAttachmentRules::first_limit_reason(
			$size,
			$entry_bytes,
			$total_bytes,
			$total_count,
			$limits['per_file'],
			$limits['per_entry'],
			$limits['total'],
			$limits['file_count']
		);

		if ( '' !== $limit_reason ) {
			$manifest['reason'] = $limit_reason;

			return [
				'manifest' => $manifest,
				'include'  => [],
			];
		}

		$entry_folder = 'entry-' . $entry_id;

		if ( ! isset( $used_zip_names[ $entry_folder ] ) ) {
			$used_zip_names[ $entry_folder ] = [];
		}

		$zip_name = DownloadAttachmentRules::unique_basename( $safe_name, $used_zip_names[ $entry_folder ] );
		$zip_path = $entry_folder . '/' . $zip_name;

		$manifest['file_name'] = $zip_name;
		$manifest['status']    = DownloadAttachmentRules::STATUS_INCLUDED;
		$manifest['reason']    = DownloadAttachmentRules::REASON_INCLUDED;

		return [
			'manifest' => $manifest,
			'include'  => [
				'src'         => $path,
				'upload_root' => $upload_root,
				'zip_path'    => $zip_path,
				'size'        => $size,
				'entry_id'    => $entry_id,
				'form_id'     => $form_id,
				'field_id'    => $field_id,
				'file_name'   => $zip_name,
				'status'      => DownloadAttachmentRules::STATUS_INCLUDED,
				'reason'      => DownloadAttachmentRules::REASON_INCLUDED,
			],
		];
	}

	/**
	 * Creates the final ZIP file from include-list artifacts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download     Download location.
	 * @param array $include_rows Include rows.
	 *
	 * @return array|WP_Error
	 */
	private function create_zip( array $download, array $include_rows ) {
		if ( ! class_exists( ZipArchive::class ) ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_zip_unavailable', __( 'ZIP support is not available on this site.', 'gk-gravityview' ) );
		}

		$archive_path          = trailingslashit( $download['dir'] ) . self::ZIP_FILENAME;
		$zip                   = new ZipArchive();
		$opened                = $zip->open( $archive_path, ZipArchive::CREATE | ZipArchive::OVERWRITE );
		$added_files           = 0;
		$skipped_files         = 0;
		$skipped_manifest_keys = [];

		if ( true !== $opened ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
		}

		foreach ( $include_rows as $row ) {
			$src           = (string) ( $row['src'] ?? '' );
			$internal_path = (string) ( $row['zip_path'] ?? '' );
			$upload_root   = (string) ( $row['upload_root'] ?? $this->get_upload_root() );

			if ( '' === $src || '' === $internal_path || ! $this->is_safe_zip_path( $internal_path ) ) {
				$this->record_zip_time_skip( $download, $row, DownloadAttachmentRules::REASON_OUTSIDE_UPLOADS );
				$skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] = true;
				++$skipped_files;
				continue;
			}

			if ( ! file_exists( $src ) ) {
				$this->record_zip_time_skip( $download, $row, DownloadAttachmentRules::REASON_MISSING_FILE );
				$skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] = true;
				++$skipped_files;
				continue;
			}

			if ( '' === $upload_root || ! $this->path_is_inside_directory( $src, $upload_root ) ) {
				$this->record_zip_time_skip( $download, $row, DownloadAttachmentRules::REASON_OUTSIDE_UPLOADS );
				$skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] = true;
				++$skipped_files;
				continue;
			}

			if ( ! is_readable( $src ) ) {
				$this->record_zip_time_skip( $download, $row, DownloadAttachmentRules::REASON_UNREADABLE_FILE );
				$skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] = true;
				++$skipped_files;
				continue;
			}

			if ( ! $zip->addFile( $src, $internal_path ) ) {
				$this->record_zip_time_skip( $download, $row, DownloadAttachmentRules::REASON_UNREADABLE_FILE );
				$skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] = true;
				++$skipped_files;
				continue;
			}

			++$added_files;
		}

		if ( ! $added_files ) {
			$zip->close();
			self::delete_path( $archive_path );

			return [
				'path'          => '',
				'url'           => '',
				'empty'         => true,
				'skipped_files' => $skipped_files,
			];
		}

		$manifest = $this->create_manifest_csv( $download, $skipped_manifest_keys );

		if ( is_wp_error( $manifest ) ) {
			$zip->close();

			return $manifest;
		}

		if ( ! $zip->addFile( $manifest, self::ARTIFACT_MANIFEST_CSV ) ) {
			$zip->close();

			return new WP_Error( 'gravityview_bulk_download_attachments_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
		}

		if ( ! $zip->close() ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
		}

		$url = $this->get_secure_download_url( $archive_path, $this->get_zip_download_filename( (int) $download['view_id'] ) );

		if ( is_wp_error( $url ) ) {
			return $url;
		}

		return [
			'path'          => $archive_path,
			'url'           => $url,
			'skipped_files' => $skipped_files,
		];
	}

	/**
	 * Records a source-file skip detected while creating the ZIP.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $download Download location.
	 * @param array  $row      Include-list row.
	 * @param string $reason   Skip reason.
	 *
	 * @return void
	 */
	private function record_zip_time_skip( array $download, array $row, $reason ) {
		$manifest = $this->manifest_row(
			(int) ( $row['entry_id'] ?? 0 ),
			(int) ( $row['form_id'] ?? 0 ),
			(string) ( $row['field_id'] ?? '' ),
			(string) ( $row['file_name'] ?? basename( (string) ( $row['zip_path'] ?? '' ) ) ),
			(int) ( $row['size'] ?? 0 ),
			DownloadAttachmentRules::STATUS_SKIPPED,
			$reason
		);

		$this->append_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST, $manifest );
		gravityview()->log->debug(
			'Attachment download source file skipped while creating ZIP.',
			[
				'reason' => $reason,
				'src'    => (string) ( $row['src'] ?? '' ),
			]
		);
	}

	/**
	 * Returns the manifest identity key for a file row.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $row Manifest or include row.
	 *
	 * @return string
	 */
	private function manifest_identity_key( array $row ) {
		return (int) ( $row['entry_id'] ?? 0 ) . ':' . (string) ( $row['file_name'] ?? '' );
	}

	/**
	 * Creates manifest.csv from manifest JSONL rows.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download              Download location.
	 * @param array $skipped_manifest_keys Manifest keys to suppress as included rows.
	 *
	 * @return string|WP_Error
	 */
	private function create_manifest_csv( array $download, array $skipped_manifest_keys = [] ) {
		$rows = $this->read_jsonl( trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST );

		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		$path   = trailingslashit( $download['dir'] ) . self::ARTIFACT_MANIFEST_CSV;
		$handle = fopen( $path, 'w' );

		if ( ! $handle ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_manifest_failed', __( 'The file manifest could not be created.', 'gk-gravityview' ) );
		}

		$columns = [ 'entry_id', 'form_id', 'field_id', 'file_name', 'size_bytes', 'status', 'reason' ];

		fputcsv( $handle, $columns );

		foreach ( $rows as $row ) {
			if ( DownloadAttachmentRules::STATUS_INCLUDED === (string) ( $row['status'] ?? '' ) && isset( $skipped_manifest_keys[ $this->manifest_identity_key( $row ) ] ) ) {
				continue;
			}

			fputcsv(
				$handle,
				array_map(
					static function ( $column ) use ( $row ) {
						return DownloadAttachmentRules::sanitize_csv_cell( $row[ $column ] ?? '' );
					},
					$columns
				)
			);
		}

		fclose( $handle );

		return $path;
	}

	/**
	 * Returns allowed fields keyed by token.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private function get_allowed_field_map( View $view, array $action ) {
		$settings = $this->get_settings( $action );
		$tokens   = DownloadAttachmentFieldToken::normalize_list( $settings[ self::SETTING_FIELDS ] ?? [] );

		if ( [] === $tokens ) {
			return [];
		}

		$fields  = $this->get_upload_field_map( $view );
		$allowed = [];

		foreach ( $tokens as $token ) {
			if ( isset( $fields[ $token ] ) ) {
				$allowed[ $token ] = $fields[ $token ];
			}
		}

		return $allowed;
	}

	/**
	 * Validates saved field tokens against the current View forms.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return array|WP_Error
	 */
	private function validate_allowed_field_settings( View $view, array $action ) {
		$settings = $this->get_settings( $action );
		$tokens   = DownloadAttachmentFieldToken::normalize_list( $settings[ self::SETTING_FIELDS ] ?? [] );
		$allowed  = $this->get_allowed_field_map( $view, $action );

		if ( [] === $tokens ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_missing_fields', __( 'No file fields were selected.', 'gk-gravityview' ) );
		}

		foreach ( $tokens as $token ) {
			if ( ! isset( $allowed[ $token ] ) ) {
				return new WP_Error( 'gravityview_bulk_download_attachments_invalid_fields', __( 'One or more selected file fields are no longer available.', 'gk-gravityview' ) );
			}
		}

		return $allowed;
	}

	/**
	 * Returns all upload-capable View fields keyed by token.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View $view View.
	 *
	 * @return array
	 */
	private function get_upload_field_map( ?View $view ) {
		$items = [];

		foreach ( $this->get_view_forms( $view ) as $form_id => $form ) {
			foreach ( $this->get_form_upload_fields( $form ) as $field ) {
				$field_id = (string) $field->id;
				$token    = DownloadAttachmentFieldToken::build( $form_id, $field_id );

				if ( '' === $token ) {
					continue;
				}

				$form_label  = (string) ( $form['title'] ?? $form_id );
				$field_label = (string) ( $field->label ?: $field_id );

				$items[ $token ] = [
					'token'       => $token,
					'form_id'     => (int) $form_id,
					'form_label'  => $form_label,
					'field_id'    => $field_id,
					'field_label' => $field_label,
					'field_type'  => (string) $field->type,
					'admin_label' => $form_label . ': ' . $field_label,
					'field'       => $field,
					'form'        => $form,
				];
			}
		}

		uasort(
			$items,
			static function ( $a, $b ) {
				$result = strnatcasecmp( (string) $a['admin_label'], (string) $b['admin_label'] );

				return 0 === $result ? strnatcasecmp( (string) $a['token'], (string) $b['token'] ) : $result;
			}
		);

		return $items;
	}

	/**
	 * Returns forms available to the View.
	 *
	 * Returns forms available to the View, falling back to the admin form picker's
	 * currently-selected form when the View context is null or unsaved.
	 *
	 * The Bulk Actions widget can be added before the View is first saved, so
	 * `$view->form->ID` may be 0 (auto-draft state) or `$view` may be null
	 * entirely (the AJAX path that renders widget settings). In both cases the
	 * admin AJAX request still carries `form_id` from the Data Source picker,
	 * which is enough to populate the upload-field list without forcing a save.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view View.
	 *
	 * @return array
	 */
	private function get_view_forms( ?View $view ) {
		// Cache by View ID for saved Views; by form_id for the admin AJAX fallback so two
		// different form pickers in the same process don't return each other's forms.
		$cache_key = $view
			? 'view-' . (int) $view->ID
			: 'form-' . absint( wp_unslash( $_POST['form_id'] ?? 0 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- gv_field_options handler verifies the nonce before this callback runs.

		if ( isset( $this->view_forms_cache[ $cache_key ] ) ) {
			return $this->view_forms_cache[ $cache_key ];
		}

		$forms = [];

		if ( $view && ! empty( $view->form->ID ) ) {
			$form = GVCommon::get_form( (int) $view->form->ID );

			if ( $form ) {
				$forms[ (int) $form['id'] ] = $form;
			}
		}

		if ( $view ) {
			foreach ( View::get_joined_forms( (int) $view->ID ) as $joined_form ) {
				if ( empty( $joined_form->ID ) ) {
					continue;
				}

				$form = GVCommon::get_form( (int) $joined_form->ID );

				if ( $form ) {
					$forms[ (int) $form['id'] ] = $form;
				}
			}
		}

		if ( [] === $forms ) {
			$fallback_form_id = isset( $_POST['form_id'] ) ? absint( wp_unslash( $_POST['form_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- gv_field_options handler verifies the nonce before this callback runs.

			if ( $fallback_form_id ) {
				$fallback_form = GVCommon::get_form( $fallback_form_id );

				if ( $fallback_form ) {
					$forms[ $fallback_form_id ] = $fallback_form;
				}
			}
		}

		$this->view_forms_cache[ $cache_key ] = $forms;

		return $forms;
	}

	/**
	 * Returns upload-capable fields for a form.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $form Form.
	 *
	 * @return GF_Field_FileUpload[]
	 */
	private function get_form_upload_fields( array $form ) {
		$fields = [];

		foreach ( (array) ( $form['fields'] ?? [] ) as $field ) {
			if ( ! $field instanceof GF_Field_FileUpload || ! in_array( (string) $field->type, [ 'fileupload', 'post_image' ], true ) ) {
				continue;
			}

			$fields[] = $field;
		}

		return $fields;
	}

	/**
	 * Groups selected fields by form ID.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string[] $tokens    Selected tokens.
	 * @param array    $field_map Allowed field map.
	 *
	 * @return array
	 */
	private function get_selected_fields_by_form( array $tokens, array $field_map ) {
		$grouped = [];

		foreach ( $tokens as $token ) {
			if ( empty( $field_map[ $token ] ) ) {
				continue;
			}

			$form_id             = (int) $field_map[ $token ]['form_id'];
			$grouped[ $form_id ] = $grouped[ $form_id ] ?? [];
			$grouped[ $form_id ][] = $field_map[ $token ];
		}

		return $grouped;
	}

	/**
	 * Expands row entries into concrete Gravity Forms entries.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $entries Entries keyed by row entry ID.
	 *
	 * @return array
	 */
	private function expand_entries( array $entries ) {
		$expanded = [];

		foreach ( $entries as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			if ( ! empty( $entry['_multi'] ) && is_array( $entry['_multi'] ) ) {
				foreach ( $entry['_multi'] as $multi_entry ) {
					if ( is_array( $multi_entry ) && ! empty( $multi_entry['id'] ) && ! empty( $multi_entry['form_id'] ) ) {
						$expanded[ (int) $multi_entry['form_id'] . ':' . (int) $multi_entry['id'] ] = $multi_entry;
					}
				}

				continue;
			}

			if ( ! empty( $entry['id'] ) && ! empty( $entry['form_id'] ) ) {
				$expanded[ (int) $entry['form_id'] . ':' . (int) $entry['id'] ] = $entry;
			}
		}

		return array_values( $expanded );
	}

	/**
	 * Returns file URLs from a field value.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param GF_Field_FileUpload $field Field object.
	 * @param mixed               $value Entry field value.
	 *
	 * @return string[]
	 */
	private function get_file_urls_from_field_value( GF_Field_FileUpload $field, $value ) {
		// `GF_Field_FileUpload` does not implement `to_array()` (only
		// `GF_Field_List` and `GF_Field_MultiSelect` do). Decode the
		// stored value directly: multi-file mode stores a JSON array
		// of URLs, single-file mode stores a single URL string.
		if ( '' === $value || null === $value ) {
			return [];
		}

		if ( ! empty( $field->multipleFiles ) ) {
			$decoded = json_decode( (string) $value, true );
			$urls    = is_array( $decoded ) ? $decoded : [];
		} else {
			$urls = [ (string) $value ];
		}

		return array_values(
			array_filter(
				array_map(
					static function ( $url ) {
						$url = is_scalar( $url ) ? trim( (string) $url ) : '';

						return '' === $url ? '' : $url;
					},
					$urls
				),
				'strlen'
			)
		);
	}

	/**
	 * Normalizes an entry file URL before filesystem resolution.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $raw_url Raw URL.
	 * @param int    $form_id Form ID.
	 *
	 * @return string
	 */
	private function normalize_file_url( $raw_url, $form_id ) {
		$url = is_scalar( $raw_url ) ? trim( (string) $raw_url ) : '';
		$url = (string) ( explode( self::GF_POST_IMAGE_DELIMITER, $url, 2 )[0] ?? '' );

		if ( '' === $url ) {
			return '';
		}

		$query = wp_parse_url( $url, PHP_URL_QUERY );

		if ( ! is_string( $query ) || '' === $query ) {
			return $url;
		}

		parse_str( $query, $args );

		if ( empty( $args['gf-download'] ) ) {
			return $url;
		}

		if ( ! empty( $args['form-id'] ) && absint( $args['form-id'] ) !== (int) $form_id ) {
			return $url;
		}

		$relative = rawurldecode( (string) $args['gf-download'] );

		if ( ! $form_id || '' === $relative ) {
			return $url;
		}

		$root_info = $this->get_form_upload_root_info( $form_id );
		$root_url  = (string) ( $root_info['url'] ?? '' );

		if ( '' === $root_url ) {
			return $url;
		}

		return trailingslashit( $root_url ) . ltrim( str_replace( [ '\\', "\0" ], [ '/', '' ], $relative ), '/' );
	}

	/**
	 * Resolves a file URL to a physical path.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $url      File URL.
	 * @param int    $entry_id Entry ID.
	 * @param int    $form_id  Form ID.
	 *
	 * @return string
	 */
	private function resolve_physical_file_path( $url, $entry_id, $form_id ) {
		$path      = GFFormsModel::get_physical_file_path( $url, $entry_id );
		$fallbacks = $this->get_upload_path_fallbacks( $url, $form_id );
		$root      = $this->get_upload_root_for_file( $url, $entry_id, $form_id );

		if ( $this->path_is_inside_directory( $path, $root ) && file_exists( $path ) ) {
			return $path;
		}

		foreach ( $fallbacks as $fallback ) {
			if ( $this->path_can_be_checked_under_directory( $fallback, $root ) ) {
				return $fallback;
			}
		}

		return $path;
	}

	/**
	 * Returns upload-path fallbacks for a URL.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $url     File URL.
	 * @param int    $form_id Form ID.
	 *
	 * @return string[]
	 */
	private function get_upload_path_fallbacks( $url, $form_id ) {
		$fallbacks = [];
		$root_info = $this->get_form_upload_root_info( $form_id );
		$form_url  = trailingslashit( (string) ( $root_info['url'] ?? GFFormsModel::get_upload_url( $form_id ) ) );
		$form_path = trailingslashit( (string) ( $root_info['path'] ?? GFFormsModel::get_upload_path( $form_id ) ) );

		if ( 0 === strpos( $url, $form_url ) ) {
			$fallbacks[] = str_replace( $form_url, $form_path, $url );
		}

		$form_url_path = wp_parse_url( $form_url, PHP_URL_PATH );
		$root_url_path = wp_parse_url( GFFormsModel::get_upload_url_root(), PHP_URL_PATH );
		$url_path      = wp_parse_url( $url, PHP_URL_PATH );

		if ( is_string( $form_url_path ) && is_string( $url_path ) && 0 === strpos( trailingslashit( $url_path ), trailingslashit( $form_url_path ) ) ) {
			$fallbacks[] = $form_path . ltrim( substr( $url_path, strlen( untrailingslashit( $form_url_path ) ) ), '/' );
		}

		if ( is_string( $root_url_path ) && is_string( $url_path ) && 0 === strpos( trailingslashit( $url_path ), trailingslashit( $root_url_path ) ) ) {
			$fallbacks[] = trailingslashit( GFFormsModel::get_upload_root() ) . ltrim( substr( $url_path, strlen( untrailingslashit( $root_url_path ) ) ), '/' );
		}

		return array_values( array_unique( array_filter( $fallbacks, 'strlen' ) ) );
	}

	/**
	 * Returns a filter-aware form upload root.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return array
	 */
	private function get_form_upload_root_info( $form_id ) {
		if ( method_exists( GF_Field_FileUpload::class, 'get_upload_root_info' ) ) {
			$root_info = GF_Field_FileUpload::get_upload_root_info( (int) $form_id );

			if ( is_array( $root_info ) ) {
				return $root_info;
			}
		}

		return [
			'path' => GFFormsModel::get_upload_path( (int) $form_id ),
			'url'  => GFFormsModel::get_upload_url( (int) $form_id ),
		];
	}

	/**
	 * Returns the filter-aware upload root for one file value.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $url      File URL.
	 * @param int    $entry_id Entry ID.
	 * @param int    $form_id  Form ID.
	 *
	 * @return string
	 */
	private function get_upload_root_for_file( $url, $entry_id, $form_id ) {
		$form_root_info = $this->get_form_upload_root_info( $form_id );
		$form_root_path = (string) ( $form_root_info['path'] ?? '' );
		$form_root_url  = (string) ( $form_root_info['url'] ?? '' );

		if ( method_exists( GF_Field_FileUpload::class, 'get_file_upload_path_info' ) ) {
			$path_info = GF_Field_FileUpload::get_file_upload_path_info( $url, $entry_id );

			if ( is_array( $path_info ) && ! empty( $path_info['path'] ) ) {
				if ( '' !== $form_root_path && '' !== $form_root_url && 0 === strpos( (string) $url, trailingslashit( $form_root_url ) ) ) {
					return $form_root_path;
				}

				return (string) $path_info['path'];
			}
		}

		return $form_root_path;
	}

	/**
	 * Returns a display basename from a URL or path value.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $url URL or path.
	 *
	 * @return string
	 */
	private function get_url_basename( $url ) {
		$path = wp_parse_url( (string) $url, PHP_URL_PATH );

		if ( ! is_string( $path ) || '' === $path ) {
			$path = (string) $url;
		}

		return rawurldecode( basename( str_replace( [ '\\', "\0" ], [ '/', '' ], $path ) ) );
	}

	/**
	 * Returns the Gravity Forms upload root.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return string
	 */
	private function get_upload_root() {
		$path = GFFormsModel::get_upload_root();

		return is_string( $path ) ? $path : '';
	}

	/**
	 * Returns whether a path is inside a root directory.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path File path.
	 * @param string $root Root directory.
	 *
	 * @return bool
	 */
	private function path_is_inside_directory( $path, $root ) {
		$root = untrailingslashit( wp_normalize_path( (string) $root ) );
		$path = wp_normalize_path( (string) $path );

		if ( '' === $root || '' === $path ) {
			return false;
		}

		if ( $this->path_contains_parent_segments( $path ) || $this->path_contains_parent_segments( $root ) ) {
			return false;
		}

		$real_root = realpath( $root );
		$real_path = realpath( $path );

		if ( false === $real_root || false === $real_path ) {
			return false;
		}

		$root = untrailingslashit( wp_normalize_path( $real_root ) );
		$path = untrailingslashit( wp_normalize_path( $real_path ) );

		return $path !== $root && 0 === strpos( $path, trailingslashit( $root ) );
	}

	/**
	 * Returns whether a path can be checked against a directory before realpath is available.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path File path.
	 * @param string $root Root directory.
	 *
	 * @return bool
	 */
	private function path_can_be_checked_under_directory( $path, $root ) {
		$root = untrailingslashit( wp_normalize_path( (string) $root ) );
		$path = untrailingslashit( wp_normalize_path( (string) $path ) );

		if ( '' === $root || '' === $path ) {
			return false;
		}

		if ( $this->path_contains_parent_segments( $path ) || $this->path_contains_parent_segments( $root ) ) {
			return false;
		}

		return $path !== $root && 0 === strpos( $path, trailingslashit( $root ) );
	}

	/**
	 * Returns whether a normalized path contains parent traversal segments.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path Path.
	 *
	 * @return bool
	 */
	private function path_contains_parent_segments( $path ) {
		$path = '/' . trim( wp_normalize_path( (string) $path ), '/' );

		return false !== strpos( $path, '/../' ) || substr( $path, -3 ) === '/..';
	}

	/**
	 * Returns current action settings.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private function get_settings( array $action ) {
		return isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
	}

	/**
	 * Returns configured limits in bytes/counts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private function get_limits( array $action ) {
		$settings = $this->get_settings( $action );

		return [
			'per_file'   => $this->setting_mb_to_bytes( $settings[ self::SETTING_PER_FILE_LIMIT_MB ] ?? self::DEFAULT_PER_FILE_LIMIT_MB, self::DEFAULT_PER_FILE_LIMIT_MB ),
			'per_entry'  => $this->setting_mb_to_bytes( $settings[ self::SETTING_PER_ENTRY_LIMIT_MB ] ?? self::DEFAULT_PER_ENTRY_LIMIT_MB, self::DEFAULT_PER_ENTRY_LIMIT_MB ),
			'total'      => $this->setting_mb_to_bytes( $settings[ self::SETTING_TOTAL_LIMIT_MB ] ?? self::DEFAULT_TOTAL_LIMIT_MB, self::DEFAULT_TOTAL_LIMIT_MB ),
			'file_count' => $this->setting_count_limit( $settings[ self::SETTING_FILE_COUNT_LIMIT ] ?? self::DEFAULT_FILE_COUNT_LIMIT ),
		];
	}

	/**
	 * Converts a megabyte setting to bytes.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed  $value        Raw setting value.
	 * @param int    $default      Default megabytes.
	 * @return int
	 */
	private function setting_mb_to_bytes( $value, $default ) {
		$mb = is_numeric( $value ) ? (int) $value : (int) $default;
		$mb = max( 1, $mb );
		$mb = min( $mb, (int) floor( PHP_INT_MAX / self::BYTES_PER_MB ) );

		return max( 1, $mb * self::BYTES_PER_MB );
	}

	/**
	 * Returns the configured file count cap.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $value Raw setting value.
	 *
	 * @return int
	 */
	private function setting_count_limit( $value ) {
		$count = is_numeric( $value ) ? (int) $value : self::DEFAULT_FILE_COUNT_LIMIT;

		return max( 1, $count );
	}

	/**
	 * Returns a manifest row.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int    $entry_id   Entry ID.
	 * @param int    $form_id    Form ID.
	 * @param string $field_id   Field ID.
	 * @param string $file_name  File name.
	 * @param int    $size_bytes Size in bytes.
	 * @param string $status     Status.
	 * @param string $reason     Reason.
	 *
	 * @return array
	 */
	private function manifest_row( $entry_id, $form_id, $field_id, $file_name, $size_bytes, $status, $reason ) {
		return [
			'entry_id'   => (int) $entry_id,
			'form_id'    => (int) $form_id,
			'field_id'   => (string) $field_id,
			'file_name'  => (string) $file_name,
			'size_bytes' => max( 0, (int) $size_bytes ),
			'status'     => (string) $status,
			'reason'     => (string) $reason,
		];
	}

	/**
	 * Appends one JSONL artifact row.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path File path.
	 * @param array  $row  Row data.
	 *
	 * @return true|WP_Error
	 */
	private function append_jsonl( $path, array $row ) {
		$json = wp_json_encode( $row );

		if ( ! is_string( $json ) ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_write_failed', __( 'The download artifacts could not be written.', 'gk-gravityview' ) );
		}

		$result = file_put_contents( $path, $json . "\n", FILE_APPEND | LOCK_EX );

		if ( false === $result ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_write_failed', __( 'The download artifacts could not be written.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Reads a JSONL artifact file.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path File path.
	 *
	 * @return array|WP_Error
	 */
	private function read_jsonl( $path ) {
		if ( ! file_exists( $path ) ) {
			return [];
		}

		$lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		if ( false === $lines ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_read_failed', __( 'The download artifacts could not be read.', 'gk-gravityview' ) );
		}

		$rows = [];

		foreach ( $lines as $line ) {
			$row = json_decode( $line, true );

			if ( is_array( $row ) ) {
				$rows[] = $row;
			}
		}

		return $rows;
	}

	/**
	 * Returns a secure download URL for a generated ZIP file.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path     Absolute file path.
	 * @param string $filename Download filename.
	 * @param array  $args     Secure download argument overrides.
	 *
	 * @return string|WP_Error
	 */
	private function get_secure_download_url( $path, $filename, array $args = [] ) {
		try {
			$args = array_merge(
				[
					'cache_duration' => 0,
					'disposition'    => 'attachment',
					'expires_in'     => ResultStore::DEFAULT_TTL,
					'filename'       => $filename,
					'limit'          => 1,
					'users'          => [ get_current_user_id() ],
				],
				$args
			);

			$result = SecureDownload::get_instance()->generate_download_url(
				$path,
				$args
			);
		} catch ( Throwable $e ) {
			gravityview()->log->error(
				'Frontend attachment download secure URL generation failed.',
				[
					'error' => $e->getMessage(),
					'path'  => $path,
				]
			);

			return new WP_Error( 'gravityview_bulk_download_attachments_secure_download_failed', __( 'The secure download link could not be created.', 'gk-gravityview' ) );
		}

		if ( ! is_array( $result ) || empty( $result['url'] ) ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_secure_download_failed', __( 'The secure download link could not be created.', 'gk-gravityview' ) );
		}

		return (string) $result['url'];
	}

	/**
	 * Returns the download location for this operation.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view    View.
	 * @param array $context Processing context.
	 *
	 * @return array|WP_Error
	 */
	private function get_download_location( View $view, array $context = [] ) {
		$token = $this->get_result_token( $context );
		$root  = self::get_download_root_for_view_id( (int) $view->ID );

		if ( '' === $token || '' === $root ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_location_failed', __( 'The download location could not be prepared.', 'gk-gravityview' ) );
		}

		$dir = trailingslashit( $root ) . $token;

		if ( ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'gravityview_bulk_download_attachments_location_failed', __( 'The download location could not be prepared.', 'gk-gravityview' ) );
		}

		$this->write_public_temp_index_files( $dir );

		return [
			'blog_id' => get_current_blog_id(),
			'dir'     => $dir,
			'token'   => $token,
			'view_id' => (int) $view->ID,
		];
	}

	/**
	 * Returns this operation's result token.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $context Processing context.
	 *
	 * @return string
	 */
	private function get_result_token( array $context ) {
		if ( ! empty( $context['job_data']['result_token'] ) ) {
			return sanitize_key( (string) $context['job_data']['result_token'] );
		}

		if ( ! empty( $context['download_token'] ) ) {
			return sanitize_key( (string) $context['download_token'] );
		}

		return wp_generate_uuid4();
	}

	/**
	 * Writes index.html files in public temp directories.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $dir Download directory.
	 *
	 * @return void
	 */
	private function write_public_temp_index_files( $dir ) {
		$content_dir = untrailingslashit( wp_normalize_path( WP_CONTENT_DIR ) );
		$dir         = untrailingslashit( wp_normalize_path( $dir ) );

		if ( '' === $dir || 0 !== strpos( $dir, trailingslashit( $content_dir ) ) ) {
			return;
		}

		$paths = [];
		$path  = $dir;

		while ( '' !== $path && 0 === strpos( $path, trailingslashit( $content_dir ) ) ) {
			$paths[] = $path;

			if ( $path === $content_dir ) {
				break;
			}

			$path = dirname( $path );
		}

		foreach ( array_reverse( $paths ) as $path ) {
			$index = trailingslashit( $path ) . 'index.html';

			if ( file_exists( $index ) ) {
				continue;
			}

			file_put_contents( $index, '' );
		}
	}

	/**
	 * Schedules cleanup for a generated download directory.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download Download location.
	 *
	 * @return void
	 */
	private function schedule_cleanup( array $download ) {
		$dir     = (string) ( $download['dir'] ?? '' );
		$view_id = absint( $download['view_id'] ?? 0 );

		if ( '' === $dir || ! $view_id || ! self::is_download_directory_path_for_view( $dir, $view_id ) ) {
			return;
		}

		$scheduled = wp_schedule_single_event( time() + ResultStore::DEFAULT_TTL + HOUR_IN_SECONDS, self::CLEANUP_HOOK, [ $dir, $view_id, (int) ( $download['blog_id'] ?? get_current_blog_id() ) ] );

		if ( false === $scheduled ) {
			gravityview()->log->debug(
				'Attachment download cleanup could not be scheduled.',
				[
					'view_id' => $view_id,
					'dir'     => $dir,
				]
			);
		}
	}

	/**
	 * Returns cleanup metadata for sticky results.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $download Download location.
	 *
	 * @return array
	 */
	private function get_cleanup_result( array $download ) {
		return [
			'blog_id' => (int) ( $download['blog_id'] ?? get_current_blog_id() ),
			'dir'     => $download['dir'],
			'view_id' => (int) ( $download['view_id'] ?? 0 ),
		];
	}

	/**
	 * Returns whether a directory belongs to this View's download root.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $dir  Directory path.
	 * @param View   $view View.
	 *
	 * @return bool
	 */
	private function is_download_directory_for_view( $dir, View $view ) {
		return self::is_download_directory_path_for_view( $dir, (int) $view->ID );
	}

	/**
	 * Returns whether a directory belongs to the expected download root for a View ID.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $dir     Directory path.
	 * @param int    $view_id View ID.
	 *
	 * @return bool
	 */
	private static function is_download_directory_path_for_view( $dir, $view_id ) {
		$root = self::get_download_root_for_view_id( $view_id );

		$dir  = realpath( $dir );
		$root = realpath( $root );

		if ( false === $dir || false === $root ) {
			return false;
		}

		$dir  = untrailingslashit( wp_normalize_path( $dir ) );
		$root = untrailingslashit( wp_normalize_path( $root ) );

		return $dir !== $root && 0 === strpos( $dir, trailingslashit( $root ) );
	}

	/**
	 * Returns the per-site download temp root.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return string
	 */
	private static function get_download_site_root() {
		return untrailingslashit( trailingslashit( get_temp_dir() ) . 'gravityview/bulk-actions/download-attachments/site-' . get_current_blog_id() );
	}

	/**
	 * Returns whether a path is the expected per-site download root.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $root Root path.
	 *
	 * @return bool
	 */
	private static function is_download_site_root( $root ) {
		$root     = realpath( $root );
		$expected = realpath( self::get_download_site_root() );

		if ( false === $root || false === $expected ) {
			return false;
		}

		return untrailingslashit( wp_normalize_path( $root ) ) === untrailingslashit( wp_normalize_path( $expected ) );
	}

	/**
	 * Returns the download root for a View ID.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private static function get_download_root_for_view_id( $view_id ) {
		$view_id = absint( $view_id );

		if ( ! $view_id ) {
			return '';
		}

		return untrailingslashit( trailingslashit( self::get_download_site_root() ) . 'view-' . $view_id );
	}

	/**
	 * Deletes a file or directory tree.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path     Path.
	 * @param bool   $contents Whether to delete directory contents and the directory itself.
	 *
	 * @return bool
	 */
	private static function delete_path( $path, $contents = false ) {
		$path = (string) $path;

		if ( '' === $path || ! file_exists( $path ) ) {
			return true;
		}

		if ( is_file( $path ) || is_link( $path ) ) {
			return @unlink( $path );
		}

		if ( ! is_dir( $path ) ) {
			return false;
		}

		$items = scandir( $path );

		if ( false === $items ) {
			return false;
		}

		foreach ( $items as $item ) {
			if ( '.' === $item || '..' === $item ) {
				continue;
			}

			self::delete_path( trailingslashit( $path ) . $item, true );
		}

		return $contents ? @rmdir( $path ) : true;
	}

	/**
	 * Returns whether a process call is running inside a background batch.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $context Processing context.
	 *
	 * @return bool
	 */
	private function is_background_context( array $context ) {
		return ! empty( $context['background'] ) || ! empty( $context['job_data']['result_token'] );
	}

	/**
	 * Returns a previous cumulative custom result count.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $context Completion or batch context.
	 * @param string $key     Count key.
	 *
	 * @return int
	 */
	private function get_previous_result_count( array $context, $key ) {
		$source = isset( $context['job_data']['result'] ) && is_array( $context['job_data']['result'] ) ? $context['job_data']['result'] : [];

		return max( 0, (int) ( $source[ $key ] ?? 0 ) );
	}

	/**
	 * Returns completion counts from context metadata.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $context Completion context.
	 *
	 * @return array
	 */
	private function get_completion_counts( array $context ) {
		$batch = isset( $context['batch_result'] ) && is_array( $context['batch_result'] ) ? $context['batch_result'] : [];
		$job   = isset( $context['job_data']['result'] ) && is_array( $context['job_data']['result'] ) ? $context['job_data']['result'] : [];

		return [
			'processed'      => max( 0, (int) ( $context['processed'] ?? $batch['processed'] ?? 0 ) ),
			'failed'         => max( 0, (int) ( $context['failed'] ?? $batch['failed'] ?? 0 ) ),
			'skipped'        => max( 0, (int) ( $batch['skipped'] ?? $job['skipped'] ?? 0 ) ),
			'included_files' => max( 0, (int) ( $batch['included_files'] ?? $job['included_files'] ?? 0 ) ),
			'skipped_files'  => max( 0, (int) ( $batch['skipped_files'] ?? $job['skipped_files'] ?? 0 ) ),
			'bytes_total'    => max( 0, (int) ( $batch['bytes_total'] ?? $job['bytes_total'] ?? 0 ) ),
			'manifest_rows'  => max( 0, (int) ( $batch['manifest_rows'] ?? $job['manifest_rows'] ?? 0 ) ),
		];
	}

	/**
	 * Builds the payload for the completion action.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $counts Completion counts.
	 * @param string $url    Download URL.
	 * @param string $path   ZIP path.
	 *
	 * @return array
	 */
	private function build_complete_result( array $counts, $url, $path ) {
		$result = $counts;

		if ( '' !== $url ) {
			$result['url'] = $url;
		}

		if ( '' !== $path ) {
			$result['path'] = $path;
		}

		return $result;
	}

	/**
	 * Fires the GravityView completion hook once per operation.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $result Processing result.
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return void
	 */
	private function fire_complete_action( array $result, View $view, array $action ) {
		/**
		 * Fires after frontend attachment download processing finishes.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param array $result  Processing counts and download metadata.
		 * @param int   $view_id View ID.
		 * @param View  $view    View.
		 * @param array $action  Action config.
		 */
		do_action( 'gk/gravityview/bulk-actions/download-attachments/complete', $result, (int) $view->ID, $view, $action );
	}

	/**
	 * Returns the batch result message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $included Included files in the current batch.
	 * @param int $skipped  Skipped files in the current batch.
	 *
	 * @return string
	 */
	private function format_batch_message( $included, $skipped ) {
		if ( $included > 0 ) {
			return strtr(
				/* translators: [files] is the number of files prepared. */
				_n( '[files] file prepared.', '[files] files prepared.', (int) $included, 'gk-gravityview' ),
				[
					'[files]' => number_format_i18n( (int) $included ),
				]
			);
		}

		if ( $skipped > 0 ) {
			return __( 'No files were prepared in this batch.', 'gk-gravityview' );
		}

		return __( 'No file fields were found in this batch.', 'gk-gravityview' );
	}

	/**
	 * Formats the final success message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $counts Completion counts.
	 * @param string $url    Download URL.
	 *
	 * @return string
	 */
	private function format_success_message( array $counts, $url ) {
		$files   = max( 0, (int) ( $counts['included_files'] ?? 0 ) );
		$entries = max( 0, (int) ( $counts['processed'] ?? 0 ) );
		$skipped = max( 0, (int) ( $counts['skipped_files'] ?? $counts['skipped'] ?? 0 ) );
		$file_phrase = strtr(
			/* translators: [count] is the number of files. */
			_n( '[count] file', '[count] files', $files, 'gk-gravityview' ),
			[
				'[count]' => number_format_i18n( $files ),
			]
		);
		$entry_phrase = strtr(
			/* translators: [count] is the number of entries. */
			_n( '[count] entry', '[count] entries', $entries, 'gk-gravityview' ),
			[
				'[count]' => number_format_i18n( $entries ),
			]
		);
		$message = strtr(
			/* translators: [files] is a localized file count; [entries] is a localized entry count. The colon/period is appended in PHP based on whether a skip clause follows. */
			__( 'Prepared [files] from [entries]', 'gk-gravityview' ),
			[
				'[files]'   => $file_phrase,
				'[entries]' => $entry_phrase,
			]
		);

		if ( $skipped > 0 ) {
			$reason_summary = $this->format_skip_reason_summary( isset( $counts['skip_reasons'] ) && is_array( $counts['skip_reasons'] ) ? $counts['skip_reasons'] : [] );

			if ( '' === $reason_summary ) {
				$reason_summary = __( 'other skipped', 'gk-gravityview' );
			}

			$message .= ': ' . strtr(
				/* translators: [files] is the number of skipped files. [reasons] is a comma-separated list of skipped-file reasons. */
				_n( '[files] file skipped, [reasons].', '[files] files skipped, [reasons].', $skipped, 'gk-gravityview' ),
				[
					'[files]'   => number_format_i18n( $skipped ),
					'[reasons]' => $reason_summary,
				]
			);
		} else {
			$message .= '.';
		}

		return $message . ' ' . strtr(
			'[link_start][download_label][link_end]',
			[
				'[link_start]'     => '<a href="' . esc_url( $url ) . '">',
				'[download_label]' => esc_html__( 'Download ZIP', 'gk-gravityview' ),
				'[link_end]'       => '</a>',
			]
		);
	}

	/**
	 * Formats the no-download result message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $counts Completion counts.
	 *
	 * @return string
	 */
	private function format_empty_result_message( array $counts ) {
		$skipped = max( 0, (int) ( $counts['skipped_files'] ?? $counts['skipped'] ?? 0 ) );
		$message = __( 'No files matched the filters for the selected entries.', 'gk-gravityview' );

		if ( ! $skipped ) {
			return $message;
		}

		$reasons = $this->format_skip_reason_summary( isset( $counts['skip_reasons'] ) && is_array( $counts['skip_reasons'] ) ? $counts['skip_reasons'] : [] );

		if ( '' === $reasons ) {
			return $message;
		}

		return $message . ' ' . strtr(
			/* translators: [files] is the number of skipped files. [reasons] is a comma-separated list of skipped-file reasons. */
			_n( '[files] file skipped, [reasons].', '[files] files skipped, [reasons].', $skipped, 'gk-gravityview' ),
			[
				'[files]'   => number_format_i18n( $skipped ),
				'[reasons]' => $reasons,
			]
		);
	}

	/**
	 * Formats skipped-file reason counts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $reasons Reasons keyed by reason slug.
	 *
	 * @return string
	 */
	private function format_skip_reason_summary( array $reasons ) {
		$parts = [];

		foreach ( $reasons as $reason => $count ) {
			$count = max( 0, (int) $count );

			if ( ! $count ) {
				continue;
			}

				$parts[] = strtr(
					/* translators: [count] is the number of skipped files. [reason] is the skipped-file reason. */
					__( '[count] [reason]', 'gk-gravityview' ),
					[
						'[count]'  => number_format_i18n( $count ),
						'[reason]' => $this->get_reason_label( (string) $reason ),
				]
			);
		}

		return implode( ', ', $parts );
	}

	/**
	 * Returns a user-facing reason label.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $reason Reason slug.
	 *
	 * @return string
	 */
	private function get_reason_label( $reason ) {
			$labels = [
				DownloadAttachmentRules::REASON_EMPTY_VALUE            => __( 'with empty field values', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_ENTRY_FORM_NOT_IN_VIEW => __( 'from forms not in the View', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_EXCLUDED_PATTERN       => __( 'excluded by filename pattern', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_FILE_COUNT_LIMIT       => __( 'over file-count limit', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_FILTERED               => __( 'filtered out', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_INVALID_FILENAME       => __( 'with invalid filenames', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_MISSING_FILE           => __( 'missing on disk', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_OUTSIDE_UPLOADS        => __( 'outside uploads directory', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_PER_ENTRY_LIMIT        => __( 'over per-entry size limit', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_PER_FILE_LIMIT         => __( 'over per-file size limit', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_TOTAL_LIMIT            => __( 'over total size limit', 'gk-gravityview' ),
				DownloadAttachmentRules::REASON_UNREADABLE_FILE        => __( 'unreadable', 'gk-gravityview' ),
			];

			return $labels[ $reason ] ?? __( 'other skipped', 'gk-gravityview' );
		}

	/**
	 * Returns a safe ZIP download filename.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private function get_zip_download_filename( $view_id ) {
		unset( $view_id );

		return 'attachments.zip';
	}

	/**
	 * Returns whether a path is safe inside a ZIP archive.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $path ZIP path.
	 *
	 * @return bool
	 */
	private function is_safe_zip_path( $path ) {
		$path = str_replace( '\\', '/', (string) $path );

		return '' !== $path
			&& false === strpos( $path, "\0" )
			&& '/' !== $path[0]
			&& false === strpos( $path, '../' )
			&& false === strpos( $path, '/..' )
			&& false === strpos( $path, '://' );
	}

	/**
	 * Returns the Gravity Forms form editor URL for a form.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return string
	 */
	private function get_form_editor_admin_url( $form_id ) {
		return admin_url(
			add_query_arg(
				[
					'page' => 'gf_edit_forms',
					'id'   => (int) $form_id,
				],
				'admin.php'
			)
		);
	}
}
